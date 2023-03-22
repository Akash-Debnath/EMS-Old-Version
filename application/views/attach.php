<?php 
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>

<link rel="stylesheet"
	href="<?php echo base_url();?>assets/css/pagination.css">

<div class='box'>
	<div class='box-header'>
	    <?php if($uType=="A") { ?>
		<a href="<?php echo base_url();?>remark/add_attach/"
			class="btn btn-info btn-sm pull-right btn_add_right" data-toggle="tooltip"
			title="Adding new Attachment"><span
			class="glyphicon glyphicon-plus"></span> Add New Attachment</a>
		<?php } ?>	
	</div>
	<div class='box-body'>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th class="hidden-xs" style="width: 10px;"></th>
					<th class="">Subject</th>
					<th class="">From</th>
					<th class="">Date</th>
				</tr>
			</thead>
			<tbody>
                <?php 
                        $i= $offset;
                        foreach ($attachments as $attachment){
//                             if($uType!="A" && $uType != $attachment->message_to){
//                                 continue;
//                             }
                            $read_by_array = explode(",",$attachment->read_by);
                        ?>
                <tr class='clickable-row'
					data-href='<?php echo base_url()?>remark/attach_detail/<?php echo ++$i; ?>'
					style="cursor: pointer;">
					<td class="hidden-xs"><?php echo $i; ?></td>
					<td><a
						<?php 
                	if (!in_array($myInfo->userId,$read_by_array)) {
                	    echo 'class="makeBold"';
                	}
                	?>
						href="<?php echo base_url(); ?>remark/attach_detail/<?php echo $i; ?>">
                	<?php if($attachment->count>0) echo "<span class='glyphicon glyphicon-paperclip' title='".$attachment->count." file/s attached' style='color:#AC2832;'></span>"." "; 
                    echo $attachment->subject; 
                    ?></a></td>
					<td><a
						href="<?php echo base_url(); ?>user/detail/<?php echo $attachment->message_from; ?>"><?php echo $attachment->name; ?></a></td>
					<td class=""><?php echo $attachment->message_date; ?></td>
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
.makeBold{
	font-weight: bold;
}
</style>
<script type="text/javascript">

jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.document.location = $(this).data("href");
    });

});

</script>