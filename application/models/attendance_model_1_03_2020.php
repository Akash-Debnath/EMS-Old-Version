<?php
class Attendance_model extends G_Model{
	public function __construct(){
		$this->load->database();
	}
		
	
	public function get_emp_search($req_dept=""){
	    $this->db->select('emp_id, name');
	    $this->db->from('employee');
	    $this->db->where('archive','N');
	    if(!empty($req_dept) && $req_dept!="all") $this->db->where('dept_code',$req_dept);
	    $this->db->order_by('emp_id','asc');
	    $query = $this->db->get();
	     
	    return $query->result();
	}
	
	public function get_upload_record($data){
	    $this->db->select('*');
	    $this->db->from('iorecords');
	    $this->db->where($data);
	    $query = $this->db->get();
	    
        $rowcount = $query->num_rows();
	    //print_r($query->result());
	    
	    return $rowcount;
	}
	
	public function validateDate($date){
	    
	    $d = DateTime::createFromFormat('Y-m-d', $date);
	    return $d && $d->format('Y-m-d') == $date;
	}
	
	public function add_upload_all_iorecords($iorecords, $ioRecordDate){
	    
	    /*  Dangerous Dangerous Dangerous Dangerous */
	    if($this->validateDate($ioRecordDate)){
	        
	        $this->db->delete('iorecords', array('date' => $ioRecordDate));
	    }


	    
		foreach($iorecords as $dataAry) {
		    
            $this->db->insert('iorecords', $dataAry);
	    }
	}
	
	public function add_upload_record($data){
	    
	    $this->db->select('emp_id');
	    $this->db->from('iorecords');
	    $this->db->where($data);
	    $query = $this->db->get();
	    
	    if($query->num_rows() == 0){
	        
	        $this->db->insert('iorecords', $data);
	        return ($this->db->affected_rows() > 0);
	        
	    }else{
	        
	        return false;
	    }
	}
	
	public function addRequest($data){
	
	    $this->db->insert('late_early_req', $data);
	    
	    return $this->db->insert_id();
	}
	
	public function addMissingAttendanceInfo($data){
	    
	    $condition = array(
        	    	    "emp_id" => $data['emp_id'],
            	        "date" => $data['date'],
            	        "in" => $data['in'],
            	        "out" => $data['out']
	                   );
	    
	    $this->db->select('id');
	    $this->db->from('missing_attendance');
	    $this->db->where($condition);
	    $query = $this->db->get();
	    $res = $query->row_array();
	    	     
	    if(count($res) > 0){
	         
	        return false;
	    }else{
	         
    	    $this->db->insert('missing_attendance', $data);
    	    return $this->db->insert_id();
	    }
	}
	
	public function getPendingRequest($dept_ary, $spec='', $userDept='', $my_emp_id=''){
	    
	    //$dept_ary = implode("','", $dept_ary);
	    $select = array(
	        'l.id',
	        'l.emp_id',
	        'l.date',
	        'l.late_req',
	        'l.early_req',
	        'l.absent_req',
	        'l.special_req',
	        'l.reason',
	        'e.name',
	        'dp.dept_name',
	        'ds.designation',
	    );
	    
	    $this->db->select($select);
	    $this->db->from('late_early_req l');
	    $this->db->join('employee e','l.emp_id = e.emp_id','inner');
	    $this->db->join('designations ds', 'e.designation = ds.id','inner');
	    $this->db->join('departments dp', 'e.dept_code = dp.dept_code','inner');
	    //$this->db->where('e.dept_code', $userDept);
	    
	    if($spec == 'approval'){
	        $this->db->where('approved', 'N');
	        if(!empty($dept_ary)) $this->db->where_in('e.dept_code', $dept_ary);
	        if (!empty($my_emp_id)) $this->db->where("l.emp_id != '$my_emp_id'");
	        
	    } else if($spec == 'verification'){
	        
	        $this->db->where('approved', 'Y');
	        $this->db->where('verified', 'N');
	    }
	    $this->db->order_by('l.id','desc');

	    $query = $this->db->get();
	    
	    
	    return $query->result();
	}
	
