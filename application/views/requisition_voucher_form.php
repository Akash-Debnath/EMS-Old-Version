<div class="container">
	<div class="row">
		<div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">PURCHASE ORDER FORM</h3>
                <button id="purchase_item" class="btn btn-primary pull-right btn_add_right" data-target="#addItemtoLedgerModal" data-toggle="modal"  type="button">
		        <span class="glyphicon glyphicon-plus"></span> Add Item
	            </button>
            </div>
            <div class="box-body">
                <div class='table-responsive'>
                    <table class="col-xs-12 table-bordered table-striped table-condensed">
                		<thead>
                			<tr>
                				<th class='hidden-xs' >Sn.</th>
                				<th>Item</th>
                				<th>Quantity</th>
                				<th>Unit Price</th>
                				<th>Total Price</th>
                				<th>Action</th>
                			</tr>
                		</thead>
                		<tbody id='tBody'>
                		</tbody>
            	    </table>
                </div>
            </div>
            <div class="box-footer">
    			<button id="submitPurchase" class="btn btn-primary"> Submit Form</button>
    		</div>
		</div>
    </div>	
	<div class="row">
		<p class="bg-success" style="padding: 10px; margin-top: 20px">
			<small><a href="" target="_blank">Link</a> to EMS</small>
		</p>
	</div>
</div>

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
                        <label class="col-sm-2 control-label" for="uPrice">Unit Price</label>
                        <div class="col-sm-4">
                        <input type="number" class="form-control" id="uPrice"  min="0" placeholder="Unit Price ...">
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
				<p>Are you sure You want to delete this holiday ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" id="deleteConfirm">Delete</button>
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
	
<script type="text/javascript">
                    	
var items = <?php echo json_encode($items)?>; 
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
        var uprice = parseFloat(form.find('#uPrice').val());
        var tprice = qty*uprice;
        
    	var dataObj = new Object();
    	dataObj.item_id = itm_id;
    	dataObj.quantity = qty;
    	dataObj.unit_price = uprice;
    	dataObj.total_price = tprice;
    	
    	ledger_rows[cid] = dataObj;

    	var row = "<tr class='rowTr'><td class='serial hidden-xs' data-title='Sn.'>"+ ++i +"</td>\
    	           <td data-title='Item'>"+ itemsAll[itm_id]+"</td>\
    	           <td data-title='Quantity'>"+ qty+"</td>\
    	           <td data-title='Unit Price'>"+ uprice+"</td>\
    	           <td data-title='Total Price'>"+ tprice+"</td>\
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
        $(this).find('#uPrice').val("");
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

	$('#submitPurchase').on("click",function(){

		console.log(ledger_rows);
		
		if($.isEmptyObject(ledger_rows))
			return;

		$.ajax({
	  	    type:"POST",
	  	    url:"<?php echo base_url()?>requisition/add_purchase/",
	  	    data:{"dataObj" : JSON.stringify(ledger_rows)},
	  	    dataType: 'json', 	    
	  	    success:function(response) {
	    	    if(response.status) {
	    	        alert(response.msg);	    	        
	    	        location.href = "<?php echo base_url();?>requisition/voucher_form/"+response.vid;
					return;
	    	    } else {
	    	        alert(response.msg);
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