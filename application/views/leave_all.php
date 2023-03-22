<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />


<form class="form-horizontal" id = 'searchForm' action="<?php echo base_url()?>leave/all" method='post'>
    <div class = 'row'>
        <div class='col-md-3 col-sm-4'>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="selected_dept">Department</label>
                <div class="col-sm-8">
                    <select name="selected_dept[]" class="selectpicker" id='selected_dept' data-width="100%" data-live-search="true" multiple>
        			<?php foreach ($departments as $dept_code=>$dept_name) {
        			    if(in_array($dept_code, $search_dept_code))
        				    echo "<option value='".$dept_code."' selected='selected' >".$dept_name."</option>";
        			    else 
        			        echo "<option value='".$dept_code."'>".$dept_name."</option>";
        		    } ?>
        		    </select>
                </div>
            </div>
        </div>
        
        <div class='col-md-3 col-sm-4'>
        	<div class="form-group">
                <label class="col-sm-3 control-label" for="selected_staff">Staff</label>
                <div class="col-sm-9">
                    <select name="selected_staff[]" class="selectpicker" id='selected_staff' data-width="100%" data-live-search="true" multiple>
        			<?php
        			$staffGenAry = array();
        			
        			if(count($search_dept_code)>0){
        			    foreach ($search_dept_code as $dpt_code){
        			        foreach ($staff_array[$dpt_code] as $obj){
        			            $staffGenAry[] = $obj;
        			        }
        			    }
        			}
        			if(count($staffGenAry) == 0){
        			    $staffGenAry = $staff_array['all'];
        			}

        			foreach ($staffGenAry as $obj) {
        			    if(in_array($obj->emp_id, $search_staffs))
        				    echo "<option value='".$obj->emp_id."' selected='selected' >".$obj->emp_id." - ".$obj->name."</option>";
        			    else
        			        echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ".$obj->name."</option>";
        		    } ?>
        		    </select>
                </div>
            </div>
        </div>
        
        <div class = 'col-md-1 col-sm-4'>
            <div class="form-group">
                <div class='col-xs-12'>
                    <input type='submit' class='btn btn-primary' name='search' value='Search'>
                </div>            
            </div>
        </div>
    </div>
</form>		

<div class="table-responsive">
	<table class="table-bordered table-striped table-condensed full-width">
		<thead class="">
			<tr >
				<th class='text-center' rowspan="2" >Leave Type</th>
				<th class='text-center' colspan="<?php echo count($leaveInYears)?>">Year</th>
			</tr>
			<tr>
			<?php foreach ($leaveInYears as $val) {					                        			                			    
    			    echo "<th class='text-center' >$val</th>";						
            } ?>
			</tr>					
		</thead>
		<tbody class='text-center'>
		<?php foreach ($staffsLeaveRecords as $emp_id=>$staffRecord) { ?>
            <tr>
				<td class="text-left table-heading" colspan="<?php echo count($leaveInYears)+2;?>"><?php echo $staffRecord['name']; ?></td>
			</tr>
    		<?php foreach ($leave_type as $leave_code=>$leave_name) {
    		    if($leave_code == 'CA') continue;
    		    
    		    echo "<tr>";
    		    echo "<td class='text-left'>$leave_name</td>";
    		    if(isset($staffRecord[$leave_code])){
    		        
    		        $leave_row = $staffRecord[$leave_code];
    		        foreach ($leaveInYears as $val) {
    		            if(isset($leave_row[$val]))
    		                echo "<td data-title='$val'>$leave_row[$val]</td>";
    		                else
    		                echo "<td data-title='$val'>0</td>";
    		        }
    		    }
    		    else{
    		        foreach ($leaveInYears as $val) {
    		            echo "<td data-title='$val'>0</td>";
    		        }            		        
    		    }
    		    echo "</tr>";
            }
        } ?>
		</tbody>
	</table>
</div>



<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>

<script type="text/javascript">                
var staffs = <?php echo json_encode($staff_array); ?>;

$(document).ready(function() {


    $("#selected_dept").change(function(){
    	var selectedStaffs;
    	
        var dept_codes = $(this).val();
        $("#selected_staff").empty();
        //console.log(dept_codes);

        if(dept_codes != null){
        	for (var d_p in dept_codes) {
            	
        		selectedStaffs = staffs[dept_codes[d_p]];
        		
        		appendOption(selectedStaffs);
            }    
        } else {
            
        	selectedStaffs = staffs['all'];
        	appendOption(selectedStaffs);
        }
    });
});



function appendOption(selectedStaffs){


	if(selectedStaffs != undefined){
	    for(var i=0; i<selectedStaffs.length; i++) {           
	        var emp = selectedStaffs[i];
	        if(emp!=null) {
	            var option = "<option value='"+emp.emp_id+"'>"+emp.emp_id+" - "+emp.name+"</option>";
	            $("#selected_staff").append(option);   	    
	        }
	    }
		
	}	
    
   $('#selected_staff').selectpicker('refresh'); 
}
</script>

