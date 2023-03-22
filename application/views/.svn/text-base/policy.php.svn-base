<?php

?>
<link href="<?php echo base_url();?>assets/css/settings.css"
	type="text/css" rel="stylesheet" />
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />

<div class="box">
	<div class="box-header">
		<!-- h3 class="box-title">Incident List</h3  -->
		<?php if($isAdmin){ ?>
		<button id="btn_add_policy"
			class="btn btn-primary pull-right btn_add_right"
			data-target="#addPolicyModal" data-toggle="modal" type="button">
			<span class="glyphicon glyphicon-plus"></span> Add policy
		</button>
		<?php }?>

	</div>
	<div class="box-body">
		<div class='table-responsive'>
			<table
				class="col-xs-12 table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th class='hidden-xs'>Sn.</th>
						<th>Policy Title</th>
						<th>Attachment</th>
						<?php if($isAdmin) echo "<th>Action</th>"?>
					</tr>
				</thead>
				<tbody>
        		<?php
        		$i = 0;
        		foreach ($policies as $obj){
        		    echo "<tr><td class='hidden-xs serial' data-title='Sn.'>";
        		    echo ++$i;
        		    echo "</td><td data-title='Policy Title'>";
        		    echo $obj->policy_title;
        		    echo "</td><td data-title='Attachment'>";
        		    if(isset($obj->file)){
        		        foreach ($obj->file as $key=>$fileObj){
        		            echo ++$key.". ".$fileObj->file_name;
        		            echo "&nbsp;&nbsp;&nbsp;<a href='".base_url()."remark/download/".$fileObj->id."' class='btn btn-xs btn-primary'>Download</a><br>";
        		        }
        		    }

        		    echo "</td>";
        		    
        		    if($isAdmin){
        		        echo "<td data-title='Action'><a class='policyDelete btn btn-danger btn-xs' data-id='".$obj->policy_id."' >Delete</a></td>";
        		    }
        		}?>
        		</tbody>
			</table>
		</div>
	</div>
</div>


<!-- Add Policy Modal -->
<div class="modal fade" id="addPolicyModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id='policyForm' class='form-horizontal'>
				<input type='hidden' id='incidentId' value="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Add a Incident</h4>
				</div>
				<div class="modal-body">
				<div class='col-xs-12'>
					<div class="form-group">
						<label>Policy Title</label> <input id='policy_title' name='policy_title' type="text" placeholder="Enter ..."
							class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label" for="prescriptionFile">Attachment: </label>
						<div class="">
							<a class="btn btn-default" href='javascript://' id='attachment'>Browse
								attachment</a>
						</div>
					</div>
				</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" id="btn_dept_save" class="btn btn-primary">Add Policy</button>
				</div>
			</form>
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!--Delete Modal Dialog -->
<div class="modal fade" id="deleteModal" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Delete Parmanently</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure You want to delete this policy?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" id="deleteConfirm">Delete</button>
			</div>
		</div>
	</div>
</div>


<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js"
	type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>

<html>
<script type="text/javascript">

window.                		
$(document).ready(function(){


	var attCount = 0;
	$("#attachment").click(function(){
		$(this).parent().before("<div><input type='file' class='upload"+attCount+"' name='upload[]' style='display:none;'></div>");
		$(".upload"+attCount).on("change",function(){
			var filename = $(this).val().split('\\').pop();
			$(this).parent().append("<span>"+filename+" <a href='#' class='btn btn-danger btn-xs fileRemove'>Remove</a></span>");
			
			$(".fileRemove").unbind("click").bind("click",function(){
				//console.log($(this).parent().parent());
				$(this).parent().parent().remove();
			});
		});
		
		$(".upload"+attCount).click();
		attCount++;
	});

	$("#policyForm").on('submit',(function(e) {
		e.preventDefault();			
		$("body").append("<div class='loader'>Please wait&hellip;</div>");
		$('#addPolicyModal').modal('hide');

		var title = $("#policy_title").val();


		if(title.length<5){
			console.log(title.length);
			alert('please add meaningful policy title that contains atleast 5 letters.');
			return;
		}	
		
		$.ajax({
			type:"POST",
			url:'<?php echo base_url()?>remark/add_policy',
			data:  new FormData(this),
			contentType: false,
			cache: false,
			processData:false,
			dataType:"json",
			success:function(response) {
				$(".loader").remove();
				
				if(!response.status) {
					
				    alert(response.msg);
				    location.href = "<?php echo base_url();?>remark/policy/";	
				}else{
					alert(response.msg);
					location.href = "<?php echo base_url();?>remark/policy/";
				}
				
			}
		});
	}));

	
	var deleteButton;
	var parentRow;

	$('.policyDelete').on('click', function(){
		$('#deleteModal').modal('show');
		deleteButton = $(this);		
	});

	$('#deleteConfirm').unbind("click").bind("click",function(){
   		bindDeleteEvent(deleteButton);
   	});
 	
    
});

function bindDeleteEvent(it){
	var policyId = $(it).attr('data-id');	
	
	$.ajax({
  	    type:"POST",
  	    url:"<?php echo base_url()?>remark/del_policy/"+policyId,
  	    data:{},
  	    dataType:"json",
  	    success:function(response) {
    	    if(response.status) {
    	    	$(it).parents('tr').remove();
    	    	$('#deleteModal').modal('hide');
    	    	var serial = $('table').find('.serial');
    	    	for(i=0; i<serial.length; i++) {
    	    	    $(serial[i]).text(i+1);
    	    	}
    	    } else {
    	        alert(response.msg);
				return;
			}
    	}      	    
    });
}

</script>
</html>
