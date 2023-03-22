<style>
<!--

   @media only screen and (min-width: 801px) {
	
        .custom-table > tbody > tr > td, 
        .custom-table > tbody > tr > th,
       	.custom-table > thead > tr > td, 
        .custom-table > thead > tr > th  {
       	
            border: 1px solid #ddd;
        	padding: 4px 3px;
        }
        
        .custom-table > thead > tr > td, 
        .custom-table > thead > tr > th {
            border-bottom-width: 2px;
        }
    }
    
    .table-heading{
	   padding-left: 5px !important;
    }

-->
</style>

<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />

<form class="form-horizontal" id = 'searchForm' action="<?php echo base_url()?>leave/glance" method='post'>
    <div class = 'row'>
        <div class='col-md-2 col-sm-6'>
        	<div class="form-group">
                <label class="col-sm-5 control-label" for="select_year">Year</label>
                <div class="col-sm-7">
					<select name="select_year" class="selectpicker form-control" id='select_year' 
						data-live-search="false">
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
        <div class='col-md-3 col-sm-6'>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="select_dept">Department</label>
                <div class="col-sm-8">
                    <select name="select_dept[]" class="selectpicker" id='select_dept' data-width="100%" data-live-search="true" multiple>
        			<?php foreach ($departments as $dept_code=>$dept_name) {
        			    if(in_array($dept_code, $search_dept_code))
        				    echo "<option value='".$dept_code."' selected='selected'>".$dept_name."</option>";
        			    else 
        			        echo "<option value='".$dept_code."'>".$dept_name."</option>";
        		    } ?>
        		    </select>
                </div>
            </div>
        </div>
        <div class = 'col-md-3 col-sm-6'>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="leave_type">Leave Type</label>
                <div class="col-sm-8">
					<select name="leave_type[]" class="selectpicker form-control" id='leave_type' data-live-search="false" multiple>
            			<?php foreach ($leaves_array as $key=>$val) {
            			    if(in_array($key, $search_leave_type))
            			        echo "<option value='".$key."' selected='selected'>".$val."</option>";
            			    else 
            				    echo "<option value='".$key."' >".$val."</option>";
            			} ?>
                    </select>
                </div>
            </div>
        </div>
        <div class = 'col-md-1 col-sm-6'>
            <div class="form-group">
                <div class='col-xs-12'>
                    <input type='submit' class='btn btn-primary' name='search' value='Search'>
                </div>
            </div>        
        </div>
    </div>
</form>		

<div class="no-more-tables">
	<table class="custom-table full-width">
		<thead class="">
			<tr >
				<th>ID</th>
				<th>Name</th>
			<?php foreach ($leave_type as $key=>$val) {
			        if($key == 'CA') continue;
			        
    			    echo "<th>$val</th>";						
            } ?>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($departments as $dept_code=>$dept_name) {
		    
		    $arrayOfStaffs = isset($staffsLeaveRecords[$dept_code])? $staffsLeaveRecords[$dept_code] : array();        		    
	        if(empty($arrayOfStaffs)) {
	            continue;
	        }
            ?>
            <tr>
				<td class="table-heading" colspan="<?php echo (count($leave_type) - 1 ) + 2;?>"><?php echo $dept_name." ("; echo count($arrayOfStaffs).") "; ?></td>
			</tr>
		<?php foreach ($arrayOfStaffs as $id=>$staffs) { ?>
			<tr>
				<td data-title="ID"><?php echo $id?></td>
				<td data-title="Name"><?php echo $staffs['name']?></td>
			<?php foreach ($leave_type as $key=>$val) {
			        if($key == 'CA') continue;
			        
    			    if(isset($staffs[$key]))
    			        echo "<td data-title='$val'>$staffs[$key]</td>";
    			    else
    			        echo "<td data-title='$val'>0</td>";						
            } ?>
			</tr>
		<?php }
        } ?>
        
		</tbody>
	</table>
</div>

	
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
	
<script type="text/javascript">

$(document).ready(function() {

	

	/*
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

		},
        submitHandler : function(event) {
            return true;
        }
	});*/

	
	
});
</script>

