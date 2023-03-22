<?php
class Attendance extends G_Controller {

	public $data = array();
	public $adminFlag = false;
	public $office_schedule = array();
	public $Login = array();
	public $Logout = array();
	
	public $LateReq = array();
	public $EarlyReq = array();
	public $AbsentReq = array();
	public $AbsentOnSpecialReq = array();
	
	public $totalWorkingDay = 0;
	public $totalWorkingSeconds = 0;
	public $totalOfficeSeconds = 0;
	public $totalPresentDay = 0;
	public $totalAbsent = 0;
	
	
	public $totalLateLogin = 0;
	public $totalEarlyLogout = 0;
	public $totalIncompleteOfficeDuration = 0;
	public $durationForAbsentRequest = 0;
	
	public $extraWSecDuringWDay = 0;
	public $extraWSecWithoutWDay = 0;
	
	public $myEmpId = '';
	
	public function __construct() {
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library('session');
		$this->load->model('user_model');
		$this->load->model('leave_model');
		$this->load->model('attendance_model');
		$this->load->library('pagination');
		$this->load->library('mailer');
		$this->data["myInfo"] = $this->session->GetMyBriefInfo();
		$this->data["userId"] = $this->session->GetLoginId();
		$this->data["userName"] = $this->GetUserName();
		$this->data['departments'] = $this->user_model->department();
		
		
		$this->data["menu"] = "Attendance";
		$this->data["uType"] = $this->session->GetUserType();
		$this->data["controller"] = $this;
		
		$this->myEmpId = $this->session->GetLoginId();		
		$this->data['isManagement'] = $this->session->IsManagement($this->myEmpId);
		$this->data['isAdmin'] = $this->session->IsAdmin($this->myEmpId);
		$this->data['isManager'] = $this->session->IsManager($this->myEmpId);
		
		$this->data["title"] = "Attendance";
		$this->data["sub_title"] = "ABC";
		
		if(!$this->data['isAdmin']) {
		    $this->data["status_array"] = $this->status_array;
		    $this->data["title"] = "ABC";
		    $this->data["sub_title"] = "ABC";
		    $this->data["message"] = "You have no privilege to access this page!";
		}			
				
		$users = $this->user_model->getStaffEidArray();
		
	}
	
	public function show(){
	    
	    $this->isLoggedIn();
	    
	    //priviledge settings
	    $userType = $this->data["uType"];
	    
	    if( $this->data['isAdmin'] || $this->data['isManagement'] ) {
	        
	        $this->data['departmentLists'] = $this->data['departments'];
	        $this->data['staff_array'] = $this->user_model->getStaffArray();
	         
	    } else if ( $this->data['isManager'] ) {
	        
	        $this->data['departmentLists'] = $this->user_model->getManagersDepts($this->data["userId"]);
	        $depts = array_keys($this->data['departmentLists']);
	        $this->data['staff_array'] = $this->user_model->getStaffArray($depts);
	        	         	        
	    } else {
	    
	        $this->data['departmentLists'] = array();
	        $this->data['staff_array'] = array();
	    }

	    
	    $myInfo = $this->data["myInfo"];
	    
	    //Post
	    $emp_id = isset($_POST['select_staff']) ? $_POST['select_staff'] : $this->myEmpId;
	    $sdate = isset($_POST['dateFrom']) ? $_POST['dateFrom'] : date('Y-m-01');
	    $edate = isset($_POST['dateTo']) ? $_POST['dateTo'] : date('Y-m-t');
	    $selDept = (isset($_POST['select_dept']) && !empty($_POST['select_dept'])) ? $_POST['select_dept'] : ( ($this->myEmpId == $emp_id)? $myInfo->userDeptCode : $this->user_model->getDeptCode($emp_id) );
	    
	    if( ($emp_id != $this->myEmpId)){
	        
            if( ($this->data['isManager']) && !$this->data['isAdmin'] && !$this->data['isManagement'] ){
                
                $staffAry = $this->data['staff_array']['all'];
                $staffIdAry = array();
                foreach ($staffAry as $obj){
                    $staffIdAry[] = $obj->emp_id;
                }
                
                if(!in_array($emp_id , $staffIdAry)){
                    
                    $emp_id = $this->myEmpId;
                }
                
            }else if ($userType == 'E' && !$this->data['isAdmin'] && !$this->data['isManagement']){
                
                $emp_id = $this->myEmpId;
            }	        
	    }
	    	    	   	    
	    //dateValidation($sdate, $edate);
	    if($sdate > $edate){
	        $temp = $sdate;
	        $sdate = $edate;
	        $edate = $temp;
	    }
	    if($edate > date("Y-m-d")){
	        $edate = date("Y-m-d");
	    }
	    
	    $this->data['sel_dept'] = $selDept;
	    $this->data['sel_emp_id'] = $emp_id;
	    $this->data['sel_sdate'] = $sdate;
	    $this->data['sel_edate'] = $edate;
	    	    	    
	    // getting & setting Basic Info
	    $empInfo = $this->attendance_model->getEmployeeInfo($emp_id);	    
	    $leaveDates = $this->attendance_model->getLeaveDates($emp_id, $sdate, $edate);
	    $holidays = $this->attendance_model->getHolidays($empInfo, $sdate, $edate);
	    $incidents = $this->attendance_model->getIncidents($sdate, $edate);
	    $this->setLateEarlyRequest($emp_id, $sdate, $edate);
	    
	    //get Office Shcedule
	    $result = $this->attendance_model->getOfficeSchedule($empInfo, $sdate, $edate, $this->rosterType, $this->default_office_time);
	    $weekendsByNotScheduled = $result['weekends'];
	    $officeSchedules = $result['office_schedule'];	    
	    $this->toSort($officeSchedules, "stime");
	    $weekends = $this->getWeekend($empInfo, $sdate, $edate);	    
	    foreach ($weekendsByNotScheduled as $wDate){
	        
	        $weekends[$wDate] = true;	        
	    }

	    $logTimes = $this->attendance_model->getLogTimes($emp_id, $sdate, $edate);

	    $records = array();
	    
	    //print_r($officeSchedules); //RND
	    foreach ($officeSchedules as $key =>$oSchedules){
	        
	        $record = new stdClass();
	        
	        //print_r($oSchedules);
	        	       	        
	        $office_day = $oSchedules['noOfSlot'];
	        $stime_schedule = $oSchedules['stime'];
	        $etime_schedule = $oSchedules['etime'];
	        $sdate_schedule = substr($stime_schedule, 0,10);
	        $edate_schedule = substr($etime_schedule, 0,10);
	        
	        if($sdate_schedule == $edate_schedule){
	            $record->recordDate = $sdate_schedule;
	            $record->recordDay = date('l', strtotime($sdate_schedule));
	            	           
	        }else{
	            $record->recordDate = $sdate_schedule." - ".$edate_schedule;
	            $record->recordDay = date('D', strtotime($sdate_schedule))." - ".date('D', strtotime($edate_schedule));	            
	        }	        

	        if($weekends[$sdate_schedule] || $leaveDates[$sdate_schedule] || $holidays[$sdate_schedule]) {
	            
	            if($leaveDates[$sdate_schedule]){
	                
	                if($leaveDates[$sdate_schedule] == 1){
	                    $record->officeStart = "--";
	                    $record->officeEnd = "--";
	                    $record->officeDuration = "--";
	                    
	                } else{
	                    // half day leave
	                    $o_duration = strtotime($etime_schedule) - strtotime($stime_schedule);
	                    
	                    //double slot
	                    if($o_duration > 9*3600){
	                        if($leaveDates[$sdate_schedule] == 0.5){
	                             
	                            $stime_schedule = date("Y-m-d H:i:s", strtotime($etime_schedule) - (12*3600)) ;
	                             
	                        }else if($leaveDates[$sdate_schedule] == 0.6){
	                             
	                            $etime_schedule = date("Y-m-d H:i:s", strtotime($stime_schedule) + (12*3600));
	                        }
	                    }else{
	                        //single slot
	                        if($leaveDates[$sdate_schedule] == 0.5){
	                        
	                            $stime_schedule = date("Y-m-d H:i:s", strtotime($etime_schedule) - (4*3600)) ;
	                        
	                        }else if($leaveDates[$sdate_schedule] == 0.6){
	                        
	                            $etime_schedule = date("Y-m-d H:i:s", strtotime($stime_schedule) + (4*3600));
	                        }
	                    }	            

	                    
	                    $record->officeStart = date("h:i:s a",strtotime($stime_schedule));
	                    $record->officeEnd = date("h:i:s a",strtotime($etime_schedule));
	                    $record->officeDuration = $this->secondsToHours( strtotime($etime_schedule) - strtotime($stime_schedule) );

	                    $office_day -=  0.5;
	                    
	                    if($record->recordDate != date("Y-m-d")){
	                        
	                        $this->totalWorkingDay += $office_day;
	                    }
	                    
	                }      // half day leave end
	                
	            } else{
	                $record->officeStart = "--";
	                $record->officeEnd = "--";
	                $record->officeDuration = "--";
	            }
	            
	        } else {
	            $record->officeStart = date("h:i:s a",strtotime($stime_schedule));
	            $record->officeEnd = date("h:i:s a",strtotime($etime_schedule));
	            $record->officeDuration = $this->secondsToHours( strtotime($etime_schedule) - strtotime($stime_schedule) );
	            
	            if($record->recordDate != date("Y-m-d")){
	                 
	                $this->totalWorkingDay += $office_day;
	            }
                
	        }

	        $logData = $this->getLogtimeDetails($empInfo, $logTimes, $stime_schedule, $etime_schedule, $office_day, $weekends, $leaveDates, $holidays, $incidents);
	        $record->logIn = $logData['logIn'];
	        $record->logOut = $logData['logOut'];
	        $record->logD = $logData['logD'];
	        $record->incident = $logData['incident'];
	        $record->message = $logData['message'];
	        $record->attendance = $logData['attendance'];
	        
	        $records[] = $record;
	    }
	    
	    $this->data["records"] = $records;

	    //load view page    
        $this->data["title"] = "report";
        $this->data["sub_title"] = "Attendace Report";
        $this->view('attendance_view', $this->data);
	    
	}
	
