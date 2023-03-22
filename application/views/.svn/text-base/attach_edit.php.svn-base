<?php
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>

<div class="panel panel-danger">
	<div class="panel-heading ">
		<h2 class="panel-title ">
			<strong>Update Attachment</strong>
		</h2>
	</div>

	<div class="panel-body" style="min-height: 400px;">

		<form id="attachForm" enctype="multipart/form-data" method="post"
			class="form-horizontal"
			action="<?php echo base_url(); ?>remark/updateAttach">
			<input type="hidden" value="<?php echo isset($notices->is_encrypted) ? $notices->is_encrypted : "Y"; ?>" name="is_encrypted">
			<input type="hidden" id="attach_id" name="attach_id" value="<?php echo $attaches->id; ?>">
			<input type="hidden" id="page_number" name="page_number" value="<?php echo $page_number; ?>">

			<div class="form-group">
				<label for="AttachDate" class="col-sm-1 control-label">Date</label>
				<div class="col-sm-10">
					<input type="text" class="form-control " id="attachDate"
						name="attachDate" placeholder="YYYY-MM-DD"
						value="<?php echo $attaches->message_date; ?>" required
						style="width: 100px;">
				</div>
			</div>

			<div class="form-group">
				<label for="lastname" class="col-sm-1 control-label">To</label>
				<div class="col-sm-10">
				
                    <select name="attachTo" id='attachTo' class="selectpicker selectAtleastOne"
        				data-live-search="false" style='width: 200px;'>
        				<option value=''>--Select--</option>
        			<?php foreach ($attach_to as $key=>$value) {
        				if($attaches->message_to == $key)
        					echo "<option value='".$key."' selected='selected'>".$value."</option>";
        				else 
        					echo "<option value='".$key."'>".$value."</option>";
        			} ?>
        		    </select>
        		    
    		        <span id ='andSign' class=''>&</span>
    		    
        		    <select name="customTo[]" id='customToId' class="selectpicker selectAtleastOne" data-live-search="true" style='width: 200px;' multiple>
        		    
            		<?php
            		    $custom_recipient = explode(',', $attaches->custom_recipient);
            		    print_r($custom_recipient);
            		    foreach ($staff_array as $obj) {
            			    if( in_array($obj->emp_id, $custom_recipient) )
            				    echo "<option value='".$obj->emp_id."' selected='selected' >".$obj->emp_id." - ".$obj->name."</option>";
            			    else
            			        echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ".$obj->name."</option>";
            		    } ?>
                    </select>     		    

				</div>
			</div>

			<div class="form-group">
				<label for="attachSubject" class="control-label col-sm-1">Subject</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="attachSubject"
						name="attachSubject" placeholder="Subject"
						value="<?php echo $attaches->subject; ?>" style="width: 70%;"
						required>
				</div>
			</div>
			<div class="form-group">
				<label for="attachBody" class="col-sm-1 control-label">Message</label>
				<div class="col-sm-10">
					<textarea class="form-control" id="attachBody" name="attachBody"
						rows="12" placeholder="write your message here" required><?php echo $attaches->message; ?></textarea>
				</div>
			</div>
			<?php if ($attach_update && count($attFiles) > 0) {
			$i = 0;
			?>
			<div class="form-group">
				<label for="ufile1" class="col-sm-1 control-label">Uploaded File</label>
				<div class="col-sm-5">
				    <?php foreach ($attFiles as $file) {
				        echo '<div class="col-sm-8 au_file">';
				        if(empty($file->original_name)) echo ++$i.'. Attached File<br>';
				        else echo ++$i.'. '.$file->original_name.'<br>';
				        echo '</div>';
				        echo '<div class="col-sm-4 au_file">';
				        echo '<button type ="button" class="btn btn-xs btn-danger delFile" title="Delete" fid="'.$file->id.'">Remove</button>';
				        echo '</div>';    				    
				        echo '<div class="clearfix"></div>';
				    }    
				    ?>	
				    <input type="hidden" id='removedFile' name='removedFile' />
				</div>
			</div>
			<?php } ?>

			<div class="form-group">
				<label for="ufile1" class="col-sm-1 control-label">Upload File</label>
				<div class="col-sm-5">
					<div class="col-sm-6" style="padding: 0;">
						<input type="file" multiple="multiple" id="ufile1" name="ufile1"
							accept="application/zip,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,
						application/vnd.ms-excel,application/vnd.ms-powerpoint,image/jpg,image/jpeg,image/png,image/gif"
							style="max-width: 235px;">
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6" style="padding: 0;">
						<input type="file" multiple="multiple" id="ufile2" name="ufile2"
							accept="application/zip,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,
						application/vnd.ms-excel,application/vnd.ms-powerpoint,image/jpg,image/jpeg,image/png,image/gif"
							style="max-width: 235px; display: none;">
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6" style="padding: 0;">
						<input type="file" multiple="multiple" id="ufile3" name="ufile3"
							accept="application/zip,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,
						application/vnd.ms-excel,application/vnd.ms-powerpoint,image/jpg,image/jpeg,image/png,image/gif"
							style="max-width: 235px; display: none;">
					</div>
					<div class="clearfix"></div>
					<div class="col-sm-6" style="padding: 0;">
						<input type="file" multiple="multiple" id="ufile4" name="ufile4"
							accept="application/zip,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,
						application/vnd.ms-excel,application/vnd.ms-powerpoint,image/jpg,image/jpeg,image/png,image/gif"
							style="max-width: 235px; display: none;">
					</div>
					<a href="#" id='btnMore'>More&hellip;</a>
				</div>
			</div>
		</form>
	</div>

	<div class="panel-footer">
		<button type="submit" id="btn_submit"
			class="btn btn-primary pull-right">Done</button>
		<a type="button"
			href="<?php echo base_url()?>remark/attach_detail/<?php echo $page_number; ?>"
			class="btn btn-default pull-right" style="margin-right: 8px;">Cancel</a>
		<div class="clearfix"></div>
	</div>
