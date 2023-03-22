<?php

?>
<link href="<?php echo base_url();?>assets/css/settings.css"
	type="text/css" rel="stylesheet" />
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />
	
<style>
<!--
    .margin-top{
	   margin-top: 15px;
    }
-->
</style>

<div class="box">
	<div class="box-header">
	    <div class="col-xs-12">
    	    <div class="row margin-top">        
                <div class='col-sm-8'>
                    <form id='sendYearForm' class="form-horizontal" method="post" action="<?php echo base_url()?>settings/incident" >
                        <div class="form-group" style="margin-bottom: 0px;" >
                            <label class="col-sm-3 col-xs-3 control-label">Year</label>
                            <div class="col-sm-3 col-xs-5">                        
                                <select id="yearSelect" class="selectpicker form-control" name="yearSelect">
                                    <option value="">--- Select Year---</option>
                                    <?php
                                    for($iYear = date('Y'); $iYear >= $ems_start_year; $iYear--){
                                        if($select_year == $iYear){
                                            echo "<option value='".$iYear."' selected>".$iYear."</option>";
                                        }else{
                                            echo "<option value='".$iYear."'>".$iYear."</option>";
                                        }
                                    }
                                    ?>
                                     
                                </select>
                            </div>
                            
                        </div>
                    </form>
                </div>                 
                <?php if($isAdmin) {?>
                <div class='col-sm-4'>     
                    <button id="btn_add_incident"
            			class="btn btn-primary pull-right"
            			data-target="#addIncidentModal" data-toggle="modal" type="button">
            			<span class="glyphicon glyphicon-plus"></span> Add Incident
            		</button>
        		</div>
                <?php }?>
            </div>
        </div>
    </div>
	<div class="box-body">
		<div class='table-responsive'>
			<table
				class="col-xs-12 table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th class='hidden-xs'>Sn.</th>
						<th>Date</th>
						<th>Description</th>
						<?php if($isAdmin) {?><th>Action</th><?php }?>
					</tr>
				</thead>
				<tbody>
        		<?php
        		$i = 0;
        		foreach ($incidents as $obj){
        		    echo "<tr><td class='hidden-xs serial' data-title='Sn.'>";
        		    echo ++$i;
        		    echo "</td><td data-title='Date'>";
        		    echo date('dS F Y (l)', strtotime($obj->date));
        		    echo "</td><td data-title='Description'>";
        		    echo $obj->description;
        		    echo "</td>";
        		    
        		    if($isAdmin){
        		        echo "<td data-title='Action'>";
        		        echo "<a class='incidentEdit btn btn-warning btn-xs' data-id='".$obj->id."' >Edit</a> <a class='incidentDelete btn btn-danger btn-xs' data-id='".$obj->id."' >Delete</a>";
        		    }                		    
        		}?>
        		</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addIncidentModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id='incidentForm' class='form-horizontal'>
				<input type='hidden' id='incidentId' value="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Add a Incident</h4>
				</div>
				<div class="modal-body">

					<div class='row'>
						<div class='col-sm-5'>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="dateFrom">From</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="dateFrom"
										name="dateFrom" placeholder="yyyy-mm-dd" value="" required>
								</div>
							</div>
						</div>
						<div class='col-sm-5'>
							<div class="form-group">
								<label class="col-sm-3 control-label" for="dateTo">To</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" id="dateTo"
										name="dateTo" placeholder="yyyy-mm-dd" value="" required>
								</div>
							</div>
						</div>
						<div class='col-xs-12'>
							<div class='col-xs-12'>
								<div class="form-group">
									<label>Description</label> <input type="text" id="incidentDesc"
										name="incidentDesc" placeholder="Enter ..."
										class="form-control">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" id="btn_dept_save" class="btn btn-primary">Save
						changes</button>
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
				<p>Are you sure You want to delete this incident?</p>
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


<script>
$(document).ready(function(){

    //Date Correction    

	$('#dateFrom, #dateTo').datepicker({
	 	format: 'yyyy-mm-dd'
	});

	$('#dateFrom').on('changeDate', function(ev){

		$('#dateTo').datepicker('setStartDate',$(this).val());
		$("#dateTo").datepicker("setDates", $(this).val());
		$(this).datepicker('hide');
	});	
	$('#dateTo').on('changeDate', function(ev){
	    //$('#dateFrom').datepicker('setEndDate',$(this).val());
	    $(this).datepicker('hide'); 
	});

	$('#yearSelect').on('change', function(){

		$("#sendYearForm").submit();
	});

	$("#incidentForm").validate({
		rules: {
			dateFrom: {
			    required: true,
			    date: true,
			},
			dateTo: {
				required: true,
				date: true,
			},
			incidentDesc:{
				required: true,
				minlength:3,
			}	
				
		},
		submitHandler : function(event) {
			var id = $('#incidentId').val()
			
			if(id == ""){
				var url = "<?php echo base_url()?>settings/add_incident/";
			}else{
				var url = "<?php echo base_url()?>settings/update_incident/"+id;
			}
       		var desc = $('#incidentDesc').val();
       		var from = $('#dateFrom').val();
       		var to = $('#dateTo').val();
			$.ajax({
            	    type:"POST",
            	    url:url,
            	    data: {description:desc, from:from, to:to},
            	    dataType:"json", 
            	    success:function(response) {
                	    
                  	    if(response.status) {
                  	    	location.reload();                 	      
                  	    } else {
                  	        alert(response.msg);
              				return;
              			}
                    	$('#addIncidentModal').modal('hide');
                  	}      	    
            });
        }		
	});

	var deleteButton;
	var parentRow;

	$('.incidentEdit').on('click', function(){

		var incidentId = $(this).attr('data-id');
				
		parentRow = $(this).parents('tr');		
		var from  =  parentRow.find("td:nth-child(2)").text();
		var description = parentRow.find("td:nth-child(3)").text();
		
		$('#addIncidentModal').find('#incidentId').val(incidentId);
		$('#addIncidentModal').find('#dateFrom').val(from);
		$('#addIncidentModal').find('#dateTo').val(from);
		$('#addIncidentModal').find('#incidentDesc').val(description);
		$('#addIncidentModal').modal('show');	
	});

	$('#addIncidentModal').on('hidden.bs.modal', function(){
		
		$(this).find('#incidentId').val('');
		$(this).find('#dateFrom').val('');
		$(this).find('#dateTo').val('');
		$(this).find('#incidentDesc').val('');
	});
	

	$('.incidentDelete').on('click', function(){
		$('#deleteModal').modal('show');
		deleteButton = $(this);		
	});

	$('#deleteConfirm').unbind("click").bind("click",function(){
   		bindDeleteEvent(deleteButton);
   	});
 	
    
});

function bindDeleteEvent(it){
	var incidentId = $(it).attr('data-id');	
	
	$.ajax({
  	    type:"POST",
  	    url:"<?php echo base_url()?>settings/del_incident/"+incidentId,
  	    data:{},
  	    dataType:"json",
  	    success:function(response) {
    	    if(response.status) {
        	    
    	    	$(it).parents('tr').remove();
    	    	$('#deleteModal').modal('hide');
    	    	var serial = $('table').find('.serial');
    	    	for(var i=0; i<serial.length; i++) {
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