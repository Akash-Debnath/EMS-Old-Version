<?php include 'header.php'; ?>
<style>
.custom-group {
	display: inline-block !important;
	margin-bottom: 15px !important;
}
</style>

<div class='row'>
    <div class='col-xs-12'>
        <form class="form-inline">
        	<div class="form-group custom-group">
        		<label for="select_dept" >Department</label>
        	    <select name="dept_code" class="selectpicker"  id='select_dept'
        				data-live-search="true" style='width: 200px;'>
        				
        			<?php foreach ($designations as $dept_code=>$obj) {
        				if(isset($obj->dept_name)) {
        					if($dept_code_of_designation==$dept_code)
        						echo "<option value='".$dept_code."' selected='selected'>".$obj->dept_name."</option>";
        					else
        						echo "<option value='".$dept_code."'>".$obj->dept_name."</option>";
        				}	
        		    } ?>
        		</select>
        	</div>
        	<div class="form-group custom-group pull-right">
            	<button type="button" class="btn btn-primary btn-sm "
    			id="btn_add_designation" data-toggle="modal"
    			title="Adding new designation" data-target="#addDesModal">
    			<span class="glyphicon glyphicon-plus"></span> Add New Designation
    		    </button>
    		</div>    
        </form>
        
    </div>
</div>

