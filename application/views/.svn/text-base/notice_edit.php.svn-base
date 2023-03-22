<?php
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>


<div class="panel panel-danger">
	<div class="panel-heading ">
		<h2 class="panel-title ">
			<strong>Update Notice</strong>
		</h2>
	</div>

	<div class="panel-body" style="min-height: 400px;">

		<form id="noticeForm" method="post" class="form-horizontal"
			action="<?php echo base_url(); ?>remark/updateNotice">
			<input type="hidden" id="notice_id" name="notice_id" value="<?php echo $notices->id; ?>">
			<input type="hidden" name="isEncrypted" value="<?php echo isset($notices->isEncrypted) ? $notices->isEncrypted : "Y"; ?>">
			<input type="hidden" id="page_number" name="page_number" value="<?php echo $page_number; ?>">
				
			<div class="form-group">
				<label for="noticeDate" class="col-sm-1 control-label">Date</label>
				<div class="col-sm-10">
					<input type="text" class="form-control " id="noticeDate"
						name="noticeDate" placeholder="YYYY-MM-DD"
						value="<?php echo $notices->notice_date; ?>" required
						style="width: 100px;">
				</div>
			</div>
			<div class="form-group">
				<label for="noticeSubject" class="control-label col-sm-1">Subject</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="noticeSubject"
						name="noticeSubject" placeholder="Subject"
						value="<?php echo $notices->subject; ?>" style="width: 70%;" required>
				</div>
			</div>
			<div class="form-group">
				<label for="noticeBody" class="col-sm-1 control-label">Notice</label>
				<div class="col-sm-10">
					<textarea class="form-control" id="noticeBody" name="noticeBody"
						rows="12" placeholder="write your notice here" required><?php echo $notices->notice; ?></textarea>
				</div>
			</div>
		</form>
	</div>

	<div class="panel-footer">
		<button type="submit" id="btn_submit"
			class="btn btn-primary pull-right">Done</button>
		<a type="button" href="<?php echo base_url()?>remark/notice_detail/<?php echo $page_number; ?>"
			class="btn btn-default pull-right" style="margin-right: 8px;" >Cancel</a>
		<div class="clearfix"></div>
	</div>
</div>


<?php include 'footer.php'; ?>

<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />

<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>

<style>
.panel {
	border: 1px solid #ddd;
}
</style>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/jquery.cleditor.css" />
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.cleditor.min.js"></script>
<link href="<?php echo base_url();?>assets/css/progress.css" type="text/css" rel="stylesheet"/>

<script type="text/javascript">

$( document ).ready(function() {

    $('#btn_submit').click( function(){
        $('#noticeForm').submit();
    });

    //$("#noticeDate").attr( 'readOnly' , 'true' );
    $("#noticeDate").keypress(function(event) {event.preventDefault();});

	$("#noticeDate").datepicker({
	    format: 'yyyy-mm-dd'
	});
	$('#noticeDate').on('changeDate', function(ev){
	    $(this).datepicker('hide');
	});	
	$("#noticeBody").cleditor();
	//$("#noticeBody").cleditor({width:"600px", height:"400px"})[0].focus();

	$("#btn_submit").mousedown(function(){
	    $("body").append("<div class='loader'>Please wait&hellip;</div>");
	});

	var validator = $("#noticeForm").validate({
		invalidHandler: function() {
		    if(validator.numberOfInvalids()) {
		        $(".loader").remove();
		    }
		 },
//		ignore: [],
		rules: {
			noticeSubject: {
				required: true,
				minlength: 6
			},
			noticeBody: {
				required: true,
				minlength:10
			},
			noticeDate: {
			    required: true,
			    date: true
			}
		},
// 		errorPlacement: function(error, element) {
// 	        if (element.attr("name") == "grade") {

// 	        	error.insertAfter($("#grade").parent().find("div.bootstrap-select")); 
// 	        }else if (element.attr("name") == "dept_code") {
		        
// 	        	error.insertAfter($("#dept_code").parent().find("div.bootstrap-select"));
// 			}
// 	    },
		messages: {
			noticeSubject: {
				required: "Please enter notice's subject",
				minlength: "Notice subject must consist of at least 6 characters"
			},
			noticeBody: {
				required: "Please enter a employee ID",
				minlength: "Notice Body must consist of at least 10 characters"
			}
		},
	});
});
</script>
<style>
<!--
.error{
    color:#FF0000;
}
-->
</style>

