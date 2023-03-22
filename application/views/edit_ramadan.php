<?php

//print_r($grade_list);
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/pagination.css">
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />
<style>

	.forScroll {
		height: 250px;
		overflow-y: auto;
	}

	.padding-5{
		padding: 4px 6px;
	}

	.no-border{
		border: 0;
	}

	.ul-padding-left{
		list-style-type: none;
		padding-left: 20px;
	}

	.m-b{
		margin-bottom: 15px;
	}
	.error{
		display: block;
		font-size: 11px;
		color:red;
	}
</style>

<div id="rmadan-div" style="width:100%">
	<?php
		if (count($ramadan)>0) {
			echo 	'<div class="alert alert-success" style="margin-left:0">
						<strong>Ramdan</strong> month goes on.
					</div>';
		}
	?>
</div>
<button type="button" data-toggle="modal" data-target="#setRamadanModal" class="pull-right btn btn-primary"><span class="fa fa-plus">&nbsp; Add Ramadan</span></button><br><hr style="border:none">
    <!-- <span>1</span> -->
<div class="box-body table-responsive">
	<table class="table text-center table-hover">
		<thead>
			<tr>
				<th rowspan='2'>Sl No</th>
				<th rowspan='2'>From</th>
				<th rowspan='2'>To</th>
				<th colspan='2'>Action</th>	
			</tr>
		</thead>
		<tbody>
			
			<?php 
				$j = $offset; 
				for($i=0;$i<count($get_ramadan_date);$i++){ 
			?>
				<tr>
					<td class="hidden-xs"><?= ++$j; ?></td>	
					<td><?= date_format(date_create($get_ramadan_date[$i]['stime']),'d-m-Y'); ?></td>
					<td><?= date_format(date_create($get_ramadan_date[$i]['etime']),'d-m-Y'); ?></td>
					<td><button class="btn btn-default" onclick="showEditModal('<?php echo $get_ramadan_date[$i]['id'] ?>','<?php echo date_format(date_create($get_ramadan_date[$i]['stime']),'d-m-Y'); ?>','<?php echo date_format(date_create($get_ramadan_date[$i]['etime']),'d-m-Y'); ?>')" data-toggle="tooltip" data-placement="top" title="click to Edit ramadan"><span class="fa fa-edit"></span></button></td>
					<td><button class="btn btn-danger" data-deleteid="<?= $get_ramadan_date[$i]['id'] ?>" id="deletebtn" data-toggle="modal" data-target="#deleteModal" data-toggle="tooltip" data-placement="left" title="click to Delete ramadan"><span class="fa fa-trash"></span></button></td>
				</tr>
			<?php }?>
		</tbody>
	</table>
</div>


    
	<div id="pagination">
		<ul class="tsc_pagination">
			<!-- Show pagination links -->
		<?php foreach ($links as $link) {
			echo "<li>". $link."</li>";
		} ?>
		</ul>
	</div>
		
    


<div class="modal fade" id="deleteModal" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Are you sure want to 'Delete' this item</h4>
        </div>
        <div class="modal-body text-right">
		<br>
			<form action="<?php echo base_url()?>ramadan/deleteRamadan" method="post">
				<input type="hidden" name="deletedId" id="deletedId">
				<button type="submit" class="btn btn-danger">Delete</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</form>
        </div>
        <!-- <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div> -->
      </div>
    </div>
</div>
<div class="modal fade" id="editModal" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Press 'Save' to confirm</h4>
		</div>
		<div class="modal-body">
			<div id="edit-error-div" style="display:none;margin-left:0;" class="alert alert-danger"></div>
			<form action="<?php echo base_url()?>ramadan/edit_ramadan_date" method="post">
				<input type="hidden" name="id" id="editedId">
				<!-- <div class="col-sm-3">
					<label for="stime">From</label>
				</div> -->
				<!-- <input type="date" class="" id="dateStime" name="stime" placeholder="yyyy-mm-dd" value="" required><br><br> -->
				<div class="form-group">
					<label class="col-sm-3 control-label" for="stime">From</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="dateStime"
							name="stime" placeholder="yyyy-mm-dd"
							value="###" required>
					</div>
				</div><br><br><br>
				<div class="form-group">
					<label class="col-sm-3 control-label" for="etime">To</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="dateEtime"
							name="etime" placeholder="yyyy-mm-dd"
							value="###" required>
					</div>
				</div><br>
		</div>
		<div class="modal-footer">
				<button type="button" class="btn btn-success" id="edit_ramadan_date">Save</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</form>
		</div>
		</div>
	</div>