	public function getLogtimeDetails($empInfo, &$logTimes, $stime_schedule, $etime_schedule, $office_day, $weekends, $leaveDates, $holidays, $incidents){	 	   
	    	    


	    $ret= array();	    
	    $sdate_schedule = substr($stime_schedule, 0,10);
	    $edate_schedule = substr($etime_schedule, 0,10);
	    $duration_schedule_sec = strtotime($etime_schedule) - strtotime($stime_schedule);
	    $duration_schedule =  $this->secondsToHours($duration_schedule_sec );
	    
	    //generate attendance status
	    $attendance = array();
	    
        $isCyan = false;
        if($weekends[$sdate_schedule] ){
        
            $attendance[11] = "<span class='txt-grn'>Weekend</span>";
            $isCyan = true;
        }
        
        if($leaveDates[$sdate_schedule] ) {
        
            if($leaveDates[$sdate_schedule] == 1)
                $attendance[21] = "<span class='txt-grn'>Leave</span>";
            else
                $attendance[21] = "<span class='txt-grn'>Half Leave</span>";
            $isCyan = true;
        }
        
        if($holidays[$sdate_schedule]) {
        
            $attendance[31] = "<span class='txt-grn'>Holiday</span>";
            $isCyan = true;
        }
        
        if($sdate_schedule != $edate_schedule ){
            
            if($weekends[$edate_schedule]){
                $attendance[12] = "<span class='txt-grn'>Weekend</span>";
                $isCyan = true;
            }
            if($leaveDates[$edate_schedule]){
                $attendance[22] = "<span class='txt-grn'>Leave</span>";
                $isCyan = true;
            }
            if($holidays[$edate_schedule]){
                $attendance[32] = "<span class='txt-grn'>Holiday</span>";
                $isCyan = true;
            }
        }        

        
	    //if($sdate_schedule == $edate_schedule){
	        //office duration within one day
	        
	        if( isset($logTimes[$sdate_schedule]) || isset($logTimes[$edate_schedule]) ){
	            // atlest one log
	            
	            $this->totalPresentDay += $office_day;
	            
	            if($sdate_schedule == $edate_schedule){
	                
	                $logAry = &$logTimes[$sdate_schedule];
	                
	            }else{
	                $logAry = array();
                    for ($idate = $sdate_schedule; $idate <= $edate_schedule;) {
                        
                        if(isset($logTimes[$idate])){
                            
                            $refAry[$idate] = &$logTimes[$idate];
                            $logAry = array_unique(array_merge($logAry ,$logTimes[$idate]));
                        }
                        
                        $idate = date("Y-m-d", strtotime($idate . " +1 day"));
                    }
                    sort($logAry);	               
	            }
	            
	            $halfTime =  $duration_schedule_sec/2 ;
	            $median = strtotime($stime_schedule) + $halfTime;
	            
	            $prevMedian = strtotime($stime_schedule) - 6*3600;
	            $nextMedian = strtotime($etime_schedule) + 6*3600;

	            if(count($logAry) < 2){
	                /* ################ one log ####################*/
	                
	                $first = reset($logAry);
	                $firstSec = strtotime($first);
	                
	                //remove from the $logAry, $logTimes
	                $this->deleteItemByVal($first, $logAry);

	                if( $firstSec < $median && $firstSec > $prevMedian){
	                     
	                    $ret['logIn'] = $this->getLogIn($first, $stime_schedule, $weekends, $leaveDates, $holidays, $incidents, $duration_schedule);
	                    
	                    if($isCyan){	                         
	                        $ret['logOut'] = "<td class='txt-cyan-bold topTips' title='Not working day but present'>?</td>";
	                    }else{
	                        
	                        if(isset($this->EarlyReq[$edate_schedule])) {
	                            
	                            $ret['logOut'] = "<td class='txt-grn-bold topTips' title='".$this->EarlyReq[$edate_schedule]." on ".$edate_schedule."'>
					                             ".date("h:i:s a", strtotime($etime_schedule))."<sup style='color:red;'>*</sup> </td>";
	                        }else{
	                            $ret['logOut'] = "<td class='txt-red-bold'>?</td>";
	                            $this->totalEarlyLogout++;	                            
	                        }
	                        
	                    }
	                } else if( $firstSec > $median && $firstSec < $nextMedian){
	                    
	                    $ret['logOut'] = $this->getLogOut($first, $etime_schedule, $weekends, $leaveDates, $holidays, $incidents, $duration_schedule);
	                    
	                    if($isCyan){
	                        $ret['logIn'] = "<td class='txt-cyan-bold topTips' title='Not working day but present'>?</td>";
	                    }else{
	                        
	                        if(isset($this->LateReq[$sdate_schedule])) {
	                             
	                            $ret['logIn'] = "<td class='txt-grn-bold topTips' title='".$this->LateReq[$sdate_schedule]." on ".$sdate_schedule."'>
					                             ".date("h:i:s a", strtotime($stime_schedule))."<sup style='color:red;'>*</sup> </td>";
	                        }else{
	                            
    	                        $ret['logIn'] = "<td class='txt-red-bold'>?</td>";
    	                        $this->totalLateLogin++;
	                        }	                        
	                    }
	                } else{
	                    
	                    if($isCyan){
	                        
	                        $ret['logIn'] = $ret['logOut'] = "<td class='txt-cyan-bold topTips' title='Not working day but present'>?</td>";
	                        
	                    }else{
	                        
	                        $ret['logIn'] = "<td class='txt-red-bold'>?</td>";
	                        $ret['logOut'] = "<td class='txt-red-bold'>?</td>";
	                        $this->totalLateLogin++;
	                        $this->totalEarlyLogout++;
	                    }
	                }
	                
	                if($isCyan){
	                    
	                    $ret['logD'] = "<td class='txt-cyan-bold topTips' title='Not working day but present'>?</td>";
	                }else{
	                    
	                    if(isset($this->LateReq[$sdate_schedule]) || isset($this->EarlyReq[$edate_schedule])) {
	                        
	                        $logInSec = isset($this->LateReq[$sdate_schedule]) ? strtotime($stime_schedule): $firstSec;
	                        $logOutSec = isset($this->EarlyReq[$edate_schedule]) ? strtotime($etime_schedule): $firstSec;	                        
	                        $logDurationSec = $logOutSec - $logInSec;	                       
	                        $logDurationText  = $this->secondsToHours($logDurationSec);
	                        $officeDurationSec = strtotime($etime_schedule)- strtotime($stime_schedule);
	                        
	                        $this->totalWorkingSeconds += $logDurationSec;	                        
	                        $this->extraWSecDuringWDay += ($logDurationSec - $officeDurationSec);                        
	                        
	                        $titleText = "";	                        	                        
                            if(isset($this->LateReq[$sdate_schedule])){
	                            
	                            $titleText .= $this->LateReq[$sdate_schedule]." on ".$sdate_schedule;
	                        }else{ 
	                            $titleText .= $this->EarlyReq[$edate_schedule]." on ".$edate_schedule;
	                        }
	                        
	                        $ret['logD'] = "<td class='txt-ylo-bold topTips' title='".$titleText."'>".$logDurationText."</td>";
	                        
	                    }else{
	                        
	                        $ret['logD'] = "<td class='txt-red-bold'>?</td>";
	                        $this->totalIncompleteOfficeDuration++;
	                    }
	                    
	                }
	                
	                
	            } else if(count($logAry) >= 2){
	                /* ################ Atlest two log ####################*/

	                $first = $this->getFistElemArray($logAry, $stime_schedule, $median, $prevMedian, $nextMedian);            

	                //remove from the $logAry, $logTimes
	                if($sdate_schedule == $edate_schedule){
	                     
	                    $this->deleteItemByVal($first, $logAry);
	                }else{	                     
	                    for ($idate = $sdate_schedule; $idate <= $edate_schedule;) {
	                
	                        if(isset($refAry[$idate])){
	                
	                            $this->deleteItemByVal($first, $refAry[$idate]);
	                            $this->deleteItemByVal($first, $logAry);
	                        }
	                
	                        $idate = date("Y-m-d", strtotime($idate . " +1 day"));
	                    }
	                }
	                
	                $last = $this->getLastElemArray($logAry, $etime_schedule, $median, $prevMedian, $nextMedian);
	                
	                //remove from the $logAry, $logTimes
	                if($sdate_schedule == $edate_schedule){

	                    $this->deleteItemByVal($last, $logAry);
	                }else{
	                    
	                    for ($idate = $sdate_schedule; $idate <= $edate_schedule;) {
	                         
	                        if(isset($refAry[$idate])){

	                            $this->deleteItemByVal($last, $refAry[$idate]);
	                        }
	                         
	                        $idate = date("Y-m-d", strtotime($idate . " +1 day"));
	                    }
	                }

	                if(empty($first)){
	                    $ret['logIn'] =  "<td class='txt-red-bold'>?</td>";
	                    $this->totalLateLogin++;
	                } else{
	                    $ret['logIn'] = $this->getLogIn($first, $stime_schedule, $weekends, $leaveDates, $holidays, $incidents, $duration_schedule);
	                }
	                
	                if(empty($last)){
	                    $ret['logOut'] =  "<td class='txt-red-bold'>?</td>";
	                    $this->totalEarlyLogout++;
	                } else{
	                    $ret['logOut'] = $this->getLogOut($last, $etime_schedule, $weekends, $leaveDates, $holidays, $incidents, $duration_schedule);
	                }
	                	                
	                if( !empty($first) && !empty($last) ){
	                    
	                    $ret['logD'] =  $this->getLogDuration($first, $last, $stime_schedule, $etime_schedule, $weekends, $leaveDates, $holidays, $incidents, $duration_schedule);
	                }  else {
	                    
	                    $ret['logD'] =  "<td class='txt-red-bold'>?</td>";
	                    $this->totalIncompleteOfficeDuration++;
	                }
	                
	            }
	            $ret['incident'] = ($incidents[$sdate_schedule]) ?  "<td>".$incidents[$sdate_schedule]."</td>": "<td>--</td>";
	            $ret['message'] = "";
       
	            //generate attendance status
	            if(count($attendance)==0) {
	                $attendance[] = "<span class='txt-blu'>Working Day</span>";
	                
	            } else {
	                // weekend/holiday/leave
	                
	                if($sdate_schedule != $edate_schedule){
	                    
	                    foreach ($attendance as $key=>$val){
	                        
	                        if($key%2 == 1){
	                            // Start date	                            
	                            if(isset($attendance[$key+1])){
	                                //unset($attendance[$key+1]);
	                                $attendance[$key+1] = "Present";
	                            }else{
	                                $attendance[$key+1] = "<span class='txt-blu'>Working Day</span>";
	                            }
	                            
	                        }else{
	                            // end date
	                            if(isset($attendance[$key-1])){
	                                unset($attendance[$key-1]);
	                                $attendance[$key+1] = "Present";
	                            }else{
	                                $attendance[$key-1] = "<span class='txt-blu'>Working Day</span>";
	                            }	                            
	                        }
	                    }
	                    	                     
	                } else{
	                    
	                    $attendance[] = "Present";
	                }
	                
	                ksort($attendance);	                
	            }
	            
	        } else{
	            // Absent/no log         
	            $ret['logIn'] = "";
	            $ret['logOut'] = "";
	            $ret['logD'] = "";
	            $ret['incident'] = "";
	            
	            //generate Message
	            $message="";
	            if(isset($this->AbsentReq[$sdate_schedule]) || isset($this->AbsentOnSpecialReq[$sdate_schedule]) ) {
	                
	                $tips = isset($this->AbsentReq[$sdate_schedule]) ? $this->AbsentReq[$sdate_schedule] : $this->AbsentOnSpecialReq[$sdate_schedule];
	                $message = "<span class='topTips' title='".$tips."'>(by default work duration added ". $duration_schedule .")<sup style=' color:red;'>*</sup></span>";
	                	                	                
	            } else if( $holidays[$sdate_schedule]) {
	                $message = $holidays[$sdate_schedule];
	            }
	            
	            $ret['message'] = "<td colspan='4' align='center'>".$message."</td>";
	            
	            //generate attendance status
	            if(count($attendance)==0) {

	                if(@$this->AbsentReq[$sdate_schedule] || @$this->AbsentOnSpecialReq[$sdate_schedule]) {

	                    $attendance[] = "<span class='txt-grn'>Absent granted</span>";
	                    
	                    $this->totalPresentDay += $office_day;
	                    $this->totalWorkingSeconds += $duration_schedule_sec;
	                    
	                } else {
	                    
	                    
	                    $attendance[] = "<span class='txt-red'>Absent</span>";
	                    $this->totalAbsent += $office_day;
	                }
	            }
	            
	        }

	        $ret['attendance'] = implode("/",$attendance);
	        
	        
	        return $ret;
	    //}	    

	}

