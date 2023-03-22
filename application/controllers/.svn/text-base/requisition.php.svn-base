<?php
class Requisition extends G_Controller {

    public $adminFlag = false;
    public $data = array();
    public $myEmpId = '';
    
    public $purchase_creator_access = FALSE;
    public $purchase_approver_access = FALSE;
    public $purchase_verifier_access = FALSE;
    
    
    
    public function __construct() {
        parent::__construct();
        $this->load->library('session');

        $this->isLoggedIn();

        $this->load->model('user_model');
        $this->load->model('requisition_model');
        $this->load->library('pagination');
        $this->load->library('mailer');
        $this->data["myInfo"] = $this->session->GetMyBriefInfo();

        $this->data['departments'] = $this->user_model->department();
        $this->data["menu"] = "Store";
        $this->data["uType"] = $this->session->GetUserType();
        
        $this->myEmpId = $this->session->GetLoginId();
        $this->data['isManagement'] = $this->session->IsManagement($this->myEmpId);
        $this->data['isAdmin'] = $this->session->IsAdmin($this->myEmpId);
        $this->data['isManager'] = $this->session->IsManager($this->myEmpId);
        
        if(!$this->purchase_access) {

            $this->data["title"] = "ABC";
            $this->data["sub_title"] = "ABC";
            $this->data["message"] = "You have no privilege to access this page!";
        }
        $this->data["web_url"] = $this->web_url;
        $this->data["controller"] = $this;
        
        if( in_array($this->myEmpId, $this->purchase_creators)){ $this->purchase_creator_access = true; }
        if(in_array($this->myEmpId, $this->purchase_approvers)){ $this->purchase_approver_access = true; }
        if(in_array($this->myEmpId, $this->purchase_verifiers)){ $this->purchase_verifier_access = true; }
        
    }
    
    public function category(){
        
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
        
        $this->data["title"] = "Category";
        $this->data["sub_title"] = "Category List";
        
        $select = array('*');
        $this->data['categories'] = $this->requisition_model->getCategories($select);
        
        $this->view('requisition_category',$this->data);
        //$this->load->view('delete_confirm');
    }
    
    public function add_category() {
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
         
         
        $data['category'] = isset($_POST['category_name']) ? $_POST['category_name']: "";
        $data['description'] = isset($_POST['description']) ? $_POST['description']: "";
         
        $insert_id = $this->requisition_model->addCategory($data);
    
        if($insert_id){
            redirect(base_url().'requisition/category');
        }else{
            $return['message'] = $this->message['insert_f'];
            
            $this->load->view('not_found', $return);
        }
         
    }
    
    public function update_category($id) {
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
    
        $data['category'] = isset($_POST['category_name']) ? $_POST['category_name']: "";
        $data['description'] = isset($_POST['description']) ? $_POST['description']: "";
    
        $flag =$this->requisition_model->updateCategory($id, $data);
         
        if($flag){
    
            redirect(base_url().'requisition/category');
             
        }else{
            $return['message'] = $this->message['update_f'];
            $this->load->view('not_found', $return);
        }
    }
    
    public function del_category(){
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
        $flag = false; 
        $id = isset($_POST['del_category_id']) ? $_POST['del_category_id']: "";
        
        if(!empty($id))            
            $flag = $this->requisition_model->delCategory($id);
    
        if($flag){
            
            redirect(base_url().'requisition/category');
        }else{
           $return['message'] = $this->message['update_f'];
            $this->load->view('not_found', $return);
        }
    
    }
    
    public function item(){
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
                
        
        $sel1 = array('i.*', 'c.category');
        $this->data['items'] = $this->requisition_model->getItems($sel1);
        $sel2 = array('category_id', 'category');
        $this->data['categories'] = $this->requisition_model->getCategories($sel2);
        
        $this->data["title"] = "Item";
        $this->data["sub_title"] = "Item List";
        $this->view('requisition_item',$this->data);
        //$this->load->view('delete_confirm');
    }
    
    public function add_item() {
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
         
         
        $data['item'] = isset($_POST['item_name']) ? $_POST['item_name']: "";
        $data['category_id'] = isset($_POST['categorySelect']) ? $_POST['categorySelect'] : "";
        $data['description'] = isset($_POST['description']) ? $_POST['description']: "";        
         
        $insert_id = $this->requisition_model->addItem($data);
    
        if($insert_id){
            redirect(base_url().'requisition/item');
        }else{
            $return['message'] = $this->message['insert_f'];
    
            $this->load->view('not_found', $return);
        }
         
    }
    
