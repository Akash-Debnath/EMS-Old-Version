<link rel="stylesheet"
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet"
	href="<?php echo base_url();?>assets/lib/theme/css/AdminLTE.css">


<div class='row'>
	<div class='col-sm-8 col-sm-offset-2'>
	
		<?php		
		if(!empty($message)){
		    if($message['status']){
		        echo "<h2 class='text-center text-success'>$message[msg]</h2>";
		        echo "<h4 class='text-blue text-center'><a  href='".base_url()."user/login'> Go Back to Login page</a></h4>";
		    } else{
		        echo "<h2 class='text-center text-danger'>$message[msg]</h2>";
		    }
		} else {?>
		<?php //if($isKeyMatch && $isToTime) {?>	
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">Change your password</h3>
			</div>
			<!-- /.box-header -->
			<!-- form start -->
			<form id="passwordform" class="form-horizontal" method="post" action="<?php echo base_url()?>user/reset_pass?key=<?php if(isset($key)) echo $key?>">
				<input type ='hidden' name='key_id' value='<?php echo $key?>'>
				<div class="box-body">
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
					<a class="btn btn-default" href='<?php echo base_url()?>user/login'>Cancel</a>
					<button class="btn btn-info pull-right" type="submit">Save Changes</button>
				</div>
				<!-- /.box-footer -->
			</form>
		</div>
		<?php } ?>

	</div>
</div>

<script src="<?php echo base_url();?>assets/js/jquery-1.11.2.min.js"></script>
<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap.min.js"></script>
<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>


<script type="text/javascript">

$("#passwordform").validate({
	rules: {

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