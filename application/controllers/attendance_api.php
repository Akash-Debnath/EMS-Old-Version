<?php
class Attendance_api extends CI_Controller {

	public $gsl_emp_id_limit = 6000;
	public $managementAry = array();
	public $rosterType = array("CA"=>"S","SY"=>"S");//S=Slot,R=Regular
	public $default_office_time = array('start'=> '09:00:00', 'end' => '18:00:00', );
	public $default_weekend = array("sun"=>"N","mon"=>"N","tue"=>"N","wed"=>"N","thu"=>"N","fri"=>"Y", "sat"=>"Y");
	public $web_url = "http://staff.genuitysystems.com/emsdev/";
	public $web_url_main = "http://staff.genuitysystems.com/";

	public function __construct() {
	    parent::__construct();

		$this->load->model('user_model');
		$this->load->model('attendance_model');
		$this->load->library('mailer');
        
		$this->managementAry = $this->getManagementAry();
	}


	/*public function test($today = ""){
		//echo "<pre>";
		//$sql = "SELECT * FROM leaves WHERE leave_date = '2017-12-31' AND emp_id = '0130'";
		//print_r($this->db->query($sql)->result_array());
		
		//$data = $this->attendance_model->updateAttendanceFromEngine($today);
		//$this->attendanceUpdateCron();
        //var_dump(date("Y-m-28"));die;
        $this->upload_missing_fingerprint(date("Y-m-28"));
		//print_r($data);
		//$sql = "UPDATE employee SET pass = '63c5465d0705eeda' WHERE emp_id = '0279'";
		//$this->db->query($sql);
	}*/
	
	
	public function dataTest(){
		echo "<pre>";

		/*echo "<pre>";
		echo "BD";
		$sql = "DESCRIBE notice";
		print_r($this->db->query($sql)->result_array());*/

		$this->load->model('leave_model');
		$roster_setting_accessers = $this->leave_model->getPermissionPrivileger(ROSTER_SETTING);
		print_r($roster_setting_accessers['0130']);

		$this->db->select('emp_id,name');
		$this->db->from('employee');
		$this->db->where('dept_code','CA');
		$this->db->where('archive','N');
		$this->db->where('roster','Y');
		$result = $this->db->get()->result_array();
		print_r($result);


		$this->db->select('staff_id,privileger_id');
		$this->db->from('activity_permission');
		$this->db->where('privileger_id','0130');
		$result = $this->db->get()->result_array();
		print_r($result);

		$result = $this->db->query("SELECT `emp_id`, `name`, `dept_code` FROM (`employee`) WHERE `archive` = 'N' AND `emp_id` IN ('0132', '0206', '0119', '0185', '0221', '0156', '0128', '0167', '0160', '0130', '0210') ORDER BY `emp_id` asc");
		print_r($result->result_array());
	} 

	public function getTodaysAttendanceLog($tdate = ""){
		if($tdate == "")
			$tdate = date("Y-m-d");
		//$tdate = '2017-09-26';
		echo "<pre>";
		echo $tdate;
		$data_ary = array();
		$this->db->select('emp_id,date,stime');
		$this->db->from('iorecords');
		$this->db->where('date',$tdate);
		$result = $this->db->get()->result_array();

		foreach ($result as $key => $value) {
			$data_ary[$value['emp_id']][] = $value['stime'];
		}

		print_r($data_ary);
	}

	public function getTodaysAttendeeLog($tdate = "",$dept_code = ""){
		if($tdate == "")
			$tdate = date("Y-m-d");
		if($dept_code == "")
			$dept_code = "CA";
		echo "<pre>";
		$this->db->select('*');
		$this->db->from('employee_roster_schedule');
		$this->db->where('ddate',$tdate);
		$this->db->where('dept_code',$dept_code);
		
		$data = $this->db->get()->result_array();
		print_r($data);
	}

