<style>
	<!--
	.margin-bottom{
		margin-bottom: 8px;
	}
	-->
</style>
<link href="<?php echo base_url()?>assets/lib/bootstrap/css/bootstrap-select.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/main.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/roster.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url()?>assets/lib/tipsy/tipsy.css" type="text/css" rel="stylesheet" />
<link rel="stylesheet" href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-timepicker.min.css">
<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css" type="text/css" rel="stylesheet" />

<form class="form-horizontal" id = 'departmentSelectForm' action="" method='post'>
	<div class = 'row'>
		<div class='col-md-4 col-sm-5'>
			<div class="form-group">
				<label class="col-sm-4 control-label" for="selected_dept">Department : </label>
				<div class="col-sm-8">
					<select name="selected_dept" class="selectpicker" id='selected_dept' data-width="100%" data-live-search="true">

						<?php

						foreach ($rosterDepartments  as $dept_code) {
							//if($dept_code != 'CA'){
								if( $dept_code == $selectedDeptCode){
									echo "<option value='".$dept_code."' selected='selected' >".$departments[$dept_code]."</option>";
								}else{
									echo "<option value='".$dept_code."'>",$departments[$dept_code],"</option>";
								}
							/*} else {
								if($roster_status == 'Y'){
									echo "<option value='".$dept_code."' roster = 'N'>Call Center (Non Roster)</option>";
									echo "<option value='".$dept_code."' roster = 'Y' selected>".$departments[$dept_code]."(Roster)</option>";
								} else if($roster_status == 'N') {
									echo "<option value='".$dept_code."' roster = 'N' selected>Call Center (Non Roster)</option>";
									echo "<option value='".$dept_code."' roster = 'Y'>".$departments[$dept_code]."(Roster)</option>";
								} else {
									echo "<option value='".$dept_code."' roster = 'N'>Call Center (Non Roster)</option>";
									echo "<option value='".$dept_code."' roster = 'Y'>".$departments[$dept_code]."(Roster)</option>";
								}

							} */
						} ?>

					</select>
				</div>
			</div>
		</div>
		<div class='col-md-4 col-sm-5'>
			<div class="form-group">
				<label class="col-sm-4 control-label" for="selected_dept">Roster Type : </label>
				<div class="col-sm-8">
					<select name="rsoter_type" id="roster_type" class="selectpicker">
						<option value="">Select</option>
						<?php if ($roster_status == 'Y') { ?>
							<option value="Y" selected>Roster</option>
							<option value="N">Non-Roster</option>
						<?php } else if($roster_status == 'N') { ?>
							<option value="Y">Roster</option>
							<option value="N" selected>Non-Roster</option>
							<?php
						} else { ?>
							<option value="Y">Roster</option>
							<option value="N">Non-Roster</option>
						<?php } ?>
					</select>
				</div>
			</div>
		</div>
	</div>
</form>

<form id="dateForm" action="<?php echo base_url()?>roster/set/<?php echo $selectedDeptCode;?>" method="post" class="form-horizontal">
	<input type="hidden" name="roster_status" value="<?php echo $roster_status; ?>">
	<div class="row">
		<div class='col-sm-4'>
			<div  class='form-group'>
				<label class="col-sm-3 control-label" for="sdate">From:</label>
				<div class="col-sm-9">
					<input type='text' id='sdate' name='sdate' value='<?php echo $sdate; ?>' class="form-control">
				</div>
			</div>
		</div>
		<div class='col-sm-4'>
			<div  class='form-group'>
				<label class="col-sm-3 control-label" for="edate">To:</label>
				<div class="col-sm-9">
					<input type='text' id='edate' name='edate' value='<?php echo $edate; ?>' class="form-control">
				</div>
			</div>
		</div>
		<div class='col-sm-4'>
			<div  class='form-group'>
				<div class="col-sm-9">
					<input type='submit' name='search' value='Show' class="btn btn-primary">
				</div>
			</div>
		</div>
	</div>
</form>
<div class='clearfix'></div>

<script type="text/javascript">

	$(document).ready(function(){
		var base_url = "<?php echo base_url();?>";

		$('#sdate, #edate').datepicker({
			format: 'yyyy-mm-dd'
		});

		$('#sdate').on('changeDate', function(ev){
			$(this).datepicker('hide');
			$('#edate').datepicker('setStartDate',$(this).val());
		});

		$('#edate').on('changeDate', function(ev){
			$(this).datepicker('hide');
			$('#sdate').datepicker('setEndDate',$(this).val());
			$("#dateForm").submit();
		});


		$('#roster_type').on('change', function(){

			var selected = $("#selected_dept").find("option:selected").val();
			var rosterStatus = $(this).find("option:selected").val();
			if(typeof rosterStatus != 'undefined'){
				var action_url = base_url+ "roster/set/"+selected+"/"+rosterStatus;
			} else {
				var action_url = base_url+ "roster/set/"+selected;
			}

			$('#departmentSelectForm').attr("action", action_url);

			$('#departmentSelectForm').submit();
		});

		$("#selected_dept").on('change',function () {
			var dept_code = $("#selected_dept").val();
			location.href = '<?php echo base_url(); ?>roster/set/'+dept_code;
		});

	});


</script>