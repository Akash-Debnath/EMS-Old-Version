<?php
class Remark extends G_Controller {

    public $adminFlag = false;
    public $data = array();
    public $userId = "";
    public $myEmpId = '';
    
    public function __construct() {
        parent::__construct();

        $this->isLoggedIn();
        
        $this->data["menu"] = "Remark";
        $this->load->library('session');        
        $this->load->model('remark_model');
        $this->load->model('user_model');
        $this->load->library('pagination');
        $this->load->library('mailer');
        
        $this->data["myInfo"] = $this->session->GetMyBriefInfo();
        $this->data['departments'] = $this->user_model->department();
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
    
    public function notice($page = 1) {
    
        $total_row = $this->remark_model->notice_count();
        
        /* pagination config */
        $config['base_url'] = base_url() . "remark/notice/";
        $config['total_rows'] = $total_row;
        $config['per_page'] = ROWS_PER_PAGE;
        $config['uri_segment'] = 3;
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        $config['first_tag_open']=$config['last_tag_open']=$config['next_tag_open']=$config['prev_tag_open']=$config['num_tag_open']='<li>';
        $config['first_tag_close']=$config['last_tag_close']=$config['next_tag_close']=$config['prev_tag_close']=$config['num_tag_close']='</li>';        
        $config['cur_tag_open'] = '<li><a class="current">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $this->pagination->initialize($config);
        $this->data["pagination_links"] = $this->pagination->create_links();
        
        
        $this->data["offset"] = ($page - 1) * ROWS_PER_PAGE;
        $this->data['notices'] = $this->remark_model->get_notices($this->data["offset"]);
        

        $this->data["status_array"] = $this->status_array;
        $this->data["title"] = "Notice";
        $this->data["sub_title"] = "Notice Board";
         
        $this->load->view('notice', $this->data);
    }
    
    public function notice_detail($page=1){

        $total_row = $this->remark_model->notice_count();
        
        /* pagination config */
        $config['base_url'] = base_url() . "remark/notice_detail/";
        $config['total_rows'] = $total_row;
        $config['per_page'] = 1;
        $config['uri_segment'] = 3;
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        $config['first_tag_open']=$config['last_tag_open']=$config['next_tag_open']=$config['prev_tag_open']=$config['num_tag_open']='<li>';
        $config['first_tag_close']=$config['last_tag_close']=$config['next_tag_close']=$config['prev_tag_close']=$config['num_tag_close']='</li>';        
        $config['cur_tag_open'] = '<li><a class="current">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $this->pagination->initialize($config);
        $this->data["pagination_links"] = $this->pagination->create_links();
        
        
        $offset = ($page - 1) ;
        $this->data['notices'] = $this->remark_model->notice_details($offset);
	if($this->data['notices']->isEncrypted == 'Y'){
            $this->data['notices']->notice = base64_decode($this->data['notices']->notice);
        }
        
        // add read_by employee count
        $this->remark_model->addReadBy('notice', $this->data['notices']->id, $this->data["myInfo"]->userId);
        
        $this->data["page_number"] = $page;
        $this->data["status_array"] = $this->status_array;
        $this->data["title"] = "Notice";
        $this->data["sub_title"] = "Notice Detail";
         
    
        $this->load->view('notice_detail', $this->data);
    }
    
    public function edit_notice($id)
    {
    	if(!$this->data['isAdmin']) {
        $this->load->view('not_found', $this->data);
        return;
        }
        
        
        if($this->uri->segment(4)){
            $page = $this->uri->segment(4);
        } else{
            $page = 1;
        }
        
        $this->data['notices'] = $this->remark_model->notice_by_id($id);
	if($this->data['notices']->isEncrypted == 'Y'){
            $this->data['notices']->notice = base64_decode($this->data['notices']->notice);
        }

        $this->data["status_array"] = $this->status_array;
        $this->data["title"] = "Notice";
        $this->data["sub_title"] = "Edit Notice";
        $this->data["page_number"] = $page;
        
        $this->load->view('notice_edit', $this->data);
    }
    
    public function add_notice()
    {
    	if(!$this->data['isAdmin']) {
        $this->load->view('not_found', $this->data);
        return;
        }     

        $info = new stdClass();
        $info->id = "";
        $info->subject = "";
        $info->notice = "";
        $info->notice_date = date('Y-m-d');
      
        $this->data['notices'] = $info;
    
        $this->data["status_array"] = $this->status_array;
        $this->data["title"] = "Notice";
        $this->data["sub_title"] = "Add New Notice";
        $this->data["page_number"] = "";
    
        $this->load->view('notice_edit', $this->data);
    }
    
    public function updateNotice($aData = array(), $addF=false, $needToRedirect = true){

    	if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
        
        //$id = isset($this->input->post('notice_id'))? $this->input->post('notice_id'): "";
        //$id = $addF ? "" : $this->input->post('notice_id');


        //$sesId = $this->data["myInfo"]->userId;
        
        if($addF == true) {
            $id = "";
            $data['notice_date'] = date('Y-m-d');
            $data['subject'] = $aData['subject'];
            $data['notice'] = $aData['notice'];

        } else {
            $id = $this->input->post('notice_id', true);
            $data['notice_date'] = $this->input->post('noticeDate', true);
            $data['subject'] = $this->input->post('noticeSubject', true);
            $data['notice'] = $this->input->post('noticeBody', true);
            $data['isEncrypted'] = $this->input->post('isEncrypted', true);
            $page = $this->input->post('page_number', true);
        }

        if(empty($id)){
            //Add Notice
            $data['read_by'] = "";
            
            $insert_id = $this->remark_model->addNotice($data);
            
            if($insert_id){
                //Succesfull
                //add log
                $logData = array('emp_id'=>$this->data["myInfo"]->userId, "activity"=>"A", "affected"=>$insert_id, "log_text"=>"notice=>id:".$insert_id, "log_time"=> date('Y-m-d H:i:s'));
                $this->user_model->setLog($logData);
                
                // sent mail                
                $receiver = $this->remark_model->getMail();
                $sender = array();
                $sender['name'] = $this->data["myInfo"]->userName;
                $sender['email'] = $this->data["myInfo"]->email;                
                
                foreach ($receiver as $key=>$obj){
                    if($obj->emp_id==$this->data["myInfo"]->userId) {
                        unset($receiver[$key]);
                    }
                }

                $data['notice_date'] = date("Y-m-d");
                $dt_ary = explode("-",$data['notice_date']);
                $emailDate = $dt_ary[2]."/".$dt_ary[1]."/".$dt_ary[0];
                $emailBody = "<table width='90%' align='center'><tr>
							<td colspan='3'>
								A <a href='".base_url()."/index.php?rto=".base64_encode("notice.php")."'>new notice($insert_id)</a> added to <a href='".base_url()."'>".COMPANY_PREFIX." Staff</a> notice board!<br><br>
									</td>
									</tr>
									<tr><td width='50'>Date</td><td><b>:</b></td><td><b>".$emailDate."</b></td></tr>
									<tr><td>Subject</td><td><b>:</b></td><td style='border-bottom:1px dotted #DDDDDD;'><b>".$data['subject']."</b></td></tr>
									<tr><td valign='top'></td><td></td><td>".$this->rawNoticeBody."</td></tr>
									</table>
									</td>
									</tr></table>";

                if($this->mailer->sendMail($data['subject'], $emailBody, $receiver, $sender )) {
                    if($addF == true){
                        
                        if($needToRedirect){
                            
                            redirect(base_url().'user/detail/'.$aData['emp_id']);
                        }                        
                        return ;
                    }
                    
                    if($needToRedirect){
                        redirect(base_url().'remark/notice'.$page);
                    }                
                }
                
                
            }else {
                //Unsuccesfull
                echo "failed";
            }
        } else {
            //Update Notice
            $flag = $this->remark_model->updateNotice($id, $data);
            
            if($flag){
                //Succesfull
                //add log
                $logData = array('emp_id'=>$this->data["myInfo"]->userId, "activity"=>"U", "affected"=>$id, "log_text"=>"notice=>id:".$id, "log_time"=> date('Y-m-d H:i:s'));
                $this->user_model->setLog($logData);
                
                if($needToRedirect){
                    redirect(base_url().'remark/notice_detail/'.$page);
                }
            }else {
                //Unsuccesfull
                echo "failed";
            }
        }

    }
    
    
    public function delete_notice($id){
        
    	if(!$this->data['isAdmin']) {
        $this->load->view('not_found', $this->data);
        return;
        }
        
    
        $flag =  $this->remark_model->delNotice($id);
    
    	if($flag){
	        //Succesfull
    	    //add log
    	    $logData = array('emp_id'=>$this->data["myInfo"]->userId, "activity"=>"D", "affected"=>$id, "log_text"=>"notice=>id:".$id, "log_time"=> date('Y-m-d H:i:s'));
    	    $this->user_model->setLog($logData);
    	    
	        redirect(base_url().'remark/notice');
	    }else {
	        //Unsuccesfull
	        echo "failed";
	    }
    }
    
    public function attachment( $page = 1) {
    
        $text = $this->getAttachTo();
        $total_row = $this->remark_model->attachment_count($text, $this->myEmpId);
        
        /* pagination config */
        $config['base_url'] = base_url() . "remark/attachment/";
        $config['total_rows'] = $total_row;
        $config['per_page'] = ROWS_PER_PAGE;
        $config['uri_segment'] = 3;
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        $config['first_tag_open']=$config['last_tag_open']=$config['next_tag_open']=$config['prev_tag_open']=$config['num_tag_open']='<li>';
        $config['first_tag_close']=$config['last_tag_close']=$config['next_tag_close']=$config['prev_tag_close']=$config['num_tag_close']='</li>';
        $config['cur_tag_open'] = '<li><a class="current">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $this->pagination->initialize($config);
        $this->data["pagination_links"] = $this->pagination->create_links();
        
        
        $this->data["offset"] = ($page - 1) * ROWS_PER_PAGE;   
        $this->data['attachments'] = $this->remark_model->get_attachments($this->data["offset"], $text, $this->myEmpId);
    
        $this->data["status_array"] = $this->status_array;
        $this->data["title"] = "Attachment";
        $this->data["sub_title"] = "Attachment Board";
         
    
        $this->load->view('attach', $this->data);
    }
    
    public function attach_detail($page=1){

        $text = $this->getAttachTo();        
        $total_row = $this->remark_model->attachment_count($text, $this->myEmpId);
        
        /* pagination config */
        $config['base_url'] = base_url() . "remark/attach_detail/";
        $config['total_rows'] = $total_row;
        $config['per_page'] = 1;
        $config['uri_segment'] = 3;
        $config['num_links'] = 5;
        $config['use_page_numbers'] = TRUE;
        $config['first_tag_open']=$config['last_tag_open']=$config['next_tag_open']=$config['prev_tag_open']=$config['num_tag_open']='<li>';
        $config['first_tag_close']=$config['last_tag_close']=$config['next_tag_close']=$config['prev_tag_close']=$config['num_tag_close']='</li>';        
        $config['cur_tag_open'] = '<li><a class="current">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Prev';
        $this->pagination->initialize($config);
        $this->data["pagination_links"] = $this->pagination->create_links();        
        
        $offset = ($page - 1) ;
        $attaches = $this->remark_model->attach_detail($offset , $text, $this->myEmpId);        
		if($attaches->is_encrypted == 'Y') $attaches->message = base64_decode($attaches->message);																						  
        $this->data['attFiles'] = $this->remark_model->getAttFiles($attaches->id);
        $this->data['attaches'] = $attaches;
        
        // add read_by employee count
        $this->remark_model->addReadBy('attach_msg', $attaches->id, $this->myEmpId);
        
        $this->data["page_number"] = $page;
        $this->data["status_array"] = $this->status_array;
        $this->data["title"] = "Attach";
        $this->data["sub_title"] = "Attach Detail";
         
    
        $this->load->view('attach_detail', $this->data);
    }
    
    public function add_attach()
    {
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
        
        $info = new stdClass();
        $info->id = "";
        $info->subject = "";
        $info->message = "";
        $info->message_date = date('Y-m-d');
        $info->message_to = "";
        $this->data["attach_update"] = false;
    
        $this->data['attaches'] = $info;
        $this->data['attach_to'] = $this->attachment_send_to;
        
        $this->data["title"] = "Attach";
        $this->data["sub_title"] = "New Attachment";
        $this->data["page_number"] = "";
        
        $staff_array = $this->user_model->getStaffArray();
        $this->data['staff_array'] = $staff_array['all'];

    
        $this->load->view('attach_edit', $this->data);
    }
    
    public function edit_attach($id)
    {
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }    
    
        if($this->uri->segment(4)){
            $page = $this->uri->segment(4);
        } else{
            $page = 1;
        }

        $this->data['attaches'] = $this->remark_model->attach_by_id($id);        
		if($this->data['attaches']->is_encrypted == 'Y') $this->data['attaches']->message = base64_decode($this->data['attaches']->message);																																	
        $this->data['attFiles'] = $this->remark_model->getAttFiles($id);        
        $this->data['attach_to'] = $this->attachment_send_to;
        
        $staff_array = $this->user_model->getStaffArray();
        $this->data['staff_array'] = $staff_array['all'];
    
        $this->data["title"] = "Attach";
        $this->data["sub_title"] = "Edit Attachment";
        $this->data["page_number"] = $page;
        $this->data["attach_update"] = true;
    
        $this->load->view('attach_edit', $this->data);
    }
    
