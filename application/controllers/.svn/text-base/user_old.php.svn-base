<?php
class User extends G_Controller {
	
    public $adminFlag = false;
	public $data = array();
	public $myEmpId = '';
	public function __construct() {
	    
		parent::__construct();
		//$this->load->helper(array('form', 'url'));
		$this->load->library('session');
		$this->load->model('user_model');
		$this->load->library('pagination');
		
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

	public function index(){
		$this->login();
	}

	public function login()
	{
		if(!empty($this->myEmpId)){
			redirect(base_url()."user/detail/".$this->myEmpId);
		}

		$login_id = isset($_POST["login_id"]) ? trim($_POST["login_id"]) : "";
		$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
		$redirect_to = isset($_POST["rto"]) ? trim($_POST["rto"]) : base_url()."user/detail/".$login_id;

		$errMsg = "";
		$user = null;

		if(isset($_POST["login"])) {
			$ary = $this->session->GetSessionData();

			if(empty($login_id) && empty($password)) $errMsg = 'Employee ID & Password Required!';
			elseif(empty($login_id)) $errMsg = 'Employee ID Required!';
			elseif(empty($password)) $errMsg = 'Password Required!';

			$isBlocked = $this->user_model->missed_login_check ( $login_id );
			if(!$isBlocked) {
				if (empty($errMsg)) $errMsg = $this->validateLogin($login_id, $password);
				if (empty($errMsg)) {
					if (filter_var($login_id, FILTER_VALIDATE_EMAIL)) {
						/* valid email address; login by dhakatel/genuitysystems */
						list($user_name, $domain) = explode('@', $login_id);
						$login_flag = false;

						if (($domain == 'dhakatel.com') || ($domain == 'genuitysystems.com') || ($domain == 'genusys.us')) {
							$login_flag = $this->login_dhakatel($user_name, $password);
							if ($login_flag) {
								//update login_ID from mail to eid
								$login_id = $this->user_model->getEidbyMail($login_id);
								if ($login_id) {
									$user = $this->user_model->login_gmail($login_id);
								}
							} else {
								//$this->user_model->addFailedLog($login_id);
								$errMsg = 'User ID or Password Wrong!';
							}
						} else {
							//$this->user_model->addFailedLog($login_id);
							$errMsg = 'User ID is wrong!';
						}
					} else {
						// normal Employee ID
						if (!preg_match("/^[a-zA-Z0-9]{3,10}$/", trim($login_id))) {
							//$this->user_model->addFailedLog($login_id);
							$errMsg = "Please provide valid User ID";
						} else {
							$login_id = strtoupper($login_id);
							$user = $this->user_model->login($login_id, $password);
						}
					}

					if ((isset($user) && $user != null)) {

						$logData = array('emp_id' => $login_id, "activity" => "L", "affected" => "", "log_text" => "Logged in", "log_time" => date('Y-m-d H:i:s'));
						$this->user_model->setLog($logData);

						$ses['emp_id'] = $user['emp_id'];
						$ses['name'] = $user['name'];
						$ses['lock'] = $user['active'];
						$ses['archive'] = $user['archive'];
						$ses['dept_name'] = $user['dept_name'];
						$ses['dept_code'] = $user['dept_code'];
						$ses['designation'] = $user['designation'];
						$ses['gender'] = $user['gender'];
						$ses['email'] = $user['email'];
						$ses['image'] = $this->getImagePath($user['image']);
						$ses['login_by_gmail'] = false;
						//in leave employee and joined employee after leave counter
						$ses['todaysLeaveCount'] = $this->tadaysLeaveCount();
						$ses['tadaysJoinCount'] = $this->tadaysJoinCount();

						//$this->SetSessionSettings();
						$this->SetSessionSettings($user['emp_id']);

						if ($this->session->IsAdmin($login_id)) {
							$ses['uType'] = 'A';
						} else if ($this->session->IsManagement($login_id)) {
							$ses['uType'] = 'B';
						} else if ($this->session->IsManager($login_id)) {
							$ses['uType'] = 'M';
						} else {
							$ses['uType'] = 'E';
						}

						if ($ses['lock'] == 'U' && $ses['archive'] == 'N') {
							$this->session->SetSessionData($ses);

							$data = array('online' => 'Y', 'login_time' => date('Y-m-d H:i:s'));
							$this->user_model->setOnline($data, $login_id);

							$lastPage = $this->session->get_lastpage();
							if (empty($lastPage)) {
								redirect(base_url() . "user/detail/" . $ses['emp_id']);
							} else {
								redirect($lastPage);
							}
						} else if ($ses["lock"] == "L") {
							$errMsg = "Sorry, You are locked by admin";
						} else {
							$errMsg = "Sorry, You are now in archive";
						}
					} else {
						$this->user_model->addFailedLog($login_id);
						$errMsg = 'User ID or Password Wrong!';
					}
				}//end login if
			} else {
				$errMsg = $isBlocked;
			}
		}

		$data = array();
		$data["login_id"] = $login_id;
		$data["password"] = "";//$password;
		$data["errMsg"] = $errMsg;
		$this->load->view('login', $data);
	}
	
	protected function validateLogin($user, $pass){
	    $err = '';

	    if (empty($err)) {
	        $pos = $this->multi_strpos($pass, array("'", " ", ",", ";"));
	        if ($pos !== false) {
	            $err = "Please provide valid Password";
	        }
	    }
	
	    return $err;
	}
	
	protected function multi_strpos($haystack, $needles, $offset = 0) {
	    foreach ($needles as $n) {
	        if (strpos($haystack, $n, $offset) !== false)
	            return strpos($haystack, $n, $offset);
	    }
	    return false;
	}

	protected function htmlEncode($s) {
		$key_words = array('information_schema','UNION','CAST','column_name','--','\\','SLEEP','SCHEMA','SELECT','SYSDATE','javascript');
		$s = str_replace($key_words, '', $s);

	    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
	}
	
	public function login_gmail(){

	    $id_token = isset($_POST['id_token']) ? $_POST['id_token'] : "";
	    $errMsg = "";
	    
	    $return = array();

	    if(!empty($id_token)){

	        $server_ip = "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=".$id_token;

	        $ch = curl_init(); 
	         
	        curl_setopt_array ( $ch, array (
    	        CURLOPT_URL => $server_ip,
    	        CURLOPT_RETURNTRANSFER => true,
    	        CURLOPT_HTTPAUTH => CURLAUTH_ANY,
    	        CURLOPT_COOKIESESSION => false,
    	        )
	        );
	        
	        $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
	         
	        curl_setopt ( $ch, CURLOPT_USERAGENT, $agent );
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        $status_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
	        $output = curl_exec ( $ch );
	        curl_close ( $ch );

            
            $resultObject = json_decode($output);

            if(isset($resultObject->email_verified) && $resultObject->email_verified){
                
                $email = $resultObject->email;
                $login_id = $this->user_model->checkEmail($email);

                if($login_id){
                     
                    $user = $this->user_model->login_gmail($login_id);
                
                    if($user!=null) {
                
                        $logData = array('emp_id'=>$login_id,"activity"=>"L","affected"=>"","log_text"=>"", "log_time"=> date('Y-m-d H:i:s'));
                        $this->user_model->setLog($logData);
                
                        $ses['emp_id'] = $user['emp_id'];
                        $ses['name'] = $user['name'];
                        $ses['lock'] = $user['active'];
                        $ses['archive'] = $user['archive'];
                        $ses['dept_name'] = $user['dept_name'];
                        $ses['dept_code'] = $user['dept_code'];
                        $ses['designation'] = $user['designation'];
                        $ses['gender'] = $user['gender'];
                        $ses['email'] = $user['email'];
                        $ses['image'] = $this->getImagePath($user['image']);
                        $ses['login_by_gmail'] = true;
                         
                        $this->SetSessionSettings();
                         
                        if($this->session->IsAdmin($login_id)) {
                            $ses['uType'] = 'A';
                        } else if ($this->session->IsManagement($login_id)) {
                            $ses['uType'] = 'B';
                        } else if($this->session->IsManager($login_id)) {
                            $ses['uType'] = 'M';
                        } else {
                            $ses['uType'] = 'E';
                        }
                         
                        if($ses['lock']=='U' && $ses['archive']=='N') {
                            $this->session->SetSessionData($ses);
                
                            $data=array('online'=>'Y','login_time'=>date('Y-m-d H:i:s'));
                            $this->user_model->setOnline($data, $login_id);
                
                            $lastPage = $this->session->get_lastpage();
                
                            if(empty($lastPage)) {
                                $go_url = base_url()."user/detail/".$ses['emp_id'];
                                //redirect(base_url()."user/detail/".$ses['emp_id']);
                            } else {
                                $go_url = $lastPage;
                                //redirect($lastPage);
                            }
                            
                            $return['status'] = true;
                            $return['msg'] = "Login Successfully.";
                            $return['go_url'] = $go_url;
                            $return['output'] = $output;
                            
                            echo json_encode($return);
                            die;
                                                                        
                        } else if($ses["lock"]=="L") {
                             
                            $errMsg = "Sorry, You are locked by admin";
                             
                        } else {
                             
                            $errMsg = "Sorry, You are now in archive";
                        }
                    } else{
			$this->user_model->addFailedLog($login_id);
                        $errMsg = 'User ID or Password Wrong!';
                    }
                     
                }else{
                    $this->user_model->addFailedLog($login_id);	
                    $errMsg = "This Gmail is not integrated to EMS yet.";
                }
                
            }else{
                $errMsg = "Some errors ocurr. Try Again.";
                
            }
	    }
	    
	    $return['status'] = false;
	    $return['msg'] = $errMsg;	    
	    echo json_encode($return);
	    
	    die;
	    
// 	    $data = array();
// 	    $data["login_id"] = "";
// 	    $data["password"] = "";
// 	    $data["errMsg"] = $errMsg;
// 	    $this->load->view('login', $data);	
	}

	public function delete_gmail($eid = NULL){
		if($eid == "") redirect(base_url()."user/login");
		$flag = $this->user_model->delete_gmail($eid);

		redirect(base_url()."user/detail/".$eid);
	}
	
	public function login_dhakatel($user_name = NULL, $pass = NUlL){

		if($user_name == "" || $pass == "") redirect(base_url()."user/login");

	    $this->load->library('simple_html_dom');
	    //require_once ('simple_html_dom.php');
	    $server_ip = 'http://www.dhakatel.com/cgi-bin/gswebmail/gswebmail.pl';
	    $ch = curl_init();
	    $fields = array( 'browserjavascript'=>'dom',
	        'httpcompress'=>'1',
	        'loginbutton'=>'Login',
	        'logindomain'=>'Dhakatel.com',
	        'loginname'=>$user_name,
	        'password'=>$pass
	    
	    );
	    $postvars = '';
	    foreach($fields as $key=>$value) {
	        $postvars .= $key . "=" . $value . "&";
	    }
	    curl_setopt_array ( $ch, array (
	    CURLOPT_URL => $server_ip,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_HTTPAUTH => CURLAUTH_ANY,
	    CURLOPT_COOKIESESSION => false,
	    
	    )
	    );
	    
	    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
	    curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
	    curl_setopt ( $ch, CURLOPT_USERAGENT, $agent );
	    $status_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
	    $output = curl_exec ( $ch );
	    curl_close ( $ch );
	    
	    $html = str_get_html($output);
	    
	    $success = false;
	    foreach($html->find('a.loading') as $element)
	    {
	        $success = true ;
	    
	    }
	    
	    return $success;
	}
	
	public function logout()
	{
	    $isClean = $this->session->ClearSessionData();
	    if($isClean) {
	        header("Location: ".base_url());
	    }
	
	    $data=array('online'=>'N');
	    $this->user_model->setOnline($data, $this->data["myInfo"]->userId);
	}
	
    protected function SetSessionSettings($loggedEmpId='')
	{
		$settings = $this->user_model->settings();
		$rosterPrivEmpId = $this->user_model->hasRosterPriv($loggedEmpId);
		$this->session->SetRosterSession($rosterPrivEmpId);
			
		$managers=array();
		$manager_ary=array();
		$admin_ary=array();
		$management_ary=array();
		
		foreach($settings as $row) {
		    
			$conf_emp_id = $row->emp_id;
			$conf_type = $row->type;
			$conf_dept_code = $row->dept_code;
			if($conf_type=="M") {
				$managers[]=$conf_emp_id;
				$manager_ary[$conf_dept_code][]=$conf_emp_id;
				$manager_depts[$conf_emp_id][] = $conf_dept_code;
			} else if($conf_type=="A") {
				$admin_ary[]=$conf_emp_id;
			} else if($conf_type=="B") {
				//$managers[]=$conf_emp_id;
				//$manager_ary[$conf_dept_code][]=$conf_emp_id;
				//$admin_ary[]=$conf_emp_id;
				$management_ary[]=$conf_emp_id;
			}
		}
		$settings = array(
				"manager"=>$managers,
				"dept_n_manager"=>$manager_ary,
				"manager_n_dept"=>$manager_depts,
				"admin"=>$admin_ary,
				"management"=>$management_ary
		);
		
		$this->session->SetSessionSettings($settings);
	}
	
	
	public function forgot(){
	    
	    if(!empty($this->myEmpId)){
	        redirect(base_url()."user/detail/".$this->myEmpId);
	    }
	    
	    $data['msg'] = isset($_POST['msg']) ? $_POST['msg']: "";
	    
	    $this->load->view('forgot_password', $data);
	}
	
	public function password(){
	    
	    if(!empty($this->myEmpId)){
	        redirect(base_url()."user/detail/".$this->myEmpId);
	    }
	    
	    $conditon = array();
	    $conditon['emp_id'] = isset($_POST['employeeId']) ? $_POST['employeeId'] : "";
	    $conditon['email'] = isset($_POST['emailId']) ? $_POST['emailId'] : "";
	    
	    $time_key = $this->user_model->add_key($conditon);
	    
	    if(!empty($time_key)){
		$this->load->library('mailer');
	        
	        //sent a mail
	        $receiver = array();
	        $receiver[] = $this->user_model->getBriefInfo($conditon['emp_id']);

	        $sender = array();
	        $sender['name'] = "EMS";
	        $sender['email'] = "ems@genuitysystems.com";
	        
	        $mail = array();
	        $mail['subject'] = "Reset Your Password";
	        
	        $url =  "<a href='".$this->web_url."user/reset_pass/?key=".$time_key."'>".$this->web_url."user/reset_pass/?key=".$time_key."</a>";
	        $text = "<h3>To reset your password simply follow the link:</h3><br>"
	                .$url."<br><h3>Otherwise ingnore it.</h3>";
	        $mail['body'] = $text;
	        $mail['receiver'] = $receiver;
	        $mail['sender'] = $sender;
	        $mail['web_url'] = $this->web_url;
	        
	        
	        if ($this->mailer->sendEmail($mail)) {
	            $return['msg'] = "A confirmation mail has been sent to $conditon[email] with instruction on how to to reset your password.";
	        } else {
	            $return['msg'] = "something went wrong"." and ".$this->message['mail_f'];
	            //clear key and key_date
	            $this->user_model->vanish_key($conditon);
	        }

	        $return['status'] = true;
	        
	    }else{
	        $return['status'] = false;
	        $return['msg'] = "We cant't find an account with that Employee ID and email! try another.";
	    }
	    
	    echo json_encode($return);
	}
	
	public function reset_pass(){
	    
	    if(!empty($this->myEmpId)){
	        redirect(base_url()."user/detail/".$this->myEmpId);
	    }
	    
	    $key = isset($_GET['key']) ? $_GET['key'] : "";
	    
	    $pass1 = isset($_POST['newPassword']) ? $_POST['newPassword'] : "";
	    $pass2 = isset($_POST['retypePassword']) ? $_POST['retypePassword'] : "";

	    $ary = $this->user_model->get_key_info($key);
	    	
	    $data['isKeyMatch'] = false;
	    $data['isToTime'] = false;
	    
	    if(count($ary) > 0){
	        //key matches to databse key
	        $data['isKeyMatch'] = true;	        
	        $key_date = $ary['key_date'];
	        $emp_id = $ary['emp_id'];	        
	        $now = date("Y-m-d H:i:s");
	        $diff = strtotime($now) - strtotime($key_date);
	        
	        $conditon['emp_id'] =$emp_id;
	        $conditon['key'] = $key;
	        
	        if($diff <= 1800){
	            //Time difference <= 30min
	            $data['isToTime'] = true;
	            
	            if(count($_POST) > 0){
	                //new password is posted.
	                $flag = false;
	                if(!empty($pass1) && $pass1 == $pass2){
	                    $flag = $this->user_model->updatePassword($emp_id, $pass1);
	                }
	                 
	                if($flag){
	                    $message['status'] = true;
	                    $message['msg'] = "Your password has been changed successfully.";
	                     
	                    //clear key and key_date
	                    $this->user_model->vanish_key($conditon);
	                    $affectedTxt = "emp_id=$emp_id, pass: " .  "**" . substr($pass1, -1) . "(".strlen($pass1).")";
	                    $this->addActivityLog('U', $affectedTxt, 'Reset Password', $emp_id);
	                }else{
	                    $data['key'] = $key;
	                    $message['status'] = false;
	                    $message['msg'] = "Somthing's wrong. try again.";
	                }
	                $data['message'] = $message;
	                $this->load->view('reset_password', $data);

	            }else{
	                $data['key'] =$key;
	                $this->load->view('reset_password', $data);
	            }
	            
	        
	        } else{
	            //Time difference > 30min
	            //clear key and key_date
	            $this->user_model->vanish_key($conditon);
	            
	            $message['status'] = false;
	            $message['msg'] = "It's too late to reset password. Password reset links has expired. Try again.";
	            $data['message'] = $message;
	            $this->load->view('reset_password', $data);
	        }
	    } else{
	        //key does't match to databse key	        
	        $message['status'] = false;
	        $message['msg'] = "Your password reset links is ambiguous. Try again later.";
	        $data['message'] = $message;
	        $this->load->view('reset_password', $data);
	    }

	}
		
	public function show() {
		
		$this->isLoggedIn();
		
		if($this->uri->segment(4)){
			$page = $this->uri->segment(4);
		} else{
			$page = 1;
		}
		
		$req_dept = $this->uri->segment(3) ? $this->uri->segment(3) : "all";

		$this->data["base_url"] = base_url() . "user/show/$req_dept/";
		$total_row = $this->user_model->record_count($req_dept);
		$this->data["total_rows"] = $total_row;
		$this->data["per_page"] = ROWS_PER_PAGE;
		$this->data["offset"] = ($page - 1) * ROWS_PER_PAGE;
		$this->data['use_page_numbers'] = TRUE;
		$this->data['num_links'] = $total_row;
		$this->data['cur_tag_open'] = '&nbsp;<a class="current">';
		$this->data['cur_tag_close'] = '</a>';
		$this->data['next_link'] = 'Next';
		$this->data['prev_link'] = 'Prev';
		$this->data["uri_segment"] = 4;
		
		$this->pagination->initialize($this->data);
		
		$array = array(
		    'dept_code'=>$req_dept,
		    'offset'=>$this->data["offset"],
		    'select'=>array(
		        'e.emp_id',
		        'e.name',
		        'e.status',
		        'e.jdate',
		        'e.dept_code',
		        'dp.dept_name',
		        'ds.designation'
		    )
		);
		

		$users = $this->user_model->get_user($array);

		$this->data['users'] = $users;
		
		$this->data['allUsers'] = $this->user_model->get_emp_search($req_dept);
		$str_links = $this->pagination->create_links();
		$this->data["links"] = explode('&nbsp;',$str_links );
		
		 
		$this->data["status_array"] = $this->status_array;
		$this->data["title"] = $req_dept;
		$this->data["sub_title"] = "Employee List";

		$this->load->view('user', $this->data);
	}
	
	public function detail($emp_id = "") 
	{
	    
		$this->isLoggedIn();
		
		if(empty($emp_id)) $emp_id = $this->myEmpId;		
		$user = $this->user_model->detail($emp_id);		

		if(empty($user)) redirect("user/login");

		$this->data['facilities'] = $this->user_model->empFacility($emp_id);
		$this->data['facility_array'] = $this->user_model->facility();
		$this->data['status_array'] =  $this->status_array;		
		$this->data['status_history'] = $this->user_model->getStatusHistory($emp_id);
		$this->data['grade_list'] = $this->user_model->getGradeList();
		$this->data['grades'] = $this->user_model->getGrades();

		$this->data['id'] = $user['id'];
		$this->data['emp_id'] = $user['emp_id'];
		$this->data['name'] = $user['name'];
		$this->data['grade'] = $user['grade'];
		$this->data['desig'] = $user['designation'];
		$this->data['dept_code'] = $user['dept_code'];
		$this->data['dept'] = $user['dept_name'];
		$this->data['jdate'] = $user['jdate'];
		$this->data['status'] = $user['status'];
		
		$this->data['mobile'] = $user['mobile'];
		$this->data['phone'] = $user['phone'];
		$this->data['email'] = $user['email'];
		$this->data['pre_address'] = $user['present_address'];
		$this->data['per_address'] = $user['permanent_address'];
		$this->data['last_edu_achieve'] = $user['last_edu_achieve'];
		$this->data['dob'] = $user['dob'];
		$this->data['gen'] = $user['gender'];
		$this->data['gender_array'] = $this->gender_array;
		$this->data['ArchiveV'] = $user['archive'];	
		$this->data['resign_date'] = $user['resignation_date'];
		$this->data['blood_group'] = $user['blood_group'];
		$this->data['image'] = $user['image'];
		$this->data['gmail'] = $user['gmail'];
		//echo $user['image'];
		//Right Panel
		$this->data['image_path'] = $this->getImagePath($user['image']);
		//echo $this->data['image_path'];
		$this->data['activeLock'] = $user['active'];
		$this->data["online"] = $user['online'];		
		$this->data["gslLife"] = $this->getLife();
		$login_time =  empty($user['login_time']) || $user['login_time'] == "0000-00-00 00:00:00"  ? date("Y-m-d H:i:s") : $user['login_time'];			
		$this->data["loginDay"] = $this->getLoginDay($login_time);	
		$this->data["loginTime"] = $this->getLoginTime($login_time);
		$this->data["unread_notice"] = $this->user_model->unreadNotice($this->data['emp_id']);
		$this->data["unread_attach"] = $this->user_model->unreadAttach($this->data['emp_id']);
		
		
		$this->data["title"] = $this->data['dept_code'];
		
		if($this->myEmpId == $this->data['emp_id']){

		    $this->data['isPanelMenuOpen'] = false;
		}
		
		
		$this->data["sub_title"] = "Employee Detail";
		$this->data["notSelect"] = true;
		
		$this->load->view('user_detail', $this->data);
	}
	

	
	public function add($dept_code = NULL)
	{
	    if(!$this->data['isAdmin'] || $dept_code == "") {
			redirect(base_url()."user/login");
			//$this->load->view('not_found', $this->data);
	        //return;
	    }
	     
	    $dept_code = !empty($dept_code) ? $dept_code : "all";
	    
		$this->data['designations'] = $this->user_model->designation();
		$this->data['grades'] = $this->user_model->grade();
// 		$this->data['emp_ids'] = $this->user_model->get_all_emp_id();

		$info = new stdClass();
		$info->id = "";
		$info->emp_id = "";
		$info->name= "";
		$info->grade_id= "";
		$info->designation_id= "";
		$info->designation= "";
		$info->dept_code= $dept_code;
		$info->department= "";
		$info->jdate= "";
		$info->status= "";
		$info->mobile= "";
		$info->phone= "";
		$info->email= "";
		$info->pre_address= "";
		$info->per_address= "";
		$info->last_edu_achieve= "";
		$info->dob= "";
		$info->gen = "";
		$info->blood_group="";
		$info->image= "";
		$info->image_path= "#";
		
		$this->data['title'] = $dept_code;
		$this->data['sub_title'] = "Employee Detail";
		$this->data['data'] = $info;
		$this->data['status_array'] = $this->status_array;
		$this->data['blood_group_array'] = $this->blood_group_array;
		$this->data['gender_array'] = $this->gender_array;
		
		$this->load->view('user_edit', $this->data);
	}
	
	public function edit($emp_id = NULL, $pData = array())
	{
		$isExists = $this->user_model->isEmployeeExists($emp_id);
		if(!$isExists || $emp_id == ""){
			redirect(base_url()."user/login");
		}

	    $this->data['designations'] = $this->user_model->designation();
	    $this->data['grades'] = $this->user_model->grade();
	    $this->data['status_array'] = $this->status_array;
	    $this->data['blood_group_array'] = $this->blood_group_array;
	    $this->data['gender_array'] = $this->gender_array;
	    
	    $this->data['sub_title'] = "Employee Detail";
	    
		if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }
        
        if(empty($emp_id) && count($pData) > 0){
            
            $pData['designation'] = $this->input->post('designation');
            
            $info = new stdClass();
            $info->id = $pData['id'];
            $info->emp_id = $pData['emp_id'];
            $info->name= $pData['name'];
            $info->grade_id= $pData['grade_id'];
            
            $info->designation_id= $pData['designation'];
            //$info->designation= $pData['designation'];
            $info->dept_code= $pData['dept_code'];
            //$info->department= $pData['dept_name'];
            
            $info->jdate= $pData['jdate'];
            $info->status= $pData['status'];
            $info->mobile= $pData['mobile'];
            $info->phone= $pData['phone'];
            $info->email= $pData['email'];
            $info->pre_address= $pData['present_address'];
            $info->per_address= $pData['permanent_address'];
            $info->last_edu_achieve= $pData['last_edu_achieve'];
            $info->dob= $pData['dob'];
            $info->gen = $pData['gender'];
            $info->blood_group= $pData['blood_group'];
            $info->image= $pData['image'];
            $info->image_path= $this->getImagePath($pData['image']);
            
            
            
            $this->data['data'] = $info;
            $this->data['title'] = $pData['dept_code'];
            //$this->load->view('user_edit', $this->data);
                        
        } else{
            
            $user = $this->user_model->detail($emp_id);
            
            $info = new stdClass();
            $info->id = $user['id'];
            $info->emp_id = $user['emp_id'];
            $info->name= $user['name'];
            $info->grade_id= $user['grade_id'];
            $info->designation_id= $user['ds_id'];
            $info->designation= $user['designation'];
            $info->dept_code= $user['dept_code'];
            $info->department= $user['dept_name'];
            $info->jdate= $user['jdate'];
            $info->status= $user['status'];
            $info->mobile= $user['mobile'];
            $info->phone= $user['phone'];
            $info->email= $user['email'];
            $info->pre_address= $user['present_address'];
            $info->per_address= $user['permanent_address'];
            $info->last_edu_achieve= $user['last_edu_achieve'];
            $info->dob= $user['dob'];
            $info->gen = $user['gender'];
            $info->blood_group= $user['blood_group'];
            $info->image= $user['image'];
            $info->image_path= $this->getImagePath($user['image']);
            
            $this->data['data'] = $info;
            $this->data['title'] = $user['dept_code'];
            
            //$this->load->view('user_edit', $this->data);
            //exit;
            
        }	 
        $this->load->view('user_edit', $this->data);
		
		//die;
	}
	
