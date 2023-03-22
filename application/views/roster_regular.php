<link
	href="<?php echo base_url()?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />

<form class="form-horizontal">
	<div class='row'>
		<div class="col-md-6 col-sm-12">
			<div class="form-group">
				<label class="col-sm-3 control-label" for="select_dept">Select Staff: </label>
				<div class="col-sm-6">
					<select name='staff' id='staff' class='selectpicker form-control'
							data-live-search='true' multiple required>
						<?php

						
						if(count($rosterStatus) > 0){
							echo "<option value='all'>All</option>";
							foreach ($staffArray as $emp_id=>$name) {
								if(in_array($emp_id,$rosterStatus)){
									echo "<option value='$emp_id'>$name</option>";
								}
							}
						} /* else {
							foreach ($staffArray as $emp_id=>$name) {
								echo "<option value='$emp_id'>$name</option>";
							}
						} */
						?>
					</select>
				</div>
			</div>
		</div>
	</div>
</form>
<div class='clearfix'></div>
<div class='row'>
	<div class="col-md-12 col-sm-12">
		<div id="staffContainer" class="form-group">
			<div class="tokens"></div>
		</div>
	</div>
</div>
<ul id="myTab" class="nav nav-tabs">
	<li class="active"><a href="#same" data-toggle="tab">Same time for all
			day</a></li>
	<li class=""><a href="#custom" data-toggle="tab">Custom time for
			different day</a></li>
</ul>
<div id="myTabContent" class="tab-content">

	<div class="tab-pane fade active in" id="same">
		<br>
		<form action="<?php echo base_url()?>roster/save" method="post"
			  class="form-horizontal">
			<input type='hidden' name='sdate' value='<?php echo $sdate; ?>'>
			<input type='hidden' name='edate' value='<?php echo $edate; ?>'>
			<input type='hidden' name='type' value='same'>
			<input type="hidden" name='staffIds' class="staffIds" value="">



			<div class='col-sm-5'>
				<div class="row">
					<div class='form-group'>
						<label class="col-sm-5 control-label" for="select_dept">Office
							Start Time: </label>
						<div class="col-sm-3 bootstrap-timepicker">
							<input type='text' name='sstime' id='sstime' value='09:00'
								   class='form-control'>
						</div>
					</div>

					<div class='form-group'>
						<label class="col-sm-5 control-label" for="select_dept">Office End
							Time: </label>
						<div class="col-sm-3 bootstrap-timepicker">
							<input type='text' name='setime' id='setime' value='18:00'
								   class='form-control'>
						</div>
					</div>

					<div class='form-group'>
						<label class="col-sm-5 control-label" for=""></label>
						<div class='col-sm-3'>
							<input type='submit' name='save' id='save_same'
								   value="Set Roster Time" class='btn btn-info'>
						</div>
					</div>
				</div>
			</div>
			<div class='col-sm-3'>
				<div class="box box box-info">
					<div class="box-header text-center">
						<strong>Select Weekend(s)</strong>
					</div>
					<div class="box-body">
						<div class='container'>
							<div class="form-group" id="selectWeekend">
								<?php foreach ($day_array as $key=>$val){
									echo "<div class='checkbox'><label><input type='checkbox' name='chk[]' value='$key'>$val</label></div>";
								}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>


	<div class="tab-pane fade" id="custom">
		<br>

		<form id="CustomformId" action="<?php echo base_url()?>roster/save" method='post'>
			<input type='hidden' name='type' value='custom'>

			<input type='hidden' name='sdate' value='<?php echo $sdate; ?>'>
			<input type='hidden' name='edate' value='<?php echo $edate; ?>'>
			<input type="hidden" name='staffIds' class="staffIds" value="">
			<input type='hidden' id='customReason' name='customReason' value=''>
			<input type='hidden' id='toAdmin' name='toAdmin' value=''>
			<input type="hidden" name="d_code" value="<?php echo $selectedDeptCode?>">

			<div class='col-sm-12 table-responsive'>
				<table class='table table-bordered text-center'>
					<tr style=''>
						<th>Weekend</th>
						<th>Date</th>
						<th>Day</th>
						<th>From</th>
						<th>To</th>
					</tr>
					<?php
					//print_r($aDays);


					foreach($aDays as $i=>$date) {
						$stime = isset($old_data[$date]) ? $old_data[$date]["stime"] : "09:00:00";
						$etime = isset($old_data[$date]) ? $old_data[$date]["etime"] : "18:00:00";
						//$remarks = isset($remarks_array[$date]) ? $remarks_array[$date] : "";
						$style = $i%2==0 ? "background-color:#F7F9FB" : "";
						echo "<tr  style='$style'>";
						echo "<td ><input type='checkbox' id='check".$i."' class='leave_check $date' name='leave_chk[]' value='$date'></td>";
						echo "<td>".$date."<input type='hidden' class='roster_date' name='date[]' value='".$date."'></td>";
						echo "<td>".date("l",strtotime($date))."</td>";
						echo "<td><span class='bootstrap-timepicker'><input type='text' name='stime[]' value='$stime' readonly class='time_field stime cstime'></span><span class='holiText text-danger' hidden><b>Weekend</b></span></td>";
						echo "<td><span class='bootstrap-timepicker'><input type='text' name='etime[]' value='$etime' readonly class='time_field etime cetime'></span><span class='holiText text-danger' hidden><b>Weekend</b></span></td>";
						//echo "<td><input type='text' name='remarks' value='$remarks' size='100' class='remarks'></td>";
						echo "</tr>";
					}
					?>
				</table>
				<div class='form-group'>
					<label class="col-sm-2 control-label" for="select_dept"></label>
					<div class='col-sm-2'>
						<input type='submit' name='save' id='save' value="Set Roster Time"
							   class='btn btn-info'>
					</div>
				</div>
			</div>
		</form>
	</div>

