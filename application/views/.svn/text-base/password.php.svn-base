<div class='row'>
	<div class='col-sm-6 col-sm-offset-3'>
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">Change your password</h3>
			</div>
			<!-- /.box-header -->
			<!-- form start -->
			<form id="passwordform" class="form-horizontal" method="post" action="<?php echo base_url()?>settings/change_password">
				<div class="box-body">
					<div class="form-group">
						<label class="col-sm-3 control-label" for="currentPassword">Current</label>
						<div class="col-sm-5">
							<input type="password" placeholder="Password"
								id="currentPassword" name='currentPassword' class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="newPassword">New</label>
						<div class="col-sm-5">
							<input type="password" placeholder="Password"
								id="newPassword" name="newPassword" class="form-control" required>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="retypePassword">Retype New</label>
						<div class="col-sm-5">
							<input type="password" placeholder="Password"
								id="retypePassword" name="retypePassword" class="form-control" required>
						</div>
					</div>
				</div>
				<!-- /.box-body -->
				<div class="box-footer">
					<button class="btn btn-default" type="button">Cancel</button>
					<button class="btn btn-info pull-right" type="submit">Save Changes</button>
				</div>
				<!-- /.box-footer -->
			</form>
		</div>
		<?php if(!empty($message)){
		    if($message['status']){
		        echo "<h2 class='text-center text-success'>$message[msg]</h2>";
		    } else{
		        echo "<h2 class='text-center text-danger'>$message[msg]</h2>";
		    }
		}?>

	</div>
</div>


<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>


<script type="text/javascript">

$("#passwordform").validate({
	rules: {
		currentPassword: {
			required: true,
		},
		newPassword: {
			required: true,
			minlength:5,
			maxlength: 15,
		},
		retypePassword: {
			required: true,
			equalTo: "#newPassword",
			minlength:5,
			maxlength: 15,
		}
	},

});

</script>