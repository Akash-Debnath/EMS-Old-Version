<?php

class Settings_model extends G_Model {

    public function __construct()
    {
        $this->load->database();
    }

    public function department() {
        $query = $this->db->get('departments');
        
        return $query->result();
    }
    
    public function facility() {
        $query = $this->db->get('facility_options');
    
        return $query->result();
    }
    
    public function designation($dept_code = "")
    {
        $this->db->select('id, dept_code, designation');
        $this->db->from('designations');
        if (!empty($dept_code)) $this->db->where(array("dept_code" => $dept_code));
        $this->db->order_by('dept_code', 'asc');
        $query = $this->db->get();
        return $query->result();
    }
    /*
     * add a department
     */
    public function addDept($data){
        $this->db->insert('departments', $data);
        
        return $this->db->insert_id();
    }
    
    public function addFacility($data){
    
        $this->db->insert('facility_options', $data);         
        return $this->db->insert_id();
    }
    
    public function addHoliday($data){
        
        $datediff = strtotime($data['to']) - strtotime($data['from']);
        $datediff = floor($datediff/(60*60*24));
        $insert_ids =array();
        for($i = 0; $i < $datediff + 1; $i++){
            
            $date = date("Y-m-d", strtotime($data['from'] . ' + ' . $i . 'day'));  

            $this->db->insert('holy_day', array('date'=>$date, 'description' =>$data['description']) );
            
            $insert_ids[] = $this->db->insert_id();
        }
        
        return $insert_ids;        
    }
    
    public function addIncident($data){
    
        $datediff = strtotime($data['to']) - strtotime($data['from']);
        $datediff = floor($datediff/(60*60*24));
        $insert_ids =array();
        for($i = 0; $i < $datediff + 1; $i++){
    
            $date = date("Y-m-d", strtotime($data['from'] . ' + ' . $i . 'day'));
    
            $this->db->insert('incident', array('date'=>$date, 'description' =>$data['description']) );
    
            $insert_ids[] = $this->db->insert_id();
        }
    
        return $insert_ids;
    }
    
    public function addNote($data){
    
        $this->db->insert('tasks', $data);
        
        return $this->db->insert_id();
    }
    
    
    /*
     * add a designation
     */
    public function getDept($dept_name){
        $query = $this->db->get_where('departments', array('dept_name' => $dept_name));
        return $query->result();
    }
    
    public function updateDept($id, $data){
        //print_r($data);
        $this->db->where('id', $id);
        $this->db->update('departments', $data);
        
        return ($this->db->affected_rows() > 0);
    }
    
    public function getFacility($facility){
        $query = $this->db->get_where('facility_options', array('facility' => $facility));
        return $query->result();
    }
    
