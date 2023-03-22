<?php 
$skin = "skin-blue";
echo link_tag('assets/css/leave.css');
?>

<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />

<style>
<!--

.bootstrap-select > .btn {
	    
    width: 100% !important;
}

.m-t{
	margin-top: 15px;
}

.m-b{
	margin-bottom: 15px;
}

-->
</style>

<div class="row">
	<div class="col-lg-9">
		<div class='box'>
			<div class='box-header no-padding'>
			
			    <form class="form-horizontal" id='searchForm' action="<?php echo base_url()?>leave/show/" method='post'>
                	<div class='row m-t'>
                	
                	    <div class='col-md-3 col-sm-6'>
                    	    <div class="form-group">
                        		<label for="leaveIn" class="col-sm-6 control-label">Leave in:</label>
                        		<div class="col-sm-6">
                        			<select name="leaveIn" class="selectpicker form-control"
                        				id='leaveIn' data-live-search="false">
                            			<?php foreach ($leaveInYears as $key=>$val) {
                            			    if($year == $val)
                            			        echo "<option value='".$val."' selected='selected'>".$val."</option>";
                            			    else 
                            				    echo "<option value='".$val."' >".$val."</option>";
                            			} ?>
                                    </select>
                        		</div>
                        	</div>
                	    </div>
                	
                        <?php if(count($departmentLists) > 0) { ?>
                        <div class='col-md-3 col-sm-6'>
                			<div class="form-group">
                				<label class="col-sm-4 control-label" for="select_dept">Department</label>
                				<div class="col-sm-8">
                					<select name="select_dept" class="selectpicker" id='select_dept'
                						data-width="100%" data-live-search="true">
                						<option value=''>---Select Department---</option>
                        			<?php foreach ($departmentLists as $dept_code=>$dept_name) {
                        			    if($SearchDept == $dept_code)
                        			        echo "<option value='".$dept_code."' selected='selected'>".$dept_name."</option>";
                        				else
                            			    echo "<option value='".$dept_code."'>".$dept_name."</option>";
                        		    } ?> 
                        		    </select>
                				</div>
                			</div>
                
                		</div>
                        <?php 
                        }
                        if(count($staff_array) > 0) { ?>    
                    
                        <div class='col-md-4 col-sm-6'>
                			<div class="form-group">
                				<label class="col-sm-3 control-label" for="select_staff">Staff</label>
                				<div class="col-sm-9">
                					<select name="select_staff" class="selectpicker" id='select_staff'
                						data-width="100%" data-live-search="true" required>
                						<option value=''>---Select Staff---</option>
                        			<?php foreach ($staff_array['all'] as $obj) {
                        			    if($searchId == $obj->emp_id )
                        			        echo "<option value='".$obj->emp_id."' selected='selected' >".$obj->emp_id." - ".$obj->name."</option>";
                        				else 
                        			        echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ".$obj->name."</option>";
                        		    } ?>
                        		    </select>
                				</div>
                			</div>
                		</div>
                        <?php }?> 

                			  
                        <div class='col-md-2 col-sm-6 '>
                			<input type='submit' class='btn btn-primary m-b' name='search'
                					value='Search'>                
                		</div>
                	</div>
                </form>

				
				
			</div>
			<div class='box-body no-padding'>
			    <?php if(empty($leaveLists)){ ?>
                <table class='no-record'>
					<tbody>
						<tr>
							<td><font color="#FF0000"><h1>No Records Found!</h1>
									</h1></font></td>
						</tr>
					</tbody>
				</table>
                <?php } else{ ?>
                <div class='table-responsive'>         
    				<table class="table table-bordered">
    					<thead>
    						<tr>
    							<th rowspan="2" class='hidden-xs'></th>
    							<th rowspan="2" class='text-center'>Leave Type</th>
    							<th rowspan="2" class='text-center'>Day(s)</th>
    							<th rowspan="2" class='text-center'>Leave Date</th>
    							<th colspan="2" class='text-center'>Approval</th>
    							<th rowspan="2" class='text-center'>Action</th>
    						</tr>
    						<tr>
    							<th class='text-center'>Manager</th>
    							<th class='text-center'>Admin</th>
    						</tr>
    					</thead>
    					<tbody>
                        <?php
                        $sl = 0;
                        foreach ($leaveLists as $obj) {
                            
                            echo "<tr align='center' class='clickable-row' data-href='".base_url()."leave/request/".$obj->id."'>";
                            echo "<td class='hidden-xs'>".++$sl."</td>";
                            echo "<td align='left'>&nbsp;".$leave_type[$obj->leave_type]." Leave</td>";

                            
                            echo "<td>".$obj->period."</td>";
                            if($obj->leave_type == 'HL')
                                echo "<td><em>".$obj->leave_start."</em> (".$half_leave_slot[$obj->time_slot].")</td>";
                            else 
                                echo "<td><em>".$obj->leave_start."</em> <b>to</b> <em>".$obj->leave_end."</em></td>";
                            
                            if(empty($obj->m_approved_date))
                                echo "<td><span class='mark'>Approval Pending</span></td>";
                            else
                                echo "<td>Approved</td>";
                            
                            if(empty($obj->admin_approve_date))
                                echo "<td><span class='mark'>Verification Pending</span></td>";
                            else
                                echo "<td>Verified for Record</td>";
                            
                            if($viewFlag){
                                
                                if($controller->myEmpId == $searchId){
                                    
                                    if(empty($obj->m_approved_date)){
                                        echo "<td><a class='btn btn-sm btn-primary' href='".base_url()."leave/request/".$obj->id."'>Edit</a> | <a href='#' onclick='delLeaveFunc(".$obj->id.");' class='btn btn-sm btn-danger deleteLeaveBtn' data-id='".$obj->id."' href='#'>Delete</a></td>";
                                    }else{
                                        echo "<td><a class='btn btn-sm' href='".base_url()."leave/request/".$obj->id."?cancel=1'>Cancel</a></td>";
                                    }
                                    
                                }else{
                                    echo "<td><a class='btn btn-sm btn-success' href='".base_url()."leave/request/".$obj->id."'>View</a></td>";
                                }
                                    
                                
                            }else if(empty($obj->m_approved_date) && empty($obj->admin_approve_date)){
                                
                                echo "<td><a class='btn btn-sm btn-primary' href='".base_url()."leave/request/".$obj->id."'>Edit</a> | <a href='#' onclick='delLeaveFunc(".$obj->id.");' class='btn btn-sm btn-danger deleteLeaveBtn'  data-id='".$obj->id."' >Delete</a></td>";                        
                            }else{
                                
                                echo "<td><a class='btn btn-sm btn-warning' href='".base_url()."leave/request/".$obj->id."?cancel=1'>Cancel</a></td>";
                            }
                            
                            echo "</tr>";
                        } ?>
                        </tbody>
    				</table>
    			</div>	
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="sidebar-module sidebar-module-inset">
			<h5 class="sidebar-header">Leave Request</h5>
			<p>
				I hereby declare that I'm aware and agree to the <em>terms and
					conditions</em> of related leave policies.
			</p>
			<a href="<?php echo base_url()?>leave/request"
				class="btn btn-primary"><b>Send Leave Request</b></a>
		</div>
		
		<div class="sidebar-module">
			<h5 class="sidebar-header">Leave Taken in <?php echo $year?></h5>
			
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
            			
            			if( (empty($applier_gender) && $key =='ML') || ($applier_gender == 'F' && $key =='PL' ) || ($applier_gender != 'F' && $key =='ML' )) continue;
            			
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

<!-- Modal Dialog -->
<div class="modal fade" id="confirmLeaveDelete" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Delete Parmanently</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure to delete this leave ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" data-id="" id="confirmDelBtn">Delete</button>
			</div>
		</div>
	</div>
</div>


<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>


<script type="text/javascript">
<!--

var staffs = <?php echo json_encode($staff_array); ?>;

function delLeaveFunc(leave_id){
	
	$('#confirmDelBtn').attr('data-id', leave_id);
	$('#confirmLeaveDelete').modal('show');
	
	return false;
}


$(function(){

    $("#select_dept").change(function(){
        
        var dept_code = $(this).val();
        $("#select_staff").empty();

        if(dept_code != ""){
        	var selectedStaffs = staffs[dept_code];

        }else{
        	var selectedStaffs = staffs['all'];
        	var option = "<option value=''>---Select Staff---</option>";
            $("#select_staff").append(option);   	    
        }	

        for(i=0; i<selectedStaffs.length; i++) {           
            var emp = selectedStaffs[i];
            if(emp!=null) {
                var option = "<option value='"+emp.emp_id+"'>"+emp.emp_id+" - "+emp.name+"</option>";
                $("#select_staff").append(option);   	    
            }
        }    
       $('#select_staff').selectpicker('refresh');
    });

	var base_url = "<?php echo base_url()?>";


    $(".clickable-row").click(function(evt) {

        
    	if($(evt.target).hasClass('deleteLeaveBtn') ) {
    	    
    		evt.stopImmediatePropagation();
    		    return;
    		}
        
    	window.location.href = $(this).data("href");
    });

    $('#confirmDelBtn').click(function(){

    	var leave_id = $('#confirmDelBtn').data('id');
    	window.location.href = base_url+"leave/del_leave/"+leave_id;

    	return false;        
    });

    $('#confirmLeaveDelete').on('hidden.bs.modal', function (e) {
        
    	$('#confirmDelBtn').attr('data-id', "");
    });
    
});

//-->
</script>