    public function update_item($id) {
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
    
        $data['item'] = isset($_POST['item_name']) ? $_POST['item_name']: "";
        $data['category_id'] = isset($_POST['categorySelect']) ? $_POST['categorySelect'] : "";
        $data['description'] = isset($_POST['description']) ? $_POST['description']: "";
        
        $flag =$this->requisition_model->updateItem($id, $data);
         
        if($flag){
    
            redirect(base_url().'requisition/item');
             
        }else{
            $return['message'] = $this->message['update_f'];
            $this->load->view('not_found', $return);
        }
    }
    
    public function del_item(){
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
        $flag = false;
        $id = isset($_POST['del_item_id']) ? $_POST['del_item_id']: "";
    
        if(!empty($id))
            $flag = $this->requisition_model->delItem($id);
    
        if($flag){
    
            redirect(base_url().'requisition/item');
        }else{
            $return['message'] = $this->message['update_f'];
            $this->load->view('not_found', $return);
        }
    
    }
    
    public function purchase(){
        
        if(!$this->purchase_access) {
            
            $this->load->view('not_found', $this->data);
            return;
        }
    
        //$this->data["adminFlag"] = $this->data['isAdmin'];
        //$this->data['items'] = $this->requisition_model->getItems();
        //$this->data['categories'] = $this->requisition_model->getCategories();
    
        $this->data['vouchers'] = $this->requisition_model->getVouchers();
        
        //print_r($this->data['vouchers']);
        
        $this->data["title"] = "purchase";
        $this->data["sub_title"] = "Voucher";
        $this->view('requisition_purchase',$this->data);    
    }
    
    
    public function voucher_form($vid=""){
        
        if(!$this->purchase_access) {
        
            $this->load->view('not_found', $this->data);
            return;
        }
        
        $this->data["title"] = "Purchase";
        $this->data["sub_title"] = "Purchase Form";

        if(!empty($vid)){
            
            $ary = $this->requisition_model->getLedgers($vid);
            $this->data["ledgers"] = $ary['objects'];
            $this->data["total"] = $ary['total'];
            $this->data["voucher"] = $this->requisition_model->getVoucherInfo($vid);
            
            if(empty( $this->data["voucher"])){
                $this->data['message'] = "this voucher Can't be found!";
                $this->load->view('not_found', $this->data);
                return;
            }
            
            $manager = $this->session->getManagersByDeptCode("AD");
            $this->data["isManagerOfAdmin"] = in_array($this->myEmpId, $manager);
            $this->data["isManagement"] = $this->session->IsManagement($this->myEmpId);

            $this->view('requisition_vform_id',$this->data);
            
        }else{
        
            if(!$this->purchase_creator_access) {

                $this->data["title"] = "ABC";
                $this->data["sub_title"] = "ABC";
                $this->data["message"] = "You have no privilege to access this page!";
                $this->load->view('not_found', $this->data);
                return;
            }
                        
            $this->data['items'] = $this->requisition_model->getItemLedger();
            $sel2 = array('category_id', 'category');
            $this->data['categories'] = $this->requisition_model->getCategories($sel2);
            $this->view('requisition_voucher_form',$this->data);
        }
        
    }
    
    public function add_purchase() {
        
        if(!$this->purchase_creator_access) {

            $this->data["title"] = "ABC";
            $this->data["sub_title"] = "ABC";
            $this->data["message"] = "You have no privilege to access this page!";
            $this->load->view('not_found', $this->data);
            return;
        }
         
        $dataObj = isset($_POST['dataObj']) ? $_POST['dataObj']: array();
        $objects = json_decode($dataObj);

        $data['date'] = date('Y-m-d');
        $data['requested_by'] = $this->myEmpId;
         
        $voucher_id = $this->requisition_model->addVoucher('store_voucher', $data);
        
        $ary = $this->requisition_model->addLedger($objects, $voucher_id);
        
        //print_r($insert_keys);

        if($ary['flag']){
            $return['vid'] = $voucher_id;
	        $return['msg'] = $this->message['insert_s'];
	        $return['status'] = true;
	        
	        //sent mail to manager of Admin
	        $receiver = array();
	        $managers = $this->session->getManagersByDeptCode("AD");
	        foreach ($managers as $man_id){
	            $receiver[] = $this->user_model->getBriefInfo($man_id);
	        }
	        
	        $sender = array();
	        $sender['name'] = $this->data["myInfo"]->userName;
	        $sender['email'] = $this->data["myInfo"]->email;
	        $subject = "Purchase Request by $sender[name]";
	        
	        
	        $designation = $this->data['myInfo']->userDesignation;
	        $dept = $this->data['myInfo']->userDepartment;
	        $time = date('h:i:s A');
	        $day = date('l');
	        $emailBody = "<tr>
                    <td width='50%' valign='top' align='left'>
                    <table cellpadding='3' cellspacing='0'>
                    <tr><td>" . $this->myEmpId . "</td></tr>
                    <tr><td><b><a href='" . $this->web_url . "user/detail/" . $this->data["myInfo"]->userId . "'>$sender[name]</a></b></td></tr>
                    <tr><td><i>$designation</i></td></tr>
                    <tr><td>$dept</td></tr>
                    </table>
                    </td>
                    <td width='50%' valign='center' align='right'>
                    <a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 10px 30px; border: 1px solid #333333;' href='" . $this->web_url . "requisition/voucher_form/$voucher_id'>Show The Purchase Request</a>
                    </td>
                    </tr>
                    <tr height='40'><td colspan='2'>has sent you a purchase request and waiting for you approval.</td></tr>";
	        $mail = array();
	        $mail['subject'] = $subject;
	        $mail['body'] = $emailBody;
	        $mail['receiver'] = $receiver;
	        $mail['sender'] = $sender;
	        $mail['web_url'] = $this->web_url;
	        
	        if ($this->mailer->sendEmail($mail)) {
	            $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
	        } else {
	            $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
	        }
	         
	    }else{
	        $return['msg'] = $this->message['insert_f'];
	        $return['status'] = false;
	    }
	     
	    echo  json_encode($return);
         
    }
    