	Public function updateEmployee(){
		if(strtoupper($_SERVER['REQUEST_METHOD']) != "POST") redirect(base_url()."user/login");
		if(!$this->data['isAdmin']) {
            $this->load->view('not_found', $this->data);
            return;
        }

        $image =  $this->input->post('imageH');
        $uploadFlag = false;
        $config = array(
            'upload_path'     => './assets/pictures/',
            'allowed_types'   => 'jpg|jpeg|png|gif',
            'file_name'       => $this->input->post('emp_id'),
            'overwrite'       => TRUE,
            'max_size'        => "1000KB",
            'max_height'      => "768",
            'max_width'       => "1024"
        );
                
        $this->load->library('upload', $config);
        if(!$this->upload->do_upload('uimage')) {
            $this->data['error'] = $this->upload->display_errors();
            echo "Image upload failed.";
        }else{
            //success
            $filename = './assets/pictures/'.$image;
            if (file_exists($filename)) {
                //echo 'file exits';
                $uploadFlag = true;
            }
            
            $data_upload_files = $this->upload->data();
            $image = $data_upload_files['file_name'];                        
        }       
        	        
	    $id = $this->input->post('empInfo_id');
	    $pData['name'] = $this->input->post('name');
	    $pData['emp_id'] = $this->input->post('emp_id');
	    $pData['grade_id'] = $this->input->post('grade');
	    $pData['dept_code'] = $this->input->post('dept_code');
	    $pData['designation'] = $this->input->post('designation');
	    $pData['jdate'] = $this->input->post('jdate');
	    $pData['status'] = $this->input->post('status');
	    $pData['mobile'] = $this->input->post('mobile');
	    $pData['phone'] = $this->input->post('phone');
	    $pData['email'] = $this->input->post('email');
	    $pData['present_address'] = $this->input->post('pre_address');
	    $pData['permanent_address'] = $this->input->post('per_address');
	    $pData['last_edu_achieve'] = $this->input->post('last_edu_achieve');
	    $pData['dob'] = $this->input->post('dob');
	    $pData['blood_group'] = $this->input->post('blood_group');
	    $pData['gender'] = $this->input->post('gender');
	    $pData['image'] = $image;
	    

        if(empty($id)){
            
            //Add employee
            
            $password = $this->passwordGenerator();
            $flag = $this->user_model->addEmp($pData, $password);
            

            if($flag){                
                
                
                // add log
                $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"A","affected"=>$pData['emp_id'],"log_text"=>"New employee '".$pData['name']."' has been added to EMS.", "log_time"=> date('Y-m-d H:i:s'));
                $this->user_model->setLog($logData);
                
                $nData = array();
                $department = "";
                
                foreach ($this->data['departments'] as $dept_code=>$dept_name){
                    if($pData['dept_code'] == $dept_code){
                        $department = $dept_name;
                    }
                }
                
                $Des = $this->user_model->designation();
                foreach ($Des as $obj) {
                    if ($pData['designation'] == $obj->id){
                        $designation = $obj->designation;
                    }
                }
                $addressing_1 = ($pData['gender'] == "M") ? 'he':'she';
		$addressing_2 = ($pData['gender'] == "M") ? 'him':'her';
		$addressing_3 = ($pData['gender'] == "M") ? 'his':'her';
		$nFlag = true;
		$nData['notice_id'] = "";
		$nData['emp_id'] = $pData['emp_id'];
		$nData['subject'] = 'Reference: GSL/ HR & Admin/ Joining of New Employee.';
		$nData['notice'] = '<p class="MsoNormal" style="text-align:justify;line-height:150%"><b style="mso-bidi-font-weight:normal"><span style="font-size:10.0pt;line-height:
                    150%;font-family:&quot;Verdana&quot;,&quot;sans-serif&quot;">Dear Colleagues,</span></b></p>
                    
                    <p class="MsoNormal" style="text-align:justify;line-height:150%">
                    <span style="font-size:10.0pt;line-height:150%;font-family:&quot;Verdana&quot;,&quot;sans-serif&quot;">
                    It is with great pleasure we announce that
                    <b style="mso-bidi-font-weight:normal">'.$pData["name"].'</b>,
                    '.$designation.',
                    '.$department.'
                    has joined as a new employee to Genuity Systems Ltd. We are very pleased that '.$addressing_1.' has chosen to accept our offer of employment. 
		    We know that this is the beginning of a mutually beneficial association.</span></p>
                    
                    <p class="MsoNormal" style="text-align:justify;line-height:150%">
                    <span style="font-size:10.0pt;line-height:150%;font-family:&quot;Verdana&quot;,&quot;sans-serif&quot;">
                    We hope that '.$addressing_1.' will experience cooperative and friendly association with the rest
                    of the staff. We congratulate '.$addressing_2.' and welcome '.$addressing_2.' in our company and wish best luck
                    in '.$addressing_3.' career with Genuity.
                    </span></p>
                    
                    <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto">
                    	<span style="font-size:10.0pt;font-family:&quot;Verdana&quot;,&quot;sans-serif&quot;">Best regards,&nbsp;<br>'.$this->data["myInfo"]->userName.'<br>'.$this->data["myInfo"]->userDesignation.', '.$this->data["myInfo"]->userDepartment.'</span>
                    </p>';

		$this->load->library('../controllers/remark');
		$needToRedirect = false;
		$this->remark->updateNotice($nData, $nFlag, $needToRedirect);
                
                /* sent mail staff or creator about password */
                $mail = array();
                $myInfo = $this->data['myInfo'];
                $empInfo = new stdClass();
                
                $mail["empInfo"]  = $this->user_model->getBriefInfo($pData['emp_id']);                
                if(!empty($pData['email']) && filter_var($pData['email'], FILTER_VALIDATE_EMAIL)){
                    $receiver = new stdClass();
                
                    $receiver->name = $pData['name'];
                    $receiver->emp_id = $pData['emp_id'];
                    $receiver->email = $pData['email'];
                
                    $mail['receiver'] = array($receiver);                    
                    $mail['msg_1'] = "<h3>Hi ".$pData['name'].",</h3><p>You have been added to EMS. Your brief information:</p>";
                    $mail['msg_2'] = "Change the password after a successfull login using these credentials.";
                }else{
                
                    $receiver->name = $myInfo->userName;
                    $receiver->emp_id = $this->myEmpId;
                    $receiver->email = $myInfo->email;
                
                    $mail['receiver'] = array($receiver);
                    $mail['msg_1'] = "<h3>Hi ".$myInfo->userName.",</h3><p>A new employee has been added to EMS. His brief information:</p>";
                    $mail['msg_2'] = "Give him/her these Credential information and suggest him/her to change the password after a successfull login using these credentials.";
                }
                
                $mail['subject'] = "Credential Information of New Employee.";

	            if( $this->remark->mailer->passwordMail($mail, $password, $this->web_url) ){
                    
                }else{
                    // message page showing password and link back
                    $this->data["title"] = "Employee Addition";
                    $this->data["sub_title"] = "Employee Addition";
                    $this->data['message'] = "<span class='text-warning'>Sending Mail is failed.<span> <br>Please note the credential Info: <br> User Id: ".$pData['emp_id']." <br> Password :".$password;
                    
                    $link =array();
                    $link['href'] = base_url().'user/detail/'.$pData['emp_id'];
                    $link['text'] = 'Go back to Employee details page';
                    $this->data['link'] = $link;
                     
                    $this->view('message_view', $this->data);
                    return;
                }
                
            }
        } else {
            //Update Employee
            $oldData['name'] = $this->input->post('nameH');
            $oldData['emp_id'] = $this->input->post('emp_idH');
            $oldData['grade_id'] = $this->input->post('grade_idH');
            $oldData['dept_code'] = $this->input->post('dept_codeH');
            $oldData['designation'] = $this->input->post('designation_idH');
            $oldData['jdate'] = $this->input->post('jdateH');
            $oldData['status'] = $this->input->post('statusH');
            $oldData['mobile'] = $this->input->post('mobileH');
            $oldData['phone'] = $this->input->post('phoneH');
            $oldData['email'] = $this->input->post('emailH');
            $oldData['present_address'] = $this->input->post('pre_addressH');
            $oldData['permanent_address'] = $this->input->post('per_addressH');
            $oldData['last_edu_achieve'] = $this->input->post('last_edu_achieveH');
            $oldData['dob'] = $this->input->post('dobH');
            $oldData['gender'] = $this->input->post('genH');
            $oldData['blood_group'] = $this->input->post('blood_groupH');
            
            
            $flag = $this->user_model->updateEmp($id, $pData);
            
            $keys = array('name', 'emp_id', 'grade_id', 'dept_code', 'designation', 'jdate', 'status', 'mobile', 'phone', 'email', 'present_address', 'permanent_address', 'last_edu_achieve', 'dob', 'gender', 'blood_group');
            $isSame = true;
            foreach ($keys as $value){
                if($pData[$value] != $oldData[$value]) {
                    $isSame = false;
                    break;
                }
            }
            
            $log_text = "";          
            if($uploadFlag){
                $log_text .= "picture Changed";
                echo $log_text;
                $flag = true;
            }
            
            if($isSame && !$flag){
                redirect(base_url().'user/detail/'.$pData['emp_id']);
            }
            
            if($flag){            
                
                foreach ($keys as $value){
                    if($pData[$value] != $oldData[$value]) { $log_text .= $value.":".$oldData[$value].'=>'.$pData[$value].'; ';}
                }
                $logData = array('emp_id'=>$this->data["myInfo"]->userId, "activity"=>"U", "affected"=>$pData['emp_id'], "log_text"=>$log_text, "log_time"=> date('Y-m-d H:i:s'));
                $this->user_model->setLog($logData);
            }else{
                
                $pData['id'] = $id;
                $this->edit("", $pData);
                
            }
            
        }
	    