	public function getLogIn($logInDT, $stime_schedule, $weekends, $leaveDates, $holidays, $incidents, $duration_schedule) {
		
        // $logInDate = $sdate_schedule
	    $this->isLoggedIn();
	    
		$logInDate = substr($logInDT, 0,10);
		$logInTime = substr($logInDT, -8);
		
		$officeStartTime = substr($stime_schedule, -8);

		$flexible_office_time = $this->flexible_office_times;
		$flexible_time = isset($flexible_office_time[$officeStartTime]) ? $flexible_office_time[$officeStartTime] : $flexible_office_time['regular'];
		
		$logInSec = strtotime($logInDT);
		$officeStartSec = strtotime($stime_schedule);
		$flexibleSec = strtotime($flexible_time) - strtotime('TODAY');
		
		if( $weekends[$logInDate] || ($leaveDates[$logInDate] == 1) || $holidays[$logInDate] ) {

		    if(!empty($stime_schedule)){
		        
		        $login_status = "<td class='txt-cyan-bold topTips' title='Not working day but present'>".date('h:i:s a', $logInSec)."</td>";		        
		    }else{
		        $login_status = "<td class='txt-cyan-bold topTips' title='Not working day but present'>?</td>";
		    }
		    return $login_status;
		}
		
		
		
		if( $logInSec <= ($officeStartSec + $flexibleSec) ) {
		    // log in right time
		    //echo "test";
			$login_status = "<td class='txt-grn-bold'>".date('h:i:s a', $logInSec)."</td>";
			
		} else {
		    //wrong time
		    
		    if($incidents[$logInDate]) {
		    
		        // 3600sec = 1hr 
		        if( ($logInSec - $officeStartSec ) > 3600 ) {
		            	
		            $login_status = "<td class='txt-red-bold topTips' title='".$incidents[$logInDate]." on ".$logInDate."&#013but late more than flexi-time(1 hour)'>".date('h:i:s a', $logInSec)."</td>";
		            $this->totalLateLogin++;
		        } else {
		            
		            $login_status = "<td class='txt-blu-bold topTips' title='".$incidents[$logInDate]." on ".$logInDate."&#013 using flexi time ".$this->secondsToHours($logInSec - $officeStartSec)."'>".date('h:i:s a', $logInSec)."</td>";
		        }
		    } else if(isset($this->LateReq[$logInDate])) {
				    
	       		$login_status = "<td class='txt-grn-bold topTips' title='".$this->LateReq[$logInDate]." on ".$logInDate."&#013 Login time:".$logInTime."'> ".date("h:i:s a", $officeStartSec)."<sup style=' color:red;'>*</sup> </td>";
	       		
	       		//0.6 means second half Leave
			} else if( $leaveDates[$logInDate] == 0.6){
			    
		        $office_duration_sec = strtotime($duration_schedule);
		        $officeEndSec = ($officeStartSec + $office_duration_sec);
		        $office_new_start_sec = $officeEndSec - (4*3600);
		        		        
		        if($logInSec <= $office_new_start_sec){
		            //log in right time in Half Leave
		            $login_status = "<td class='txt-grn-bold'>".date('h:i:s a', $logInSec)."</td>";
		        }else{
		            $login_status = "<td class='txt-red-bold topTips' title='Late &raquo; ".$this->secondsToHours($logInSec - $office_new_start_sec)."'>".date('h:i:s a', $logInSec)."</td>";
		            $this->totalLateLogin++;
		        }
		        
		         
		    } else {
		    
		        $login_status = "<td class='txt-red-bold topTips' title='Late &raquo; ".$this->secondsToHours($logInSec - $officeStartSec) ."'>".date('h:i:s a', $logInSec)."</td>";
		        $this->totalLateLogin++;
		    }
		    
		    
		}
		
		return $login_status;
	}
	

	
	public function getLogOut($logOutDT, $etime_schedule, $weekends, $leaveDates, $holidays, $incidents, $duration_schedule) {
	    
	    $this->isLoggedIn();
	    
	    // $logInDate = $sdate_schedule
	    $logOutDate = substr($etime_schedule, 0,10);
	    $logOutTime = substr($logOutDT, -8);
	
	    $logOutSec = strtotime($logOutDT);
	    $officeEndSec = strtotime($etime_schedule);
	
	
	    if( $weekends[$logOutDate] || ($leaveDates[$logOutDate] == 1) || $holidays[$logOutDate] ) {
	
	        $logOut_status = "<td class='txt-cyan-bold topTips' title='Not working day but present'>".date('h:i:s a', $logOutSec)."</td>";
	        return $logOut_status;
	    }
	
	    if( $logOutSec >= $officeEndSec ) {
	        // log out right time
	        $logOut_status = "<td class='txt-grn-bold'>".date('h:i:s a', $logOutSec)."</td>";
	        	
	    } else {
	        //wrong time	        
	
	        if($incidents[$logOutDate]) {
	
	            // 3600sec = 1hr
	            if( ($officeEndSec - $logOutSec) > 3600 ) {
	                 
	                $logOut_status = "<td class='txt-red-bold topTips' title='".$incidents[$logOutDate]." on ".$logOutDate."&#013but late more than flexi-time(1 hour)'>".date('h:i:s a', $logOutSec)."</td>";
	                $this->totalEarlyLogout++;
	            } else {
	
	                $logOut_status = "<td class='txt-blu-bold topTips' title='".$incidents[$logOutDate]." on ".$logOutDate."&#013 using flexi time ".$this->secondsToHours($officeEndSec -$logOutSec)."'>".date('h:i:s a', $logOutSec)."</td>";
	            }
	        } else if(isset($this->EarlyReq[$logOutDate])) {
	
	            $logOut_status = "<td class='txt-grn-bold topTips' title='".$this->EarlyReq[$logOutDate]." on ".$logOutDate."&#013 Login time:".$logOutTime."'> ".date("h:i:s a", $officeEndSec)."<sup style='color:red;'>*</sup> </td>";
	
	            // 0.5 => means fist Half Leave
	        } else if( $leaveDates[$logOutDate] == 0.5){

	            $office_duration_sec = strtotime($duration_schedule);
		        $officeStartSec = $officeEndSec - $office_duration_sec;
		        $office_new_end_sec = $officeStartSec + (4*3600);
	
	            if($logOutSec >= $office_new_end_sec){
	                //log in right time in Half Leave
	                $logOut_status = "<td class='txt-grn-bold'>".date('h:i:s a', $logOutSec)."</td>";
	            }else{
	                $logOut_status = "<td class='txt-red-bold topTips' title='Late &raquo; ".$this->secondsToHours($office_new_end_sec - $logOutSec)."'>".date('h:i:s a', $logOutSec)."</td>";
	                $this->totalEarlyLogout++;
	            }
		             
	        } else {
	            // Wrong log out
	            $logOut_status = "<td class='txt-red-bold topTips' title='Late &raquo; ".$this->secondsToHours($officeEndSec - $logOutSec)."'>".date('h:i:s a', $logOutSec)."</td>";
	            $this->totalEarlyLogout++;
	        }
	
	    }
	
	    return $logOut_status;
	}
	
	public function getLogDuration($logInDT, $logOutDT, $stime_schedule, $etime_schedule, $weekends, $leaveDates, $holidays, $incidents, $duration_schedule) {

	    
	    $officeStartDate = substr($stime_schedule, 0,10);
	    $officeEndDate = substr($etime_schedule, 0,10);
	    $logInDate = substr($logInDT, 0,10);
	    $logOutDate = substr($logOutDT, 0,10);
	    
	    //$officeEndTime = substr($etime_schedule, -8);
	    
	    $officeDurationSec =  strtotime($etime_schedule) - strtotime($stime_schedule);	    
	    $logDurationSec = strtotime($logOutDT) - strtotime($logInDT);	    
	    $this->totalWorkingSeconds += $logDurationSec;
	    
	    $logDurationText  = $this->secondsToHours($logDurationSec);

	
	
	    if( $weekends[$logInDate] || ($leaveDates[$logInDate] == 1) || $holidays[$logInDate] ) {
	        
	        $logD_status = "<td class='txt-cyan-bold topTips' title='Not working day but present &#013 Extra duty &raquo; ".$logDurationText." '>".$logDurationText."</td>";
	        $this->extraWSecWithoutWDay += $logDurationSec;
	        return $logD_status;
	    }
	
	    if( $logDurationSec >= $officeDurationSec ) {
	        // log Duration
	                         
            $extraDuration = $logDurationSec - $officeDurationSec;
	        $login_status = "<td class='txt-grn-bold topTips' title='Extra Work: ".$this->secondsToHours($extraDuration)."'>".$logDurationText."</td>";
	        $this->extraWSecDuringWDay += $extraDuration;
	        
	    } else {
	        //wrong time

	        if($incidents[$logInDate]) {
	
	            // 3600sec = 1hr for login and logout each; 2hr for incident
	            if( ($officeDurationSec - $logDurationSec) > (2*3600) ) {
	                
	                $lessDuration = $officeDurationSec - $logDurationSec - (2*3600);
	                
	                $login_status = "<td class='txt-red-bold topTips' title='".$incidents[$logInDate]." on ".$logInDate."&#013 but late more than flexi-time(2 hours)'>".$logDurationText."</td>";
	                $this->totalIncompleteOfficeDuration++;	                
	                $this->extraWSecDuringWDay -= $lessDuration;
	            } else {
	
	                $login_status = "<td class='txt-blu-bold topTips' title='".$incidents[$logInDate]." on ".$logInDate."&#013 using flexi time ".$this->secondsToHours($officeDurationSec - $logDurationSec)."'>".$logDurationText."</td>";
	            }
	            
	        } else if(isset($this->LateReq[$logInDate])) {
	            // $stime_shcedule-----------$logInDT -----------||---------$etime_schedule----$logOutDT
	            $logDurationSecLate = strtotime($logOutDT) - strtotime($stime_schedule);	            
	            $logDurationText  = $this->secondsToHours($logDurationSecLate);
	            
	            $this->totalWorkingSeconds += ($logDurationSecLate - $logDurationSec);
	            $this->extraWSecDuringWDay += ($logDurationSecLate - $officeDurationSec);
	            
	            $login_status = "<td class='txt-ylo-bold topTips' title='".$this->LateReq[$logInDate]."&#013 on &#013 ".$logInDate."'>".$logDurationText."</td>";
	            
	        } else if(isset($this->EarlyReq[$logOutDate])) {
	            
	            $logDurationSecEarly = strtotime($etime_schedule) - strtotime($logInDT);
	            $logDurationText  = $this->secondsToHours($logDurationSecEarly);
	            
	            $this->totalWorkingSeconds += ($logDurationSecEarly - $logDurationSec);
	            $this->extraWSecDuringWDay += ($logDurationSecEarly - $officeDurationSec);
	            
	            $login_status = "<td class='txt-ylo-bold topTips' title='".$this->EarlyReq[$logOutDate]."&#013 on &#013 ".$logOutDate."'> ".$logDurationText."</td>";
	
	        } else if( isset($this->AbsentReq[$logInDate]) || isset($this->AbsentOnSpecialReq[$logInDate]) ) {
	            
	            $logDurationSecEarly = strtotime($etime_schedule) - strtotime($logInDT);
	            $logDurationText  = $this->secondsToHours($logDurationSecEarly);
	            
	            //$this->totalWorkingSeconds += $officeDurationSec;
	            
	            $login_status = "<td class='txt-ylo-bold topTips' title='".$this->EarlyReq[$logOutDate]."&#013 on &#013".$logOutDate."'> ".$logDurationText."</td>";
	
	            // 0.5 => means fist Half Leave
	        } else if( $leaveDates[$logOutDate] == 0.5 || $leaveDates[$logOutDate] == 0.6){

	            if($logDurationSec >= (4*3600)){
	                //log in right time in Half Leave
	                $extraDuration = $logDurationSec - (4*3600);
	                $login_status = "<td class='txt-grn-bold topTips' title='Extra Work: ".$this->secondsToHours($extraDuration)."'>".$logDurationText."</td>";
	                $this->extraWSecDuringWDay += $extraDuration;
	                	                
	            }else{
	                $lessDuration = (4*3600)- $logDurationSec;
	                $login_status = "<td class='txt-red-bold topTips' title='Less duration &raquo; ".$this->secondsToHours($lessDuration)."'>".$logDurationText."</td>";
	                $this->totalIncompleteOfficeDuration++;
	                $this->extraWSecDuringWDay -= $lessDuration;
	            }
	             
	        } else {
	            // Wrong log out
	            $lessDuration = $officeDurationSec- $logDurationSec;
	            $login_status = "<td class='txt-red-bold topTips' title='Less duration &raquo; ".$this->secondsToHours($lessDuration)."'>".$logDurationText."</td>";
	            $this->totalIncompleteOfficeDuration++;	            
	            $this->extraWSecDuringWDay -= $lessDuration;
	        }
	
	    }
	
	    return $login_status;	    	
	}

	
	public function getWeekend($empInfo, $sdate, $edate){
	    
	    $Weekends = array();
	
	    if($empInfo["roster"] == "N") {
	        //Non-Roster Weekend
	        $weekly_leave = $this->attendance_model->getWeeklyLeave($empInfo["emp_id"]);
	        $dayCount= 0;
	        foreach ($weekly_leave as $key=>$val){
	
	            if($key == 'emp_id')  continue;
	            if($val == 'Y')   $dayCount++;
	        }
	
	        if($dayCount == 0){
	            $weekly_leave = $this->default_weekend;
	        }
	
	        for($idate=$sdate; $idate<=$edate; ) {
	
	            $day = strtolower(date("D",strtotime($idate)));
	
	            if($weekly_leave[$day] == 'N'){
	                $Weekends[$idate] = false;
	            }else {
	                $Weekends[$idate] = true;
	            }
	             
	            $idate = date("Y-m-d",strtotime($idate." +1 day"));
	        }
	    } else{
	        //Roster Weekend	         
	        for($idate=$sdate; $idate<=$edate; ) {
	             
	            $Weekends[$idate] = false;
	            $idate = date("Y-m-d",strtotime($idate." +1 day"));
	        }
	         
	        $data = $this->attendance_model->getRosterWeekend($empInfo["emp_id"], $sdate, $edate);
	
	        foreach($data as $row){
	            $temp_date = $row['date'];
	            $Weekends[$temp_date] = true;
	        }	         
	    }
	    return $Weekends;
	}
	