    public function approve_purchase($vid=""){
        
        $voucher = $this->requisition_model->getVoucherInfo($vid);
        $requesterDeptCode = $this->user_model->getEmpDeptCodeById($voucher['requested_by']);
        
        /* get Privilege  */
        $purchase_approvers= $this->requisition_model->getFullPrivilege(PURCHASE_APPROVE);

        if( !isset($purchase_approvers[$this->myEmpId]) ){
            
            $return['status'] = false;            
            $return['msg'] = $this->message['no_priv'];
            
            echo json_encode($return);            
            return;
            
        }else if( !empty($purchase_approvers[$this->myEmpId]) ){
                                    
            $dept_privs = $purchase_approvers[$this->myEmpId];                        
            
            if( !in_array($requesterDeptCode, $dept_privs )){
                
                $return['status'] = false;            
                $return['msg'] = $this->message['no_priv'];
                
                echo json_encode($return);            
                return;         
            }             
        }
        
        
        $data['approved_by'] = $this->myEmpId;        
        $flag = $this->requisition_model->updateVoucher($data, $vid);

        if($flag){
            
            $return['status'] = true;
            $return['msg'] = $this->message['update_s'];
            $return['vid'] = $vid;
            
            
            //send mail to Verifier & requester
            $purchase_verifiers = $this->requisition_model->getFullPrivilege(PURCHASE_VERIFY);
            $verifiers = $this->getPrivilegersByDeptCode($requesterDeptCode, $purchase_verifiers); 
                        
            $receiver = array();
            $receiver = $this->user_model->getMailInfoByIds($verifiers);
            $receiver[] = $this->user_model->getBriefInfo($voucher['requested_by']);
            
            $sender = array();
            $sender['name'] = $this->data["myInfo"]->userName;
            $sender['email'] = $this->data["myInfo"]->email;
            $subject = "Purchase Request Approval";
             
             
            $designation = $this->data['myInfo']->userDesignation;
            $dept = $this->data['myInfo']->userDepartment;
            $time = date('h:i:s A');
            $day = date('l');
            $emailBody = "<tr>
                    <td width='50%' valign='top' align='left'>
                    <table cellpadding='3' cellspacing='0'>
                    <tr><td>" . $this->myEmpId . "</td></tr>
                    <tr><td><b><a href='" . $this->web_url . "user/detail/" . $this->data["myInfo"]->userId . "'>$sender[name]</a></b></td></tr>
                                <tr><td><i>$designation</i></td></tr>
                                <tr><td>$dept</td></tr>
                                </table>
                                </td>
                                <td width='50%' valign='center' align='right'>
                                <a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 10px 30px; border: 1px solid #333333;' href='" . $this->web_url . "requisition/voucher_form/$vid'>Show The Purchase Request</a>
                                </td>
                                </tr>
                                <tr height='40'><td colspan='2'>has approved the purchase request and waiting for management verification.</td></tr>";
            
            $mail = array();
            $mail['subject'] = $subject;
            $mail['body'] = $emailBody;
            $mail['receiver'] = $receiver;
            $mail['sender'] = $sender;
            $mail['web_url'] = $this->web_url;
             
            if ($this->mailer->sendEmail($mail)) {
                $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
            } else {
                $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
            }
            
        }else{
            $return['status'] = false;
            $return['msg'] = $this->message['update_f'];
        }
        
        echo json_encode($return);
    }
    
    
    
