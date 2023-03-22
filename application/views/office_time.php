<link href="<?php echo base_url();?>assets/css/main.css"
	type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/settings.css"
	type="text/css" rel="stylesheet" />
	
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


<div class="no-more-tables">
	<table class="custom-table full-width">
		<thead class="cf">
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Attendance</th>
				<th>Roster</th>
				<th>Weekend</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($departmentLists as $dept_code=>$dept_name) {
		    
        if(isset($staffs[$dept_code]))  $array = $staffs[$dept_code];
        else continue;?>
            <tr>
				<td class="table-heading" colspan="5"><?php echo $dept_name." ("; echo count($array).") "; ?></td>
			</tr>
		<?php foreach ($array as $obj) {
		    $att = $obj->scheduled_attendance == 'Y' ? 'Scheduled' : 'Non-scheduled';
		    $roster = $obj->roster == 'Y' ? 'Roster' : 'Non-roster';
		     
		    ?>
			<tr>
				<td data-title="ID"><?php echo $obj->emp_id?></td>
				<td data-title="Name"><?php echo $obj->name?></td>
				<td data-title="Attendance"><a class='scheduleClass'
					data-id='<?php echo $obj->emp_id?>'
					data-value='<?php echo $obj->scheduled_attendance?>'><?php echo $att ?></a></td>
				<td data-title="Roster"><a class='rosterClass'
					data-id='<?php echo $obj->emp_id?>'
					data-value='<?php echo $obj->roster?>'><?php echo $roster?></a></td>
				<td data-title="Weekend">
				<?php if($obj->roster != 'Y') {?>
				<?php
				$array = array() ;
				foreach ($weekend[$obj->emp_id] as $key=>$val){
				    if($val == 'Y') $array[] = ucfirst($key);
				}
				echo implode(", ", $array);
				?>
				<span class='pull-right'>
				    <a class='editWeekend' data-id='<?php echo $obj->emp_id?>'><span
						class='glyphicon glyphicon-edit'></span> Edit &nbsp;</a>
				</span>
				<?php } ?>
				</td>
			</tr>
		<?php } 
        } ?>
		</tbody>
	</table>
</div>



<!-- Modal Dialog -->
<div class="modal fade" id="confirmModal" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure about this ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-warning" id="confirm">Ok</button>
			</div>
		</div>
	</div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="editWeekendModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form id='weekendForm' method="post" action='<?php echo base_url()?>settings/update_weekly_leave' class='form'>
				<input type='hidden' id='staffId' name ='staffId' value="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">tick mark the respective weekend</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" id="btn_weekend_save" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

var weekend = <?php echo json_encode($weekend)?>;
var day_array = <?php echo json_encode($day_array)?>;

$(document).ready(function() {
    var empId;
    var rosterValue;
    var scheduleValue;
    var clickElement;
     
	$('.rosterClass').on('click', function(){
		clickElement = $(this);
		empId = clickElement.attr('data-id');
		rosterValue = clickElement.attr('data-value');
		scheduleValue = "";
		var message = 'Change to ';
		if(rosterValue == 'Y')
			message += 'Non-roster?';
		else
			message += 'Roster?';
		
		var confirmModal = $('#confirmModal');		
		confirmModal.modal('show');
		confirmModal.find('.modal-body p').text(message);
	});	
	
	$('.scheduleClass').on('click', function(){
		clickElement = $(this);
		empId = clickElement.attr('data-id');
		scheduleValue = clickElement.attr('data-value');
		rosterValue="";
		
		var message = 'Change to ';
		if(scheduleValue == 'Y')
			message += 'Non-scheduled?';
		else
			message += 'Scheduled?';
		
		var confirmModal = $('#confirmModal');		
		confirmModal.modal('show');
		confirmModal.find('.modal-body p').text(message);
	});	

	
	$('#confirm').unbind("click").bind("click",function(){
		
		$.ajax({
	  	    type:"POST",
	  	    url:"<?php echo base_url()?>settings/changeType/"+empId,
	  	    data:{
		  	    schedule:scheduleValue,
		  	    roster:rosterValue},
	  	    dataType:"json",
	  	    success:function(response) {
	  	    	$('#confirmModal').modal('hide');
	    	    if(response.status) {
	    	    	location.reload(); 
	    	    	clickElement.text(response.text);
	    	    	clickElement.attr('data-value', response.value);
	    	    } else {
	    	        alert(response.msg);
					return;
				}
	    	}      	    
	    });
   	});


	$('.editWeekend').on('click', function(){
	    var staff_id = $(this).attr('data-id');	
	    

	    var temp ="";
	    var obj =weekend[staff_id];
	    
	    for (var key in obj) {
		    if(key == 'emp_id') continue;
	        temp += "<div class='checkbox'>\
	                    <label><input type='checkbox' value='"+key+"' name='chk[]'";
	        if(obj[key] == 'Y') temp += 'checked';
	        temp += ">"+day_array[key]+"\
	                    </label>\
	                </div>";
		}
		
		var weekendModal = $('#editWeekendModal');		
		weekendModal.modal('show');		
		weekendModal.find('#staffId').val(staff_id);
		weekendModal.find('div.form-group').html(temp);
	});
		

});
</script>