</div>


<?php include 'footer.php'; ?>


<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/remark.css"
	type="text/css" rel="stylesheet" />
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />


<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>

<style>
.panel {
	border: 1px solid #ddd;
}
</style>

<link rel="stylesheet" type="text/css"
	href="<?php echo base_url();?>assets/css/jquery.cleditor.css" />
<script type="text/javascript"
	src="<?php echo base_url();?>assets/js/jquery.cleditor.min.js"></script>
<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />

<script type="text/javascript">

$( document ).ready(function() {

	var fcount = 1;
	$("#btnMore").click(function(){
		fcount++;
	    $("#ufile"+fcount).show();
	    if(fcount==4){
		    $(this).hide();
	    }    
	});

	var r_files = new Array();
	var i= 0;
	$(".delFile").click(function(){
		//$(this).parent().hide();
		if(confirm("Are you sure?")) {
			 var fid = $(this).attr("fid");
			 r_files[i] = fid;
			 $("#removedFile").val(r_files.join(","));
			 i++;
			 
			$(this).parent().prev().hide();
			$(this).parent().hide();			
		}
	});

    $('#btn_submit').click( function(){

        if( $('#attachForm').valid() ) {
            
        	$('#attachForm').submit();
        }
    });

    //$("#attachDate").attr( 'readOnly' , 'true' );
    $("#attachDate").keypress(function(event) {event.preventDefault();});

	$("#attachDate").datepicker({
	    format: 'yyyy-mm-dd'
	});
	$('#attachDate').on('changeDate', function(ev){
	    $(this).datepicker('hide');
	});	
	$("#attachBody").cleditor();
	//$("#attachBody").cleditor({width:"600px", height:"400px"})[0].focus();

	$("#btn_submit").mousedown(function(){
	    $("body").append("<div class='loader'>Please wait&hellip;</div>");
	});

	var validator = $("#attachForm").validate({
		
		invalidHandler: function() {
			//console.log('errrorrrr');
		    if(validator.numberOfInvalids()) {
		        $(".loader").remove();
		    }
		},
		ignore: [],
		rules: {
			attachSubject: {
				required: true,
				minlength: 6
			},
			attachBody: {
				required: true,
				minlength:10
			},
			attachDate: {
			    required: true,
			    date: true
			},			
 			attachTo: {
 			    require_from_group: [1, ".selectAtleastOne"]
			},
			customTo: {
				require_from_group: [1, ".selectAtleastOne"]
			}
		},
		messages: {
			attachSubject: {
				required: "Please enter attach's subject",
				minlength: "attach subject must consist of at least 6 characters"
			},
			attachBody: {
				required: "Please enter a employee ID",
				minlength: "attach Body must consist of at least 10 characters"
			}
		},
		errorPlacement: function(error, element) {
	        if(element.attr("name") == "attachTo" || element.attr("name") == "customTo[]") {
	            error.insertAfter(element.parent().find("div.bootstrap-select").last());
	        } else {
	            error.insertAfter(element);
	        }
		}
	});

	$('#attachForm').validate().settings.ignore = ':not(select:hidden, input:visible, textarea:visible)';
	
});
</script>