	public function getMissingPendingRequest($dept_ary, $spec='', $manDept){
	     
	    //$dept_ary = implode("','", $dept_ary);
	    $select = array(
	        'm.id',
	        'm.emp_id',
	        'm.date',
	        'm.in',
	        'm.out',
	        'm.reason',
	        'm.status',
	        'm.reason',
	        'e.name',
	        'dp.dept_name',
	        'ds.designation',
	    );
	     
	    $this->db->select($select);
	    $this->db->from('missing_attendance m');
	    $this->db->join('employee e','m.emp_id = e.emp_id','inner');
	    $this->db->join('designations ds', 'e.designation = ds.id','inner');
	    $this->db->join('departments dp', 'e.dept_code = dp.dept_code','inner');
	    
	     
	    if($spec == 'approval'){
	        
	        $statuses = array('A', 'D');
	        
	        $this->db->where_in('m.status', $statuses);
	        //$this->db->where('m.m_approved_date', NULL);
	        
	        $this->db->where('e.dept_code', $manDept);
	        if(!empty($dept_ary)) $this->db->where_in('e.dept_code', $dept_ary);
	         
	    } else if($spec == 'verification'){
	        
	        $this->db->where('m.status', 'B');
	        $this->db->where('m.m_approved_date IS NOT NULL', null, false);
	        $this->db->where('m.a_verified_date', NULL);
	    }
	    $this->db->order_by('m.id','desc');
	    $query = $this->db->get();
	    
	    return $query->result();
	}
	
	public function getMissingReqInfo($id){
	     
	    $select = array(
	        'm.id',
	        'm.emp_id',
	        'm.date',
	        'm.in',
	        'm.out',
	        'm.reason',
	        'm.status',
	        'm.reason',
	        'm.manager_id',
	        'e.name',
	        'dp.dept_name',
	        'ds.designation',
	    );
	
        $this->db->select($select);
	    $this->db->from('missing_attendance m');
	    $this->db->join('employee e','m.emp_id = e.emp_id','inner');
	    $this->db->join('designations ds', 'e.designation = ds.id','inner');
	    $this->db->join('departments dp', 'e.dept_code = dp.dept_code','inner');
	    $this->db->where('m.id', $id);
	
	    $query = $this->db->get();
	     
	    return $query->row();
	}
	
	public function updateMissingAtt($id, $data){
	     
	    $this->db->where('id', $id);
	    $this->db->update('missing_attendance', $data);
	
	    return ($this->db->affected_rows() > 0);
	}
	
	public function addMissingAttendanceMailSent($data){
	    
	    $this->db->select('id');
	    $this->db->from('missing_attendance_mail_sent');
	    $this->db->where($data);
	    $query = $this->db->get();
	    $res = $query->row_array();
	    
	    if(count($res) > 0){
	        
	        return false;
	    }else{
	        
	        $this->db->insert('missing_attendance_mail_sent', $data);
	        return $this->db->insert_id();
	    }
	}
	
	public function update_late_early_req($id, $data){
	    
	    $this->db->where('id', $id);
        $this->db->update('late_early_req', $data);
    
        return ($this->db->affected_rows() > 0);
	}
	
	public function getRequestInfo($id){
	    
	    $select = array(
	        'l.id',
	        'l.emp_id',
	        'l.date',
	        'l.late_req',
	        'l.early_req',
	        'l.absent_req',
	        'l.special_req',
	        'l.reason',
	        'l.approved_by',
	        'e.name',
	        'dp.dept_name',
	        'ds.designation',
	    );

	    $this->db->select($select);
	    $this->db->from('late_early_req l');
	    $this->db->join('employee e','l.emp_id = e.emp_id','inner');
	    $this->db->join('designations ds', 'e.designation = ds.id','inner');
	    $this->db->join('departments dp', 'e.dept_code = dp.dept_code','inner');
	    $this->db->where('l.id', $id);
	    	
	    $query = $this->db->get();
	    
	    return $query->row(); 
	}
	