    public function refuse_approve($vid=""){
  
        $voucher = $this->requisition_model->getVoucherInfo($vid);
        $requesterDeptCode = $this->user_model->getEmpDeptCodeById($voucher['requested_by']);
        
        /* get Privilege  */
        $purchase_approvers= $this->requisition_model->getFullPrivilege(PURCHASE_APPROVE);

        if( !isset($purchase_verifiers[$this->myEmpId]) ){
            
            $return['status'] = false;            
            $return['msg'] = $this->message['no_priv'];
            
            echo json_encode($return);            
            return;
            
        }else if( !empty($purchase_approvers[$this->myEmpId]) ){
                                    
            $dept_privs = $purchase_approvers[$this->myEmpId];                        
            
            if( !in_array($requesterDeptCode, $dept_privs )){
                
                $return['status'] = false;            
                $return['msg'] = $this->message['no_priv'];
                
                echo json_encode($return);            
                return;
            }             
        }

        $excuse = $_POST['excuse'];

        $flag = $this->requisition_model->deleteVoucher($vid);

        
        if($flag){
            $return['status'] = true;
            $return['msg'] = $this->message['delete_s'];
            $return['vid'] = $vid;
    
            //send mail to requester
            $receiver = array();
            $receiver[] = $this->user_model->getBriefInfo($voucher['requested_by']);
    
            $sender = array();
            $sender['name'] = $this->data["myInfo"]->userName;
            $sender['email'] = $this->data["myInfo"]->email;

            $mail = array();
            $mail['subject'] = "Purchase Request Deleted";
            $text = "has refused your purchase order request. So if you have a query, you can talk to him verbally.<br><b>Reason:</b>$excuse";
            $mail['body'] = $this->getMailBody($text, $vid);
            $mail['receiver'] = $receiver;
            $mail['sender'] = $sender;
            $mail['web_url'] = $this->web_url;
             
            if ($this->mailer->sendEmail($mail)) {
                $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
            } else {
                $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
            }
    
        } else {
            $return['status'] = false;
            $return['msg'] = $this->message['delete_f'];
        }
    
        echo json_encode($return);
    }
    
    
    public function verify_purchase($vid=""){
        
        
        $voucher = $this->requisition_model->getVoucherInfo($vid);
        $requesterDeptCode = $this->user_model->getEmpDeptCodeById($voucher['requested_by']);
        
        /* get Privilege  */
        $purchase_verifiers= $this->requisition_model->getFullPrivilege(PURCHASE_VERIFY);
        
        if( !isset($purchase_verifiers[$this->myEmpId]) ){
        
            $return['status'] = false;
            $return['msg'] = $this->message['no_priv'];
        
            echo json_encode($return);
            return;
        
        }else if( !empty($purchase_verifiers[$this->myEmpId]) ){
        
            $dept_privs = $purchase_verifiers[$this->myEmpId];
        
            if( !in_array($requesterDeptCode, $dept_privs )){
        
                $return['status'] = false;
                $return['msg'] = $this->message['no_priv'];
        
                echo json_encode($return);
                return;
            }
        }
    
        $data['verified_by'] = $this->myEmpId;    
        $flag = $this->requisition_model->updateVoucher($data, $vid);
    
        if($flag){
            $return['status'] = true;
            $return['msg'] = $this->message['update_s'];
            
            //send mail to approver & requester            
            $receiver = array();
            $receiver[] = $this->user_model->getBriefInfo($voucher['requested_by']);
            $receiver[] = $this->user_model->getBriefInfo($voucher['approved_by']);
            
            $sender = array();
            $sender['name'] = $this->data["myInfo"]->userName;
            $sender['email'] = $this->data["myInfo"]->email;
            $subject = "Purchase Request Approval";

            $designation = $this->data['myInfo']->userDesignation;
            $dept = $this->data['myInfo']->userDepartment;
            $time = date('h:i:s A');
            $day = date('l');
            $emailBody = "<tr>
                    <td width='50%' valign='top' align='left'>
                    <table cellpadding='3' cellspacing='0'>
                    <tr><td>" . $this->myEmpId . "</td></tr>
                    <tr><td><b><a href='" . $this->web_url . "user/detail/" . $this->data["myInfo"]->userId . "'>$sender[name]</a></b></td></tr>
                                <tr><td><i>$designation</i></td></tr>
                                <tr><td>$dept</td></tr>
                                </table>
                                </td>
                                <td width='50%' valign='center' align='right'>
                                <a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 10px 30px; border: 1px solid #333333;' href='" . $this->web_url . "requisition/voucher_form/$vid'>Show The Purchase Request</a>
                                </td>
                                </tr>
                                <tr height='40'><td colspan='2'>has verified the purchase request.</td></tr>";
                                $mail = array();
                                $mail['subject'] = $subject;
            $mail['body'] = $emailBody;
            $mail['receiver'] = $receiver;
            $mail['sender'] = $sender;
                $mail['web_url'] = $this->web_url;
                 
                if ($this->mailer->sendEmail($mail)) {
                $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
                
            } else {
                $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
            }
            
        }else{
            $return['status'] = false;
            $return['msg'] = $this->message['update_f'];
        }
    
        echo json_encode($return);
    }
    
