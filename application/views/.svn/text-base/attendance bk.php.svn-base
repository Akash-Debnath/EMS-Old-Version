<link href="<?php echo base_url();?>assets/css/attendance.css"
	type="text/css" rel="stylesheet" />

<link href="<?php echo base_url();?>assets/css/main.css" type="text/css"
	rel="stylesheet" />
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />

<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />

<?php
    //echo $controller->totalAbsent;
     
?>	

<form class="form-horizontal" id='searchForm'
	action="<?php echo base_url()?>attendance/show_new" method='post'>
	<div class='row'>
    <?php if(count($departments) > 0) { ?>
        <div class='col-md-3 col-sm-6'>
			<div class="form-group">
				<label class="col-sm-4 control-label" for="select_dept">Department</label>
				<div class="col-sm-8">
					<select name="select_dept" class="selectpicker" id='select_dept'
						data-width="100%" data-live-search="true">
						<option value=''>---Select Department---</option>
        			<?php foreach ($departments as $dept_code=>$dept_name) {
        			    if($sel_dept == $dept_code)
        			        echo "<option value='".$dept_code."' selected='selected'>".$dept_name."</option>";
        				else
            			    echo "<option value='".$dept_code."'>".$dept_name."</option>";
        		    } ?> 
        		    </select>
				</div>
			</div>

		</div>
    <?php }?>
    <?php if(count($staff_array) > 0) { ?>
        <div class='col-md-3 col-sm-6'>
			<div class="form-group">
				<label class="col-sm-3 control-label" for="select_staff">Staff</label>
				<div class="col-sm-9">
					<select name="select_staff" class="selectpicker" id='select_staff'
						data-width="100%" data-live-search="true" required>
						<option value=''>---Select Staff---</option>
        			<?php foreach ($staff_array['all'] as $obj) {
        			    if($sel_emp_id == $obj->emp_id )
        			        echo "<option value='".$obj->emp_id."' selected='selected' >".$obj->emp_id." - ".$obj->name."</option>";
        				else 
        			        echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ".$obj->name."</option>";
        		    } ?>
        		    </select>
				</div>
			</div>
		</div>
    <?php }?>    
        <div class='col-md-6 col-sm-12'>
			<div class='row'>
				<div class='col-sm-4'>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="dateFrom">From</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="dateFrom"
								name="dateFrom" placeholder="yyyy-mm-dd"
								value="<?php echo $sel_sdate?>" required>
						</div>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="dateTo">To</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="dateTo" name="dateTo"
								placeholder="yyyy-mm-dd" value="<?php echo $sel_edate?>"
								required>
						</div>
					</div>
				</div>
				<div class='col-sm-4'>
					<input type='submit' class='btn btn-primary' name='search'
						value='Search'>
				</div>
			</div>

		</div>
	</div>
</form>
<div class='table-responsive'>
	<table class="table  table-bordered text-center">

		<thead>
			<tr>
				<th rowspan='2'>Date</th>
				<th rowspan='2'>Day</th>
				<th colspan='3'>Official Time Table</th>
				<th colspan='3'>Log</th>
				<th rowspan='2'>Incident</th>
				<th rowspan='2'>Attendance</th>
			</tr>
			<tr>
				<th>Start</th>
				<th>End</th>
				<th>Duration</th>
				<th>In</th>
				<th>Out</th>
				<th>Duration</th>
			</tr>

		</thead>

		<tbody>
		
    	<?php
    	if(isset($records)){
            foreach ($records as $obj) {
                echo "<tr>
        				<td>" . $obj->recordDate . "</td>
        				<td>" . $obj->recordDay . "</td>
        				<td>" . $obj->officeStart . "</td>
                		<td>" . $obj->officeEnd . "</td>
                    	<td>" . $obj->officeDuration . "</td>"  .
                                    $obj->logIn . 
                                    $obj->logOut . 
                                    $obj->logD . 
                                    $obj->incident . 
                                    $obj->message . 
                        "<td>" . $obj->attendance . "</td>
                    </tr>";
            }
    	}    
        ?>
    </tbody>

	</table>
