<?php

//print_r($grade_list);
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/pagination.css">
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />
<style>

	.forScroll {
		height: 250px;
		overflow-y: auto;
	}

	.padding-5{
		padding: 4px 6px;
	}

	.no-border{
		border: 0;
	}

	.ul-padding-left{
		list-style-type: none;
		padding-left: 20px;
	}

	.m-b{
		margin-bottom: 15px;
	}


</style>

	<div class="box-body table-responsive">
		<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th>Sl No:</th>
					<th>ID</th>
					<th>Name</th>
					<th>Mobile</th>
					<th>Phone</th>
					<th>Present Address</th>
					<th>Permanent Address</th>
					<th>Achievement</th>
					<th>Experience</th>
					<th>Date of Birth</th>
					<th>Blood Group</th>
					<th>Gender</th>
					<th>Approve</th>
					<th>Reject</th>
				</tr>
			</thead>
			<tbody>
				<?php $flag=1; foreach($updated_request as $updated) { ?>
					<tr>
						<td><?= $flag++ ?></td>
						<td><?= $updated['emp_id'] ?></td>
						<td><?= $updated['name'] ?></td>
						<td><?= $updated['mobile'] ?></td>
						<td><?= $updated['phone'] ?></td>
						<td><?= $updated['present_address'] ?></td>
						<td><?= $updated['permanent_address'] ?></td>
						<td><?= $updated['last_edu_achieve'] ?></td>
						<td><?= $updated['experience'] ?></td>
						<td><?= $updated['dob'] ?></td>
						<td><?= $updated['blood_group'] ?></td>
						<td><?= $updated['gender'] ?></td>
						<td><?php if ($updated['status']=="N" || $updated['status']=="R") {?>
							<button class='btn btn-success fa fa-check' onclick="approved( '<?= $updated['emp_id'] ?>' )"></button>
						<?php }else{
							echo "";
						} ?></td>
						<td><?php if ($updated['status']=="N" || $updated['status']=="A") {?>
							<button class='btn btn-danger fa fa-trash' onclick="reject( '<?= $updated['emp_id'] ?>' )"></button>
						<?php }else{
							echo "";
						} ?></td>

					</tr>
				<?php }?>
			</tbody>
		</table>
	</div>

	<div class="modal fade" id="RejectModal" role="dialog">
		<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Are you sure want to 'Reject' this item</h4>
			</div>
			<div class="modal-body text-right">
			<br>
				<form action="<?php echo base_url()?>user/reject_Update" method="post">
					<input type="hidden" name="id" id="reject_id">
					<button type="submit" class="btn btn-danger">Reject</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</form>
			</div>
		</div>
		</div>
	</div>
	<div class="modal fade" id="ApproveModal" role="dialog">
		<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Are you sure want to 'Approve' this item</h4>
			</div>
			<div class="modal-body text-right">
			<br>
				<form action="<?php echo base_url()?>user/approve_Update" method="post">
					<input type="hidden" name="id" id="approve_id">
					<button type="submit" class="btn btn-success">Approve</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</form>
			</div>
		</div>
		</div>
	</div>
<?php include 'footer.php'; ?>



<link href="<?php echo base_url();?>assets/css/user.css"
	  type="text/css" rel="stylesheet" />
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

<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>

<script src="https://apis.google.com/js/platform.js?onload=onLoadCallback" async defer></script>


<script type="text/javascript">


	$(document).ready(function() {

		

	});
	function approved(id) {
		$('#ApproveModal').modal("show");
		var emp=$('#approve_id').val(id);
	}
	function reject(id) {
		$('#RejectModal').modal("show");
		var emp=$('#reject_id').val(id);
	}

</script>

