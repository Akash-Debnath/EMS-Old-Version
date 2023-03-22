<div class="row">
	<div class="col-lg-10 col-lg-offset-1">

		<div class="box box-info">
			<div class="box-header with-border">
			    <div class="form-group">
					<div class="col-sm-12 text-center">
                      		<?php echo img('assets/images/genuity.gif'); ?>
                    </div>
                    
                	<h3 class='text-center'>Store Requisition Form</h3>
					<div class='col-xs-12'>
						<div class='pull-right'> <label>Date:</label>  &nbsp; <?php if(isset($voucher['date'])) echo $voucher['date']; else echo date('Y-m-d');?></div>
					</div>

					<div class="col-sm-6">
						<label>Employee Name:</label> &nbsp; <span class='dashUnderline'><?php if(isset($requester->name)) echo $requester->name; else echo $myInfo->userName;?></span>
					</div>
					<div class="col-sm-6">
						<label>Employee ID:</label> &nbsp; <span class='dashUnderline'><?php if(isset($requester->emp_id)) echo $requester->emp_id; else echo $myInfo->userId;?></span>
					</div>
					<div class="col-sm-6">
						<label>Department:</label> &nbsp; <span class='dashUnderline'><?php if(isset($requester->dept_name)) echo $requester->dept_name; else echo $myInfo->userDepartment;?></span>
					</div>
					<div class="col-sm-6">
						<label>Designation:</label> &nbsp; <span class='dashUnderline'><?php if(isset($requester->designation)) echo $requester->designation; else echo $myInfo->userDesignation;?></span>
					</div>
				</div>	
			</div>
			<!-- /.box-header -->
			<div class="box-body">
				<div class='table-responsive'>
					<table
						class="col-xs-12 table-bordered table-striped table-condensed text-center">
						<thead>
							<tr>
								<th class='hidden-xs'>Sn.</th>
								<th>Item Name</th>
								<th>Quantity</th>
								<th>Remarks</th>
								<?php if(empty($vid)) echo '<th>Action</th>'?>
							</tr>
						</thead>
                		<tbody id='tBody'>
                		    <?php if(isset($ledgers)){
                		            $i = 0;
                		            foreach ($ledgers as $obj){
                		                echo "<tr><td class='serial hidden-xs' data-title='Sn.'>";
                		                echo ++$i;
                		                echo "</td><td data-title='Item'>";
                		                echo $obj->item;
                		                echo "</td><td data-title='Quantity'>";
                		                echo $obj->quantity;
                		                echo "</td><td data-title='Remarks'>";
                		                echo $obj->remark;
                		                echo "</td></tr>";
                		            }
                		    }?>
                		</tbody>
					</table>
				</div>
			</div>
			<!-- /.box-body -->
			<div class="box-footer">
			<?php
			    if(empty($vid)) { ?>	
				<button class="btn btn-primary" data-target="#addItemtoLedgerModal" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add Item
				</button>
				<button id='SendBtn' class="btn btn-info pull-right">Send</button>
			<?php
                } else{
                    if($isManager){
                        
                        if(empty($voucher['approved_by'])){
                            echo "<button id='approveRequisition' class='btn btn-primary'> Approve</button>";
                            echo "<button id='refuseApprove' class='btn btn-danger'> Refuse</button>";
                        }
                    }
                    if($isAdmin && !empty($voucher['approved_by'])){
                        
                        if(empty($voucher['verified_by'])){
                            echo "<button id='verifyRequisition' class='btn btn-primary'> Verify</button>";
                            echo "<button id='refuseVerify' class='btn btn-danger'> Refuse</button>";
                        }
                    }    
                }
            ?>
			</div>
			<!-- /.box-footer -->
		</div>

	</div>
</div>

<?php if(empty($vid)){ ?>
<!-- Add Facility Modal -->
<div class="modal fade" id="addItemtoLedgerModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		<form id="itemForm" class='form-horizontal' method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Add a New Item</h4>
			</div>
			<div class="modal-body">
				    <div class="form-group">
						<label for="lastname" class="col-sm-2 control-label">Category:</label>
						<select class="col-sm-4 selectpicker " id='categorySelect' data-live-search="false"
							style='width: 200px;'>
                	       <option value=''>---Select---</option>
                    	<?php foreach ($categories as $obj) {
                    		echo "<option value='".$obj->category_id."'>".$obj->category."</option>";
                    	} ?>
                    	</select>
					</div>
					<div class="form-group">
						<label for="lastname" class="col-sm-2 control-label">Item</label>
						<select class="col-sm-4 selectpicker " id='itemSelect' data-live-search="false"
							 required>
                	       <option value=''>---Select---</option>
                    	<?php
                    	foreach ($items['all'] as $item_id=>$item) {
                    		echo "<option value='".$item_id."'>".$item."</option>";
                    	} ?>
                    	</select>
					</div>
					
					<div class="form-group">
                        <label class="col-sm-2 control-label" for="quantity">Quantity</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" id="quantity" min="1" placeholder="number ..." required>
                        </div>
                    </div>
					<div class="form-group">
						<label for="remarkText" class="col-sm-2 control-label">Remark:</label>
						<div class="col-sm-10">
						    <textarea class="form-control" name="excuseText" id="remarkText"></textarea>
						</div>  
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="btn_item_save" class="btn btn-primary">Save Changes</button>
			</div>
		</form>	
		</div>
		<!-- /.modal-content -->
	</div>
	<!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<!--Delete Modal Dialog -->
<div class="modal fade" id="deleteModal" role="dialog"	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		    <input type="hidden" id='row_id' value=''>
		    
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Delete Parmanently</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure You want to delete this?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" id="deleteConfirm">Delete</button>
			</div>
		</div>
	</div>
</div>
<?php } else {?>

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
						<label for="excuseText" class="control-label">Give an excuse to
							refuse this Purchase request:</label>
						<textarea class="form-control" name="excuseText" id="excuseText"></textarea>
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
<?php } ?>

