<?php 
class Evaluation_model extends G_Model {
	public function __construct()	{
	  $this->load->database(); 
	}
	
	public function get_user($array=array()) {

	  	$this->db->select($array['select']);
	  	$this->db->from('employee e');
	  	$this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	  	$this->db->join('designations ds', 'ds.id=e.designation', 'left');
	  	$this->db->where('e.archive','N');
	  	if(isset($array["dept_code"]) && !empty($array["dept_code"]) && $array["dept_code"]!="all") 
	  	    $this->db->where('e.dept_code',$array["dept_code"]);
	  	$this->db->order_by('e.emp_id','asc');
	  	if(isset($array["offset"])) $this->db->limit(ROWS_PER_PAGE, $array["offset"]);
	  	
	  	$query = $this->db->get();
	  	return $query->result();
	}
	
	public function getEmpEvalInfo($id){
	    $data = array(
	        'e.emp_id',
	        'e.name',
	        'e.dept_code',
	        'e.email',
	        'e.jdate',
	        'e.gender',
	        'e.status',
	        'dp.dept_name',
	        'ds.designation',
	        'sl.date'
	    );
	    $this->db->select($data);
	    $this->db->from('employee e');
	    $this->db->join('departments dp','e.dept_code = dp.dept_code','inner');
	    $this->db->join('designations ds','e.designation = ds.id','inner');
	    $this->db->join('status_log sl','e.emp_id = sl.emp_id AND e.status = sl.status','left');
	    $this->db->where('e.emp_id', $id);
	    $query = $this->db->get();
	
	    return $query->row_array();	    
	}
	
	public function getEvalInfo($id, $select){
	    
	    $this->db->select($select);
	    $this->db->from('evaluations');
	    $this->db->where('id', $id);
	    $query = $this->db->get();
	    
	    return $query->row_array();
	}
	
	
	public function getFullEval($id, $emp_id=''){

	    $this->db->select('
            ev.*, ev.status AS evstatus, 
            e.name as employee_name,
            m.name as manager_name,
            a.name as admin_name
	        ');
	    $this->db->from('evaluations ev');
	    $this->db->join('employee e', 'e.emp_id=ev.emp_id', 'left');
	    $this->db->join('employee m', 'm.emp_id=ev.manager_id', 'left');
	    $this->db->join('employee a', 'a.emp_id=ev.admin_id', 'left');
	    $this->db->where('ev.id', $id);
	    if (!empty($emp_id)){
	        $this->db->where('ev.emp_id', $emp_id);
	    }
	    $query = $this->db->get();
	   
	    return $query->row_array();
	    
	    //echo 
	}

	public function getDraftEval($dataArray=array()){
	
	    $this->db->select('ev.*, ev.status AS evstatus, e.name as employee_name, m.name as manager_name, a.name as admin_name');
	    $this->db->from('evaluations ev');
	    $this->db->join('employee e', 'e.emp_id=ev.emp_id', 'left');
	    $this->db->join('employee m', 'm.emp_id=ev.manager_id', 'left');
	    $this->db->join('employee a', 'a.emp_id=ev.admin_id', 'left');
	    $this->db->where('ev.emp_id', $dataArray['emp_id']);
	    $this->db->where('ev.eve_from', $dataArray['eve_from']);
	    $this->db->where('ev.eve_to', $dataArray['eve_to']);
	    $this->db->where('ev.status','N');
	    $query = $this->db->get();
	
	    return $query->row_array();
	}

	public function detail($emp_id) {
		$this->db->select('e.id, e.emp_id, e.name, e.status, e.jdate, e.email, e.dept_code,
		                  e.present_address, e.mobile, e.phone, e.online, e.login_time,
		                  e.permanent_address, e.last_edu_achieve, e.resignation_date,
		                  e.dob,e.gender, e.blood_group, e.active, e.archive, e.image,
		                  dp.dept_name, ds.id as ds_id, ds.designation, g.grade, g.grade_id');
	  	$this->db->from('employee e');
	  	$this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
	  	$this->db->join('designations ds', 'ds.id=e.designation', 'left');
	  	$this->db->join('grade g', 'g.grade_id=e.grade_id', 'left');
		$this->db->where('e.emp_id',$emp_id);
		$query = $this->db->get();
		
		//return $query->row_array();
		return $query->row_array();
	}
	
	public function addEval($data){
	    $this->db->insert('evaluations', $data);
	     
	    return $this->db->insert_id();
	}
	
	public function updateEval($id, $data){
	    $this->db->where('id', $id);
	    $this->db->update('evaluations', $data);
	
	    return ($this->db->affected_rows() > 0);
	}
	
	public function getDeptCode($emp_id){
	    $this->db->select('dept_code');
	    $this->db->from('employee');
	    $this->db->where('emp_id', $emp_id);
	    $query = $this->db->get();
	
	    $res = $query->row_array();
	
	    return $res['dept_code'];
	}

	public function get_user_eval($emp_id, $cur_emp_id='') {
	    $this->db->select('id, emp_id, eve_from, eve_to, admin_id, status, emp_sig_date');
	    $this->db->from('evaluations');
	    $this->db->where('emp_id', $emp_id);
	    if ($cur_emp_id == $emp_id){
	        $this->db->where("status != 'N' AND status != ''");
	    }
	    $this->db->order_by('id','asc');
	
	    $query = $this->db->get();
	    return $query->result();
	}
}
?>