	function setLateEarlyRequest($emp_id, $sdate, $edate) {
	
	    $result = $this->attendance_model->getLateEarlyRequest($emp_id, $sdate, $edate);
	
	    foreach ($result as $row) {
	        $date = $row['date'];
	
	        if($row['late_req'] =="R") $this->LateReq[$date] = $row["reason"];
	        if($row['early_req'] =="R") $this->EarlyReq[$date] = $row["reason"];
	        if($row['absent_req'] =="R") $this->AbsentReq[$date] = $row["reason"];
	        if($row['special_req'] =="R") $this->AbsentOnSpecialReq[$date] = $row["reason"];
	    }
	}
	
	public function secondsToHours($sec){
	    $abcSec = abs($sec);

	    $hours = floor($abcSec / 3600);
	    $minutes = floor(($abcSec / 60) % 60);
	    $seconds = $abcSec % 60;
	
	    if($sec < 0){
	        return  "<span class='txt-red-bold'>- ".sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds)."</span>";
	    }else{
	        return  sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
	    }
	     
	    //return  sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
	}
	

	public function deleteItemByVal($item, &$arrays){
	     
	    if (($key = array_search($item, $arrays)) !== false) {
	        unset($arrays[$key]);
	    }

	    $arrays = array_values($arrays);
	}
	
	public function getFistElemArray($logAry, $stime_schedule, $median, $prevMedian, $nextMedian){
	    
	    /*
	     * date = login_date and abs(timeDif(login, office_start)) <= 6*3600;
	     * 
	     * or,
	     * 
	     * date = login_date and login > office_start;
	     */
	    
	    $office_in_sec = strtotime($stime_schedule);
	    $office_in_date = substr($stime_schedule, 0,10);

	    
	    // first try
	    foreach ($logAry as $logDT){
	         
	        $logSec = strtotime($logDT);
	        $logDate = substr($logDT, 0,10);
	         
	        if(  ($logDate == $office_in_date)  &&  (abs($office_in_sec - $logSec) < (6*3600))  ){

	            return $logDT;
	        }
	    }
	    
	    //second try;
        foreach ($logAry as $logDT){
        
	        $logSec = strtotime($logDT);
	        $logDate = substr($logDT, 0,10);
        
        	if(  ($logDate == $office_in_date)  &&  ($logSec > $office_in_sec) && ($logSec <= $nextMedian)  ){
	
	            return $logDT;
	        }
        }

        //finally
	    return "";
	}
	
	public function getLastElemArray($logAry, $etime_schedule, $median, $prevMedian, $nextMedian){
	    
	    /*
	     * date = logout_date and abs(timeDif(logout, office_end)) <= 6*3600;
	     *
	     * or,
	     *
	     * date = logout_date and logout < office_end;
	     */
	    
	    $office_out_sec = strtotime($etime_schedule);
	    $office_out_date = substr($etime_schedule, 0,10);
	    
	    // first try
	    for($key = count($logAry)-1; $key>=0; $key--){
	    
	        $logDT = $logAry[$key];
	        $logSec = strtotime($logDT);
	        $logDate = substr($logDT, 0,10);
	         
	        if(  ($logDate == $office_out_date)  &&  (abs($office_out_sec - $logSec) < (6*3600))  ){
	    
	            return $logDT;
	        }
	    }
	     
	    //second try;
	    for($key = count($logAry)-1; $key>=0; $key--){
	         
	        $logDT = $logAry[$key];
	        $logSec = strtotime($logDT);
	        $logDate = substr($logDT, 0,10);
	         
	        if(  ($logDate == $office_out_date)  &&  ($logSec < $office_out_sec) && ($logSec>= $prevMedian) ){
	    
	            return $logDT;
	        }
	    }
	    
	    //finally 
	    return "";
	}
	
	
	public function toSort(&$officeSchedule, $str) {
	     
	    $sorter = array();
	    $ret = array();	    
	
	    foreach ($officeSchedule as $indx => $ary) {
	        
	        $sorter[$indx]= $ary[$str];
	    }
	    
	    asort($sorter);
	    $i=0;
	    foreach ($sorter as $indx => $val) {
	        
	        $ret[$i++] = $officeSchedule[$indx];
	    }

	    $officeSchedule = $ret;
	}
	
	
	
	
	
	/*
	 * Other functions
	 */

	
	public function auto_upload_fingerprint(){
	   
	    if(!$this->input->is_cli_request()){        
	        //die;
	    }
	    
	    //$server_ip = 'http://192.168.10.16/';
	    $server_ip = 'http://58.65.224.5:5000/';
	    
	    $userName = 'admin2';
	    $passWord = 'admin2';
	    $ch = curl_init();
	    curl_setopt_array(
	    $ch, array(
	    CURLOPT_URL => $server_ip.'status.htm',
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HTTPAUTH => CURLAUTH_ANY,
	    CURLOPT_USERPWD => $userName.":".$passWord
	    ));
	    
	    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    $output = curl_exec($ch);
	    
	    $year = date('y');
	    $month = date('m');
	    $day = date('d');	    

	    $secondUrl = $server_ip.'if.cgi?redirect=UserLog.htm&failure=fail.htm&type=search_user_log&type=0&sel=0&u_id=&even=0&even=0&even=0&even=0&even=0&year='.$year.'&mon='.$month.'&day='.$day.'&year='.$year.'&mon='.$month.'&day='.$day.'&card=0&card=0&card=0&card=0&card=0&card=0&card=0&card=0&fun_t=1&e_t=0&mode_t=1&output_defined_function=0';
	    curl_setopt($ch,CURLOPT_URL,$secondUrl);	    
	    $output = curl_exec($ch);

        $text_file_name = $month.$day.$year.".txt";
        $thirdUrl = $server_ip.$text_file_name;	    
	    $thirdUrl = urldecode($thirdUrl);
	    
	    set_time_limit(0);
	    $ch = curl_init($thirdUrl);
	    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    $result = curl_exec($ch);
	    curl_close ($ch);

	    /* upload part */
	    $lines = explode(PHP_EOL, $result);
    	    
	    //$files = file($lines);
	    
	    $iorecords = array();
	    $formatedRecords = array();
	    $eidAry =array();
	    
	    foreach($lines as $key=>$line) {
	        if(!$key) continue;
	    
	        $ary = explode(",",$line);
	    
	        if(count($ary)<9) continue;
	    
	        $emp_id = str_replace('"','',$ary[2]);
	        $date = str_replace('"','',$ary[4]);
	        $date = explode("/",$date);
	        $date = $date[2]."-".$date[0]."-".$date[1];
	        $time = str_replace('"','',$ary[5]);
	        
	        $gsl_emp_id_limit = $this->gsl_emp_id_limit;
	        if($emp_id >= $gsl_emp_id_limit) { continue; }
	        //else if($emp_id > 5000) $emp_id = $emp_id - 5000;
	        
	        //$eidAry[] = $emp_id;
	        $iorecords[] = array("date"=>$date,"emp_id"=>$emp_id,"stime"=>$time);	         
	        $formatedRecords[$emp_id][$date][] = $date." ".$time;
	    }	  
	    
	    if(empty($iorecords)){
            echo "No record!";
	        die;
	    }else{
	        
	        $iorecord = reset($iorecords);
	        $ioRecordDate = $iorecord['date'];
	    }

	    echo $ioRecordDate;
	    print_r($formatedRecords); 
	    
	    //$eidAry = array_unique($eidAry);
	    $eidAry = $this->user_model->getStaffEidArray();
	    
	    // add to database
	    $counts = $this->attendance_model->add_upload_all_iorecords($iorecords, $ioRecordDate);
	    
	    // check missing and sent mail only at 11:30pm/ 23:00
	    $time = date('H:i');	    
	    //$exacTime = "23:30";
	    $exacTime = "17:35";
	    $difTime = abs(strtotime($time) - strtotime($exacTime));
	    	    
	    if( $difTime <= 10*60){
	        
	        $fileModifiedDT = date('Y-m-d H:i:s');
	        //$fileModifiedDT = false;
	        	        
	        $prevDate = date("Y-m-d",strtotime($ioRecordDate." -1 day"));
	        $nextDate = date("Y-m-d",strtotime($ioRecordDate." +1 day"));
	         
	        /* gen Object */
	        $leaveDatesAll = $this->attendance_model->getLeaveDateByEids($eidAry, $prevDate, $ioRecordDate, $nextDate);
	        $logTimesAll = $this->attendance_model->getLogTimesbyEids($eidAry, $prevDate, $ioRecordDate, $nextDate);
	        $empInfosAll = $this->attendance_model->getAllEmployeeAttendanceInfo($eidAry);
	        $holidaysAll = $this->attendance_model->getHolidaysByEids($eidAry, $empInfosAll, $ioRecordDate, $nextDate);
	        $incidents = $this->attendance_model->getIncidents($prevDate, $nextDate);

	        //get Office Shcedule
	        $OfficeScheduleResultsAll = $this->attendance_model->getOfficeScheduleByOneDateByEids($eidAry, $empInfosAll, $prevDate, $nextDate, $this->rosterType, $this->default_office_time);
	        
	        if(count($empInfosAll) == 0 ){ return; } /* those employe info isn't in my local DB */

	        foreach ($formatedRecords as $eid=>$dateAry){
	             
	            foreach ($dateAry as $date=>$aryofLogs){
	        
	                sort($aryofLogs);
	                $dateAry[$date]= $aryofLogs;
	            }
	            ksort($dateAry);
	            $formatedRecords[$eid] = $dateAry;
	        }
	        	        
	        $emptyLogs = array( $prevDate => array(), $ioRecordDate => array() );
	        
           /* Check missing attendance */
	        foreach ($eidAry as $emp_id){
	            
	            /*Don't check missing attendance for */
    	        if($this->session->IsManagement($emp_id) ){
        	        continue;
        	    }        	    
	             
	            $step = 1;
	        
	            /* individual Data */
	            $leaveDates = $leaveDatesAll[$emp_id];
	            $logTimes = isset($logTimesAll[$emp_id]) ? $logTimesAll[$emp_id] : $emptyLogs;
	            $empInfos = $empInfosAll[$emp_id];
	            $holidays = $holidaysAll[$emp_id];
	        
	            $OfficeScheduleResults = $OfficeScheduleResultsAll[$emp_id];
	            $officeSchedules = $OfficeScheduleResults['office_schedule'];
	            $weekendsByNotScheduled = $OfficeScheduleResults['weekends'];
	            $this->toSort($officeSchedules, "stime");

	            $this->checkSingleDateMissingAttendance($emp_id, $prevDate, $ioRecordDate, $step, $fileModifiedDT, $leaveDates, $logTimes, $empInfos, $holidays, $incidents, $officeSchedules, $weekendsByNotScheduled);
	        }
            
	    }	   
        /* end of 11:30 timing Condition */

	}
	
	
	public function checkSingleDateMissingAttendance($emp_id, $sdate, $edate, $step, $fileModifiedDT, $leaveDates, $logTimes, $empInfos, $holidays, $incidents, $officeSchedules, $weekendsByNotScheduled){
        
	    $nextDate = date("Y-m-d",strtotime($edate." +1 day"));
	    
	    $weekends = $this->getWeekend($empInfos, $sdate, $nextDate);

	
	    foreach ($weekendsByNotScheduled as $wDate){
	
	        $weekends[$wDate] = true;
	    }
	    
	
	    foreach ($officeSchedules as $key =>$oSchedules){
	
	        $office_day = $oSchedules['noOfSlot'];
	        $stime_schedule = $oSchedules['stime'];
	        $etime_schedule = $oSchedules['etime'];
	        $sdate_schedule = substr($stime_schedule, 0,10);
	        $edate_schedule = substr($etime_schedule, 0,10);
	
	        if($leaveDates[$sdate_schedule]){
	             
	            if($leaveDates[$sdate_schedule] != 1){
	                // half day leave
	                 
	                if( $$office_day > 1){
	                    //double slot
	
	                    if($leaveDates[$sdate_schedule] == 0.5){
	                         
	                        $stime_schedule = date("Y-m-d H:i:s", strtotime($etime_schedule) - (12*3600)) ;
	                         
	                    }else if($leaveDates[$sdate_schedule] == 0.6){
	                         
	                        $etime_schedule = date("Y-m-d H:i:s", strtotime($stime_schedule) + (12*3600));
	                    }
	                }else{
	                    //single slot
	                    if($leaveDates[$sdate_schedule] == 0.5){
	
	                        $stime_schedule = date("Y-m-d H:i:s", strtotime($etime_schedule) - (4*3600)) ;
	
	                    }else if($leaveDates[$sdate_schedule] == 0.6){
	                         
	                        $etime_schedule = date("Y-m-d H:i:s", strtotime($stime_schedule) + (4*3600));
	                    }
	                }
	                $office_day -=  0.5;
	
	            } // half day leave end	             
	        }

	        $logData = $this->getLogtimeDetailsForMissing($empInfos, $logTimes, $stime_schedule, $etime_schedule, $office_day, $weekends, $leaveDates, $holidays, $incidents, $fileModifiedDT);
	        
	        if($step == 1){
	             
	            /* if any missing punch is founded then mail to curresponding employee */
	            if( !( $logData['logIn'] && $logData['logOut']) ){	                	               
	                 
	                $missingData = array();
	                $missingData['date'] = $sdate_schedule;
	                $mData = array();
	                $mData['emp_id'] = $emp_id;
	                 
	                if($logData['logIn']){
	                     
	                    $missingData['in'] = 'Yes';
	                }else{
	                     
	                    $missingData['in'] = "<span style='color:red;'> Missing</span>";
	                    $mData['date_time'] =  $stime_schedule;
	                    $login_missing_mail_sent =  $this->attendance_model->addMissingAttendanceMailSent($mData);
	                }
	                if($logData['logOut']){
	                     
	                    $missingData['out'] = 'Yes';
	                }else{
	                     
	                    $missingData['out'] = "<span style='color:red;'> Missing</span>";
	                    $mData['date_time'] =  $etime_schedule;
	                    $logout_missing_mail_sent = $this->attendance_model->addMissingAttendanceMailSent($mData);
	                }
	                 
	                if( (isset($login_missing_mail_sent) && $login_missing_mail_sent) || (isset($logout_missing_mail_sent) && $logout_missing_mail_sent)){
	                    //if mail isn't sent earlier, then sent only
	                     
	                    $subject = "Fingerprint Missing in IN/OUT Attendance.";
	                    $staffInfo = $this->user_model->getBriefInfo($emp_id);
	                    $sender['name'] = 'EMS';
	                    $sender['email'] = 'hrd@genuitysystems.com';
	
	                    $receiver = array();
	                    $obj = new stdClass();
	                    $obj->name = $staffInfo->name;
	                    $obj->email = $staffInfo->email;
	                    $receiver[] = $obj;
	
	                    if( $this->mailer->sendMailForMissingFingerprint($subject, $receiver, $staffInfo, $sender, $missingData,  $this->web_url ) ){
	                        //mail sent successfuly
	
	                    }else {
	                        //mail sent failed
	                        return false;
	                    }
	                     
	                } else{
	                    //not
	                    return true;
	                }
	            }
	            /* end of step 1; mail sent part */
	        }else if($step == 2){
	             
	            return $logData;
	        }
	
	
	    }
	    /*  end of schedule loop */
	}
	
	public function getLogtimeDetailsForMissing($empInfo, &$logTimes, $stime_schedule, $etime_schedule, $office_day, $weekends, $leaveDates, $holidays, $incidents, $fileModifiedDT){
	
	    $ret= array();
	    $sdate_schedule = substr($stime_schedule, 0,10);
	    $edate_schedule = substr($etime_schedule, 0,10);
	
	    $duration_schedule_sec = strtotime($etime_schedule) - strtotime($stime_schedule);
	    $duration_schedule =  $this->secondsToHours($duration_schedule_sec );
	     
	    /* median */
	    $halfTime =  $duration_schedule_sec/2 ;
	    $median = strtotime($stime_schedule) + $halfTime;
	    $prevMedian = strtotime($stime_schedule) - 6*3600;
	    $nextMedian = strtotime($etime_schedule) + 6*3600;
	
	
	    $isOffDay = false;
	    if($weekends[$sdate_schedule] || $weekends[$edate_schedule]
	        || $leaveDates[$sdate_schedule] || $leaveDates[$edate_schedule]
	        || $holidays[$sdate_schedule] || $holidays[$edate_schedule]){
	
	        $isOffDay = true;
	    }
	
	    if( isset($logTimes[$sdate_schedule]) || isset($logTimes[$edate_schedule]) ){
	
	        /*   **************** At lest One log ************  */
	
	        if($sdate_schedule == $edate_schedule){
	
	            $logAry = &$logTimes[$sdate_schedule];
	        }else{
	             
	            $logAry = array();
	            for ($idate = $sdate_schedule; $idate <= $edate_schedule;) {
	
	                if(isset($logTimes[$idate])){
	
	                    $refAry[$idate] = &$logTimes[$idate];
	                    $logAry = array_unique(array_merge($logAry ,$logTimes[$idate]));
	                }
	
	                $idate = date("Y-m-d", strtotime($idate . " +1 day"));
	            }
	            sort($logAry);
	        }
	
	        if(count($logAry) < 2){
	            	            
	            /* ################ one log ####################*/
	
	            $first = reset($logAry);
	            $firstSec = strtotime($first);
	
	            //remove from the $logAry, $logTimes
	            $this->deleteItemByVal($first, $logAry);
	
	            if( $firstSec < $median && $firstSec > $prevMedian){
	
	                $ret['logIn'] = true;
	
	                if($isOffDay){
	                    $ret['logOut'] = true;
	                }else{
	                     
	                    /* data record txt is uploaded before max logtime( time + 6h) */
	                    if($fileModifiedDT){
	                        if($fileModifiedDT < $nextMedian){
	
	                            $ret['logOut'] = true;
	                        }else{
	                            $ret['logOut'] = false;
	                        }
	                    }else{
	                        $ret['logOut'] = false;
	                    }
	
	                     
	                }
	            } else if( $firstSec > $median && $firstSec < $nextMedian){
	
	                $ret['logOut'] = true;
	                 
	                if($isOffDay){
	                    $ret['logIn'] = true;
	                }else{
	                    $ret['logIn'] = false;
	                }
	            } else{
	
	                if($isOffDay){
	                    $ret['logIn'] = true;
	                    $ret['logOut'] = true;
	                }else{
	                     
	                    /* data record txt is uploaded before max logtime( time + 6h) */
	                     
	                    if($fileModifiedDT){
	
	                        if($fileModifiedDT < ( strtotime($stime_schedule) + 6*3600 )){
	                             
	                            $ret['logIn'] = true;
	                        }else{
	                            $ret['logIn'] = false;
	                        }
	                         
	                        if($fileModifiedDT < $nextMedian){
	
	                            $ret['logOut'] = true;
	                        }else{
	                            $ret['logOut'] = false;
	                        }
	                    }else{
	                         
	                        $ret['logIn'] = false;
	                        $ret['logOut'] = false;
	                    }
	                     
	
	                }
	            }
	
	
	        } else if(count($logAry) >= 2){
	            /* ################ Atlest two log #################### */	             
	
	            $first = $this->getFistElemArray($logAry, $stime_schedule, $median, $prevMedian, $nextMedian);
	
	            //remove from the $logAry, $logTimes
	            if($sdate_schedule == $edate_schedule){
	
	                $this->deleteItemByVal($first, $logAry);
	            }else{
	                for ($idate = $sdate_schedule; $idate <= $edate_schedule;) {
	
	                    if(isset($refAry[$idate])){
	
	                        $this->deleteItemByVal($first, $refAry[$idate]);
	                        $this->deleteItemByVal($first, $logAry);
	                    }
	
	                    $idate = date("Y-m-d", strtotime($idate . " +1 day"));
	                }
	            }
	
	            $last = $this->getLastElemArray($logAry, $etime_schedule, $median, $prevMedian, $nextMedian);
	
	            //remove from the $logAry, $logTimes
	            if($sdate_schedule == $edate_schedule){
	
	                $this->deleteItemByVal($last, $logAry);
	            }else{
	
	                for ($idate = $sdate_schedule; $idate <= $edate_schedule;) {
	
	                    if(isset($refAry[$idate])){
	
	                        $this->deleteItemByVal($last, $refAry[$idate]);
	                    }
	
	                    $idate = date("Y-m-d", strtotime($idate . " +1 day"));
	                }
	            }
	
	            if(empty($first)){
	                 
	                /* data record txt is uploaded before max logtime( time + 6h) */
	                if($fileModifiedDT < ( strtotime($stime_schedule) + 6*3600 )){
	                     
	                    $ret['logIn'] = true;
	                }else{
	                    $ret['logIn'] = false;
	                }
	                 
	            } else{
	                $ret['logIn'] = true;
	            }
	
	            if(empty($last)){
	                 
	                /* data record txt is uploaded before max logtime( time + 6h) */
	                 
	                if($fileModifiedDT){
	                     
	                    if($fileModifiedDT < $nextMedian){
	                         
	                        $ret['logOut'] = true;
	                    }else{
	                        $ret['logOut'] = false;
	                    }
	                }else{
	                    $ret['logOut'] = false;
	                }
	
	            } else{
	                $ret['logOut'] = true;
	            }
	        }
	
	    } else{
	        /*   ************** Absent/ No log ************   */
	
	        /* data record txt is uploaded before max logtime( time + 6h) */
	        if($fileModifiedDT){
	             
	            if($fileModifiedDT < ( strtotime($stime_schedule) + 6*3600 )){
	
	                $ret['logIn'] = true;
	            }else{
	                $ret['logIn'] = false;
	            }
	             
	            if($fileModifiedDT < $nextMedian){
	                 
	                $ret['logOut'] = true;
	            }else{
	                $ret['logOut'] = false;
	            }
	        }else{
	             
	            $ret['logIn'] = false;
	            $ret['logOut'] = false;
	        }
	
	    }
	
	    return $ret;
	}
	

	public function upload(){
	    
	    $this->data['selDept'] = $this->input->get('dept');
	    $this->data['selStaff'] = $this->input->get('eid');
	     
	    $this->isLoggedIn();
	
	    if( !($this->data['isAdmin'] || $this->data['isManagement']) ) {
	        $this->load->view('not_found', $this->data);
	        return;
	    }
	    
	    $this->data['departmentLists'] = $this->data['departments'];
	    $this->data['staff_array'] = $this->user_model->getStaffArray();
	
	    $this->data["title"] = "Upload";
	    $this->data["sub_title"] = "Upload Attendance Information";
	
	    $this->view('upload_fingerprint',$this->data);
	}
	
	public function upload_manual_fingerprint(){
	
	    $this->isLoggedIn();
	    
	    if( !($this->data['isAdmin'] || $this->data['isManagement']) ) {
	        $this->load->view('not_found', $this->data);
	        return;
	    }

	    $radioBtnVal = $this->input->post('causeRadioBtn');
	    $staffId = $this->input->post('select_staff');
	    $dateIn = $this->input->post('dateIn');
	    $logIn = $this->input->post('logIn');
	    $logOut = $this->input->post('logOut');
	    
	    $staffInfo = $this->user_model->getBriefInfo($staffId);

	    if($radioBtnVal & $staffId && $dateIn && ($logIn || $logOut) ){

	    	// add to attendance table
	        $data = array();
	        $data['emp_id'] = $staffId;
	        $data['date'] = $dateIn;
	        
	        $flag1 = false;
	        $flag2 = false;
	        
	        if($logIn){
	            $data['stime'] =  date("H:i:s", strtotime($logIn));         
	            $flag1 = $this->attendance_model->add_upload_record($data);
	        }
	        
	        if($logOut){
	            $data['stime'] = date("H:i:s", strtotime($logOut));
	            $flag2 = $this->attendance_model->add_upload_record($data);
	        }
	        
	        if($flag1 || $flag2){
	            
	            /* sent mail to requester and his managers */
	            $mailData['message'] = ($radioBtnVal=='F') ? "Forgot to punch " : "Loss of finger's outer skin by peeling";
	            $mailData['date'] = $dateIn;
	            $mailData['logIn'] = $logIn;
	            $mailData['logOut'] = $logOut;
	            	            
	            $subject = "Manual fingerprint is added.";
	            
	            $senderInfo = $this->data["myInfo"];
	            $sender['name'] = $senderInfo->userName;
	            $sender['email'] = $senderInfo->email;
	            	            
	            
	            $receiver = array();
	            $receiver = $this->leave_model->getMailInfoByType('M', $staffInfo->dept_code);	            
	            $obj = new stdClass();
	            $obj->name = $staffInfo->name;
	            $obj->email = $staffInfo->email;
	            $receiver[] = $obj;	            
	            
	            if( $this->mailer->sendMailForAddingManualFingerprint($subject, $receiver, $staffInfo, $sender, $mailData,  $this->web_url ) ){
	                //mail sent successfuly
	                $this->data['message'] = "<span class='text-success'>Attendance information is successfully added.<span>";	            
	            }else {
	                //mail sent failed
	                $this->data['message'] = "<span class='text-warning'>Attendance information is successfully added but mail sending is failed.<span>";	                	            
	            }
	            	            
	        }else {
	            $this->data['message'] = "<span class='text-warning'>Attendance Information is not added.<span>";
	        }	        	        
	        
	    } else{
	        $this->data['message'] = "<span class='text-danger'>Failed; Required Filed: Cause, Cause, Date & at least one of Login Time, Logout Time<span>";
	        
	    }

	    $link =array();
	    $link['href'] = base_url()."attendance/upload?dept=".$staffInfo->dept_code."&eid=".$staffId;
	    $link['text'] = 'Go back to upload page';
	    $this->data['link'] = $link;
	    
	    $this->view('message_view', $this->data);
	}
	
	
	public function upload_fingerprint(){
	    
	    $this->isLoggedIn();
	    
	    if( !($this->data['isAdmin'] || $this->data['isManagement']) ) {
	        $this->load->view('not_found', $this->data);
	        return;
	    }
	
	    if(isset($_FILES['textFile'])) {
	
	        set_time_limit(0);
	        $filename = $_FILES['textFile']['name'];
	        $ext = substr($filename,-3);
	
	        if(strtoupper($ext)=="TXT"){
	             
	            $file_temp = $_FILES['textFile']['tmp_name'];
	            $lines = file($file_temp);
	             
	            /* modified date of upload file */
	            $fileModifiedDT = filemtime($file_temp);
	             
	            $eidAry = array();
	            $iorecords = array();
	            $formatedRecords = array();
	            foreach($lines as $key=>$line) {
	                if(!$key) continue;
	
	                $ary = explode(",",$line);
	
	                if(count($ary)<9) continue;
	
	                $emp_id = str_replace('"','',$ary[2]);
	                $date = str_replace('"','',$ary[4]);
	                $date = explode("/",$date);
	                $date = $date[2]."-".$date[0]."-".$date[1];
	                $time = str_replace('"','',$ary[5]);
	
	                if($emp_id >= 5000) { continue; }
	                //else if($emp_id > 5000) $emp_id = $emp_id - 5000;
	                 
	                $eidAry[] = $emp_id;
	                $iorecords[] = array("date"=>$date,"emp_id"=>$emp_id,"stime"=>$time);
	                 
	                $formatedRecords[$emp_id][$date][] = $date." ".$time;
	            }
	             
	            if(empty($iorecords)){
	                
	                $this->data['message'] = "<span style='color: red'> No Record is found!<span>";
	             
    	            $link =array();
    	            $link['href'] = base_url().'attendance/upload';
    	            $link['text'] = 'Go back to upload page';
    	            $this->data['link'] = $link;
    	             
    	            $this->view('message_view', $this->data);
	                return;
	            }else{
	                	
	                $iorecord = reset($iorecords);
	                $ioRecordDate = $iorecord['date'];
	            }
	            $eidAry = array_unique($eidAry);
	
	             
	            // add to database
	            $count = 0;
	            foreach($iorecords as $array) {
	                $total = 0;
	                $total = $this->attendance_model->get_upload_record($array);
	                 
	                if($total==0) {
	                    $flag = $this->attendance_model->add_upload_record($array);
	                    if($flag) $count++;
	                }
	            }
	
	            foreach ($formatedRecords as $eid=>$dateAry){
	
	                foreach ($dateAry as $date=>$aryofLogs){
	                     
	                    sort($aryofLogs);
	                    $dateAry[$date]= $aryofLogs;
	                }
	                ksort($dateAry);
	                $formatedRecords[$eid] = $dateAry;
	            }
	             
	            foreach ($formatedRecords as $emp_id=>$dateAry){
	                
	                /*Don't check missing attendance for Management */
	                if($this->session->IsManagement($emp_id) ){
	                    continue;
	                }
	                
	                reset($dateAry);
	                $sdate = key($dateAry);
	                end($dateAry);
	                $edate = key($dateAry);
	                /* for getting the nextsloted date */
	                $edate = date("Y-m-d",strtotime($edate." +1 day"));
	                $step = 1;

	                $this->checkMissingAttendance($emp_id, $sdate, $edate, $step, $fileModifiedDT);
	                 
	            }
	            /*  end of Employee/$formatedRecords loop */
	
	
	            $this->data['message'] = "<span style='color: green'>Done; $count entry(s) added to database<span>";
	             
	            $link =array();
	            $link['href'] = base_url().'attendance/upload';
	            $link['text'] = 'Go back to upload page';
	            $this->data['link'] = $link;
	             
	            $this->view('message_view', $this->data);
	
	        } else {
	            $this->data['message'] = "Wrong File Format";
	            $this->view('error', $this->data);
	        }
	    }else{
	        echo '404 error!';
	    }
	}
	
	
	public function checkMissingAttendance($emp_id, $sdate, $edate, $step, $fileModifiedDT){
	    
	    $logTimes = $this->attendance_model->getLogTimes($emp_id, $sdate, $edate);
	    
	    $empInfo = $this->attendance_model->getEmployeeInfo($emp_id);
	    if(count($empInfo) == 0 ){ return; } /* those employe info isn't in my local DB */
	    	    
	    $leaveDates = $this->attendance_model->getLeaveDates($emp_id, $sdate, $edate);
	    $holidays = $this->attendance_model->getHolidays($empInfo, $sdate, $edate);
	    $incidents = $this->attendance_model->getIncidents($sdate, $edate);
	    $this->setLateEarlyRequest($emp_id, $sdate, $edate);
	     
	    //get Office Shcedule
	    $result = $this->attendance_model->getOfficeScheduleByOneDate($empInfo, $sdate, $edate, $this->rosterType, $this->default_office_time);
	    $weekendsByNotScheduled = $result['weekends'];
	    $officeSchedules = $result['office_schedule'];
	    $this->toSort($officeSchedules, "stime");

	    $weekends = $this->getWeekend($empInfo, $sdate, $edate);
	     
	    foreach ($weekendsByNotScheduled as $wDate){
	         
	        $weekends[$wDate] = true;
	    }
	     
	    foreach ($officeSchedules as $key =>$oSchedules){
	         
	        $office_day = $oSchedules['noOfSlot'];
	        $stime_schedule = $oSchedules['stime'];
	        $etime_schedule = $oSchedules['etime'];
	        $sdate_schedule = substr($stime_schedule, 0,10);
	        $edate_schedule = substr($etime_schedule, 0,10);
	         
	        if($leaveDates[$sdate_schedule]){
	    
	            if($leaveDates[$sdate_schedule] != 1){
	                // half day leave
	                 
	    
	                if( $$office_day > 1){
	                    //double slot
	                     
	                    if($leaveDates[$sdate_schedule] == 0.5){
	    
	                        $stime_schedule = date("Y-m-d H:i:s", strtotime($etime_schedule) - (12*3600)) ;
	    
	                    }else if($leaveDates[$sdate_schedule] == 0.6){
	    
	                        $etime_schedule = date("Y-m-d H:i:s", strtotime($stime_schedule) + (12*3600));
	                    }
	                }else{
	                    //single slot
	                    if($leaveDates[$sdate_schedule] == 0.5){
	                         
	                        $stime_schedule = date("Y-m-d H:i:s", strtotime($etime_schedule) - (4*3600)) ;
	                         
	                    }else if($leaveDates[$sdate_schedule] == 0.6){
	    
	                        $etime_schedule = date("Y-m-d H:i:s", strtotime($stime_schedule) + (4*3600));
	                    }
	                }
	                $office_day -=  0.5;
	                 
	            } // half day leave end
	    
	        }
	        
	        $logData = $this->getLogtimeDetailsForMissing($empInfo, $logTimes, $stime_schedule, $etime_schedule, $office_day, $weekends, $leaveDates, $holidays, $incidents, $fileModifiedDT);        
	        
	        if($step == 1){
	            
	            /* if any missing punch is founded then mail to curresponding employee */
	            if( !( $logData['logIn'] && $logData['logOut']) ){
         
	                $missingData = array();
	                $missingData['date'] = $sdate_schedule;	                
	                $mData = array();
	                $mData['emp_id'] = $emp_id;
	                
	                if($logData['logIn']){
	                    
	                    $missingData['in'] = 'Yes';
	                }else{
	                    
	                    $missingData['in'] = "<span style='color:red;'> Missing</span>";
	                    $mData['date_time'] =  $stime_schedule;
	                    $login_missing_mail_sent =  $this->attendance_model->addMissingAttendanceMailSent($mData);
	                }
	                if($logData['logOut']){
	                    
	                    $missingData['out'] = 'Yes';
	                }else{
	                    
	                    $missingData['out'] = "<span style='color:red;'> Missing</span>";
	                    $mData['date_time'] =  $etime_schedule;
	                    $logout_missing_mail_sent = $this->attendance_model->addMissingAttendanceMailSent($mData);
	                }
	                
	                if( (isset($login_missing_mail_sent) && $login_missing_mail_sent) || (isset($logout_missing_mail_sent) && $logout_missing_mail_sent)){
	                    //if mail isn't sent earlier, then sent only 
	                    
	                    $subject = "Fingerprint Missing in IN/OUT Attendance.";
	                    $staffInfo = $this->user_model->getBriefInfo($emp_id);
	                    $sender['name'] = 'EMS';
	                    $sender['email'] = 'hrd@genuitysystems.com';
	                     
	                    $receiver = array();
	                    $obj = new stdClass();
	                    $obj->name = $staffInfo->name;
	                    $obj->email = $staffInfo->email;
	                    $receiver[] = $obj;
	                     
	                    if( $this->mailer->sendMailForMissingFingerprint($subject, $receiver, $staffInfo, $sender, $missingData,  $this->web_url ) ){
	                        //mail sent successfuly
	                         
	                    }else {
	                        //mail sent failed
	                        return false;
	                         
	                    }
	                    
	                } else{
	                    //not
	                    return true;
	                    
	                }	                
	            }
	        /* end of step 1; mail sent part */
	        }else if($step == 2){
	            
	            return $logData;	            	            
	        }

	         
	    }
	    /*  end of schedule loop */
	}

	
	public function request(){
	    
	    $this->isLoggedIn();
	    
	    $this->data['leave_type'] = $this->late_early_leave;
	     
	    $this->data['title'] = "att_request";
	    $this->data['sub_title'] = "Request";
	    $this->view('att_request', $this->data);
	}
	
	public function send_request()
	{
	    $this->isLoggedIn();
	    
	    if (! empty($_POST)) {
	        $data['emp_id'] = $this->myEmpId;
	        $data['date'] = $_POST['requestDate'];
	        $data['reason'] = $_POST['reason'];
	        $leaveInfo = new stdClass();
	        $leaveInfo->reason = $data['reason'];
	        $leaveInfo->date = $data['date'];
	
	        if(isset($_POST['late'])){
	            $data['late_req'] = 'R';
	            $leaveInfo->message = $this->late_early_leave['l'];
	        }else{
	            $data['late_req'] = 'N';
	        }
	        if(isset($_POST['early'])){
	            $data['early_req'] = 'R';
	            $leaveInfo->message = $this->late_early_leave['e'];
	        }else{
	            $data['early_req'] = 'N';
	        }
	        if(isset($_POST['absent'])){
	            $data['absent_req'] = 'R';
	            $leaveInfo->message = $this->late_early_leave['a'];
	        }else{
	            $data['absent_req'] = 'N';
	        }
	        if(isset($_POST['special'])){
	            $data['special_req'] = 'R';
	            $leaveInfo->message = $this->late_early_leave['u'];
	        }else{
	            $data['special_req'] = 'N';
	        }
	        if( $data['early_req'] == 'R' && $data['late_req'] == 'R'){
	            $leaveInfo->message = $this->late_early_leave['l']." & ".$this->late_early_leave['e'];
	        }

	        if (!empty($data['reason'])){
	            $data['reason'] = addslashes($data['reason']);
	        }
	        //add to database
	        $insert_id = $this->attendance_model->addRequest($data);
	
	        if (!empty($insert_id)){
	            $this->config->load('gsladmin_conf');
    	        $myInfo = $this->data["myInfo"];
    	        if($this->session->IsManager($this->myEmpId)){
    	            $rec = $this->config->item('att_receiver');
    	            $receiver = $this->user_model->getMailInfoByIds($rec);    	            
    	        }else{    	            
    	            $receiver = $this->leave_model->getMailInfoByType('M', $myInfo->userDeptCode);
    	        }
    	        
    	        $senderInfo = $this->data["myInfo"];
    	        $subject = "Request for early/late leave from ".$senderInfo->userName." on EMS.";
    	
    	        //Send mail to manager
    	        if($this->mailer->lateEarlyMailToMan($subject, $receiver, $senderInfo, $leaveInfo, $this->web_url)) {
    	            $return['status'] = true;
    	            $return['msg'] = "Late/early leave request has been sent successfully.";
    	        }else{
    	            $return['status'] = true;
    	            $return['msg'] = "Mail isn't sent successfully.";
    	        }
	        }else {
	            $return['status'] = false;
	            $return['msg'] = "Failed to send late/early leave request!";
	        }
	    } else {
	        $return['status'] = false;
	        $return['msg'] = "Late/early leave request has been failed.";
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	
	public function pending() {
	    
	    $this->isLoggedIn();
	    
	    //================Approval request========================
	
	    $dept_ary = array();
	    $type =array(
	        'admin' => false,
	        'manager'=>false,
	        'management'=>false,
	    );	    

	    if($this->session->IsAdmin($this->myEmpId)) $type['admin'] = true;
	    if($this->session->IsManager($this->myEmpId)) $type['manager'] = true;
	    if($this->session->IsManagement($this->myEmpId)) $type['management'] = true;
	
	    if( $type['manager'] || $type['management'] || $type['admin']) {
	        
	        if(!$type['management'] && $type['manager']){
	            $dept_ary = $this->session->getManagerDepartments($this->myEmpId);
	        }


	    } else{
	        exit("You have no privilege to access this page!");
	    }
	        	
	    $myInfo = $this->data["myInfo"];
	    $approvalRequest = array();
	    
		if( $type['manager'] ) {
	        $approvalRequest = $this->attendance_model->getPendingRequest($dept_ary, 'approval', $myInfo->userDeptCode, $this->myEmpId);
	    }elseif( $type['management'] ) {
	        $approvalRequest = $this->attendance_model->getPendingRequest($dept_ary, 'approval', $myInfo->userDeptCode);
	    }  

	    		
	    //===============Verification Request======================
	
	    $verificationRequest = array();
	    if($type['admin']) {
	        //echo 'dfdf';
	        $verificationRequest = $this->attendance_model->getPendingRequest($dept_ary, 'verification', $myInfo->userDeptCode);
	    }
	
	    //$this->data["title"] = "Approval Request";
	
	    $this->data["late_early_leave"] = $this->late_early_leave;
	    $this->data["type"] = $type;
	    $this->data["title"] = "req_pending";
	    $this->data["sub_title"] = "Pending Request";
	    $this->data["approvalRequest"] = $approvalRequest;
	    $this->data["verificationRequest"] = $verificationRequest;
	    //$this->load->view('leaveform', $this->data);
	    $this->view('att_pending', $this->data);
	}
	
	public function approve($id=""){
	    
	    $this->isLoggedIn();
	    
	    if( !($this->session->IsManager($this->myEmpId) || $this->session->IsManagement($this->myEmpId)) ){
	        echo "You don't have prividege to this page!";
	        return;
	    }
	
	    $leaveInfo = $this->attendance_model->getRequestInfo($id);
	    $ms = '';
	    if($leaveInfo->late_req == 'R')  $ms = $this->late_early_leave['l'];
	    if($leaveInfo->early_req == 'R') $ms = $this->late_early_leave['e'];
	    if($leaveInfo->late_req == 'R' && $leaveInfo->early_req == 'R'  ) $ms = $this->late_early_leave['l']." and ".$this->late_early_leave['e'];
	    if($leaveInfo->absent_req == 'R') $ms = $this->late_early_leave['a'];
	    if($leaveInfo->special_req == 'R') $ms = $this->late_early_leave['u'];
	    $leaveInfo->message = $ms;
	
	    //update to database
	    $data['approved'] = 'Y';
	    $data['approved_by'] = $this->myEmpId;
	    $data['approved_time'] = date('Y-m-d  h:i:s');
	    $flag = $this->attendance_model->update_late_early_req($id, $data);
	
	    if($flag){
	        //send mail to admin
	        
	        //from ".$staffInfo->name." on EMS";
	        $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	        
	        $subject = "Request for early/late leave from ".$staffInfo->name." on EMS.";
	        $receiver = array();
	
// 	        $obj = new stdClass();
// 	        $obj->name = $staffInfo->name;
// 	        $obj->email = $staffInfo->email;
// 	        $receiver[] = $obj;
	        
	        $adminAry = $this->session->GetAdminArray();
	        $receiver = $this->user_model->getMailInfoByIds($adminAry);
	
	        if($this->mailer->requestApproveMailByManager($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url)){
	            
	            $return['msg'] = $this->message['update_s'].' and '.$this->message['mail_s'];
	        }else {
	            
	            $return['msg'] = $this->message['update_s'].' but '.$this->message['mail_f'];
	        }
	        // return info
	        $return['status'] = true;
	    }else{
	        $return['status'] = false;
	        $return['msg'] = $this->message['update_f'].' and '.$this->message['mail_f'];
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	public function verify($id = ""){
	       
	    $this->isLoggedIn();
	       
	    if (! $this->session->IsAdmin($this->myEmpId)) {
	        echo "You don't have prividege to this page!";
	        return;
	    }
	
	    $leaveInfo = $this->attendance_model->getRequestInfo($id);
	    $ms = '';
	    if ($leaveInfo->late_req == 'R')
	        $ms = $this->late_early_leave['l'];
	    if ($leaveInfo->early_req == 'R')
	        $ms = $this->late_early_leave['e'];
	    if ($leaveInfo->late_req == 'R' && $leaveInfo->early_req == 'R')
	        $ms = $this->late_early_leave['l'] . " and " . $this->late_early_leave['e'];
	    if ($leaveInfo->absent_req == 'R')
	        $ms = $this->late_early_leave['a'];
	    if ($leaveInfo->special_req == 'R')
	        $ms = $this->late_early_leave['u'];
	    $leaveInfo->message = $ms;
	
	    $data['verified'] = 'Y';
	    $data['verified_by'] = $this->myEmpId;
	    $data['verified_time'] = date('Y-m-d  h:i:s');
	
	    $flag = $this->attendance_model->update_late_early_req($id, $data);
	    if ($flag) {
	        // send mail
	        $subject = $subject = "Request for early/late leave from ".$staffInfo->name." has been verified on EMS";
	        $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	        $receiver = array();
	        $obj = new stdClass();
	        $obj->name = $staffInfo->name;
	        $obj->email = $staffInfo->email;
	        $receiver[] = $obj;
	        $receiver[] = $this->user_model->getBriefInfo($leaveInfo->approved_by);
	
	        if ($this->mailer->requestVerifyMailByAdmin($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url)) {
	            $return['msg'] = $this->message['update_s'] . ' and ' . $this->message['mail_s'];
	        } else {
	            $return['msg'] = $this->message['update_s'] . ' but ' . $this->message['mail_f'];
	        }
	        // return info
	        $return['status'] = true;
	    } else {
	        $return['status'] = false;
	        $return['msg'] = $this->message['update_f'] . ' and ' . $this->message['mail_f'];
	    }
	
	    echo json_encode($return);
	    die();
	}
	
	public function refuse($l_id){
	    
	    $this->isLoggedIn();
	    
	    $manager_sent = false;
	    $admin_sent = false;
	
	    if($this->uri->segment(4)){
	        if($this->uri->segment(4) == 'approve'){
	            $manager_sent = true;
	        }else if($this->uri->segment(4) == 'verify'){
	            $admin_sent = true;
	        }
	    }
	     
	    $excuse = $_POST['excuse'];
	
	    $leaveInfo = $this->attendance_model->getRequestInfo($l_id);
	
	    $ms = '';
	    if($leaveInfo->late_req == 'R')  $ms = $this->late_early_leave['l'];
	    if($leaveInfo->early_req == 'R') $ms = $this->late_early_leave['e'];
	    if($leaveInfo->late_req == 'R' && $leaveInfo->early_req == 'R'  ) $ms = $this->late_early_leave['l']." and ".$this->late_early_leave['e'];
	    if($leaveInfo->absent_req == 'R') $ms = $this->late_early_leave['a'];
	    if($leaveInfo->special_req == 'R') $ms = $this->late_early_leave['u'];
	    $leaveInfo->message = $ms;
	
	    $flag = $this->attendance_model->del_req($l_id);
	
	    if($flag){
	        //send mail to Staff
	        $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	        $receiver = array();
	
	        $obj = new stdClass();
	        $obj->name = $staffInfo->name;
	        $obj->email = $staffInfo->email;
	        $receiver[] = $obj;
	
	        if($manager_sent){
	            $subject = "Leave request's approval has been refused by manager";
	            $message = "Your late/early leave request has been refused by manager for the following resaon.<br><b>Reason</b> : $excuse";
	        }else if($admin_sent){
	            $subject = "Leave request's verification has been refused by admin";
	            $receiver[] = $this->user_model->getBriefInfo($leaveInfo->approved_by);
	            $message = "Your late/early leave request has been refused by admin for the following resaon.<br><b>Reason</b> : $excuse";
	        }
	
	        if($this->mailer->requestRefuseMail($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url, $message)) {
	            $return['msg'] = $this->message['delete_s'].' and '.$this->message['mail_s'];
	        }else {
	            $return['msg'] = $this->message['delete_s'].' but '.$this->message['mail_f'];
	        }
	        // return info
	        $return['status'] = true;
	    } else {
	        $return['status'] = false;
	        $return['msg'] = $this->message['delete_f'].' and '.$this->message['mail_f'];
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	
	public function missing($date =""){
	    
	    $this->isLoggedIn();
	    
	    $eid = $this->uri->segment(4) ? $this->uri->segment(4) : '';

	    if( empty($date) || empty($eid) || ($this->myEmpId != $eid)){
	        
	        echo "400 Bad Request";
	        die;
	    }else{	        	       
	        
	        $sdate = $date;
	        $edate = date('Y-m-d', strtotime( $date.' +1 day'));
	        $step = 2;
	        $fileModifiedDT = false;	  
	        	        	       
	        $logData = $this->checkMissingAttendance($eid ,$sdate, $edate, $step, $fileModifiedDT);
	        
	        if($logData['logIn'] && $logData['logOut'] ){
	            
	            
	            $this->data['message'] = "<span style='color: red'> Request is failed.";
	             
	            $link =array();
	            $link['href'] = base_url().'attendance/show';
	            $link['text'] = 'Go back to Attendance Page';
	            $this->data['link'] = $link;
	             
	            $this->view('message_view', $this->data);
	            return;
	            
	        }else{
	            $this->data['logData'] = $logData;
	            $this->data['date'] = $date;
	             
	            $this->data['title'] = "missing";
	            $this->data['sub_title'] = "IN/OUT Missing";
	            $this->view('missing_punch', $this->data);
	        }

	    }
	}
	
	public function missing_mail_to_man(){
	    
	    $this->isLoggedIn();
	    
	    $date = isset($_POST['date']) ? $_POST['date'] : '';
        $inAtt = isset($_POST['inAtt']) ? $_POST['inAtt'] : NULL;
        $outAtt = isset($_POST['outAtt']) ? $_POST['outAtt'] : NULL;
        $reason = isset($_POST['reason']) ? $_POST['reason'] : "";
        
        if(!empty($date)){
            
            if(!is_null($inAtt) || !is_null($outAtt)){
                $attData = array();
                $attData['emp_id'] = $this->myEmpId;
                $attData['date'] = $date;
                $attData['in'] = $inAtt;
                $attData['out'] = $outAtt;
                $attData['reason'] = $reason;
                $attData['status'] = 'A';                               
                
                $flag = $this->attendance_model->addMissingAttendanceInfo($attData);
                
                if($flag){
                    //sent mail to Manager
                    
                    $myInfo = $this->data["myInfo"];
                    $receiver = $this->leave_model->getMailInfoByType('M', $myInfo->userDeptCode);
                    
                    
                    $senderInfo = $this->data["myInfo"];
                    $subject = "Request for missing attendance from ".$senderInfo->userName." on EMS.";
                    
                    if($this->mailer->sendMailForFingerprintToMan($subject, $receiver, $senderInfo, $attData, $this->web_url)) {
                        

                        
//                         $return['status'] = true;
//                         $return['msg'] = "Leave request has been sent successfully.";
                    }else{
//                         $return['status'] = true;
//                         $return['msg'] = "Mail isn't sent successfully.";
                    }
                }
                                
                
                if($flag){
                    $this->data['message'] = "<span style='color: green'>Request is stored and a noticfiaction mail is sent Successfully to Manager.";
                }else{
                    $this->data['message'] = "<span style='color: red'> Request is failed.";
                }
                
                 
                $link =array();
                $link['href'] = base_url().'attendance/show';
                $link['text'] = 'Go back to Attendance Page';
                $this->data['link'] = $link;
                 
                $this->view('message_view', $this->data);
            }
        }
	    
	}
	
	
	public function missing_pending(){
	    
	    $this->isLoggedIn();
	    
	    //================Approval request========================
	    
	    $dept_ary = array();
	    $type =array(
	        'admin' => false,
	        'manager'=>false,
	        'management'=>false,
	    );
	    
	    if($this->session->IsAdmin($this->myEmpId)) $type['admin'] = true;
	    if($this->session->IsManager($this->myEmpId)) $type['manager'] = true;
	    if($this->session->IsManagement($this->myEmpId)) $type['management'] = true;
	    
	    if( $type['manager'] || $type['management'] || $type['admin']) {
	         
	        $dept_ary = $this->session->getManagerDepartments($this->myEmpId);
	    } else{
	        exit("You have no privilege to access this page!");
	    }	    
	    
	    $myInfo = $this->data["myInfo"];
	    $missingApprovalRequest = array();
	    
	    
	     
	    if( $type['manager'] || $type['management'] ) {
	         
	        $missingApprovalRequest = $this->attendance_model->getMissingPendingRequest($dept_ary, 'approval', $myInfo->userDeptCode);
	    }
	     
	    //===============Verification Request======================
	    
	    $missingVerificationRequest = array();
	    if($type['admin']) {
	        //echo 'dfdf';
	        $missingVerificationRequest = $this->attendance_model->getMissingPendingRequest($dept_ary, 'verification', $myInfo->userDeptCode);

	    }
	    
	    //$this->data["title"] = "Approval Request";
	    
	    $this->data["type"] = $type;
	    $this->data["title"] = "missing_req_pending";
	    $this->data["sub_title"] = "Missing Attendace Pending Request";
	    $this->data["missingApprovalRequest"] = $missingApprovalRequest;
	    $this->data["missingVerificationRequest"] = $missingVerificationRequest;
	    
	    $this->view('missing_att_pending', $this->data);
	}
	
	public function missing_approve($id=""){
	    
	    $this->isLoggedIn();
	    
	    if( !($this->session->IsManager($this->myEmpId) || $this->session->IsManagement($this->myEmpId)) ){
	        echo "You don't have prividege to this page!";
	        return;
	    }
	
	    $missingReqInfo = $this->attendance_model->getMissingReqInfo($id);
	    
	
	    //update to database
	    $data['status'] = 'B';
	    $data['m_approved_date'] = date('Y-m-d');
	    $data['manager_id'] = $this->myEmpId;
	    
	    $flag = $this->attendance_model->updateMissingAtt($id, $data);
	
	    if($flag){
	        //send mail
	        $subject = "Missing Attendance:approved and waiting for admin's verification.";
	        $staffInfo = $this->user_model->getBriefInfo($missingReqInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	        $receiver = $this->user_model->getAdminInfo();
	
	        if($this->mailer->sendMailForFingerprintByManToAdmin($subject, $receiver, $senderInfo, $staffInfo, $missingReqInfo, $this->web_url)){
	            
	            $return['msg'] = $this->message['update_s'].' and '.$this->message['mail_s'];
	        }else {
	            $return['msg'] = $this->message['update_s'].' but '.$this->message['mail_f'];
	        }
	        // return info
	        $return['status'] = true;
	    }else{
	        $return['status'] = false;
	        $return['msg'] = $this->message['update_f'].' and '.$this->message['mail_f'];
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	public function missing_refuse($id){
	    
	    $this->isLoggedIn();
	    
	    $manager_sent = false;
	    $admin_sent = false;
	    
	    $excuse = $_POST['excuse'];
	    $missingReqInfo = $this->attendance_model->getMissingReqInfo($id);
	    
	    if($this->uri->segment(4)){
	        if($this->uri->segment(4) == 'manager'){
	            
	            $manager_sent = true;
	            $uData['status'] = 'E';
	            $uData['manager_id'] = $this->myEmpId;
	            
	        }else if($this->uri->segment(4) == 'admin'){
	            
	            $admin_sent = true;
	            $uData['status'] = 'D';
	            $uData['admin_id'] = $this->myEmpId;
	        }
	    }
	    
	    //update to Database
	    $flag = $this->attendance_model->updateMissingAtt($id, $uData);
	
	    if($flag){
	        //send mail to Staff
	        $staffInfo = $this->user_model->getBriefInfo($missingReqInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	        $receiver = array();
	
	
	        if($manager_sent){
	            
	            $obj = new stdClass();
	            $obj->name = $staffInfo->name;
	            $obj->email = $staffInfo->email;
	            $receiver[] = $obj;

	            $subject = "Missing attendance request's approval has been refused by manager";
	            $message = "Missing attendance request's approval has been refused by manager for the following resaon.<br><b>Reason</b> : $excuse";
	        
	        }else if($admin_sent){
	            
	            $manInfo = $this->user_model->getBriefInfo($missingReqInfo->manager_id);
	            $obj = new stdClass();
	            $obj->name = $manInfo->name;
	            $obj->email = $manInfo->email;
	            $receiver[] = $obj;
	            
	            $subject = "Missing attendance request's verification has been refused by admin";	            
	            $message = "His missing attendance request's verification has been refused by me for the following resaon.<br><b>Reason</b> : $excuse";
	        }
	
	        if($this->mailer->sendRefuseMailForFingerprint($subject, $receiver, $senderInfo, $staffInfo, $missingReqInfo, $message, $this->web_url)) {
	            
	            $return['msg'] = $this->message['mail_s'];
	        }else {
	            
	            $return['msg'] = $this->message['mail_f'];
	        }
	        // return info
	        $return['status'] = true;
	        
	    } else {
	        
	        $return['status'] = false;
	        $return['msg'] = 'error occurs! try again.';
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	public function missing_verify($id = "")
	{   
	    $this->isLoggedIn();
	    
	    if (! $this->session->IsAdmin($this->myEmpId)) {
	        echo "You don't have prividege to this page!";
	        return;
	    }
	
	    $missingReqInfo = $this->attendance_model->getMissingReqInfo($id);
	    
	    //update to database
	    $data['status'] = 'C';
	    $data['a_verified_date'] = date('Y-m-d');
	    $data['admin_id'] = $this->myEmpId;
	
	    $flag = $this->attendance_model->updateMissingAtt($id, $data);
	    
	    if ($flag) {
	        
	        //finally add to attendance table
	        $rData = array();
	        $rData['emp_id'] = $missingReqInfo->emp_id;
	        $rData['date'] = $missingReqInfo->date;
	        
	        if($missingReqInfo->in != null){
	            $rData['stime'] = $missingReqInfo->in;
	            
	            $flag = $this->attendance_model->add_upload_record($rData);
	        }
	        if($missingReqInfo->out != null){
	            $rData['stime'] = $missingReqInfo->out;
	            
	            $flag = $this->attendance_model->add_upload_record($rData);
	        }
	        	        	        	      
	        
	        // send mail
	        $subject = "Missing attendance request has been verified by admin";
	        $staffInfo = $this->user_model->getBriefInfo($missingReqInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	        $receiver = array();
	        $obj = new stdClass();
	        $obj->name = $staffInfo->name;
	        $obj->email = $staffInfo->email;
	        $receiver[] = $obj;
	        $receiver[] = $this->user_model->getBriefInfo($missingReqInfo->manager_id);
	
	        if($this->mailer->sendMailForFingerprintByAdminToEmp_Man($subject, $receiver, $senderInfo, $staffInfo, $missingReqInfo, $this->web_url)){
	            
	            $return['msg'] = $this->message['update_s'].' and '.$this->message['mail_s'];
	        }else {
	            $return['msg'] = $this->message['update_s'].' but '.$this->message['mail_f'];
	        }
	        // return info
	        $return['status'] = true;
	    } else {
	        $return['status'] = false;
	        $return['msg'] = $this->message['update_f'] . ' and ' . $this->message['mail_f'];
	    }
	
	    echo json_encode($return);
	    die();
	}
	
	
	

}