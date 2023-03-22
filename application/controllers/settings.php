<?php
class Settings extends G_Controller {

    public $adminFlag = false;
	public $data = array();
	public $myEmpId = '';
	public function __construct() {
		parent::__construct();
		$this->load->library('session');
		
		$this->isLoggedIn();
		
		$this->load->model('user_model');
		$this->load->model('settings_model');
		$this->load->library('pagination');
		//$this->data["userId"] = $this->session->GetLoginId();
		$this->data["myInfo"] = $this->session->GetMyBriefInfo();
		//$this->data["userImage"] = $this->session->GetUserImage();
		//$this->data["userDesignation"] = $this->session->GetUserDesignation();
		//$this->data["userDepartment"] = $this->session->GetUserDepartment();
		$this->data['departments'] = $this->user_model->department();
		$this->data["menu"] = "Settings";
		$this->data["uType"] = $this->session->GetUserType();
		
		$this->myEmpId = $this->session->GetLoginId();		
		$this->data['isManagement'] = $this->session->IsManagement($this->myEmpId);
		$this->data['isAdmin'] = $this->session->IsAdmin($this->myEmpId);
		$this->data['isManager'] = $this->session->IsManager($this->myEmpId);
		
		
		if(!$this->data['isAdmin']) {
		    $this->data["status_array"] = $this->status_array;
		    $this->data["title"] = "ABC";
		    $this->data["sub_title"] = "ABC";
		    $this->data["message"] = "You have no privilege to access this page!";
		}
		$this->data["controller"] = $this;
		
		
		
	}
	public function deptIdGenerator($dept_name) {
	    $code_ary = array();
	    foreach ($this->data["departments"] as $key=>$val) {
	
	        $code_ary[] = $key;
	    }
	    
	    $temp_code = $dept_name[0];
	    $dn_len = strlen($dept_name);
	    $temp2_code=array();
	    for($i=1; $i<$dn_len; $i++) {
	        if($dept_name[$i]!=' ') {
	            $temp2_code[]= $temp_code.$dept_name[$i];
	        }
	    }
	    
	    foreach($temp2_code AS $t2c) {
	        if(!in_array(strtoupper($t2c),$code_ary)) {
	            return strtoupper($t2c);
	        }
	    }
	    
	    return null;
	}
	
	public function department()
	{
    	if(!$this->data['isAdmin']) {
        $this->load->view('not_found', $this->data);
        return;
        }
        
        $this->data["title"] = "Department";
		$this->data["sub_title"] = "Department List";
		
		$departments = $this->settings_model->department();
		$this->data['depts'] = $departments;
		
		$this->load->view('department',$this->data);
	}
	
	public function add_dept() {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	     
	    $this->data['departments'] = $this->user_model->department();
	    $dept_name = ucfirst($_POST["dept_name"]);
	     
	    $res = $this->settings_model->getDept($dept_name);
	    $num = count($res);
	     
	    $data['dept_code'] = $this->deptIdGenerator($dept_name);
	    if($num || empty($data['dept_code'])) {
	        exit(json_encode(array("status"=>false,"msg"=>"Duplicate or Erroneous Name ")));
	    }
	     
	    $data['dept_name'] = $dept_name;
	    $data['id']= $this->settings_model->addDept($data);
	    if($data['id']){
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"A","affected"=>$data['id'],"log_text"=>"departments=>dept_name:".$data['dept_name'], "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	        $data['status'] = true;
	    }else {
	        $data['status'] = false;
	    }
	    echo json_encode($data);
	}
	
	
	
	public function update_dept() {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	
	    $this->data['departments'] = $this->user_model->department();
	    $dept_name = ucfirst($_POST["dept_name"]);
	    $id = $_POST['dept_id'];
	    $old_dept_name = $_POST['old_dept_name'];
	
	    $flag = $this->settings_model->getDept($dept_name);
	    $num = count($flag);
	     
	    //$data['dept_code'] = $this->deptIdGenerator($dept_name);
	    if($num) {
	        exit(json_encode(array("status"=>false,"msg"=>"Duplicate or Erroneous Name ")));
	    }
	     
	    $data['dept_name'] = $dept_name;
	    $flag = $this->settings_model->updateDept($id, $data);
	    $data['id']=$id;
	    if($flag){
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"U","affected"=>$id,"log_text"=>"departments=>dept_name:".$old_dept_name.'=>'.$data['dept_name'], "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	        $data['status']=true;
	    }else {
	        $data['status']=false;
	    }
	     
	    echo json_encode($data);
	}
	
	
	