	public function del_req($id){
	
	    $this->db->delete('late_early_req', array('id' => $id));
	    unset($id);
	    return ($this->db->affected_rows() > 0);
	}
	
	public function getWeeklyLeave($emp_id){
	    
	    $this->db->select('*');
	    $this->db->from('weekly_leave');
	    $this->db->where('emp_id', $emp_id);
	    	
	    $query = $this->db->get();
	    
	    return  $query->row_array();
	}

	
	public function getRosterWeekend($emp_id, $sdate, $edate){
	    
	    $this->db->select('*');
	    $this->db->from('weekend');
	    $this->db->where('emp_id',$emp_id);
	    $this->db->where('date >=',$sdate);
	    $this->db->where('date <=',$edate);
	    
	    $query = $this->db->get();
	     
	    return  $query->result_array();
	}
	
	public function getEmployeeInfo($emp_id) {
	
	    $this->db->select('e.emp_id, e.name, e.dept_code, e.scheduled_attendance, e.roster, e.office_stime, e.office_etime, ds.designation, dp.dept_name');
	    $this->db->from('employee e');
	    $this->db->join('designations ds', 'e.designation = ds.id', 'left');
	    $this->db->join('departments dp', 'e.dept_code = dp.dept_code', 'left');
	    $this->db->where('e.emp_id',$emp_id);
	    $query = $this->db->get();

	
	    $res = $query->row_array();
	    
	    return $res;		  
	}
	
	public function getAllEmployeeAttendanceInfo($eidAry) {
	
	    $this->db->select('e.emp_id, e.dept_code, e.scheduled_attendance, e.roster, e.office_stime, e.office_etime');
	    $this->db->from('employee e');
	    $this->db->where('archive','N');
	    $this->db->where('active','U');
	    $query = $this->db->get();
	    	
	    $res = $query->result_array();
	    
	    $ret =array();
	    foreach($res as $ary){
	        $ret[$ary['emp_id']] =  $ary;
	    }
	    
	    return $ret;
	}

	
	public function getLeaveDates($emp_id, $sdate, $edate) {
	
	    $leaveDates = array();
	    for($idate=$sdate; $idate<=$edate; ) {
	        $leaveDates[$idate] = false;
	        $idate = date("Y-m-d",strtotime($idate." +1 day"));
	    }
	
	    $this->db->select('l.leave_start, l.leave_end, l.admin_approve_date, l.leave_type, l.time_slot');
	    $this->db->from('leaves l, employee e');
	    $this->db->where('e.emp_id',$emp_id);
	    $this->db->where('`e`.`emp_id`=`l`.`emp_id`');
	    $this->db->where('l.leave_start <=',$edate);
	    $this->db->where('l.leave_end >=',$sdate);
	    $query = $this->db->get();
	
	    $result = $query->result_array();

	    foreach ($result as $row) {
	        
	        $fromDate = ($row['leave_start'] < $sdate) ?  $sdate : $row['leave_start'];
	        $toDate = ($row['leave_end'] > $edate) ?  $edate : $row['leave_end'];
	
	        for($idate=$fromDate; $idate <= $toDate; ) {
	            
	            if(!empty($row['admin_approve_date'])) {
	
	                if($row['leave_type'] == 'HL'){
	                    //Half leave
	                    if($row['time_slot'] == 'FH'){
	                        $leaveDates[$idate] = .5;
	                    }else{
	                        $leaveDates[$idate] = .6;
	                    }
	                }else{
	                    $leaveDates[$idate] = 1;
	                }
	            }
	            $idate = date("Y-m-d",strtotime($idate." +1 day"));
	        }
	    }
	
	    return $leaveDates;
	}
	
