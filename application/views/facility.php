<?php include 'header.php'; ?>
<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>

<div class='box'>

	<div class='box-header'>
    	<button type="button" class="btn btn-info pull-right btn_add_right"
    		id="btn_add_facility" data-toggle="modal" title="Adding new facility"
    		data-target="#addFacilityModal">
    		<span class="glyphicon glyphicon-plus"></span>Add New Facility
    	</button>
	</div>
	<div class='box-body'>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="hidden-xs" style="width: 10px;"></th>
					<th class="">Facility</th>
					<th class="">Description</th>
					<th class="">Action</th>
				</tr>
			</thead>
			<tbody>
    		<?php
                $i = 0;
                foreach ($facilities as $obj) {
            ?>
    			<tr class="row<?php echo $obj->facility_id; ?>">
						<td class="hidden-xs serial"><?php echo ++$i; ?></td>
						<td class=""><?php echo $obj->facility; ?></td>
						<td class=""><?php echo $obj->description; ?></td>
						<td class="">
							<button type="button" class="btn btn-warning btn-xs"
								id="btn_edit_facility" data-toggle="modal"
								data-id="<?php echo $obj->facility_id; ?>"
								data-name="<?php echo $obj->facility; ?>"
								data-description="<?php echo $obj->description; ?>"
								title="Editing a facility" data-target="#editFacilityModal">
								<span class="glyphicon glyphicon-edit"></span> Edit
							</button>
							<button class="btn btn-xs btn-danger" type="button"
								data-toggle="modal" data-target="#deleteFacilityModal"
								data-id="<?php echo $obj->facility_id; ?>"
								data-name="<?php echo $obj->facility; ?>">
								<i class="glyphicon glyphicon-trash"></i> Delete
							</button>
						</td>
					</tr>
    		<?php } ?>
			</tbody>
		</table>
	</div>
</div>


<?php include 'footer.php'; ?>


<!-- Add Facility Modal -->
<div class="modal fade" id="addFacilityModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Add a New Facility</h4>
			</div>
			<div class="modal-body">
				<form id="facilityAddForm">
					<div class="form-group">
						<label for="facility_name" class="control-label">Facility:</label>
						<input type="text" class="form-control" id="facility_name"
							name="facility_name" placeholder="Facility name" required>
					</div>
					<div class="form-group">
						<label for="description" class="control-label">Description:</label>
						<textarea rows="3" cols="" class="form-control" id="description"
							placeholder="Add related description" style="max-width: 100%;"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" id="btn_facility_save" class="btn btn-primary">Save
					changes</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Edit Facility Modal -->
<div class="modal fade" id="editFacilityModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
				<form id="facilityEditForm">
					<div class="form-group">
						<label for="facility_name_edit" class="control-label">Facility:</label>
						<input type="text" class="form-control" id="facility_name_edit"
							name="facility_name" placeholder="Facility name" required>
					</div>
					<div class="form-group">
						<label for="description_edit" class="control-label">Description:</label>
						<textarea rows="3" cols="" class="form-control"
							id="description_edit" placeholder="Add related description"
							style="max-width: 100%;"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="btn_facility_edit_save"
					class="btn btn-primary">Save changes</button>
			</div>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!-- Delete Modal -->
<div class="modal fade" id="deleteFacilityModal" role="dialog"
	aria-labelledby="deleteFacilityModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this facility ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger"
					id="btn_delete_facility">Delete</button>
			</div>
		</div>
	</div>
</div>
<!-- /.modal -->

<script type="text/javascript">            



$().ready(function() {

	// validate signup form on keyup and submit
	$("#facilityAddForm").validate({
		rules: {
			facility_name: {
				required: true,
				minlength: 2
			},
		}
		
	});


	$('#addFacilityModal').on('shown.bs.modal', function () {
		$("#facility_name").val("");
		$('#facility_name').focus();
	});

	$('#btn_facility_save').unbind('click').bind('click', function () {
		var facility = $("#facility_name").val();
		var description = $("#description").val();

		if(facility.length==0) {
			$('#facility_name').focus();
			return;
		}	
		
		$.ajax({
			type:"POST",
			url:"<?php echo base_url()?>settings/add_facility",
			data: {facility:facility, description:description},
			dataType:"json",
			success:function(response) {

				if(!response.status) {
				    alert(response.msg);
				    $("#facility_name").val("");
				    $('#facility_name').focus();
				    return;
				}
				
	 			$('.modal').modal('hide');
	 		    window.location.href= "<?php echo base_url()?>settings/facility";
			}
		});
	});

	// edit Modal part

	$('#editFacilityModal').on('shown.bs.modal', function (e) {
		var btn = $(e.relatedTarget);
		var facility_id = btn.attr('data-id');
		
		var facility_name = btn.attr('data-name');
		var description = btn.attr('data-description');
		var editFacility = $('#facility_name_edit');
		var editDescription = $('#description_edit'); 
		
		editFacility.val(facility_name);
		editDescription.val(description);

		// Multiply by 2 to ensure the cursor always ends up at the end;
		// Opera sometimes sees a carriage return as 2 characters.
		var strLength= editFacility.val().length * 2;
		editFacility.focus();
		editFacility[0].setSelectionRange(strLength, strLength);

	    // Save button of edit modal
		$('#btn_facility_edit_save').off('click').on('click', function (e) {
			var facility = $("#facility_name_edit").val();
			var description = $("#description_edit").val();

			if(facility.length==0) {
				$('#facility_name_edit').focus();
				return;
			}	
			
			$.ajax({
				type:"POST",
				url:"<?php echo base_url()?>settings/update_facility",
				data: {facility:facility, description:description, facility_id:facility_id},
				dataType:"json",
				success:function(response) {

					if(!response.status) {
					    alert(response.msg);
					    $('#facility_name').focus();
					    return;
					}
					
		 			$('.modal').modal('hide');
		 		    window.location.href= "<?php echo base_url()?>settings/facility";
				}
			});
		});
	});



	//Form confirm (yes/ok) handler, submits form
	$('#deleteFacilityModal').on('shown.bs.modal', function (e) {
	    var f_id = $(e.relatedTarget).attr('data-id');
	    var facility_name = $(e.relatedTarget).attr('data-name');
		
		$('#btn_delete_facility').unbind('click').bind("click", function(){
		    //$(this).data('form').submit();
		    $.ajax({
			    type:"POST",
			    url:"<?php echo base_url().'settings/delete_facility/'; ?>"+f_id,
			    data:{facility_name:facility_name},
			    dataType:"json",
			    success:function(response) {
			    	if(!response.status) {
			    		$('.modal').modal('hide');
					    alert(response.msg);
					    return;
					}
					
		 			$('.modal').modal('hide');
		 		    window.location.href= "<?php echo base_url()?>settings/facility";
		  	    }
			});
		});
	});

});
</script>