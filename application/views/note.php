<link href="<?php echo base_url();?>assets/css/main.css"
	type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/settings.css"
	type="text/css" rel="stylesheet" />
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />


<div class="box">
	<div class="box-header">
		<h3 class="box-title">Note List</h3>
		<button id="btn_add_note"
			class="btn btn-primary pull-right btn_add_right"
			data-target="#addNoteModal" data-toggle="modal" type="button">
			<span class="glyphicon glyphicon-plus"></span> Add New Note
		</button>

	</div>
	<div class="box-body">
		<div class='table-responsive'>
			<table
				class="col-xs-12 table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th>Date</th>
						<th>Subject</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
                <?php
                foreach ($notes as $obj) {
                    echo "<tr class='clickable-row'></td><td data-title='Date'>";
                    echo $obj->date;
                    echo "</td><td data-title='Subject'>";
                    echo $obj->subject;
                    echo "</td><td data-title='Action'>";
                    echo "<a class='noteView btn btn-primary btn-xs' data-id='" . $obj->id . "' >View</a> <a class='noteEdit btn btn-warning btn-xs' data-id='" . $obj->id . "' >Edit</a> <a class='holidayDelete btn btn-danger btn-xs' data-id='" . $obj->id . "' >Delete</a>";
                }
                ?>
        		</tbody>
			</table>
		</div>
	</div>
</div>



<!--View Modal Dialog -->
<div class="modal fade" id="viewModal" role="dialog"
	aria-labelledby="viewModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Note View</h4>
			</div>
			<div class="modal-body">
				<p id='pDate'></p>
				<p id='pSubject'></p>
				<p id='pNote'></p>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>
<!-- Add Department Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id='noteForm' class='form-horizontal'>
				<input type='hidden' id='noteId' value="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">Add a New Note</h4>
				</div>
				<div class="modal-body">
					<div class='row'>
						<div class='col-xs-12'>

							<div class="form-group">
								<label class="col-sm-1 control-label" for="dateNote">Date</label>
								<div class="col-sm-4">
									<input type="text" class="form-control" id="dateNote"
										name="dateNote" placeholder="yyyy-mm-dd" value="" required>
								</div>
							</div>
							<div class='col-xs-12'>
								<div class="form-group">
									<label for="subjectNote">Subject</label> <input type="text"
										placeholder="Enter ..." id="subjectNote" name='subjectNote'
										class="form-control">
								</div>
							</div>
							<div class='col-xs-12'>
								<div class="form-group">
									<label>Note</label>
									<textarea id="textNote" name="textNote" placeholder="Enter ..."
										class="form-control"></textarea>
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
				<p>Are you sure You want to delete this note ?</p>
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

var notes = <?php echo json_encode($notes)?>;
var deleteButton;

var addModal = $('#addNoteModal');

$(document).ready(function(){

	$('#dateNote').datepicker({
	 	format: 'yyyy-mm-dd'
	});
	$('#dateNote').on('changeDate', function(ev){
		$(this).datepicker('hide');
	});	

	$("#noteForm").validate({
		rules: {
			dateNote: {
				required: true,
				date: true,
			},
			subjectNote:{
				required: true,
				minlength:3,
			},
			textNote:{
				required: true,
				minlength:5,
			}		
				
		},
		submitHandler : function(event) {
			var id = $('#noteId').val();
			
			if(id == ""){
				var url = "<?php echo base_url()?>settings/add_note/";
			}else{
				var url = "<?php echo base_url()?>settings/update_note/"+id;
			}
       		var date = $('#dateNote').val();
       		var subject = $('#subjectNote').val();
       		var text = $('#textNote').val();
			$.ajax({
            	    type:"POST",
            	    url:url,
            	    data: {date:date, subject:subject, text:text},
            	    dataType:"json", 
            	    success:function(response) {
                	    
                  	    if(response.status) {
                  	    	location.reload();                 	      
                  	    } else {
                  	        alert(response.msg);
              				return;
              			}
                    	addModal.modal('hide');
                  	}      	    
            });
        }		
	});

	$('.noteEdit').on('click', function(e){
		e.stopPropagation();
		
		var noteId = $(this).attr('data-id');
		var date = notes[noteId]['date'];
		var subject = notes[noteId]['subject'];
		var note = notes[noteId]['note'];
		
		addModal.find('#noteId').val(noteId);
		addModal.find('#dateNote').val(date);
		addModal.find('#subjectNote').val(subject);
		addModal.find('#textNote').val(note);
		addModal.modal('show');	
	});

// 	$(".clickable-row ").click(function() {
// 		var noteId = $(this).attr('data-id');
// 		var date = notes[noteId]['date'];
// 		var subject = notes[noteId]['subject'];
// 		var note = notes[noteId]['note'];
// 	        window.document.location = $(this).data("href");
// 	});

	$(".clickable-row").click(function() {
		var noteId = $(this).find('a.noteView').attr('data-id');
		addTextModal(noteId); 
	});
	
	$(".noteView").click(function(e) {
		e.stopPropagation();
		var noteId = $(this).attr('data-id');
		addTextModal(noteId); 
		});
		
	function addTextModal(noteId) {

		var date = "<label>Date:</label>&nbsp;&nbsp;&nbsp;"+notes[noteId]['date'];
		var subject = "<label>Subject:</label>&nbsp;&nbsp;&nbsp;"+ notes[noteId]['subject'];
		
		var note = "<label>Note:</label>&nbsp;&nbsp;&nbsp;"+notes[noteId]['note'];

		$('#viewModal').find('.modal-body #pDate').html(date);
		$('#viewModal').find('.modal-body #pSubject').html(subject);
		$('#viewModal').find('.modal-body #pNote').html(note);
		
		$('#viewModal').modal('show');	
		
	    //window.document.location = $(this).data("href");
	}
	
	$('.holidayDelete').on('click', function(e){
		e.stopPropagation();
		$('#deleteModal').modal('show');
		deleteButton = $(this);		
	});

	$('#deleteConfirm').unbind("click").bind("click",function(){
   		bindDeleteEvent(deleteButton);
   	});
 	
    
});

function bindDeleteEvent(it){
	var noteId = $(it).attr('data-id');	
	
	$.ajax({
  	    type:"POST",
  	    url:"<?php echo base_url()?>settings/del_note/"+noteId,
  	    data:{},
  	    dataType:"json",
  	    success:function(response) {
    	    if(response.status) {
    	    	$(it).parents('tr').remove();
    	    	$('#deleteModal').modal('hide');
    	    } else {
    	        alert(response.msg);
				return;
			}
    	}      	    
    });
}

</script>