	    if($flag){
	        //Succesfull	        
	        redirect(base_url().'user/detail/'.$pData['emp_id']);	        

	    }else {
	        //Unsuccesfull
	        echo "failed";
	    }
	}
	
	public function deleteEmp($id = NULL){
		$isExists = $this->user_model->isEmployeeExists($id);
		if($id == "" || !$isExists) redirect(base_url()."user/login");

		if(!$this->data['isAdmin']) {
			$this->load->view('not_found', $this->data);
			return;
        }
	    
    
	    $flag = $this->user_model->deleteEmp($id);
	    
	    $req_dept = $this->uri->segment(4) ? $this->uri->segment(4) : "all";
	    if($flag){
	        //Succesfull
	        //add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId, "activity"=>"D", "affected"=>$id, "log_text"=>"Employee($id) Deleted", "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);

	        redirect(base_url().'user/show/'.$req_dept);
	    }else {
	        //Unsuccesfull
	        echo "failed";
	    }
	}
	
	public function lock($emp_id = NULL) {

		$isExists = $this->user_model->isEmployeeExists($emp_id);
		if(empty($emp_id) || !$isExists) redirect(base_url().'user/login/');

		if(!$this->data['isAdmin']) {
			$this->load->view('not_found', $this->data);
			return;
        }
	    
	    
	    $pData['active'] = $this->input->post('active');
	    $oldActve = ($pData['active'] == 'U') ? 'L':'U';
	    
	    $flag = $this->user_model->lockArchive($emp_id, $pData);
	    $data = array();
	    if($flag){
	        //Succesfull
	        //add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId, "activity"=>"U", "affected"=>$emp_id, "log_text"=>"Employee($emp_id) status changed :: active: ".$oldActve.'=>'.$pData['active'], "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	        $data["status"] = true; 
	    } else {
	        //Unsuccesfull
	        $data["status"] = false;
	        $data["message"] = "Update to change Lock/Unlock Failed due to some technical error.";
	    }
	   echo json_encode($data);
	}
	
	public function changeArchive($emp_id = NULL) {
		$this->isLoggedIn();
		$isExists = $this->user_model->isEmployeeExists($emp_id);
		if(!$isExists) redirect(base_url()."user/login");

		if(!$this->data['isAdmin']) {
			$this->load->view('not_found', $this->data);
			return;
        }
	    
	    
	    $date = $this->input->post('resDate');	    
	    $date = $date.date(' h:i:s'); 
	    $data['resignation_date'] = $date;
	    $data['archive'] = 'Y';
	    $flag = $this->user_model->lockArchive($emp_id, $data);
	    
	    if($flag){
	        //Succesfull
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId, "activity"=>"U", "affected"=>$emp_id, "log_text"=>"archived", "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	        redirect(base_url().'user/archive');
	    }else {
	        //Unsuccesfull
	        echo "Update to archive this employee Failed due to some technical error.";
	    }
	}

	
	public function archive(){
	    $this->isLoggedIn();
	    	    
	    $this->data["menu"] = "Archive";
	    $this->data["title"] = "Archive";
	    $this->data["sub_title"] = "Archive All";
		
		if($this->uri->segment(4)){
			$page = $this->uri->segment(4);
		} else{
			$page = 1;
		}
		
		$this->data["base_url"] = base_url() . "user/archive/all/";
		$total_row = $this->user_model->archive_count();
		$this->data["total_rows"] = $total_row;
		$this->data["per_page"] = ROWS_PER_PAGE;
		$this->data["offset"] = ($page - 1) * ROWS_PER_PAGE;
		$this->data['use_page_numbers'] = TRUE;
		$this->data['num_links'] = $total_row;
		$this->data['cur_tag_open'] = '&nbsp;<a class="current">';
		$this->data['cur_tag_close'] = '</a>';
		$this->data['next_link'] = 'Next';
		$this->data['prev_link'] = 'Prev';
		$this->data["uri_segment"] = 4;
		$this->data['users'] = $this->user_model->get_archive($this->data["offset"]);
		 
		$this->pagination->initialize($this->data);
		$str_links = $this->pagination->create_links();
		$this->data["links"] = explode('&nbsp;',$str_links );

		$this->load->view('archive_person', $this->data);
	} 
	
	public function facilities($emp_id = NULL){
	    $this->isLoggedIn();
	
	    //$this->load->model('user_model');
	    $user = $this->user_model->detail($emp_id);
	    $this->data['facilities'] = $this->user_model->empFacility($emp_id);
	    $this->data['facility_array'] = $this->user_model->facility();
	
	    $this->load->view('user_facilities', $this->data);
	}
	
	public function update_facility(){
	    
		if(!$this->data['isAdmin']) {
			redirect(base_url()."user/login");
			//$this->load->view('not_found', $this->data);
			//return;
        }

	    $facilityPo = isset($_POST['facility']) ? $_POST['facility'] : "";
	    $fromDatePo = isset($_POST['fromDate']) ? $_POST['fromDate'] : "";
	    
	    if( empty($facilityPo) || empty($fromDatePo))  {
	        
	        exit(json_encode(array("status"=>false,"msg"=>"Empty Field")));
	    }
	    
	    $id = $this->input->post('f_id');
	    $data['emp_id'] = $this->input->post('empID');
	    $data['facility_id'] = $facilityPo;
	    $data['from_date'] = $fromDatePo.date(' h:i:s');
	    $data['remark'] = isset($_POST['remark']) ? $_POST['remark'] : "";	    
	    $data['to_date'] = isset($_POST['toDate']) ? $_POST['toDate'] : "0000-00-00 00:00:00";	    

	    if(empty($id)){
	        //Add Facility
	        $flag = $this->user_model->addFacility($data);
	        
	        if ($flag){
	            // add log
	            $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"A","affected"=>$data['emp_id'],"log_text"=>"facilities=>facility_id: ".$data['facility_id'], "log_time"=> date('Y-m-d H:i:s'));
	            $this->user_model->setLog($logData);
	        }
	        
	    } else {
	        //generate log text
	        $facilities = $this->user_model->empFacilityByID($id);
	        $oF_date = date('Y-m-d', strtotime($facilities['from_date']));
	        $oT_date = date('Y-m-d', strtotime($facilities['to_date']));
	        $f_date = date('Y-m-d', strtotime($data['from_date']));
	        $t_date = date('Y-m-d', strtotime($data['to_date']));
	        
	        $keys = array('facility_id', 'from_date', 'to_date', 'remark');
	        $log_text = "facilities=>";
	    
	        if($f_date != $oF_date) { $log_text .= 'from_date: '.":".$facilities['from_date'].'=>'.$data['from_date'].'; ';}
	        if($t_date != $oT_date) { $log_text .= 'to_date: '.":".$facilities['to_date'].'=>'.$data['to_date'].'; ';}
	        if($data['facility_id'] != $facilities['facility_id']) { $log_text .= "facility_id:".$facilities['facility_id'].'=>'.$data['facility_id'].'; ';}
	        if($data['remark'] != $facilities['remark']) { $log_text .= "remark:".$facilities['remark'].'=>'.$data['remark'].'; ';}
	        //end
	        
	        //Update Facility
	        $flag = $this->user_model->updateFacility($id, $data);
	        
	        if ($flag){
	            // add log
	            $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"U","affected"=>$data['emp_id'],"log_text"=>$log_text, "log_time"=> date('Y-m-d H:i:s'));
	            $this->user_model->setLog($logData);
	        }
	    }
	     
		if($flag){
	        $res["status"] = true; 
	    } else {
	        $res["status"] = false;
	        $res["message"] = "Adding/Updating facility Failed due to some technical error.";
	    }
	    echo json_encode($res);
	}
	
	public function delete_facility(){
	    
		if(!$this->data['isAdmin']) {
			redirect(base_url()."user/login");
        	//$this->load->view('not_found', $this->data);
        	//return;
        }
	    
	    $id = $this->input->post('fac_id');
	    $affected_id = $this->input->post('emp_id');
	    
	    $flag = $this->user_model->deleteFacility($id);
	    
	    if($flag){
	        $data["status"] = true;
	        // add log
	        $logData = array('emp_id'=>$this->data["myInfo"]->userId,"activity"=>"D","affected"=>$affected_id,"log_text"=>"facilities=>id:".$id, "log_time"=> date('Y-m-d H:i:s'));
	        $this->user_model->setLog($logData);
	    } else {
	        $data["status"] = false;
	        $data["message"] = "Deleting facility Failed due to some technical error.";
	    }
	    echo json_encode($data);
	}
	
	
	private function getLife(){
	    //date_default_timezone_set("Asia/Dhaka");
	    $date1 = new DateTime($this->data['jdate']);
	    $date2 = new DateTime(date('Y-m-d'));
	    $interval = $date1->diff($date2);
	    $life = "";
	    if($interval->y){
	        if($interval->y >1) 
	           {$life = $interval->y . " years ";
	        } else{
	            $life = $interval->y . " year ";
	        }
	    }
	    if($interval->m){
	        if($interval->m >1) {
	           $life .= $interval->m." months ";
	        } else{
	            $life .= $interval->m." month ";
	        }
	    }	    
        if($interval->d >1) {
            $life .= $interval->d." days";
        } else{
           $life .= $interval->d." day";
        }
          
	    return $life;
	}
	
	private function getLoginDay($date){
	    date_default_timezone_set("Asia/Dhaka");
	    $lastLogin = date('Y-m-d', strtotime($date));
	    $date1 = new DateTime($lastLogin);
	    $date2 = new DateTime(date('Y-m-d'));
	    $interval = $date1->diff($date2);
	    $loginDay = "";
	    if($interval->y){
	        if($interval->y >1)
	        {$loginDay = $interval->y . " years ";
	        } else{
	            $loginDay = $interval->y . " year ";
	        }
	    }
	    if($interval->m){
	        if($interval->m >1) {
	            $loginDay .= $interval->m." months ";
	        } else{
	            $loginDay .= $interval->m." month ";
	        }
	    }
	    
	    if($interval->d >1) {
	        $loginDay .= $interval->d." days ago";
	    }elseif($interval->d == 1) {
	            $loginDay .= 'yesterday';
	    }else{
	        $loginDay .= 'today';	        
	    }
	     
	    return $loginDay;
	}
	
	
	private function getLoginTime($date){
	    date_default_timezone_set("Asia/Dhaka");
	    $timestamp = strtotime($date);
	    $lastLogin = date('d M Y', $timestamp)." - ".date('l', $timestamp)." - ".date('h:i A',$timestamp);
	    
	    return $lastLogin;
	}
	
    private function getImagePath($image){

	    if(empty($image)) $image = "no_picture.gif";
	    $imageURL = $this->web_url_main."assets/pictures/".$image;
	    
	    $headers = get_headers($imageURL);
	    $response_code = substr($headers[0], 9, 3);
	    
	    if ($response_code != "200" ) {
	        $imageURL = "";
	        $imageSubPath = rtrim(base_url(), "/");
	        $imgSubArr = explode("/", $imageSubPath);
	        $loopLimit = count($imgSubArr);
	        if ($loopLimit > 0){
	            $imageSubPath = "";
	            for ($i=0; $i<($loopLimit-1); $i++){
	                $imageSubPath .= $imgSubArr[$i] . "/";
	            }
	            $imageURL = $imageSubPath."pictures/".$image;
	        }
	        if (!empty($imageURL)){
	            $headers = get_headers($imageURL);
	            $response_code = substr($headers[0], 9, 3);
	            if ($response_code != "200" ) {
	                $imageURL = $this->web_url_main."assets/pictures/no_picture.gif";
	            }
	        }else {
	            $imageURL = $this->web_url_main."assets/pictures/no_picture.gif";
	        }
	    }
	    
	    return $imageURL;
	}
	
	public function add_status_log($emp_id = NULL){
		$this->isLoggedIn();
		$isExists = $this->user_model->isEmployeeExists($emp_id);
		if($emp_id == "" || !$isExists) redirect(base_url()."user/login");

	    $data = array();
	    $data['emp_id'] = $emp_id;
	    $data['status'] = $_POST['status'];
	    $data['date'] = $_POST['date'];
	    
        $insert_id = $this->user_model->add_status_log($data);
        
        if($insert_id){
            //update status to employee table
            $eData['status'] =  $data['status'];          
            $flag = $this->user_model->updateByEmpId($emp_id, $eData);
            
            
            $return['status'] = true;
            $return['msg'] = 'New status has been successfully added.';
            $return['update_emp'] = $flag;
            $return['insert_id'] = $insert_id;
        }else{
            $return['status'] = false;
            $return['msg'] = 'failed to add new status. try again.';
        }

        echo json_encode($return);
	}
	
	public function del_status_log($status_id = NULL){
		$this->isLoggedIn();
		if($status_id == "") redirect(base_url()."user/login");
	    $flag = $this->user_model->del_status_log($status_id);
	
	    if($flag){
	        $return['status'] = true;
	        $return['msg'] = 'Status has been successfully removed.';
	    }else{
	        $return['status'] = false;
	        $return['msg'] = 'failed to remove status. try again.';
	    }
	
	    echo json_encode($return);
	}
	
	public function change_grade($grade_id = NULL){
		$this->isLoggedIn();
		if($grade_id == "") redirect(base_url()."user/login");
	    $emp_id = isset($_POST['staffId']) ? $_POST['staffId'] : "";

	    $flag = $this->user_model->change_grade($emp_id, $grade_id);
	
	    if($flag){
	        $this->addActivityLog("U", "emp_id=$emp_id, grade_id=$grade_id", "Employee($emp_id) Grade Updated");
	        $return['status'] = true;
	        $return['msg'] = $this->message['update_s'];
	    }else{
	        $return['status'] = false;
	        $return['msg'] = $this->message['update_f'];
	    }
	
	    echo json_encode($return);
	}
	
	public function add_grade($grade_value = NULL){
		if($grade_value == "") redirect(base_url()."user/login");
	    $my_emp_id = $this->session->GetLoginId();
	    
	    if(!empty($grade_value) && $this->session->IsAdmin($my_emp_id)) {
	        $flag = $this->user_model->add_new_grade($grade_value);
	    }else {
	        $flag = false;
	    }

	    if($flag){
	        $return['status'] = true;
	        $return['msg'] = "New grade has been added successfully";
	    }else{
	        $return['status'] = false;
	        $return['msg'] = "Failed to add new grade! Try again later.";
	    }
	
	    echo json_encode($return);
	}
	
	public function integrate_gmail(){
	    
	    $return = array();
	    if(!empty($this->myEmpId) && isset($_POST['email'])){
	        
	        
	        
	        $data['user_id'] = $this->myEmpId;
	        $data['email'] = $_POST['email'];
	        $data['token'] = "";
	        
	        $flag = $this->user_model->add_gmail($data);
	        
	         
	        
	        if($flag){
	            
	            $return['status'] = true;
	            $return['msg'] = "successfully gmail integrated.";
	            
	        }else{
	            
	            $return['status'] = false;
	            $return['msg'] = "gmail already integrated.";
	        }
	        
	    }else{
	        $return['status'] = false;
	        $return['msg'] = "error occured.";

	    }
	    
	    echo json_encode($return);
	    return;
	}
	
	private function passwordGenerator(){
	    
        $len = 10;
        $base = 'ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
        $max = strlen($base) - 1;
        $activatecode = '';
        mt_srand((double) microtime() * 1000000);
        while (strlen($activatecode) < $len + 1) {
            $activatecode .= $base{mt_rand(0, $max)};
        }
        return $activatecode;
    }

	public function login_developer()
	{
		exit("Developer Login Blocked");
		if(!empty($this->myEmpId)){
			redirect(base_url()."user/detail/".$this->myEmpId);
		}
	
		$login_id = isset($_POST["login_id"]) ? trim($_POST["login_id"]) : "";
		$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
		$redirect_to = isset($_POST["rto"]) ? trim($_POST["rto"]) : base_url()."user/detail/".$login_id;	
		$errMsg = "";	
	
		if(isset($_POST["login"])) {
			$ary = $this->session->GetSessionData();
	
			if(empty($login_id) && empty($password)) $errMsg = 'Employee ID & Password Required!';
			elseif(empty($login_id)) $errMsg = 'Employee ID Required!';
			elseif(empty($password)) $errMsg = 'Password Required!';
				
			if (empty($errMsg)) $errMsg = $this->validateLogin($login_id, $password);
				
			if(empty($errMsg)) {
				if (filter_var($login_id, FILTER_VALIDATE_EMAIL)) {					 
					list($user_name, $domain) = explode('@', $login_id);
					$login_flag = false;
	
					if (($domain == 'dhakatel.com') || ($domain == 'genuitysystems.com') || ($domain == 'genusys.us') ) {	
						$login_flag = $this->login_dhakatel($user_name, $password);
						 
						if($login_flag){
							$login_id = $this->user_model->getEidbyMail($login_id);
							if($login_id){								 
								$user = $this->user_model->login_gmail($login_id);
							}
						}else{							 
							$errMsg = 'User ID or Password Wrong!';
						}
	
					}else{
						$errMsg = 'User ID is wrong!';
					}
				}else{
					$login_id = strtoupper($login_id);
					$user = $this->user_model->loginByTester($login_id, $password);
				}
				 
				if( (isset($user) && $user!=null) ) {
	
					$logData = array('emp_id'=>$login_id,"activity"=>"L","affected"=>"","log_text"=>"Software Tester Logged in", "log_time"=> date('Y-m-d H:i:s'));
					$this->user_model->setLog($logData);
	
					$ses['emp_id'] = $user['emp_id'];
					$ses['name'] = $user['name'];
					$ses['lock'] = $user['active'];
					$ses['archive'] = $user['archive'];
					$ses['dept_name'] = $user['dept_name'];
					$ses['dept_code'] = $user['dept_code'];
					$ses['designation'] = $user['designation'];
					$ses['gender'] = $user['gender'];
					$ses['email'] = $user['email'];
					$ses['image'] = $this->getImagePath($user['image']);
					$ses['login_by_gmail'] = false;
						
					$this->SetSessionSettings($user['emp_id']);
						
					if($this->session->IsAdmin($login_id)) {
						$ses['uType'] = 'A';
					} else if ($this->session->IsManagement($login_id)) {
						$ses['uType'] = 'B';
					} else if($this->session->IsManager($login_id)) {
						$ses['uType'] = 'M';
					} else {
						$ses['uType'] = 'E';
					}
						
					if($ses['lock']=='U' && $ses['archive']=='N') {
						$this->session->SetSessionData($ses);
	
						$data=array('online'=>'Y','login_time'=>date('Y-m-d H:i:s'));
						$this->user_model->setOnline($data, $login_id);
	
						$lastPage = $this->session->get_lastpage();
						if(empty($lastPage)) {
							redirect(base_url()."user/detail/".$ses['emp_id']);
						} else {
							redirect($lastPage);
						}
					} else if($ses["lock"]=="L") {
						echo "Sorry, You are locked by admin";
					} else {
						echo "Sorry, You are now in archive";
					}
				} else{
					$this->user_model->addFailedLog($login_id);
					$errMsg = 'User ID or Password Wrong!';
				}
			}
		}
	
		$data = array();
		$data["login_id"] = $login_id;
		$data["password"] = "";
		$data["errMsg"] = $errMsg;
		$this->load->view('login_tester', $data);
	}
	
}
?>