	public function designation($dept_code=""){	
		if(!$this->data['isAdmin']) {
        $this->load->view('not_found', $this->data);
        return;
        }
	    
		$designations = $this->settings_model->designation($dept_code);
		
		$designation_array = array();
		foreach ($designations as $obj) {
			$designation_array[$obj->dept_code][] = $obj;
		}

		$dept_code = !empty($dept_code) ? $dept_code : "AC";
		
		foreach ($this->data['departments'] as $key=>$val) {
		    
			$ary = isset($designation_array[$key]) ? $designation_array[$key] : array();
			
			$o = new stdClass();			
			$o->designation_array = $ary;			
			$o->dept_name = $val;
			$designation_array[$key] = $o;

			
		}

		$this->data["designations"] = $designation_array;		
		$this->data["dept_code_of_designation"] = $dept_code;
		$this->data["title"] = "Designation";
		$this->data["sub_title"] = "Designation List";
		$this->load->view('designation',$this->data);
	}
	
	public function add_des(){
	    if(!$this->data['isAdmin']) {
	        $this->load->view('not_found', $this->data);
	        return;
	    }
	
	    $data['dept_code'] = $_POST['dept_code'];
	    $data['designation'] = $_POST['designation'];
	
	    $data['id'] = $this->settings_model->addDes($data);
	    if($data['id']){
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"A","affected"=>$data['id'],"log_text"=>"Designation=>".$data['dept_code']."=>".$data['designation'], "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	        $data['status']=true;
	
	    }else {
	        $data['status']=false;
	    }
	
	    echo json_encode($data);
	}
	
	public function update_des() {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	
	    $data['dept_code'] = $_POST['dept_code'];
	    $data['designation'] = $_POST['designation'];
	    $old_des_name = $_POST['old_des_name'];
	    $id = $_POST['des_id'];
	
	    $flag = $this->settings_model->updateDes($id, $data);
	
	    if($flag){
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"U","affected"=>$id,"log_text"=>"Designation=>".$data['dept_code'].":".$old_des_name.'=>'.$data['designation'], "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	        $data['status']=true;
	    }else {
	        $data['status']=false;
	    }
	    $data['id'] = $id;
	    $data['status']=true;
	
	    echo json_encode($data);
	}
	
	public function privilege (){
	    
		if( !($this->data['isAdmin'] || $this->data['isManagement']) ) {
            $this->load->view('not_found', $this->data);
            return;
        }
	    
	    $roster = array();
	    $objects = $this->settings_model->getPriv();    
	    
	    foreach ($objects as $key=>$obj){
            if($obj->type == 'M') {
	           $manager[$obj->dept_code][] = $obj;
            } elseif ($obj->type == 'A'){
                $admin[] = $obj; 
            } elseif ($obj->type == 'R'){
                $roster[] = $obj; 
            } else{
                $boss[] = $obj;
            }
	    }
	    //print_r($this->data['departments']); 
	    foreach ($this->data['departments'] as $dept_code=>$dept_name){
	        $ary = isset($manager[$dept_code]) ? $manager[$dept_code]: array();
	        
	        $o = new stdClass();
	        $o->managers = $ary;
	        $o->dept_name = $dept_name;
	        //$o->dept_code = 
	        
	        $new[$dept_code] = $o;
	    }	    
	    
	    $this->data['manager'] = $new;
	    $this->data['admin'] = $admin;
	    $this->data['boss'] = $boss;
	    $this->data['roster'] = $roster;
	    	    
	    $employees = $this->settings_model->getEmployee();
	    foreach ($employees as $obj){
	        $emp[$obj->dept_name][] = $obj;
	    }
	    $this->data['employees'] = $emp;
	    
	    $this->data["title"] = "Privilege";
		$this->data["sub_title"] = "Administrator Privilege Setting";
		$this->load->view('privilege',$this->data);
	}
	

	
	public function add_priviledge(){
	    
	    if( !($this->data['isAdmin'] || $this->data['isManagement']) ) {
	        $this->load->view('not_found', $this->data);
	        return;
	    }
	     
	    $data['type'] = $this->input->post('type');
	    $data['emp_id'] = $this->input->post('select_emp_id');
	    $data['dept_code'] = $this->input->post('dept_code');
	    
	    $flag = $this->settings_model->addPriv($data);
	    
	    if($flag){
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"A","affected"=>$data['emp_id'],"log_text"=>$data['dept_code']."=>".$data['type'], "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	        redirect(base_url().'settings/privilege');
	    }else {
	    	redirect(base_url().'settings/privilege');
	    }
	    /*
	    if ($flag == false){
	        echo '<script language="javascript">';
	        echo 'alert("This person already is in this privilege!")';
	        echo '</script>';
	    }*/
	}
	
