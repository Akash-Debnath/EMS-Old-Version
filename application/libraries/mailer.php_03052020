<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Mailer {

	public function __construct()
	{
		include('phpmailer_v5.1/class.phpmailer.php');
	}
	
	public function sendMail($subject, $emailBody, $receiver, $sender) {	    	  
	    ini_set("sendmail_from","hrd@genuitysystems.com");
	    ini_set("SMTP","mail.dhakatel.com");
	    ini_set("smtp_port","25");
	    
	    $to="";
	    $bcc=array();
	    $bcc_names = array();

	    foreach ($receiver as $obj){	        
	        if(!empty($obj->email)){
    	        $bcc_names[] = $obj->name;
    	        $bcc[] =  $obj->email;
	        }        
		}
		// echo "<pre>";
		// var_dump($mail->SMTPDebug);
		// die();
	    $mailtext = "<br><br>
		<table height='300' width='700' cellpadding='1' cellspacing='1' align='center' style='background-color:#FFFFFF; border:1px solid #DDDDDD;'>
			<tr style='background-color:#FFFFFF;'>
				<td style='border:1px solid #FFFFFF; font-size:16px;' align='center'>".$emailBody."	                
									</td>
									</tr>
									</table><br><br>
									";
	    
	    $mail = new PHPMailer();
	
        $mail->IsSMTP();
		//$mail->needReplyTo(false);
        $mail->Host       = "mail.dhakatel.com";    // SMTP server
        $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
        $mail->SMTPAuth   = false;                 // enable SMTP authentication
        $mail->Port       = 25;                    // set the SMTP port for the GMAIL server

        $mail->SetFrom("hrd@genuitysystems.com", "Genuity");
	    $mail->Subject    = $subject;
		$mail->MsgHTML($mailtext);
		
		

        $mail->From     = $sender['email'];
        $mail->FromName = $sender['name'];
        //$mail->AddAddress($sender['email'], $sender['name']);
        
        if(count($bcc) == 1){            
            $mail->AddAddress($bcc[0], $bcc_names[0]);            
        }else{
            //$mail->AddAddress($bcc[0], $bcc_names[0]);
	    $mail->AddAddress('hrd@genuitysystems.com', 'Genuity Systems Ltd.');
            foreach($bcc as $key=>$value) {
                //if ($key != 0){
                    $mail->AddBCC($value,$bcc_names[$key]);
                //}
            }
        }        

        if($mail->Send()) {
            return true;
    	}else {
    	    return false;
    	}
    	return true;
	}
	
	
	public function sendSpecificMail($subject, $emailBody, $receiver, $sender) {
	    
	    ini_set("sendmail_from","hrd@genuitysystems.com");
	    ini_set("SMTP","mail.dhakatel.com");
	    ini_set("smtp_port","25");
	     
	     
	    $mailtext = "<br><br>
		<table height='300' width='700' cellpadding='1' cellspacing='1' align='center' style='background-color:#FFFFFF; border:1px solid #DDDDDD;'>
			<tr style='background-color:#FFFFFF;'>
				<td style='border:1px solid #FFFFFF; font-size:16px;' align='center'>".$emailBody."
									</td>
									</tr>
									</table><br><br>
									";
	     
	    $mail = new PHPMailer();
	
	    $mail->IsSMTP();
	    $mail->Host       = "mail.dhakatel.com";    // SMTP server
	    $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
	    $mail->SMTPAuth   = false;                 // enable SMTP authentication
	    $mail->Port       = 25;                    // set the SMTP port for the GMAIL server
	
	    $mail->SetFrom("hrd@genuitysystems.com", "Genuity");
	    $mail->Subject    = $subject;
	    $mail->MsgHTML($mailtext);
	
	    $mail->From     = $sender['email'];
	    $mail->FromName = $sender['name'];
	    
	    
	    if(isset($receiver["to"]) && !empty($receiver["to"])){
	        
	        foreach ($receiver["to"] as $obj){
	            
	            if(!empty($obj->email)){
	            
	                $mail->AddAddress($obj->email, $obj->name);
	            }	            
	        }	        
	    }
	    
	    if(isset($receiver["cc"]) && !empty($receiver["cc"])){
	    
	        foreach ($receiver["cc"] as $obj){
	    
	            if(!empty($obj->email)){
	    
	                $mail->addCC($obj->email, $obj->name);
	            }
	        }
	    }
	    
	    if(isset($receiver["bcc"]) && !empty($receiver["bcc"])){
	         
	        foreach ($receiver["bcc"] as $obj){
	             
	            if(!empty($obj->email)){
	                 
	                $mail->addBCC($obj->email, $obj->name);
	            }
	        }
	    }
	    
	
	    if($mail->Send()) {
	        
	        return true;
	    }else {
	        
	        return false;
	    }
	    
	    return true;
	}
	
	
	public function sendMailByManager( $subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url) {
	    
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $message = "has sent a <a href='".$web_url."leave/request/". $leaveInfo->id . "'>leave application</a> to <a href='$web_url'>EMS</a> on ".$leaveInfo->leave_date.". The leave request has approved and forwarded to you on ".date("d/m/Y")." at ".date("h:i:s A")." for verification.";
	    $lastHeader = "Leave Application Recomended by -"; 
	    $emailBody = $this->getLeaveEmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);

	    
	    	  
	    if($this->sendSpecificMail($subject, $emailBody, $receiver, $sender)){
	        //success to send mail admin
	        $sub = "Leave application has recomended";
	        $obj = new stdClass();
	        $obj->name = $staffInfo->name;
	        $obj->email = $staffInfo->email;
	       
	        $r["to"] = array($obj);
	        $s = $sender;
	        
	        $message = "Your <a href='".$web_url."leave/request/".$leaveInfo->id."'>leave application</a> has been approved and sent to admin on ".date("d/m/Y")." at ".date("h:i:s A")." for verification.";
	        $lastHeader = "Leave Application Recomended by -";
	        $eBody = $this->getLeaveEmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);
	        
	        if($this->sendSpecificMail($sub, $eBody, $r, $s)){
	            //success to send mail to staff back
	            return true;
	             
	        }else{
	            //failed to send mail staff back
	            return false;
	        }
	    }else{
	        //failed to send mail admin
	        return false;
	        
	    }  	    
	}
	
	public function sendMailByAdmin($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url){
	    
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $message = "The <a href='".$web_url."leave/request/".$leaveInfo->id."'>leave application</a> has been verified for record by admin on ".date("d/m/Y")." at ".date("h:i:s A").".";
	    $lastHeader = "Leave verified for record by -";
	    $emailBody = $this->getLeaveEmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);

	    if($this->sendSpecificMail($subject, $emailBody, $receiver, $sender)){
	        //success to send mail to manager and staff back
	        return true;
	    }else{
	        //failed to send mail admin
	        return false;	         
	    }
	}
	
	public function sendRefuseMailByManager($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url, $excuse){
	    
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $message = "Your leave application has been refused for reason by manager which is mentioned bellow.<br><b>Reason</b> : $excuse";
	    $lastHeader = "Request refused by -";
	    $showLink = false;
	    $emailBody = $this->getLeaveEmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader, $showLink);
	    
	    if($this->sendSpecificMail($subject, $emailBody, $receiver, $sender)){
	        //success to send refuse mail to staff by manager
	        return true;
	    }else{
	        //failed to send refuse mail to staff by manager
	        return false;
	    }	    
	}
	
	public function sendRefuseMailByAdmin($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url, $excuse){
	    
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $message = "Your leave application has been refused for reason by admin which is mentioned bellow.<br><b>Reason</b> : $excuse";
	    $lastHeader = "Request refused by -";
	    $showLink = false;
	    $emailBody = $this->getLeaveEmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader, $showLink);

	    
	    if($this->sendSpecificMail($subject, $emailBody, $receiver, $sender)){
	        //success to send refuse mail to staff by manager
	        return true;
	    }else{
	        //failed to send refuse mail to staff by manager
	        return false;
	    }
	}
	
	public function sendCancellationMail($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url, $excuseReason){
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $message = "I am scheduled to take my leave from ".$leaveInfo->leave_start." till ".$leaveInfo->leave_end.". But due to the following reasons, I wish to cancel it. Hope you would consider my request favourably.<br><b>Reason :</b> $excuseReason";
	    $lastHeader = "Leave Cancellation Requested by -";
	    $emailBody = $this->getLeaveEmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);
	    
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        //success to send refuse mail to staff by manager
	        return true;
	    }else{
	        //failed to send refuse mail to staff by manager
	        return false;
	    }
	}
	
	public function sendCancellationApproveMail($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url){
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $message = "Leave cancellation has been approved.";
	    $lastHeader = "Leave Cancellation Approved by -";
	    $emailBody = $this->getLeaveEmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);
	     
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        //success to send refuse mail to staff by manager
	        return true;
	    }else{
	        //failed to send refuse mail to staff by manager
	        return false;
	    }
	}
    public function sendCancellationRefuseMail($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url, $excuse){
        $sender['name'] = $senderInfo->userName;
        $sender['email'] = $senderInfo->email;
        $message = "Leave cancellation has been refused for reason which is mentioned bellow.<br><b>Reason :</b> $excuse";
        $lastHeader = "Leave Cancellation Refused by -";
        $emailBody = $this->getLeaveEmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);

        if($this->sendMail($subject, $emailBody, $receiver, $sender)){
            //success to send refuse mail to staff by manager
            return true;
        }else{
            //failed to send refuse mail to staff by manager
            return false;
        }
    }
	
	public function getLeaveEmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader, $showLink = true){
	        
	    
	    $innerDiv = "<div align='left'>
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$staffInfo->emp_id."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$staffInfo->emp_id."'>".$staffInfo->name."</a></b></td></tr>
                    	    <tr><td><i>".$staffInfo->designation."</i></td></tr>
                    	    <tr><td>".$staffInfo->dept_name."</td></tr>
                    	</table>
                    	<p>".$message."</p>";
	    
	    if($showLink){
	        
	        $innerDiv .= "<p>Open the following link to see the leave request form.</p>
	    
                    	<div align='center' style='padding:10px;'>                    		
                    		<a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 10px 30px; border: 1px solid #333333;' href='". $web_url . "leave/request/" . $leaveInfo->id . "'>Leave Request Form</a>
                    	</div>";
	    }   

        $innerDiv .= "</div>
                    <div align='center' style='padding:5px 10px; margin-top:10px; background-color:#3C8DBC; color:#FFFFFF;'>Leave Details</div>
                    <div align='left'>
                    	<table width='100%' cellpadding='3' cellspacing='0'>
                    		<tr><td width='150'><i>Leave type</i></td><td width='20' align='center'><b>:</b></td><td>&nbsp;".$leaveInfo->leave_type."</td></tr>
            	            <tr><td><i>Leave from</i></td><td align='center'><b>:</b></td><td>&nbsp;".$leaveInfo->leave_start."</td></tr>
            	            <tr><td><i>Leave to</i></td><td align='center'><b>:</b></td><td>&nbsp;".$leaveInfo->leave_end."</td></tr>
                    	    <tr><td><i>Period</i></td><td align='center'><b>:</b></td><td>".$leaveInfo->period."</td></tr>
                    	    <tr><td valign='top'><i>Address during leave</i></td><td align='center' valign='top'><b>:</b></td><td>".$leaveInfo->address_d_l."</td></tr>
                    	    <tr><td><i>Reason</i></td><td align='center'><b>:</b></td><td>".$leaveInfo->speacial_reason."</td></tr>
                    	</table>
                    </div>
                    <div align='center' style='padding:5px 10px; margin-top:10px; background-color:#3C8DBC; color:#FFFFFF;'>".$lastHeader."</div>
                    <div align='right'>
                	    <table width='100%' cellpadding='3' cellspacing='0' style='text-align:right;'>
                	    <tr><td>".$senderInfo->userId."</td></tr>
                	    <tr><td><a href='".$web_url."user/detail/".$senderInfo->userId."'>".$senderInfo->userName."</a></td></tr>    
                	    <tr><td>".$senderInfo->userDesignation."</td></tr>
                	    <tr><td>".$senderInfo->userDepartment."</td></tr>            	    
                	    </table>
                    </div>";
	    
        $emailBody = $this->outterDiv($innerDiv, $web_url);
        
        return $emailBody;	    
	}
	
	
	public function lateEarlyMailToMan($subject, $receiver, $senderInfo, $leaveInfo, $web_url){
	    
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $message = "has sent a late/early leave request on <a href='$web_url'>EMS</a> on ".date('Y-m-d')." and waiting for your approval.";
	    
	    $innerDiv = "<div align='left'>	        
            		<table cellpadding='3' cellspacing='0'>
            			<tr><td>".$senderInfo->userId."</td></tr>
            			<tr><td><b><a href='".$web_url."user/detail/".$senderInfo->userId."'>$sender[name]</a></b></td></tr>
            			<tr><td><i>".$senderInfo->userDesignation."</i></td></tr>
            			<tr><td>".$senderInfo->userDepartment."</td></tr>
            		</table>
            		<p>".$message."</p>
            		<p>Please open the following link to approve/refuse the request.</p>
            		
            		<div align='center' style='padding:10px;'>
            			<a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 3px 20px; border: 1px solid #333333;' href='".$web_url."attendance/pending/'>Late/Early Request Pending Panel</a>
            		</div>
                	</div>
                				
                	<div align='center' style='padding:5px 10px; margin-bottom:10px; background-color:#3C8DBC; color:#FFFFFF;'>Leave Request Details</div>
                
                	<table width='100%' cellpadding='5' cellspacing='0'>
                		<tr><td width='150'><i>Request type</i></td><td width='20' align='center'><b>:</b></td><td>&nbsp;".$leaveInfo->message."</td></tr>
                		<tr><td><i>Date</i></td><td align='center'><b>:</b></td><td>&nbsp;".$leaveInfo->date."</td></tr>
                		<tr><td><i>Reason</i></td><td align='center'><b>:</b></td><td>".$leaveInfo->reason."</td></tr>
                	</table>";
	    
	    $emailBody = $this->outterDiv($innerDiv, $web_url);
	    
	    
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        //success to send mail manager
	        return true;
	    }else{
	        //failed to send mail manager
	        return false;
	    }
	}
	
	public function requestApproveMailByManager($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url){
	     
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    
	    //$message = "Your late/early leave request has been approved by manager.";
	    
	    //$lastHeader = "Request approved by -";
	    //$emailBody = $this->att_EmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);
	    
	    $message = "Late/Early request by this employee has been approved in <a href='$web_url'>EMS</a> on ".date('Y-m-d')." and waiting for admin verification.";
	     
	    $innerDiv = "<div align='left'>
            		<table cellpadding='3' cellspacing='0'>
            			<tr><td>".$senderInfo->userId."</td></tr>
            			<tr><td><b><a href='".$web_url."user/detail/".$senderInfo->userId."'>$sender[name]</a></b></td></tr>
	                			<tr><td><i>".$senderInfo->userDesignation."</i></td></tr>
            			<tr><td>".$senderInfo->userDepartment."</td></tr>
            		</table>
            		<p>".$message."</p>
            		<p>Please open the following link to verify/refuse the request.</p>
	    
            		<div align='center' style='padding:10px;'>
            			<a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 3px 20px; border: 1px solid #333333;' href='".$web_url."attendance/pending/'>Late/Early Request Pending Panel</a>
            		</div>
                	</div>
	    
                	<div align='center' style='padding:5px 10px; margin-bottom:10px; background-color:#3C8DBC; color:#FFFFFF;'>Leave Request Details</div>
	    
                	<table width='100%' cellpadding='5' cellspacing='0'>
                		<tr><td width='150'><i>Request type</i></td><td width='20' align='center'><b>:</b></td><td>&nbsp;".$leaveInfo->message."</td></tr>
                		<tr><td><i>Date</i></td><td align='center'><b>:</b></td><td>&nbsp;".$leaveInfo->date."</td></tr>
                		<tr><td><i>Reason</i></td><td align='center'><b>:</b></td><td>".$leaveInfo->reason."</td></tr>
                	</table>";
	     
	    $emailBody = $this->outterDiv($innerDiv, $web_url);	    
	    
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        //success to send refuse mail to staff by manager
	        return true;
	    }else{
	        //failed to send refuse mail to staff by manager
	        return false;
	    }
	}
	
	public function requestVerifyMailByAdmin($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url){
	     
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $message = "His/her late/early leave request has been verified for record.";
	    $lastHeader = "Request verified by -";
	    $emailBody = $this->att_EmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);
	     
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        //success to send refuse mail to staff by manager
	        return true;
	    }else{
	        //failed to send refuse mail to staff by manager
	        return false;
	    }
	}
	
	public function requestRefuseMail($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url, $message){
	    
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;	    
	    $lastHeader = "Request refused by -";
	    $emailBody = $this->att_EmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);
	    
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        //success to send refuse mail to staff by manager
	        return true;
	    }else{
	        //failed to send refuse mail to staff by manager
	        return false;
	    }
	}
	
	public function sendMailForMissingFingerprint($subject, $receiver, $staffInfo, $sender, $missingData, $web_url ){
	    
	    $innerBody = "<tbody>
                    		<tr>
                    			<td>
                    				<h1>Hi, ". $staffInfo->name ."</h1>
                    				<p> It seems that you missed or forgot to give fingerprint in IN/OUT attendance at Attendance Systems.</p>
                    				<p> Please open the following link and give the manual entry of IN/OUT attendance.</p>
                    			</td>
                    		</tr>
                    		<tr height='50'>
                    		    <td valign='center' align='center'>
                        	    <a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 5px 20px; border: 1px solid #333333;' href='".$web_url."attendance/missing/".$missingData['date']."/". $staffInfo->emp_id ."'>Show Attendance Missing Form</a>
                        	    </td>
                    	    </tr>
                    	</tbody>
                    	<thead style='width:100%; color:white;'bgcolor= '#3C8DBC'>
                    		<tr><th colspan=2><b>Missing Attendance Details</b></th></tr>
                    	</thead>
                    	<tbody>
                    		<tr>
                    			<td colspan='3'>
                    				<table width='100%' cellpadding='5' cellspacing='0'>
                    					<tr>
                    						<td>
                    							<table width='100%' cellpadding='5' cellspacing='0'>
                    								<tr><td>Date</td><td align='center'><b>:</b></td><td>&nbsp;".$missingData['date']."</td></tr>
                    								<tr><td>Day</td><td align='center'><b>:</b></td><td>&nbsp;". date('l', strtotime($missingData['date'])) ."</td></tr>
                    								
                    								<tr><td>IN Attendance</td><td width='20' align='center'><b>:</b></td><td>&nbsp;".$missingData['in']."</td></tr>
                    								
                    								<tr><td>OUT Attendance</td><td align='center'><b>:</b></td><td>&nbsp;".$missingData['out']."</td></tr>
                    							</table>
                    						</td>
                    					</tr>
                    				</table>
                    			</td>
                    		</tr>
                    	</tbody>";
	    
	    $emailBody = $this->outterFrame($innerBody, $web_url );
	    
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        return true;
	    }else{
	        return false;
	    }								    
	}
	
	public function sendMailForFingerprintToMan($subject, $receiver, $senderInfo, $attData, $web_url ){
	    
	    
	    
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $message = "has sent a request for missing attendance on <a href='$web_url'>EMS</a> on ".date('Y-m-d')." and waiting for your approval.";
	    
	    $reqRow = "";
	    if(!is_null($attData['in'])){
	        $reqRow .= "<tr><td>IN Att.</td><td align='center'><b>:</b></td><td>&nbsp;".$attData['in']."</td></tr>";
	    }
	    if(!is_null($attData['out'])){
	        $reqRow .= "<tr><td>OUT Att.</td><td align='center'><b>:</b></td><td>&nbsp;".$attData['out']."</td></tr>";
	    }

	    $innerDiv = "<div align='left'>	        
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$senderInfo->userId."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$senderInfo->userId."'>$sender[name]</a></b></td></tr>
                    		<tr><td><i>".$senderInfo->userDesignation."</i></td></tr>
                    		<tr><td>".$senderInfo->userDepartment."</td></tr>
                    	</table>
               		    <p>".$message."</p>
           				<p>Please open the following link to approve/refuse the request.</p>
                        
                		<div align='center' style='padding:10px;'>
                			<a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 3px 20px; border: 1px solid #333333;' href='".$web_url."attendance/missing_pending/'>Go to Attendance Missing Pannel</a>
                		</div>
                    </div>
                	            
                    <div align='center' style='padding:5px 10px; margin-bottom:10px; background-color:#3C8DBC; color:#FFFFFF;'>Request Attendance Details</div>

					<table width='100%' cellpadding='5' cellspacing='0'>
						<tr><td>Date</td><td align='center'><b>:</b></td><td>&nbsp;".$attData['date']."</td></tr>
						<tr><td>Day</td><td align='center'><b>:</b></td><td>&nbsp;". date('l', strtotime($attData['date'])) ."</td></tr>
						".$reqRow."
					</table>";
	     
	    $emailBody = $this->outterDiv($innerDiv, $web_url);
	    
	    //echo $emailBody;
	     
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	public function sendMailForFingerprintByManToAdmin($subject, $receiver, $senderInfo, $staffInfo, $attData, $web_url ){
	    
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    
	    $message = "Missing attendance by this employee has been approved in <a href='$web_url'>EMS</a> on ".date('Y-m-d')." and waiting for your verification.";
	     
	    $reqRow = "";
	    if(!is_null($attData->in)){
	        $reqRow .= "<tr><td>IN Att.</td><td align='center'><b>:</b></td><td>&nbsp;".$attData->in."</td></tr>";
	    }
	    if(!is_null($attData->out)){
	        $reqRow .= "<tr><td>OUT Att.</td><td align='center'><b>:</b></td><td>&nbsp;".$attData->out."</td></tr>";
	    }
	
	    $innerDiv = "<div align='left'>
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$staffInfo->emp_id."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$staffInfo->emp_id."'>$staffInfo->name</a></b></td></tr>
	                    		<tr><td><i>".$staffInfo->designation."</i></td></tr>
                    		<tr><td>".$staffInfo->dept_code."</td></tr>
                    	</table>
               		    <p>".$message."</p>
	                    <p>Please open the following link to verify/refuse the request.</p>
                		<div align='center' style='padding:10px;'>
                			<a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 3px 20px; border: 1px solid #333333;' href='".$web_url."attendance/missing_pending/'>Go to Attendance Missing Pannel</a>
                		</div>
                    </div>
               
                    <div align='center' style='padding:5px 10px; margin-bottom:10px; background-color:#3C8DBC; color:#FFFFFF;'>Request of Missing Attendance Details</div>
	
					<table width='100%' cellpadding='5' cellspacing='0'>
						<tr><td>Date</td><td align='center'><b>:</b></td><td>&nbsp;".$attData->date."</td></tr>
						<tr><td>Day</td><td align='center'><b>:</b></td><td>&nbsp;". date('l', strtotime($attData->date)) ."</td></tr>
						".$reqRow."
					</table>";
	
	    $emailBody = $this->outterDiv($innerDiv, $web_url);
	     
	    //echo $emailBody;
	
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	public function sendMailForFingerprintByAdminToEmp_Man($subject, $receiver, $senderInfo, $staffInfo, $attData, $web_url ){
	     
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	     
	    $message = "Missing attendance by this employee has been verified in <a href='$web_url'>EMS</a> on ".date('Y-m-d')." and auto attendance(s) will be added to system.";
	
	    $reqRow = "";
	    if(!is_null($attData->in)){
	        $reqRow .= "<tr><td>IN Att.</td><td align='center'><b>:</b></td><td>&nbsp;".$attData->in."</td></tr>";
	    }
	    if(!is_null($attData->out)){
	        $reqRow .= "<tr><td>OUT Att.</td><td align='center'><b>:</b></td><td>&nbsp;".$attData->out."</td></tr>";
	    }
	
	    $innerDiv = "<div align='left'> 
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$staffInfo->emp_id."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$staffInfo->emp_id."'>$staffInfo->name</a></b></td></tr>
	                    		<tr><td><i>".$staffInfo->designation."</i></td></tr>
                    		<tr><td>".$staffInfo->dept_code."</td></tr>
                    	</table>
               		    <p>".$message."</p>
                    </div>
        
                    <div align='center' style='padding:5px 10px; margin-bottom:10px; background-color:#3C8DBC; color:#FFFFFF;'>Request of Missing Attendance Details</div>
	
					<table width='100%' cellpadding='5' cellspacing='0'>
						<tr><td>Date</td><td align='center'><b>:</b></td><td>&nbsp;".$attData->date."</td></tr>
						<tr><td>Day</td><td align='center'><b>:</b></td><td>&nbsp;". date('l', strtotime($attData->date)) ."</td></tr>
						".$reqRow."
					</table>";
	    
        $emailBody = $this->outterDiv($innerDiv, $web_url);
        
        // echo $emailBody;
        
        if ($this->sendMail($subject, $emailBody, $receiver, $sender)) {
            return true;
        } else {
            return false;
        }
	}
	
	public function sendRefuseMailForFingerprint($subject, $receiver, $senderInfo, $staffInfo, $attData, $message, $web_url ){
	
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    
	    
	    
	    
		
	    $reqRow = "";
	    if(!is_null($attData->in)){
	        $reqRow .= "<tr><td>IN Att.</td><td align='center'><b>:</b></td><td>&nbsp;".$attData->in."</td></tr>";
	    }
	    if(!is_null($attData->out)){
	        $reqRow .= "<tr><td>OUT Att.</td><td align='center'><b>:</b></td><td>&nbsp;".$attData->out."</td></tr>";
	    }
	    
	    $innerDiv = "<div align='left'>
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$staffInfo->emp_id."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$staffInfo->emp_id."'>".$staffInfo->name."</a></b></td></tr>
	                    		<tr><td><i>".$staffInfo->designation."</i></td></tr>
                    		<tr><td>".$staffInfo->dept_name."</td></tr>
                    	</table>
               		    <p>".$message."</p>
                    </div>
	
                    <div align='center' style='padding:5px 10px; margin-bottom:10px; background-color:#3C8DBC; color:#FFFFFF;'>Request of Missing Attendance Details</div>
	
					<table width='100%' cellpadding='5' cellspacing='0'>
						<tr><td>Date</td><td align='center'><b>:</b></td><td>&nbsp;".$attData->date."</td></tr>
						<tr><td>Day</td><td align='center'><b>:</b></td><td>&nbsp;". date('l', strtotime($attData->date)) ."</td></tr>
						".$reqRow."
	    </table>";
	  
	    $emailBody = $this->outterDiv($innerDiv, $web_url);
        
        if ($this->sendMail($subject, $emailBody, $receiver, $sender)) {
            
            return true;
        } else {
            
            return false;
        }
	}
	
	public function att_EmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader){
	    $eb = "<table>
                	<thead style='width:100%; color:white;' bgcolor= '#3C8DBC' >
                		<tr><th colspan=2>".COMPANY_PREFIX." Staff</th></tr>
                	</thead>
                	<tbody>
                		<tr>
                			<td width='50%' valign='top' align='left'>
                	 			<table cellpadding='3' cellspacing='0'>
                                    <tr><td>".$staffInfo->emp_id."</td></tr>
                                    <tr><td><b><a href='".$web_url."user/detail/".$staffInfo->emp_id."'>".$staffInfo->name."</a></b></td></tr>
                                    <tr><td><i>".$staffInfo->designation."</i></td></tr>
                                    <tr><td>".$staffInfo->dept_name."</td></tr>
                            	</table>
                			</td>
                		</tr>
                		<tr height='40'><td colspan='2'>$message</td></tr>
                	</tbody>
                	<thead style='width:100%; color:white;'bgcolor= '#3C8DBC'>
                		<tr><th colspan=2><b>Leave Request Details</b></th></tr>
                	</thead>
                	<tbody>
                        <tr>
                	    	<td colspan='3'>
                	            <table width='100%' cellpadding='5' cellspacing='0'>
                	                <tr>
                	                    <td>
                	                        <table width='100%' cellpadding='5' cellspacing='0'>
                	                            <tr><td width='150'><i>Request type</i></td><td width='20' align='center'><b>:</b></td><td>&nbsp;".$leaveInfo->message."</td></tr>
                	                            <tr><td><i>Date</i></td><td align='center'><b>:</b></td><td>&nbsp;".$leaveInfo->date."</td></tr>
                								<tr><td><i>Reason</i></td><td align='center'><b>:</b></td><td>".$leaveInfo->reason."</td></tr>
                							</table>
                	                    </td>
                	                </tr>
                	            </table>
                	    	</td>
                		</tr>
                	</tbody>
                    <thead style='width:100%; color:white; text-align:left;'bgcolor= '#3C8DBC'>
            	        <tr><th colspan=2><b>&nbsp;$lastHeader</b></th></tr>
            	    </thead>
            	    <tbody>
            	        <tr><td colspan='3'><table width='100%' cellpadding='5' cellspacing='0'>
	            	    <tr>
            	            <td>
        	            	    <table width='100%' cellpadding='3' cellspacing='0' style='text-align:right;'>
        	            	    <tr><td>".$senderInfo->userId."</td></tr>
                        	    <tr><td><a href='".$web_url."user/detail/".$senderInfo->userId."'>".$senderInfo->userName."</a></td></tr>
                        	    <tr><td>".$senderInfo->userDesignation."</td></tr>
                        	    <tr><td>".$senderInfo->userDepartment."</td></tr>
        	            	    </table>
	            	        </td>
	            	    </tr>
	            	    </table>
	            	    </td>
	            	    </tr>
            	    </tbody>
                    	<tfoot>
                    		<tr><td colspan='2' align='right' style='border-top:1px solid #D5D1B6; color:white;' bgcolor='#3C8DBC'>Powered by - <a href='".$web_url."'>EMS</a> &nbsp;</b></td></tr>
                    	</tfoot>
                    </table>";

	    return $eb;
	}
	
	public function purchase_mail($subject, $receiver, $leaveInfo, $staffInfo, $senderInfo, $web_url, $message){
	     
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $lastHeader = "Request refused by -";
	    $emailBody = $this->att_EmailBody($leaveInfo, $staffInfo, $senderInfo, $web_url, $message, $lastHeader);
	     
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        //success to send refuse mail to staff by manager
	        return true;
	    }else{
	        //failed to send refuse mail to staff by manager
	        return false;
	    }
	}
	
	public function sendEmail($mail){
	    
	    $emailBody = $this->outterFrame($mail['body'], $mail['web_url'] );
	    
	    //print_r($emailBody);
	    
	    $flag = $this->sendMail($mail['subject'], $emailBody, $mail['receiver'], $mail['sender']);
	    
	    return $flag;	    
	}
	
	public function sendMailForAttachment($subject, $receiver, $senderInfo, $insert_id, $web_url, $totalFiles=0, $messageBody='', $msgDate=''){	     
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	    $emailMsgDate = !empty($msgDate) && strlen($msgDate)==10 ? $msgDate : date("d-m-Y");
	    $timestamp = strtotime($emailMsgDate);
	    
	    $emailDate = date('d', $timestamp)."/".date('m', $timestamp)."/".date('Y', $timestamp);
	    $emailBody = "<table width='90%' align='center'><tbody>";
	    $emailBody .= "<tr><td colspan='3' style='font-family:Verdana, sans-serif; font-size:14px;'><a style='color:#0000ff; font-weight:bold;' href='".$web_url."remark/attachment'>".COMPANY_PREFIX." Attachment Board Notice</a></td></tr>
			<tr><td width='50' style='font-family:Verdana, sans-serif; font-size:14px;'>Date</td><td style='font-family:Verdana, sans-serif; font-size:14px;'><b>:</b></td><td style='font-family:Verdana, sans-serif; font-size:14px;'><b>".$emailDate."</b></td></tr>
			<tr><td style='font-family:Verdana, sans-serif; font-size:14px;'>Subject</td><td style='font-family:Verdana, sans-serif; font-size:14px;'><b>:</b></td><td style='font-family:Verdana, sans-serif; font-size:14px; border-bottom:1px dotted #DDDDDD;'><b>".$subject."</b></td></tr>
			<tr><td valign='top'>&nbsp;</td><td>&nbsp;</td><td style='font-family:Verdana, sans-serif; font-size:13px; text-align:justify;'>".$messageBody."</td></tr>";
	    if ($totalFiles > 0){
	        $emailBody .= "<tr><td colspan='3'>&nbsp;</td></tr>";
	        $emailBody .= "<tr><td colspan='3' style='font-family:Verdana, sans-serif; font-size:14px;'><b>$totalFiles</b> file(s) or document(s) attached on Attachment Board of <a href='$web_url'>EMS</a> on ".$emailMsgDate.".</td></tr>";
	        $emailBody .= "<tr><td colspan='3' style='font-family:Verdana, sans-serif; font-size:14px;'>Please open the following link to find attached <a style='color:#0000ff;' href='".$web_url."remark/attachment'>Attachment Board</a></td></tr>";
	    }
		$emailBody .= "<tbody></table>";
	    
	    /* $message = "has attached file or document on Attachment Board of <a href='$web_url'>EMS</a> on ".date('Y-m-d').".<br/>";
	    $innerDiv = "<div align='left'>
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$senderInfo->userId."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$senderInfo->userId."'>$sender[name]</a></b></td></tr>
	                    		<tr><td><i>".$senderInfo->userDesignation."</i></td></tr>
                    		<tr><td>".$senderInfo->userDepartment."</td></tr>
                    	</table>
               		    <p>".$message."</p>
           				<p>Please open the following link to find attached ...</p>
	
                		<div align='center' style='padding:10px;'>
                			<a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 3px 20px; border: 1px solid #333333;' href='".$web_url."remark/attachment'>Go to Attachment Board</a>
                		</div>
                    </div>";
	
	    $emailBody = $this->outterDiv($innerDiv, $web_url); */	     
	    //echo $emailBody;
	
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        return true;
	    }else{
	        return false;
	    }
	}
	
	public function leaveMailToManager($subject, $receiver, $leaveInfo, $senderInfo, $message, $web_url ){
	
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;

	    $innerDiv = "<div align='left'>
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$senderInfo->userId."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$senderInfo->userId."'>$sender[name]</a></b></td></tr>
                    			<tr><td><i>".$senderInfo->userDesignation."</i></td></tr>
                    		<tr><td>".$senderInfo->userDepartment."</td></tr>
                    	</table>
                    	<p>".$message."</p>
                    	<p>Open the following link to see the leave request form.</p>
                    
                    	<div align='center' style='padding:10px;'>
                    		<a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 3px 20px; border: 1px solid #333333;' href='".$web_url."leave/request/".$leaveInfo["leave_id"]."'>Leave Request Form</a>
                    	</div>
                    </div>
                    <div align='center' style='padding:5px 10px; margin-top:10px; background-color:#3C8DBC; color:#FFFFFF;'>Leave Details</div>
                    <div align='left'>
                    	<table width='100%' cellpadding='3' cellspacing='0'>
                    		<tr><td width='150'><i>Leave type</i></td><td width='20' align='center'><b>:</b></td><td>&nbsp;".$leaveInfo["leave_name"]."</td></tr>
                    		<tr><td><i>Leave from</i></td><td align='center'><b>:</b></td><td>&nbsp;".$leaveInfo["leave_start"]."</td></tr>
                    		<tr><td><i>Leave to</i></td><td align='center'><b>:</b></td><td>&nbsp;".$leaveInfo["leave_end"]."</td></tr>
                    		<tr><td><i>Period</i></td><td align='center'><b>:</b></td><td>".$leaveInfo["period"]."</td></tr>
                    		<tr><td valign='top'><i>Address during leave</i></td><td align='center' valign='top'><b>:</b></td><td>".$leaveInfo["address_d_l"]."</td></tr>
                    		<tr><td><i>Reason</i></td><td align='center'><b>:</b></td><td>".$leaveInfo["speacial_reason"]."</td></tr>
                    	</table>                    
                    </div>";
	
        $emailBody = $this->outterDiv($innerDiv, $web_url);


        if($this->sendSpecificMail($subject, $emailBody, $receiver, $sender)){
            return true;
	    }else{
	        return false;
	    }
	}
	
	public function requisitionMail($mail, $web_url ){
	    
	    $senderInfo = $mail["senderInfo"];
	    $sender['name'] = $senderInfo->userName;
	    $sender['email'] = $senderInfo->email;
	
	    $innerDiv = "<div align='left'>
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$senderInfo->userId."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$senderInfo->userId."'>$sender[name]</a></b></td></tr>
	                    		<tr><td><i>".$senderInfo->userDesignation."</i></td></tr>
                    		<tr><td>".$senderInfo->userDepartment."</td></tr>
                    	</table>
                    	<p>".$mail["message"]."</p>";
	    
	    if(isset($mail['voucher_id'])){
	        
	        $innerDiv .= "<p>Open the following link to see the requisition request form.</p>	
                    	<div align='center' style='padding:10px;'>
                    		<a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 3px 20px; border: 1px solid #333333;' href='".$web_url."requisition/form/".$mail['voucher_id']."'>Requisition Request Form</a>
                    	</div>";
	    }
	    $innerDiv .= "</div>";
                    
	
        $emailBody = $this->outterDiv($innerDiv, $web_url);	
	
	    if($this->sendMail($mail["subject"], $emailBody, $mail["receiver"], $sender)){
	        
            return true;
        }else{
            return false;
        }
	}
	
	
	public function passwordMail($mail, $password,  $web_url ){
	     
	    $empInfo = $mail["empInfo"];
	    $sender['name'] = "EMS";
	    $sender['email'] = "hrd@genuitysystems.com";
	
	    $innerDiv = "<div align='left'>	        
	                    ".$mail['msg_1']."	        
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$empInfo->emp_id."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$empInfo->emp_id."'>$empInfo->name</a></b></td></tr>
	                    		<tr><td><i>".$empInfo->designation."</i></td></tr>
                    		<tr><td>".$empInfo->dept_name."</td></tr>
                    	</table>
                    	<h3>Credential Information:</h3>
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td><b>User ID:</b></td><td>".$empInfo->emp_id."</td></tr>
                    		<tr><td><b>Password:</b></td><td>".$password."</td></tr>
                    	</table>
                    	<p>".$mail['msg_2']."</p> 
                    	<p>Thank you.</p>
                    </div>";

	
    	$emailBody = $this->outterDiv($innerDiv, $web_url);
    	
    	if($this->sendMail($mail["subject"], $emailBody, $mail["receiver"], $sender)){
    	 
    	   return true;
    	}else{
    	    return false;
    	}
	}
	
	public function sendMailForAddingManualFingerprint($subject, $receiver, $staffInfo, $sender, $mailData, $web_url ){
	    
	    $row = ($mailData['logIn'])? "<tr><td>IN Att.</td><td width='20' align='center'><b>:</b></td><td>&nbsp;".$mailData['logIn']."</td></tr>" : "";
	    $row .= ($mailData['logOut'])? "<tr><td>OUT Att.</td><td width='20' align='center'><b>:</b></td><td>&nbsp;".$mailData['logOut']."</td></tr>" : "";
	    
	    $innerDiv = "<div align='left'>
                    	<table cellpadding='3' cellspacing='0'>
                    		<tr><td>".$staffInfo->emp_id."</td></tr>
                    		<tr><td><b><a href='".$web_url."user/detail/".$staffInfo->emp_id."'>".$staffInfo->name."</a></b></td></tr>
	                    		<tr><td><i>".$staffInfo->designation."</i></td></tr>
                    		<tr><td>".$staffInfo->dept_name."</td></tr>
                    	</table>
                    	<p>This employee's missing attendance is uploaded manually by admin and the reason behind missing attendance.<br><b>Reason: </b>".$mailData["message"]."</p>
                    	<p>Open the following link to see the attendance report.</p>
	
                    	<div align='center' style='padding:10px;'>
                    		<a style='font: bold 15px; text-decoration: none; background-color: #798B9F; color: black; padding: 3px 20px; border: 1px solid #333333;' href='".$web_url."attendance/show/'>Attendance Report Page</a>
                    	</div>
                    </div>
                    <div align='center' style='padding:5px 10px; margin-top:10px; background-color:#3C8DBC; color:#FFFFFF;'>Manual Attendance Info</a></div>
                    <div align='left'>
						<table width='100%' cellpadding='5' cellspacing='0'>
							<tr><td>Date</td><td align='center'><b>:</b></td><td>&nbsp;".$mailData['date']."</td></tr>
							<tr><td>Day</td><td align='center'><b>:</b></td><td>&nbsp;". date('l', strtotime($mailData['date'])) ."</td></tr>							
							".$row."    
						</table>
                    </div>";
	
        $emailBody = $this->outterDiv($innerDiv, $web_url);
        
        
	    if($this->sendMail($subject, $emailBody, $receiver, $sender)){
	        return true;
    	} else{
	        return false;
	    }
	}
	
	
	
	public function outterFrame($innerBody, $werb_url){
	    $body = "<table>
                <thead style='width:100%; color:white;' bgcolor= '#3C8DBC' >
                <tr><th colspan=2>".COMPANY_PREFIX." Staff</th></tr>
                </thead>
                <tbody>"
                .$innerBody.
                "
                </tbody>
                <tfoot>
                <tr><td colspan='2' align='right' style='border-top:1px solid #D5D1B6; color:white;' bgcolor='#3C8DBC'>Powered by - <a href='" . $werb_url . "'>EMS</a> &nbsp;</b></td></tr>
                </tfoot>
                </table>";
	    
	    return $body;
	}
	
	public function outterDiv($innerDiv, $web_url){
	    
	    $body = "<div style='padding:5px;  border: 1px solid gray'>
            	<div align='center' style='padding:5px 10px; margin-bottom:10px; background-color:#3C8DBC; color:#FFFFFF;'>
            		".COMPANY_PREFIX." Staff
            	</div>"
            	.$innerDiv.
            	"<div align='right' style='padding:5px 10px; margin-top:10px; background-color:#3C8DBC; color:#FFFFFF;'>
                Powered by - <a href='#'>EMS</a>
                </div>
                </div>";
            	    
	    
	    return $body;
	}
		
}
?>