	public function atDataTest($tdate = "",$dept_code = ""){
		if($tdate == "")
			$tdate = date("Y-m-d");
		if($dept_code == "")
			$dept_code = "CA";

		echo "<br>";
		echo "<pre>";
		$this->db->select('emp_id,date,stime');
		$this->db->from('iorecords');
		//$ids = array('0160','0268','0250','0132','0270','0269','0128','0257','0261','0185','0262','0130','0272');
		//$this->db->where_in('emp_id',$ids);
		$this->db->where('date',$tdate);
		print_r($this->db->get()->result_array());

		echo "schedule";
		echo "<pre>";
		$this->db->select('*');
		$this->db->from('employee_roster_schedule');
		$this->db->where('ddate',$tdate);
		$this->db->where('dept_code',$dept_code);
		//$this->db->where('is_holiday','W');
		
		$data = $this->db->get()->result_array();
		print_r($data);

	} 

	public function attendanceUpdateCron(){
		if(!$this->input->is_cli_request()){
			die;
		}

		$offset = 6*60*60;
  		$current_time = time()+$offset;
  		$today = date("Y-m-d",$current_time);

  		$ftime_start = strtotime("$today 00:00:00");
		$ftime_end = strtotime("$today 06:00:00");

		if($current_time >= $ftime_start && $current_time <= $ftime_end) {
			$this->attendance_model->setRosterDataIfNotSet($today);
			$this->attendance_model->saveEmployeeDailySchedule($today);
			$this->attendance_model->setAllRosterToWeekend($today);
		} else {
			$data1 = $this->upload_missing_fingerprint($today);
			$data = $this->attendance_model->updateAttendanceFromEngine($today);
			$this->LogWriter($data,$data);
		}

		$ftime_end = strtotime("$today 08:20:00");
		if($current_time >= $ftime_start && $current_time <= $ftime_end) {
			$one_day_back = date("Y-m-d",strtotime($today." -1 day"));
			
			$data = $this->upload_missing_fingerprint($one_day_back);
			$this->attendance_model->updateAttendanceFromEngine($one_day_back);
			$date_str = date("Y-m-d H:i:s",$current_time)." - ".$one_day_back;
			$this->LogWriter($data,$date_str);
		}
	}

