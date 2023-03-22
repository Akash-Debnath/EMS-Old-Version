<?php
class Ramadan extends G_Controller {
	
    public $adminFlag = false;
	public $data = array();
	public $myEmpId = '';
	public function __construct() {
	    
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		// $this->load->library('session');
		$this->load->model('user_model');
		$this->load->model('ramadan_model');
		$this->load->library('pagination');
		$this->load->library('form_validation');
		$this->load->helper('ems_helper');
		
		$this->data["myInfo"] = $this->session->GetMyBriefInfo();
		$this->data['departments'] = $this->user_model->department();
		$this->data["uType"] = $this->session->GetUserType();

        $this->myEmpId = $this->session->GetLoginId();
        $this->data['isManagement'] = $this->session->IsManagement($this->myEmpId);
        $this->data['isAdmin'] = $this->session->IsAdmin($this->myEmpId);
        $this->data['isManager'] = $this->session->IsManager($this->myEmpId);
		
		$this->data["controller"] = $this;
		$this->data["menu"] = "employee";
		
		if(!$this->data['isAdmin']) {
		    $this->data["status_array"] = $this->status_array;
		    $this->data["title"] = "ABC";
		    $this->data["sub_title"] = "ABC";
		    $this->data["message"] = "You have no privilege to access this page!";
		} 
    }
    public function ramadan_set()
	{
		$data=$this->input->post();
		$stime = convertDate($data['stime'],"Y-m-d");
		$etime = convertDate($data['etime'],"Y-m-d");
		$get_ramadan_date=$this->ramadan_model->get_ramadan();
		$days = dateDiff($stime,$etime);
		$isRamadanDateExists=$this->isDateExist($stime,$etime,$get_ramadan_date);

		if ($stime<$etime && $days==30 && $isRamadanDateExists == false) {
			$this->ramadan_model->set_ramadan($stime,$etime);
		}else{
			echo "invalid ".$days." ".$isRamadanDateExists;
		}	
	}
	public function isDateExist($stime,$etime,$get_ramadan_date=array())
	{
		$isDateExists = array_map(function($date) use ($stime,$etime) {
			if ($stime >= $date['stime'] && $stime <= $date['etime'] || $etime >= $date['stime'] && $etime <= $date['etime']) {
				return true;
			}else{
				return false;
			}
		}, $get_ramadan_date);
		$isRamadanDateExists = in_array(true,$isDateExists);
		return $isRamadanDateExists;
	}
	
	public function edit_ramadan()
	{    
	    $this->isLoggedIn();
	
	    if( !($this->data['isAdmin'] || $this->data['isManagement']) ) {
	        $this->load->view('not_found', $this->data);
	        return;
		}
		if($this->uri->segment(4)){
			$page = $this->uri->segment(4);
		} else{
			$page = 1;
		}
		$req_dept = $this->uri->segment(3) ? $this->uri->segment(3) : "ramadan";
		$this->data["base_url"] = base_url() . "ramadan/edit_ramadan/$req_dept/";
		$total_row = count($this->ramadan_model->get_ramadan());
		$this->data["offset"] = ($page - 1) * ROWS_PER_PAGE;
		$this->pagination->initialize(paginationAttribute($this->data,$total_row,$page,$req_dept));
		$array=array('offset'=>$this->data["offset"]);
		
		if(empty($emp_id)) $emp_id = $this->myEmpId;		
		$user = $this->user_model->detail($emp_id);		

		$ramadan_date_check=$this->ramadan_model->check_ramadan(date("Y-m-d"));
		$get_ramadan_date=$this->ramadan_model->get_all_ramadan($array);
		$this->data["ramadan"] = $ramadan_date_check;
        $this->data["get_ramadan_date"] = $get_ramadan_date;
		
		$str_links = $this->pagination->create_links();
		$this->data["links"] = explode('&nbsp;',$str_links );
		


		$this->data['emp_id'] = $user['emp_id'];
		$this->data['dept_code'] = $user['dept_code'];	
		$this->data["title"] = $this->data['dept_code'];
		if($this->myEmpId == $this->data['emp_id']){

		    $this->data['isPanelMenuOpen'] = false;
		}
		$this->data["sub_title"] = "Edit Ramadan Date";
		$this->data["notSelect"] = true;
		
		$this->load->view('edit_ramadan', $this->data);
	}
	public function edit_ramadan_date()
	{
		$data=$this->input->post();
		$id = $data['id'];
		$stime=convertDate($data['stime'],"Y-m-d");
		$etime=convertDate($data['etime'],"Y-m-d");


		$get_ramadan_date=$this->ramadan_model->get_ramadan();

		$days = dateDiff($stime,$etime);

		if($stime<$etime)
		{
			if ($days>=29 && $days<=30) {
				$this->ramadan_model->edit_ramadan($id,$stime,$etime);

			}else{
				echo "lessTime";
			}
			
		}else{
			echo "stimeLarger";
		}
	}
	public function deleteRamadan()
	{
		$id = $this->input->post('deletedId');
		$this->ramadan_model->delete_ramadan($id);
		redirect(base_url().'ramadan/edit_ramadan');
	}
	
}
?>