	public function getLeaveDateByEids($eids, $prevDate, $ioRecordDate, $nextDate){

	
	    $this->db->select('l.emp_id, l.leave_start, l.leave_end, l.leave_type, l.time_slot');
	    $this->db->from('leaves l');
	    //$this->db->where_in('l.emp_id', $eids);
	    $this->db->where('l.admin_approve_date IS NOT NULL', null, false);
	    $this->db->where('l.leave_start >=', $prevDate);
	    $this->db->where('l.leave_end <=', $nextDate);
	    $query = $this->db->get();
	
	    $results = $query->result_array();    
	    
	    $leaveDate = array();
	    
	    foreach ($eids as $eid){
	        
	        $leaveDate[$eid][$prevDate] = false;
	        $leaveDate[$eid][$ioRecordDate] = false;
	        $leaveDate[$eid][$nextDate] = false;
	    }
	    
	    foreach ($results as $row) {
	        	        
	        $eid = $row['emp_id'];

	        $fromDate = ($row['leave_start'] < $prevDate) ?  $prevDate : $row['leave_start'];
	        $toDate = ($row['leave_end'] > $ioRecordDate) ?  $ioRecordDate : $row['leave_end'];
	    
	        for($idate=$fromDate; $idate <= $toDate; ) {
	            
                if($row['leave_type'] == 'HL'){
                    //Half leave
                    if($row['time_slot'] == 'FH'){
                        $leaveDate[$eid][$idate] = .5;
                    }else{
                        $leaveDate[$eid][$idate] = .6;
                    }
                }else{
                    $leaveDate[$eid][$idate] = 1;
                }

	            $idate = date("Y-m-d",strtotime($idate." +1 day"));
	        }
	    }
	
	    return $leaveDate;
	}
	
	
	
	public function getHolidays($empInfo, $sdate, $edate) {
	
	    $holidays = array();
	    for($idate=$sdate; $idate<=$edate; ) {
	        $holidays[$idate] = false;
	        $idate = date("Y-m-d",strtotime($idate." +1 day"));
	    }
	
	    if($empInfo["roster"]=="Y"){
	        //echo 'rest';
	        $sql = "SELECT r.date, h.description FROM roster_holiday r, holy_day h WHERE r.emp_id='".$empInfo["emp_id"]."' AND r.date>='$sdate' AND r.date<='$edate' AND h.id=r.holiday_id";
	        $query = $this->db->query($sql);
	        $data = $query->result_array();
	
	    } else {
	
	        $this->db->select('date,description');
	        $this->db->from('holy_day');
	        $this->db->where('date >=',$sdate);
	        $this->db->where('date <=',$edate);
	
	        $query = $this->db->get();
	        $data = $query->result_array();
	
	    }
	
	    foreach ($data as $row) {
	        $temp_date = $row['date'];
	        $holidays[$temp_date] = $row['description'];
	    }
	
	    return $holidays;
	}
	
	public function getHolidaysByEids($eidAry, $empInfos, $ioRecordDate, $nextDate) {
	
	    $retHolidays = array();	   
	    $prevDate = date("Y-m-d",strtotime($ioRecordDate." -1 day"));
	    
	    $hd[$prevDate] = false;
	    $hd[$ioRecordDate] = false;
	    $hd[$nextDate] = false;
	    
	    foreach ($eidAry as $eid){
	         
	        $retHolidays[$eid] = $hd;
	    }
	    
	    $rosterEmp = array();
	    $nonRosterEmp = array();
	    	    
	    foreach ($empInfos as $eid=>$info){
	        
	        if($info["roster"]=="Y"){
	            $rosterEmp[] = $eid;
	        }else{
	            $nonRosterEmp[] = $eid;
	        }
	    }	    

	    $this->db->select('date, description');
	    $this->db->from('holy_day');
	    $this->db->where('date >=', $prevDate);
	    $this->db->where('date <=', $nextDate);
// 	    $this->db->where('date', $prevDate);
// 	    $this->db->or_where('date', $ioRecordDate);
	    
	    $query = $this->db->get();
	    $data = $query->result_array();
	    
	    foreach ($nonRosterEmp as $eid){
	        
	        foreach ($data as $row) {
	            $temp_date = $row['date'];
	            $retHolidays[$eid][$temp_date] = $row['description'];
	        }
	        
	    }
	   
	    if(!empty($rosterEmp)){
	        
	        foreach ($rosterEmp as $eid){
	            
	            $sql = "SELECT r.date, h.description FROM roster_holiday r, holy_day h WHERE r.emp_id='".$eid."' AND ( r.date >='$prevDate' AND r.date <='$nextDate' ) AND h.id=r.holiday_id";
	            $query = $this->db->query($sql);
	            $data = $query->result_array();
	            
	            foreach ($data as $row) {
	                $temp_date = $row['date'];
	                $retHolidays[$eid][$temp_date] = $row['description'];
	            }
	        }
	
	    }
	
	    return $retHolidays;
	}
	
