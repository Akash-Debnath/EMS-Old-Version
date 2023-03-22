<?php echo link_tag('assets/css/leave.css');


//echo "sdfdf";
$s_i = "disabled";
$m_i = "disabled";
$a_i = "disabled";
$s_c = "disabled";
$m_c = "disabled";
$a_c = "disabled";
$flag = false;


$approver_name = "";
$approver_desig = "";
$approver_dept = "";


$cancel_approver_name="";


if(empty($leaveId)){
    //echo "one";
    $s_i = ""; 
     
} else {
    
    
    if( ($myInfo->userId == $leaveInfo->emp_id)
        || in_array($myInfo->userId, $manager)
        || in_array($myInfo->userId, $admin)
        || $bossFlag )
    {
        //hass access to this page.
        
        if( ($myInfo->userId == $leaveInfo->emp_id) ){
            //staff editable section
            
            
        
            if(empty($leaveInfo->m_approved_date)){
                //echo"test 5";
                $s_i = "";
                $flag = true;
            }else if(!empty($leaveInfo->m_approved_date) && !empty($leaveInfo->admin_approve_date) && empty($leaveInfo->cancel_req_date)){
                
                $s_c = "";
        
            }
             
        }
        
        if( ($myInfo->userId != $leaveInfo->emp_id) && in_array($myInfo->userId, $manager) ){
            //manager editable section
        
            if(empty($leaveInfo->m_approved_date)){
                $m_i = "";
                
            }else if(!empty($leaveInfo->cancel_req_date)){
                $m_c = "";
                $cancel_approver_name  = $myInfo->userName;
            }
        
        }
        
        if ( !empty($leaveInfo->m_approved_date) && in_array($myInfo->userId, $admin) ){
            //admin editable section
        
            if(empty($leaveInfo->admin_approve_date)){
                $a_i = "";
            }else if(!empty($leaveInfo->cancel_req_date)){
                $a_c = "";
            }
        }
        
        if( $bossFlag ){
            //Management editable section
        
            if(empty($leaveInfo->m_approved_date)){
                $m_i = "";
            }else if(!empty($leaveInfo->cancel_req_date)){
                $m_c = "";
            }
        }

        
    }else{
               
        echo "no access to this page";
        exit();        
    }
    
/* end of main brace */
} 
    
    


if(!$isCancel){
    
    $s_c = "disabled";
}

?>


<style>
<!--
    .m-t{
	margin-top: 5px;
    }
-->
</style>