    public function refuse_verify($vid=""){
    
        $voucher = $this->requisition_model->getVoucherInfo($vid);
        $requesterDeptCode = $this->user_model->getEmpDeptCodeById($voucher['requested_by']);
        
        /* get Privilege  */
        $purchase_verifiers= $this->requisition_model->getFullPrivilege(PURCHASE_VERIFY);
        
        if( !isset($purchase_verifiers[$this->myEmpId]) ){
        
            $return['status'] = false;
            $return['msg'] = $this->message['no_priv'];
        
            echo json_encode($return);
            return;
        
        }else if( !empty($purchase_verifiers[$this->myEmpId]) ){
        
            $dept_privs = $purchase_verifiers[$this->myEmpId];
        
            if( !in_array($requesterDeptCode, $dept_privs )){
        
                $return['status'] = false;
                $return['msg'] = $this->message['no_priv'];
        
                echo json_encode($return);
                return;
            }
        }
    
        $excuse = $_POST['excuse'];
        $flag = $this->requisition_model->deleteVoucher($vid);
        
        if($flag){
            $return['status'] = true;
            $return['msg'] = $this->message['delete_s'];
            $return['vid'] = $vid;
    
            //send mail to requester & approver
            $receiver = array();
            $receiver[] = $this->user_model->getBriefInfo($voucher['requested_by']);
            $receiver[] = $this->user_model->getBriefInfo($voucher['approved_by']);
    
            $sender = array();
            $sender['name'] = $this->data["myInfo"]->userName;
            $sender['email'] = $this->data["myInfo"]->email;
    
            $mail = array();
            $mail['subject'] = "Purchase Request has been  Refused";
            $text = "has refused to verify your purchase order request. So if you have a query, you can talk to him verbally.<br><b>Reason:</b>$excuse";
            $mail['body'] = $this->getMailBody($text, $vid);
            $mail['receiver'] = $receiver;
            $mail['sender'] = $sender;
            $mail['web_url'] = $this->web_url;
             
            if ($this->mailer->sendEmail($mail)) {
                $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
            } else {
                $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
            }
    
        } else {
            $return['status'] = false;
            $return['msg'] = $this->message['delete_f'];
        }
    
        echo json_encode($return);
    }
    
    
    public function lists($rid=''){
        
        $dept_codes = isset($_POST['selected_dept']) ? $_POST['selected_dept'] : array();
        $staffs = isset($_POST['selected_staff']) ? $_POST['selected_staff'] : array();
        
        //priviledge settings        
        $requisition_approvers= $this->requisition_model->getFullPrivilege(REQUISITION_APPROVE);
        $requisition_verifiers = $this->requisition_model->getFullPrivilege(REQUISITION_VERIFY);

        if( !(isset($requisition_approvers[$this->myEmpId]) || isset($requisition_verifiers[$this->myEmpId])) ){
            
            $staffs[] = $this->myEmpId;
            $rid = $this->myEmpId;
            $this->data['departments'] = array();  
            $this->data['staff_array'] = array();
                      
        }else{
            
            $departments = $this->data['departments']; 
            $newDeptAry = array();
            $deptCodeAry = array();
            
            $isApprover = false;
            $isVerifier = false;
            
            if(isset($requisition_approvers[$this->myEmpId])){
                
                $isApprover = true;
                foreach ($requisition_approvers[$this->myEmpId] as $deptCode){
                    $newDeptAry[$deptCode] = $departments[$deptCode];
                    $deptCodeAry[] = $deptCode;
                }
            }
            
            if(isset($requisition_verifiers[$this->myEmpId])){
            
                $isVerifier = true;
                foreach ($requisition_verifiers[$this->myEmpId] as $deptCode){
                    $newDeptAry[$deptCode] = $departments[$deptCode];
                    $deptCodeAry[] = $deptCode;
                }
            }
            
            if( ($isApprover && empty($requisition_approvers[$this->myEmpId]) ) ||
                ($isVerifier && empty($requisition_verifiers[$this->myEmpId]) )
            ){
                /* user has access to all employee/departments  */
                $this->data['staff_array'] = $this->user_model->getStaffArray();
                
            }else{

                $this->data['departments'] = $newDeptAry;
                $this->data['staff_array'] = $this->user_model->getStaffArray($deptCodeAry);
            }
        }
                
        $this->data["rid"] = $rid;
        $this->data["search_dept_code"] = $dept_codes;
        $this->data["search_staffs"] = $staffs;
        
        $this->data['requisitions'] = $this->requisition_model->getReqVouchers($dept_codes, $staffs);
            
        $this->data["title"] = "Requisition";
        $this->data["sub_title"] = "Requisition List";
        $this->view('requisition_lists',$this->data);
    }
    
    
    public function form($vid=""){
        
        //$myInfo = $this->data['myInfo'];
        $this->data["title"] = "Requisition";
        $this->data["sub_title"] = "Requisition Form";
        $this->data["vid"] = $vid;
    
        if(!empty($vid)){
            
            $voucher = $this->requisition_model->getReqVoucherInfo($vid);
            
            if(empty($voucher)){
                $this->data['message'] = "this voucher Can't be found!";
                $this->load->view('not_found', $this->data);
                return;
            }
            
            $requester_dept_code = $this->user_model->getDeptCode($voucher['requested_by']);
            
            $requisition_approvers= $this->requisition_model->getFullPrivilege(REQUISITION_APPROVE);
            $requisition_verifiers = $this->requisition_model->getFullPrivilege(REQUISITION_VERIFY);
                        
            $approvers = $this->getPrivilegersByDeptCode($requester_dept_code, $requisition_approvers);
            $verifiers = $this->getPrivilegersByDeptCode($requester_dept_code, $requisition_verifiers);

            $this->data['requisition_approve_access'] = in_array($this->myEmpId, $approvers) ? true : false;
            $this->data['requisition_verify_access'] = in_array($this->myEmpId, $verifiers) ? true : false;

            
            $this->data["ledgers"] = $this->requisition_model->getReqLedgers($vid);
            $this->data["voucher"] = $voucher;

            
            $this->data['requester'] = $this->user_model->getBriefInfo($voucher['requested_by']);
            
            $this->view('requisition_form',$this->data);
    
        }else{
            /*
            $requisition_creators= $this->requisition_model->getPrivilege(REQUISITION_CREATE);           
            if(in_array($this->myEmpId, $requisition_creators)){
                
                $this->data["title"] = "ABC";
                $this->data["sub_title"] = "ABC";
                $this->data["message"] = "You have no privilege to access this page!";
                
                $this->load->view('not_found', $this->data);
                return;
            }*/
            
            
            //$this->data['myInfo'] = 
            $this->data['items'] = $this->requisition_model->getItemLedger();
            $sel2 = array('category_id', 'category');
            $this->data['categories'] = $this->requisition_model->getCategories($sel2);
            $this->view('requisition_form',$this->data);
        }
    
    }
    