</div>

<!--Send Request Modal Dialog -->
<div class="modal fade" id="requestModal" role="dialog"
	 aria-labelledby="confirmDeleteLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id='reqWeekendForm' class='form-horizontal' action="<?php echo base_url()?>roster/save/" method="post">

				<input type='hidden' name='sdate' value='<?php echo $sdate; ?>'>
				<input type='hidden' name='edate' value='<?php echo $edate; ?>'>
				<input type='hidden' name='type' value='same'>
				<input type="hidden" name='staffIds' class="staffIds" value="">

				<input type="hidden" name="d_code" value="<?php echo $selectedDeptCode?>">
				<input type="hidden" id="from_time" name="sstime" value="09:00:00">
				<input type="hidden" id="to_time" name="setime" value="18:00:00">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"
							aria-hidden="true">&times;</button>
					<h4 class="modal-title">Send Request</h4>
				</div>
				<div class="modal-body">
					<p>To select more than two weekends in a week slot, send a request
						to admin selecting those weekends. Otherwise Cancel it.</p>
					<div class='col-xs-12'>
						<div class="form-group" id="modalSelect">
							<?php foreach ($day_array as $key=>$val){
								echo "<div class='checkbox'><label><input type='checkbox' name='chk[]' value='$key'>$val</label></div>";
							}
							?>
						</div>
					</div>

					<div class='col-xs-12'>
						<div class="form-group">
							<label>Reason</label>
							<textarea id ="reason" name="reason" placeholder="Enter ..." rows="2" class="form-control"></textarea>
						</div>
					</div>


					<div class='clearfix'></div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-warning" id="sendRequest">Send	Request</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- custom Modal Dialog -->
<div class="modal fade" id="customModal" role="dialog"
	 aria-labelledby="customModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"
						aria-hidden="true">&times;</button>
				<h4 class="modal-title">Send Request</h4>
			</div>
			<div class="modal-body">
				<p>To select weekends more than as usual in a week slot, a request will be sent
					to admin. Otherwise <mark><b>Cancel</b></mark> it.</p>
				<div class='col-xs-12'>
					<div class="form-group">
						<label>Reason</label>
						<textarea id ="customR" placeholder="Enter valid reason for this weekend ..." rows="2" class="form-control"></textarea>
					</div>
				</div>
				<div class='clearfix'></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" id="customCancel" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-warning" id="customOk">Ok</button>
			</div>
		</div>
	</div>
</div>


<script
	src="<?php echo base_url()?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-timepicker.min.js"></script>
<link href="<?php echo base_url();?>assets/css/progress.css"
	  type="text/css" rel="stylesheet" />


