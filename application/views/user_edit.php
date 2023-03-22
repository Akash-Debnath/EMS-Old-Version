<?php 
$skin = "skin-blue";
?>
<?php include 'header.php'; ?>

<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>

<style>
.error {
	color: red;
}

#empInfo div div input {
	display: inline-block;
	margin-right: 5px;
	color: black;
}

/*
.error{
    display: none;
    margin-left: 10px;
}       
 
.error_show{
    color: red;
    margin-left: 10px;
}
input.invalid, textarea.invalid{
    border: 2px solid red;
}
 
input.valid, textarea.valid{
    border: 2px solid green;
}
*/
</style>


<form id="empInfo" class="form-horizontal" method="post"
	enctype="multipart/form-data" accept-charset="utf-8"
	action="<?php echo base_url();?>user/updateEmployee">
	<input type="hidden" id="empInfo_id" name="empInfo_id"
		value="<?php echo $data->id; ?>"> <input type="hidden" name="nameH"
		value="<?php echo $data->name; ?>"> <input type="hidden"
		name="emp_idH" value="<?php echo $data->emp_id; ?>"> <input
		type="hidden" name="grade_idH" value="<?php echo $data->grade_id; ?>">
	<input type="hidden" name="dept_codeH"
		value="<?php echo $data->dept_code; ?>"> <input type="hidden"
		name="designation_idH" value="<?php echo $data->designation_id; ?>"> <input
		type="hidden" name="jdateH" value="<?php echo $data->jdate; ?>"> <input
		type="hidden" name="statusH" value="<?php echo $data->status; ?>"> <input
		type="hidden" name="mobileH" value="<?php echo $data->mobile; ?>"> <input
		type="hidden" name="phoneH" value="<?php echo $data->phone; ?>"> <input
		type="hidden" name="emailH" value="<?php echo $data->email; ?>"> <input
		type="hidden" name="pre_addressH"
		value="<?php echo $data->pre_address; ?>"> <input type="hidden"
		name="per_addressH" value="<?php echo $data->per_address; ?>"> <input
		type="hidden" name="last_edu_achieveH"
		value="<?php echo $data->last_edu_achieve; ?>"> <input type="hidden"
		name="dobH" value="<?php echo $data->dob; ?>"> <input type="hidden"
		name="genH" value="<?php echo $data->gen; ?>"> <input type="hidden"
		name="blood_groupH" value="<?php echo $data->blood_group; ?>"> <input
		type="hidden" name="imageH" value="<?php echo $data->image; ?>">

	<div class="form-group">
		<label for="name" class="col-sm-2 control-label">Name</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" id="name" name="name"
				value="<?php echo $data->name; ?>" placeholder="Name"
				style="width: 50%" required>

		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-10 form-header">Employee Information</div>
	</div>
	<div class="form-group">
		<label for="emp_id" class="col-sm-2 control-label">Employee ID</label>
		<div class="col-sm-8">
			<input type="text" class="form-control " id="emp_id" name="emp_id"
				value="<?php echo $data->emp_id;?>" placeholder="Employee ID"
				style="width: 20%" required>

		</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-sm-2 control-label">Grade</label>
		<div class="col-sm-10">
			<select name="grade" class="selectpicker " id='grade'
				data-live-search="false" style='width: 200px;'>
			<?php echo "<option value=''>---Select---</option>"; ?>
			<?php foreach ($grades as $obj) {
				if($data->grade_id==$obj->grade_id)
					echo "<option value='".$obj->grade_id."' selected='selected'>".$obj->grade."</option>";
				else
					echo "<option value='".$obj->grade_id."'>".$obj->grade."</option>";
			} ?>
		</select>
		</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-sm-2 control-label">Department</label>
		<div class="col-sm-8">
			<select name="dept_code" class="selectpicker " id='dept_code'
				data-live-search="true" style='width: 200px;' required>
			<?php echo "<option value=''>---Select---</option>"; ?>
			<?php foreach ($departments as $key=>$val) {
				if($data->dept_code==$key)
					echo "<option value='$key' selected='selected'>$val</option>";
				else
					echo "<option value='$key'>$val</option>";
			} ?>
		</select>
		</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-sm-2 control-label">Operational
			Designation</label>
		<div class="col-sm-10">
			<select name="designation" class="selectpicker " id='designation'
				data-live-search="false" style='width: 200px;' required>
			<?php echo "<option value=''>---Select---</option>"; ?>
			<?php foreach ($designations as $obj) {
				if($data->dept_code==$obj->dept_code) {
					if($data->designation_id==$obj->id) {
						echo "<option value='".$obj->id."' selected='selected'>".$obj->designation."</option>";
					} else {
						echo "<option value='".$obj->id."'>".$obj->designation."</option>";
					}
				}
			} ?>
		</select>
		</div>
	</div>
	<div class="form-group">
		<label for="jdate" class="col-sm-2 control-label">Joining Date</label>
		<div class="col-sm-8">
			<input type="text" class="form-control " id="jdate" name="jdate"
				placeholder="YYYY-MM-DD" value="<?php echo $data->jdate; ?>"
				required style="width: 15%">
		</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-sm-2 control-label">Current Status</label>
		<div class="col-sm-10">
			<select name="status" class="selectpicker " id='status'
				data-live-search="false" style='width: 200px;' required>
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
		<label for="jdate" class="col-sm-2 control-label">Office Login Time</label>
		<div class="col-sm-8">
			<input type="text" class="form-control " id="stime" name="stime"
				placeholder="00:00:00" value="<?php if(isset($data->office_stime))echo $data->office_stime;else echo '09:00:00' ?>"
				required style="width: 15%">
		</div>
	</div>
	<div class="form-group">
		<label for="jdate" class="col-sm-2 control-label">Office Logout Time</label>
		<div class="col-sm-8">
			<input type="text" class="form-control " id="etime" name="etime"
				placeholder="00:00:00" value="<?php if(isset($data->office_etime))echo $data->office_etime;else echo '18:00:00'?>"
				required style="width: 15%">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-10 form-header">Contact Information</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-sm-2 control-label">Mobile</label>
		<div class="col-sm-8">
			<input type="text" class="form-control " id="mobile" name="mobile"
				placeholder="Mobile" value="<?php echo $data->mobile; ?>"
				style="width: 30%">
		</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-sm-2 control-label">Home Phone</label>
		<div class="col-sm-8">
			<input type="text" class="form-control " id="phone" name="phone"
				placeholder="Home Phone" value="<?php echo $data->phone; ?>"
				style="width: 30%">
		</div>
	</div>
	<div class="form-group">
		<label for="email" class="col-sm-2 control-label">Email</label>
		<div class="col-sm-8">
			<input type="email" class="form-control " id="email" name="email"
				placeholder="Email" value="<?php echo $data->email; ?>"
				style="width: 50%">
		</div>
	</div>
	<div class="form-group">
		<label for="pre_address" class="col-sm-2 control-label">Present
			Address</label>
		<div class="col-sm-5">
			<textarea class="form-control " id="pre_address" name="pre_address"
				placeholder="Present Address"><?php echo $data->pre_address; ?></textarea>

		</div>
	</div>
	<div class="form-group">
		<label for="per_address" class="col-sm-2 control-label">Permanent
			Address</label>
		<div class="col-sm-5">
			<textarea class="form-control " id="per_address" name="per_address"
				placeholder="Permanent Address"><?php echo $data->per_address; ?></textarea>
		</div>
	</div>


	<div class="form-group">
		<div class="col-sm-10 form-header">Educational Information</div>
	</div>
	<div class="form-group">
		<label for="last_edu_achieve" class="col-sm-2 control-label">Last
			Achievement</label>
		<div class="col-sm-5">
			<input type="text" class="form-control " id="last_edu_achieve"
				name="last_edu_achieve" placeholder="Last Achievement"
				value="<?php echo $data->last_edu_achieve; ?>">
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-10 form-header">Personal Information</div>
	</div>
	<div class="form-group">
		<label for="dob" class="col-sm-2 control-label">Date of Birth</label>
		<div class="col-sm-8">
			<input type="text" class="form-control " id="dob" name="dob"
				placeholder="Date of Birth" value="<?php echo $data->dob; ?>"
				required style="width: 15%">
		</div>
	</div>
	<div class="form-group">
		<label for="lastname" class="col-sm-2 control-label">Blood Group</label>
		<div class="col-sm-10">
			<select name="blood_group" id='blood_group' class="selectpicker "
				data-live-search="false" style='width: 200px;'>
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
		<label for="gender" class="col-sm-2 control-label">Gender</label>
		<div class="col-sm-10">
			<select name="gender" id='gender' class="selectpicker "
				data-live-search="false" style='width: 200px;' required>
			<?php foreach ($gender_array as $key=>$value) {
				if($data->gen==$key)
					echo "<option value='".$key."' selected='selected'>".$value."</option>";
				else 
					echo "<option value='".$key."'>".$value."</option>";
			} ?>
		</select>
		</div>
	</div>

	<div class="form-group">
		<label for="uimage" class="col-sm-2 control-label">Upload image</label>
		<div class="col-sm-5">
			<div class="col-sm-6" style="padding: 0;">
				<input type="file" id="uimage" name="uimage" accept="image/*"
					style="max-width: 235px;">
			</div>
			<div class="col-sm-6" style="padding: 0;">
				<img id="showimage" src="<?php echo $data->image_path; ?>"
					height="100" width="100" alt="your image" />
			</div>
		</div>
	</div>


	<div class="form-group">
		<div id="submit" class="col-sm-offset-2 col-sm-10">
			<input type="submit" class="btn btn-default" value="Done"
				id="btnSubmit">
		</div>
	</div>