    public function add_req(){
        
        $dataObj = isset($_POST['dataObj']) ? $_POST['dataObj']: array();
        $objects = json_decode($dataObj);
        
        $data['date'] = date('Y-m-d');
        $data['requested_by'] = $this->myEmpId;
         
        $voucher_id = $this->requisition_model->addVoucher('store_req_voucher', $data);
        $ary = $this->requisition_model->addReqLedger($objects, $voucher_id);
        
        if($ary['flag']){
            
            $return['vid'] = $voucher_id;
            $return['msg'] = $this->message['insert_s'];
            $return['status'] = true;
             
            //sent mail to manager of of Dept
            $receiver = array();
            $myInfo = $this->data["myInfo"];

            //sent mail to requisition approver
            $requistion_approvers= $this->requisition_model->getFullPrivilege(REQUISITION_APPROVE);
            $approvers = $this->getPrivilegersByDeptCode($myInfo->userDeptCode, $requistion_approvers);

            if(empty($approvers)){
                
                $return['status'] = false;
                $return['msg'] = $this->message['insert_f'];
                echo json_encode($return);
                return;
            }
            
            $receiver = $this->user_model->getMailInfoByIds($approvers);

            $sender = array();
            $sender['name'] = $myInfo->userName;
            $sender['email'] = $myInfo->email;

            $mail = array();
            $mail['subject'] = "Requisition Request";
            $text = "has sent you requisition request form. So you can approve or refuse his request. Go to the link";
            $mail['body'] = $this->getMailBody($text, $voucher_id);
            $mail['receiver'] = $receiver;
            $mail['sender'] = $sender;
            $mail['web_url'] = $this->web_url;
             
            if ($this->mailer->sendEmail($mail)) {
                $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
            } else {
                $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
            }
    
        } else {
            $return['status'] = false;
            $return['msg'] = $this->message['insert_f'];
        }
    
        echo json_encode($return);
        return;
    }
    
    
    public function approve_req($vid=""){
        $voucher = $this->requisition_model->getReqVoucherInfo($vid);
        
        /*
        $requester_dept_code = $this->user_model->getDeptCode($voucher['requested_by']);        
        $managers = $this->session->getManagersByDeptCode($requester_dept_code);
        $isManager = in_array($this->myEmpId, $managers);
    
        if( !$isManager) {
            $this->load->view('not_found', $this->data);
            return;
        }*/
        
        $requistion_approvers= $this->requisition_model->getFullPrivilege(REQUISITION_APPROVE);
        $approvers = $this->getPrivilegersByDeptCode($myInfo->userDeptCode, $requistion_approvers);
            
        if(!in_array($this->myEmpId, $approvers)){
            
            $this->data["title"] = "ABC";
            $this->data["sub_title"] = "ABC";
            $this->data["message"] = "You have no privilege to access this page!";            
            $this->load->view('not_found', $this->data);
            return;
        }

        
        $this->data['requisition_approve_access'] = in_array($this->myEmpId, $approvers) ? true : false;

        $data['approved_by'] = $this->myEmpId;
        $flag = $this->requisition_model->updateReqVoucher($data, $vid);
    
        if($flag){
            $return['status'] = true;
            $return['msg'] = $this->message['update_s'];
    
            //send mail to requester & Verifier
            $requistion_verifiers= $this->requisition_model->getFullPrivilege(REQUISITION_VERIFY);
            $verifiers = $this->getPrivilegersByDeptCode($myInfo->userDeptCode, $requistion_verifiers);
            
            $receiver = array();
            $receiver = $this->user_model->getMailInfoByIds($verifiers);
            $receiver[] = $this->user_model->getBriefInfo($voucher['requested_by']);
    
            $sender = array();
            $sender['name'] = $this->data["myInfo"]->userName;
            $sender['email'] = $this->data["myInfo"]->email;

            $mail = array();
            $mail['subject'] = "Purchase Request Approval";
            $text = "has approved the purchase request and waiting for management verification.";
            $mail['body'] = $this->getMailBody($text, $vid);
            $mail['receiver'] = $receiver;
            $mail['sender'] = $sender;
            $mail['web_url'] = $this->web_url;
             
            if ($this->mailer->sendEmail($mail)) {
                $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
            } else {
                $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
            }
    
        } else {
            $return['status'] = false;
            $return['msg'] = $this->message['update_f'];
        }
    
        echo json_encode($return);
    }
    