<script>
	var startDate = '<?php echo $sdate?>';
	var endDate = '<?php echo $edate?>';
	var weekDay = <?php echo json_encode($day_array)?>;
	var periodDate = <?php echo json_encode($aDays)?>;
	var mLimit = <?php echo $max_weekend?>;
	//console.log(periodDate);

	$(function() {

		$("#CustomformId").submit(function(e){

			var staffs = $("#staff").val();

			if(staffs == null || staffs.length == 0 ){

				e.preventDefault();
				alert("please Select Staff field.");
				return false;
			}
		});

		var isCustomToAdmin = false;
		var clickedDate;
		var isOk = false;
		var customDateAry = new Array();

		//format SetOfDate
		var setOfDate = new Array();
		var j=0;
		var tmpAry = new Array();
		for(var i =0; i<periodDate.length; i++){
			var datex = new Date(periodDate[i]);
			var dayN = datex.getDay();
			tmpAry[tmpAry.length] = periodDate[i];

			if(dayN == 6 || i == (periodDate.length -1)){
				setOfDate[j] = tmpAry;
				tmpAry = new Array();
				j++;
			}
		}


		$('input.leave_check:checkbox').click(function(){
			clickedDate = $(this);
			var parentRow = $(this).parents("tr");
			var tbody = $(this).parents("tbody");
			var str = parentRow.find('td:nth-child(2) input.roster_date:hidden').val();	//select date

			var indexAtPeriodDate = $.inArray(str, periodDate);
			var aryIndex =getIndex(str, setOfDate);   //get [' index', 'inner index']

			var countS = 0;
			var index1 = aryIndex[0];
			var innerIndex = aryIndex[1];
			var selectedSet = setOfDate[index1];
			var previousSet = setOfDate[index1-1];
			var nextSet = setOfDate[index1+1];
			var lengthSelectedSet = (selectedSet.length-1);

			for(var k=0; k < selectedSet.length; k++){
				var id = selectedSet[k];
				var sId = "input."+id+":checkbox";
				var dateChecked = tbody.find(sId).is(":checked");

				if(dateChecked){

					if((k==0 && dateChecked && previousSet != undefined)){
						var countprv = 0;
						var countIn = 1;

						for(var t=0; t<mLimit; t++){
							var chk = "#check"+(indexAtPeriodDate-(t+1+innerIndex));
							if(tbody.find(chk).is(":checked")){
								countprv++;
							}else {
								break;
							}
						}

						for(var i=1; i<mLimit; i++){

							var id = selectedSet[i];
							var sId = "input."+id+":checkbox";
							var innerChecked = tbody.find(sId).is(":checked");

							if(innerChecked){
								countIn++;
							}else {
								break;
							}
						}

						var sum = countprv+countIn;
						console.log("sum:"+sum);
						if(sum <= mLimit){
							countprv=0;
						}

						console.log("prv:"+countprv);
						countS += countprv;
						//console.log(countS);

					} else if( k==lengthSelectedSet && dateChecked && nextSet != undefined){
						var countnxt = 0;
						var countIn = 1;

						for(var t=0; t<mLimit; t++){
							var chk = "#check"+(indexAtPeriodDate+(t+1+(lengthSelectedSet-innerIndex)));
							if(tbody.find(chk).is(":checked")){
								countnxt++;
							}else {
								break;
							}
						}
						for(var i=(lengthSelectedSet-1); i>(lengthSelectedSet-mLimit); i--){

							var id = selectedSet[i];
							var sId = "input."+id+":checkbox";
							var innerChecked = tbody.find(sId).is(":checked");

							if(innerChecked){
								countIn++;
							}else {
								break;
							}
						}

						var sum = countnxt+countIn;
						console.log("sum:"+sum);
						if(sum <= mLimit){
							countnxt=0;
						}

						console.log("nxt:"+countnxt);
						countS += countnxt;
					}

					countS++;
					console.log("countS:"+countS);

					if(countS > mLimit){
						$('#customModal').modal('show');
						//$(this).prop("checked", false);
						//bindModal($(this));
						break;
					}
				}
			}

			var isChecked = $(this).is(":checked");
			parentRow.find(":input.time_field").attr("hidden", isChecked);
			parentRow.find(".holiText").attr("hidden", !isChecked);
		});


		$('#customOk').on('click', function(){
			var reason = $('#customR').val();

			//console.log(reason);
			if(reason.length<10){
				//console.log(reason);
				alert("Please enter a valid reason(in atleast 10 words).");
				return;
			}else{
				$('#customModal').modal('hide');
				$('#customReason').val($('#customReason').val()+reason+"; ");
				$('#toAdmin').val(true);
				isOk = true;
				isCustomToAdmin = true;
			}
		});

		$('#customModal').on('hidden.bs.modal', function () {
			if(!isOk){
				clickedDate.prop("checked", false);
				//var parentRow = clickedDate.parents("tr");
				clickedDate.parents("tr").find(":input.time_field").attr("hidden", false);
				clickedDate.parents("tr").find(".holiText").attr("hidden", true);
			}
			isOk = false;
		});


		$('#staff').on('change', function(){

			var idsArray = new Array();
			var str = "";
			var i=0;

			$("#staff :selected").each(function () {
				if($(this).val() == 'all'){
					$('#staff').selectpicker('deselectAll');
					$('#staff').selectpicker('val', 'all');
					//console.log('test');
				}
			});

			$("#staff :selected").each(function () {
				if($(this).val() == 'all'){
					//console.log('test2');
					$("#staff option").each(function () {
						if($(this).val() != 'all'){
							idsArray[i] = $(this).val();
							i++;
						}
					});
				} else {
					idsArray[i] = $(this).val();
					str += "<div class='token'>"+$(this).text()+"</div>";
					i++;
				}

			});


			$(".staffIds").val(idsArray.join(','));

			str = "<div class='col-md-2'>Selected Staffs : </div><div class='col-md-9'>"+str+"</div>";
			$(".tokens").html(str);
		});

		//same time
		$('#selectWeekend').find(':checkbox').click(function (e) {
			//console.log($(this));
			var count = 0;

			$(this)
				.closest('.form-group')
				.find(':checkbox').each(function (i, el) {

				if (el.checked){
					count++;
					var select = "input:checkbox[value="+el['value']+"]";
					$('#modalSelect').find(select).prop("checked", true);
				}
				//else count = 0;

				if (count > mLimit){
					e.preventDefault();
					$('#requestModal').modal('show');
				}
			});
		});

		$('#requestModal').on('hidden.bs.modal', function () {

			$('#modalSelect').find(':checkbox').each(function (i, el) {
				$(this).prop("checked", false);
			});
		});

		$('#sendRequest').on('click', function(){
			//$('#reqWeekendForm').submit();
			$('#requestModal').modal('hide');

			$("body").append("<div class='loader'>Please wait&hellip;</div>");
			var url = "<?php echo base_url()?>roster/save";
			$.ajax({
				type: "POST",
				url: url,
				data: $("#reqWeekendForm").serialize(), // serializes the form's elements.
				dataType:"json",
				success: function(response){
					$(".loader").remove();
					if(!response.status) {
						alert(response.msg);
						return;
					}else{
						alert(response.msg);
						location.href = "";
					}
				}
			});
		});

	});

	$('#sstime, #setime, .cstime, .cetime').timepicker({
		defaultTime: false,
		showMeridian: false,
		minuteStep: 5,
		showSeconds: true,
		disableFocus: true,
		modalBackdrop: true,
		template: 'dropdown'
	});

	$('#sstime').timepicker().on('hide.timepicker', function(e) {
		$('#from_time').val(e.time.value);
	});
	$('#setime').timepicker().on('hide.timepicker', function(e) {
		$('#to_time').val(e.time.value);
	});

	var now = new Date();
	var day = ("0" + now.getDate()).slice(-2);
	var month = ("0" + (now.getMonth() + 1)).slice(-2);
	var startday = now.getFullYear() + "-" + (month) + "-" + "01";
	var endDay = "<?php echo $edate; ?>";
	if(endDay.length==0) endDay = now.getFullYear() + "-" + (month) + "-" + (day);


	function getIndex(str, setOfDate ){

		for(var i =0; i<setOfDate.length; i++){

			var index = $.inArray(str, setOfDate[i]);
			if(index != -1){
				var array = [i, index];
				return array;
			}
		}
	}

</script>