<div class="row">
	<div class="col-lg-10 col-lg-offset-1">

		<div class="box box-info">
			<div class="box-header">
				<div class="form-group">
					<div class="col-sm-12 text-center">
                  		<?php echo img('assets/images/genuity.gif'); ?>
                </div>
					<h3 class='text-center'>Leave Request Form</h3>
					<div class="col-sm-6">
						<label>Employee Name:</label> <span class='dashUnderline'><?php if(isset($applierInfo->name)) echo $applierInfo->name; else echo $myInfo->userName;?></span>
					</div>
					<div class="col-sm-6">
						<label>Employee ID:</label> <span class='dashUnderline'><?php if(isset($applierInfo->emp_id)) echo $applierInfo->emp_id; else echo $myInfo->userId;?></span>
					</div>
					<div class="col-sm-6">
						<label>Department:</label> <span class='dashUnderline'><?php if(isset($applierInfo->dept_name)) echo $applierInfo->dept_name; else echo $myInfo->userDepartment;?></span>
					</div>
					<div class="col-sm-6">
						<label>Designation:</label> <span class='dashUnderline'><?php if(isset($applierInfo->designation)) echo $applierInfo->designation; else echo $myInfo->userDesignation;?></span>
					</div>
				</div>
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<div class='row'>
					<div class='col-sm-7'>

						<div class="row">
							<div class='col-xs-12'>
								<div class="leaveHeader">Leave Request Form</div>
							</div>
						</div>
						<div class='row'>
							<div class='col-xs-12'>
								<div class='box'>
									<div class='box-body'>
										<form id='uploadForm'>
											<input type='hidden' id='leavePeriod' name='leavePeriod' value="<?php if(isset($leaveInfo->period)) echo $leaveInfo->period;?>">
                            	        <?php if(empty($leaveId) || (!empty($leaveId) && empty($leaveInfo->m_approved_date) && $flag ) ) { ?>
                            
                            	        <div class="form-group">
												<label for="leaveType">Leave Type:</label> <select
													class="selectpicker" name="leaveType" id='leaveType'
													data-live-search="false" <?php echo $s_i?>>
													<option value=''>---Select appropriate type---</option>
													
                                    			<?php foreach ($leave_type as $key=>$val) {   
                                    			                 			    
                                    			    if(($applierInfo->gender == 'M' && $key =='ML') || ($applierInfo->gender == 'F' && $key =='PL')) continue;                                    			    
                                    			    if($key == 'CA') continue;
                                    			    
                                    			    if( ($key == 'AL' || $key == 'HL') && $leaveInfo->leave_type == 'CA' ){
                                    			        
                                    			        if( ($key == 'HL' && !empty($leaveInfo->time_slot)) || $key == 'AL' && empty($leaveInfo->time_slot) ){
                                    			            
                                    			            echo "<option value='".$key."' selected='selected' >".$val." leave"."</option>";
                                    			        }else{
                                    			            echo "<option value='".$key."' >".$val." leave"."</option>";
                                    			        }    
                                    			        
                                    			    } else if( ($leaveInfo->leave_type == $key)){
                                    			        
                                    			        echo "<option value='".$key."' selected='selected' >".$val." leave"."</option>";
                                    			    }
                                    			    else {
                                    			        echo "<option value='".$key."' >".$val." leave"."</option>";
                                    			    }    
                                    				    
                                    			    
                                    			} ?>
                                	        </select>
											</div>

                                            <p><label>Balance:</label> &nbsp; <span id="balance"></span></p>
                                            
                                            <?php if(false && date('m') <= ANNUAL_LEAVE_MONTH_LIMIT){ ?>    										
    										<div id="takeFromDiv" class="form-group">
												<label class='' for="leaveStart" >Take From:</label>

											    <select class='selectpicker' id='takeFromAnnual' name='takeFromAnnual'>
											        <option value="AL"> Current Availabe</option>
											        <option value="CA" <?php if(isset($leaveInfo->leave_type) && $leaveInfo->leave_type == 'CA') echo "selected";?> > Carry Forwarded</option>														        														    
											    </select>
											</div>											
											<?php } ?>
											
											<div class="" id="halfLeave" style="display: none;">
												<!-- p><label>Balance:</label> &nbsp;<?php //echo $available_current['HL']." Day(s)";?></p -->

												
												<div class="form-inline">
													<div class="form-group">
														<label for="leaveDate">Date:</label> <input type="text"
															class="form-control" id="leaveDate" name="leaveDate"
															placeholder="yyyy-mm-dd"
															value="<?php echo $leaveInfo->leave_start; ?>"
															<?php echo $s_i;?>>
													</div>

												</div>
												<div class="form-inline">
    												<div class="form-group">
    													<label for="timeSlot">Time Slot:</label> 
    													<select	class="selectpicker" id='timeSlot' name="timeSlot"
    														data-live-search="false" <?php echo $s_i;?>>
    														<option value="">---Select---</option>
    														<option value="FH"
    															<?php if($leaveInfo->time_slot == "FH") echo "selected";?>>First
    															Half</option>
    														<option value="SH"
    															<?php if($leaveInfo->time_slot == "SH") echo "selected";?>>Second
    															Half</option>
    													</select>
    												</div>
    											</div>
    											
    										</div>

											<div class="" id="otherLeave">
												<div class='form-inline'>
													<div class="form-group">
														<label for="leaveStart">From:</label> <input type="text"
															class="form-control dateInputWidth" id="leaveStart" name="leaveStart"
															placeholder="yyyy-mm-dd"
															value="<?php echo $leaveInfo->leave_start ?>"
															<?php echo $s_i?> >
													</div>
													<div class="form-group">
														<label for="leaveEnd">To:</label> <input type="text"
															class="dateInputWidth form-control " id="leaveEnd" name="leaveEnd"
															placeholder="yyyy-mm-dd"
															value="<?php echo $leaveInfo->leave_end?>"
															<?php echo $s_i?>>
													</div>

													<span> <label>Day(s):</label><span class="dashUnderline"
														id="totalDay"><?php echo $leaveInfo->period?></span>
													</span>
												</div>
											</div>

											<div class="" id="sickLeave">
												<div class='row'>
												    <div class="col-xs-12">
    													<div class="form form-inline">
    														<div class="form-group">
    															<label for="attachment">Medical Prescription: </label>
    															<a class="btn btn-default" href='javascript://'	id='attachment'>Browse attachment</a>
    															
    														</div>
                                                        </div>
    													<label for="">Uploaded File: </label>
    													<div class="" id="uploadedFileDiv"></div>    
    													<hr>
                                        			    <?php if(!empty($leaveId) && empty($leaveInfo->m_approved_date) && $flag ){?>
                                    					<div class="col-xs-12">
    															<p>
    																<b>Attaced Files(<?php echo count($leaveFiles); ?>):</b>
    															</p>
    															<table id='leaveFileTable' class='table'>
    																<tbody>
                                                    		    <?php
                                                    		        $i = 0;
                                                    		        foreach ($leaveFiles as $file){
                                                    		            echo "<tr><td class='serial'>";		            
                                                                        echo ++$i;
                                                                        echo "</td><td>";
                                                                        
                                                                        echo $file->original_file_name."&nbsp; &nbsp;<a href = 'javascript://' class='removeLeaveFile btn btn-xs btn-danger' data-id='".$file->id."' data-name='".$file->file_name."'>Remove</a>&nbsp; &nbsp;";
                                                                        echo "<a href='".base_url()."leave/download/".$file->id."' class='btn btn-xs btn-primary'>Download</a>";
                                                    		            echo "</td></tr>";
                                                    		        }
                            
                                                    		    ?>
                                                            	</tbody>
															</table>

														</div>         			    
                                			        <?php }?>
                                        		    </div>
												</div>
											</div>
                            			
                            		    <?php } else { ?>
                            		    
                            			<div class='row '>
												<div class='row col-xs-12'>
													<label class="col-xs-3" for='text1'>Leave Type: </label>
													<span class="col-xs-6" id='text1'> <?php
													    echo $leave_type[$leaveInfo->leave_type]." leave";
													?>
													</span>
												</div>
												<div class='row col-xs-12'>
													<label class="col-xs-3" for='text2'>From: </label> <span
														class="col-xs-4" id='text2'><?php echo $leaveInfo->leave_start;?></span>
												</div>
												<div class='row col-xs-12'>
													<label class="col-xs-3" for='text3'><?php if($leaveInfo->leave_type == "HL"){ echo 'Time-slot:';} else{ echo 'To:';}?></label>
													<span class="col-xs-4" id='text3'><?php if($leaveInfo->leave_type == "HL") echo $half_leave_slot[$leaveInfo->time_slot];
                                  		        else echo $leaveInfo->leave_end; ?></span>
												</div>
												<div class='row col-xs-12'>
													<label class="col-xs-3" for='text4'>Period: </label> <span
														class="col-xs-4" id='text4'><?php echo $leaveInfo->period." Day(s)";?></span>
												</div>
												<div class="col-xs-12 fileBox">
													<p>
														<b>Attaced Files(<?php echo count($leaveFiles); ?>):</b>
													</p>
                                    		    <?php
                                    		        $i = 0;
                                    		        foreach ($leaveFiles as $file){		            
                                    		            echo '<div class="">';
                                    		            if(empty($file->original_file_name)) echo ++$i.'. Attached File<br>';
                                    		            else echo ++$i.'. '.$file->original_file_name.'&nbsp;';
                                    		            echo ' <a type ="button" href = "'.base_url().'leave/download/'.$file->id.'" class="btn btn-xs btn-primary downFile" title="Download">Download</a>';
                                    		            echo '</div>';
                                    		            echo '<div class="clearfix"></div>';
                                    		        }
                                    		    ?>
                                    		</div>
											</div>
                            
                                  	    <?php } ?>
                                  	    
                            			<div class="form-group">
												<label>Address During Leave:</label>
												<textarea class="form-control" rows="2" id="address"
													name="address" <?php echo $s_i?>><?php echo $leaveInfo->address_d_l; ?></textarea>
											</div>

											<div class="form-group">
												<label>Special Reason for Leave:</label>
												<textarea class="form-control" rows="2" id="reason"
													name="reason" <?php echo $s_i?>><?php echo $leaveInfo->speacial_reason; ?></textarea>
											</div>

											<p>I declare that I shall rejoin for duty on expiry of the
												granted leave and will not apply for any extension except
												under unavoidable circumstances.</p>

											<div class="form-group">
												<div class="col-sm-7 ">
													<span class="col-sm-12"> <i class="dashUnderline"><?php echo $leaveInfo->employee_name;?></i></span>
													<label for="firstname" class="col-sm-12  ">Applicant's
														signature</label>
												</div>
												<div class="col-sm-5">

													<span class="col-sm-12"> <i class="dashUnderline"><?php echo $leaveInfo->leave_date; ?></i></span>
													<label for="firstname" class="col-sm-12 control-label ">Date</label>
												</div>
											</div>             
                                        <?php if(empty($leaveId) || (!empty($leaveId) && empty($leaveInfo->m_approved_date) && $flag ) ){ ?>
                                            <div
												class="form-group">
												<div class="col-sm-12 center">
													<input type='submit' class="btn btn-primary btn-sm pull-right"
														id="btn_send_leave" <?php echo $s_i?>
														value="<?php if(empty($leaveId)) echo 'Send'; else echo 'Update';?> Leave Request">
												</div>
											</div>
                                        <?php } ?>
                                    <div class='clearfix'></div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Left Panel  -->
					<div class="col-sm-5">
						<div class="row">
							<div class="col-sm-12 leaveHeader">Leave Approval</div>
						</div>
						<div class='row'>
							<div class='box'>
								<div class="box-header">
									<h4 class="box-title">Comments of The Leave Approver</h4>
								</div>
								<div class='box-body'>
									<div class="form-group">
										<div class="checkbox">
											<label><input type="checkbox" value="Y" id="comment1"
												name="comment1"
												<?php echo $m_i; if($leaveInfo->comment1 == 'Y') echo ' checked'; ?>>
												I am satisfied that grant of leave </label>
										</div>

										<div class="checkbox">
											<label><input type="checkbox" value="Y" id="comment2"
												name="comment2"
												<?php echo $m_i; if($leaveInfo->comment2 == 'Y') echo ' checked'; ?>>
												Will not prejudice the normal works </label>
										</div>

										<div class="checkbox">
											<label><input type="checkbox" value="Y" id="comment3"
												name="comment3"
												<?php echo $m_i; if($leaveInfo->comment3 == 'Y') echo ' checked'; ?>>
												His/Her duties will be carried out by </label>
										</div>
									</div>
									
									<?php if(isset($leaveInfo->manager_remark) && !empty($leaveInfo->manager_remark)) {

									    echo "<p><label>Approver Remark:</label> ".$leaveInfo->manager_remark."</p>";
									}?>

									<div class="form-group">
										<div class="radio-inline">
											<label for="manApproveRadio"> <input type="radio" name="approve"
												id="manApproveRadio"
												data-url="<?php echo base_url()?>leave/confirm_m/<?php echo $leaveId?>"
												data-reload="<?php echo base_url()?>leave/show/"
												value="yes"
												<?php echo $m_i; if(!empty($leaveInfo->manager_id)) echo ' checked'; ?>>
												Approve
											</label>
										</div>
										<div class="radio-inline">
											<label for="manRefuseRadio"> <input type="radio" name="approve"
												id="manRefuseRadio"
												data-message="Give an excuse to refuse this leave request:"
												data-url="<?php echo base_url()?>leave/refuse_m/<?php echo $leaveId?>"
												data-reload="<?php echo base_url()?>leave/show/"
												value="no" <?php echo $m_i?>> Refuse
											</label>
										</div>
									</div>

									<div class="form-group">
										<div class="col-sm-12 ">
											<table class="smallTable">
												<tbody>
													<tr>
														<td><i class="dashUnderline2">
														<?php
														if(isset($leaveInfo->manager_name) && !empty($leaveInfo->manager_name)){
														    echo $leaveInfo->manager_name;
														} else if(empty($m_i)){
														   
														    echo $myInfo->userName;
														}
														?></i></td>
													</tr>
													<tr>
														<td><b>Signature</b></td>
													</tr>
													<tr>
														<td>
														<?php
														if(isset($leaveInfo->manager_id) && !empty($leaveInfo->manager_id)){
														    echo $leaveInfo->manager_desig.", ".$leaveInfo->manager_dept; 
														} else if(empty($m_i)){
														    echo $myInfo->userDesignation.", ".$myInfo->userDepartment;
														}
														?>
														</td>
													</tr>
													<tr>
														<td>Date: <i class="dashUnderline"><?php if(!empty($leaveInfo->m_approved_date)){
														    echo $leaveInfo->m_approved_date; 
														}else if(empty($m_i)){
														    echo date("Y-m-d");
														}?></i></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
									<div class='clearfix'></div>
								</div>
							</div>
						</div>
						<!-- end manager panel -->
						<!-- left side table -->
						<div class='row'>
							<div class='box'>
								<div class='box-header'>
									<h3 class='box-title'>Leave status: <?php echo $year;?></h3>
								</div>
								<div class='box-body'>
									<table class="table table-bordered table-condensed">
										<thead>
											<tr>
												<th>Leave</th>
												<th>Total</th>
												<th>Taken</th>
												<th>Available</th>
											</tr>
										</thead>
										<tbody>
                              	            <?php
                              	            //$forwardAnnualAvailable = $forward_annual_leave - $taken['CA'];
                              	            
                              	            
                                            foreach ($leave_type as $key => $val) {                                                
                                                                                                                                                
                                                if(isset($genuity_leaves_array[$key])){
                                                    continue;
                                                }
                                                
                                                if($key == 'CA') continue;
                                                
                                                echo "<tr>";
                                                // Leave (1st column)
                                                echo "<td>&nbsp;" . $val . "</td>"; 
                                                                           
                                                // Total (2nd column) 
                                                if ($key == 'SLM') {
                                                    
                                                    echo "<td class='text-center' ><span id='sickId' class='forwardColor'>" . $total_forward_sick_leave . "</span> + " . $total_current[$key] . "</td>";
                                                } else if ($key == 'HL') {
                                                    
                                                    $cfaText = "";
                                                    if(date('m') <= ANNUAL_LEAVE_MONTH_LIMIT ){
                                                        $cfaText = "<span class='forwardColor'>" . $forward_annual_leave . "</span> + ";
                                                    }
                                                   echo "<td class='text-center' rowspan='2' style='vertical-align: middle;'>". $cfaText . $total_current[$key] . "</td>";
                                                }else if($key == 'AL'){
                                                    
                                                } else {
                                                    
                                                    echo "<td class='text-center' >" . $total_current[$key] . "</td>";
                                                }     
                                                                       
                                                // Taken (3rd column)
                                                
                                                if($key == 'AL'){
                                                    
                                                    $cfaText = "";
                                                    $cfaText = "<span class='forwardColor'>" . ($forward_annual_leave - $forwardAnnualAvailable) . "</span> + ";
                                                    echo "<td class='text-center' >" . $cfaText . $taken[$key] . "</td>";
                                                    
                                                }else{

                                                    echo "<td class='text-center' >" . $taken[$key] . "</td>";
                                                }

                                                
                                                // Available (4th column)
                                                if ($key == 'SLM') {
                                                    
                                                    $total_forward_sick_leave_available = $total_forward_sick_leave;
                                                    
                                                    if ($available_current[$key] < 0 && $total_forward_sick_leave >= abs($available_current[$key])) {
                                                        
                                                        $total_forward_sick_leave_available += $available_current[$key];
                                                        $available_current[$key] = 0;
                                                    }
                                                    
                                                    echo "<td class='text-center' ><span class='forwardColor'>" . $total_forward_sick_leave_available . "</span> + " . $available_current[$key] . "</td>";
                                                    
                                                } else if ($key == 'AL') {
                                                        // empty
                                                } else if ($key == 'HL') {
                                                    
                                                    //$fal = $forwardAnnualAvailable;                                                    
                                                    
                                                    if ($available_current[$key] < 0 && $forward_annual_leave >= abs($available_current[$key])) {
                                                        
                                                        //$fal = $forward_annual_leave + $available_current[$key];
                                                        $available_current[$key] = 0;
                                                    }
                                                    
                                                    $cfaText = "";
                                                    if(date('m') <= ANNUAL_LEAVE_MONTH_LIMIT ){
                                                        $cfaText = "<span class='forwardColor'>" . $forwardAnnualAvailable . "</span> + ";
                                                    }
                                                                                                        
                                                    echo "<td rowspan='2' class ='gn text-center' style='vertical-align: middle;' >". $cfaText . $available_current[$key] . "</td>";
                                                    
                                                } else {
                                                    echo "<td class='text-center'>" . $available_current[$key] . "</td>";
                                                }
                                                echo "</tr>";
                                            }
                                            
                                            ?>
                                            <tr><td colspan="4" class="success text-bold">Leave in Genuity Life</td>
                                            </tr>
                              	            <?php
                              	            foreach ($genuity_leaves_array as $key=>$val) {
                              	                
                              	                if( (empty($applierInfo->gender) && $key =='ML') || ($applierInfo->gender == 'F' && $key =='PL' ) || ($applierInfo->gender != 'F' && $key =='ML' )) continue;
                              	                
                              	                echo "<tr>";
                              	                echo "<td>".$val."</td>";
                              	                echo "<td class='text-center'>".$total_current[$key]."</td>";
                              	                echo "<td class='text-center'>".$genuity_leave_taken[$key]."</td>";	    
                              	                
                              	                echo "<td class='text-center'>". ((($total_current[$key] - $genuity_leave_taken[$key]) >=0) ? ($total_current[$key] - $genuity_leave_taken[$key]) : 0 ) ."</td>";
                              	                
                              	                echo "</tr>";
                              	            }?>
                    					</tbody>
									</table>
								</div>
							</div>
						</div>
						<!-- end left table -->
					</div>
					<!-- end left panel -->
				</div>

			</div>
			<!-- /.box-body -->
			<div class="box-footer">
				<div class="row">
					<div class='col-xs-12'>
						<div class="leaveHeader">Leave Verification</div>
					</div>
				</div>

				<div class="row">
					<div class="col-xs-12">
						<div class='box'>
							<div class='box-body'>
								<p>
									The leave application for <span class="dashUnderline2"><?php echo $leaveInfo->period?></span>
									days <i class="dashUnderline2"><?php 
									if(!empty($leaveInfo->leave_type)){ 
									    echo $leave_type[$leaveInfo->leave_type].' leave';
									}?>
									</i>
									for <i class="dashUnderline2"><?php echo $leaveInfo->leave_start?></i>
									to <i class="dashUnderline2"><?php echo $leaveInfo->leave_end?></i>
								</p>
								<ul>
									<li>has duly approved by competent authority and that the
										leave has duly been recorded</li>
									<li>has not been approved by the competent authority</li>
								</ul>
								
								
								<?php if(isset($leaveInfo->admin_remark) && !empty($leaveInfo->admin_remark)) {

								    echo "<p><label>Admin Remark:</label> ".$leaveInfo->admin_remark."</p>";
								}?>

								<div class="form-group">
									<div class="radio-inline">
										<label for="adminVerifyRadio"> <input type="radio" name="verified"
											id="adminVerifyRadio" value="yes"
											data-message="Are you sure you want to verify this leave request?"
											data-url="<?php echo base_url()?>leave/confirm_a/<?php echo $leaveId?>"
											data-reload="<?php echo base_url()?>leave/show/"
											<?php echo $a_i; if(!empty($leaveInfo->admin_approve_date)) echo ' checked';?>>
											Verified for Record
										</label>
									</div>
									<div class="radio-inline">
										<label for="radio4"><input type="radio" name="verified"
											id="radio4"
											data-message="Give an excuse to refuse this leave verification:"
											data-url="<?php echo base_url()?>leave/refuse_a/<?php echo $leaveId?>"
											data-reload="<?php echo base_url()?>leave/show/" value="no"
											<?php echo $a_i?>> Refuse </label>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-3">
										<span class="col-sm-12"> <i class="dashUnderline2"><?php echo $leaveInfo->admin_name ?></i></span>
										<label class="col-sm-12">Verified by</label>
									</div>
									<div class="col-sm-3">
										<span class="col-sm-12"> <i class="dashUnderline2"><?php echo $leaveInfo->admin_approve_date ?></i></span>
										<label class="col-sm-12">Date</label>
									</div>
								</div>
								<div class='clearfix'></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /.box-footer -->
			<div class='box-footer'>
				<div class="row">
					<div class='col-xs-12'>
						<div class="leaveHeader">Leave Cancelation</div>
					</div>
				</div>
				<div class='row'>
					<div class='col-sm-4 col-xs-12'>
						<div class='box'>
							<div class='box-body'>
								<p>
									Here by I request to cancel my leave from <span
										class="dashUnderline2"><?php if($isCancel || !empty($leaveInfo->cancel_req_date)) echo $leaveInfo->leave_start;?></span>
									to <i class="dashUnderline2"><?php if($isCancel || !empty($leaveInfo->cancel_req_date)) echo $leaveInfo->leave_end;?></i>
									no. of day(s) <i class="dashUnderline2"><?php if($isCancel || !empty($leaveInfo->cancel_req_date)) echo $leaveInfo->period;?></i>.
								</p>

								<label for="cancellation"><input type="checkbox" onclick=""
									id="cancellation" name="cancellation"
									data-message="Are you sure you want to send request to cancel this leave?"
									data-url="<?php echo base_url()?>leave/cancel_leave/<?php echo $leaveId?>"
									data-reload="<?php echo base_url()?>leave/show/"
									<?php echo $s_c; if(!empty($leaveInfo->cancel_req_date)) echo ' checked';?>>
									Send Request to Cancel Leave</label>

								<div class="form-group">
								    <div class='row'>
    									<div class="col-sm-7 ">
    										<span class="col-sm-12"> <i class="dashUnderline2"><?php if($isCancel || !empty($leaveInfo->cancel_req_date)) echo $leaveInfo->employee_name; ?></i></span>
    										<label class="col-sm-12  ">Applicant's signature</label>
    									</div>
    									<div class="col-sm-5">
    										<span class="col-sm-12"> <i class="dashUnderline2"><?php if($isCancel || !empty($leaveInfo->cancel_req_date)) echo date('d-m-Y'); ?></i></span>
    										<label class="col-sm-12">Date</label>
    									</div>
    								</div>	
								</div>
							</div>
						</div>
					</div>

					<div class='col-sm-4 col-xs-12'>
						<div class='box'>
							<div class='box-body'>
								<div class="form-group">
									<div class="radio">
										<label for="radio5"><input type="radio"
											name="approveCancelManger" id="radio5"
											data-message="Are you sure you want to approve this leave cancellation request?"
											data-url="<?php echo base_url()?>leave/cancel_approve/<?php echo $leaveId?>"
											data-reload="<?php echo base_url()?>leave/show/"
											value="yes" <?php echo $m_c?>> <b>Approve</b> </label>
									</div>
									<div class="radio">
										<label for="radio6"><input type="radio"
											name="approveCancelManger" id="radio6"
											data-message="Give an excuse to refuse this leave cancellation request:"
											data-url="<?php echo base_url()?>leave/cancel_refuse/<?php echo $leaveId?>"
											data-reload="<?php echo base_url()?>leave/show/" value="no"
											<?php echo $m_c?>> <b>Refuse</b> </label>
									</div>
								</div>

								<table class="">
									<tbody>
										<tr>
											<td><i class='dashUnderline2'><?php if(!empty($leaveInfo->cancel_req_date) && empty($leaveInfo->m_approved_date) && in_array($myInfo->userId, $manager)) echo $myInfo->userName;?></i></td>
										</tr>
										<tr>
											<td><b>Signature</b></td>
										</tr>
										<tr>
											<td>Head of the Department</td>
										</tr>
										<tr>
											<td>Date: <i class="dashUnderline2"><?php if( !empty($leaveInfo->cancel_req_date) && empty($leaveInfo->m_approved_date)) echo date('d-m-Y'); ?></i></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class='col-sm-4 col-xs-12'>
						<div class='box'>
							<div class='box-body'>
								<div class="form-group">
									<div class="radio">
										<label for="radio7"><input type="radio"
											name="approveCancelAdmin" id="radio7"
											data-message="Are you sure you want to approve this leave cancellation request?"
											data-url="<?php echo base_url()?>leave/cancel_approve/<?php echo $leaveId?>"
											data-reload="<?php echo base_url()?>leave/show/"
											value="yes" <?php echo $a_c?>> <b>Approve</b> </label>
									</div>
									<div class="radio">
										<label for="radio8"><input type="radio"
											name="approveCancelAdmin" id="radio8"
											data-message="Give an excuse to refuse this leave cancellation request:"
											data-url="<?php echo base_url()?>leave/cancel_refuse/<?php echo $leaveId?>"
											data-reload="<?php echo base_url()?>leave/show/" value="no"
											<?php echo $a_c?>> <b>Refuse</b> </label>
									</div>
								</div>

								<table class="smallTable">
									<tbody>
										<tr>
											<td><i class='dashUnderline2'><?php if( !empty($leaveInfo->m_approved_date) && empty($leaveInfo->admin_approve_date) && in_array($myInfo->userId, $admin)) echo "".$myInfo->userName."";?></i></td>
										</tr>
										<tr>
											<td><b>Signature</b></td>
										</tr>
										<tr>
											<td>Date: <i class="dashUnderline2"><?php  if( !empty($leaveInfo->m_approved_date) && empty($leaveInfo->admin_approve_date) && in_array($myInfo->userId, $admin)) echo date('d-m-Y'); ?></i></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /.box-footer -->

		</div>
		<!-- end main box -->
	</div>