    public function updateAttach(){
           
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
    
        $infos = $this->remark_model->getMail();
    
        $id = $this->input->post('attach_id');
        $removedFile = $this->input->post('removedFile');
        $removeFileIDs = explode(',', $removedFile);
        
        $data['message_date'] = $this->input->post('attachDate');
        $data['subject'] = $this->input->post('attachSubject');
        $data['message'] = $this->input->post('attachBody');
        $data['message_from'] = $this->data["myInfo"]->userId;
        $data['message_to'] = $this->input->post('attachTo');
		$data['is_encrypted'] = $this->input->post('is_encrypted');														   
        $page = $this->input->post('page_number');
        $custom_to = $this->input->post('customTo');
        $data['custom_recipient'] = implode($custom_to, ',');
        
        if(empty($id)){
            //Add Attach
            $data['read_by'] = "";
            $attachFileCount = 0;
            $insert_id = $this->remark_model->addAttach($data);
    
            if($insert_id){
                //Add Succesfull
                
                $aData['att_id'] = $insert_id;
                $uploadFlag = false;
                $name = "att_";
                $num =  $this->remark_model->getLastname();
                $config = array(
                    'upload_path'     => './assets/files/',
                    'allowed_types'   => 'jpg|jpeg|png|gif||pdf|doc|docx|ppt|pptx|xml|zip|rar|exe',                    
                    'max_size'        => "1000KB",
                );
                $this->load->library('upload');
                
                foreach ($_FILES as $fieldname => $fileObject)  //fieldname is the form field name
                {
                    if (!empty($fileObject['name'])) {
                        $config['file_name'] = $name.++$num;
                        $this->upload->initialize($config);
                        if (!$this->upload->do_upload($fieldname)) {
                            $errors = $this->upload->display_errors();
                            print_r($errors);
                        }else {
                            // Code After Files Upload Success GOES HERE                             
                            $data_upload_files = $this->upload->data();
                            $aData['filename'] = $data_upload_files['file_name'];
                            $aData['original_name'] = $fileObject['name'];
                            
                            $this->remark_model->addToAttachFile($aData);
                            $attachFileCount++;
                        }
                    }
                }

                /* send mail to  all recipient*/
                $custom_recipients = $this->user_model->getMailInfoByIds($custom_to);
                
                $receiver = array();
                $receiver = $this->user_model->getMailInfoByType($data['message_to']);
                
                foreach ($custom_recipients as $obj){

                    $receiver[$obj->emp_id] = $obj;
                }

                
                //foreach ($receiver as $key=>$obj){
                //    if($obj->emp_id==$this->data["myInfo"]->userId) {
                //        unset($receiver[$key]);
                //    }
                //}
                
                $data['notice_date'] = "2015-05-05";
                $dt_ary = explode("-",$data['notice_date']);
                $emailDate = $dt_ary[2]."/".$dt_ary[1]."/".$dt_ary[0];
                $emailBody = "<table width='90%' align='center'><tr>
							<td colspan='3'>
								A <a href='".base_url()."/index.php?rto=".base64_encode("notice.php")."'>new notice</a> added to <a href='".base_url()."'>".COMPANY_PREFIX." Staff</a> notice board!<br><br>
									</td>
									</tr>
									<tr><td width='50'>Date</td><td><b>:</b></td><td><b>".$emailDate."</b></td></tr>
									<tr><td>Subject</td><td><b>:</b></td><td style='border-bottom:1px dotted #DDDDDD;'><b>".$data['subject']."</b></td></tr>
									<tr><td valign='top'></td><td></td><td>".$data['notice']."</td></tr>
									</table>
									</td>
									</tr></table>";
                
                if(  $this->mailer->sendMailForAttachment($data['subject'], $receiver, $this->data["myInfo"], $insert_id, $this->web_url, $attachFileCount, $this->rawNoticeBody, $data['message_date'])  ){
                    
                    redirect(base_url().'remark/attachment');
                
                }else{
                    
                    $this->data['message'] = "<span style='color: red'> Sending mail to recipients is failed!<span>";
                    
                    $link =array();
                    $link['href'] = base_url().'remark/attachment';
                    $link['text'] = 'Go back to Attachment Board';
                    $this->data['link'] = $link;
                    
                    $this->view('message_view', $this->data);
                    return;
                }
                
            }else {
                
                //Add Unsuccesfull
                echo "failed one";
            }
        } else {
            //Update Attach
            $uFlag = $this->remark_model->updateAttach($id, $data);
            
            //remove Attach file
            foreach ($removeFileIDs as $rID){
                
                $file_name = $this->remark_model->getFileNameByID($rID);
                //remove from diretory
                if(is_file('assets/files/'.$file_name)){
                    unlink('assets/files/'.$file_name);
                }
                //remove from database
                $rFlag = $this->remark_model->delAttachFile($rID);
            }
            
            //Add Atach Files
            
            $aData['att_id'] = $id;
            $name = "att_";
            $num =  $this->remark_model->getLastname();
            $config = array(
                'upload_path'     => './assets/files/',
                'allowed_types'   => 'jpg|jpeg|png|gif||pdf|doc|docx|ppt|pptx|xml|zip|rar|exe',
                'max_size'        => "1000KB",
            );
            $this->load->library('upload');
            
            foreach ($_FILES as $fieldname => $fileObject)  //fieldname is the form field name
            {
                if (!empty($fileObject['name'])) {
                    $config['file_name'] = $name.++$num;
                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload($fieldname)) {
            
                        $errors = $this->upload->display_errors();
                        print_r($errors);
                    }else {
                        // Code After Files Upload Success GOES HERE
                        $data_upload_files = $this->upload->data();
                        $aData['filename'] = $data_upload_files['file_name'];
                        $aData['original_name'] = $fileObject['name'];
            
                        $aFlag = $this->remark_model->addToAttachFile($aData);
                    }
                }
            }
    
            if($uFlag||$rFlag||$aFlag){
                //Update Succesfull
                //add log
                $logData = array('emp_id'=>$this->data["myInfo"]->userId, "activity"=>"U", "affected"=>$id, "log_text"=>"attachment=>id:".$id, "log_time"=> date('Y-m-d H:i:s'));
                $this->user_model->setLog($logData);
    
                redirect(base_url().'remark/attach_detail/'.$page);
            }else {
                //Update Unsuccesfull
                echo "failed  two";
            }
        }
    
    }
    
    public function del_attach($id){
    
        if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
        //delete from diretory        
        $file_names = $this->remark_model->getFileNameBy_att_id($id);

        foreach ($file_names as $obj){
            if(is_file('assets/files/'.$obj->filename)){
                unlink('assets/files/'.$obj->filename);
            }
        }      
        //delete from database
        $flag =  $this->remark_model->delAttach($id);
        
    
        if($flag){
            //Succesfull
            //add log
            $logData = array('emp_id'=>$this->data["myInfo"]->userId, "activity"=>"D", "affected"=>$id, "log_text"=>"attach=>id:".$id, "log_time"=> date('Y-m-d H:i:s'));
            $this->user_model->setLog($logData);
            	
            redirect(base_url().'remark/attachment');
        }else {
            //Unsuccesfull
            echo "failed";
        }
    }
    
    public function downloadAttach($id){
        //echo $id;
        $reader = false;
        $utype = $this->data["uType"];
        $sUser = $this->data["myInfo"]->userId;
        $attach_file = $this->remark_model->getAttFilesbyID($id);

        //print_r($attach_file);
        
        $message_to = $this->remark_model->getMessage_TO($attach_file->att_id);
        
        if($message_to =='E') {
            $reader=true;
        } elseif($message_to =='A') {
            if($utype=='A') {
                $reader=true;
            }
        } elseif($message_to =='B') {
            if($utype=='B' || $sUser='DO') {
                $reader=true;
            }
        } elseif($message_to =='M') {
            if($this->session->IsManager($sUser)) {
                $reader=true;
            }
        } elseif($message_to =='C') {
            if($this->session->IsManager($sUser) || $this->session->IsManagement($sUser) || $sUser=='DO') {
                $reader=true;
            }
        }
        
        if($reader==true) {        
            $headers = get_headers(base_url()."assets/files/".$attach_file->filename);
            $response_code = substr($headers[0], 9, 3);
            
            if( $response_code == "200"){
                //success
                $this->load->helper('download');
                $data = file_get_contents("./assets/files/".$attach_file->filename);
                
                $file_ext = pathinfo($attach_file->filename, PATHINFO_EXTENSION);
                $name = !empty($attach_file->original_name) ? $attach_file->original_name : "Attachment.".$file_ext;
            
                force_download($name, $data);
            
            }else{
                echo "ERROR: File not Found!";
            }
        }
    }
    
        
        
    public function policy(){
    
        $policies = $this->remark_model->getPolicy();
        $policy_file = $this->remark_model->getPolicyFile();
        
        foreach ($policy_file as $obj){
            
            //$policies[$obj->policy_id]->file[] = array();
            if(!empty($policies[$obj->policy_id]))  $policies[$obj->policy_id]->file[] = $obj;
            
        }
        
        $this->data['isAdmin'] = $this->data['isAdmin'];
        $this->data['policies'] = $policies;
        $this->data["title"] = "Policy";
        $this->data["sub_title"] = "Policy Board";
        $this->view('policy',$this->data);
    }

    
    public function add_policy() {
        
        if(!$this->data['isAdmin']) {
            //$this->load->view('not_found', $this->data);
            return;
        }
         
        $data['policy_title'] = $_POST['policy_title'];


        $all_files = array();
        $file = array();
        
        $count = isset($_FILES['upload']) ? count($_FILES['upload']['name']) : 0;

        for($i=0; $i<$count; $i++){

            foreach($_FILES['upload'] as $key => $ary) {
                $file[$key] = $ary[$i];
            }
            $all_files[$i] = $file;
        }

        $extArray = array("jpg","jpeg","png","gif","txt","pdf","doc","docx");

        foreach ($all_files as $fileObject)  //fieldname is the form field name
        {
            $original_name = $fileObject["name"];
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $ext = strtolower($ext);
            if(!in_array($ext,$extArray)) {
                $return['msg'] = "Wrong file format!";
                $return['status'] = false;
                echo json_encode($return);
                die;
            }
        }
         
         
        $insert_policy_id = $this->remark_model->add_policy($data);
          
        $fData = array();     
            
        foreach ($all_files as $fileObject)  //fieldname is the form field name
        {
            $fData['file_name'] = $fileObject["name"];
            $fData['policy_id'] = $insert_policy_id;
            $insert_id = $this->remark_model->add_policy_file($fData);
            
            $original_name = $fileObject["name"];
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $file_name = "policy_".$insert_id.".".$ext;
            move_uploaded_file($fileObject["tmp_name"], "./assets/files/$file_name");
        }

        
        if($insert_policy_id){
            $return['status'] = true;
            $return['msg'] = $this->message['insert_s'];
        }else{
            $return['status'] = true;
            $return['msg'] = $this->message['insert_f'];
        }
        
        echo json_encode($return);
        die;
    }
    
    public function download($id){
        
        $fileObj =  $this->remark_model->getPolicyFileById($id);                
        $original_name = $fileObj->file_name;

        $ext = strtolower(substr(strrchr($original_name,"."),1));
        $file_name = 'policy_'.$id.'.'.$ext;

        $headers = get_headers(base_url()."assets/files/".$file_name);
        $response_code = substr($headers[0], 9, 3);
    
        if( $response_code == "200"){
            //success
            $this->load->helper('download');
            $data = file_get_contents("./assets/files/".$file_name);
            //echo $data;die;
            $name = $original_name;
    
            force_download($name, $data);
    
        }else{
            
            $headers = get_headers($this->web_url_main."attachments/".$file_name);
            $response_code = substr($headers[0], 9, 3);
            
            if( $response_code == "200"){
                
                //success
                $this->load->helper('download');
                $data = file_get_contents($this->web_url_main."attachments/".$file_name);
                //echo $data;die;
                $name = $original_name;
            
                force_download($name, $data);
            
            }else{
                echo "ERROR: File not Found!";
            }
        }
    }
    
    
	public function del_policy($id){
	    if(!$this->data['isAdmin']) {
	        //$this->load->view('not_found', $this->data);
	        return;
	    }
	     
	    $flag = $this->remark_model->del_policy($id);
	     
	    if($flag){
	        $return['msg'] = $this->message['delete_s'];
	        $return['status'] = true;
	         
	    }else{
	        $return['msg'] = $this->message['delete_f'];
	        $return['status'] = false;
	    }
	     
	    echo  json_encode($return);
	}
	
	public function jdboard(){
	     
	    $array = array(
	        'select'=>array(
	            'e.emp_id',
	            'e.name',
	            'e.dept_code',
	            'e.grade_id',
	            'ds.designation'
	        )
	    );
	    $staffsRecord = $this->user_model->get_user($array);
	    $this->data["grades"] = $this->user_model->getGrades();
	    
	    //print_r($staffsRecord);
	    
	     
	    $staffs = array();
	    foreach ($staffsRecord as $obj) {
	        $staffs[$obj->dept_code][] = $obj;
	    }
	    
	    $job_files = $this->remark_model->getJobFiles();
	    
	    //print_r($job_files);

	    $this->data["staffs"] = $staffs;     
	    $this->data["job_files"] = $job_files;
	    $this->data["title"] = "Job_Description";
	    $this->data["sub_title"] = "Job Description Board";
	    $this->view('job_description',$this->data);
	     
	    //$employees = $this->settings_model->
	}

    public function add_job_desc()
    {

        $flagD = false; 
        
        $data['emp_id'] = isset($_POST['staffId']) ? $_POST['staffId']: '';
        $data['file_name'] = isset($_FILES['inputFile']['name']) ? $_FILES['inputFile']['name'] : '';
        $prev_file =  isset($_POST['prev_file']) ? $_POST['prev_file'] : '';
        
        if(!empty($prev_file)){ 
            $ext = strtolower(substr(strrchr($prev_file,"."),1));
            $path_to_file = 'assets/files/job_'.$data['emp_id'].'.'.$ext;
            
            if(is_file($path_to_file)){ 
                
                unlink($path_to_file);                
                $flagD = $this->remark_model->delete_job_file($data['emp_id']);
            }
        }
        
        $config = array(
            'upload_path'     => './assets/files/',
            'allowed_types'   => 'gif|jpg|png|jpeg|pdf|doc|docx|xml|txt|zip|rar',
            'file_name'       => 'job_'.$data['emp_id'],
            'overwrite'       => TRUE,
            'max_size'        => "1000"
        );
        
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('inputFile')) {
            //echo "Image upload failed.";
            $this->data['message'] = "Wrong File Format";
            $this->view('error', $this->data);

        }else{
            //success

            $flag = $this->remark_model->add_job_desc_file($data);
            
            redirect(base_url()."remark/jdboard");

        }

    }
    
    public function download_jdfile($id){
    
        if($this->uri->segment(4)) {
            $original_name =  $this->uri->segment(4);
        }
    
        $ext = strtolower(substr(strrchr($original_name,"."),1));
        $file_name = 'job_'.$id.'.'.$ext;
    
        $headers = get_headers(base_url()."assets/files/".$file_name);
        $response_code = substr($headers[0], 9, 3);
    
        if( $response_code == "200"){
            //success
            $this->load->helper('download');
            $data = file_get_contents("./assets/files/".$file_name);
            //echo $data;die;
            $name = $original_name;
    
            force_download($name, $data);
    
        }else{
            echo "ERROR: File not Found!";
        }
    }
    
    public function getAttachTo(){
        $text = array();
        
        if ($this->data["uType"] == "M" || $this->session->IsManager($this->myEmpId)) {
            $text = Array('M', 'C', 'E');
        } else if($this->data["uType"] == "B" || $this->session->IsManagement($this->myEmpId)) {
            $text = Array('B', 'C', 'E');
        }
        
        if($this->data["uType"] == 'A' || $this->session->IsAdmin($this->myEmpId)){
            $text[] = "A";
            $text[] = "E";
        }else if($this->data["uType"] == 'E'){
            $text = array('E');
        }
        
        return $text;
    }
	
} 