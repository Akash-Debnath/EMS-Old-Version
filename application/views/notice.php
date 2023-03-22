<?php
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>

<link rel="stylesheet"
	  href="<?php echo base_url();?>assets/css/pagination.css">

<div class='box'>
	<div class='box-header'>
		<?php if($uType=="A") { ?>
			<a href="<?php echo base_url();?>remark/add_notice/"
			   class="btn btn-info btn-sm pull-right btn_add_right"
			   data-toggle="tooltip" title="Adding new Notice"><span
					class="glyphicon glyphicon-plus"></span> Add New Notice</a>
		<?php } ?>
	</div>
	<div class='box-body'>
		<table class="table table-bordered">
			<thead>
			<tr>
				<th class="hidden-xs" style="width: 10px;">SL</th>
				<th class="">Subject</th>
				<th class="">Notice Date</th>
				<th class="">Notice No.</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$i= $offset;
			foreach ($notices as $notice){
				$read_by_array = explode(",",$notice->read_by);
				?>
				<tr class='clickable-row'
					data-href='<?php echo base_url()?>remark/notice_detail/<?php echo ++$i; ?>'
					style="cursor: pointer;">
					<td class="hidden-xs"><?php echo $i; ?></td>
					<td><a
							<?php
							if (!in_array($myInfo->userId,$read_by_array)) {
								echo 'class="makeBold"';
							}
							?>
							href="<?php echo base_url()?>remark/notice_detail/<?php echo $i; ?>"><?php echo $notice->subject; ?></a></td>
					<td class=""><?php echo $notice->notice_date; ?></td>
					<td class=""><?php echo $notice->id; ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="box-footer">
		<div id="pagination">
			<ul class="tsc_pagination">
				<!-- Show pagination links -->
				<?php echo $pagination_links;?>
			</ul>
		</div>
	</div>
</div>



<?php include 'footer.php'; ?>
<style>
	.makeBold {
		font-weight: bold;
	}
</style>
<script type="text/javascript">

	jQuery(document).ready(function() {

		$(".clickable-row").click(function() {
			window.document.location = $(this).data("href");
		});


	});

</script>