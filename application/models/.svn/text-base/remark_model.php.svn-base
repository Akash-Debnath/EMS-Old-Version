<?php
class Remark_model extends G_Model{
	public function __construct(){
	  $this->load->database(); 
	}
    
    public function notice_count() {
        $this->db->select('notice.id');
        $this->db->from('notice');
        $query = $this->db->get();
        
        return $query->num_rows();
    }
    
    public function get_notices($offset) {
        
        $this->db->select('n.id, n.subject, n.notice, n.notice_date, n.read_by');
        $this->db->from('notice n');
        $this->db->order_by('n.notice_date','desc');
        $this->db->limit(ROWS_PER_PAGE, $offset);
        $query = $this->db->get();
        
        //echo $this->db->last_query();
        
        return $query->result();
    }
    
    public function unreadNotice($id){
         
        $this->db->not_like('read_by', $id);
        $this->db->from('notice');
        return $this->db->count_all_results();
    }
    
    public function addReadBy($table, $id, $empID){
        $this->db->not_like('read_by', $empID);
        $this->db->from($table);
        $this->db->where('id', $id);
        
        if($this->db->count_all_results()){
            $this->db->set('read_by', "CONCAT(read_by,'".$empID."',',')", FALSE); 
            $this->db->where( 'id', $id);
            $this->db->update($table);
        }
        //echo $this->db->last_query();
    }
    public function notice_details($offset) {
    
        $this->db->select('n.id, n.subject, n.notice, n.notice_date, n.isEncrypted');
        $this->db->from('notice n');
        $this->db->order_by('n.notice_date','desc');
        $this->db->limit(1, $offset);
        $query = $this->db->get();
        
        //echo $this->db->last_query();
        
        
        $object = new stdClass();
        foreach ($query->result() as $obj){
            $object = $obj;
        }
        //print_r($object);
        return $object;
    }
    public function notice_by_id($id) {
    
        $this->db->select('id, subject, notice, notice_date, isEncrypted');
        $this->db->from('notice');
        $this->db->where('id', $id);
        $query = $this->db->get();

        $object = new stdClass();
        foreach ($query->result() as $obj){
            $object = $obj;
        }
        return $object;
    }
    
    public function addNotice($data){
    
        $this->db->insert('notice', $data);
        return $this->db->affected_rows();
        //return $this->db->insert_id();
    }
    
    public function add_policy($data){            
        $this->db->insert('policy', $data);
                 
        return $this->db->insert_id();
    }
    
    public function add_policy_file($data){
        $this->db->insert('policy_files', $data);
         
        return $this->db->insert_id();
    }
    
    public function add_job_desc_file($data){
        
        $this->db->insert('job_desc_files', $data);
        
        return $this->db->insert_id();
    }
    
