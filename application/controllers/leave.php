<?php
class Leave extends G_Controller {

	public $data = array();
	public $myEmpId = '';
	public function __construct() {
	  
		parent::__construct();
		
		$this->isLoggedIn();

		$this->load->helper(array('form', 'url'));
		$this->load->library('session');
		$this->load->model('user_model');
		$this->load->model('leave_model');
		$this->load->library('pagination');
		$this->load->library('mailer');
		$this->data["myInfo"] = $this->session->GetMyBriefInfo();
		
		$this->myEmpId = $this->session->GetLoginId();
		$this->data['isManagement'] = $this->session->IsManagement($this->myEmpId);
		$this->data['isAdmin'] = $this->session->IsAdmin($this->myEmpId);
		$this->data['isManager'] = $this->session->IsManager($this->myEmpId);
		
		$this->data['departments'] = $this->user_model->department();
		//print_r($this->data['departments']);
		$this->data["menu"] = "Leave";
		$this->data["uType"] = $this->session->GetUserType();		
		$this->data["controller"] = $this;
		$this->data["gender"] = $this->data["myInfo"]->gender;
	}
	
	public function show($temp_id="") {
	    
	    $temp_id = isset($_POST['select_staff']) ? $_POST['select_staff'] : $temp_id;
	    $year = isset($_POST['leaveIn']) ? $_POST['leaveIn'] : date('Y');	    
	    
	    if(empty($temp_id)){
	        $temp_id = $this->myEmpId;
	    }
	    
	    $eid = '';
	    $viewFlag = false;
	    
        /* Privileger */
	    $leave_approvers = $this->leave_model->getPermissionPrivileger(LEAVE_APPROVE);
	    $leave_verifiers = $this->leave_model->getPermissionPrivileger(LEAVE_VERIFY);	    
	    $approveStaff =  isset($leave_approvers[$this->myEmpId]) ? $leave_approvers[$this->myEmpId] : array();
	    $verifyStaff =  isset($leave_verifiers[$this->myEmpId]) ? $leave_verifiers[$this->myEmpId] : array();	    
	    $privilegedStaffIds = array_unique(array_merge($approveStaff, $verifyStaff), SORT_REGULAR);

        if($this->session->IsAdmin($this->myEmpId) || $this->session->IsManagement($this->myEmpId)){

            
	        $eid = $temp_id;
	        $viewFlag = true;
	        
	        $this->data['departmentLists'] = $this->data['departments'];
	        $this->data['staff_array'] = $this->user_model->getStaffArray();
	        
	    }else if($this->session->IsManager($this->myEmpId)){
	        
	        $dept_code = "";        	         
            $dept_code = $this->leave_model->getDeptCode($temp_id);
	        $manager = $this->session->getManagersByDeptCode($dept_code);
	        if(in_array($this->myEmpId, $manager)){
	            $eid = $temp_id;
	            $viewFlag = true;	            
	        }
	        
	        $this->data['departmentLists'] = $this->user_model->getManagersDepts($this->myEmpId);
	        $depts = array_keys($this->data['departmentLists']);	        
	        $managerDeptStaffs = $this->user_model->getStaffArray($depts);
	        
	        if(in_array($temp_id, $privilegedStaffIds)){
	            
	            $privilegedStaffs = $this->user_model->getStaffArrayByIds( $privilegedStaffIds );
	            	            
	            $managerDeptStaffIdAry = array();
	            foreach ($managerDeptStaffs['all'] as $obj){
	            
	                $managerDeptStaffIdAry[] = $obj->emp_id;
	            }
	            
	            foreach ($privilegedStaffIds as $id){
	                
	                if(!in_array($id, $managerDeptStaffIdAry)){
	                    
	                    foreach ($privilegedStaffIds as $obj){
	                        if($obj->id == $id){
	                            $managerDeptStaffs['all'][] = $obj;
	                            break;
	                        }
	                    }
	                }
	            }
	        }
	        
	        $this->data['staff_array'] = $managerDeptStaffs;
	        
	    }else if( !empty($privilegedStaffIds)  ) {
	        
	        if(in_array($temp_id, $privilegedStaffIds)){
	            
	            $eid = $temp_id;
	            $viewFlag = true;
	            
	        }else{
	            
	            $eid = $this->myEmpId;
	        }
	        
	        $this->data['departmentLists'] = array();
	        $this->data['staff_array'] = $this->user_model->getStaffArrayByIds( $privilegedStaffIds );
	        
	    }else{
	        
	        $eid = $this->myEmpId;
	        
	        $this->data['departmentLists'] = array();
	        $this->data['staff_array'] = array();
	    }
	    
	    if(empty($eid)){
	        $eid = $this->myEmpId;
	    }
	    $myInfo = $this->data['myInfo'];
	    $this->data["searchId"] = $eid;
	    $this->data['SearchDept'] = (isset($_POST['select_dept']) && !empty($_POST['select_dept'])) ? $_POST['select_dept'] : ( ($this->myEmpId == $eid)? $myInfo->userDeptCode : $this->user_model->getDeptCode($eid) );
	    unset($myInfo);
	    
	    /* Basic Info */
	    $applierGender= $this->user_model->getGenderById($eid);

	    /* Leaves Info */
		$leaveInYears = $this->getYearsArray();		
		$leaveLists = $this->leave_model->getLeaveList($eid, $year);		
		$leave_taken = $this->leave_model->getLeaveStatus($eid, $year);
				
		$this->data["genuity_leaves_array"] = $this->genuity_leaves_array;
		$this->data["genuity_leave_taken"]  = $this->leave_model->getGenuityLeaveStatus($eid, $this->genuity_leaves_array);
		
		
		$leave_type = $this->leaves_array;
		$total_current = $this->leave_default_array;
		//gender correction
		$total_current['WL'] = $total_current['WL'][$applierGender];
		
		
		//carry forwarded Calculation
		$leaveHistory = $this->getLeaveHistory($eid, $total_current, $year);
		$forward_sick_leaves = $leaveHistory['forward_sick_leave'];
		$forwardAnnualAry =  $leaveHistory['forward_annual_leave'];
		$forward_annual_leave = $forwardAnnualAry['forwardAnnualLeave'];
		$totalForwardedAnnualTaken = $forwardAnnualAry['totalForwardedAnnualTaken'];
		$forwardAnnualAvailable = $forward_annual_leave - $totalForwardedAnnualTaken;
		 
		 
		$this->data['forwardAnnualAvailable'] = $forwardAnnualAvailable;
		 
		$total_forward_sick_leave = array_sum($forward_sick_leaves);
		 
		if($total_forward_sick_leave > FORWARDED_SICK_LEAVE_LIMIT){
		     
		    $total_forward_sick_leave = FORWARDED_SICK_LEAVE_LIMIT;
		}
		 
		$this->data['forward_sick_leaves'] = $forward_sick_leaves;
		$this->data['total_forward_sick_leave'] = $total_forward_sick_leave;
		$this->data['forward_annual_leave'] = $forward_annual_leave;
		
		//taken calculation
		$taken = array();
		$available_current = array();
		 
		foreach ($total_current as $key=>$val){
		    $taken[$key] = 0;
		    $available_current[$key] = $val;
		}
		/* Carry Forwarded Annual Leave */
		$taken['CA'] = 0;
		$available_current['CA'] = $forward_annual_leave;
		
		foreach ($leave_taken as $obj){
		     
		    $taken[$obj->leave_type] += $obj->period;
		    $available_current[$obj->leave_type] -= $obj->period;
		}
		$available_current['HL'] -= $taken['AL'];
		$available_current['AL'] -= $taken['HL'];
		

		$this->data["total_current"] = $total_current;
		$this->data["available_current"] = $available_current;
		$this->data["applier_gender"] = $this->user_model->getGenderById($eid);
		
		/* end new  */
		
		$this->data["viewFlag"] = $viewFlag;		
		$this->data["leaveLists"] = $leaveLists;		
		$this->data["leave_type"] = $leave_type;
		$this->data["half_leave_slot"] = $this->half_leave_slot;
		$this->data["taken"] = $taken;
		$this->data["year"] = $year;
		$this->data["leaveInYears"] = $leaveInYears;		
		
		$this->data["title"] = "Leave_List";
		$this->data["sub_title"] = "Leave List";
		$this->view('leave', $this->data);					
	}	
	