    public function refuse_approve_req($vid=""){
        
        $voucher = $this->requisition_model->getReqVoucherInfo($vid);
        
        $requisition_approvers= $this->requisition_model->getPrivilege(REQUISITION_APPROVE);
        if(!in_array($this->myEmpId, $requisition_approvers)){
            
            $this->data["title"] = "ABC";
            $this->data["sub_title"] = "ABC";
            $this->data["message"] = "You have no privilege to access this page!";            
            $this->load->view('not_found', $this->data);
            return;
        }
    
        $excuse = $_POST['excuse'];    
        $flag = $this->requisition_model->deleteReqVoucher($vid);
      
        if($flag){
            $return['status'] = true;
            $return['msg'] = $this->message['delete_s'];
    
            //send mail to requester
            $receiver = array();
            $receiver[] = $this->user_model->getBriefInfo($voucher['requested_by']);
    
            $sender = array();
            $sender['name'] = $this->data["myInfo"]->userName;
            $sender['email'] = $this->data["myInfo"]->email;
    
            $mail = array();
            $mail['subject'] = "Requisition Request's Approval has been Refused";
            $text = "has refused your purchase order request. So if you have a query, you can talk to him verbally.<br><b>Reason:</b>$excuse";
            $mail['body'] = $this->getMailBody($text, $vid);
            $mail['receiver'] = $receiver;
            $mail['sender'] = $sender;
            $mail['web_url'] = $this->web_url;
             
            if ($this->mailer->sendEmail($mail)) {
                $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
            } else {
                $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
            }
    
        } else {
            $return['status'] = false;
            $return['msg'] = $this->message['delete_f'];
        }
    
        echo json_encode($return);
    }
    
