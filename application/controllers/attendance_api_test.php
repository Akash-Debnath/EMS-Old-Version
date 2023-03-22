<?php
class Attendance_api_test extends CI_Controller {

	public $gsl_emp_id_limit = 5000;
	public $managementAry = array();
	public $rosterType = array("CA"=>"S","SY"=>"S");//S=Slot,R=Regular
	public $default_office_time = array('start'=> '09:00:00', 'end' => '18:00:00', );
	public $default_weekend = array("sun"=>"N","mon"=>"N","tue"=>"N","wed"=>"N","thu"=>"N","fri"=>"Y", "sat"=>"Y");
	public $web_url = "http://www.genuitysystems.com/staff/ems/";
	public $web_url_main = "http://www.genuitysystems.com/staff/";
	

	public function __construct() {
	    parent::__construct();
		date_default_timezone_set("Asia/Dhaka");
		$this->load->model('user_model');
		$this->load->model('attendance_model');
		$this->load->library('mailer');
        
		$this->managementAry = $this->getManagementAry();

		$this->load->library('session');
		$mySessionId = $this->session->GetLoginId();
		if (empty($mySessionId) || $mySessionId != '0181'){
		    echo "Access Denied...";
		    exit();
		}
	}	
	
	public function auto_upload_fingerprint(){

	    $thirdUrl = "http://www.genuitysystems.com/staff/ems/070417.txt";
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
	    $iorecords = array();
	     
	    foreach($lines as $key=>$line) {
	        if(!$key) continue;
	         
	        $ary = explode(",",$line);
	         
	        if(count($ary)<9) continue;
	         
	        $emp_id = str_replace('"','',$ary[1]);
	        $date = str_replace('"','',$ary[4]);
	        $date = explode("/",$date);
	        $date = $date[2]."-".$date[0]."-".$date[1];
	        $time = str_replace('"','',$ary[5]);
	        

	        if($emp_id >= $this->gsl_emp_id_limit) { continue; }

		if(strlen($emp_id) < 4){
			if(strlen($emp_id) == 1){
				$emp_id = '000'.$emp_id;
			}elseif(strlen($emp_id) == 2){
				$emp_id = '00'.$emp_id;
			}elseif(strlen($emp_id) == 3){
				$emp_id = '0'.$emp_id;
			}
		}

	        $iorecords[] = array("date"=>$date,"emp_id"=>$emp_id,"stime"=>$time);	    }
	     
	    if(empty($iorecords)){	        
	        return;
	    }else{
	         
	        $iorecord = reset($iorecords);
	        $ioRecordDate = $iorecord['date'];
	    }	     	    
	    print_r($iorecords); print_r($ioRecordDate);
	    // add to database
	    $counts = $this->attendance_model->add_upload_all_iorecords($iorecords, $ioRecordDate);
	}

	
	private function missing_attendance(){

	    $eidAry = $this->attendance_model->getActiveStaffEidArray();

	    $ioRecordDate =  date('Y-m-d');
	    $fileModifiedDT = date('Y-m-d H:i:s');
	    
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
	    
	    $emptyLogs = array( $prevDate => array(), $ioRecordDate => array() );
	    
	    /* Check missing attendance */
	    foreach ($eidAry as $emp_id){

	        /*Don't check for Manangement*/	         
	        if(in_array($emp_id, $this->managementAry) ){
	            continue;
	        }
	        /*Don't have mail*/
	        $staffInfo = $this->user_model->getBriefInfo($emp_id);
	        if( empty($staffInfo->mail) ){
	            continue;
	        }
	        /*$empInfos not found*/
	        $empInfos = $empInfosAll[$emp_id];
	        if(count($empInfos) == 0 ){ continue; }
	        
	        
	        $leaveDates = $leaveDatesAll[$emp_id];
	        $logTimes = isset($logTimesAll[$emp_id]) ? $logTimesAll[$emp_id] : $emptyLogs;	        
	        $holidays = $holidaysAll[$emp_id];
	        
	        $OfficeScheduleResults = $OfficeScheduleResultsAll[$emp_id];
	        $officeSchedules = $OfficeScheduleResults['office_schedule'];
	        $weekendsByNotScheduled = $OfficeScheduleResults['weekends'];
	        $this->toSort($officeSchedules, "stime");
	        	       
	    
	        $this->checkSingleDateMissingAttendance($emp_id, $staffInfo, $prevDate, $ioRecordDate, $fileModifiedDT, $leaveDates, $logTimes, $empInfos, $holidays, $incidents, $officeSchedules, $weekendsByNotScheduled);
	    }	    
	}
	
	
	public function checkSingleDateMissingAttendance($emp_id, $staffInfo, $sdate, $edate, $fileModifiedDT, $leaveDates, $logTimes, $empInfos, $holidays, $incidents, $officeSchedules, $weekendsByNotScheduled){

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
                    $sender['name'] = 'EMS';
                    $sender['email'] = 'info@genuitysystems.com';

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
	    }
	    /*  end of schedule loop */
	}
	
	public function getLogtimeDetailsForMissing($empInfo, &$logTimes, $stime_schedule, $etime_schedule, $office_day, $weekends, $leaveDates, $holidays, $incidents, $fileModifiedDT){
	
	    $ret= array();
	    $sdate_schedule = substr($stime_schedule, 0,10);
	    $edate_schedule = substr($etime_schedule, 0,10);
	
	    $duration_schedule_sec = strtotime($etime_schedule) - strtotime($stime_schedule);
	    $duration_schedule =  $this->secondsToHours($duration_schedule_sec );
	
	    $fileModifiedDTInSec = strtotime($fileModifiedDT);
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
                        if($fileModifiedDTInSec < $nextMedian){

                            $ret['logOut'] = true;
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
                        if($fileModifiedDTInSec < ( strtotime($stime_schedule) + 6*3600 )){

                            $ret['logIn'] = true;
                        }else{
                            $ret['logIn'] = false;
                        }

                        if($fileModifiedDTInSec < $nextMedian){

                            $ret['logOut'] = true;
                        }else{
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
	                if( $fileModifiedDTInSec < ( strtotime($stime_schedule) + 6*3600 )){
	
	                    $ret['logIn'] = true;
	                }else{
	                    $ret['logIn'] = false;
	                }
	
	            } else{
	                $ret['logIn'] = true;
	            }
	
	            if(empty($last)){
	
	                /* data record txt is uploaded before max logtime( time + 6h) */
                    if($fileModifiedDTInSec < $nextMedian){

                        $ret['logOut'] = true;
                    }else{
                        $ret['logOut'] = false;
                    }
                    	
	            } else{
	                $ret['logOut'] = true;
	            }
	        }
	        	        
	    /* end atlest one log */
	    } else{
	        
	        /*   ************** Absent/ No log ************   */
	
	        /* data record txt is uploaded before max logtime( time + 6h) */

            if($fileModifiedDTInSec < ( strtotime($stime_schedule) + 6*3600 )){

                $ret['logIn'] = true;
            }else{
                $ret['logIn'] = false;
            }

            if( $fileModifiedDTInSec < $nextMedian){

                $ret['logOut'] = true;
            }else{
                $ret['logOut'] = false;
            }

	    }
	
	    return $ret;
	}

	function getManagementAry(){
	    
	    $this->db->select('emp_id');
	    $this->db->from('settings');
	    $this->db->where('type', 'B');
	    $query = $this->db->get();
	    
	    $result = $query->result();
	    $ret = array();
	    foreach ($result as $obj){
	        
	        $ret[] = $obj->emp_id;
	    }
	    
	    return $ret;
	}	
	
	function toSort(&$officeSchedule, $str) {
	
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
	
	function isBetween($from, $till, $input) {
	
	    $f = DateTime::createFromFormat('!H:i', $from);
	    $t = DateTime::createFromFormat('!H:i', $till);
	    $i = DateTime::createFromFormat('!H:i', $input);
	
	
	    if ($f > $t) $t->modify('+1 day');
	    return ($f <= $i && $i <= $t) || ($f <= $i->modify('+1 day') && $i <= $t);
	}
	
}	