	public function request($leaveId="") {
	    
	    $isCancel = isset($_GET['cancel']) ? true : false;
	    $this->data['isCancel'] = $isCancel;

	    
	    //echo $leaveId;
	    $leaveInfo = new stdClass();
	    $applierInfo =  new stdClass();
	    $empId4LeaveStatus = "";
	    if(empty($leaveId)) {
            $leaveInfo = $this->getEmptyLeaveInfo();
            

	        $applierInfo->name = $this->data["myInfo"]->userName;
	        $applierInfo->emp_id = $this->data["myInfo"]->userId;
	        $applierInfo->dept_name = $this->data["myInfo"]->userDepartment;
	        $applierInfo->designation = $this->data["myInfo"]->userDesignation;
	        $applierInfo->gender = strtoupper($this->data["myInfo"]->gender);

	        $empId4LeaveStatus = $this->myEmpId;
	        $this->data["leaveId"]="";
	        
	    }else {
	        
	        $leaveInfo = $this->leave_model->getLeaveInfo($leaveId);
	        
	    	if(empty($leaveInfo)){
	            header("HTTP/1.0 404 Not Found");
	            echo "<h1>404 Not Found</h1>";
	            echo "The page that you have requested could not be found.";
	            exit();
	        }
	        $empId4LeaveStatus = $leaveInfo->emp_id;

	        $this->data["leaveId"]=$leaveId;
	        
	        $applierInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
	        $applierInfo->gender = strtoupper($applierInfo->gender);
	        
	        $this->data['leaveFiles'] = $this->leave_model->getAttFiles($leaveId);
	    }
	     
	    $year ="";    	    
	    if(empty($leaveInfo->leave_end)){
	        
	        $year = date('Y');
	    }else {
	        if($leaveInfo->leave_type == 'CA'){
	            
	            //$time= strtotime($leaveInfo->leave_end . " -1 year");
	            $time=strtotime($leaveInfo->leave_end);
	        }else{
	            
	            $time=strtotime($leaveInfo->leave_end);
	        }

	        $year=date("Y", $time);
	        $this->data["leaveId"] = $leaveId;
	    }	   
	    	     
	    $leave_type = $this->leaves_array;
	    $total_current = $this->leave_default_array;
	    if(!empty($applierInfo->gender)){
	        
	        $total_current['WL'] = $total_current['WL'][$applierInfo->gender];    //gender correction
	    } else{
	        $total_current['WL'] = 0;
	    }    
	    
	    //carry forwarded Calculation
	    $leaveHistory = $this->getLeaveHistory($empId4LeaveStatus, $total_current, $year);
	    $forward_sick_leaves = $leaveHistory['forward_sick_leave'];
	    $forwardAnnualAry =  $leaveHistory['forward_annual_leave'];	    
	    $forward_annual_leave = $forwardAnnualAry['forwardAnnualLeave'];	    
	    $totalForwardedAnnualTaken = $forwardAnnualAry['totalForwardedAnnualTaken'];
	    $forwardAnnualAvailable = $forward_annual_leave - $totalForwardedAnnualTaken;
	    
	    
	    $this->data['forwardAnnualAvailable'] = $forwardAnnualAvailable;
	    
	    $total_forward_sick_leave = array_sum($forward_sick_leaves);
	    
	    if($total_forward_sick_leave > FORWARDED_SICK_LEAVE_LIMIT){
	        
	        $total_forward_sick_leave = FORWARDED_SICK_LEAVE_LIMIT;
	    }
	        
	    $this->data['forward_sick_leaves'] = $forward_sick_leaves;
	    $this->data['total_forward_sick_leave'] = $total_forward_sick_leave;
	    $this->data['forward_annual_leave'] = $forward_annual_leave;

	    //taken calculation
	    $leave_taken = $this->leave_model->getLeaveStatus($empId4LeaveStatus, $year);
	    
	    $taken = array();
	    $available_current = array();
	    
	    foreach ($total_current as $key=>$val){
	        $taken[$key] = 0;
	        $available_current[$key] = $val;
	    }
	    /* Carry Forwarded Annual Leave */
	    $taken['CA'] = 0;
	    $available_current['CA'] = $forward_annual_leave;
	    //print_r( $leave_taken);
	     
	    foreach ($leave_taken as $obj){
	        
	        $taken[$obj->leave_type] += $obj->period;
	        $available_current[$obj->leave_type] -= $obj->period;
	    }
	    $available_current['HL'] -= $taken['AL'];
	    $available_current['AL'] -= $taken['HL'];
	
	    
	    /*  Privileger */
	    $admin = array();
	    $manager = array();
	    $bossFlag = false;
	    if($this->session->IsManager($leaveInfo->emp_id) || $this->session->IsAdmin($leaveInfo->emp_id)){
	        $bossFlag = $this->session->IsManagement($this->myEmpId);
	    }elseif ($this->myEmpId == 'DM' && $leaveInfo->leave_type == 'SP'){//Hard coded for special leave: Only DM can approve special leave.
	        $bossFlag = $this->session->IsManagement($this->myEmpId);
	    }
	    $leave_emp_dept_code = "";
	    if(!empty($leaveInfo->emp_id)) $leave_emp_dept_code = $this->leave_model->getDeptCode($leaveInfo->emp_id);
	    
	    
	    if($this->session->IsManager( $leaveInfo->emp_id) ){
	        $manager = $this->activeManagementAry;	    
	    }else {	         
	        $manager = $this->session->getManagersByDeptCode($leave_emp_dept_code);
	    }
	    
	    
	    $admin = $this->session->GetAdminArray();
	    
	    $leave_approvers = $this->leave_model->getPermissionPrivileger(LEAVE_APPROVE);
	    $leave_verifiers = $this->leave_model->getPermissionPrivileger(LEAVE_VERIFY);
	     
	    $approveAry = array_keys($leave_approvers);
	    $verifierAry = array_keys($leave_verifiers);
	    
	    $manager = array_unique(array_merge($manager, $approveAry), SORT_REGULAR);
	    $admin = array_unique(array_merge($admin, $verifierAry), SORT_REGULAR);
	    /* end of privileger */
	    
	    
	    $this->data["genuity_leaves_array"] = $this->genuity_leaves_array;
	    $this->data["genuity_leave_taken"]  = $this->leave_model->getGenuityLeaveStatus($applierInfo->emp_id, $this->genuity_leaves_array);

	    $this->data["applierInfo"] = $applierInfo;
	    $this->data["manager"] = $manager;
	    $this->data["admin"] = $admin;
	    $this->data["bossFlag"] = $bossFlag;
	    $this->data["half_leave_slot"] = $this->half_leave_slot;
	    $this->data["year"] = $year;
	    $this->data["leaveInfo"] = $leaveInfo;
	    $this->data["leave_type"] = $leave_type;	    
	    $this->data["total_current"] = $total_current;
	    $this->data["taken"] = $taken;
	    $this->data["available_current"] = $available_current;
	    $this->data["sandwichLeave"] = $this->sandwichLeave;
	
	    $this->data["title"] = "Request";
	    $this->data["sub_title"] = "Leave Application";
	    
	    //$this->load->view('leaveform', $this->data);
	    $this->view('leaveform', $this->data);
	}
	
	
	public function add_request() {
	    
	    $data = array();
	    $data['leave_type'] = isset($_POST['leaveType']) ? $_POST['leaveType'] : "";	    	   
	    
	    if($data['leave_type'] == 'HL'){
	        
	        $data['time_slot'] = isset($_POST['timeSlot']) ? $_POST['timeSlot'] : "";
	        $data['leave_start'] = isset($_POST['leaveDate']) ? $_POST['leaveDate'] : "";
	        $data['leave_end'] = isset($_POST['leaveDate']) ? $_POST['leaveDate'] : "";
	        $data['period'] = .5;
	        	        
	    }else{
	        $data['time_slot'] = '';
	        $data['leave_start'] = isset($_POST['leaveStart']) ? $_POST['leaveStart'] : "";
	        $data['leave_end'] = isset($_POST['leaveEnd']) ? $_POST['leaveEnd'] : "";
	        $data['period'] = isset($_POST['leavePeriod']) ? $_POST['leavePeriod'] : "";
	    }
	    	    
	    $data['address_d_l'] = isset($_POST['address']) ? $_POST['address'] : "";
	    $data['speacial_reason'] = isset($_POST['reason']) ? $_POST['reason'] : "";   
	    $data['emp_id'] =$this->data["myInfo"]->userId;
	    $data['leave_date'] = date('Y-m-d');
	    
	    
	    if(empty($data['leave_type']) || 
	       empty($data['leave_start']) ||
	       empty($data['leave_end']) ||
	       empty($data['period'])
	       ){
	        
	        $return['status'] = false;
	        $return['msg'] = "Please fill up all necessary fields.";
	        echo json_encode($return);
	        die;
	    }
	    
	    $count = isset($_FILES['upload']) ? count($_FILES['upload']['name']) : 0;
	    
	    if($count > 0 && ($data['leave_type'] == 'SL' || $data['leave_type'] == 'SLM')){
	        $uploadFlag = false;	               

	        $all_files = array();
	        $file = array();
	        $count = isset($_FILES['upload']) ? count($_FILES['upload']['name']) : 0;
	         
	        for($i=0; $i<$count; $i++){

	            foreach($_FILES['upload'] as $key => $ary) {
	                $file[$key] = $ary[$i];
	            }
	            $all_files[$i] = $file;
	        }
	         
	        $extArray = array("jpg","jpeg","png","gif","txt","pdf","doc","docx");
	         
	        foreach ($all_files as $fileObject)  //fieldname is the form field name
	        {
	            $original_file_name = $fileObject["name"];
	            $ext = pathinfo($original_file_name, PATHINFO_EXTENSION);
	            
	            $lowerExtn = strtolower($ext);
	            
	            if(!in_array( $lowerExtn, $extArray)) {
	                
	                $return['status'] = false;
	                $return['msg'] = "Wrong file format!";
	                echo json_encode($return);
	                die;	                
	            }
	        }
	    }


	    if(date('m') <= ANNUAL_LEAVE_MONTH_LIMIT && ($data['leave_type'] == 'HL' || $data['leave_type'] == 'AL') ){
	        
	        
	        $year = empty($data['leave_end']) ? date('Y') : date("Y", strtotime($data['leave_end']) );
	        $forwardAnnualLeave = $this->getForwardAnnualLeave($this->myEmpId, $this->leave_default_array, $year);
	        
	        $forwardedAL = $forwardAnnualLeave['forwardAnnualLeave'] - $forwardAnnualLeave['totalForwardedAnnualTaken'];
	        

	        if($forwardedAL > 0){
	            
	            if($data['leave_type'] == 'HL' ){
	            
	                $data['leave_type'] = 'CA';
	            }
	             
	            if($data['leave_type'] == 'AL' ){
	                 
	                $period = $data['period'];
	                $leave_end = $data['leave_end'];
	                 
	                if($data['period'] <= $forwardedAL){
	                     
	                    $data['leave_type'] = 'CA';
	                }else{
	                    /* annual leave > carry forwarded AL; so add two leave */
	                    /* 1. CA leave */
	                    	                    
	                    $data['leave_type'] = 'CA';
	                    $data['period'] = $forwardedAL;
	                    
	                    $num = intval(($forwardedAL-1), 10);
	                    $data['leave_end'] = date("Y-m-d", strtotime( $data['leave_start'] . " +$num day"));
	                     
	                    /* Add to databse  */
	                    $flag_id = $this->leave_model->add_request($data);
	                    
	                    if($flag_id){
	                         
	                        /* 2. AL leave */
	                        $data['leave_type'] = 'AL';
	                        $data['period'] = $period - $forwardedAL;
	                        $data['leave_start'] = date("Y-m-d", strtotime($data['leave_end'] . " +1 day"));
	                        $data['leave_end'] = $leave_end;
	                         
	                    }else{
	                         
	                        $return['status'] = false;
	                        $return['msg'] = "Adding of leave request has been failed.";
	                        echo json_encode($return);
	                        die;
	                    }
	                }
	            }
	        }
	    }
	    
	    /* Add to Database  */
	    $flag_id = $this->leave_model->add_request($data);
	    $fFlag =false;
	    $affected = "period=".$data['period'].", leave_type=".$data['leave_type'];
	    $actLogTxt = "Leave Request(employee=".$data['emp_id'].", start=".$data['leave_start'].", end=".$data['leave_end'].") Added";
	    
	    if($count > 0 && ($data['leave_type'] == 'SL' || $data['leave_type'] == 'SLM')){
	        $fData = array();
	        
	        foreach ($all_files as $fileObject)  //fieldname is the form field name
	        {
	            $fData['original_file_name'] = $fileObject["name"];
	            $fData['leave_id'] = $flag_id;	            
	            $insert_id = $this->leave_model->add_leave_file($fData);
	            
	            $ext = pathinfo($original_file_name, PATHINFO_EXTENSION);
	            $file_name = "leave_".$insert_id.".".$ext;
	            move_uploaded_file($fileObject["tmp_name"], "./assets/files/$file_name");
	            
	            $fFlag = $this->leave_model->update_leave_attachment($insert_id, $file_name);
	        }
	    }

	    if($flag_id){
	        $this->addActivityLog('A', $affected, $actLogTxt);
	        //sent mail to manager
	        $receiver = array();
	        $managers = array();
	        
	        $leave_approvers = $this->leave_model->getPermissionPrivileger(LEAVE_APPROVE, $this->myEmpId);
	        $approveAry = array_keys($leave_approvers);

	        if($this->session->IsManager($this->myEmpId) ){
	            
	            $managers = $this->activeManagementAry;
	        }else {	            
	            $managers = $this->session->getManagersByDeptCode($this->data["myInfo"]->userDeptCode);
	        }	  
	              
	        $approveAry = array_unique(array_merge($managers, $approveAry), SORT_REGULAR);

	        
	        if(empty($approveAry)){
	            
	            $return['status'] = true;
	            $return['msg'] = "Leave request has been added but mail isn't sent to manager.";
	            echo json_encode($return);
	            die;	            
	        }
	        
	        //Hard coded for special leave: Only ED can approve special leave.
	        if ($data['leave_type'] == 'SP'){
	            $approveAry = array('DM');
	        }

	        $receiver["to"] = $this->user_model->getMailInfoByIds($approveAry);
	        
            $leave_name = $this->leaves_array[$data['leave_type']];         
            $subject = $leave_name." leave request from ".$this->data["myInfo"]->userName." on EMS.";
            

            $lDate = date('h:i:s A');
            $lDay = date('l');
            
            $message = "has sent a <a href='".$this->web_url."leave/request/".$flag_id."'>leave application</a> to <a href='".$this->web_url."'>EMS</a> on $lDay at $lDate and waiting for your approval.";  
            
            $senderInfo = $this->data["myInfo"];
            
            $leaveInfo = $data;
            $leaveInfo["leave_id"] = $flag_id;
            $leaveInfo["leave_name"] = $this->leaves_array[$data['leave_type']];;
            
            if( $this->mailer->leaveMailToManager($subject, $receiver, $leaveInfo, $senderInfo, $message,  $this->web_url) ){
                $return['msg'] = "Leave request has been added and a mail is sent to manager successfully.";
            }else{
                $return['msg'] = "Leave request has been added but mail isn't sent to manager.";
            }
    
	        //return info
	        $return['status'] = true;
	        $return['id'] = $flag_id;
	    }else {
	        $return['status'] = false;
	        $return['fFlag'] = $fFlag;
	        $return['id'] = $flag_id;
	        $return['msg'] = "Adding of leave request has been failed.";
	    }

	    echo json_encode($return);	    
	    die;
	}
	
