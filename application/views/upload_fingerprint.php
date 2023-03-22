<link href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />

<link href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" />
	
<div class="row">
	
	<div class="col-md-6">
		<div class="box box-danger">
			<div class="box-header">
				<h3 class="box-title text-danger">Add Attendance Manually</h3>
			</div>
			<form class="form-horizontal" method='post' action="<?php echo base_url()?>attendance/upload_manual_fingerprint">
				<div class="box-body">
				    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1">
				    
        				    <div class="form-group">
        				        <label class="col-sm-3 control-label">Cause</label>
        				        <div class="col-sm-8">
            						<div class="radio">
            							<label for="radio1"><input type="radio" id="radio1" value="F" name="causeRadioBtn" required="required"> Forgot to punch </label>
            						</div>
            						<div class="radio">
            							<label for="radio2"><input type="radio" id="radio2" value="S" name="causeRadioBtn" required="required"> Loss of finger's outer skin by peeling </label>
            						</div>
            					</div>	
        					</div>
        				            				    
        					
                			<div class="form-group">
                				<label class="col-sm-3 control-label" for="select_dept">Department</label>
                				<div class="col-sm-7">
                					<select name="select_dept" class="selectpicker" id='select_dept'
                						data-width="100%" data-live-search="true">
                						<option value=''>---Select Department---</option>
                        			<?php foreach ($departmentLists as $dept_code=>$dept_name) {
                        			    
                        			    if($selDept && ($selDept == $dept_code))
                        			        echo "<option value='".$dept_code."' selected='selected'>".$dept_name."</option>";
                        				else
                            			    echo "<option value='".$dept_code."'>".$dept_name."</option>";
                        		    } ?> 
                        		    </select>
                				</div>
                			</div>

                    		<div class="form-group">
                				<label class="col-sm-3 control-label" for="select_staff">Staff</label>
                				<div class="col-sm-7">
                					<select name="select_staff" class="selectpicker" id='select_staff'
                						data-width="100%" data-live-search="true" required="required" >
                						<option value=''>---Select Staff---</option>
                        			<?php

                        			$staffAry = $selDept ? ( isset($staff_array[$selDept])? $staff_array[$selDept] :  $staff_array['all'] ) : $staff_array['all'];
                        			foreach ($staffAry as $obj) {
                        			    
                        			    if( $selStaff && ($selStaff == $obj->emp_id) )
                        			        echo "<option value='".$obj->emp_id."' selected='selected' >".$obj->emp_id." - ".$obj->name."</option>";
                        				else 
                        			        echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ".$obj->name."</option>";
                        		    } ?>
                        		    </select>
                				</div>
                			</div>
                			
                			<div class="form-group">
                				<label class="col-sm-3 control-label">Date</label>
                				<div class="col-sm-6">
                    				<div class='input-group date dateInput'>
                                        <input type='text' class='form-control dateInput' value='<?php echo date("Y-m-d");?>' placeholder='yyyy-mm-dd' name='dateIn' required="required">
                                        <div class='input-group-addon'><span class='glyphicon glyphicon-calendar'></span></div>
                                    </div>
                				</div>
                			</div>
                			
                			<div class="form-group">
                				<label class="col-sm-3 control-label" for="logInInput">Login Time</label>
                				<div class="col-sm-6">
                                    <div class='input-group date timeInput' id='logInInput'>
                                        <input id="logIn" class="form-control timeInput" type="text" placeholder="hh:mm:ss" name="logIn">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                				</div>
                			</div>
                			
                			<div class="form-group">
                				<label class="col-sm-3 control-label" for="logOutInput">Logout Time</label>
                				<div class="col-sm-6">
                				    <div class='input-group date timeInput' id='logOutInput'>
                                        <input id="logOut" class="form-control timeInput" type="text" placeholder="hh:mm:ss" name="logOut">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                    
                				</div>
                			</div>
                			
        			    </div>
        			</div>
					
				</div>
				<div class="box-footer text-center">
					<button  class="btn btn-primary" type="submit"> Add</button>
				</div>
			</form>
		</div>
	</div>
	
	
	<div class="col-md-6">
		<div class="box box-success">
			<div class="box-header">
				<h3 class="box-title text-success">Upload Attendance File</h3>
			</div>
			<form enctype="multipart/form-data" method='post' action="<?php echo base_url()?>attendance/upload_fingerprint">
				<div class="box-body">
				    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1">
                        
        					<div class="form-group">
        						<label for="exampleInputFile">Select Text File</label> <input
        							type="file" id="textFile" name='textFile' accept="text/plain" required>
        						<p class="help-block">only text(.txt) file can be uploaded.</p>
        					</div>
        				</div>
        			</div>		
				</div>
				<div class="box-footer text-center">
					<button class="btn btn-primary" type="submit"> Upload</button>
				</div>
			</form>
		</div>
	</div>
	<div class="col-md-6">
		<div class="box box-success">
			<div class="box-header">
				<h3 class="box-title text-success">Upload Training Attendance File</h3>
			</div>
			<form enctype="multipart/form-data" method='post' action="<?php echo base_url()?>attendance/upload_training_attendance">
				<div class="box-body">
				    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1">
                        
        					<div class="form-group">
        						<label for="exampleInputFile">Select Text File</label> <input
        							type="file" id="textFile" name='textFile' accept="text/plain" required>
        						<p class="help-block">only text(.txt) file can be uploaded.</p>
        					</div>
        				</div>
        			</div>		
				</div>
				<div class="box-footer text-center">
					<button class="btn btn-primary" type="submit"> Upload</button>
				</div>
			</form>
		</div>
	</div>
		
</div>

<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />
	
<script	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js" type="text/javascript"></script>
<script	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
	
	
<script type="text/javascript">

var staffs = <?php echo json_encode($staff_array); ?>;

$(document).ready(function(){

    /*date*/
    $('.dateInput').datetimepicker({
        viewMode: 'days',
        format: 'YYYY-MM-DD'
    });

    $('.timeInput').datetimepicker({
        format: 'LT'
    });


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


	$("form").on('submit',(function(e) {
 		
		$("body").append("<div class='loader'>Please wait&hellip;</div>");
		//$('#submitButton').attr('disabled','disabled');

		$( ":button" ).attr('disabled','disabled');
	}));	
});

</script>	