	private function LogWriter($data,$date){
		$ndata[$date] = $data;
		$ndata = print_r($ndata,true);
		$file = "./application/logs/log.txt";
		if(!is_writable($file)) { echo "Not Permitted"; return; }
		$file = fopen($file,"a");
		fwrite($file,$ndata.PHP_EOL);
		fclose($file);
	}
	public function uploadPrevFingerprint(){
		die();
		$stdate = $this->input->get('startdate');
		$endate = $this->input->get('enddate');
		$verified = $this->input->get('verified');
		$allLogs = $this->getZktecoAttendanceData($stdate,$endate,$verified);
		$iorecords = []; 
		if($allLogs['allLogs']){
			foreach($allLogs['allLogs'] as $index => $log){
				$dateTime = explode(" ",$log['DateTime']);
				$empId = sprintf('%04d', $log['PIN']);
				$iorecords[$dateTime[0]][] = array("date"=>$dateTime[0],"emp_id"=>$empId,"stime"=>$dateTime[1]);
			}
		}
		
		if(empty($iorecords)){	       
	        return;
	    } else {
	        foreach($iorecords as $date => $iorecords){
				$this->attendance_model->add_upload_all_iorecords($iorecords, $date);
			}
	    }   
	    
	}
	private function upload_missing_fingerprint($date){

	    $year = date('y');
	    $month = date('m');
	    $day = date('d');

	    $this->genZktecoAttendanceData();
	
	    $text_file_name = $month.$day.$year.".txt";
		$filePath = "text_files/".$text_file_name;

	    /* upload part */
	    $lines = explode("\n", file_get_contents($filePath)); 
		$iorecords = array();	
	     
	    foreach($lines as $key=>$line) {
	        if(!$key) continue;
	        $ary = explode(",",$line); 
	        if(count($ary)<9) continue;
	         
	        $emp_id = str_replace('"','',$ary[2]);
	        $date = str_replace('"','',$ary[4]);
	        $time = str_replace('"','',$ary[5]);

	        if($emp_id >= $this->gsl_emp_id_limit) { continue; }

	        $iorecords[] = array("date"=>$date,"emp_id"=>$emp_id,"stime"=>$time);
	    }

	    if(empty($iorecords)){	       
	        return;
	    } else {
	        $iorecord = reset($iorecords);
	        $ioRecordDate = $iorecord['date'];
	    }

	    $counts = $this->attendance_model->add_upload_all_iorecords($iorecords, $ioRecordDate);
	    return $iorecords;
	}
	public function getZktecoAttendanceData($stdate,$enDate){
		$this->load->library('zkteco');
		$conn = [
			['ip' => '192.168.10.240','password' => 3090],
			['ip' => '192.168.10.241','password' => 3090],
			['ip' => '192.168.10.242','password' => 3090],
			['ip' => '192.168.10.243','password' => 3090],
			['ip' => '192.168.10.244','password' => 3090],
			['ip' => '192.168.10.245','password' => 3090]
//			['ip' => '118.179.144.62','password' => 3090]
		];
		$machineErrorIps = [];
		$allUserInfo = [];
		$allLogs = [];
		foreach($conn as $ipval){
			try{ 
				$allUserInfo = $this->zkteco->getMachineInstance($ipval['ip'],$ipval['password'])->get_all_user_info()->to_array();
				$allUserInfo = array_column($allUserInfo['Row'], NULL, 'PIN2');
				break;
			}catch(Exception $e) { 
				
			}
			
		}
		foreach($conn as $val){
			try{
				$m = $this->zkteco->getMachineInstance($val['ip'],$val['password']);
				$logs = $m->get_att_log()->filter_by_date(['start'=>$stdate,'end'=>$enDate])->filter_by_verified(1)->to_array();  
				if(!empty($logs['Row'])){
					if(!isset($logs['Row'][0])){
						$logs['Row']= [$logs['Row']];
					}
					// add machine ip to array
					$logs['Row'] = array_map(function($item) use ($val) {
						$ipSlice = explode(".",$val['ip']);
						$newVal['machineIp'] = end($ipSlice);
						return $item+$newVal;
						
					}, $logs['Row']);
					$allLogs = @array_merge(
						$allLogs, isset($logs['Row']) ? $logs['Row'] : []
					);
				}

			}catch(Exception $e){ 
				$machineErrorIps[] = $val['ip'];
			}
			
			
		}
		if(!empty($machineErrorIps)){
			$this->sendIpErrorEmail($machineErrorIps);
		}
		
		$data = [
			'allUserInfo' => $allUserInfo,
			'allLogs' => $allLogs,
		];
		
		return $data;
	}
	/**
	 * send email to admin when machine will not work/throw exception error
	 * @param $machineErrorIps array
	 */
	private function sendIpErrorEmail($machineErrorIps){
		$subject = "Fingerprint Machine Error";                    
		$sender['name'] = 'EMS';
		$sender['email'] = 'hrd@genuitysystems.com';
		$emailBody = "<div style='text-align:left;margin-bottom: 10px;'>Dear Concern,</div><div style='text-align:left'>Something went wrong on the fingerprint machine. The machine ip's are given below:</div><ul><li style='text-align:left'>".implode("</li><li style='text-align:left'>",$machineErrorIps)."</li></ul><div style='text-align:left'>Please take care of it.</div>";
		$receiver = array();
		$obj = new stdClass();
		$obj->name = "NOC";
		$obj->email = "noc@genuitysystems.com";  
		$receiver[] = $obj; 

		$obj->name = "Mostofa Iqbal Mahmud";
		$obj->email = "iqbal.mahmud@genusys.us"; 
		$receiver[] = $obj; 
		
		if( $this->mailer->sendMail($subject, $emailBody, $receiver, $sender) ){
			return true;
		}else{
			return false;
		}
	}
	public function genZktecoAttendanceData(){ 
		$stdate = date('Y-m-d'); 
		$enDate = date('Y-m-d');
		$allLogs = $this->getZktecoAttendanceData($stdate,$enDate,1);
		if($allLogs['allLogs']){
			$year = date('y');
			$month = date('m');
			$day = date('d');
		
			$textFileName = $month.$day.$year.".txt";
			$filePath = "text_files/".$textFileName;
			$file = fopen($filePath, "w") or die("Unable to open file!");
			$txt = '"No.","Card No","Employee ID","User name","Date","Time","Terminal","IN/OUT","Door"';
			fwrite($file, $txt."\n");
			foreach($allLogs['allLogs'] as $index => $log){
				$dateTime = explode(" ",$log['DateTime']);
				$empId = sprintf('%04d', $log['PIN']);
				$txt = '"'.($index+1).'","'.$allLogs['allUserInfo'][$log['PIN']]['Card'].'","'.$empId.'","'.$allLogs['allUserInfo'][$log['PIN']]['Name'].'","'.$dateTime[0].'","'.$dateTime[1].'","----","01","'.$log['machineIp'].'"';
				fwrite($file, $txt."\n");
			}
			fclose($file);
		}
	}
	public function auto_upload_fingerprint(){ 

	    if(!$this->input->is_cli_request()){
	       die;
	    }
		
	    // check missing and sent mail only at time between 11:00pm to 05:00am
	    $nowTime = date('H:i');
	    $from = "23:00";
	    $till = "05:00";
	    if($this->isBetween($from, $till, $nowTime)){ 
	        $this->missing_attendance();
	    }

	    // rest execution at time between 12:00am to 06:00am
	    $from = "00:00";
	    $till = "06:00";
	    if($this->isBetween($from, $till, $nowTime)){
	        return;
	    }

	    $year = date('y');
	    $month = date('m');
		$day = date('d');

		$this->genZktecoAttendanceData();
	
	    $text_file_name = $month.$day.$year.".txt";
		$filePath = "text_files/".$text_file_name;

	    /* upload part */
	    $lines = explode("\n", file_get_contents($filePath)); 
		$iorecords = array();	 

	    foreach($lines as $key=>$line) {
	        if(!$key) continue;
	        $ary = explode(",",$line); 
	        if(count($ary)<9) continue;
	         
	        $emp_id = str_replace('"','',$ary[2]);
	        $date = str_replace('"','',$ary[4]);
	        $time = str_replace('"','',$ary[5]);

	        if($emp_id >= $this->gsl_emp_id_limit) { continue; }

	        $iorecords[] = array("date"=>$date,"emp_id"=>$emp_id,"stime"=>$time);
		} 
	    if(empty($iorecords)){	       
	        return;
	    } else {
	        $iorecord = reset($iorecords);
	        $ioRecordDate = $iorecord['date'];
	    }   
	    
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


	////////////RND START
	function insertDataFromManualFile(){
        //$iorecords = array();
        foreach (glob("text_files/*.txt") as $file) {
            /* upload part */
            $lines = file($file);
            var_dump($file);
            //$lines = explode(PHP_EOL, $file);
            $iorecords = array();
            foreach ($lines as $key => $line) {
                if (!$key) continue;
                $ary = explode(",", $line);
                if (count($ary) < 9) continue;
                $emp_id = str_replace('"', '', $ary[2]);
                $date = str_replace('"', '', $ary[4]);
                $date = explode("/", $date);
                $date = $date[2] . "-" . $date[0] . "-" . $date[1];
                $time = str_replace('"', '', $ary[5]);
                if ($emp_id >= $this->gsl_emp_id_limit) {
                    continue;
                }

                //$done = array('0227', '0021', '0035', '0146', '0022', '0023', '0041', '0234');
                /*if (in_array($emp_id, $done)) {
                }*/
                $iorecords[] = array("date" => $date, "emp_id" => $emp_id, "stime" => $time);
            }
            if (empty($iorecords)) {
                //return;
                continue;
            } else {
                $iorecord = reset($iorecords);
                $ioRecordDate = $iorecord['date'];
                $this->attendance_model->add_upload_all_iorecords($iorecords, $ioRecordDate);
            }

            //$this->prepare_emp_roster_schedule($iorecords);
            //$this->create_iorecord_query($iorecords);
            //die;
            //$this->LogWriter($data,$data);
        }
        //var_dump($iorecords);
        //$this->create_iorecord_query($iorecords);
    }

    /*function create_iorecord_query($iorecords){
        $sql = "INSERT INTO `iorecords` (`date`, `emp_id`, `stime`) VALUES ";
        if (!empty($iorecords)) {
            foreach ($iorecords as $key){
                $sql .= "('".$key['date']. "', '". $key['emp_id']. "', '". $key['stime'] ."'),";
            }
        }
        echo rtrim($sql,",");
    }*/

    /*function prepare_emp_roster_schedule($iorecords){
        $temp_arra = [];
        $emp_id_array = [];
        if (!empty($iorecords)){
            foreach ($iorecords as $key ){
                if (in_array($key['emp_id'],$temp_arra)){
                    $emp_id_array[] = $key['emp_id'];
                }
                $temp_arra[] = $key['emp_id'];
            }
        }
        $final_arr = array();
        if (!empty($emp_id_array)){
            foreach ($emp_id_array as $emp_id){
                $arr = array();
                $date = '';
                foreach ($iorecords as $key ){
                    if ($emp_id == $key['emp_id']) {
                        $arr[] = $key['stime'];
                        $date = $key['date'];
                    }
                }
                sort($arr);
                $final_arr [] = array("emp_id"=>$emp_id, "ddate"=>$date, "entry_time"=> $arr[0], "out_time"=>end($arr));
            }
        }
        //$query = $this->create_sql($final_arr);
        //$this->create_sql($final_arr);
    }*/

    /*function create_sql($arr){
        $emp_detail = $this->emp_schedule();
        $sql = "insert into employee_roster_schedule ( emp_id, ddate, start_time, end_time, entry_time, out_time, dept_code ) VALUES ";
        $data = array();
        $omited = array();
        if (!empty($arr)){
            foreach ($arr as $item) {
                if (array_key_exists($item['emp_id'], $emp_detail)){
                    //$data[] = $item['emp_id'];
                    $sql .= " ( '".$item['emp_id']."', '". $item['ddate']."', '". $item['ddate']." ".$emp_detail[$item['emp_id']]['start_time']."', '".   $item['ddate']." ".$emp_detail[$item['emp_id']]['end_time']."', '". $item['ddate']." ".$item['entry_time']."', '". $item['ddate']." ".$item['out_time']."', '". $emp_detail[$item['emp_id']]['dept'] ."' ),";
                } else {
                    //$omited[] = $item['emp_id'];
                }
            }
            echo rtrim($sql,",");

        }
    }*/

    /*function emp_schedule(){
        $schedule['0021'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=>"HR");
        $schedule['0022'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=>"AO");
        $schedule['0023'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=>"AO");
        $schedule['0035'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "HR");
        $schedule['0041'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=>"AO");
        $schedule['0088'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "GP");
        $schedule['0106'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "TR");
        $schedule['0113'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "CO");
        $schedule['0130'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "CA");
        $schedule['0146'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "HR");
        $schedule['0152'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "AM");
        $schedule['0208'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "AM");
        $schedule['0211'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept' => "SO");
        $schedule['0219'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "GP");
        $schedule['0223'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "CO");
        $schedule['0226'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept' => "SO");
        $schedule['0227'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept' => "SO");
        $schedule['0228'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "GE");
        $schedule['0234'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=>"AO");
        $schedule['0266'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "TR");
        $schedule['0276'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept'=> "TR");
        $schedule['0278'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept' => "SO");
        $schedule['0281'] = array('start_time' => '09:30', 'end_time' => '16:30', 'dept' => "SO");
        return $schedule;
    }*/
	
}	