<?php 
//$title = "Employee";
//$sub_title = "Employee List";
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/pagination.css">

<style>
/* table td:last-child, th:last-child { */
/*     color: red; */
/* }  */
</style>

<div class="table-responsive">
	<table class="table table-bordered table-striped table-hover">
		<thead>
			<tr>
				<th class="hidden-xs" style="width:10px;"></th>
				<th class="hidden-xs">ID</th>
				<th>
					<span class="hidden-xs">Name</span>
					<span class="visible-xs">Staff</span>
				</th>
				<th class="hidden-xs">Department</th>
				<th class="hidden-xs">Designation</th>
				<th class="hidden-xs">Joining Date</th>
				<th class="hidden-xs">Resignation Date</th>
			</tr>
		</thead>
		<tbody>
            <?php 
            $i=0;
            foreach ($users as $user){ 
            ?>
            <tr>
            	<td class="hidden-xs"><?php echo ++$i; ?></td>
            	<td class="hidden-xs"><?php echo $user->emp_id; ?></td>
            	<td>
            		<span class="hidden-xs"><a href="<?php echo base_url();?>user/detail/<?php echo $user->emp_id; ?>"><?php echo $user->name; ?></a></span> 
            		<div class="visible-xs">
            			<table>
            			<tr><td><b>ID</b></td><td width='10'><b>:</b></td><td><?php echo $user->emp_id; ?></td></tr>
            			<tr><td><b>Name</b></td><td><b>:</b></td><td><a href="<?php echo base_url();?>user/detail/<?php echo $user->emp_id; ?>"><?php echo $user->name; ?></a></td></tr>
            			<tr><td><b>Department</b></td><td><b>:</b></td><td><?php echo $user->dept_name; ?></td></tr>
            			<tr><td><b>Designation</b></td><td><b>:</b></td><td><?php echo $user->designation; ?></td></tr>
            			<tr><td><b>Joining Date</b></td><td><b>:</b></td><td><?php echo $user->jdate; ?></td></tr>
            			<tr><td><b>Resignation Date</b></td><td><b>:</b></td><td><?php echo $user->resignation_date; ?></td></tr>
            			</table>
            		</div>
            	</td>
            	<td class="hidden-xs"><?php echo $user->dept_name; ?></td>
            	<td class="hidden-xs"><?php echo $user->designation; ?></td>
            	<td class="hidden-xs"><?php echo $user->jdate; ?></td>
            	<td class="hidden-xs"><?php echo $user->resignation_date; ?></td>
            </tr>
            <?php } ?>
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

<?php include 'footer.php'; ?>