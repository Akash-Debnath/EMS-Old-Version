<?php include 'header.php'; ?>
<div class="pull-right">
	<!-- Button trigger add modal -->
	<button type="button" class="btn btn-primary btn-sm"
		id="btn_add_department" data-toggle="modal"
		title="Adding new department" data-target="#addDeptModal">
		<span class="glyphicon glyphicon-plus"></span> Add New Department
	</button>
</div>
<div class="clearfix" style="margin-bottom: 10px;"></div>


<table class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th class="hidden-xs" style="width: 10px;"></th>
			<th class="">Department</th>
			<th class="">Action</th>
		</tr>
	</thead>
	<tbody>
    <?php
    $i = 0;
    $dept = $depts;
    // print_r($des);
    foreach ($dept as $obj) {  ?>
    	<tr class="row<?php echo $obj->id; ?>">
			<td class="hidden-xs serial"><?php echo ++$i; ?></td>
			<td class=""><?php echo $obj->dept_name; ?></td>
			<td class="">
				<button type="button" class="btn btn-warning btn-xs"
					id="btn_edit_department" data-toggle="modal"
					data-id="<?php echo $obj->id; ?>"
					data-name="<?php echo $obj->dept_name; ?>"
					title="Editing a department" data-target="#editDeptModal">
					<span class="glyphicon glyphicon-edit"></span> Edit
				</button>
				<button class="btn btn-xs btn-danger" type="button"
					data-toggle="modal" data-target="#confirmDelete"
					data-title="Confirmation" data-id="<?php echo $obj->id; ?>"
					data-name="<?php echo $obj->dept_name; ?>" data-model="department"
					data-message="Are you sure you want to delete this department ?">
					<i class="glyphicon glyphicon-trash"></i> Delete
				</button>
			</td>
		</tr>
    	<?php } ?>
	</tbody>
</table>



<?php include 'footer.php'; ?>

<!-- Delete Confirm Modal -->
<?php include 'delete_confirm.php'; ?>

<!-- Add Department Modal -->
<div class="modal fade" id="addDeptModal" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Add a New Department</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="department" class="control-label">Department:</label>
						<input type="text" class="form-control" id="dept_name">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="btn_dept_save" class="btn btn-primary">Save
					changes</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Edit Department Modal -->
<div class="modal fade" id="editDeptModal" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Edit This Department</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="department" class="control-label">Department:</label>
						<input type="text" class="form-control" id="dept_name_edit">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="btn_dept_edit_save"
					class="btn btn-primary">Save changes</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script type="text/javascript">            

$('#addDeptModal').on('shown.bs.modal', function () {
	$("#dept_name").val("");
	$('#dept_name').focus();
});

$('#btn_dept_save').unbind('click').bind('click', function () {
	var dept = $("#dept_name").val();
	if(dept.length==0) return;
	
	$.ajax({
		type:"POST",
		url:"<?php echo base_url()?>settings/add_dept",
		data: {dept_name:dept},
		dataType:"json",
		success:function(response) {

			if(!response.status) {
			    alert(response.msg);
			    //$("#dept_name").val("");
			    return;
			}

		    var count = $(".box-body table tbody tr").length;
		    count++;
 			var html = "<tr class=\"row"+response.id+"\">\
				<td class=\"  sl\">"+count+"</td>\
				<td class=\" \">"+response.dept_name+"</td>\
				<td class=\" \">\
        			<button type=\"button\" class=\"btn btn-warning btn-xs\"\
    				id=\"btn_edit_department\" data-toggle=\"modal\"\
    				data-id=\""+response.id+"\"\
    				data-name = \""+response.dept_name+"\"\
    				title=\"Editing a department\" data-target=\"#editDeptModal\">\
    				<span class=\"glyphicon glyphicon-edit\"></span> Edit\
        			</button>\
					<button class=\"btn btn-xs btn-danger\" type=\"button\"\
						data-toggle=\"modal\" data-target=\"#confirmDelete\"\
						data-title=\"Confirmation\" data-id=\""+response.id+"\"\
						data-model=\"department\"\
						data-message=\"Are you sure you want to delete this department ?\">\
						<i class=\"glyphicon glyphicon-trash\"></i> Delete\
					</button>\
				</td>\
			</tr>"                				
			
		    $(".box-body table tbody").append(html);
 			$('.modal').modal('hide')
		}
	});
});

// edit Modal part
var dept_id;
var old_dept_name;
var btn;
$('#editDeptModal').on('shown.bs.modal', function (e) {
	btn = $(e.relatedTarget);
    dept_id = btn.attr('data-id');
	old_dept_name = btn.attr('data-name');
	var editInput = $('#dept_name_edit');
	editInput.val(old_dept_name);

	// Multiply by 2 to ensure the cursor always ends up at the end;
	// Opera sometimes sees a carriage return as 2 characters.
	var strLength= editInput.val().length * 2;
	editInput.focus();
	editInput[0].setSelectionRange(strLength, strLength);
});

$('#btn_dept_edit_save').off('click').on('click', function (e) {
	var dept = $("#dept_name_edit").val();
	if(dept.length==0) return;

	$.ajax({
		type:"POST",
		url:"<?php echo base_url()?>settings/update_dept",
		data: {dept_name:dept,dept_id:dept_id, old_dept_name:old_dept_name},
		dataType:"json",
		success:function(response) {
			$('#dept_name_edit').val("");
			if(!response.status) {
			    alert(response.msg);
			    //$("#dept_name").val("");
			    return;
			}

			var td = $(".row"+response.id).find('td');
			$(td[1]).text(response.dept_name);

			btn.attr('data-name',response.dept_name);
		    //$(".box-body table tbody").append(html);
 			$('.modal').modal('hide')
		}
	});
});

</script>