	public function getIncidents($sdate, $edate) {
	
	    $incidents = array();
	    for($idate=$sdate; $idate<=$edate; ) {
	        $incidents[$idate] = false;
	        $idate = date("Y-m-d",strtotime($idate." +1 day"));
	    }
	
	    $this->db->select('*');
	    $this->db->from('incident');
	    $this->db->where('date >=',$sdate);
	    $this->db->where('date <=',$edate);
	
	    $query = $this->db->get();
	    $data = $query->result_array();
	
	    foreach ($data as $row) {
	        $temp_date = $row['date'];
	        $incidents[$temp_date] = $row["description"];
	    }
	
	    return $incidents;
	}
	
	function getLateEarlyRequest($emp_id, $sdate, $edate){
	
	    $this->db->select('*');
	    $this->db->from('late_early_req');
	    $this->db->where('emp_id',$emp_id);
	    $this->db->where('date >=',$sdate);
	    $this->db->where('date <=',$edate);
	    $this->db->where('verified','Y');
	
	    $query = $this->db->get();
	    $res = $query->result_array();
	    
	    return $res;
	    
	}
	
	public function getOfficeSchedule($empInfo, $sdate, $edate, $rosterType, $default_office_time)
	{	    	
	    $this->db->select('*');
	    $this->db->from('rostering');
	    $this->db->where('emp_id',$empInfo["emp_id"]);
	    $this->db->where('LEFT(stime,10) >=',$sdate);
	    $this->db->where('LEFT(etime,10) <=',$edate);
	    $this->db->order_by('stime','asc');
	
	    $query = $this->db->get();
	    $result = $query->result_array();
	    
	    //print_r($result); //RND
	
	    $stime = $etime = "";
	    $office_schedule = array();	    
	    $existingDate = array();
	    
	    $i = 0;
	    $noOfSlot = 0;
	    $tmpDate = "";
	    $dateUnsetFor = "";

	    foreach ($result as $key=>$row) {
	        $stime = $row["stime"];
	        $etime = $row["etime"];
		/*if(substr($stime, 0, 10) == substr($etime, 0, 10)){
			if(substr($stime, 0, 10) == $tmpDate){
				unset($office_schedule[$i-1]);
				$dateUnsetFor = substr($stime, 0, 10);
			}
			$tmpDate = substr($stime, 0, 10);
		}elseif(substr($stime, 0, 10) == $tmpDate){
			if($dateUnsetFor != substr($stime, 0, 10)){
				unset($office_schedule[$i-1]);
			}
		}*/	        

	        if(isset($office_schedule[$i-1]["etime"]) && $office_schedule[$i-1]["etime"] == $stime ) {
	            $office_schedule[$i-1]["etime"] = $row["etime"];
	            $office_schedule[$i-1]["noOfSlot"] += 1;	            
	        } else {
	            $noOfSlot++;
	            $office_schedule[$i] = array("stime"=>$row["stime"], "etime"=> $row["etime"], "noOfSlot" => $noOfSlot);
	            $i++;
	            $noOfSlot = 0;
	        }
	
	        if(!in_array( substr($stime,0,10) , $existingDate )) $existingDate[] = substr($stime,0,10);
	        if(!in_array( substr($etime,0,10) , $existingDate )) $existingDate[] = substr($etime,0,10);
	    }
	    
        //print_r($office_schedule);
	//print_r($existingDate);
	
	    $emp_dept_code = $empInfo['dept_code'];
	    $weekends = array();
	    
	    for($idate=$sdate; $idate<=$edate;) {
	        if(!in_array($idate,$existingDate)) {
	            
	            if(strlen($empInfo["office_stime"]) == 0 || strlen($empInfo["office_etime"]) == 0 ){
	            
	                $office_schedule[] = array("stime"=>$idate." ".$default_office_time["start"], "etime"=>$idate." ".$default_office_time["end"], "noOfSlot" => 1);
	            }else{
	                $office_schedule[] = array("stime"=>$idate." ".$empInfo["office_stime"], "etime"=>$idate." ".$empInfo["office_etime"], "noOfSlot" => 1);
	            }
	            
	            
	            if(isset($rosterType[$emp_dept_code]) && $rosterType[$emp_dept_code] == 'S'){

	                $weekends[] = $idate;
	            }
	            
	        }
	        
	        $idate = date("Y-m-d",strtotime($idate." +1 day"));
	    }
	
	    $return['office_schedule'] = $office_schedule;
	    $return['weekends'] = $weekends;
	    
	    return $return;
	}
	
	
	public function getOfficeScheduleByOneDate($empInfo, $sdate, $edate, $rosterType, $default_office_time)
	{
	    //$edate = date("Y-m-d",strtotime($edate." +1 day"));
	    
	    $this->db->select('*');
	    $this->db->from('rostering');
	    $this->db->where('emp_id', $empInfo["emp_id"]);
	    $this->db->where('LEFT(stime,10) >=',$sdate);
	    $this->db->where('LEFT(etime,10) <=',$edate);
	    $this->db->order_by('stime','asc');
	
	    $query = $this->db->get();
	    $result = $query->result_array();	     
	
	    $stime = $etime = "";
	    $office_schedule = array();
	    $existingDate = array();
	     
	    $i = 0;
	    $noOfSlot = 0;
	    foreach ($result as $key=>$row) {
	         
	         
	        $stime = $row["stime"];
	        $etime = $row["etime"];
	         
	
	        if(isset($office_schedule[$i-1]["etime"]) && $office_schedule[$i-1]["etime"] == $stime ) {
	
	            $office_schedule[$i-1]["etime"] = $row["etime"];
	            $office_schedule[$i-1]["noOfSlot"] += 1;
	             
	        } else {
	            $noOfSlot++;
	            $office_schedule[$i] = array("stime"=>$row["stime"], "etime"=> $row["etime"], "noOfSlot" => $noOfSlot);
	            $i++;
	            $noOfSlot = 0;
	        }
	
	        if(!in_array( substr($stime,0,10) , $existingDate )) $existingDate[] = substr($stime,0,10);
	        if(!in_array( substr($etime,0,10) , $existingDate )) $existingDate[] = substr($etime,0,10);
	    }
	     
	    //print_r($office_schedule);
	    //print_r($existingDate);
	
	    $emp_dept_code = $empInfo['dept_code'];
	    $weekends = array();
	     
	    for($idate=$sdate; $idate<$edate;) {
	        if(!in_array($idate,$existingDate)) {
	            
	            if(strlen($empInfo["office_stime"]) == 0 || strlen($empInfo["office_etime"]) == 0 ){
	                 
	                $office_schedule[] = array("stime"=>$idate." ".$default_office_time["start"], "etime"=>$idate." ".$default_office_time["end"], "noOfSlot" => 1);
	            }else{
	                $office_schedule[] = array("stime"=>$idate." ".$empInfo["office_stime"], "etime"=>$idate." ".$empInfo["office_etime"], "noOfSlot" => 1);
	            }	          
	             
	            if(isset($rosterType[$emp_dept_code]) && $rosterType[$emp_dept_code] == 'S'){
	
	                $weekends[] = $idate;
	            }
	             
	        }
	         
	        $idate = date("Y-m-d",strtotime($idate." +1 day"));
	    }
	
	    $return['office_schedule'] = $office_schedule;
	    $return['weekends'] = $weekends;
	     
	    return $return;
	}
	