</div>


<!-- modal -->
<!-- Modal Dialog -->
<div class="modal fade" id="confirmApproveModal" role="dialog"
	aria-labelledby="confirmApproveLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure about this ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="confirmCancel">Cancel</button>
				<button type="button" class="btn btn-primary" id="confirmApprove">Confirm</button>
			</div>
		</div>
	</div>
</div>

<!-- Manager Approve Modal Dialog -->
<div class="modal fade" id="manApproveModal" role="dialog"
	aria-labelledby="manApproveModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to approve this leave request?</p>
				<form>
					<div class="form-group">
						<label for="excuseText" class="control-label">Add a Remark:</label>
						<textarea class="form-control" id="approveText"></textarea>
					</div>
				</form>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="confirmCancel">Cancel</button>
				<button type="button" class="btn btn-primary" id="manApproveBtn">Confirm</button>
			</div>
		</div>
	</div>
</div>

<!-- Admin Verify Modal Dialog -->
<div class="modal fade" id="adminVerifyModal" role="dialog"
	aria-labelledby="manApproveModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to verify this leave request?</p>
				<form>
					<div class="form-group">
						<label for="excuseText" class="control-label">Add a Remark:</label>
						<textarea class="form-control" id="verifyText"></textarea>
					</div>
				</form>
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="confirmCancel">Cancel</button>
				<button type="button" class="btn btn-primary" id="adminVerifyBtn">Confirm</button>
			</div>
		</div>
	</div>
