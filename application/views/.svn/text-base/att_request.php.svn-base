
<link href="<?php echo base_url();?>assets/css/main.css" type="text/css"
	rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/attendance.css"
	type="text/css" rel="stylesheet" />

<div class='row'>
	<div class='col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2'>
		<div class="box box-primary">
			<form id ='requestForm' method="post" action=''>
				<div class="box-header">
					<h3 class="box-title">Send request for early/late office time</h3>
				</div>
				<div class="box-body">
					<div class='row'>
						<div class='col-sm-2'>
							<label>Request For:</label>
						</div>
						<div class='col-sm-10'>
							<div class="form-group">
								<div class="checkbox">
									<label> <input id='late' type="checkbox" value="L" name="late">
										<?php echo $leave_type['l']?>
									</label>
								</div>

								<div class="checkbox">
									<label> <input id="early" type="checkbox" value="E"
										name="early"> <?php echo $leave_type['e']?>
									</label>
								</div>

								<div class="checkbox">
									<label> <input id="absent" type="checkbox" value="A"
										name="absent"> <?php echo $leave_type['a']?>
									</label>
								</div>
								<div class="checkbox">
									<label> <input id="special" type="checkbox" value="S"
										name="special"> <?php echo $leave_type['u']?>
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class='row footer-margin'>
						<div class='col-sm-2'>
							<label>Date:</label>
						</div>
						<div class='col-sm-10'>
							<input id='requestDate' class="" type="text"
								value="<?php echo date('Y-m-d') ?>" placeholder='yyyy-mm-dd'
								name="requestDate">
						</div>
					</div>
					<div class='row'>
						<div class='col-sm-2'>
							<label>Reason:</label>
						</div>
						<div class='col-sm-10'>
							<textarea id='reason' class="" placeholder='Enter...'
								name="reason"></textarea>
						</div>
					</div>
				</div>
				<div class="box-footer text-center">
					<input id ='submitButton'  class="btn btn-primary btn-sm" type="submit"
						value='Send Auto Mail to Manager'>
				</div>
			</form>
		</div>
	</div>
</div>


<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />

<script type="text/javascript">

$(document).ready(function(){

	$('#requestDate').datepicker({
	   format: 'yyyy-mm-dd'
	});
	
	$('#requestDate').on('changeDate', function(ev){
	    $(this).datepicker('hide');  	    
	});

	var late = $('#late');
	var early = $('#early');
	var absent = $('#absent');
	var special = $('#special');

	absent.change(function() {
		late.prop('checked', false);
		early.prop('checked', false);
		special.prop('checked', false);
    });

	special.change(function() {
		late.prop('checked', false);
		early.prop('checked', false);
		absent.prop('checked', false); 
    });

	late.add(early).change(function() {
		absent.prop('checked', false); 
		special.prop('checked', false);
    });

	
	$("#requestForm").on('submit',(function(e) {
    	//console.log("Invocked");
		e.preventDefault();
 		
		$("body").append("<div class='loader'>Please wait&hellip;</div>");
		
		 
		if( !(late.is(':checked') || early.is(':checked') || absent.is(':checked') || special.is(':checked')) ){
			$(".loader").remove();
			return;  
	    }
		$('#submitButton').attr('disabled','disabled');
		
		$.ajax({
			type:"POST",
			url:"<?php echo base_url()?>attendance/send_request",
			data:  new FormData(this),
			contentType: false,
			cache: false,
			processData:false,
			dataType:"json",
			success:function(response) {
				
				$(".loader").remove();
				
				if(!response.status) {
					$('#submitButton').prop('disabled', false);
										
				    alert(response.msg);
				    return;
				}else{
					alert(response.msg);

					location.href = "<?php echo base_url()?>user/detail/<?php echo $myInfo->userId?>"; 
					return;					
				}	
			}
		});
	}));
	
});

	
</script>
