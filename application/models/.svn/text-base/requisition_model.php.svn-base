<?php
class Requisition_model extends G_Model {

    public function __construct()
    {
        $this->load->database();
    }
    
    public function getCategories($select) {
        $this->db->select($select);
        $query = $this->db->get('store_categories');
    
        return $query->result();
    }
    
    public function getItems($select) {
        $this->db->select($select);
        $this->db->from('store_items i');
        $this->db->join('store_categories c','i.category_id = c.category_id','inner');
    
        $query = $this->db->get();
        return $query->result();
    }
    
    public function getItemLedger() {
        $this->db->select('item_id, item, category_id');
        $this->db->from('store_items');
    
        $query = $this->db->get();
        $res = $query->result();
        
        $ary = array();
        
        foreach ($res as $obj){
            
            $ary[$obj->category_id][$obj->item_id] = $obj->item;
            $ary['all'][$obj->item_id] = $obj->item;
        }
        
        return $ary;
    }
    
    public function addCategory($data){
        
        $this->db->insert('store_categories', $data);
        
        return $this->db->insert_id();
    }
    
    public function updateCategory($id, $data){
        $this->db->where('category_id', $id);
        $this->db->update('store_categories', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    public function delCategory($id){
    
        $this->db->delete('store_categories', array('category_id' => $id));
        unset($id);

        return ($this->db->affected_rows() > 0);
    }
    
    

    
    public function addItem($data){
    
        $this->db->insert('store_items', $data);
    
        return $this->db->insert_id();
    }
    
    public function updateItem($id, $data){
        $this->db->where('item_id', $id);
        $this->db->update('store_items', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    

    public function delItem($id){
    
        $this->db->delete('store_items', array('item_id' => $id));
        unset($id);
    
        return ($this->db->affected_rows() > 0);
    }
    
    
    public function addVoucher($table='', $data){
    
        $this->db->insert($table, $data);
    
        return $this->db->insert_id();
    }
    
    public function updateVoucher($data, $vid){
        
        $this->db->where('voucher_id', $vid);
        $this->db->update('store_voucher', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function updateReqVoucher($data, $vid){
        $this->db->where('voucher_id', $vid);
        $this->db->update('store_req_voucher', $data);
    
        return ($this->db->affected_rows() > 0);
    }
    
    public function addLedger($objects,$voucher_id){
        $insert_keys = array();
        $flag = true;
        $data['voucher_id'] = $voucher_id;
        
        foreach ($objects as $obj){
            $data['item_id']= $obj->item_id;
            $data['quantity']= $obj->quantity;
            $data['unit_price']= $obj->unit_price;
            $data['total_price']= $obj->total_price;
            
            $this->db->insert('store_ledger', $data);
            
            $insert_keys[] = $this->db->insert_id();
            $flag = $flag && ($this->db->affected_rows() > 0);
        }
        
        $ary['insert_keys'] = $insert_keys;
        $ary['flag'] = $flag;
        
        return $ary;
    }
    
    public function addReqLedger($objects,$voucher_id){
        $insert_keys = array();
        $flag = true;
        $data['voucher_id'] = $voucher_id;
    
        foreach ($objects as $obj){
            $data['item_id']= $obj->item_id;
            $data['quantity']= $obj->quantity;
            $data['remark']= $obj->remark;
    
            $this->db->insert('store_req_ledger', $data);
    
            $insert_keys[] = $this->db->insert_id();
            $flag = $flag && ($this->db->affected_rows() > 0);
        }
    
        $ary['insert_keys'] = $insert_keys;
        $ary['flag'] = $flag;
    
        return $ary;
    }
    
    public function getVouchers() {
        
        //select v.*, sum(l.total_price) total from store_voucher v left join store_ledger l on v.voucher_id = l.voucher_id group by v.voucher_id;
        
        $this->db->select('v.*, e.name approver_name, f.name verifier_name, SUM(l.total_price) as total');
        $this->db->from('store_voucher v');
        $this->db->join('store_ledger l', 'v.voucher_id = l.voucher_id', 'left');
        $this->db->join('employee e','v.approved_by = e.emp_id','left');
        $this->db->join('employee f','v.verified_by = f.emp_id','left');
        $this->db->order_by('v.voucher_id', 'desc');
        $this->db->group_by('v.voucher_id');
        
        
        $query = $this->db->get();
        return $query->result();
    }
    
    
    public function getReqVouchers($dept_codes, $staffs){
        
        $this->db->select('s.*, a.name approver_name, v.name verifier_name');
        $this->db->from('store_req_voucher s');
        
        $this->db->join('employee r', 's.requested_by = r.emp_id', 'left');
        $this->db->join('employee a', 's.approved_by = a.emp_id', 'left');
        $this->db->join('employee v', 's.verified_by = v.emp_id', 'left');
        
        if(!empty($staffs)){
            $this->db->where_in('s.requested_by', $staffs);
        }
        if(!empty($dept_codes)){
            $this->db->where_in('r.dept_code', $dept_codes);
        }
        $this->db->order_by('s.voucher_id', 'desc');

        $query = $this->db->get();
        return $query->result();
    }
    
    public function getVoucherInfo($vid) {
    
    
        $this->db->select('*');
        $this->db->from('store_voucher');
        $this->db->where('voucher_id', $vid);
            
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function getReqVoucherInfo($vid) {
    
    
        $this->db->select('*');
        $this->db->from('store_req_voucher');
        $this->db->where('voucher_id', $vid);
    
        $query = $this->db->get();
        return $query->row_array();
    }
    
    
    public function getLedgers($vid) {
    
        $this->db->select('l.*, i.item');
        $this->db->from('store_ledger l');
        $this->db->where('l.voucher_id', $vid);
        $this->db->join('store_items i','l.item_id = i.item_id', 'inner');
        //$this->db->join('store_voucher v','l.voucher_id = v.voucher_id', 'inner');
    
        $query = $this->db->get();
        
        $ary = array();
        $res = $query->result();
        $ary['objects'] = $res;
        $total = 0;
        foreach ($res as $obj){
            $total += $obj->total_price;
        }
        $ary['total'] = $total;
        
        return $ary;
    }
    
    public function getReqLedgers($vid) {
    
        $this->db->select('l.*, i.item');
        $this->db->from('store_req_ledger l');
        $this->db->where('l.voucher_id', $vid);
        $this->db->join('store_items i','l.item_id = i.item_id', 'inner');
        //$this->db->join('store_voucher v','l.voucher_id = v.voucher_id', 'inner');
        $query = $this->db->get();
    
        return $query->result();
    }   
    
    public function deleteVoucher($vid){
        
        $this->db->where('voucher_id', $vid);
        $this->db->delete('store_voucher');
        
        $f1 = ($this->db->affected_rows() > 0);
        
        $this->db->where('voucher_id', $vid);
        $this->db->delete('store_ledger');
        
        return ($f1 && ($this->db->affected_rows() > 0));
    }
    
    public function deleteReqVoucher($vid){
    
        $this->db->where('voucher_id', $vid);
        $this->db->delete('store_req_voucher');
    
        $f1 = ($this->db->affected_rows() > 0);
    
        $this->db->where('voucher_id', $vid);
        $this->db->delete('store_req_ledger');
    
        return ($f1 && ($this->db->affected_rows() > 0));
    }

    
    public function getPrivilege($code = ""){
        
        $this->db->select('s.emp_id');
        $this->db->from('activity_priv p');       
        $this->db->where('p.activity_code', $code);
        $this->db->join('activity_stack s', 's.activity_id = p.activity_id', 'left');
        $query = $this->db->get();
        
        $res = $query->result();
        
        $ret = array();
        foreach ($res as $obj){
            $ret[] = $obj->emp_id;            
        }

        return $ret;
        
    }
    
    public function getFullPrivilege($code = ""){
    
        $this->db->select('s.emp_id, s.dept_priv');
        $this->db->from('activity_priv p');
        $this->db->where('p.activity_code', $code);
        $this->db->join('activity_stack s', 's.activity_id = p.activity_id', 'left');
        $query = $this->db->get();
    
        $res = $query->result();
    
        $ret = array();
        foreach ($res as $obj){
            
            $newAry = array();
            $dept_privs = explode(',', $obj->dept_priv);
            
            foreach ($dept_privs as $val){
                
                if (!empty($val)){
                    $newAry[] = trim($val);
                }
                
            }
    
            $ret[$obj->emp_id] = $newAry;
        }
    
        return $ret;    
    }
    
    
}    