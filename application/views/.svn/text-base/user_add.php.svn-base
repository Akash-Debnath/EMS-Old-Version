<?php include 'header.php'; ?>


<form class="form-horizontal" role="form">
   <div class="form-group">
      <label for="firstname" class="col-sm-2 control-label">Name</label>
      <div class="col-sm-4">
         <input type="text" class="form-control" id="name" name="name" placeholder="Name">
      </div>
   </div>
   <div class="form-group">
   		<div class="col-sm-7 form-header">
      		Employee Information
      	</div>
   </div>
   <div class="form-group">
      <label for="firstname" class="col-sm-2 control-label">Employee ID</label>
      <div class="col-sm-2">
         <input type="text" class="form-control" id="emp_id" name="emp_id" value="<?php echo $data->emp_id;?>" placeholder="Employee ID">
      </div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Grade</label>
      <div class="col-sm-10">
         <select name="grade" class="selectpicker" id='grade' data-live-search="false" style='width: 200px;'>
			<?php for ($i=1; $i<=13; $i++) {
				if($data->grade==$i)
					echo "<option value='".$i."' selected='selected'>".$i."</option>";
				else 
					echo "<option value='".$i."'>".$i."</option>";
			} ?>
		</select>
      </div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Department</label>
      <div class="col-sm-10">
         <select name="dept_code" class="selectpicker" id='dept_code' data-live-search="true" style='width: 200px;'>
			<?php echo "<option value=''>---Select---</option>"; ?>
			<?php foreach ($departments as $obj) {
				if($data->dept_code==$obj->dept_code)
					echo "<option value='".$obj->dept_code."' selected='selected'>".$obj->dept_name."</option>";
				else
					echo "<option value='".$obj->dept_code."'>".$obj->dept_name."</option>";
			} ?>
		</select>
      </div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Operational Designation</label>
      <div class="col-sm-10">
         <select name="designation" class="selectpicker" id='designation' data-live-search="false" style='width: 200px;'>
			<?php echo "<option value=''>---Select---</option>"; ?>
			<?php foreach ($designations as $obj) {
				if($data->dept_code==$obj->dept_code) {
					if($data->designation_id==$obj->id) {
						echo "<option value='".$obj->id."' selected='selected'>".$obj->designation."</option>";
					} else {
						echo "<option value='".$obj->id."'>".$obj->designation.$data->designation."</option>";
					}
				}
			} ?>
		</select>
      </div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Joining Date</label>
      <div class="col-sm-2">
         <input type="text" class="form-control" id="jdate" name="jdate" placeholder="Joining Date">
      </div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Current Status</label>
      <div class="col-sm-10">
         <select name="status" class="selectpicker" id='status' data-live-search="false" style='width: 200px;'>
			<?php foreach ($status_array as $key=>$value) {
				if($data->status==$key)
					echo "<option value='".$key."' selected='selected'>".$value."</option>";
				else 
					echo "<option value='".$key."'>".$value."</option>";
			} ?>
		</select>
      </div>
   </div>
   
   <div class="form-group">
   		<div class="col-sm-7 form-header">
      		Contact Information
      	</div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Mobile</label>
      <div class="col-sm-2">
         <input type="text" class="form-control" id="mobile" va name="mobile" placeholder="Mobile">
      </div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Home Phone</label>
      <div class="col-sm-2">
         <input type="text" class="form-control" id="phone" name="phone" placeholder="Home Phone">
      </div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Email</label>
      <div class="col-sm-4">
         <input type="text" class="form-control" id="email" name="email" placeholder="Email">
      </div>
   </div>
   <div class="form-group">
      <label for="pre_address" class="col-sm-2 control-label">Present Address</label>
      <div class="col-sm-5">
         <textarea class="form-control" id="pre_address" name="pre_address" placeholder="Present Address"><?php echo $data->pre_address; ?></textarea>
      </div>
   </div>
   <div class="form-group">
      <label for="per_address" class="col-sm-2 control-label">Permanent Address</label>
      <div class="col-sm-5">
         <textarea class="form-control" id="per_address" name="per_address" placeholder="Permanent Address"><?php echo $data->per_address; ?></textarea>
      </div>
   </div>
   
   
   <div class="form-group">
   		<div class="col-sm-7 form-header">
      		Educational Information
   		</div>
   	</div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Last Achievement</label>
      <div class="col-sm-5">
         <input type="text" class="form-control" id="last_edu_achieve" placeholder="Last Achievement">
      </div>
   </div>
   
   <div class="form-group">
   		<div class="col-sm-7 form-header">
      		Personal Information
      	</div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Date of Birth</label>
      <div class="col-sm-2">
         <input type="text" class="form-control" id="dob" name="dob" placeholder="Date of Birth">
      </div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Blood Group</label>
      <div class="col-sm-10">
         <select name="blood_group" id='blood_group' class="selectpicker" data-live-search="false" style='width: 200px;'>
			<?php foreach ($blood_group_array as $key=>$value) {
				if($data->blood_group==$key)
					echo "<option value='".$key."' selected='selected'>".$value."</option>";
				else 
					echo "<option value='".$key."'>".$value."</option>";
			} ?>
		</select>
      </div>
   </div>
   <div class="form-group">
      <label for="lastname" class="col-sm-2 control-label">Gender</label>
      <div class="col-sm-10">
         <select name="gender" id='gender' class="selectpicker" data-live-search="false" style='width: 200px;'>
			<?php foreach ($gender_array as $key=>$value) {
				if($data->gender==$key)
					echo "<option value='".$key."' selected='selected'>".$value."</option>";
				else 
					echo "<option value='".$key."'>".$value."</option>";
			} ?>
		</select>
      </div>
   </div>
   
   <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
         <button type="submit" class="btn btn-default">Update</button>
      </div>
   </div>
</form>


<?php include 'footer.php'; ?>

<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css" type="text/css" rel="stylesheet"/>

<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css" type="text/css" rel="stylesheet"/>


<script type="text/javascript">
$(function(){
	$("#dept_code").change(function(){
		var dept_code = $(this).val();
		var options = "";

		$("#designation").empty();
		var data = <?php echo json_encode($designations);?>;
		$("#designation").append("<option value=''>---Select---</option>");
		for(var key in data) {
			//alert(data[key].dept_code);
			if(dept_code==data[key].dept_code) $("#designation").append("<option value='"+data[key].id+"'>"+data[key].designation+"</option>");
		}

		$("#designation").selectpicker('refresh');
		//$(this).empty();
		//$(this).append(options);
		
	});

	$("#jdate, #dob").datepicker({
	    format: 'yyyy-mm-dd'
	});
	$('#jdate, #dob').on('changeDate', function(ev){
	    $(this).datepicker('hide');
	});

});
</script>