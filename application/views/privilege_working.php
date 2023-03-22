<?php include 'header.php'; ?>
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />
	
<style>
.tableBox {
	background: none repeat scroll 0 0 #ffffff;
	border-radius: 3px;
	box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
	margin-bottom: 8px;
	position: relative;
	width: 100%;
	padding: 5px;
}

table.tx .hover-btn {
	opacity: 0;
}

table.tx:hover .hover-btn {
	opacity: 1;
}
</style>


<div class="row">
	<!-- Managerial Privilege Table -->

	<div class="col-lg-5">
		<div class='box'>
			<div class='box-header'>
				<h3 class='box-title'>Managerial Privilege</h3>
			</div>
			<div class='box-body no-padding'>
				<table class="table table-bordered table-striped table-hover">
					<thead>
						<tr>
							<th class="hidden-xs" style="width: 8px;"></th>
							<th class="">Department</th>
							<th class="">Manager</th>
						</tr>
					</thead>
					<tbody>
                    <?php 
            		$i=0;
            		foreach ($manager as $key=>$objects) { 
            		if(!is_object($objects)) continue;
            		    ?>
            			<tr class="">
							<td class="hidden-xs serial"><?php echo ++$i; ?></td>
							<td class=""><?php echo $objects->dept_name ?></td>
							<td class="">
                            <?php 
            		        foreach ($objects->managers as $obj) { ?>
                                <div class="tableBox">
									<table class="tx" style="width: 100%;">
										<tbody>
											<tr>
												<td><?php echo $obj->emp_id ?></td>
												<td>
													<form class="" method="post"
														action="<?php echo base_url();?>settings/delete_priviledge">
														<input type="hidden" value="<?php echo $obj->emp_id ?>"
															name="emp_id"> <input type="hidden" value="M"
															name="type"> <input type="hidden"
															value="<?php echo $key ?>" name="dept_code">
														<div class="hover-btn">
															<button type="submit"
																onclick="return confirm('Do you want to delete?')"
																class="close">X</button>
														</div>
													</form>
												</td>
											</tr>
											<tr>
												<td><a
													href="<?php echo base_url()."user/detail/".$obj->emp_id ?>">
														<b><?php echo $obj->name ?></b>
												</a></td>
											</tr>
											<tr>
												<td><i><?php echo $obj->designation ?></i></td>
											</tr>
											<tr>
												<td><?php echo $obj->dept_name ?></td>
											</tr>
										</tbody>
									</table>
								</div>
                            	
            				<?php } ?>
                                <div class="">
									<form action="<?php echo base_url();?>settings/add_priviledge"
										method="post">
										<table>
											<tbody>
												<tr>
													<td><select name="select_emp_id" class="selectpicker"
														id='select_manager' data-live-search="true" data-width="100%">
															<option value="">Select Employee</option>
                                			
                                			<?php foreach ($employees as $dept=>$ary){ 
                                				echo "<optgroup label='".$dept."'>";
                                				
                                				foreach($ary as $obj){
                                					echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ".$obj->name."</option>";
                                				}
                                				echo "</optgroup>";                        			
                                			} ?>
                                		</select> <input type="hidden"
														name="type" value="M"> <input type="hidden"
														name="dept_code" value="<?php echo $key ?>"></td>
													<td><button type="submit" class="btn btn-primary btn-xs"
															style='margin-left: 4px;'>
															<span class="glyphicon glyphicon-plus"></span> Add
														</button></td>
												</tr>
											</tbody>
										</table>
									</form>
								</div>
							</td>
						</tr>
            		<?php } ?>
	                </tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- end col-md-5 -->


	<div class="col-lg-7">
		<div class="row">

			<!-- Admin Privilege Table -->
			<div class="col-md-6">
				<div class='box'>
					<div class='box-header'>
						<h3 class='box-title'>Admin Privilege</h3>
					</div>
					<div class='box-body no-padding'>
						<table class="table">
							<thead>
								<tr>
									<th class="hidden-xs" style="width: 8px;"></th>
									<th class="">Admin</th>
								</tr>
							</thead>
							<tbody>
                            <?php 
                    		$i=0;
                    		foreach ($admin as $obj) { 
                    		    ?>
                				<tr class="">
									<td class="hidden-xs serial"><?php echo ++$i; ?></td>
									<td class="">
										<div class="tableBox">
											<table class="tx" style="width: 100%;">
												<tbody>
													<tr>
														<td><?php echo $obj->emp_id ?></td>
														<td>
															<form class="" method="post"
																action="<?php echo base_url();?>settings/delete_priviledge">
																<input type="hidden" value="<?php echo $obj->emp_id ?>"
																	name="emp_id"> <input type="hidden" value="A"
																	name="type"> <input type="hidden" value=""
																	name="dept_code">
																<div class="hover-btn">
																	<button type="submit"
																		onclick="return confirm('Do you want to delete?')"
																		class="close">X</button>
																</div>
															</form>
														</td>
													</tr>
													<tr>
														<td><a
															href="<?php echo base_url()."user/detail/".$obj->emp_id ?>">
																<b><?php echo $obj->name ?></b>
														</a></td>
													</tr>
													<tr>
														<td><i><?php echo $obj->designation ?></i></td>
													</tr>
													<tr>
														<td><?php echo $obj->dept_name ?></td>
													</tr>
												</tbody>
											</table>
										</div>

									</td>
								</tr>
                    		<?php } ?>
                    		    <tr>
									<td class="hidden-xs"></td>
									<td>
										<div class="">
											<form
												action="<?php echo base_url();?>settings/add_priviledge"
												method="post">
												<table>
													<tbody>
														<tr>
															<td><select name="select_emp_id" class="selectpicker"
																id='select_admin' data-live-search="true" data-width="100%">
																	<option value="">Select Employee</option>
                                    			
                                    			<?php foreach ($employees as $dept=>$ary){ 
                                    				echo "<optgroup label='".$dept."'>";
                                    				
                                    				foreach($ary as $obj){
                                    					echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ".$obj->name."</option>";
                                    				}
                                    				echo "</optgroup>";
                                    			} ?>
                                    		</select> <input type="hidden"
																name="type" value="A"> <input type="hidden"
																name="dept_code" value=""></td>
															<td><button type="submit" class="btn btn-primary btn-xs"
																	style='margin-left: 4px;'>
																	<span class="glyphicon glyphicon-plus"></span> Add
																</button></td>
														</tr>
													</tbody>
												</table>
											</form>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end table -->


			<!-- Management Privilege Table -->
			<div class="col-md-6">
				<div class='box'>
					<div class='box-header'>
						<h3 class='box-title'>Management Privilege</h3>
					</div>
					<div class='box-body no-padding'>
						<table class="table table-bordered table-striped table-hover">
							<thead>
								<tr>
									<th class="hidden-xs" style="width: 10px;"></th>
									<th class="">Management</th>
								</tr>
							</thead>
							<tbody>
						    <?php
                            $i = 0;
                            foreach ($boss as $obj) {
                                ?>
                				<tr class="">
    								<td class="hidden-xs serial"><?php echo ++$i; ?></td>
    								<td class="">
    									<div class="tableBox">
    										<table class="tx" style="width: 100%;">
    											<tbody>
    												<tr>
    													<td><?php echo $obj->emp_id ?></td>
    													<td>
    														<form class="" method="post"
    															action="<?php echo base_url();?>settings/delete_priviledge">
    															<input type="hidden" value="<?php echo $obj->emp_id ?>"
    																name="emp_id"> <input type="hidden" value="B"
    																name="type"> <input type="hidden" value=""
    																name="dept_code">
    															<div class="hover-btn">
    																<button type="submit"
    																	onclick="return confirm('Do you want to delete?')"
    																	class="close">X</button>
    															</div>
    														</form>
    													</td>
    												</tr>
    												<tr>
    													<td><a
    														href="<?php echo base_url()."user/detail/".$obj->emp_id ?>">
    															<b><?php echo $obj->name ?></b>
    													</a></td>
    												</tr>
    												<tr>
    													<td><i><?php echo $obj->designation ?></i></td>
    												</tr>
    												<tr>
    													<td><?php echo $obj->dept_name ?></td>
    												</tr>
    											</tbody>
    										</table>
    									</div>
    
    								</td>
    							</tr>
                    		<?php } ?>
                                <tr>
    								<td class="hidden-xs"></td>
    								<td class="">
    									<div class="">
    										<form id="myform"
    											action="<?php echo base_url();?>settings/add_priviledge"
    											method="post">
    											<table>
    												<tbody>
    													<tr>
    														<td><select name="select_emp_id" class="selectpicker"
    															id='select_boss' data-live-search="true" data-width="100%">
    																<option value="">Select Employee</option>
                                            			
                                            			<?php foreach ($employees as $dept=>$ary){ 
                                            				echo "<optgroup label='".$dept."'>";
                                            				
                                            				foreach($ary as $obj){
                                            					echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ".$obj->name."</option>";
                                            				}
                                            				echo "</optgroup>";
                                            			} ?>
                                            		</select> <input type="hidden"
    															name="type" value="B"> <input type="hidden"
    															name="dept_code" value=""></td>
    														<td><button type="submit" id="btn_add_boss"
    																class="btn btn-primary btn-xs" style='margin-left: 4px;'>
    																<span class="glyphicon glyphicon-plus"></span> Add
    															</button></td>
    													</tr>
    												</tbody>
    											</table>
    										</form>
    									</div>
    								</td>
    							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- end table -->


		</div>
		<!-- end row -->
	</div>
	<!-- end col-md-7 -->
</div>

<?php include 'footer.php'; ?>

<script type="text/javascript">

$(document).ready(function() {
    $("#btn_add_boss").click(function(){
        
//     var v = $('#select_boss').val();
//     alert(v);
//     $("#myform").submit();
    }); 
});

</script>
