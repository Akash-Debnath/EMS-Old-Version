<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
		<div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Category List</h3>              
                <button id="btn_add_category" class="btn btn-primary pull-right btn_add_right" data-target="#addCategoryModal" data-toggle="modal"  type="button">
		        <span class="glyphicon glyphicon-plus"></span> Add Category
	            </button>	           
            </div>
            <div class="box-body">
                <div class='table-responsive'>
                    <table class="col-xs-12 table-bordered table-striped table-condensed">
                		<thead>
                			<tr>
                				<th class='hidden-xs' >Sn.</th>
                				<th>Category</th>
                				<th>Description</th>
                				<th>Action</th>
                			</tr>
                		</thead>
                		<tbody>
                		<?php
                		$i = 0;
                		foreach ($categories as $obj){
                		    echo "<tr><td class='serial hidden-xs' data-title='Sn.'>";
                		    echo ++$i;
                		    echo "</td><td data-title='Category'>";
                		    echo $obj->category;
                		    echo "</td><td data-title='Description'>";
                		    echo $obj->description;
                		    echo "</td><td data-title='Action'>";
                		    echo "<a class='categoryEdit btn btn-warning btn-xs' data-id='".$obj->category_id."' >Edit</a> <a class='categoryDelete btn btn-danger btn-xs' data-id='".$obj->category_id."' >Delete</a>";
                		}?>
                		</tbody>
            	    </table>
                </div>
            </div>
		</div>
	</div>	
</div>



<!-- Add Facility Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1"
	role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title">Add a New Category</h4>
			</div>
			<div class="modal-body">
				<form id="categoryForm" method="post">
				    <input type='hidden' id='categoryId' value="">
					<div class="form-group">
						<label for="category_name" class="control-label">Category:</label>
						<input type="text" class="form-control" id="category_name"
							name="category_name" placeholder="Category name ..." required>
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
				<button type="button" id="btn_category_save" class="btn btn-primary">Save Changes</button>
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
		<form id="deleteForm" method="post" action="<?php echo base_url()?>requisition/del_category">
		    <input type="hidden" id='del_category_id' name="del_category_id" value=''>
		    
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

<script>
$(document).ready(function(){

	$('#btn_category_save').on('click', function(){
		
		var category = $("#category_name").val();
		if(category.length <= 2) {
			alert('Enter a Meaningfull Category Name.');
			$('#category_name').focus();
			return;
		}	

		var categoryId = $('#categoryId').val();
		var url;
		if(categoryId == ""){
			url = "<?php echo base_url()?>requisition/add_category/";
		}else{
			url = "<?php echo base_url()?>requisition/update_category/"+categoryId;
		}

		$('#categoryForm').attr('action', url).submit();

	});    

	$('.categoryEdit').on('click', function(){

		var categoryId = $(this).attr('data-id');
				
		var parentRow = $(this).parents('tr');		
		var category  =  parentRow.find("td[data-title='Category']").text();
		var description = parentRow.find("td[data-title='Description']").text();


		console.log(description);
		
		$('#addCategoryModal').find('#categoryId').val(categoryId);
		$('#addCategoryModal').find('#category_name').val(category);
		$('#addCategoryModal').find('#description').val(description);
		$('#addCategoryModal').modal('show');
		//editButton = $(this);		
	});

	$('.categoryDelete').on('click', function(){
		var categoryId = $(this).attr('data-id');
		
		$('#deleteModal').find("#del_category_id").val(categoryId);
		$('#deleteModal').modal('show');	
	});

	$('#addCategoryModal').on('hidden.bs.modal', function(){
		
		$(this).find('#categoryId').val('');
		$(this).find('#category_name').val('');
		$(this).find('#description').val('');
	});
	$('#deleteModal').on('hidden.bs.modal', function(){		
		$(this).find('#del_category_id').val('');
	});
    
});

</script>