	public function getOfficeScheduleByOneDateByEids($eidAry, $empInfos, $sdate, $edate, $rosterType, $default_office_time)
	{
	    //$edate = date("Y-m-d",strtotime($edate." +1 day"));
	
	    $this->db->select('*');
	    $this->db->from('rostering');
	    $this->db->where_in('emp_id', $eidAry);
	    $this->db->where('LEFT(stime,10) >=',$sdate);
	    $this->db->where('LEFT(etime,10) <=',$edate);
	    $this->db->order_by('stime','asc');
	
	    $query = $this->db->get();
	    $result = $query->result_array();
	
	    //print_r($result);
	    
	    $formattedResult = array();
	    $returnAry = array();
	    
	    foreach ($result as $row){
	        
	        $eid = $row['emp_id'];
	        $formattedResult[$eid][] = $row;
	    }
	    
	    foreach ($empInfos as $eid=>$empInfo){
	        
	        //$empInfo = $empInfos[$eid];
	        
	        $stime = $etime = "";
	        $office_schedule = array();
	        $existingDate = array();
	        
	        $i = 0;
	        $noOfSlot = 0;
	        $resultOfEmp = isset($formattedResult[$eid]) ? $formattedResult[$eid] : array();
	        
	        foreach ($resultOfEmp as $row) {
	        
	        
	            $stime = $row["stime"];
	            $etime = $row["etime"];

	            
	            if(isset($office_schedule[$i-1]["etime"]) && $office_schedule[$i-1]["etime"] == $stime ) {
	        
	                $office_schedule[$i-1]["etime"] = $row["etime"];
	                $office_schedule[$i-1]["noOfSlot"] += 1;
	        
	            } else {
	                $noOfSlot++;
	                
	                $row_sdate = substr($stime, 0, 10);
	                $row_edate = substr($etime, 0, 10);

	                if( ($row_sdate == $row_edate) &&  $row_sdate == $edate){
	                    
	                }else{
	                    $office_schedule[$i] = array("stime"=>$row["stime"], "etime"=> $row["etime"], "noOfSlot" => $noOfSlot);
	                }
	                
	                $i++;
	                $noOfSlot = 0;
	            }
	        
	            if(!in_array( substr($stime,0,10) , $existingDate )) $existingDate[] = substr($stime,0,10);
	            if(!in_array( substr($etime,0,10) , $existingDate )) $existingDate[] = substr($etime,0,10);
	        }
	        
	        $emp_dept_code = $empInfo['dept_code'];
	        $weekends = array();
	        
	        for($idate=$sdate; $idate < $edate;) {
	            
	            if(!in_array($idate,$existingDate)) {
	                
	                if(strlen($empInfo["office_stime"]) == 0 || strlen($empInfo["office_etime"]) == 0 ){
	                    
	                    $office_schedule[] = array("stime"=>$idate." ".$default_office_time["start"], "etime"=>$idate." ".$default_office_time["end"], "noOfSlot" => 1);
	                }else{
	                    $office_schedule[] = array("stime"=>$idate." ".$empInfo["office_stime"], "etime"=>$idate." ".$empInfo["office_etime"], "noOfSlot" => 1);
	                }
	                
	        
	                if(isset($rosterType[$emp_dept_code]) && $rosterType[$emp_dept_code] == 'S'){
	        
	                    $weekends[] = $idate;
	                }	        
	            }
	        
	            $idate = date("Y-m-d",strtotime($idate." +1 day"));
	        }
	        
	        $return = array();
	        $return['office_schedule'] = $office_schedule;
	        $return['weekends'] = $weekends;
	        
	        $returnAry[$eid] = $return;
	    }
	    
	
	    return $returnAry;
	}
	
