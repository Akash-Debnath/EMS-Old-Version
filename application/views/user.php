<?php 
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/pagination.css">

<div class="row">
	<label for="select_employee" class="col-sm-2">Search Employee</label>
	<div class="col-sm-8">
		<select name="dept_code" class="selectpicker" id='select_employee'
			data-live-search="true" >
		<?php echo "<option value=''>Search by ID or name ...</option>"; ?>
		<?php foreach ($allUsers as $user) {
				echo "<option value='".$user->emp_id."'>".$user->emp_id." - ".$user->name."</option>";	
	    } ?>
	    </select>
	</div>
	
	<?php if($uType=="A") { ?>
    <div class="col-sm-2 btnAddEmp">
    	<a
    		href="<?php echo base_url();?>user/add/<?php echo $dept_code_to_add;?>"
    		class="btn btn-primary btn-sm pull-right" data-toggle="tooltip"
    		title="Adding new Employee"><span class="glyphicon glyphicon-plus"></span>
    		Add New Employee</a>
    </div>
    <?php } ?>
</div>
<div class="clearfix hightfixed"></div>


<div class="box-body table-responsive">
	<table class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th class="hidden-xs sn"></th>
				<th class="hidden-xs">ID</th>
				<th><span class="hidden-xs">Name</span> <span class="visible-xs">Staff</span>
				</th>
				<th class="hidden-xs">Department</th>
				<th class="hidden-xs">Designation</th>
				<th class="hidden-xs">Status</th>
				<th class="hidden-xs">Joining Date</th>
			</tr>
		</thead>
		<tbody>
        <?php
        $i = $offset;
        foreach ($users as $user){ 
        ?>
            <tr>
				<td class="hidden-xs"><?php echo ++$i; ?></td>
				<td class="hidden-xs"><?php echo $user->emp_id; ?></td>
				<td><span class="hidden-xs"><a
						href="<?php echo base_url();?>user/detail/<?php echo $user->emp_id; ?>"><?php echo $user->name; ?></a></span>
					<div class="visible-xs">
						<table>
							<tr>
								<td><b>ID</b></td>
								<td width='10'><b>:</b></td>
								<td><?php echo $user->emp_id; ?></td>
							</tr>
							<tr>
								<td><b>Name</b></td>
								<td><b>:</b></td>
								<td><a
									href="<?php echo base_url();?>user/detail/<?php echo $user->emp_id; ?>"><?php echo $user->name; ?></a></td>
							</tr>
							<tr>
								<td><b>Department</b></td>
								<td><b>:</b></td>
								<td><?php echo $user->dept_name; ?></td>
							</tr>
							<tr>
								<td><b>Designation</b></td>
								<td><b>:</b></td>
								<td><?php echo $user->designation; ?></td>
							</tr>
							<tr>
								<td><b>Status</b></td>
								<td><b>:</b></td>
								<td><?php echo $status_array[$user->status]; ?></td>
							</tr>
							<tr>
								<td><b>Joining Date</b></td>
								<td><b>:</b></td>
								<td><?php echo $user->jdate; ?></td>
							</tr>
						</table>
					</div></td>
				<td class="hidden-xs"><?php echo $user->dept_name; ?></td>
				<td class="hidden-xs"><?php echo $user->designation; ?></td>
				<td class="hidden-xs"><?php echo $status_array[$user->status]; ?></td>
				<td class="hidden-xs"><?php echo $user->jdate; ?></td>
			</tr>
            <?php
            }
            
            ?>
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

<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />
<link
	href="<?php echo base_url();?>assets/css/user.css"
	type="text/css" rel="stylesheet" />
	
<?php include 'footer.php'; ?>

<script>
$(function(){
	$("#select_employee").change(function(){
		var emp_id = $(this).val();
		location.href = "<?php echo base_url();?>user/detail/"+emp_id;
	});

});

</script>