</div>


<div class='row'>
	<div class='col-lg-3 col-md-4 col-sm-6'>
		<label>Total working day:</label> <?php echo $controller->totalWorkingDay;?>
	</div>
	<div class='col-lg-3 col-md-4 col-sm-6'>
		<label>Total present day:</label> <?php echo $controller->totalPresentDay;?>
	</div>
	<div class='col-lg-3 col-md-4 col-sm-6'>
		<label>Total absent day:</label> <?php echo $controller->totalAbsent;?>
	</div>
	<div class='col-lg-3 col-md-4 col-sm-6'>
		<label>Present During Holiday:</label> <span class="txt-cyan-bold"><?php echo ($controller->totalPresentDay - ($controller->totalWorkingDay - $controller->totalAbsent));?></span>
	</div>
	
	<div class='col-lg-3 col-md-4 col-sm-6'>
		<label>Total Late Login:</label> <?php if($controller->totalLateLogin == 0) echo $controller->totalLateLogin; else echo "<span class='txt-red-bold'>".$controller->totalLateLogin."</span>"?>
	</div>
	<div class='col-lg-3 col-md-4 col-sm-6'>
		<label>Total Early Logout:</label>  <?php if($controller->totalEarlyLogout == 0) echo $controller->totalEarlyLogout; else echo "<span class='txt-red-bold'>".$controller->totalEarlyLogout."</span>"?>
	</div>
	<div class='col-lg-3 col-md-4 col-sm-6'>
		<label>Incomplete office duration:</label> <?php if($controller->totalIncompleteOfficeDuration == 0) echo $controller->totalIncompleteOfficeDuration." day(s)"; else echo "<span class='txt-red-bold'>".$controller->totalIncompleteOfficeDuration." day(s)</span>"?>
	</div>
	<div class='col-lg-3 col-md-4 col-sm-6'>
		<label>Average Working Time:</label> 
		<?php 
		$avgSec = ($controller->totalWorkingDay == 0) ?  0: $controller->totalWorkingSeconds/$controller->totalWorkingDay;
		echo $controller->secondsToHours( $avgSec );?>
	</div>
	<div class='col-lg-6 col-md-6 col-sm-6'>
		<label>Extra Working Hour during Working Day:</label> <?php echo $controller->secondsToHours($controller->extraWSecDuringWDay);?>
	</div>
	<div class='col-lg-6 col-md-6 col-sm-6'>
		<label>Extra Working Hour without Working Day:</label> <span class="txt-cyan-bold"><?php echo $controller->secondsToHours($controller->extraWSecWithoutWDay);?></span>
	</div>


</div>




<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js"
	type="text/javascript"></script>


<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>

<script>

var staffs = <?php echo json_encode($staff_array); ?>;

$(document).ready(function(){
	
	var now = new Date();
	var day = ("0" + now.getDate()).slice(-2);
	var month = ("0" + (now.getMonth() + 1)).slice(-2);
	var today = now.getFullYear() + "-" + (month) + "-" + (day);
	var firstDay = now.getFullYear() + "-" + (month) + "-" + "01";

    //Date Correction    
	$('#dateFrom, #dateTo').datepicker({
	 	format: 'yyyy-mm-dd'
	});

	$('#dateFrom').on('changeDate', function(ev){
		$(this).datepicker('hide');				
		$('#dateTo').datepicker('setStartDate',$(this).val());
		
		//displayDate ();
	});	
	$('#dateTo').on('changeDate', function(ev){
	    $(this).datepicker('hide');
	    $('#dateFrom').datepicker('setEndDate',$(this).val());
	    
	    //displayDate ();	    
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

 	$('#searchForm').validate({ // initialize the plugin
 		ignore: ":not(select:hidden, input:visible, textarea:visible)",
		rules: {
			select_staff: {
				required: true,
			},
			dateFrom: {
			    required: true,
			    date: true
			},
			dateTo: {
			    required: true,
			    date: true
			}
		},
        submitHandler : function(event) {
            return true;
        }
	}); 	
    
});

</script>
