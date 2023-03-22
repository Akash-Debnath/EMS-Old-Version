<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ramadan_model extends G_Model {
    
	public function __construct()	{
	  $this->load->database(); 
    }

    public function set_ramadan($stime,$etime)
	{
		$data=array();
		$data['stime']=$stime;
		$data['etime']=$etime;
		// $data['description']='Ramadan Time';
		$this->db->insert('ramadan',$data);
		// return $data;
	}
	public function check_ramadan($date)
	{
		// return $date;
		$this->db->select('id');
		$this->db->from('ramadan');
		$this->db->where('stime <=', $date);
		$this->db->where('etime >=', $date);
		$query = $this->db->get();
		return $query->result();
	}
	public function get_ramadan()
	{
		$this->db->select('*');
		$this->db->from('ramadan');
		$query = $this->db->get();
		return $query->result_array();
	}
	public function get_all_ramadan($array=array())
	{
		$this->db->select('*');
		$this->db->from('ramadan');
		if(isset($array["offset"])) {
	            
			$this->db->limit(ROWS_PER_PAGE, $array["offset"]);
		}
		$query = $this->db->get();
		return $query->result_array();
	}
	public function delete_ramadan($id)
	{
		$this->db->delete('ramadan', array('id' => $id));
	}
	public function edit_ramadan($id,$stime,$etime)
	{
		// var_dump($etime);
		// die();
		$data=array(
			'stime' => $stime,
			'etime' => $etime
		);
		$this->db->where('id',$id);
		$this->db->update('ramadan',$data);
	}
}