</div>

<!-- Refuse Modal Dialog -->
<div class="modal fade" id="refuseModal" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="excuseText" class="control-label">Give an excuse to
							refuse this leave request:</label>
						<textarea class="form-control" name="excuseText" id="excuseText"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="refuseCancel">Cancel</button>
				<button type="button" class="btn btn-danger" id="refuseOk">Refuse</button>
			</div>
		</div>
	</div>
</div>

<!-- Cancellation Modal Dialog -->
<div class="modal fade" id="cancellationModal" role="dialog"
	aria-labelledby="cancellationModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="excuseText" class="control-label">Give an excusable
							reason to request leave cancellation otherwise cancel it</label>
						<textarea class="form-control" name="excuseReason"
							id="excuseReason"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="cancellationCancel">Cancel</button>
				<button type="button" class="btn btn-primary"
					id="cancellationConfirm">Confirm</button>
			</div>
		</div>
	</div>
</div>

<!--Delete Modal Dialog -->
<div class="modal fade" id="deleteModal" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Delete Parmanently</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure You want to delete this file?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" id="deleteConfirm">Delete</button>
			</div>
		</div>
	</div>
</div>

<!-- Dialog Modal -->
<div id='dialogModal' class="modal fade">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header custom-modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;&nbsp;</span>
				</button>
				<h4 class="modal-title">Information</h4>
			</div>
			<div class="modal-body">
				<table class="table table-condensed">

					<thead>
						<tr>
							<th>Year</th>
							<th>Carry Forwarded Leave</th>
						</tr>
					</thead>
					<tbody>
      	            <?php foreach ($forward_sick_leaves as $key=>$val){
      	                echo "<tr>";
      	                echo "<td>".$key."</td>";
      	                echo "<td>".$val."</td>";
      	                echo "</tr>";
      	            }?>
					</tbody>
					<caption align='bottom'>
						<b>Total:</b> <?php echo array_sum($forward_sick_leaves);?> day(s) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Forwarded:</b> <?php echo $total_forward_sick_leave; ?> day(s)
					</caption>
				</table>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->





