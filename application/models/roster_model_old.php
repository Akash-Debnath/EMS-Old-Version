<?php
class Roster_model extends G_Model{
	public function __construct(){
	  $this->load->database(); 
	}
	
	public function getRosterSlot($dept_code) 
	{
		$this->db->select('*');
		$this->db->from('roster_slot');
		$this->db->where('dept_code',$dept_code);
		$this->db->order_by('slot_no','asc');
		
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getRosterRecord($dept_code, $sdate)
	{
		$this->db->select('e.emp_id, e.name, r.id, r.stime, r.etime');
		$this->db->from('employee e, rostering r');
		$this->db->where('`e`.`emp_id`=`r`.`emp_id`');
		$this->db->where('`e`.`dept_code`',$dept_code);
		$this->db->where('LEFT(`r`.`stime`,10) >=',$sdate);
		
		$recordSet = $this->db->get();
		
		return $recordSet->result();
		
	}
	
	public function GetStaffListByDetpCode($dept_code) {
		
		$this->db->select('emp_id,name');
		$this->db->from('employee');
		$this->db->where('archive !=','Y');
		$this->db->where('dept_code',$dept_code);
		$this->db->order_by('emp_id','asc');
	
		$query = $this->db->get();
		return $query->result();
	}
	
	public function addRosterSlot($data){
	    
	    $this->db->insert('roster_slot', $data);
	    return  $this->db->insert_id();
	}
	
	public function updateRosterSlot($id, $data){
	
	    $this->db->where('id', $id);
	    $this->db->update('roster_slot', $data);
	     
	    return ($this->db->affected_rows() > 0);
	}
	
	public function del_roster_slot($id){
	    $this->db->where('id', $id);
	    $this->db->delete('roster_slot');
	
	    return ($this->db->affected_rows() > 0);
	}
}