	public function delete_priviledge(){
	    
	    if( !($this->data['isAdmin'] || $this->data['isManagement']) ) {
        $this->load->view('not_found', $this->data);
        return;
        }
        
	    $data['type'] = $this->input->post('type');
	    $data['emp_id'] = $this->input->post('emp_id');
	    $data['dept_code'] = $this->input->post('dept_code');
	    
	    $flag = $this->settings_model->delPriv($data);
	    if($flag){
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"D","affected"=>$data['emp_id'],"log_text"=>$data['dept_code']."=>".$data['type'], "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	        redirect(base_url().'settings/privilege');
	    }else{
	         
	    }
	}
	
	
	public function permission_priv (){
	    
	    if( !($this->data['isAdmin'] || $this->data['isManagement']) ) {
	        $this->load->view('not_found', $this->data);
	        return;
	    }

	    $permissionGroups = array("3"=>"Leave", "4"=>"Attendance","5"=>"Roster");
	    $permissionPrivType = $this->settings_model->getPermissionPrivilegeType($permissionGroups);

	    //print_r($permissionPrivType);
	    
	    $employees = $this->settings_model->getEmployee();
	    $emp = array();
	    foreach ($employees as $obj){
	        $emp[$obj->dept_name][] = $obj;
	    }
	    $this->data['employees'] = $emp;
	    $this->data['permissionGroups'] = $permissionGroups;
	    $this->data['permissionPrivType'] = $permissionPrivType;

	    $this->data["title"] = "Permission_privilege";
	    $this->data["sub_title"] = "Permission Privilege Setting";
	    $this->view('settings_permission_priv', $this->data);
	}
	
	public function add_prmsn_priv (){
	    
	    if( !($this->data['isAdmin'] || $this->data['isManagement']) ) {
            $return['status'] = false;
            $return['msg'] = "Errors occured. Try again";
            echo json_encode($return);
            return;            
	    }

	    
	    $data= array();
	    $data["activity_id"] = isset($_POST['privType']) ? $_POST['privType'] : "";
	    $data["privileger_id"]  = isset($_POST['privileger_select']) ? $_POST['privileger_select'] : "";
	    $staff_ids = isset($_POST['staffs_select']) ? $_POST['staffs_select'] : "";
	    
	    $return = array();
        if(empty($data["activity_id"]) || empty($data["privileger_id"]) || (count($staff_ids) ==0)){
            
            $return['status'] = false;
            $return['msg'] = "select all necessary field first.";
            
        } else{
            
            $flag = $this->settings_model->addPermissionPriv($data, $staff_ids);
            
            if($flag){
                
                $return['status'] = true;
                $return['msg'] = $this->message['insert_s'];
            }else{
                $return['status'] = false;
                $return['msg'] = "Errors occured. Try again";
            }
            
        }
        
        echo json_encode($return);
        return;
	}
	