<link href="<?php echo base_url();?>assets/css/main.css" type="text/css"
	rel="stylesheet" />
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />


<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />
<script type="text/javascript"
	src="//cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.js"></script>


<script>                   
    jQuery(document).ready(function($) {

        var options = {
            beforeSend: function(){
                // Replace this with your loading gif image
                $(".upload-image-messages").html('<p><img src = "<?php echo base_url() ?>images/loading.gif" class = "loader" /></p>');
            },
            complete: function(response){
                // Output AJAX response to the div container
                $(".upload-image-messages").html(response.responseText);
                $('html, body').animate({scrollTop: $(".upload-image-messages").offset().top-100}, 150);               
            }
        }; 
        // Submit the form
        //$(".upload-file-form").ajaxForm(options); 
        return false;
        
    });
</script>
<script type="text/javascript">

var userType = "<?php echo $uType; ?>";
var leaveId = "<?php echo $leaveId; ?>";
var periodDay = '<?php echo $leaveInfo->period; ?>';
var balance = <?php echo json_encode($available_current); ?>;
var sandwichLeave = <?php echo json_encode($sandwichLeave)?>;
var forward_annual_leave = parseFloat("<?php echo $forward_annual_leave?>");

var forwardAnnualAvailable = parseFloat("<?php echo $forwardAnnualAvailable?>");