    public function verify_req($vid=""){

        $requisition_verifiers = $this->requisition_model->getPrivilege(REQUISITION_VERIFY);
        if(!in_array($this->myEmpId, $requisition_verifiers)){
            
            $this->data["title"] = "ABC";
            $this->data["sub_title"] = "ABC";
            $this->data["message"] = "You have no privilege to access this page!";            
            $this->load->view('not_found', $this->data);
            return;
        }
        
        $voucher = $this->requisition_model->getReqVoucherInfo($vid);
        $data['verified_by'] = $this->myEmpId;
    
        $flag = $this->requisition_model->updateReqVoucher($data, $vid);
    
        if($flag){
            $return['status'] = true;
            $return['msg'] = $this->message['update_s'];
    
            //send mail to requester & Manager
            $receiver = array();
            $receiver[] = $this->user_model->getBriefInfo($voucher['requested_by']);
            $receiver[] = $this->user_model->getBriefInfo($voucher['approved_by']);
    
            $sender = array();
            $sender['name'] = $this->data["myInfo"]->userName;
            $sender['email'] = $this->data["myInfo"]->email;
    
            $mail = array();
            $mail['subject'] = "Purchase Request Approval";
            $text = "has verified the Requisition request.";
            $mail['body'] = $this->getMailBody($text, $vid);
            $mail['receiver'] = $receiver;
            $mail['sender'] = $sender;
            $mail['web_url'] = $this->web_url;
             
            if ($this->mailer->sendEmail($mail)) {
                $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
            } else {
                $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
            }
    
        } else {
            $return['status'] = false;
            $return['msg'] = $this->message['update_f'];
        }
    
        echo json_encode($return);
    }
    
    public function refuse_verify_req($vid=""){
        
        $requisition_verifiers = $this->requisition_model->getPrivilege(REQUISITION_VERIFY);
        if(!in_array($this->myEmpId, $requisition_verifiers)){
            
            $this->data["title"] = "ABC";
            $this->data["sub_title"] = "ABC";
            $this->data["message"] = "You have no privilege to access this page!";            
            $this->load->view('not_found', $this->data);
            return;
        }
    
        $voucher = $this->requisition_model->getReqVoucherInfo($vid);
    
        $excuse = $_POST['excuse'];
        $flag = $this->requisition_model->deleteReqVoucher($vid);
    
        if($flag){
            $return['status'] = true;
            $return['msg'] = $this->message['delete_s'];
    
            //send mail to requester & Manager
            $receiver = array();
            $receiver[] = $this->user_model->getBriefInfo($voucher['requested_by']);
            $receiver[] = $this->user_model->getBriefInfo($voucher['approved_by']);
    
            $sender = array();
            $sender['name'] = $this->data["myInfo"]->userName;
            $sender['email'] = $this->data["myInfo"]->email;
    
            $mail = array();
            $mail['subject'] = "Requisition Request's Verification has been Refused";
            $text = "has refused your Requisition order request. So if you have a query, you can talk to him verbally.<br><b>Reason:</b>$excuse";
            $mail['body'] = $this->getMailBody($text, $vid);
            $mail['receiver'] = $receiver;
            $mail['sender'] = $sender;
            $mail['web_url'] = $this->web_url;
             
            if ($this->mailer->sendEmail($mail)) {
                $return['msg'] = $return['msg']." & ".$this->message['mail_s'];
            } else {
                $return['msg'] = $return['msg']." but ".$this->message['mail_f'];
            }
    
        } else {
            $return['status'] = false;
            $return['msg'] = $this->message['delete_f'];
        }
    
        echo json_encode($return);
    }
    
    function getMailBody($text, $vid){
        
        $designation = $this->data['myInfo']->userDesignation;
        $dept = $this->data['myInfo']->userDepartment;
        $time = date('h:i:s A');
        $day = date('l');
        $emailBody = "<tr>
                    <td width='50%' valign='top' align='left'>
                    <table cellpadding='3' cellspacing='0'>
                    <tr><td>" . $this->myEmpId . "</td></tr>
                    <tr><td><b><a href='" . $this->web_url . "user/detail/" . $this->data["myInfo"]->userId . "'>".$this->data["myInfo"]->userName."</a></b></td></tr>
                            <tr><td><i>$designation</i></td></tr>
                            <tr><td>$dept</td></tr>
                            </table>
                            </td>
                            <td width='50%' valign='center' align='right'>
                            <a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 10px 30px; border: 1px solid #333333;' href='" . $this->web_url . "requisition/voucher_form/$vid'>Show The Request</a>
                            </td>
                            </tr>
                            <tr height='40'><td colspan='2'>$text</td></tr>";
                
        return $emailBody;
    }
    
    function no_privilege(){
        
        $this->data["title"] = "ABC";
        $this->data["sub_title"] = "ABC";
        $this->data["message"] = "You have no privilege to access this page!";
        $this->load->view('not_found', $this->data);
        return;
    }
    
    function getPrivilegersByDeptCode($deptCode, $privilegers = array()){

        $ret = array();
        foreach ( $privilegers as $eid=>$deptCodeAry){

            if(count($deptCodeAry) == 0 ){
                
                $ret[] = $eid;
            }else{
                
                if(in_array($deptCode, $deptCodeAry)){
                    
                    $ret[] = $eid;
                }            
            }            
        }
        
        return $ret;
    }
    
}