
<link href="<?php echo base_url();?>assets/css/main.css" type="text/css"
	rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/attendance.css"
	type="text/css" rel="stylesheet" />
<link type="text/css" href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-datetimepicker.min.css" />

<style>
.margin-top {
	margin-top: 7px;
}
</style>

<div class='row'>
	<div class='col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2'>
		<div class="box box-primary">
			<form id='requestForm' class='form-horizontal' method="post"
				action="<?php echo base_url()?>attendance/missing_mail_to_man">
				<input type="hidden" name='date' value='<?php echo $date?>'>
				<div class="box-header">
					<h1 class="box-title">Please fill out the missing ...</h1>
				</div>

				<div class="box-body">

					<div class="form-group">
						<label class="col-sm-2 control-label">Date</label>
						<div class="col-sm-8">
							<label class='margin-top'><?php echo date('l', strtotime($date)).", ".$date?></label>
						</div>
					</div>

					<?php if(!$logData['logIn']){?>
        				<div class="form-group">
						<label for="inAtt" class="col-sm-2 control-label">In Attendance</label>
			            <div class="col-sm-6">

							<div class='input-group date' id='inAttDTP'>
								<input type='text' class="form-control" name='inAtt' id='inAtt' placeholder="hh:mm:ss" required /> <span
									class="input-group-addon"> <span
									class="glyphicon glyphicon-time"></span>
								</span>
							</div>
						</div>
					</div>
					<?php }
					
					if(!$logData['logOut']){ ?>				
    				<div class="form-group">
						<label for="outAtt" class="col-sm-2 control-label">Out Attendance</label>
						<div class="col-sm-6">

							<div class='input-group date' id='outAttDTP'>
								<input type='text' class="form-control" name='outAtt' id='outAtt' placeholder="hh:mm:ss" required /> <span
									class="input-group-addon"> <span
									class="glyphicon glyphicon-time"></span>
								</span>
							</div>
						</div>
				    </div>
					<?php } ?>
					<div class="form-group">
						<label for="reasonId" class="col-sm-2 control-label">Reason</label>
						<div class="col-sm-8">

                            <textarea class="form-control" name='reason' id='reasonId' placeholder="enter ..." rows="3" required></textarea>
						</div>
				    </div>
				</div>
				<div class="box-footer text-center">
					<input id='submitButton' class="btn btn-primary btn-sm"
						type="submit" value='Send Auto Mail to Manager'>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>assets/js/moment.min.js"
	type="text/javascript"></script>

<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datetimepicker.min.js"
	type="text/javascript"></script>

<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />

<script type="text/javascript">

$(document).ready(function(){

/* 	$('#requestDate').datepicker({
	   format: 'yyyy-mm-dd'
	}); */
	
    $('#inAttDTP, #outAttDTP').datetimepicker({
    	format: 'HH:mm:ss'
    });
	
	$("#requestForm").on('submit',(function(e) {
    	//console.log("Invocked");
		//e.preventDefault();
 		
		$("body").append("<div class='loader'>Please wait&hellip;</div>");	
		$('#submitButton').attr('disabled','disabled');

		
// 		$.ajax({
// 			type:"POST",
			url:"<?php //echo base_url()?>attendance/send_request",
// 			data:  new FormData(this),
// 			contentType: false,
// 			cache: false,
// 			processData:false,
// 			dataType:"json",
// 			success:function(response) {
				
// 				$(".loader").remove();
				
// 				if(!response.status) {
// 					$('#submitButton').prop('disabled', false);
										
// 				    alert(response.msg);
// 				    return;
// 				}else{
// 					alert(response.msg);

					//location.href = "<?php //echo base_url()?>user/detail/<?php //echo $myInfo->userId?>"; 
// 					return;					
// 				}	
// 			}
// 		});
	}));
	
});

	
</script>