	public function getPrivById(){
	    
	    $privileger_id = isset($_POST['privileger_id']) ? $_POST['privileger_id'] : "";
	    $activity_id = isset($_POST['activity_id']) ? $_POST['activity_id'] : "";
	    
	    $return = array();
	    
	    if(!empty($privileger_id) && !empty($activity_id)){
	        $return['status'] = true;
	        $return['privileged_staffs'] = $this->settings_model->getPermissionById($privileger_id, $activity_id);

	    }else {
	        $return['status'] = false;
	    }
	    
	    echo json_encode($return);
	    return;
	}


	
	public function facility() {
	    if(!$this->data['isAdmin']) {
	        $this->load->view('not_found', $this->data);
	        return;
	    }
	    $this->data["title"] = "Facility";
	    $this->data["sub_title"] = "Facility List";
	
	    $facilities = $this->settings_model->facility();
	    $this->data['facilities'] = $facilities;
	
	    $this->load->view('facility',$this->data);
	    //$this->load->view('delete_confirm');
	}
	public function add_facility() {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	
	    ;
	    $data['facility'] = ucfirst($_POST["facility"]);
	    $data['description'] = ucfirst($_POST["description"]);
	    
	    $res = $this->settings_model->getFacility($data['facility']);
	    $num = count($res);
	    
	    if($num || empty($data['facility'])) {
	        exit(json_encode(array("status"=>false,"msg"=>"Duplicate or Erroneous Name ")));
	    }
	    
	    $insertID = $this->settings_model->addFacility($data);
	    //$data['facility_id'] = $this->db->insert_id();
	    if($insertID){
	        $data['status'] = true;
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"A","affected"=>$insertID,"log_text"=>"facility_options=>Facility:".$data['facility'], "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	    }else {
	        $data['status'] = false;
	        $data['msg'] = "Cant not add this facility to database due to some error.";
	    }	
	    echo json_encode($data);
	}
	
	public function update_facility() {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	
	    $data['facility'] = ucfirst($_POST["facility"]);
	    $data['description'] = ucfirst($_POST["description"]);
	    $id = $_POST['facility_id'];
	
	    $flag = $this->settings_model->updateFacility($id, $data);
	    if($flag){
	        $data['status'] = true;
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"U","affected"=>$id,"log_text"=>"facility_options=>Facility:".$data['facility'], "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	    }else {
	        $data['status'] = false;
	        $data['msg'] = "Cant not update this facility to database due to some error.";
	    }
	
	    echo json_encode($data);
	}
	
	public function delete_facility($id){
	    if(!$this->data['isAdmin']) {
            //$this->load->view('not_found', $this->data);
            return;
        }
	    
        $facility_name = $_POST['facility_name'];
        $flag = $this->settings_model->deleteFacility($id);
	    if($flag){
	        $data['status'] = true;
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"D","affected"=>$id,"log_text"=>"facility_options=>facility:".$facility_name, "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	    }else {
	        $data['status'] = false;
	        $data['msg'] = "Cant not delete this facility to database due to some error.";
	    }
	    
	    echo json_encode($data);
	}
	
	public function delete($pk_id) {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	    $name = $_POST["name"];
	    $model = ucfirst($_REQUEST["model"]);
	    $this->load->model($model);
	     
	    $mObj = new $model();
	    $mObj->load($pk_id);
	    if (!$mObj->id) {
	        show_404();
	    }
	    $mObj->delete();
	     
	    $status = $this->db->affected_rows()>0;
	    if($status){
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"D","affected"=>$pk_id,"log_text"=>$model."=>".$name, "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	    }
	    echo json_encode(array("status"=>$status));
	}
	public function holiday(){
	    
	    $year = isset($_POST['yearSelect']) ? $_POST['yearSelect'] : date('Y');

	    $holi_days = $this->settings_model->getHolidays($year);
	    $total = 0;
	    $spent = 0;
	    $today = date('Y-m-d');
    	foreach ($holi_days as $obj){
            ++$total;
            if($obj->date < $today){
                ++$spent;
            }             		    
		}
		
	    $this->data['holi_days'] = $holi_days;
	    $this->data['select_year'] = $year;
	    $this->data['total'] = $total;
	    $this->data['spent'] = $spent;
	    $this->data['ems_start_year'] = substr($this->ems_start_date, 0, 4);
	    
	    $this->data["menu"] = "Remark";
	    $this->data["title"] = "Holiday";
	    $this->data["title"] = "Holiday";
	    $this->data["sub_title"] = "List of Holidays";
	    $this->view('holidays',$this->data);
	}
	
	public function add_holiday() {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_$this->data['isAdmin']ata);
	        return;
	    }
	    
	    
	    $data['from'] = $_POST['from'];
	    $data['to'] = $_POST['to'];
	    $data['description'] = $_POST['description'];
	    
