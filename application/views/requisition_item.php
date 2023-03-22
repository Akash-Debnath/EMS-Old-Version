
<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
		<div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Item List</h3>              
                <button id="btn_add_item" class="btn btn-primary pull-right btn_add_right" data-target="#addItemModal" data-toggle="modal"  type="button">
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
                				<th>Category</th>
                				<th>Description</th>
                				<th>Action</th>
                			</tr>
                		</thead>
                		<tbody>
                		<?php
                		$i = 0;
                		foreach ($items as $obj){
                		    echo "<tr><td class='serial hidden-xs' data-title='Sn.'>";
                		    echo ++$i;
                		    echo "</td><td data-title='Item'>";
                		    echo $obj->item;
                		    echo "</td><td data-title='Category'>";
                		    echo $obj->category;
                		    echo "</td><td data-title='Description'>";
                		    echo $obj->description;
                		    echo "</td><td data-title='Action'>";
                		    echo "<a class='itemEdit btn btn-warning btn-xs' data-id='".$obj->item_id."' >Edit</a> <a class='itemDelete btn btn-danger btn-xs' data-id='".$obj->item_id."' >Delete</a>";
                		}?>
                		</tbody>
            	    </table>
                </div>
            </div>
		</div>
	</div>	
</div>


<!-- Add Facility Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Add a New Item</h4>
			</div>
			<div class="modal-body">
				<form id="itemForm" method="post">
				    <input type='hidden' id='itemId' value="">
				    <div class="form-group">
						<label for="lastname" class="control-label">Category:</label>

						<select name="categorySelect" class="selectpicker "	id='categorySelect' data-live-search="false"
							style='width: 200px;' required>
                	       <option value=''>---Select---</option>
                    	<?php foreach ($categories as $obj) {
                    		echo "<option value='".$obj->category_id."'>".$obj->category."</option>";
                    	} ?>
                    	</select>
					</div>
					<div class="form-group">
						<label for="item_name" class="control-label">Item:</label>
						<input type="text" class="form-control" id="item_name"
							name="item_name" placeholder="Item name ..." required>
					</div>
					<div class="form-group">
						<label for="description" class="control-label">Description:</label>
						<textarea rows="3" cols="" class="form-control" id="description" name = "description"
							placeholder="Add related description ..." style="max-width: 100%;"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" id="btn_item_save" class="btn btn-primary">Save Changes</button>
			</div>
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
		<form id="deleteForm" method="post" action="<?php echo base_url()?>requisition/del_item">
		    <input type="hidden" id='del_item_id' name="del_item_id" value=''>
		    
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
				<button type="submit" class="btn btn-danger" id="deleteConfirm">Delete</button>
			</div>
		</form>	
		</div>
	</div>
</div>


<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />

<script>

var categories = <?php echo json_encode($categories)?>; 

$(document).ready(function(){


	$('#btn_item_save').on('click', function(){
		
		var item = $("#item_name").val();
		if(item.length <= 2) {
			alert('Enter a Meaningfull Item Name.');
			$('#item_name').focus();
			return;
		}
		var category = $("#categorySelect").val();

		if(category == null){
			alert('Please Select Category Type.');
			$('#categorySelect').focus();
			return;
		}	

		var url;
		var itemId = $('#itemId').val();
		
		if(itemId == ""){
			url = "<?php echo base_url()?>requisition/add_item/";
		}else{
			url = "<?php echo base_url()?>requisition/update_item/"+itemId;
		}

		$('#itemForm').attr('action', url).submit();

	});    

	$('.itemEdit').on('click', function(){

		var itemId = $(this).attr('data-id');
				
		var parentRow = $(this).parents('tr');		
		var item  =  parentRow.find("td[data-title='Item']").text();
		var category = parentRow.find("td[data-title='Category']").text();
		var description = parentRow.find("td[data-title='Description']").text();

		var CatId = getCategoryId(category);
		
		$('#addItemModal').find('#itemId').val(itemId);
		$('#addItemModal').find('#item_name').val(item);
		$('#addItemModal').find('#description').val(description);
		$('#addItemModal').find("#categorySelect option[value='"+CatId+"']").prop('selected', true);
		$('#addItemModal').find("#categorySelect option[value='"+CatId+"']").attr('selected', 'selected');
		$('#addItemModal').find('.selectpicker').selectpicker('refresh');
		$('#addItemModal').modal('show');
		//editButton = $(this);		
	});

	$('.itemDelete').on('click', function(){
		var itemId = $(this).attr('data-id');
		
		$('#deleteModal').find("#del_item_id").val(itemId);
		$('#deleteModal').modal('show');	
	});

	$('#addItemModal').on('hidden.bs.modal', function(){
		
		$(this).find('#itemId').val('');
		$(this).find('#item_name').val('');
		$(this).find('#description').val('');
	});
	
	$('#deleteModal').on('hidden.bs.modal', function(){		
		$(this).find('#del_item_id').val('');
	});
    
});

function getCategoryId(cat){
	for(key in categories){
		var obj = categories[key];
		if(obj.category == cat)
			return obj.category_id;
	}

	return "";
}

</script>