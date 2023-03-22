<?php
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>

<link rel="stylesheet"
	href="<?php echo base_url();?>assets/css/pagination.css">
	
<style>
.panel {
	border: 1px solid #ddd;
}
</style>	

<div class="panel panel-danger">

	<div class="panel-heading ">
	    <div class='row'>
	       <div class="col-xs-12">
	            <span class="panel-title">
	                <strong>Subject:</strong>
		            <?php echo $attaches->subject; ?> 
	            </span>
	           	<?php if($uType=="A") { ?>
                <div class='pull-right'>
                    <strong >Date: <?php echo $attaches->message_date; ?></strong> &nbsp
                    |
                    <a href="<?php echo base_url()?>remark/edit_attach/<?php echo $attaches->id; ?>/<?php echo $page_number; ?>"  type="button" class="btn btn-warning btn-xs"
    					id="btn_edit_notice"
    					data-id="<?php //echo $obj->id; ?>"
    					title="Editing a department" style="margin-left: 5px;" >
    					<span class="glyphicon glyphicon-edit"></span> Edit
    				</a>
    				<button class="btn btn-xs btn-danger" type="button"
    					data-toggle="modal" data-target="#confirmDeleteAttach">
    					<i class="glyphicon glyphicon-trash"></i> Delete
    				</button>
               </div>
               <?php } ?>
	       </div>

	    </div>

	</div>

	<div class="panel-body">
		<div style="height: 300px; overflow-y:auto; overflow-x:hidden;">
		<?php echo $attaches->message; ?></div>
		
		<div class = "fileBox">
		    <p><b>Attaced Files(<?php echo count($attFiles); ?>):</b></p>
		    <?php
		        $i = 0;
		        foreach ($attFiles as $file){		            
		            echo '<div class="col-sm-4 au_file">';
		            if(empty($file->original_name)) echo ++$i.'. Attached File<br>';
		            else echo ++$i.'. '.$file->original_name.'<br>';
		            echo '</div>';
		            echo '<div class="col-sm-1 au_file">';
		            echo '<a type ="button" href = "'.base_url().'remark/downloadAttach/'.$file->id.'" class="btn btn-xs btn-primary downFile" title="Download">Download</a>';
		            echo '</div>';
		            echo '<div class="clearfix"></div>';
		        }
		    ?>
		</div>
	</div>

	<div class="panel-footer" style="height: 100px;">
		<div id="pagination" style="margin: 0;">
			<ul class="tsc_pagination">
				<!-- Show pagination links -->
                <?php echo $pagination_links;?>  
            </ul>
		</div>
	</div>
</div>

<!-- Modal Dialog -->
<div class="modal fade" id="confirmDeleteAttach" role="dialog"
	aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
					aria-hidden="true">&times;</button>
				<h4 class="modal-title">Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this attachment ?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a href="<?php echo base_url()?>remark/del_attach/<?php echo $attaches->id; ?>" type="button" class="btn btn-danger">Delete</a>
			</div>
		</div>
	</div>
</div>


<?php include 'footer.php'; ?>

<link
	href="<?php echo base_url();?>assets/css/remark.css"
	type="text/css" rel="stylesheet" />

<style>
<!--
div.fileBox{
	display: inline-block;
	border-top:1px dashed #d5d1b6;
	width: 100%;
    text-align: left;
	vertical-align: bottom;
}
-->
</style>