	    $insert_ids = $this->settings_model->addHoliday($data);
 
	    if(count($insert_ids)){
	        
	        $return['msg'] = $this->message['insert_s'];
	        $return['status'] = true;
	        $return['insert_ids'] = $insert_ids;
	    
	    }else{
	        $return['msg'] = $this->message['insert_f'];
	        $return['status'] = false;
	    }
	    
	    echo json_encode($return);
	}
	
	
	public function update_holiday($id) {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	
	    $data['from'] = $_POST['from'];
	    $data['to'] = $_POST['to'];
	    $data['description'] = $_POST['description'];
	
	    $flag = $this->settings_model->updateHoliday($id, $data);
	    
		if($flag){

	        $return['msg'] = $this->message['update_s'];
	        $return['status'] = true;
	        
	    }else{
	        $return['msg'] = $this->message['update_f'];
	        $return['status'] = false;
	    }
	
	    echo json_encode($return);
	}

	
	public function del_holiday($id){
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	    
	    $flag = $this->settings_model->del_holiday($id);
	    
	    if($flag){
	        $return['msg'] = $this->message['delete_s'];
	        $return['status'] = true;
	        
	    }else{
	        $return['msg'] = $this->message['delete_f'];
	        $return['status'] = false;
	    }
	    
	    echo  json_encode($return);
	}
	
	public function incident(){
	    
	    $year = isset($_POST['yearSelect']) ? $_POST['yearSelect'] : date('Y');
	    
	    $incidents = $this->settings_model->getIncident($year);
	    
	    $total = 0;
	    $spent = 0;
	    $today = date('Y-m-d');
	    foreach ($incidents as $obj){
	        ++$total;
	        if($obj->date < $today){
	            ++$spent;
	        }
	    }
	    
	    $this->data['incidents'] = $incidents;
	    	    
	    $this->data['select_year'] = $year;
	    $this->data['total'] = $total;
	    $this->data['spent'] = $spent;
	    $this->data['ems_start_year'] = substr($this->ems_start_date, 0, 4);
	    $this->data["menu"] = "Remark";
	    $this->data["title"] = "Incident";
	    $this->data["sub_title"] = "List of Incidents";
	    $this->view('incident',$this->data);
	}
	
	public function add_incident() {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }	     
	     
	    $data['from'] = $_POST['from'];
	    $data['to'] = $_POST['to'];
	    $data['description'] = $_POST['description'];
	     
	    $insert_ids = $this->settings_model->addIncident($data);
	
	    if(count($insert_ids)){
	         
	        $return['msg'] = $this->message['insert_s'];
	        $return['status'] = true;
	        $return['insert_ids'] = $insert_ids;
	         
	    }else{
	        $return['msg'] = $this->message['insert_f'];
	        $return['status'] = false;
	    }
	     
	    echo json_encode($return);
	}
	
	
	public function update_incident($id) {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	
	    $data['from'] = $_POST['from'];
	    $data['to'] = $_POST['to'];
	    $data['description'] = $_POST['description'];
	
	    $flag = $this->settings_model->updateIncident($id, $data);
	     
	    if($flag){
	
	        $return['msg'] = $this->message['update_s'];
	        $return['status'] = true;
	         
	    }else{
	        $return['msg'] = $this->message['update_f'];
	        $return['status'] = false;
	    }
	
	    echo json_encode($return);
	}	
	
	public function del_incident($id){
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	     
	    $flag = $this->settings_model->del_incident($id);
	     
	    if($flag){
	        $return['msg'] = $this->message['delete_s'];
	        $return['status'] = true;
	         
	    }else{
	        $return['msg'] = $this->message['delete_f'];
	        $return['status'] = false;
	    }
	     
	    echo  json_encode($return);
	}
	
	public function office_time(){
	    
	    //$this->data['depts'] = $departments;
	    
	    
	    $isAdmin = $this->session->IsAdmin($this->myEmpId);
	    $isManagement = $this->session->IsManagement($this->myEmpId);
	    $isManager = $this->session->IsManager($this->myEmpId);
	    
	    if(!($isAdmin || $isManagement || $isManager) ){
	        
	        $this->load->view('not_found', $this->data);
	        return;
	    }
	    
	    $select = array(
	        'select'=>array(
	            'e.emp_id',
	            'e.name',
	            'e.scheduled_attendance',
	            'e.roster',
	            'e.dept_code',
	            'dp.dept_name'
	        )
	    );
	    
	    if( $isAdmin || $isManagement ){
	         
	        $this->data['departmentLists'] = $this->data['departments'];
	        
	        $staffsRecord = $this->user_model->get_user($select);
	        
	    }else{
	        //manager	        
	        $departmentLists = $this->user_model->getManagersDepts($this->myEmpId);
	        $this->data['departmentLists'] = $departmentLists;
	        
	        $deptCodeAry = array();
	        foreach ($departmentLists as $key=>$val){
	            $deptCodeAry[] = $key;
	        }
	        
	        $staffsRecord = $this->user_model->get_usersByDeptCode($select, $deptCodeAry);	        
	    }

	    $staffs = array();
	    foreach ($staffsRecord as $obj) {
	        $staffs[$obj->dept_code][] = $obj;
	    }
	    
	    $weekend = $this->settings_model->getWeekend();
	    
	    $array =array();	    
	    foreach ($staffsRecord as $obj){

	        $array[$obj->emp_id] = $this->default_weekend;
	        $array[$obj->emp_id]['emp_id'] =$obj->emp_id;
	    }
	    foreach ($weekend as $key=>$obj){
	        
	        $array[$key] = $obj;
	    }
	    
	    //print_r($staffs);
	    
	    $this->data["staffs"] = $staffs;
	    $this->data["default_weekend"] = $this->default_weekend;
	    $this->data["weekend"] = $array;
	    $this->data['day_array'] = $this->day_array;
	    
	    $this->data["title"] = "Office_time";
	    $this->data["sub_title"] = "Department";
	    $this->view('office_time',$this->data);
	    
	    //$employees = $this->settings_model->
	}
	
	public function changeType($emp_id){
	    
		$isAdmin = $this->session->IsAdmin($this->myEmpId);
	    $isManagement = $this->session->IsManagement($this->myEmpId);
	    $isManager = $this->session->IsManager($this->myEmpId);
	    
	    if(!($isAdmin || $isManagement || $isManager) ){
	        
	        $this->load->view('not_found', $this->data);
	        return;
	    }
	    
	    if(isset($_POST['roster']) && !empty($_POST['roster'])){
	        $value = $_POST['roster'];
	        
	        if($value == 'N'){
	            $data['roster'] = 'Y';
	            $return['value'] = 'Y';
	            $return['text'] = 'Roster';
	        }elseif($value == 'Y'){
	            $data['roster'] = 'N';
	            $return['value'] = 'N';
	            $return['text'] = 'Non-roster';
	        }
	    } 
	    
	    if(isset($_POST['schedule']) && !empty($_POST['schedule'])){
	        $value = $_POST['schedule'];
	        
	        if($value == 'N'){
	            $data['scheduled_attendance'] = 'Y';
	            $return['value'] = 'Y';
	            $return['text'] = 'Scheduled';
	        }elseif($value == 'Y'){
	            $data['scheduled_attendance'] = 'N';
	            $return['value'] = 'N';
	            $return['text'] = 'Non-scheduled';
	        }
	    } 

	    $flag = $this->settings_model->updateAttendanceType($emp_id, $data);
	    
	    if($flag){
	        $return['msg'] = $this->message['update_s'];
	        $return['status'] = true;
	    }else{
	        $return['msg'] = $this->message['update_f'];
	        $return['status'] = false;
	    }
	    
	    echo json_encode($return);
	}
	
	public function update_weekly_leave(){
	    
	    //Data['']
	    
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	    
	    $options = (is_array($_POST['chk'])) ? $_POST['chk'] : array();
	    $emp_id = isset($_POST['staffId']) ? $_POST['staffId'] : '';
	    
	    
	    $weekend = $this->default_weekend;
	    foreach ($weekend as $key=>$val){
	        $weekend[$key] = 'N';
	    }
	    foreach ($options as $val){
	        $weekend[$val] = 'Y';  
	    }
	    
	    if(!empty($emp_id)){
	        
	        $flag = $this->settings_model->update_weekly_leave($emp_id, $weekend);
	    }
	    
	    redirect(base_url().'settings/office_time');
	    die;
	}
	
	public function password($message = array()){

	    $this->data['message'] = $message;
	    $this->data['title'] = "password";
	    $this->data['sub_title'] = "Password";
	    $this->view('password', $this->data);
	}
	
	
	public function change_password(){

        $currentPassword = isset($_POST['currentPassword']) ? $_POST['currentPassword'] : "";
        $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : "";
        $retypePassword = isset($_POST['retypePassword']) ? $_POST['retypePassword'] : "";

        $passMatch = $this->settings_model->getUserPass($this->myEmpId, $currentPassword);
        
        $flag = false;
        if($passMatch && ($newPassword == $retypePassword)){
            
            $flag = $this->settings_model->updatePassword($this->myEmpId, $newPassword);
            
        }else{
            //echo "false";
        }
        
        if($flag){
            $affectedTxt = "emp_id=".$this->myEmpId.", pass: " .  "**" . substr($newPassword, -1) . "(".strlen($newPassword).")";
            $this->addActivityLog('U', $affectedTxt, 'Password Changed');
            $message['status'] = true;
            $message['msg']=  "Password has been changed successfully.";
            $this->password($message);
            
        }else{
            
            $message['status'] = false;
            $message['msg']=    "Password change has been failed!";
            $this->password($message);

        }
	}
	
	
	public function note(){
	    if(!$this->data['isAdmin']) {
	        $this->load->view('not_found', $this->data);
	        return;
	    }
	     
	    $notes = $this->settings_model->getNotes();
	    $this->data['notes'] = $notes;
	    
	    $this->data["title"] = "Note";
	    $this->data["sub_title"] = "Note List";
	    $this->view('note',$this->data);
	}
	
	public function add_note() {
	    if(!$this->data['isAdmin']) {
	        $this->load->view('not_found', $this->data);
	        return;
	    }
	     
	    $data['date'] = $_POST['date'];
	    $data['subject'] = $_POST['subject'];
	    $data['note'] = $_POST['text'];
	     
	    $insert_ids = $this->settings_model->addNote($data);
	
	    if(count($insert_ids)){
	         
	        $return['msg'] = $this->message['insert_s'];
	        $return['status'] = true;
	        $return['insert_ids'] = $insert_ids;
	         
	    }else{
	        $return['msg'] = $this->message['insert_f'];
	        $return['status'] = false;
	    }
	     
	    echo json_encode($return);
	}
	
	
	public function update_note($id) {
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	
	    $data['date'] = $_POST['date'];
	    $data['subject'] = $_POST['subject'];
	    $data['note'] = $_POST['text'];
	
	    $flag = $this->settings_model->updateNote($id, $data);
	     
	    if($flag){
	
	        $return['msg'] = $this->message['update_s'];
	        $return['status'] = true;
	         
	    }else{
	        $return['msg'] = $this->message['update_f'];
	        $return['status'] = false;
	    }
	
	    echo json_encode($return);
	}
	
	
	public function del_note($id){
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	     
	    $flag = $this->settings_model->del_note($id);
	     
	    if($flag){
	        $return['msg'] = $this->message['delete_s'];
	        $return['status'] = true;
	         
	    }else{
	        $return['msg'] = $this->message['delete_f'];
	        $return['status'] = false;
	    }
	    
	    echo  json_encode($return);
	}
}