	public function update_request($l_id){
	    $leaveObj = $this->leave_model->getLeaveShortInfoById($l_id);
	     
	    if($this->myEmpId == $leaveObj->emp_id){
	        
	        $data = array();
	        $data['leave_type'] = $_POST['leaveType'];
	        if($data['leave_type'] == 'HL'){
	            $data['time_slot'] = $_POST['timeSlot'];
	            $data['leave_start'] = $_POST['leaveDate'];
	            $data['leave_end'] = $_POST['leaveDate'];
	            $data['period'] = .5;
	        }else{
	            $data['time_slot'] = '';
	            $data['leave_start'] = $_POST['leaveStart'];
	            $data['leave_end'] = $_POST['leaveEnd'];
	            $data['period'] = $_POST['leavePeriod'];
	        }
	        $data['address_d_l'] = $_POST['address'];
	        $data['speacial_reason'] = $_POST['reason'];
	        $data['emp_id'] =$this->data["myInfo"]->userId;
	        $data['leave_date'] = date('Y-m-d');
	        
	        //server side extension check
	        if($data['leave_type'] == 'SL' || $data['leave_type'] == 'SLM'){
	            $uploadFlag = false;
	        
	            $all_files = array();
	            $file = array();
	            
	            $count = isset($_FILES['upload']['name']) ? count($_FILES['upload']['name']) : 0;
	        
	            for($i=0; $i<$count; $i++){
	        
	                foreach($_FILES['upload'] as $key => $ary) {
	                    $file[$key] = $ary[$i];
	                }
	                $all_files[$i] = $file;
	            }
	        
	            $extArray = array("jpg","jpeg","png","gif","txt","pdf","doc","docx");
	        
	            foreach ($all_files as $fileObject)  //fieldname is the form field name
	            {
	                $original_file_name = $fileObject["name"];
	                $ext = pathinfo($original_file_name, PATHINFO_EXTENSION);
	                
	                $lowerExtn = strtolower($ext);
	                
	                if(!in_array($lowerExtn, $extArray)) {
    	                $return['status'] = false;
    	                $return['msg'] = "Wrong file format!";
    	                echo json_encode($return);
    	                die;
	                }
	            }
	        }
	        
	        if(date('m') <= ANNUAL_LEAVE_MONTH_LIMIT && ($data['leave_type'] == 'HL' || $data['leave_type'] == 'AL') ){
    	        
	            $year = empty($data['leave_end']) ? date('Y') : date("Y", strtotime($data['leave_end']) );
	        $forwardAnnualLeave = $this->getForwardAnnualLeave($this->myEmpId, $this->leave_default_array, $year);
	        
	        $forwardedAL = $forwardAnnualLeave['forwardAnnualLeave'] - $forwardAnnualLeave['totalForwardedAnnualTaken'];
	            
	            if($forwardedAL > 0){
    
        	        if($data['leave_type'] == 'HL' ){
        
        	            $data['leave_type'] = 'CA';
        	        }
        	        
        	        if($data['leave_type'] == 'AL' ){
    
    	                $period = $data['period'];
    	                $leave_end = $data['leave_end'];
    	                 
    	                if($data['period'] <= $forwardedAL){
    	                     
    	                    $data['leave_type'] = 'CA';
    	                }else{
    	                    /* annual leave > carry forwarded AL; so add two leave */
    	                    /* 1. CA leave */
    	                    $data['leave_type'] = 'CA';
    	                    $data['period'] = $forwardedAL;
    	                    $num = $forwardedAL-1;
    	                    $data['leave_end'] = date("Y-m-d", strtotime( $data['leave_start'] . " +$num day"));
    	                     
    	                    /* Add to databse  */
    	                    $flag_id = $this->leave_model->add_request($data);
    	        
    	                    if($flag_id){
    	                         
    	                        /* 2. AL leave */
    	                        $data['leave_type'] = 'AL';
    	                        $data['period'] = $period - $forwardedAL;
    	                        $data['leave_start'] = date("Y-m-d", strtotime($data['leave_end'] . " +1 day"));
    	                        $data['leave_end'] = $leave_end;
    	                         
    	                    }else{
    	                         
    	                        $return['status'] = false;
    	                        $return['msg'] = "Adding of leave request has been failed.";
    	                        echo json_encode($return);
    	                        die;
    	                    }
    	                }
    	            }
    	        }
    	    }
	         
	        $flag = $this->leave_model->add_confirm($data, $l_id);
	        $fFlag = false;
	        $affected = "period=".$data['period'].", leave_type=".$data['leave_type'];
	        $actLogTxt = "Leave Request(employee=".$data['emp_id'].", start=".$data['leave_start'].", end=".$data['leave_end'].") Updated";
	        
	        //data file upload
	        if($data['leave_type'] == 'SL' || $data['leave_type'] == 'SLM'){
	            $fData = array();
	             
	            foreach ($all_files as $fileObject)  //fieldname is the form field name
	            {
	                $fData['original_file_name'] = $fileObject["name"];
	                $fData['leave_id'] = $l_id;
	                $insert_id = $this->leave_model->add_leave_file($fData);
	                 
	                $ext = pathinfo($original_file_name, PATHINFO_EXTENSION);
	                $file_name = "leave_".$insert_id.".".$ext;
	                move_uploaded_file($fileObject["tmp_name"], "./assets/files/$file_name");
	                 
	                $fFlag = $this->leave_model->update_leave_attachment($insert_id, $file_name);
	            }
	        }
	        
	        if($flag){
	            $this->addActivityLog('U', $affected, $actLogTxt);
	            //return info
	            $return['status'] = true;
	            $return['id'] = $l_id;
	            $return['msg'] = "Leave request has been updated successfully.";
	        }else {
	            $return['status'] = false;
	            $return['id'] = $l_id;
	            $return['fFlag'] = $fFlag;
	            $return['msg'] = "Update of leave request has been failed.";
	        }	        
	        echo json_encode($return);
	        die;	        
	    }else{
	        $this->data["title"] = "ABC";
	        $this->data["sub_title"] = "ABC";
	        $this->data['message'] = "You don't have the priviledge to this page.";
	        $this->load->view('error', $this->data);
	    }
	    	    
	}
	