</div>
<div class="modal fade" id="setRamadanModal" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">Set Ramadan Time</h4>
			</div>
			
			<div class="modal-body">
				<div class="form-group">
					<div id="error-div" style="display:none;margin-left:0;" class="alert alert-danger"></div>
				</div>
				
					<div class="form-group">
						<label class="col-sm-3 control-label" for="etime">From</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="dateFrom1"
								name="stime" placeholder="yyyy-mm-dd"
								value="<?php echo date_format(date_create(date("Y-m-d")),'d-m-Y') ?>">
						</div>
					</div><br><br><br>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="etime">To</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="dateTo1"
								name="etime" placeholder="yyyy-mm-dd"
								value="<?php echo date_format(date_create(date("Y-m-d")."+30 day"),'d-m-Y') ?>" required>
						</div>
					</div> <br>
			</div>
			<div class="modal-footer">
					<button type="button" class="btn btn-success" id="sendform1">Set time</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>





<?php include 'footer.php'; ?>



<link href="<?php echo base_url();?>assets/css/user.css"
	  type="text/css" rel="stylesheet" />
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />

<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>

<script src="https://apis.google.com/js/platform.js?onload=onLoadCallback" async defer></script>


<script type="text/javascript">


	var staffId = "<?php echo $emp_id?>";


	$.signIn = false;

	//console.log($.signIn );

	$(document).ready(function() {

		//Date Correction    
		$('#dateStime, #dateEtime').datepicker({
			format: 'dd-mm-yyyy'
		});
		$('#dateFrom1, #dateTo1').datepicker({
			format: 'dd-mm-yyyy'
		});

		setTimeout(function(){
				$('#rmadan-div').hide();
		}, 5000);

	});

</script>

<script>
		function showEditModal(id,stime,etime) {
			
			$('#editModal').modal('show');
			var id=$('#editedId').val(id);
			var stime=$('#dateStime').val(stime);
			var etime=$('#dateEtime').val(etime);
			
		}
		$('#deleteModal').on('show.bs.modal',function (event) {
			var button=$(event.relatedTarget);
			var deleted_id=button.data('deleteid');
			var modal=$(this);
			modal.find('.modal-body #deletedId').val(deleted_id);
		})
		$('#sendform1').on('click',function () {
			var stime=$('#dateFrom1').val();
			var etime=$('#dateTo1').val();
			$.ajax({
				type: "POST",
				url: '<?=base_url();?>ramadan/ramadan_set/',
				dataType: 'text',
				data: { stime: stime,etime:etime },
				success: function(data){
					if (data.includes("invalid")) {
						if(data.substr(data.length - 1)== 1){
							$('#error-div').show();
							$('#error-div').addClass('error');
							$('#error-div').html("Date already given");
						}else if(!data.includes("30")){
							$('#error-div').show();
							$('#error-div').addClass('error');
							$('#error-div').html("Date difference should be 30 days");
						}else{
							$('#error-div').show();
							$('#error-div').addClass('error');
							$('#error-div').html("Invalid date given");
						}
					}else{
						location.reload();
					}
				}
				
			});
		})
		$('#edit_ramadan_date').on('click',function () {
			var id=$('#editedId').val();
			var stime=$('#dateStime').val();
			var etime=$('#dateEtime').val();
			$.ajax({
				type: "POST",
				url: '<?=base_url();?>ramadan/edit_ramadan_date/',
				dataType: 'text',
				data: { id:id,stime: stime,etime:etime },
				success: function(data){
					console.log(data);
					if(data.includes("lessTime")){
						$('#edit-error-div').show();
						$('#edit-error-div').addClass('error');
						$('#edit-error-div').html("Date difference should be 29/30 days");
					}else if(data.includes("stimeLarger")){
						$('#edit-error-div').show();
						$('#edit-error-div').addClass('error');
						$('#edit-error-div').html("Starting larger than endtime");
					}else{
						location.reload();
					}
				}
				
			});
		})
		
</script>