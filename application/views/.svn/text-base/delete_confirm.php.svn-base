<!-- Modal Dialog -->
<div class="modal fade" id="confirmDelete" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Delete Parmanently</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure about this ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" id="confirm">Delete</button>
			</div>
		</div>
	</div>
</div>

<!-- Dialog show event handler -->
<script type="text/javascript">
var id;
var model;
var name;
var tr;
  $('#confirmDelete').on('show.bs.modal', function (e) {
      $message = $(e.relatedTarget).attr('data-message');
      tr = $(e.relatedTarget).parents('tr');
      id = $(e.relatedTarget).attr('data-id');
      name = $(e.relatedTarget).attr('data-name');
      model = $(e.relatedTarget).attr('data-model');
      $(this).find('.modal-body p').text($message);
      $title = $(e.relatedTarget).attr('data-title');
      $(this).find('.modal-title').text($title);

      // Pass form reference to modal for submission on yes/ok
      var form = $(e.relatedTarget).closest('form');
      $(this).find('.modal-footer #confirm').data('form', form);
  });

  //Form confirm (yes/ok) handler, submits form
  $('#confirmDelete').find('.modal-footer #confirm').on('click', function(){
      //$(this).data('form').submit();
      $.ajax({
  	    type:"POST",
  	    url:"<?php echo base_url().'settings/delete/'; ?>"+id,
  	    data:{model:model, name:name},
  	    dataType:"json",
  	    success:function(response) {
  	    	$('.modal').modal('hide');
    	    if(response.status) {
    	    	$(tr).remove();
    	    	var serial = $('table').find('.serial');
    	    	for(i=0; i<serial.length; i++) {
    	    	    $(serial[i]).text(i+1);
    	    	}
    	    } else {
    	        alert("Couldn't delete?");
    	    }
  	    }
      });
  });
</script>