	public function del_leave($l_id){
	    
	    $leaveObj = $this->leave_model->getLeaveShortInfoById($l_id);	    
	    $this->data["title"] = "ABC";
	    $this->data["sub_title"] = "ABC";
	    	    
	    if($this->myEmpId == $leaveObj->emp_id){
	       $flag = $this->leave_model->del_leave($l_id);
	       if($flag){
	           $affected = "period=".$leaveObj->period.", leave_type=".$leaveObj->leave_type;
	           $actLogTxt = "Leave Request(employee=".$leaveObj->emp_id.", start=".$leaveObj->leave_start.", end=".$leaveObj->leave_end.", id=$l_id) Deleted";
	           $this->addActivityLog('D', $affected, $actLogTxt);

	           redirect(base_url().'leave/show');
	       }else {
	           $this->data['message'] = "deletion of leave has been failed.";
	           $this->load->view('error', $this->data);
	       }
	    }else{
	        $this->data['message'] = "You don't have the priviledge to this page.";
	        $this->load->view('error', $this->data);
	    }	    
	}
	
	public function confirm_m($l_id){
	    
	    $leaveInfo = $this->leave_model->getLeaveInfo($l_id);

	    if($this->session->IsManager($leaveInfo->emp_id)){	        
	        $approver_default = $this->activeManagementAry;	        
	    }else{	        
	        $leave_emp_dept_code = $this->leave_model->getDeptCode($leaveInfo->emp_id);
	        $approver_default = $this->session->getManagersByDeptCode($leave_emp_dept_code);
	    }
	    
	    $leave_approvers = $this->leave_model->getPermissionPrivileger(LEAVE_APPROVE, $leaveInfo->emp_id);
	    $leave_verifiers = $this->leave_model->getPermissionPrivileger(LEAVE_VERIFY, $leaveInfo->emp_id);
	    
	    $approverAry = array_keys($leave_approvers);
	    $verifierAry = array_keys($leave_verifiers);

	    $managerAry = array_unique(array_merge($approver_default, $approverAry), SORT_REGULAR);

	    if(!in_array($this->myEmpId, $managerAry)){
	        //Hard coded for special leave: Only Jewel Bhai can approve special leave.
	        if ($this->myEmpId == 'DM' && $leaveInfo->leave_type == 'SP'){
	            ;//ED has special permission to approve SP Leave
	        }else {
    	        $return['status'] = false;
    	        $return['msg'] = $this->message['no_priv'];
    	        
    	        echo json_encode($return);
    	        return;
	        }
	    }
	    
	    $data['comment1'] = $_POST['comment1'];
	    $data['comment2'] = $_POST['comment2'];
	    $data['comment3'] = $_POST['comment3'];
	    	    
	    $data['manager_id'] = $this->data["myInfo"]->userId;
	    $data['m_approved_date'] = date('Y-m-d');
	    $data['manager_remark'] = isset($_POST['approveText']) ? $_POST['approveText'] : "";
	    
	    $flag = $this->leave_model->add_confirm($data, $l_id);
	    
    	if($flag){
    	    $affected = "period=".$leaveInfo->period.", leave_type=".$leaveInfo->leave_type;
    	    $actLogTxt = "Leave Request(employee=".$leaveInfo->emp_id.", start=".$leaveInfo->leave_start.", end=".$leaveInfo->leave_end.") Approved by ".$this->data["myInfo"]->userId;
    	    $this->addActivityLog('U', $affected, $actLogTxt);
            //sent mail to Admin, then to leave requester
    	    $receiver = array();
    	    $adminAry = $this->session->GetAdminArray();
    	    $adminAry = array_unique(array_merge($adminAry, $verifierAry), SORT_REGULAR);
            $receiver["to"] = $this->user_model->getMailInfoByIds($adminAry);
            
            $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
            $senderInfo = $this->data["myInfo"];                           
            $leaveInfo->leave_type = $this->leaves_array[$leaveInfo->leave_type];
            $subject = $leaveInfo->leave_type." leave reequest from ".$staffInfo->name." on EMS";
           
            if($this->mailer->sendMailByManager($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url)) {
                // return info
                $return['status'] = true;
                $return['msg'] = "Leave request has been approved successfully.";
            }
        } else {
            $return['status'] = false;
            $return['msg'] = "Approval of leave request has been failed.";
        }
        
	    echo json_encode($return);
	    die;	    
	}
	