    public function updateNotice($id, $data){
        $this->db->where('id', $id);
        $this->db->update('notice', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function delNotice($id){

        $this->db->where('id', $id);
        $this->db->delete('notice');
        
        return ($this->db->affected_rows() > 0);
    }
    
    public function del_policy($id){
        
        $this->db->delete('policy_files', array('policy_id' => $id));
    
        $this->db->delete('policy', array('policy_id' => $id));
   
        return ($this->db->affected_rows() > 0);
    }
    
    public function delete_job_file ($id){
        
        $this->db->delete('job_desc_files', array('emp_id' => $id));

        return ($this->db->affected_rows() > 0);
    }
    
    public function attachment_count($text, $eid) {

        $this->db->select('id');
        $this->db->from('attach_msg');
        $this->db->where_in('message_to', $text);
        $this->db->or_where("message_from", $eid);
        $this->db->or_where("custom_recipient LIKE '%".$eid."%' ");
        $query = $this->db->get();
    
        return $query->num_rows();
    }
    
    public function get_attachments($offset, $text, $eid) {

        $this->db->select('a.id, a.subject, a.message_date, a.read_by, a.message_from, a.message_to, e.name');
        $this->db->select('(select count(*) from attach_files as af where a.id=af.att_id) as count',FALSE);
        $this->db->from('attach_msg a');
        $this->db->join('employee e', 'e.emp_id = a.message_from', 'left');
        $this->db->where_in('message_to', $text);
        $this->db->or_where("message_from", $eid);
        $this->db->or_where("custom_recipient LIKE '%".$eid."%' ");
        $this->db->order_by('a.id','desc');
        $this->db->limit(ROWS_PER_PAGE, $offset);
        //$this->db->
        $query = $this->db->get();
    
        //echo $this->db->last_query();
    
        return $query->result();
    }
    
    public function attach_detail($offset, $text, $eid) {
    
        $this->db->select('a.id, a.subject, a.message, a.message_date, a.read_by, a.message_from, a.message_to, a.is_encrypted');
        $this->db->from('attach_msg a');
        $this->db->order_by('a.id','desc');
        $this->db->where_in('message_to', $text);
        $this->db->or_where("message_from", $eid);
        $this->db->or_where("custom_recipient LIKE '%".$eid."%' ");
        $this->db->limit(1, $offset);
        $query = $this->db->get();

        $object = new stdClass();
        foreach ($query->result() as $obj){
            $object = $obj;
        }
        return $object;
    }
    public function getAttFiles($id){
        $this->db->select('id, filename, original_name');
        $this->db->from('attach_files');
        $this->db->where('att_id', $id);
        $query = $this->db->get();
        
        return $query->result();
    }
    
    public function getAttFilesbyID($id){
        $this->db->select('*');
        $this->db->from('attach_files');
        $this->db->where('id', $id);
        $query = $this->db->get();

        $res = $query->result();
        return $res[0];
    }
    
    
    public function getFileNameBy_att_id($id){
        $this->db->select('filename');
        $this->db->from('attach_files');
        $this->db->where('att_id', $id);
        $query = $this->db->get();
    
        return $query->result();
    }
    public function getFileNameByID($id){
        $this->db->select('filename');
        $this->db->from('attach_files');
        $this->db->where('id', $id);
        $query = $this->db->get();
    
        $res = $query->result();
        return $res[0]->filename;
    }
    
    public function getMessage_TO($id) {
    
        $this->db->select('message_to');
        $this->db->from('attach_msg');
        $this->db->where('id', $id);
        $query = $this->db->get();
    
        $res = $query->result();
        
        return $res[0]->message_to;
    }
    
    public function getPolicy(){
        $this->db->select('*');
        $this->db->from('policy');
        $query = $this->db->get();
        
        
        $ary = array();
        $res = $query->result();
        foreach ($res as $obj){
            $ary[$obj->policy_id] = $obj;
        }
        
        return $ary;
    }
    public function getPolicyFile(){
        $this->db->select('*');
        $this->db->from('policy_files');
        $query = $this->db->get();
    
        return $query->result();
    }
    
    public function getPolicyFileById($id){
        
        $this->db->select('*');
        $this->db->from('policy_files');
        $this->db->where('id', $id);
        $query = $this->db->get();
    
        return $query->row();
    }
    
    public function getJobFiles(){
        $this->db->select('');
        $this->db->from('job_desc_files');
        $query = $this->db->get();
        $ary = array();
        $res = $query->result();
        foreach ($res as $obj){
            $ary[$obj->emp_id] = $obj;
        }
        
        return $ary;
    }
    
    
    
    public function attach_by_id($id) {
    
        $this->db->select('id, subject, message_date, message, message_to, custom_recipient, is_encrypted');
        $this->db->from('attach_msg');
        $this->db->where('id', $id);
        $query = $this->db->get();
    
        $object = new stdClass();
        foreach ($query->result() as $obj){
            $object = $obj;
        }
        return $object;
    }
    
    public function addAttach($data){
    
        $this->db->insert('attach_msg', $data);
         
        return $this->db->insert_id();
    }
    
    public function addToAttachFile($data){
    
        $this->db->insert('attach_files', $data);
        
        return $this->db->insert_id();
    }
    
    public function updateAttach($id, $data){
        $this->db->where('id', $id);
        $this->db->update('attach_msg', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function updateAttach_Files($id, $data){
        $this->db->where('id', $id);
        $this->db->update('attach_files', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function delAttachFile($id){
    
        $this->db->where('id', $id);
        $this->db->delete('attach_files');
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function delAttach($id){
    
        $this->db->where('id', $id);
        $this->db->delete('attach_msg');
        
        $isDelete1 = $this->db->affected_rows() > 0;
        
        $this->db->where('att_id', $id);
        $this->db->delete('attach_files');
        
        $isDelete2 = $this->db->affected_rows() > 0;
        
        return $isDelete1 || $isDelete2 ? true : false;
    }
    
    public function getLastname(){
        $this->db->select('filename');
        $this->db->order_by('id','desc');
        $this->db->limit(1);
        $query = $this->db->get('attach_files');     
         
        $res = $query->result();                
        $txt = $res[0]->filename;
        
        $temp = explode('.', $txt);
        $ext  = array_pop($temp);
        $name = implode('.', $temp);
        
        $num = mb_substr($name, 4,strlen($name));
        
        return (int)$num;
    }
    

    public function getMail(){
        
        $this->db->select('emp_id, name, email');
        $this->db->from('employee');
        $this->db->where(array('active'=>'U', 'archive'=>'N'));
        $query = $this->db->get();
        
        return $query->result();        
    }
    
    
    
    
}