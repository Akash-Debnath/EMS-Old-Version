<link href="<?php echo base_url();?>assets/css/main.css"
    type="text/css" rel="stylesheet" />
    <link href="<?php echo base_url();?>assets/css/leave.css"
        type="text/css" rel="stylesheet" />

<div class=''>

    <?php if( $type['manager'] || $type['management'] ) { ?>

    <div class="leaveHeader" >Approval Request: <?php echo count($approvalRequest); ?></div>

    <div class ='row'>
       	<?php
        if(count($approvalRequest)==0) {
        	echo "<div class='noFound'>No approval request found.</div>";
        } else {
        	foreach ($approvalRequest as $obj) {
        	?>
        	<div class="col-xs-12 col-sm-4 col-md-3 leaveBox">
        	   <div class ='squareBox'>
            		<?php echo $obj->emp_id;?><br>
            		<?php echo "<a href='".base_url()."user/detail/".$obj->emp_id."'>".$obj->name."</a>";?><br>
            		<?php echo '<i>'.$obj->designation.'</i>';?><br>
            		<?php echo $obj->dept_name;?><br>
            		<div class='squareBoxHeader'>Leave Brief Info</div>
            		
            		<p><label>Leave Type:</label> <?php
            		
            		$ms = '';
            		
            		if($obj->late_req == 'R')  $ms = $late_early_leave['l'];
            		if($obj->early_req == 'R') $ms = $late_early_leave['e'];
            		if($obj->late_req == 'R' && $obj->early_req == 'R'  ) $ms = $late_early_leave['l']." and ".$late_early_leave['e'];
            		if($obj->absent_req == 'R') $ms = $late_early_leave['a'];
            		if($obj->special_req == 'R') $ms = $late_early_leave['u'];
            		
            		echo $ms;
            		?></p>
            		<p><label>Date: </label> <?php echo $obj->date;?></p>
            		<p><label>Reason: </label> <?php echo stripcslashes($obj->reason);?></p>
            		
            		<div class='text-center'>
            		    <a class='btn btn-primary btn-xs approve' data-id='<?php echo $obj->id;?>' >Approve</a> &nbsp;&nbsp;&nbsp;&nbsp;
            		    <a class='btn btn-warning btn-xs refuse' data-id='<?php echo $obj->id;?>'>Refuse</a>
            		</div>
            	</div>
        	</div>
        	<?php
        	}
        }
        ?>
    </div>
    
    <?php }
    if( $type['admin'] ) { ?>
    <div class="leaveHeader">Verification Request: <?php echo count($verificationRequest); ?></div>
    
    <div class = 'row'>
       <?php
        if(count($verificationRequest)==0) {
            echo "<div class='noFound'>No verification request found.</div>";
        } else {
        	foreach ($verificationRequest as $obj) {
        	?>
        	<div class="col-xs-12 col-sm-4 col-md-3 leaveBox">
        	   <div class ='squareBox'>
            		<?php echo $obj->emp_id;?><br>
            		<?php echo "<a href='".base_url()."user/detail/".$obj->emp_id."'>".$obj->name."</a>";?><br>
            		<?php echo '<i>'.$obj->designation.'</i>';?><br>
            		<?php echo $obj->dept_name;?><br>
            		<div class='squareBoxHeader'>Leave Brief Info</div>
            		
            		<p><label>Leave Type:</label> <?php
            		
            		$ms = '';
            		
            		if($obj->late_req == 'R')  $ms = $late_early_leave['l'];
            		if($obj->early_req == 'R') $ms = $late_early_leave['e'];
            		if($obj->late_req == 'R' && $obj->early_req == 'R'  ) $ms = $late_early_leave['l']." and ".$late_early_leave['e'];
            		if($obj->absent_req == 'R') $ms = $late_early_leave['a'];
            		if($obj->special_req == 'R') $ms = $late_early_leave['u'];
            		
            		echo $ms;
            		?></p>
            		<p><label>Date: </label> <?php echo $obj->date;?></p>
            		<p><label>Reason: </label> <?php echo $obj->reason;?></p>
            		
            		<div class='text-center'>
            		    <a class='btn btn-primary btn-xs verify' data-id='<?php echo $obj->id;?>'>Verify</a> &nbsp;&nbsp;&nbsp;&nbsp;
            		    <a class='btn btn-warning btn-xs refuseVerification' data-id='<?php echo $obj->id;?>'>Refuse</a>
            		</div>
            	</div>
        	</div>
        	<?php
        	}
        }
        ?>
    </div>
    
    <?php } ?>

 </div>


