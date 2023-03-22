<?php 
?>
<link href="<?php echo base_url();?>assets/css/settings.css"
	type="text/css" rel="stylesheet" />


<div class='table-responsive'>	
    <table class="table-bordered table-striped table-condensed full-width">
    	<thead>
    		<tr>
    			<th>ID</th>
    			<th>Name</th>
    			<th>Designation</th>
    			<th>Grade</th>
    			<th>Job Description</th>
    		</tr>
    	</thead>
    	<tbody>
    	<?php foreach ($departments as $dept_code=>$dept_name) {
        if(isset($staffs[$dept_code]))  $array = $staffs[$dept_code];
        else continue;?>
            <tr>
    			<td class="table-heading" colspan="5"><?php echo $dept_name." (".count($array).")"?></td>
    		</tr>
    	<?php foreach ($array as $obj) { ?>
    		<tr>
    			<td data-title="ID"><?php echo $obj->emp_id?></td>
    			<td data-title="Name"><a
    				href="<?php echo base_url()?>user/detail/<?php echo $obj->emp_id?>"><?php echo $obj->name?></a></td>
    			<td data-title="Designation"><?php echo $obj->designation?></td>
    			<td data-title="Grade"><?php if(isset($grades[$obj->grade_id])) echo $grades[$obj->grade_id]; ?></td>
    			<td data-title="Job Description" class="text-center">
    			<?php if(isset($job_files[$obj->emp_id])){
    			    
    			    echo "<a href='".base_url()."remark/download_jdfile/".$obj->emp_id."/".$job_files[$obj->emp_id]->file_name."' class='btn btn-info  btn-xs'>Download</a>";
    			    
    			    if($isAdmin){
    			        echo " <span class='glyphicon glyphicon-chevron-right'></span> <a class='btn btn-warning btn-xs upload' data-id='".$obj->emp_id."' data-name='".$job_files[$obj->emp_id]->file_name."'>Change</a>";
    			    }
    			    
    			}
    			else{
    			   echo "Not Found ";
    			   
    			    if($isAdmin){
    			       echo "<span class='glyphicon glyphicon-chevron-right'></span> <a class='btn btn-primary btn-xs upload' data-id='".$obj->emp_id."'>Upload</a>";
    			    }
    			}
    
                ?>
    			</td>
    		</tr>
    	<?php } 
        } ?>
    	</tbody>
    </table>
 </div>



<!-- Modal Dialog -->
<div class="modal fade" id="addFileModal" role="dialog"
	aria-labelledby="addFileModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id='JobDescForm' enctype="multipart/form-data" method='post' action="<?php echo base_url()?>remark/add_job_desc">
			<input type='hidden' id='staffId' name='staffId' value="">
			<input type='hidden' id='prev_file' name='prev_file' value="">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">&times;</button>
					<h4 class="modal-title">Add Job Description File</h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="inputFile">File input</label> <input
							type="file" id="inputFile" name='inputFile' required>
						<p class="help-block">File must be in document format (docx, pdf, etc.).</p>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary" id="confirm">Done</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function() {
     
	$('.upload').on('click', function(){
		var clickElement = $(this);
		var empId = clickElement.attr('data-id');
		var addFileModal = $('#addFileModal');
		
		if (clickElement.text() == "Change") {
			var prev_name = clickElement.attr('data-name');
			addFileModal.find('form #prev_file').val(prev_name);
		}
		
		addFileModal.modal('show');
		addFileModal.find('form #staffId').val(empId);
	});	

});
</script>