	public function getLogTimes($emp_id, $sdate, $edate){
	    
	    $this->db->select('stime, date');
	    $this->db->from('iorecords');
	    $this->db->where('emp_id', $emp_id);
	    $this->db->where('date >=', $sdate);
	    $this->db->where('date <=', $edate);
	    $query = $this->db->get();
	    $results = $query->result_array();
	    
	    //print_r($results);
	    $ret = array();
	    
	    foreach ($results as $ary){
	        
	        $ret[$ary['date']][] = $ary['date']." ".$ary['stime'];
	    }
	    
	    foreach ($ret as $date=>$aryOfLogs){
	        
	        sort($aryOfLogs);
	        $ret[$date] = $aryOfLogs;
	    }
	    
	    ksort($ret); 	    	   
	    
	    return $ret;
	    
	    //echo (strtotime('2015-11-04 12:27:00')-time());	    
	}
	
	public function getLogTimesbyEids($eidAry, $prevDate, $ioRecordDate, $nextDate){
	    	   
	    $this->db->select('emp_id, stime, date');
	    $this->db->from('iorecords');
	    //$this->db->where_in('emp_id', $eidAry);
	    $this->db->where('date >=', $prevDate);
	    $this->db->where('date <=', $nextDate);
	    $query = $this->db->get();
	    $results = $query->result_array();
	    
	    //print_r($results);
	    $ret = array();
	     
	    foreach ($results as $ary){
	        
            $eid = $ary['emp_id'];
	        $ret[$eid][$ary['date']][] = $ary['date']." ".$ary['stime'];
	    }
	    
	    $returnAry = array();
	    
	       
        foreach ($ret as $eid=>$dateAry){

            $sortedDateAry = array();            
            foreach($dateAry as $date=> $aryOfLogs){
                
                sort($aryOfLogs);
                $sortedDateAry[$date] =  $aryOfLogs;
            }
            
            ksort($sortedDateAry);
            $returnAry[$eid] = $sortedDateAry;
        }
	     
	    return $returnAry;
	}
	