</form>


<?php include 'footer.php'; ?>

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
<link href="<?php echo base_url();?>assets/css/progress.css"
	type="text/css" rel="stylesheet" />


<script type="text/javascript">

$().ready(function() {

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

	$("#btnSubmit").mousedown(function(){
	    $("body").append("<div class='loader'>Please wait&hellip;</div>");
	});

	function readURL(input) {

	    if (input.files && input.files[0]) {
	        var reader = new FileReader();

	        reader.onload = function (e) {
	            $('#showimage').attr('src', e.target.result);
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}

	$("#uimage").change(function(){
	    readURL(this);
	});

	
	// validate signup form on keyup and submit
	var validator = $("#empInfo").validate({
		invalidHandler: function() {
		    if(validator.numberOfInvalids()) {
		        $(".loader").remove();
		    }
		 },
		ignore: [],
		rules: {
			name: {
				required: true,
				minlength: 3,
				maxlength: 30
			},
			emp_id: {
				required: true,
				//number: true,
				minlength:4,
				maxlength: 10
			},
			grade: {
				number: true
			},
			dept_code: {
				required: true
			},
			designation: {
				required: true
			},
			jdate: {
			    required: true,
			    date: true
			},
			status: {
				required: true,
				maxlength: 1
			},
			mobile: {
				number: true,
				minlength:4, 
				maxlength:14
			},	
		    phone: {
		    	number: true,
				minlength:4, 
				maxlength:14
			},	
			dob:{
			    date: true
			},
			email: {
    		    email: true
			},				
			gender: {
				required: true
			},
		},
		errorPlacement: function(error, element) {
	        if (element.attr("name") == "grade") {

	        	error.insertAfter($("#grade").parent().find("div.bootstrap-select")); 
	        }else if (element.attr("name") == "dept_code") {
		        
	        	error.insertAfter($("#dept_code").parent().find("div.bootstrap-select"));
			}else if (element.attr("name") == "designation") {
				
				error.insertAfter($("#designation").parent().find("div.bootstrap-select"));				
			}else if (element.attr("name") == "status") {
				    
				error.insertAfter($("#status").parent().find("div.bootstrap-select"));
			}else if (element.attr("name") == "gender") {
				
				error.insertAfter($("#gender").parent().find("div.bootstrap-select"));
			}else {
				
		        error.insertAfter(element);
		    }
	    },
		messages: {
			name: {
				required: "Please enter a employee name",
				minlength: "Your employee name must consist of at least 3 characters"
			},
			emp_id: {
				required: "Please enter a employee ID",
				minlength: "Your employee ID must consist of at least 4 characters",
				maxlength: "Your employee ID must consist of at most 10 characters",
				idRule: "This employee ID is already in the EMS."
			},
		},
	});

});

</script>



