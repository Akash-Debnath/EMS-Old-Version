<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="google-signin-scope" content="profile email">
<meta name="google-signin-client_id" content="152628580978-stbc9394kd9ttflf7m17e9og009mngft.apps.googleusercontent.com">

<title>EMS | Log in</title>
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
<!-- Bootstrap 3.3.2 -->
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- Font Awesome Icons -->
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<!-- Theme style -->
<link href="<?php echo base_url();?>assets/lib/AdminLTE/AdminLTE.min.css" rel="stylesheet" type="text/css" />
<!-- iCheck -->
<link href="<?php echo base_url();?>assets/lib/AdminLTE/blue.css" rel="stylesheet" type="text/css" />
</head>

<body class="login-page">
				
	<div class="login-box">
		<div class="login-logo">
			<a href="<?php echo base_url();?>user/login_tester"><b>EMS</b> Login</a>
		</div><!-- /.login-logo -->
		<div class="login-box-body">
			<p class="login-box-msg">Sign in to start your session</p>
			<form action="<?php echo base_url();?>user/login_developer" method="post" style="margin-bottom: 5px;">
				<?php 
					if(!empty($errMsg)) echo "<div class='text-red text-center'><strong>$errMsg</strong></div>";
				?>
				<div class="form-group has-feedback">
					<input type="text" class="form-control" placeholder="Employee ID / Official Email ID / gmail ID" name='login_id' value="<?php echo $login_id; ?>" />
					<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
				</div>
				<div class="form-group has-feedback">
					<input type="password" class="form-control" placeholder="Password" name='password' value="<?php echo $password; ?>" />
					<span class="glyphicon glyphicon-lock form-control-feedback"></span>
				</div>
				<div class="row">
					<div class="col-xs-4 col-xs-offset-4">
						<button type="submit" name="login" class="btn btn-primary btn-block btn-flat">Sign In</button>
					</div><!-- /.col -->
				</div>
			</form>
			
			<p><a href="<?php echo base_url()?>user/forgot" >I forgot my password</a><br></p>
			
            <div class="social-auth-links text-center">
                <p>- OR -</p>
                <div align="center">
                    <div class="g-signin2" data-onsuccess="onSignIn" data-onfailure="onSignInFailure" data-theme="dark"></div>
                </div>
              
              
              <!-- a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google"></i> Sign in using Gmail</a -->
            </div><!-- /.social-auth-links -->
			
		    
		
		
		</div><!-- /.login-box-body -->
	</div><!-- /.login-box -->
	
<!-- jQuery 2.1.3 -->
<script src="<?php echo base_url();?>assets/js/jquery-1.11.2.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- iCheck -->
<script src="<?php echo base_url();?>assets/lib/AdminLTE/icheck.min.js" type="text/javascript"></script>

<script src="https://apis.google.com/js/platform.js?onload=onLoadCallback" async defer></script>

<script>

$.signIn = false;
				
$(function () {

	$(".g-signin2").click(function(){
		
		$.signIn = true;
	});
});

function onSignIn(googleUser) {	
	// Handle successful sign-in
    
	/*var profile = googleUser.getBasicProfile();
    console.log("ID: " + profile.getId()); // Don't send this directly to your server!
    console.log("Name: " + profile.getName());
    console.log("Image URL: " + profile.getImageUrl());
    console.log("Email: " + profile.getEmail());*/

	if($.signIn){
		
	    // The ID token you need to pass to your backend:
	    var id_token = googleUser.getAuthResponse().id_token;

		$.ajax({
	  	    type:"POST",
	  	    url:"<?php echo base_url()?>user/login_gmail",
	  	    data:{id_token:id_token},
	  	    dataType:"json",
	  	    success:function(response) {
	    	    if(response.status) {

	    	    	window.location = response.go_url;
	    	    	return;
	    	    	
	    	    } else {
	    	        alert(response.msg);
					return;
				}
	    	}      	    
	    });		
	}	

}

function onSignInFailure() {
  // Handle sign-in errors
	console.log("failed"); 
}

</script>
</body>

</html>