<!-- Designation Table -->
<div class="table-responsive">
	<table class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th class="hidden-xs" style="width: 10px;"></th>
				<th class="">Designation</th>
				<th class="">Action</th>
			</tr>
		</thead>
		<tbody>
		<?php 
		$i=0;
		
		$des_array = $designations[$dept_code_of_designation]->designation_array;
		//print_r($des);
		foreach ($des_array as $obj) { ?>
			<tr class="row<?php echo $obj->id; ?>">
				<td class="hidden-xs serial"><?php echo ++$i; ?></td>
				<td class=""><?php echo $obj->designation; ?></td>
				<td class="">
			        <button type="button" class="btn btn-warning btn-xs"
        				id="btn_edit_designation" data-toggle="modal"
        				data-id="<?php echo $obj->id; ?>" 
        				data-name = "<?php echo $obj->designation; ?>"
        				title="Editing a designation" data-target="#editDesModal">
        				<span class="glyphicon glyphicon-edit"></span> Edit
        			</button>
					<button class="btn btn-xs btn-danger" type="button"
						data-toggle="modal" data-target="#confirmDelete"
						data-title="Confirmation" data-id="<?php echo $obj->id; ?>"
						data-name = "<?php echo $obj->designation; ?>"
						data-model="designation"
						data-message="Are you sure you want to delete this designation ?">
						<i class="glyphicon glyphicon-trash"></i> Delete
					</button>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div> <!-- end table -->
<?php include 'footer.php'; ?>
<!-- Delete Confirm Modal -->
<?php include 'delete_confirm.php'; ?>

<!-- Add New Designation Modal -->
<div class="modal fade" id="addDesModal" tabindex="-1" role="dialog"
	aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="exampleModalLabel">Add New Designation</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="dept_code" class="control-label">Department:</label>
						<select name="dept_code" class="selectpicker" id='select_dept_modal'
							data-live-search="true" style='width: 200px;'>
            			<?php
                        foreach ($designations as $dept_code => $obj) {
                            if (isset($obj->dept_name)) {
                                if ($dept_code_of_designation == $dept_code)
                                    echo "<option value='" . $dept_code . "' selected='selected'>" . $obj->dept_name . "</option>";
                                else
                                    echo "<option value='" . $dept_code . "'>" . $obj->dept_name . "</option>";
                            }
                        }
                        ?>
            		    </select>
					</div>
					<div class="form-group">
						<label for="designation" class="control-label">Designation:</label>
						<input type="text" class="form-control" id="des_name">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="btn_des_save" class="btn btn-primary"
					data-loading-text="Loading...">Save</button>
			</div>
		</div>
	</div>
</div> <!-- end modal-->

<!-- Edit Designation Modal -->
<div class="modal fade" id="editDesModal" tabindex="-1" role="dialog"
	aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="exampleModalLabel">Edit Designation</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="dept_code" class="control-label">Department:</label>
						<select name="dept_code" class="selectpicker" id='select_dept_editModal'
							data-live-search="true" style='width: 200px;'>
            			<?php
                            foreach ($designations as $dept_code => $obj) {
                                if (isset($obj->dept_name)) {
                                    if ($dept_code_of_designation == $dept_code)
                                        echo "<option value='" . $dept_code . "' selected='selected'>" . $obj->dept_name . "</option>";
                                    else
                                        echo "<option value='" . $dept_code . "'>" . $obj->dept_name . "</option>";
                                }
                            }
                        ?>
            		    </select>
					</div>
					<div class="form-group">
						<label for="designation" class="control-label">Designation:</label>
						<input type="text" class="form-control" id="des_name_edit">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="btn_des_edit_save" class="btn btn-primary"
					data-loading-text="Loading...">Save</button>
			</div>
		</div>
	</div>
</div> <!-- end modal-->

<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />
	
<script type="text/javascript">
$(function(){
	
	$("#select_dept").change(function(){
		var dept_code = $(this).val();
		location.href = "<?php echo base_url();?>settings/designation/"+dept_code;
	});

	$("#btn_add_designation").click(function(){
		//$("#addDesModal").modal();
		$('#btn_save').on('click', function () {
		    var $btn = $(this).button('loading')
		    var designation = $("#des_name").val();
		    
		    $btn.spin();
		    //alert(designation);
		    //$btn.button('reset')
		  })
	});
});

$('#btn_des_save').off('click').on('click', function (){
	var des = $("#des_name").val();
	var select_dept_code = $('#select_dept_modal').val();
	if(des.length==0) return;
	
	/*
    $('#select_dept_modal').change(function(){
    	select_dept_code = $(this).val();
    	alert(select_dept_code);
    });
    */
	
	$.ajax({
		type:"POST",
		url:"<?php echo base_url()?>settings/add_des",
		data: {designation:des, dept_code:select_dept_code},
		dataType:"json",
		success:function(response) {

			$("#des_name").val("");
			var select_code_main = $('#select_dept').val();
		    if(select_code_main == response.dept_code){
		    	var count = $(".box-body table tbody tr").length;
			    count++;
			    var html = "<tr class=\"row"+response.id+"\">\
					<td class=\"hidden-xs\">"+count+"</td>\
					<td class=\"\">"+response.designation+"</td>\
					<td class=\"\">\
	    		        <button type=\"button\" class=\"btn btn-warning btn-xs\"\
	    				id=\"btn_edit_designation\" data-toggle=\"modal\"\
	    				data-id=\""+response.id+"\"\
	    				data-name = \""+response.designation+"\"\
	    				title=\"Editing a designation\"\
	    				data-target=\"#editDesModal\">\
	    				<span class=\"glyphicon glyphicon-edit\"></span> Edit\
	    			    </button>\
						<button class=\"btn btn-xs btn-danger\" type=\"button\"\
							data-toggle=\"modal\" data-target=\"#confirmDelete\"\
							data-title=\"Confirmation\" data-id=\""+response.id+"\"\
							data-model=\"designation\"\
							data-message=\"Are you sure you want to delete this designation ?\">\
							<i class=\"glyphicon glyphicon-trash\"></i> Delete\
						</button>\
					</td>\
				</tr>"
		
			    $(".box-body table tbody").append(html);
		    } else {
		    	location.href = "<?php echo base_url();?>settings/designation/"+response.dept_code;	    
		    }
		    $('.modal').modal('hide');
		}
		});
})
// edit Modal part
var des_id;
var btn_edit;
var old_des_name;
$('#editDesModal').on('shown.bs.modal', function (e) {
	
	btn_edit = $(e.relatedTarget);
	des_id = btn_edit.attr('data-id');
	old_des_name = btn_edit.attr('data-name');
	var editInput = $('#des_name_edit');
	editInput.val(old_des_name);
	// Multiply by 2 to ensure the cursor always ends up at the end;
	// Opera sometimes sees a carriage return as 2 characters.
	var strLength= editInput.val().length * 2;
	editInput.focus();
	editInput[0].setSelectionRange(strLength, strLength);
});
$('#btn_des_edit_save').off('click').on('click', function () {
	var des = $("#des_name_edit").val();
	var select_dept_code = $('#select_dept_editModal').val();
	//alert(select_dept_code);
	if(des.length==0) {
	    alert("Designation can't be empty!");
		return;
	}

	$.ajax({
		type:"POST",
		url:"<?php echo base_url()?>settings/update_des",
		data: {des_id:des_id, dept_code:select_dept_code, designation:des, old_des_name:old_des_name},
		dataType:"json",
		success:function(response) {
// 			$('#dept_name_edit').val("");
// 			if(!response.status) {
// 			    alert(response.msg);
// 			    //$("#dept_name").val("");
// 			    return;
// 			}

			var select_code_main = $('#select_dept').val();
		    if(select_code_main == response.dept_code){
			    
				var td = $(".row"+response.id).find('td');
				$(td[1]).text(response.designation);
				btn_edit.attr('data-name',response.designation);
		    } else {
			    var tr = $(".row"+response.id);
			    $(tr).remove();
    	    	var serial = $('table').find('.serial');
    	    	for(var i=0; i<serial.length; i++) 
        	    {
    	    	    $(serial[i]).text(i+1);
    	    	}
		    }
 			$('.modal').modal('hide');
		}
	});
});

</script>