<!-- Modal Dialog -->
<div class="modal fade" id="confirmApproveModal" role="dialog"
	aria-labelledby="confirmApproveLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure about this ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="confirmCancel">Cancel</button>
				<button type="button" class="btn btn-primary" id="confirmApprove">Confirm</button>

			</div>
		</div>
	</div>
</div>

<!-- Refuse Modal Dialog -->
<div class="modal fade" id="refuseModal" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<form>
					<div class="form-group">
						<label for="excuseText" class="control-label">Give an excuse to	refuse this leave request:</label>
						<textarea class="form-control" name="excuseText" id="excuseText" required></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"
					id="refuseCancel">Cancel</button>
				<button type="button" class="btn btn-danger" id="refuseOk">Refuse</button>
			</div>
		</div>
	</div>
</div>

<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />

<script type="text/javascript">
            
var baseURL = "<?php echo base_url()?>attendance/";

$(document).ready(function(){

	//Approve Modal
	$('.approve').on('click', function(){
		staffId = $(this).attr('data-id');
		
		$('#confirmApproveModal').modal('show');
		url = baseURL+'approve/'+staffId;

	});

	$('.verify').on('click', function(){
		var staffId = $(this).attr('data-id');
		
		$('#confirmApproveModal').modal('show');
		url = baseURL+'verify/'+staffId;
	});

    $('#confirmApproveModal').find('.modal-footer #confirmApprove').off('click').on('click', function () {
			
		$('#confirmApproveModal').modal('hide');
		$("body").append("<div class='loader'>Please wait&hellip;</div>");
		$.ajax({
			type:"POST",
			url:url,
			data: {},
			dataType:"json",
			success:function(response) {
				$(".loader").remove();
				if(!response.status) {
				    alert(response.msg);
				    return;
				}else{
					alert(response.msg);
					location.reload();
				}	
			}
		});
	});


    
    //Refuse Modal
	var staffId = "";
	var url = '';
	$('.refuse').on('click', function(){
		staffId = $(this).attr('data-id');
		//console.log(staffId);
		url = baseURL+"refuse/"+staffId+"/approve",
		$('#refuseModal').modal('show');
	});
	
	$('.refuseVerification').on('click', function(){
		staffId = $(this).attr('data-id');
		//console.log(staffId);
		url = baseURL+"refuse/"+staffId+"/verify",
		$('#refuseModal').modal('show');
	});
	
	$('#refuseModal').on('hidden.bs.modal', function () {
		$('#excuseText').val('');
	});

	
    $('#refuseModal').find('.modal-footer #refuseOk').off('click').on('click', function () {
		var excuse = $('#excuseText').val();
				
		if(excuse.length <= 3){
			alert('please give him convincing excuse.');
			return;
		}
		$('#refuseModal').modal('hide');
		$("body").append("<div class='loader'>Please wait&hellip;</div>");
		$.ajax({
			type:"POST",
			url:url,
			data: {excuse:excuse},
			dataType:"json",
			success:function(response) {
				$(".loader").remove();
				if(!response.status) {
				    alert(response.msg);
				    return;
				}else{
					alert(response.msg);
					location.reload();
				}	
			}
		});
	});

	
});
            
</script>
