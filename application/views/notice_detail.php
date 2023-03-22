<?php 
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>

<link rel="stylesheet"
	href="<?php echo base_url();?>assets/css/pagination.css">

<div class="panel panel-danger">
	<div class="panel-heading ">
		<h2 class="panel-title ">
			<strong>Subject:</strong>
		<?php echo $notices->subject; ?> 
		<span class="pull-right"><strong style="margin-right: 5px;">Date: <?php echo $notices->notice_date; ?></strong>
				<?php if($uType=="A") { ?>
				<span> |
					<a href="<?php echo base_url()?>remark/edit_notice/<?php echo $notices->id; ?>/<?php echo $page_number; ?>"  type="button" class="btn btn-warning btn-xs"
						id="btn_edit_notice"
						data-id="<?php //echo $obj->id; ?>"
						title="Editing a department" style="margin-left: 5px;" >
						<span class="glyphicon glyphicon-edit"></span> Edit
					</a>
					<button class="btn btn-xs btn-danger" type="button"
						data-toggle="modal" data-target="#confirmDeleteNotice">
						<i class="glyphicon glyphicon-trash"></i> Delete
					</button>
			     </span> 
			     <?php } ?>
			</span>
		</h2>
	</div>

	<div class="panel-body" style="min-height: 400px;">
		<p><?php echo $notices->notice; ?></p>
	</div>

	<div class="panel-footer" style="height: 100px;">
		<div id="pagination" style="margin: 0;">
			<ul class="tsc_pagination">
				<!-- Show pagination links -->
                <?php
                    echo $pagination_links;
                ?>  
            </ul>
		</div>
	</div>
</div>

<!-- Delete Modal -->

<!-- Modal Dialog -->
<div class="modal fade" id="confirmDeleteNotice" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this notice ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a href="<?php echo base_url()?>remark/delete_notice/<?php echo $notices->id; ?>" type="button" class="btn btn-danger">Delete</a>
			</div>
		</div>
	</div>
</div>


<?php include 'footer.php'; ?>

<link
	href="<?php echo base_url();?>assets/css/remark.css"
	type="text/css" rel="stylesheet" />

<style>
.panel {
	border: 1px solid #ddd;
}

.modal-open[style] {
    padding-right: 0px !important;
}
</style>