    public function getHolidays($year = ""){
        
        if(empty($year)) $year = date('Y');

        $this->db->select('*');
        $this->db->from('holy_day');
        $this->db->where('YEAR(date) =', $year);
        $this->db->order_by('date', "asc");
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function getIncident($year = ""){
        
        if(empty($year)) $year = date('Y');
        
        $this->db->select('*');
        $this->db->from('incident');
        $this->db->where('YEAR(date) =', $year);
        $this->db->order_by('date', "asc");
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function getNotes(){
    
        $query = $this->db->get('tasks');
        $res = $query->result();
        $arr = array();
        foreach ($res as $obj){
            $arr[$obj->id] = $obj;
        }
        return $arr;
    }
    
    public function getUserPass($id, $password){
         
        $this->db->select('pass');
        $this->db->from('employee');
        $this->db->where('archive','N');
        $this->db->where("emp_id", $id);
        $this->db->where("pass = OLD_PASSWORD('$password')");
        $query = $this->db->get();

		$numRows = $query->num_rows();
		
		if($numRows == 1) {
			return true;
		}else{
		    return false;
		}
    }
    
    public function updatePassword($id, $newPassword){
        
//         $this->db->set("pass","OLD_PASSWORD($newPassword)');
//         $this->db->where('emp_id', $id);
//         $this->db->update('employee');
            
        $sql = "UPDATE employee SET pass=OLD_PASSWORD('".$newPassword."') WHERE emp_id='".$id."'";
        $this->db->query($sql);
        
        
        return ($this->db->affected_rows() > 0);
    }
    
    public function updateFacility($id, $data){
        $this->db->where('facility_id', $id);
        $this->db->update('facility_options', $data);
        
        return ($this->db->affected_rows() > 0);
    }
    
    public function updateHoliday($id, $data){
        
        $this->db->delete('holy_day', array('id' => $id));
        
        $this->addHoliday($data);
        
//         $this->db->where('id', $id);
//         $this->db->update('holy_day', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function updateIncident($id, $data){
    
        $this->db->delete('incident', array('id' => $id));
    
        $this->addIncident($data);
    
        //         $this->db->where('id', $id);
        //         $this->db->update('holy_day', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function updateNote($id, $data){

        $this->db->where('id', $id);
        $this->db->update('tasks', $data);
        
        return ($this->db->affected_rows() > 0);
    }
    
    public function deleteFacility($id){
        
        $this->db->delete('facility_options', array('facility_id' => $id)); 
        unset($id);
        
        return ($this->db->affected_rows() > 0);
    }
    
    public function del_holiday($id){
    
        $this->db->delete('holy_day', array('id' => $id));
        unset($id);
            
        return ($this->db->affected_rows() > 0);
    }
    
    public function del_incident($id){
    
        $this->db->delete('incident', array('id' => $id));
        unset($id);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function del_note($id){
    
        $this->db->delete('tasks', array('id' => $id));
        unset($id);
    
        return ($this->db->affected_rows() > 0);
    }
    
    
    public function addDes($data){
        
        $this->db->insert('designations', $data);
        
        return $this->db->insert_id();
    }
    
    
    public function updateDes($id, $data){
        
        $this->db->where('id', $id);
        $this->db->update('designations', $data);
        
        return ($this->db->affected_rows() > 0);
    }
    
    public function getPriv(){
        
        $data = array(
            'settings.emp_id',
            'employee.name', 
            'designations.designation', 
            'departments.dept_name',
            'settings.type',
            'settings.dept_code'
        );
        $this->db->select($data);
        $this->db->from('settings');
        $this->db->join('employee','settings.emp_id = employee.emp_id','inner');
        $this->db->where('employee.archive','N');
        $this->db->join('designations', 'employee.designation = designations.id','inner');
        $this->db->join('departments', 'employee.dept_code = departments.dept_code','inner'); 
        $this->db->order_by('settings.emp_id', "asc");
        $query = $this->db->get();
        
        return $query->result();
    }
    public function addPriv($data){
        $query = $this->db->get_where('settings', $data);
        $flag =false;
        $count = $query->num_rows();
        if ($count === 0) {
            $flag = $this->db->insert('settings', $data);
        }
        
        return $flag;
    }
    
    public function delPriv($data){
        $this->db->delete('settings', $data);
        return ($this->db->affected_rows() > 0);
    }
    
    public function getEmployee(){
        
        $data = array(
            'employee.emp_id',
            'employee.name',
	    'employee.dept_code',
            'departments.dept_name',
        );
        $condition = array(
            'employee.active' => 'U',
            'employee.archive' => 'N',
            'departments.active' => 'Y'
        );
        $this->db->select($data);
        $this->db->from('employee');
        $this->db->join('departments','employee.dept_code = departments.dept_code','inner');
        $this->db->where($condition);
        $this->db->order_by('employee.dept_code', "asc");
        $query = $this->db->get();
        
        return $query->result();
    }

    public function delete() {
        $this->db->delete($this::DB_TABLE, array(
            $this::DB_TABLE_PK => $this->{$this::DB_TABLE_PK},
        ));
        unset($this->{$this::DB_TABLE_PK});
    }
    
    public function get_staff() {
    
        $this->db->select('e.emp_id, e.name,e.scheduled_attendance, e.roster, dp.dept_name,');
        $this->db->from('employee e');
        $this->db->join('departments dp', 'dp.dept_code=e.dept_code', 'left');
        $this->db->where('e.archive','N');        
        $this->db->order_by('e.emp_id','asc');

    
        $query = $this->db->get();
        return $query->result();
    }
    
    public function updateAttendanceType($id, $data){
        //print_r($data);
        $this->db->where('emp_id', $id);
        $this->db->update('employee', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function getWeekend(){
    
        $query = $this->db->get('weekly_leave');
        
        $result = $query->result();
        
        $ary =array();
        foreach ($result as $obj){
            $ary[$obj->emp_id] = $obj;
        }
        return $ary;
    }
    
    public function update_weekly_leave($id, $data){
        $query = $this->db->get_where('weekly_leave', array('emp_id'=>$id));
        
        $result = $query->row();        
        if(count($result) == 0){
            
            $data['emp_id'] = $id;
            $this->db->insert('weekly_leave', $data);
            
            return ($this->db->affected_rows() > 0);
            
        }else{

            $this->db->where('emp_id', $id);
            $this->db->update('weekly_leave', $data);
            
            return ($this->db->affected_rows() > 0);
        }
        
    }
    
    public function getPermissionPrivilegeType ( $permissionGroups ){
    
        $this->db->select('*');
        $this->db->from('activity_priv');
        $group_ids = array_keys($permissionGroups);
        $this->db->where_in("group_id", $group_ids);
        $query = $this->db->get();
        $res = $query->result();
        
        //echo $this->db->last_query();die;
    
        $ret =array();
        foreach ($res as $obj){
    
            $ret[$obj->group_id][] = $obj;
            $ret['all'][] = $obj;
        }
    
        return $ret;
    }
    
    public function addPermissionPriv($data, $staff_ids){
    
        $this->db->delete('activity_permission', $data);
    
        foreach ($staff_ids as $id){
    
            $data["staff_id"] = $id;
    
            $this->db->insert('activity_permission', $data);
        }
    
        return ($this->db->affected_rows() > 0);
    }
    
}
?>