//confirm modal
var message = '';
var url = '';
var reload = '';

//dissable/enable field
var staff_i = "<?php echo $s_i?>";
var manager_i = '<?php echo $m_i?>';
var admin_i = '<?php echo $a_i?>';
var staff_c = '<?php echo $s_c?>';
var manager_c = '<?php echo $m_c?>';
var admin_c = '<?php echo $a_c?>';
      	            
$(document).ready(function() {

	var attCount = 0;
	$("#attachment").click(function(){

		$("#uploadedFileDiv").append("<div><input type='file' class='upload"+attCount+"' name='upload[]' style='display:none;'></div>");
		
		$(".upload"+attCount).on("change",function(){
			
			var filename = $(this).val().split('\\').pop();
			$(this).parent().append("<div class='m-t'>"+filename+" <button class='btn btn-danger btn-xs fileRemove'>Remove</button></div>");

			$(".fileRemove").unbind("click").bind("click",function(){
				
				$(this).parent().parent().remove();
			});

		});
		
		$(".upload"+attCount).click();		
		attCount++;
	});

	

	if(staff_i != "disabled"){


		
    	var leaveTypeSt = $("#leaveType");    	
    	var halfLeaveSt = $('#halfLeave'); 
        var otherLeaveSt = $('#otherLeave');
        var sickLeaveSt = $('#sickLeave');

        
        if(leaveTypeSt.val() == "HL"){
            
    		halfLeaveSt.show();

    		
    		otherLeaveSt.hide();
    		sickLeaveSt.hide();

    		var code = leaveTypeSt.val();
    		var balancetext = "<span class='forwardColor'>" + forwardAnnualAvailable + "</span> + " + balance[code]+" Day(s)";    		
    		$('#balance').html(balancetext);
    		    
        }else if(leaveTypeSt.val() == "AL"){

    		halfLeaveSt.hide();
    		otherLeaveSt.show();
    		sickLeaveSt.hide();
    		
    		var code = leaveTypeSt.val();
    		var balancetext = "<span class='forwardColor'>" + forwardAnnualAvailable + "</span> + " + balance[code]+" Day(s)";
    		
    		$('#balance').html(balancetext);
            
        }else if(leaveTypeSt.val() == 'SL' || leaveTypeSt.val() == 'SLM'){
    		halfLeaveSt.hide();
    		otherLeaveSt.show();
    		sickLeaveSt.show();
    		var code = leaveTypeSt.val();
    		$('#balance').text(balance[code]+" Day(s)"); 
    		
        }else if(leaveTypeSt.val()){
            
    		halfLeaveSt.hide();
    		otherLeaveSt.show();
    		sickLeaveSt.hide();
    		
    		var code = leaveTypeSt.val();
    		
    		$('#balance').text(balance[code]+" Day(s)"); 
        }
                
    	leaveTypeSt.change(function(){	
        		
    		if($(this).val() == ""){
    			
    			halfLeaveSt.hide();
    			otherLeaveSt.hide();
    			sickLeaveSt.hide();

    			$('#balance').html("");
    			 
    		}else if($(this).val() == "HL"){
        		
    			halfLeaveSt.show();

    			
    			otherLeaveSt.hide();
    			sickLeaveSt.hide();

    			var balancetext;
        		if(forwardAnnualAvailable > 0){
            		
        			balancetext = "<span class='forwardColor'>" + forwardAnnualAvailable + "</span> + " + balance["HL"]+" Day(s)";	
        		}else{
            		
        			balancetext = balance["HL"]+" Day(s)";
        		}
        		
        		$('#balance').html(balancetext);
    			
    		}else if(leaveTypeSt.val() == "AL"){

        		halfLeaveSt.hide();
        		otherLeaveSt.show();
        		sickLeaveSt.hide();

        		var code = leaveTypeSt.val();
        		var balancetext = "";
        		
        		if(forwardAnnualAvailable > 0){

        			balancetext = "<span class='forwardColor'>" + forwardAnnualAvailable + "</span> + " + balance[code]+" Day(s)";	
        		}else{
            		
        			balancetext = balance[code]+" Day(s)";
        		}
        		
        		$('#balance').html(balancetext);	
        	
            }else if($(this).val() == "SL" || $(this).val() == "SLM"){
                
        		halfLeaveSt.hide();
        		otherLeaveSt.show();
        		sickLeaveSt.show();
    
    			var code = $(this).val();
    			$('#balance').text(balance[code]+" Day(s)");
    			 
    		}else{
        		halfLeaveSt.hide();
        		otherLeaveSt.show();
        		sickLeaveSt.hide();
        		
        		var code = leaveTypeSt.val();
        		$('#balance').text(balance[code]+" Day(s)"); 
    		}	
    	});
        //date dation
    	$('#leaveDate, #leaveStart, #leaveEnd').datepicker({
    	   format: 'yyyy-mm-dd'
    	});
    	$('#leaveDate').on('changeDate', function(ev){
    	    $(this).datepicker('hide');  	    
    	});
    	
    	$('#leaveStart').on('changeDate', function(ev){
    		$(this).datepicker('hide');				
    		$('#leaveEnd').datepicker('setStartDate',$(this).val());
    		
    		displayDate ();
    	});	
    	$('#leaveEnd').on('changeDate', function(ev){
    	    $(this).datepicker('hide');
    	    $('#leaveStart').datepicker('setEndDate',$(this).val());
    
    	    displayDate ();	    
    	});
    	
    	function displayDate (){
    		var start = $('#leaveStart').datepicker('getDate');
    		var end   = $('#leaveEnd').datepicker('getDate');
    		 
    		if($('#leaveStart').val()==""){			
    			start = $('#leaveStart').datepicker('getDate');          
            }          
            var days   = ((end - start)/(1000*60*60*24)) + 1;
            //periodDay = days;

        	var leaveStart = $('#leaveStart').val();
        	var leaveEnd = $('#leaveEnd').val();

        	if(leaveStart !="" && leaveEnd != "" ){
            
                var leaveCode = $("#leaveType").val();            
                if($.inArray(leaveCode, sandwichLeave) != -1){
    
                	$.ajax({
            			type:"POST",
            			url:"<?php echo base_url()?>leave/get_holiday/",
            			data: {start: leaveStart, end:leaveEnd},
            			dataType:"json",
            			success:function(response) {
            				$(".loader").remove();
            				
            				if(response.status) {
            				    var holiday = (response.holiday == null)? 0: response.holiday;                				
            					var pDays = days - holiday;
            					//console.log(days);
            	                $('#totalDay').text(pDays);
            	            	$('#leavePeriod').val(pDays);

            				}else{
            				    alert(response.msg);
            				    return;
            				}	
            			}
            		});
                    	              
                } else{
                	$('#totalDay').text(days);
                	$('#leavePeriod').val(days);
                }
        	}
    	}	
    	//end date dation

    	$("#uploadForm").on('submit',(function(e) {
        	//console.log("Invocked");
    		e.preventDefault();
    		//$(".upload-file-form").ajaxForm(options);

    		var url = "";
    		if(leaveId == ""){
    			url = "<?php echo base_url()?>leave/add_request";
    		}else{
    			url = "<?php echo base_url()?>leave/update_request/"+leaveId;
    		}
    		   		
    		$("body").append("<div class='loader'>Please wait&hellip;</div>");
    		
    		var leaveType = $("#leaveType").val();
    		var lDate = $("#leaveDate").val();
    		var timeSlot = $("#timeSlot").val();
    		var start = $("#leaveStart").val();
    		var end = $("#leaveEnd").val();
    		var totalDay = $("#leavePeriod").val();
    		var reason = $("#reason").val();
   		 
    		if( leaveType=="" || (leaveType != "HL" && (start =="" || end == "" || totalDay == 0)) ){

    			if(start =="" || end == ""){
    				alert('Please Select two date.');
    				
        		}else if (totalDay == 0){
        			alert("You Can't send leave request when total days of leave is zero.");
            	}
    	    	
				$(".loader").remove();
				return;
				  
    	    }else if(leaveType=="AL" && totalDay > balance[leaveType]){
    	    	alert("You Can't send leave request when requested leave days exceed available leave days.");
    	    	$(".loader").remove();
    	    	return false;
        	}else if(leaveType=="HL" && (lDate =="" || timeSlot == "")){
        	    
    	    	alert('Please Select both leave date and time slot.');
    	    	
				$(".loader").remove();
				return;  
    	    }

    	    if(reason == ""){
        	    
    	    	alert('Please fill in the special reason field.');
    	    	
				$(".loader").remove();
				return;  
    	    }    
    	         
    		$('#btn_send_leave').attr('disabled','disabled');
    		
    		$.ajax({
    			type:"POST",
    			url:url,
    			data:  new FormData(this),
    			contentType: false,
    			cache: false,
    			processData:false,
    			dataType:"json",
    			success:function(response) {
        			
    				$(".loader").remove();
    				if(!response.status) {
        				
        				if(response.fFlag){
        				    alert('File uploaded successfully.');
        				    return;
            			}
    				    alert(response.msg);
    				    $('#btn_send_leave').attr('disabled','enabled');
    				    return;
    				}else{
        				
    					alert(response.msg);
    					location.href = "<?php echo base_url();?>leave/show/";
    				}	
    			}
    		});
    	}));
	}
    
    //genreric confirm modal work
    var inputObj;
    var approveRId;
    var refuseRId;
    
    if(manager_i != 'disabled' ){
        
    	//approveRId = "#manApproveRadio";
    	refuseRId = "#manRefuseRadio";
    	
    }else if(admin_i != 'disabled') {
        
    	//approveRId = "#adminVerifyRadio";
    	refuseRId = "#radio4";
    	
    }else if(manager_c != 'disabled' ) {
        
    	approveRId = "#radio5";
    	refuseRId = "#radio6";
    	
    }else if(admin_c != 'disabled' ) {
        
    	approveRId = "#radio7";
    	refuseRId = "#radio8";
    }

    // #adminVerifyRadio, #radio5, #radio7'
    $(approveRId).off('click').on('click', function (e) {     
    	inputObj = $(this); 	    	

    	if(inputObj.attr('id') == 'manApproveRadio'){
        	if(!($("#comment1").is(':checked') || $("#comment2").is(':checked') || $("#comment3").is(':checked') )){
            	alert('Please check atleast one option.');
            	$('#manApproveRadio').prop('checked', false);
            	return;
        	} 
    	}	
    	message = $(this).attr("data-message");
    	url = $(this).attr("data-url");
    	reload = $(this).attr("data-reload");
    	
    	$('#confirmApproveModal').modal('show');
    });            
    $('#confirmApproveModal').on('show.bs.modal', function (e) {
        $(this).find('.modal-body p').text(message);
    });
    $('#confirmApproveModal').on('hidden.bs.modal', function (e) {
    	inputObj.prop('checked', false);          
    });
          
    //Form confirm (yes/ok) handler, submits form
    $('#confirmApproveModal').find('.modal-footer #confirmApprove').on('click', function(){
      	$('#confirmApproveModal').modal('hide');
      	$("body").append("<div class='loader'>Please wait&hellip;</div>");

      	var data = {};
      	var elemId = inputObj.attr('id');
  	
        $.ajax({
          type:"POST",
          url:url,
          data:data,
          dataType:"json",
      	success:function(response) {
      		$(".loader").remove();
      		if(!response.status) {
      		    alert(response.msg);
      		    return;
      		}else{
      			alert(response.msg);
      			location.href = "<?php echo base_url()."leave/pending"; ?>";
      		}	
      	}
        });
    });

    //', #manApproveRadio
    $('#manApproveRadio').off('click').on('click', function (e) {     
    	inputObj = $(this); 	    	

    	if(!($("#comment1").is(':checked') || $("#comment2").is(':checked') || $("#comment3").is(':checked') )){
        	alert('Please check atleast one option.');
     	    $(this).prop('checked', false);
        	return;
    	} 

    	url = $(this).attr("data-url");
    	reload = $(this).attr("data-reload");
    	
    	$('#manApproveModal').modal('show');
    });            

    $('#manApproveModal').on('hidden.bs.modal', function (e) {
    	$('#manApproveRadio').prop('checked', false);          
    });
          
    //Form confirm (yes/ok) handler, submits form
    $('#manApproveModal').find('.modal-footer #manApproveBtn').on('click', function(){
        
      	$('#manApproveModal').modal('hide');
      	$("body").append("<div class='loader'>Please wait&hellip;</div>");

      	var data = {};

		var c1 = "", c2 = "", c3 = "";    		
		if($("#comment1").is(':checked'))  c1 = 'Y';
		else c1 = 'N';
		if($("#comment2").is(':checked'))  c2 = 'Y';
		else c2 = 'N';
		if($("#comment3").is(':checked'))  c3 = 'Y';
		else c3 = 'N';

		var approveText = $('#approveText').val(); 
  		data = {comment1:c1, comment2:c2, comment3:c3, approveText: approveText};
  	
        $.ajax({
            type:"POST",
            url:url,
            data:data,
            dataType:"json",
            success:function(response) {
                
          		$(".loader").remove();
          		if(!response.status) {
          		    alert(response.msg);
          		    return;
          		}else{
          			alert(response.msg);
          			location.href = "<?php echo base_url()."leave/pending"; ?>";
          		}	
          	}
        });
    });

    /*Admin Verify portion */
    $('#adminVerifyRadio').off('click').on('click', function (e) {     
    	inputObj = $(this); 	    	
    	
    	url = $(this).attr("data-url");
    	reload = $(this).attr("data-reload");
    	
    	$('#adminVerifyModal').modal('show');
    });            

    $('#adminVerifyModal').on('hidden.bs.modal', function (e) {
    	$('#adminVerifyRadio').prop('checked', false);          
    });
          
    //Form confirm (yes/ok) handler, submits form
    $('#adminVerifyModal').find('.modal-footer #adminVerifyBtn').on('click', function(){
        
      	$('#adminVerifyModal').modal('hide');
      	$("body").append("<div class='loader'>Please wait&hellip;</div>");

      	var data = {};
		var verifyText = $('#verifyText').val(); 
  		data = {verifyText: verifyText};
  	
        $.ajax({
            type:"POST",
            url:url,
            data:data,
            dataType:"json",
            success:function(response) {
                
          		$(".loader").remove();
          		if(!response.status) {
          		    alert(response.msg);
          		    return;
          		}else{
          			alert(response.msg);
          			location.href = "<?php echo base_url()."leave/pending"; ?>";
          		}	
          	}
        });
    });
    /*End of Admin Verify portion */
    
    //Refuse Modal
    //'#manRefuseRadio, #radio4, #radio6, #radio8 '
    $(refuseRId).off('click').on('click', function (e) { 
    	inputObj = $(this);
    	message = $(this).attr("data-message");    	
    	url = $(this).attr("data-url");
    	reload = $(this).attr("data-reload");

    	$('#refuseModal').modal('show');
    });     

    $('#refuseModal').on('hidden.bs.modal', function () {
    	inputObj.prop('checked', false);        
    });

    $('#refuseModal').on('show.bs.modal', function (e) {
        $(this).find('.modal-body label').text(message);
    });

    $('#refuseModal').find('.modal-footer #refuseOk').off('click').on('click', function () {
		var excuse = $('#excuseText').val();
				
		if(excuse.length <= 3){
			alert('please give him convincing excuse.');
			return;
		}
		$('#refuseModal').modal('hide');
		$("body").append("<div class='loader'>Please wait&hellip;</div>");
		$.ajax({
			type:"POST",
			url:url,
			data: {excuse:excuse},
			dataType:"json",
			success:function(response) {
				$(".loader").remove();
				if(!response.status) {
				    alert(response.msg);
				    return;
				}else{
					alert(response.msg);
					location.href = "<?php echo base_url()."leave/pending"; ?>";
				}	
			}
		});
	});

	if(staff_c != "disabled"){
		
    	$('#cancellation').off('click').on('click', function (e) {
        	
        	$('#cancellationModal').modal('show');
        });    
        $('#cancellationCancel').on('click', function(){
        	$('#cancellation').prop('checked', false);          	
        })
    
    	$('#cancellationConfirm').off('click').on('click', function () {
    		var excuseReason = $('#excuseReason').val();
    				
    		if(excuseReason.length <= 3){
    			alert('please give him convincing excuse.');
    			return;
    		}
    		$('#cancellationModal').modal('hide');
    		$("body").append("<div class='loader'>Please wait&hellip;</div>");
    		$.ajax({
    			type:"POST",
    			url:"<?php echo base_url()?>leave/cancel_leave/"+leaveId,
    			data: {excuseReason: excuseReason},
    			dataType:"json",
    			success:function(response) {
    				$(".loader").remove();
    				if(!response.status) {
    				    alert(response.msg);
    				    return;
    				}else{
    					alert(response.msg);
    					location.href = reload;
    				}	
    			}
    		});
    	});		
	}

	var deleteButton;
	$('.removeLeaveFile').on('click', function(){
		deleteButton = $(this);
		$('#deleteModal').modal('show');			
	});

	$('#deleteConfirm').unbind("click").bind("click",function(){
		var file_id = deleteButton.attr('data-id');	
		var file_name = deleteButton.attr('data-name');

		
		$.ajax({
	  	    type:"POST",
	  	    url:"<?php echo base_url()?>leave/del_leave_file/"+file_id,
	  	    data:{file_name:file_name},
	  	    dataType:"json",
	  	    success:function(response) {
	  	    	$('#deleteModal').modal('hide');
	    	    if(response.status) {
	    	    	deleteButton.parents('tr').remove();
	    	    	var serial = $('table').find('.serial');
	    	    	for(i=0; i<serial.length; i++) {
	    	    	    $(serial[i]).text(i+1);
	    	    	}
	    	    } else {
	    	        alert(response.msg);
					return;
				}
	    	    
	    	}      	    
	    });
   	});

	var headerTitle = '';
    $('#sickId').off('click').on('click', function (e) {
    	headerTitle = "Carry Forwarded Sick Leave Information";  
    	$('#dialogModal').modal('show');    	
    }); 

    $("#prescriptionFile").change( function(event) {
    	var tmppath = URL.createObjectURL(event.target.files[0]);

    	var files = "<input type='text' class='files' name='files[]' style='hidden' value='"+tmppath+"'>";

    	$("#fileAddBtn").before(files);
 	   //var path = URL.createObjectURL(event.target.files[0])
    	//console.log(URL.createObjectURL(event.target.files[0]));
    	
        //$("img").fadeIn("fast").attr('src',URL.createObjectURL(event.target.files[0]));

        //$("#disp_tmp_path").html("Temporary Path(Copy it and try pasting it in browser address bar) --> <strong>["+tmppath+"]</strong>");
    });


});

function FileSelect() {
    $("#prescriptionFile").click();
}


</script>