	public function confirm_a($l_id){
	    
	    $leaveInfo = $this->leave_model->getLeaveInfo($l_id);
	    
	    $leave_verifiers = $this->leave_model->getPermissionPrivileger(LEAVE_VERIFY, $leaveInfo->emp_id);
	    $verifierAry = array_keys($leave_verifiers);
	    
	    $AdminAry = $this->session->GetAdminArray();
	    $AdminAry = array_unique(array_merge($AdminAry, $verifierAry), SORT_REGULAR);
	    
	    if(!in_array($this->myEmpId, $AdminAry)){
	         
	        $return['status'] = false;
	        $return['msg'] = $this->message['no_priv'];
	         
	        echo json_encode($return);
	        return;
	    }
	    

	    $data['admin_remark'] = isset($_POST['verifyText']) ? $_POST['verifyText'] : '';	    
	    $data['admin_id'] = $this->data["myInfo"]->userId;
	    $data['admin_approve_date'] = date('Y-m-d');
	     
	    $flag = $this->leave_model->add_confirm($data, $l_id);
	
	    if($flag){
	        $affected = "period=".$leaveInfo->period.", leave_type=".$leaveInfo->leave_type;
	        $actLogTxt = "Leave Request(employee=".$leaveInfo->emp_id.", start=".$leaveInfo->leave_start.", end=".$leaveInfo->leave_end.") Approved by ".$this->data["myInfo"]->userId;
	        $this->addActivityLog('U', $affected, $actLogTxt);
	        //sent mail to Reqester and bcc to Manager
	        
	        $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	        
	        $receiver = array();	        
	        $obj = new stdClass();
	        $obj->name = $staffInfo->name;
	        $obj->email = $staffInfo->email;
	        $receiver["to"] = array($obj);
	        
	        $managerInfo = $this->user_model->getBriefInfo($leaveInfo->manager_id);
	        $receiver["bcc"] = array($managerInfo);
	         
	        $leaveInfo->leave_type = $this->leaves_array[$leaveInfo->leave_type];
	        $subject = "Leave of ".$staffInfo->name." verified for record";
	
	        if($this->mailer->sendMailByAdmin($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url)) {
	            
	            $return['msg'] = "Leave request has been verified successfully.";
	        }else {
	            $return['msg'] = "Leave request has been verified successfully but mail isn't sent successfully.";
	        }
	        // return info
	        $return['status'] = true;
	    } else {
	        $return['status'] = false;
	        $return['msg'] = "verification of leave request has been failed.";
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	public function refuse_m($l_id){

	    $leaveInfo = $this->leave_model->getLeaveInfo($l_id);
	    
	    if($this->session->IsManager($leaveInfo->emp_id)){
	         
	        $approver_default = $this->activeManagementAry;
	    }else{
	         
	        $leave_emp_dept_code = $this->leave_model->getDeptCode($leaveInfo->emp_id);
	        $approver_default = $this->session->getManagersByDeptCode($leave_emp_dept_code);
	    }

        $leave_approvers = $this->leave_model->getPermissionPrivileger(LEAVE_APPROVE, $leaveInfo->emp_id);
        $approveAry = array_keys($leave_approvers);
	    $managerAry = array_unique(array_merge($approver_default, $approveAry), SORT_REGULAR);
	    
	    if(!in_array($this->myEmpId, $managerAry)){
	         
	        $return['status'] = false;
	        $return['msg'] = $this->message['no_priv'];
	         
	        echo json_encode($return);
	        return;
	    }

	    $excuse = $_POST['excuse'];
	    $flag = $this->leave_model->del_leave($l_id);

	    if($flag){
	        $affected = "period=".$leaveInfo->period.", leave_type=".$leaveInfo->leave_type;
	        $actLogTxt = "Leave Request(employee=".$leaveInfo->emp_id.", start=".$leaveInfo->leave_start.", end=".$leaveInfo->leave_end.") Refused by ".$this->data["myInfo"]->userId;
	        $this->addActivityLog('D', $affected, $actLogTxt);
	        
	        //send mail to Staff
	        $receiver = array();
	        $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
	        $obj = new stdClass();
	        $obj->name = $staffInfo->name;
	        $obj->email = $staffInfo->email;
	        $receiver["to"] = array($obj);
	        $senderInfo = $this->data["myInfo"];
	        $leaveInfo->leave_type = $this->leaves_array[$leaveInfo->leave_type];
	        $subject = $leaveInfo->leave_type." leave request has been refused by manager";
	         
	        if($this->mailer->sendRefuseMailByManager($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url, $excuse)) {
	            
	            $return['msg'] = "Leave request has been deleted and mail is sent successfully.";
	        }else {
	            
	            $return['msg'] = "Leave request has been deleted successfully but mail isn't sent successfully";
	        }
	        // return info
	        $return['status'] = true;
	    } else {
	        
	        $return['status'] = false;
	        $return['msg'] = "Deletion of leave request and mail sent have been failed.";
	    }
	    	
	    echo json_encode($return);
	    die;
	}
	
	public function refuse_a($l_id){
	    
	    $leaveInfo = $this->leave_model->getLeaveInfo($l_id);
	    
	    $leave_verifiers = $this->leave_model->getPermissionPrivileger(LEAVE_VERIFY, $leaveInfo->emp_id);
	    $verifierAry = array_keys($leave_verifiers);

	    $AdminAry = $this->session->GetAdminArray();
	    $AdminAry = array_unique(array_merge($AdminAry, $verifierAry), SORT_REGULAR);
	     
	    if(!in_array($this->myEmpId, $AdminAry)){
	    
	        $return['status'] = false;
	        $return['msg'] = $this->message['no_priv'];
	    
	        echo json_encode($return);
	        return;
	    }

	    $excuse = $_POST['excuse'];
	    
	    $leaveInfo = $this->leave_model->getLeaveInfo($l_id);
	    $flag = $this->leave_model->del_leave($l_id);
	     
	    if($flag){
	        $affected = "period=".$leaveInfo->period.", leave_type=".$leaveInfo->leave_type;
	        $actLogTxt = "Leave Request(employee=".$leaveInfo->emp_id.", start=".$leaveInfo->leave_start.", end=".$leaveInfo->leave_end.") Refused by ".$this->data["myInfo"]->userId;
	        $this->addActivityLog('D', $affected, $actLogTxt);
	        //send mail to Staff and bcc to manager
	        $receiver = array();
	        $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
     
	        $obj = new stdClass();
	        $obj->name = $staffInfo->name;
	        $obj->email = $staffInfo->email;	        
	        $receiver["to"] = array($obj);
	        
	        $managerInfo = $this->user_model->getBriefInfo($leaveInfo->manager_id);	        
	        $receiver["bcc"] = array($managerInfo);
	        
	        $senderInfo = $this->data["myInfo"];
	        $leaveInfo->leave_type = $this->leaves_array[$leaveInfo->leave_type];
	        $subject = $leaveInfo->leave_type." leave reequest has been refused by admin";
	
	        if($this->mailer->sendRefuseMailByAdmin($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url, $excuse)) {
	            $return['msg'] = "Leave request has been deleted and mail is sent successfully.";
	        }else {
	            $return['msg'] = "Leave request has been deleted successfully but mail isn't sent successfully";
	        }
	        // return info
	        $return['status'] = true;
	    } else {
	        $return['status'] = false;
	        $return['msg'] = "Deletion of leave request and mail sent have been failed.";
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	public function cancel_leave($l_id){
	
	    $excuseReason = $_POST['excuseReason'];
	    
	    $data['cancel_req_date'] = date('Y-m-d');	
	    $flag = $this->leave_model->add_confirm($data, $l_id);
	
	    if($flag){
	        //sent mail to Manager & Admin
	        $leaveInfo = $this->leave_model->getLeaveInfo($l_id);
	        $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	        
	        $affected = "period=".$leaveInfo->period.", leave_type=".$leaveInfo->leave_type;
	        $actLogTxt = "Leave Cancel Request(employee=".$leaveInfo->emp_id.", start=".$leaveInfo->leave_start.", end=".$leaveInfo->leave_end.")";
	        $this->addActivityLog('U', $affected, $actLogTxt);
	        
	        $receiver = array();
	        $receiver[] = $this->user_model->getBriefInfo($leaveInfo->manager_id);
	        $receiver[] = $this->user_model->getBriefInfo($leaveInfo->admin_id);

	        $leaveInfo->leave_type = $this->leaves_array[$leaveInfo->leave_type];
	        $subject = "Application for the cancellation of a leave.";
	
	        if($this->mailer->sendCancellationMail($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url, $excuseReason)) {
	            $return['msg'] = "Leave cancellation request has been sent successfully.";
	        }else {
	            $return['msg'] = "Leave cancellation request has been sent successfully but mail isn't sent successfully.";
	        }
	        // return info
	        $return['status'] = true;
	        $return['id'] = $flag;
	    } else {
	        $return['status'] = false;
	        $return['msg'] = "Leave cancellation request has been failed.";
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	public function cancel_approve($l_id){
	    
	    $leaveInfo = $this->leave_model->getLeaveInfo($l_id);
	    $leave_emp_dept_code = $this->leave_model->getDeptCode($leaveInfo->emp_id);
	    
	    if($this->session->IsManager( $leaveInfo->emp_id) ){
	    
	        $approver_deafult = $this->activeManagementAry;
	    }else {
	        $approver_deafult = $this->session->getManagersByDeptCode($leave_emp_dept_code);
	    }	     
	    $admins = $this->session->GetAdminArray();
	     
	    $leave_approvers = $this->leave_model->getPermissionPrivileger(LEAVE_APPROVE);
	    $leave_verifiers = $this->leave_model->getPermissionPrivileger(LEAVE_VERIFY);
	    
	    $approverAry = array_keys($leave_approvers);
	    $verifierAry = array_keys($leave_verifiers);
	     
	    $approverAry = array_unique(array_merge($approver_deafult, $approverAry), SORT_REGULAR);	    
	    $verfierAry = array_unique(array_merge($admins, $verifierAry), SORT_REGULAR);
	    
	    
	    if( !in_array($this->myEmpId, $approverAry) && !in_array($this->myEmpId, $verfierAry) ){
	         
	        $return['status'] = false;
	        $return['msg'] = $this->message['no_priv'];
	         
	        echo json_encode($return);
	        return;
	    }
	    
	    
	    $flag = $this->leave_model->del_leave($l_id);
	
	    if($flag){
	        $affected = "period=".$leaveInfo->period.", leave_type=".$leaveInfo->leave_type;
	        $actLogTxt = "Leave Cancel Request(employee=".$leaveInfo->emp_id.", start=".$leaveInfo->leave_start.", end=".$leaveInfo->leave_end.") Approved by ".$this->data["myInfo"]->userId;
	        $this->addActivityLog('D', $affected, $actLogTxt);
	        //sent mail to Staff
	        $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	        
	        $receiver = array();
	        $obj = new stdClass();
	        $obj->name = $staffInfo->name;
	        $obj->email = $staffInfo->email;
	        $receiver[] = $obj;
	        
	        if($this->data["uType"] == "B"){ 
	            $receiver[] = $this->user_model->getBriefInfo($leaveInfo->manager_id);
	        }
	        if(($this->data["uType"] == "M") || ($this->data["uType"] == "B")){
	            
	           $receiver[] = $this->user_model->getBriefInfo($leaveInfo->admin_id);
	        }else if($this->data["uType"] == "A"){
	            
	            $receiver[] = $this->user_model->getBriefInfo($leaveInfo->manager_id);
	        }

	        $leaveInfo->leave_type = $this->leaves_array[$leaveInfo->leave_type];
	        $subject = "Cancellation of the leave has been approved.";
	
	        if($this->mailer->sendCancellationApproveMail($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url)) {
	            $return['msg'] = "Leave cancellation request has been approved successfully.";
	        }else {
	            $return['msg'] = "Leave cancellation request has been approved but mail isn't sent successfully.";
	        }
	        // return info
	        $return['status'] = true;
	    } else {
	        $return['status'] = false;
	        $return['msg'] = "Approval of leave cancellation request has been failed.";
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	public function cancel_refuse($l_id){
	    
		$leaveInfo = $this->leave_model->getLeaveInfo($l_id);
	    $leave_emp_dept_code = $this->leave_model->getDeptCode($leaveInfo->emp_id);
	    
	    if($this->session->IsManager( $leaveInfo->emp_id) ){
	    
	        $approver_deafult = $this->activeManagementAry;
	    }else {
	        $approver_deafult = $this->session->getManagersByDeptCode($leave_emp_dept_code);
	    }	     
	    $admins = $this->session->GetAdminArray();
	     
	    $leave_approvers = $this->leave_model->getPermissionPrivileger(LEAVE_APPROVE);
	    $leave_verifiers = $this->leave_model->getPermissionPrivileger(LEAVE_VERIFY);
	    
	    $approverAry = array_keys($leave_approvers);
	    $verifierAry = array_keys($leave_verifiers);
	     
	    $approverAry = array_unique(array_merge($approver_deafult, $approverAry), SORT_REGULAR);	    
	    $verfierAry = array_unique(array_merge($admins, $verifierAry), SORT_REGULAR);

	    
	    if( !in_array($this->myEmpId, $approverAry) && !in_array($this->myEmpId, $verfierAry) ){
	         
	        $return['status'] = false;
	        $return['msg'] = $this->message['no_priv'];
	         
	        echo json_encode($return);
	        return;
	    }
	
	    $excuse = $_POST['excuse'];
	    $data['cancel_req_date'] = null;	    
	    $flag = $this->leave_model->add_confirm($data, $l_id);
	    
	    if($flag){
	        $affected = "period=".$leaveInfo->period.", leave_type=".$leaveInfo->leave_type;
	        $actLogTxt = "Leave Cancel Request(employee=".$leaveInfo->emp_id.", start=".$leaveInfo->leave_start.", end=".$leaveInfo->leave_end.") Refused by ".$this->myEmpId;
	        $this->addActivityLog('D', $affected, $actLogTxt);
	        //sent mail to Staff
	        $staffInfo = $this->user_model->getBriefInfo($leaveInfo->emp_id);
	        $senderInfo = $this->data["myInfo"];
	         
	        $receiver = array();
	        $obj = new stdClass();
	        $obj->name = $staffInfo->name;
	        $obj->email = $staffInfo->email;
	        $receiver[] = $obj;
	
	        $leaveInfo->leave_type = $this->leaves_array[$leaveInfo->leave_type];
	        $subject = "Cancellation of the leave has been Refused.";
	
	        if($this->mailer->sendCancellationRefuseMail($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $this->web_url, $excuse)) {
	            $return['msg'] = "Leave cancellation request has been refused successfully.";
	        }else {
	            $return['msg'] = "Leave cancellation request has been refused but mail isn't sent successfully.";
	        }
	        // return info
	        $return['status'] = true;
	    } else {
	        $return['status'] = false;
	        $return['msg'] = "Refusal of leave cancellation request has been failed.";
	    }
	
	    echo json_encode($return);
	    die;
	}
	
	public function get_holiday(){ 
	    
	    $data['start'] = $_POST['start'];
	    $data['end'] = $_POST['end'];	    
	    $data['emp_id'] = $this->myEmpId;
	    $data['default_weekend'] = $this->default_weekend;
	    
	    $holiday = $this->leave_model->get_holiday($data);
	    
	    if($holiday>=0){
	        $return['status'] = true;
	        $return['holiday'] = $holiday;	        
	    }else{
	        $return['status'] = false;
	        $return['msg'] = "something went wrong!";
	    }
	    
	    echo json_encode($return);
	    die;
	}	
	
	public function getEmptyLeaveInfo(){
	    $leaveInfo = new stdClass();
	    $leaveInfo->id = "";
	    $leaveInfo->leave_type = "";
	    $leaveInfo->emp_id = "";
	    $leaveInfo->time_slot = "";
	    $leaveInfo->leave_start = "";
	    $leaveInfo->leave_end = "";
	    $leaveInfo->leave_date = date('Y-m-d');
	    $leaveInfo->period = "";
	    $leaveInfo->address_d_l = "";
	    $leaveInfo->speacial_reason = "";
	    $leaveInfo->comment1 = "";
	    $leaveInfo->comment2 = "";
	    $leaveInfo->comment3 = "";
	    $leaveInfo->manager_id = "";
	    $leaveInfo->admin_id = "";
	    $leaveInfo->m_approved_date = "";
	    $leaveInfo->admin_approve_date = "";
	    $leaveInfo->cancel_req_date = "";
	    $leaveInfo->cancel_approve_date = "";
	    $leaveInfo->employee_name = $this->data["myInfo"]->userName;
	    $leaveInfo->manager_name = "";
	    $leaveInfo->admin_name = "";
	    
	    return $leaveInfo; 
	}
	
	public function getLeaveHistory($empId4LeaveStatus, $total_current, $year){
	    $forward_sick_leave = $this->getForwardSickLeave($empId4LeaveStatus, $total_current, $year);
	    $forward_annual_leave = $this->getForwardAnnualLeave($empId4LeaveStatus, $total_current, $year);
	    
	    $leaveHistory = array(
	        "forward_sick_leave" => $forward_sick_leave,
	        "forward_annual_leave" => $forward_annual_leave
	    );	    
	    return $leaveHistory;
	}
	
	public function getForwardSickLeave($empId4LeaveStatus, $total_current, $year){

	    //calculation for joining year
        $join_date = $this->leave_model->getJoiningDateById($empId4LeaveStatus);
        $time = strtotime($join_date);
        $join_year = date("Y",$time);
        $join_month = date("m",$time);
        
        $sick_leave_per_month = ($total_current['SL']+$total_current['SLM'])/12;        
        $sick_leave_in_jYear = round( $sick_leave_per_month * (12 - $join_month));
        
        $forward_sick_leave = array();
        
        if($join_year == $year){            
            
            return $forward_sick_leave;
            
        }else{
            
            $forward_sick_leave[$join_year] = $sick_leave_in_jYear;
        }
        
        
        for($iYear = $join_year+1; $iYear<$year; $iYear++) {
            
            $forward_sick_leave[$iYear] = $total_current["SL"]+$total_current["SLM"];
        }
        
        $sick_leave_taken = $this->leave_model->getSickLeaveStatus($empId4LeaveStatus, $year);
        
        foreach ($sick_leave_taken as $obj){
            
            $lYear = $obj->year;
            $lTaken = $obj->taken;
            
            if(isset($forward_sick_leave[$lYear])) {

                $forward_sick_leave[$lYear] -= $lTaken;
            }
        }

        return $forward_sick_leave;        
	}
	
	public function getForwardAnnualLeave($empId4LeaveStatus, $total_current, $year){
	    
	    if(date('m') <= ANNUAL_LEAVE_MONTH_LIMIT && $year == date('Y')){
	        	        
	        $last_year = $year - 1;
	        
	        $forwardAnnualLeave = 0;
	        //calculation for joining year
	        $join_date = $this->leave_model->getJoiningDateById($empId4LeaveStatus);	        
	        $time = strtotime($join_date);
	        $join_year = date("Y",$time);	        
	        
	        if($join_year == $last_year){
	            
	            $join_month = date("m",$time);
	            $anual_leave_per_month = $total_current["AL"]/12;
	            
	            $forwardAnnualLeave = round($anual_leave_per_month * (12 - $join_month + 1));
	            
	        }else if($join_year != $year){
	            
	            $forwardAnnualLeave =  $total_current["AL"];
	        }
	        
	        //subtract the taken leave	        
	        $TakenAry = $this->leave_model->getAnnualTakenLeave($empId4LeaveStatus, $year);
	        
	        $totalAnnualTaken = $TakenAry['totalAnnualTaken'];
	        $forwardAnnualLeave -= $totalAnnualTaken;
	        
	        $forwardAnnualLeave = $forwardAnnualLeave > FORWARDED_ANNUAL_LEAVE_LIMIT ? FORWARDED_ANNUAL_LEAVE_LIMIT : $forwardAnnualLeave;

	        
	        $retAry = array(
	            'forwardAnnualLeave' => $forwardAnnualLeave,
	            'totalForwardedAnnualTaken' => $TakenAry['totalForwardedAnnualTaken']
	        );
	        return $retAry;
	        
	    }else{
	        //carry forwarded leave lapsed if not claimed within ANNUAL_LEAVE_MONTH_LIMIT.
	        
	        $retAry = array(
	            'forwardAnnualLeave' => 0,
	            'totalForwardedAnnualTaken' => 0
	        );
	        
	        return $retAry;
	    }    
	}
	
	public function getYearsArray(){

	    $start_year = date('Y', strtotime($this->ems_start_date));
	    $current_year = date('Y');
	    $range = range($current_year, $start_year);
	    $leaveInYears = array_combine($range, $range);
	    
	    return $leaveInYears;
	}
	
	public function pending() {

	    $dept_ary = $emp_id_ary = "";
	    
	    
	    $leave_approvers = $this->leave_model->getPermissionPrivileger(LEAVE_APPROVE);
	    $leave_verifiers = $this->leave_model->getPermissionPrivileger(LEAVE_VERIFY);
	    
	    $approverAry = array_keys($leave_approvers);
	    $verifierAry = array_keys($leave_verifiers);
	    
	    if($this->session->IsManager($this->myEmpId) || $this->session->IsManagement($this->myEmpId) || $this->session->IsAdmin($this->myEmpId) || isset($leave_approvers[$this->myEmpId]) || isset($leave_verifiers[$this->myEmpId]) ) {
	    
	    	$dept_ary = $this->session->getManagerDepartments($this->myEmpId);
	        
	        if($this->session->IsManagement($this->myEmpId)) {
	            
	            $emp_id_ary = $this->session->GetManagerArray();
	        }
	    }else exit("You have no privilege to access this page!");
	    
	    //print_r($dept_ary);
	    
	    //================Approval request========================
	
	    $isSetSPLeave = false;
	    $query = "SELECT l.id AS rid, l.period, l.leave_type, l.leave_date, l.leave_start, l.leave_end, e.emp_id, e.name, dp.dept_name, ds.designation FROM leaves l, employee e, departments dp, designations ds WHERE e.emp_id=l.emp_id AND e.emp_id!='".$this->myEmpId."' AND e.dept_code=dp.dept_code AND e.designation=ds.id ";
	    
	    
	    
	    if(!empty($dept_ary) || count($emp_id_ary)>0 || isset($leave_approvers[$this->myEmpId])) {
	        
	        $dept_ary = implode("','", $dept_ary);	        
	        if(!empty($emp_id_ary)) {
	            $emp_id_ary = implode("','",$emp_id_ary);
	        }
	        
	        $query .= "AND (( e.dept_code IN('$dept_ary') ";
	        if (!empty($emp_id_ary)){
	            $query .= "OR e.emp_id IN('$emp_id_ary') ";
	        }
	        
	        if(isset($leave_approvers[$this->myEmpId])){
	            $aryText = implode("','", $leave_approvers[$this->myEmpId]);
	            $query .= " OR e.emp_id IN('$aryText')"; 
	        }
	        //Hard coded for special leave: Only ED can approve special leave.
	        $isSetSPLeave = true;
	        if ($this->myEmpId == 'DM'){
    	        $query .= " OR l.leave_type='SP' ";
    	    }else {
    	        $query .= " AND l.leave_type!='SP' ";
    	    }
	        $query .= ") ) ";
	    }
	    //Hard coded for special leave: Only ED can approve special leave.
	    if (!$isSetSPLeave){
	        if ($this->myEmpId == 'DM'){
	            $query .= " OR l.leave_type='SP' ";
	        }else {
	            $query .= " AND l.leave_type!='SP' ";
	        }
	    }
	    $query .= "AND l.m_approved_date IS NULL ORDER BY l.leave_date DESC";
	    $result = $this->db->query($query);
	
	    //echo $this->db->last_query();
	
	    $approvalRequest = $result->result_array();
	
	    //===============Verification Request======================
	
	    $verificationRequest = array();
	    if($this->session->IsAdmin($this->myEmpId) || isset($leave_verifiers[$this->myEmpId]) ) {
	        
	        //$this->db->select();
	        	
	        $query = "SELECT l.id AS rid, l.period, l.leave_type, l.leave_date, l.leave_start, l.leave_end, e.emp_id, e.name, dp.dept_name, ds.designation FROM leaves l, employee e, departments dp, designations ds WHERE e.emp_id=l.emp_id  AND e.dept_code=dp.dept_code AND e.designation=ds.id ";
	        $query .= "AND l.m_approved_date IS NOT NULL AND l.admin_approve_date IS NULL ";
	        
	        
	        if(isset($leave_verifiers[$this->myEmpId]) && !$this->session->IsAdmin($this->myEmpId)){

	            $aryText = implode("','", $leave_verifiers[$this->myEmpId]);
	            $query .= " AND e.emp_id IN('$aryText')"; 
	        }
	        
	        $query .= " ORDER BY l.leave_date DESC";
	        $result = $this->db->query($query);
	        	
	        //echo $this->db->last_query();
	        	
	        $verificationRequest = $result->result_array();
	
	    }
	    
	    //===============Cancelation Request======================
	    
	    $cancelationRequest = array();
    
        $cancelationRequest = $this->leave_model->getCancelationRequest($this->myEmpId, $dept_ary);
	
	    $this->data["title"] = "Pending";
	    $this->data["sub_title"] = "Request";
	    $this->data["approvalRequest"] = $approvalRequest;
	    $this->data["verificationRequest"] = $verificationRequest;
	    $this->data["cancelationRequest"] = $cancelationRequest;
	    $this->data["leaves_array"] = $this->leaves_array;
	    
	    //$this->load->view('leaveform', $this->data);
	    $this->view('leave_request', $this->data);
	}	

	public function download($id){
	
	    $fileObj =  $this->leave_model->getLeaveAttFileById($id);
	    
	    $original_name = $fileObj->original_file_name;
	    $file_name =  $fileObj->file_name;
	
	    $headers = get_headers(base_url()."assets/files/".$file_name);
	    $response_code = substr($headers[0], 9, 3);
	
	    if( $response_code == "200"){
	        //success
	        $this->load->helper('download');
	        $data = file_get_contents("./assets/files/".$file_name);

	        force_download($original_name, $data);
	
	    }else{
	
	        $headers = get_headers($this->web_url_main."attachments/".$file_name);
	        $response_code = substr($headers[0], 9, 3);
	
	        if( $response_code == "200"){
	
	            //success
	            $this->load->helper('download');
	            $data = file_get_contents($this->web_url_main."attachments/".$file_name);
	
	            force_download($original_name, $data);
	
	        }else{
	            echo "ERROR: File not Found!";
	        }
	    }
	}
	
	public function del_leave_file($id){

	    $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : "";
	    
	    $flag = $this->leave_model->del_leave_file($id);
	    
	    if($flag){
	        $path = "assets/files/".$file_name;
		    if(is_file($path)) unlink($path);
	        
	        $return['msg'] = $this->message['delete_s'];
	        $return['status'] = true;
	         
	    }else{
	        $return['msg'] = $this->message['delete_f'];
	        $return['status'] = false;
	    }
	     
	    echo  json_encode($return);
	}
	
	public function glance(){
	    	    
	    /* Post */
	    $year = isset($_POST['select_year']) ? $_POST['select_year'] : date('Y');
	    $dept_code = isset($_POST['select_dept']) ? $_POST['select_dept'] : array();
	    $l_type = isset($_POST['leave_type']) ? $_POST['leave_type'] : array();

	    
    	//priviledge settings
        $userType = $this->data["uType"];
        if( $this->session->IsManagement($this->myEmpId) || $this->session->IsAdmin($this->myEmpId)  ) {
            
            $this->data['staff_array'] = $this->user_model->getStaffArray();
             
        } else if ($this->session->IsManager($this->myEmpId) ) {
             
            $this->data['departments'] = $this->user_model->getManagersDepts($this->myEmpId);
            
            $depts = array_keys($this->data['departments']);
            
            if(count($dept_code) > 0){
                foreach ($dept_code as $val){
                    if(!in_array($val, $depts)){
                        $this->data["status_array"] = $this->status_array;
                        $this->data["title"] = "ABC";
                        $this->data["sub_title"] = "ABC";
                        $this->data["message"] = "You have no privilege to access this page!";
                        
                        $this->load->view('not_found', $this->data);
                        return;
                    }
                }
            }
            
            $this->data['staff_array'] = $this->user_model->getStaffArray($depts);
            	         	        
        } else {
            
            $this->data["status_array"] = $this->status_array;
            $this->data["title"] = "ABC";
            $this->data["sub_title"] = "ABC";
            $this->data["message"] = "You have no privilege to access this page!";
            
            $this->load->view('not_found', $this->data);
            return;
        }

	    
	    $leave_type = array();
	    if(!empty($l_type)){
	       foreach ($l_type as $key){
	           $leave_type[$key] = $this->leaves_array[$key];
	       }
	    }else{
	        $leave_type = $this->leaves_array;
	    }

	    //die;

	    $staffsLeaveRecords = $this->leave_model->get_all_leaves($year, $dept_code, $l_type);
	    //print_r($staffsLeaveRecords);

        $this->data['leaves_array'] = $this->leaves_array;
	    $this->data["leaveInYears"] = $this->getYearsArray();
	    $this->data["year"] = $year;
	    $this->data["search_dept_code"] = $dept_code;
	    $this->data["search_leave_type"] = $l_type;
	    $this->data["staffsLeaveRecords"] = $staffsLeaveRecords; 
	    $this->data["leave_type"] = $leave_type;
	    $this->data["title"] = "Glance";
	    $this->data["sub_title"] = "At a Glance";
	    $this->view('leave_glance', $this->data);
	}
	
	public function all(){
	    
	    $dept_code = isset($_POST['selected_dept']) ? $_POST['selected_dept'] : array();
	    $staffs = isset($_POST['selected_staff']) ? $_POST['selected_staff'] : array();
	    
	    //priviledge settings
	    $userType = $this->data["uType"];
	    if( $this->session->IsManagement($this->myEmpId) || $this->session->IsAdmin($this->myEmpId) ) {
	    
	        $this->data['staff_array'] = $this->user_model->getStaffArray();
	         
	    } else if ($this->session->IsManager($this->myEmpId) ) {
	         
	        $this->data['departments'] = $this->user_model->getManagersDepts($this->myEmpId);
	    
	        $depts = array_keys($this->data['departments']);
	    
	        if(count($dept_code) > 0){
	            foreach ($dept_code as $val){
	                if(!in_array($val, $depts)){
	                    $this->data["status_array"] = $this->status_array;
	                    $this->data["title"] = "ABC";
	                    $this->data["sub_title"] = "ABC";
	                    $this->data["message"] = "You have no privilege to access this page!";
	    
	                    $this->load->view('not_found', $this->data);
	                    return;
	                }
	            }
	        }
	    
	        $this->data['staff_array'] = $this->user_model->getStaffArray($depts);
	         
	    } else {
	    
	        $this->data["status_array"] = $this->status_array;
	        $this->data["title"] = "ABC";
	        $this->data["sub_title"] = "ABC";
	        $this->data["message"] = "You have no privilege to access this page!";
	    
	        $this->load->view('not_found', $this->data);
	        return;
	    }

	    
	    $staffsLeaveRecords = $this->leave_model->get_all_years_leave($dept_code, $staffs);

	    $this->data['leave_type'] = $this->leaves_array;
	    $this->data["leaveInYears"] = $this->getYearsArray();
	    $this->data["search_dept_code"] = $dept_code;
	    $this->data["search_staffs"] = $staffs;
	    $this->data["staffsLeaveRecords"] = $staffsLeaveRecords;
	    
	    $this->data["title"] = "All_Leave";
	    $this->data["sub_title"] = "All Leave Taken by Employee";
	    $this->view('leave_all', $this->data);
	}
	
	public function report($today="") {
	    
	    $leave_approvers = $this->leave_model->getPermissionPrivileger(LEAVE_APPROVE);
	    $leave_verifiers = $this->leave_model->getPermissionPrivileger(LEAVE_VERIFY);
	     
	    $approverAry = array_keys($leave_approvers);
	    $verifierAry = array_keys($leave_verifiers);
	     
	    $hasPrivilege = false;
	    if($this->session->IsManager($this->myEmpId) || $this->session->IsManagement($this->myEmpId) || $this->session->IsAdmin($this->myEmpId) || isset($leave_approvers[$this->myEmpId]) || isset($leave_verifiers[$this->myEmpId]) ) {
	         
	        $hasPrivilege = true;
	        
	    }// else exit("You have no privilege to access this page!");
	    
	    if (!empty($_POST['today_date']) && strlen($_POST['today_date']) == 10){
	        $today = $_POST['today_date'];
	    }
	    
	    if(empty($today)) {
	        $today = date('Y-m-d');
	    }
	    
	    $sql = "SELECT l.emp_id, e.name, e.image, ds.designation, dp.dept_name, l.id, l.leave_end ".
	        "FROM leaves l, employee e, designations ds, departments dp ".
	        "WHERE e.emp_id=l.emp_id AND ".
	        "e.designation=ds.id AND ".
	        "e.dept_code=dp.dept_code AND ".
	        "l.leave_start<='$today' AND ".
	        "l.leave_end>='$today' AND e.archive='N' AND l.admin_approve_date IS NOT NULL";
	    $query = $this->db->query($sql);
	    $this->data["will_join"] = $query->result_array();
	    
	    $yesterday = date('Y-m-d', strtotime('-1 day', strtotime($today)));
	    $sql = "SELECT l.emp_id, e.name, e.image, ds.designation, dp.dept_name, l.id, l.leave_end ".
        	"FROM leaves l, employee e, designations ds, departments dp ".
        	"WHERE e.emp_id=l.emp_id AND ".
        	"e.designation=ds.id AND ".
        	"e.dept_code=dp.dept_code AND ".
        	"l.leave_end='$yesterday' AND e.archive='N' ";
	    $query = $this->db->query($sql);
	    $this->data["joined_today"] = $query->result_array();
	    
	    $this->data["title"] = "Report";
	    $this->data["sub_title"] = "Today's Leave";
	    $this->data["today"] = $today;
	    $this->data["hasPrivilege"] = $hasPrivilege;
	    //$this->data["sub_title"] = date("Y-m-d") == $today ? "Today's Leave" : "Today($today)'s Leave";
		
		$will_join_emp_id = array();
		foreach($this->data["will_join"] as $employee){
			$will_join_emp_id[] = $employee['emp_id'];
		}

		$joined_today = array();
		$arrCon = 0;
		foreach($this->data["joined_today"] as $employee){
			if (!in_array($employee['emp_id'], $will_join_emp_id)){
				$joined_today[$arrCon]['emp_id'] = $employee['emp_id'];
				$joined_today[$arrCon]['name'] = $employee['name'];
				$joined_today[$arrCon]['image'] = $employee['image'];
				$joined_today[$arrCon]['designation'] = $employee['designation'];
				$joined_today[$arrCon]['dept_name'] = $employee['dept_name'];
				$joined_today[$arrCon]['id'] = $employee['id'];
				$joined_today[$arrCon]['leave_end'] = $employee['leave_end'];
				$arrCon++;
			}
		}

		$this->data["joined_today"] = $joined_today;
	    
	    $this->view('leave_report', $this->data);
	} 

}