<!--Confirm Modal Dialog -->
<div class="modal fade" id="confirmModal" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		    <input type="hidden" id='row_id' value=''>
		    
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>text text text</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>

<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />
	
<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />
	
<?php if(empty($vid)){?>	
<script type="text/javascript">
                    	
var items = <?php echo json_encode($items)?>;
var categories = <?php echo json_encode($categories)?>;

console.log(items);
console.log(categories);

var ledger_rows = new Object();

$(document).ready(function(){

    $("#categorySelect").change(function(){

    	var category_id = $(this).val();
    	refreshSelectPicker(category_id);        
    });

    $("#itemSelect").change(function(){

    	var item_id = $(this).val();

    	var CatId = CatID(item_id);
    	$("#categorySelect").selectpicker('val', CatId);
    	$("#categorySelect").selectpicker('refreash');    	
    });

    function CatID(item_id){
        for(key in items){
            
            if(key == 'all'){
                return "";
            }
            if(item_id in items[key]){
                return key;
            }
        }
        return "";
    }

    var cid = 0;
    var i = 0;
    $('#btn_item_save').on('click', function(){
        var itemsAll = items.all;
        console.log("i:"+i);

        var form = $(this).parents('form');
        var itm_id = form.find('#itemSelect').val();
        var qty = parseInt(form.find('#quantity').val());
        var remark = form.find('#remarkText').val();
        
    	var dataObj = new Object();
    	dataObj.item_id = itm_id;
    	dataObj.quantity = qty;
    	dataObj.remark = remark;
    	
    	ledger_rows[cid] = dataObj;

    	var row = "<tr class='rowTr'><td class='serial hidden-xs' data-title='Sn.'>"+ ++i +"</td>\
    	           <td data-title='Item Name'>"+ itemsAll[itm_id]+"</td>\
    	           <td data-title='Quantity'>"+ qty+"</td>\
    	           <td data-title='Remarks'>"+ remark+"</td>\
    	           <td data-title='Action'><a class='rowDelete btn btn-danger btn-xs' data-id='"+cid+"' >Delete</a></td>\
    	           </tr>";
	    
	    $('#tBody').append(row);   
    	cid++;
    	$('#addItemtoLedgerModal').modal('hide');
    	console.log(ledger_rows);
    });

    $('#addItemtoLedgerModal').on('hidden.bs.modal', function(){
        
        $(this).find('#categorySelect').val("");
        $(this).find('#categorySelect').selectpicker('refresh');
        refreshSelectPicker("");
        $(this).find('#quantity').val("");
        $(this).find('#remarkText').val("");
    });

    var rowTr;

    $(".container").on('click', "a.rowDelete", function(){
	    //console.log('tesr');
		rowTr = $(this).parents('tr.rowTr');
		var rowId = $(this).attr('data-id');
		
		$('#deleteModal').find("#row_id").val(rowId);
		$('#deleteModal').modal('show');	
    });

	$('#deleteConfirm').unbind("click").bind("click",function(){
		
		var rowId = $('#deleteModal').find("#row_id").val();
		delete ledger_rows[rowId];

		rowTr.remove();
    	$('#deleteModal').modal('hide');
    	
    	var serial = $('#tBody').find('.serial');
    	for(i=0; i<serial.length; i++) {
    	    $(serial[i]).text(i+1);
    	}

   	});

    $('#deleteConfirm').on('hidden.bs.modal', function(){
        
    	$('#deleteModal').find("#row_id").val("");
    	rowTr="";
    });

	$('#SendBtn').on("click",function(){
		$("body").append("<div class='loader'>Please wait&hellip;</div>");
        var aButton = $(this);        
        aButton.prop('disabled', true);
        
		$.ajax({
	  	    type:"POST",
	  	    url:"<?php echo base_url()?>requisition/add_req/",
	  	    data:{"dataObj" : JSON.stringify(ledger_rows)},
	  	    dataType: 'json', 	    
	  	    success:function(response) {
        		$('.loader').remove();
    			$('#confirmModal').find('div.modal-body p').text(response.msg);
    			$('#confirmModal').modal('show');
    			
	    	    if(response.status) {
 			    	$('#confirmModal').on('hidden.bs.modal', function(){

 			    		location.href = "<?php echo base_url();?>requisition/lists/";
 	 			    });		
					return;
	    	    } else {
	    	    	aButton.prop('disabled', false);
					return;
				}
	    	}      	    
	    });
   	});

});

function refreshSelectPicker(category_id){
	
	var selectedItems;
	if(category_id != ""){
    	selectedItems = items[category_id];
    }else{
    	selectedItems = items['all'];
    }

    $("#itemSelect").empty();
    var option = "<option value=''>---Select---</option>";	

    for( item_id in selectedItems) {   
                
        var item = selectedItems[item_id];
        if(item!=null) {
            option += "<option value='"+item_id+"'>"+item+"</option>";
            
        }
    }
    $("#itemSelect").append(option);
    $('#itemSelect').selectpicker('refresh');
}
</script>
<?php } else{ ?>
<script type="text/javascript">

var vid = "<?php echo $voucher['voucher_id']?>";

$(document).ready(function(){


	$('#approveRequisition').on('click', function(){

        var btn = $(this);        
        btn.prop('disabled', true);
        $('#refuseApprove').prop('disabled', true);
        
        $("body").append("<div class='loader'>Please wait&hellip;</div>");
    	$.ajax({
    		type: 'POST',
    		url: '<?php echo base_url()?>requisition/approve_req/'+vid,
    		data:{},
    		dataType:'json',
    		success: function(response){
    			$(".loader").remove();
    			$('#confirmModal').find('div.modal-body p').text(response.msg);
    			$('#confirmModal').modal('show');
 			    if(response.status){
 	 			    
 			    	$('#confirmModal').on('hidden.bs.modal', function(){
 			    		location.href = "<?php echo base_url();?>requisition/lists/";
 	 			    }); 	 			    	 			    
 	 			}else{
 	 				btn.prop('disabled', false);
 	 		        $('#refuseApprove').prop('disabled', false);
 	 	 		}	
        	}
    	});	
    });
    
    $('#refuseApprove').on('click', function(){
                   
        var url = '<?php echo base_url()?>requisition/refuse_approve_req/'+vid;
        var refuseBtn = $(this);
        var approveBtn = $('#approveRequisition');     
        refuseBtn.prop('disabled', true);
        approveBtn.prop('disabled', true);
        $('#refuseModal').modal('show');

        bindRefuseModal (url, refuseBtn, approveBtn);
    });

	$('#verifyRequisition').on('click', function(){

        var btn = $(this);        
        btn.prop('disabled', true);
        $('#refuseVerify').prop('disabled', true);
        
        $("body").append("<div class='loader'>Please wait&hellip;</div>");
    	$.ajax({
    		type: 'POST',
    		url: '<?php echo base_url()?>requisition/verify_req/'+vid,
    		data:{},
    		dataType:'json',
    		success: function(response){
    			$(".loader").remove();
    			$('#confirmModal').find('div.modal-body p').text(response.msg);
    			$('#confirmModal').modal('show');
 			    if(response.status){
 	 			    
 			    	$('#confirmModal').on('hidden.bs.modal', function(){
 			    		location.href = "<?php echo base_url();?>requisition/lists/";
 	 			    }); 	 			    	 			    
 	 			}else{
 	 				btn.prop('disabled', false);
 	 		        $('#refuseVerify').prop('disabled', false);
 	 	 		}	
        	}
    	});
    });

    $('#refuseVerify').on('click', function(){
        
        var url = '<?php echo base_url()?>requisition/refuse_verify_req/'+vid;
        var refuseBtn = $(this);
        var verifyBtn = $('#verifyRequisition');     
        refuseBtn.prop('disabled', true);
        verifyBtn.prop('disabled', true);
        $('#refuseModal').modal('show');

        bindRefuseModal (url, refuseBtn, verifyBtn);
    });

    function bindRefuseModal (url, refuseBtn, otherBtn){
        
        $('#refuseModal').find('.modal-footer #refuseOk').off('click').on('click', function () {
    		var excuse = $('#excuseText').val();
    				
    		if(excuse.length <= 6){
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
        			$('#confirmModal').find('div.modal-body p').text(response.msg);
        			$('#confirmModal').modal('show');
        			
     			    if(response.status){
     	 			    
     			    	$('#confirmModal').on('hidden.bs.modal', function(){
     			    		location.href = "<?php echo base_url();?>requisition/lists/";
     	 			    });
     	 			}else{
     	 				refuseBtn.prop('disabled', false);
     	 				otherBtn.prop('disabled', false);
     	 	 		}	
    			}
    		});

    	});

        $('#refuseModal').on('hidden.bs.modal', function () {
        	refuseBtn.prop('disabled', false);
        	otherBtn.prop('disabled', false);
        	$(this).find('#excuseText').val("");
        });
    }

});

</script>
<?php } ?>