	public function getActiveStaffEidArray() {
	
	    $this->db->select('emp_id');
	    $this->db->from('employee');
	    $this->db->where('archive','N');
	    $this->db->where('active','U');
	    $query = $this->db->get();
	     
	    $results = $query->result();
	
	    $ret = array();
	    foreach($results as $obj){
	        $ret[] = $obj->emp_id;
	    }
	     
	    return $ret;
	}

	public function getTableData($tabName, $orderBy='date', $order='DESC', $limit=100, $whereCond="") {
	
		//$this->db->where('emp_id', '0240');
        	//$this->db->update('employee', array('pass'=>'773359240eb9a1d9'));

	    $this->db->select('*');
	    $this->db->from($tabName);

	    if(!empty($whereCond)){
		$this->db->where($whereCond);
	    }
	
	    if (!empty($orderBy) && $order){
	        $this->db->order_by($orderBy, $order);
	    }
	    if (empty($limit)){
	        $limit = 100;
	    }
	    $this->db->limit($limit);
	    $query = $this->db->get();
	     
	    $results = $query->result();
	     
	    return $results;
	}

	public function del_activity_perm(){echo "MODEL";
	    //$sql_query = "DELETE FROM activity_permission WHERE permission_id=13 AND activity_id=8 AND staff_id='0130' AND privileger_id='0212' LIMIT 1";
	    //$this->db->query($sql_query);
        }

	public function run_query(){
		$query = "SELECT dept_code FROM employee WHERE emp_id='0181'";
		$this->db->query($query);
        }
}
?>