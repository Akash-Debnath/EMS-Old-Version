<link rel="stylesheet" href="<?php echo base_url();?>assets/css/evaluation.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/main.css">
<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js" type="text/javascript"></script>
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css" type="text/css" rel="stylesheet" />

<?php

$points = array(1, 2, 3, 4, 5);
$start_date = isset($employee['jdate']) ? $employee['jdate'] : $ems_start;

$d1 = new DateTime($start_date);
$d2 = new DateTime('today');
$diff = $d2->diff($d1);
$noOfYears = $diff->y;

if(!empty($employee['date'])){
    $d3 = new DateTime($employee['date']);
    $diff2 = $d2->diff($d3);
    $year = $diff2->y;
    $month = $diff2->m;
    $day = $diff2->d;
}

//echo "s".$insert_id."s";

$e_disabled = "disabled";
$m_disabled = "disabled";
$a_disabled = "disabled";
$isManager = false;

if( ($myInfo->userId != $eid && in_array($myInfo->userId, $manager)) || $bossFlag ) {
    //manager section
    //echo "test";
    $isManager = true;
    if(!isset($evaluation['emp_sig_date'])  || (isset($evaluation['emp_sig_date']) && empty($evaluation['emp_sig_date'])) ){
        $m_disabled = "";
    }elseif((!empty($evaluation['evstatus']) && ($evaluation['evstatus'] == 'B' || $evaluation['evstatus'] == 'N')) || !empty($evaluation['status']) && ($evaluation['status'] == 'B' || $evaluation['status'] == 'N')){
        $m_disabled = "";
    }    
} else if (!empty($evaluation['manager_id']) && $myInfo->userId == $evaluation['emp_id']){
    $e_disabled = "";
} else if(!empty($evaluation['emp_sig_date']) && $isAdmin){
    $a_disabled = "";
}

function isCustomEvVal($evData){
    if (strlen($evData) > 2 && substr($evData, 2, 1) != 0){
        return true;
    }
    return false;
}
//echo "do".$m_disabled."do";
?>

<div class='row'>
	<div class='col-md-10 col-md-offset-1'>
		<div class="box box-info">
			<div class="box-header">
				<h3 class="text-center">EMPLOYEE PERFORMANCE EVALUATION</h3>
			</div>
			<!-- /.box-header -->
			<!-- form start -->
			<form class="form-horizontal" id ="eval_form" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" id='last_page' name='last_page' value='<?php echo $page ?>'>
			<input type="hidden" id='eval_id' name='eval_id' value='<?php echo $insert_id ?>'>
				<div class="box-body">
				
				<?php if($page == 1){?>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="emp_name">Employee
							Name</label>
						<div class="col-sm-9">
							<input type="text"
								class="form-control inputDash"
								value="<?php echo isset($employee['name'])? $employee['name'] : ""?>" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="job_title">Job Title</label>
						<div class="col-sm-9">
							<input type="text" placeholder="" id="job_title"
								class="form-control inputDash"
								value="<?php echo isset($employee['designation']) ? $employee['designation'] : ""?>" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="dept">Department</label>
						<div class="col-sm-9">
							<input type="text" placeholder="" id="dept"
								class="form-control inputDash"
								value="<?php echo isset($employee['dept_name']) ? $employee['dept_name'] : ""?>" readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="emp_name">Period of
							Evaluation</label>
						<div class="col-sm-9">
							<div class='row'>
								<div class="col-sm-10">
									<label class="control-label" for="eve_from">From <input
										type="text" placeholder="" name='eve_from' id="eve_from" class="inputDash"
										value="<?php if(isset($evaluation['eve_from'])) echo $evaluation['eve_from']; else  echo date('Y',strtotime('-1 year')).'-'.$period_evaluation['from'];?>" <?php echo $m_disabled?> ></label>
									<label class="control-label" for="eve_to">To <input
										type="text" id="eve_to" name ="eve_to" class=" inputDash"
										value="<?php if(isset($evaluation['eve_to'])) echo $evaluation['eve_to']; else echo date('Y').'-'.$period_evaluation['to'];?>" <?php echo $m_disabled?> ></label>
								</div>
							</div>

						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="emp_name">Time in
							Current Position</label>
						<div class="col-sm-9">
							<div class='row'>
								<div class="col-xs-9 btn-group">
									<label class="control-label normal" for="pos_year"> <select
										class="selectpicker" id="pos_year" data-width="auto" <?php echo $m_disabled?> >
											<?php
											echo "<option val='0'>0</option>";
											for ($i=1; $i<$noOfYears; $i++){
											    if(!empty($employee['date'])){
											        if($i == $year){
											            
											            echo "<option val='$i' selected='selected'>$i</option>";
											            continue;
											        }												            
											    }    
											    echo "<option val='$i'>$i</option>";
											}?>
									</select> Year(s)
									</label> <label class="control-label normal" for="pos_month">
										<select class="selectpicker" id="pos_month" data-width="auto" <?php echo $m_disabled?> >
											<?php
											echo "<option val='0'>0</option>";
											for ($i=1; $i<12; $i++){
											    
											    if(!empty($employee['date'])){
											        if($i == $month){
											            echo "<option val='$i' selected='selected'>$i</option>";
											            continue;
											        }
											    }
											    echo "<option val='$i'>$i</option>";
											}?>
									</select> Month(s)
									</label> <label class="control-label normal" for="pos_day"> <select
										class="selectpicker" id="pos_day" data-width="auto" <?php echo $m_disabled?> >
											<?php
											echo "<option val='0'>0</option>";
										    for ($i=1; $i<30; $i++){
											    if(!empty($employee['date'])){
											        if($i == $day){
											            echo "<option val='$i' selected='selected'>$i</option>";
											            continue;
											        }
											    }
											    echo "<option val='$i'>$i</option>";
											}?>
									</select> Day(s)
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="time_org">Time with
							the Organization</label>
						<div class="col-sm-9">
							<input type="text" placeholder="" id="time_org"
								class="form-control inputDash"
								value="<?php echo $diff->format('%y years %m months and %d days');?>"
								readonly>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="status">Employee
							Status</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($status_array as $key=>$val){
								    if(empty($key)) continue;									    
                                    echo "<label class='radio-inline'>";
                                    $status_key = isset($employee['status']) ? $employee['status'] : "";
                                    if($status_key == $key ){
                                        echo "<input type='radio'  value='$key' checked /> ";
                                        echo "$val</label>";
                                        continue;
                                    }
                                    echo "<input type='radio' value='$key' disabled/> ";
                                    echo "$val</label>";
                                }?>							

							</div>
						</div>
					</div>


					<h3>PART I - INSTRUCTIONS TO RATER</h3>
					<p>Listed below are five performance factors, seven behavioral
						traits, and six supervisory factors that are important in the
						performance of the employee's job. Performance factors and
						behavioral traits must be utilized for all employees. The
						supervisor factors should be utilized only for employees with
						supervisory responsibilities. NOTE: A rating of Unacceptable (1),
						Needs Improvement (2) or Superior (5) requires comments. The
						"overall performance" evaluation should reflect the employee's
						total performance, including the performance factors as related
						to the employee's responsibilities and duties as set forth in the
						job description, behavioral traits and supervisory factors, if
						applicable.</p>

					<table class='table table-bordered'>
						<tbody>
							<tr>
								<td><b>DISTRIBUTION INSTRUCTIONS</b></td>
								<td>
									<ol>
										<li>Return the original form to the HR Department</li>
										<li>Maintain one copy for your departmental records.</li>
										<li>Distribute one copy to the employee</li>
									</ol>
								</td>
							</tr>
							<tr>
								<td><b>MARKING INSTRUCTIONS</b></td>
								<td>
									<ol>
										<li>The supervisor should indicate the employee's performance
											by using check box next to the appropriate level of
											performance.</li>
									</ol>
								</td>
							</tr>
						</tbody>
					</table>
					<br>
					<p>The following rating scale guide is being provided to assist
						the evaluator in assigning the most appropriate measurement of
						the employees' performance factors, behavioral traits and
						supervisory factors.</p>

					<table>
						<tbody>
							<tr>
								<td valign="top">1&nbsp;&nbsp;&nbsp;</td>
								<td valign="top">=&nbsp;&nbsp;&nbsp;</td>
								<td><b>UNACCEPTABLE</b> - Consistently fails to meet job
									requirements; performance clearly below minimum requirements.
									Immediate improvement required to maintain employment.</td>
							</tr>
							<tr>
								<td valign="top">2</td>
								<td valign="top">=</td>
								<td><b>NEEDS IMPROVEMENT</b> - Occasionally fails to meet job
									requirements; performance must improve to meet expectations of
									position.</td>
							</tr>
							<tr>
								<td valign="top">3</td>
								<td valign="top">=</td>
								<td><b>MEETS EXPECTATIONS</b> - Able to perform 100% of job
									duties satisfactorily. Normal guidance and supervision are
									required.</td>
							</tr>
							<tr>
								<td valign="top">4</td>
								<td valign="top">=</td>
								<td><b>EXCEEDS EXPECTATIONS</b> - Frequently exceeds job
									requirements; all planned objectives were achieved above the
									established standards and accomplishments were made in
									unexpected areas as well.</td>
							</tr>
							<tr>
								<td valign="top">5</td>
								<td valign="top">=</td>
								<td><b>SUPERIOR</b> - Consistently exceeds job requirements;
									this is the highest level of performance that can be attained.
								</td>
							</tr>
						</tbody>
					</table>

											
					<?php } else if($page == 2) {?>
					<!-- page 2 -->
					<h3>PART II - PERFORMANCE FACTORS</h3>
					<p>
						<label>1. Knowledge, Skills, Abilities: </label> Consider the
						degree to which the employee exhibits the required level of job
						knowledge and/or skills to perform the job and this employee's
						use of established techniques, materials and equipment as they
						relate to performance.
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
							    <?php foreach ($points as $val){
							        echo "<label class='radio-inline'> <input type='radio' name='ksa' value='$val' ";
							        if(isset($evaluation['ksa']) && $evaluation['ksa'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";
								        
								}?>
                                <label class="radio-inline"> Custom Value: 
                                <input type="number" id='ksa_f' name="ksa_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> <?php echo $m_disabled;?> <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['ksa']) && isCustomEvVal($evaluation['ksa']) ? $evaluation['ksa'] : ""; ?>">
								</label>


							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="ksa_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="ksa_comments" name='ksa_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['ksa_comments'])) echo $evaluation['ksa_comments']?></textarea>
						</div>
					</div>

					<p>
						<label>2. Quality of Work: </label> Does the employee complete
						assignments meeting quality standards? Consider accuracy,
						neatness, thoroughness and adherence to standards and safety
						rules.
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label text-left" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'> <input type='radio' name='qlw' value='$val' ";
							        if(isset($evaluation['qlw']) && $evaluation['qlw'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";
								        
								}?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" id ='qlw_f' name="qlw_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['qlw']) && isCustomEvVal($evaluation['qlw']) ? $evaluation['qlw'] : ""; ?>">
								</label>


							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="qlw_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="qlw_comments" name='qlw_comments'  
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['qlw_comments']))echo $evaluation['qlw_comments']?></textarea>
						</div>
					</div>

					<p>
						<label>3. Quantity of Work: </label> Consider the results of this
						employee's efforts. Does the employee demonstrate the ability to
						manage several responsibilities simultaneously; perform work in a
						productive and timely manner; meet work schedules?
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
							
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='qtw' value='$val' ";
							        if(isset($evaluation['qtw']) && $evaluation['qtw'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";
								        
								}?>
                                <label class="radio-inline"> Custom Value: <input
									type="number" id='qtw_f' name="qtw_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['qtw']) && isCustomEvVal($evaluation['qtw']) ? $evaluation['qtw'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="qtw_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="qtw_comments" name='qtw_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['qtw_comments'])) echo $evaluation['qtw_comments']?></textarea>
						</div>
					</div>

					<p>
						<label>4. Work Habits: </label> To what extent does the employee
						display a positive, cooperative attitude toward work assignments
						and requirements? Consider compliance with established work rules
						and organizational policies.
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
							    <?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='wh' value='$val' ";
							        if(isset($evaluation['wh']) && $evaluation['wh'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";
							        
							    }?>
								<label class="radio-inline"> Custom Value: <input
									type="number" id ='wh_f' name="wh_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['wh']) && isCustomEvVal($evaluation['wh']) ? $evaluation['wh'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="wh_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="wh_comments" name = 'wh_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['wh_comments'])) echo $evaluation['wh_comments']?></textarea>
						</div>
					</div>

					<p>
						<label>5. Communication: </label> Consider job related
						effectiveness in dealing with others. Does the employee express
						ideas clearly both orally and in writing, listen well and respond
						appropriately?
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='com' value='$val' ";
							        if(isset($evaluation['com']) && $evaluation['com'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
								<label class="radio-inline"> Custom Value: <input
									type="number" id='com_f' name="com_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['com']) && isCustomEvVal($evaluation['com']) ? $evaluation['com'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="com_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="com_comments" name='com_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['com_comments'])) echo $evaluation['com_comments']?></textarea>
						</div>
					</div>
                    
                    <?php } else if($page == 3) {?>

					<!-- Page 3 -->
					<h3>PART III - BEHAVIORAL TRAITS</h3>
					<p>
						<label>1. Dependability: </label> Consider the amount of time
						spent directing this employee. Does the employee monitor projects
						and exercise follow-through; adhere to time frames; is on time
						for meetings and appointments; and responds appropriately to
						instructions and procedures?
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='dep' value='$val' ";
							        if(isset($evaluation['dep']) && $evaluation['dep'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="dep_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['dep']) && isCustomEvVal($evaluation['dep']) ? $evaluation['dep'] : ""; ?>">
								</label>


							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="dep_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="dep_comments" name= 'dep_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['dep_comments'])) echo $evaluation['dep_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>2. Cooperation: </label> How well does the employee work
						with co-workers and supervisors as a contributing team member?
						Does the employee demonstrate consideration of others; maintain
						rapport with others; help others willingly?
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label text-left" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='coo' value='$val' ";
							        if(isset($evaluation['coo']) && $evaluation['coo'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="coo_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['coo']) && isCustomEvVal($evaluation['coo']) ? $evaluation['coo'] : ""; ?>">
								</label>


							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="coo_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="coo_comments" name='coo_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['coo_comments'])) echo $evaluation['coo_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>3. Initiative: </label> Consider how well the employee
						seeks and assumes greater responsibility, monitors projects
						independently, and follows through appropriately.
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='ini' value='$val' ";
							        if(isset($evaluation['ini']) && $evaluation['ini'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="ini_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['ini']) && isCustomEvVal($evaluation['ini']) ? $evaluation['ini'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="ini_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="ini_comments" name='ini_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['ini_comments'])) echo $evaluation['ini_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>4. Adaptability: </label> Consider the ease with which the
						employee adjusts to any change in duties, procedures, supervisors
						or work environment. How well does the employee accept new ideas
						and approaches to work, respond appropriately to constructive
						criticism and to suggestions for work improvement?
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='ada' value='$val' ";
							        if(isset($evaluation['ada']) && $evaluation['ada'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="ada_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['ada']) && isCustomEvVal($evaluation['ada']) ? $evaluation['ada'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="ada_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="ada_comments" name='ada_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['ada_comments'])) echo $evaluation['ada_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>5. Judgment: </label> Consider how well the employee
						effectively analyzes problems, determines appropriate action for
						solutions, and exhibits timely and decisive action; thinks
						logically.
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='jud' value='$val' ";
							        if(isset($evaluation['jud']) && $evaluation['jud'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="jud_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['jud']) && isCustomEvVal($evaluation['jud']) ? $evaluation['jud'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="jud_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="jud_comments" name='jud_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['jud_comments'])) echo $evaluation['jud_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>6. Attendance:</label> Consider number of absences, use of
						annual and sick leave in accordance with Genuity Systems Ltd.
						policy.
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='att' value='$val' ";
							        if(isset($evaluation['att']) && $evaluation['att'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="att_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['att']) && isCustomEvVal($evaluation['att']) ? $evaluation['att'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="att_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="att_comments" name='att_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['att_comments'])) echo $evaluation['att_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>7. Punctuality: </label> Consider work arrival and
						departure in accordance with departmental and Company Policy.
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='pun' value='$val' ";
							        if(isset($evaluation['pun']) && $evaluation['pun'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="pun_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['pun']) && isCustomEvVal($evaluation['pun']) ? $evaluation['pun'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="pun_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="pun_comments" name='pun_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['pun_comments'])) echo $evaluation['pun_comments'] ?></textarea>
						</div>
					</div>
					<!--End Page 3 -->
                    
                    <?php } else if($page == 4){?>
                    <label class='rating'>Average Rating: <span><?php if(isset($evaluation['avg_rate'])) echo $evaluation['avg_rate']?></span></label>
                    <input type='hidden' name='avg_rate' value="<?php if(isset($evaluation['avg_rate'])) echo $evaluation['avg_rate']?>">
					<!-- Page 4 -->
					<h3>PART IV - SUPERVISORY FACTORS</h3>
					<p>
						<label>1. Leadership: </label> Consider how well the employee
						demonstrates effective supervisory abilities; gains respect and
						cooperation; inspires and motivates subordinates; directs work
						group toward common goal.
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='led' value='$val' ";
							        if(isset($evaluation['led']) && $evaluation['led'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="led_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['led']) && isCustomEvVal($evaluation['led']) ? $evaluation['led'] : ""; ?>">
								</label>


							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="led_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="led_comments" name="led_comments"
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['led_comments'])) echo $evaluation['led_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>2. Delegation: </label> How well does the employee
						demonstrate the ability to direct others in accomplishing work;
						effectively select and motivate staff; define assignments;
						oversee the work of subordinates?
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label text-left" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='del' value='$val' ";
							        if(isset($evaluation['del']) && $evaluation['del'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="del_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['del']) && isCustomEvVal($evaluation['del']) ? $evaluation['del'] : ""; ?>">
								</label>


							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="del_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="del_comments" name='del_comments'
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['del_comments'])) echo $evaluation['del_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>3. Planning and Organizing:</label> Consider how well the
						employee plans and organizes work; coordinates with others, and
						establishes appropriate priorities; anticipates future needs;
						carries out assignments effectively.
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='pla' value='$val' ";
							        if(isset($evaluation['pla']) && $evaluation['pla'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="pla_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['pla']) && isCustomEvVal($evaluation['pla']) ? $evaluation['pla'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="pla_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="pla_comments" name = "pla_comments"
								class="form-control"  <?php echo $m_disabled;?>><?php if(isset($evaluation['pla_comments'])) echo $evaluation['pla_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>4. Administration: </label> How well does the employee
						perform day-to-day administrative tasks; manage time; administer
						policies and implement procedures; maintain appropriate contact
						with supervisor and utilize funds, staff or equipment?
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='adm' value='$val' ";
							        if(isset($evaluation['adm']) && $evaluation['adm'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="adm_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['adm']) && isCustomEvVal($evaluation['adm']) ? $evaluation['adm'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="adm_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="adm_comments" name="adm_comments"
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['adm_comments'])) echo $evaluation['adm_comments'] ?></textarea>
						</div>
					</div>

					<p>
						<label>5. Personnel Management: </label> Consider how well the
						employee serves as a role model; provides guidance and
						opportunities to their staff for their development and
						advancement; resolves work-related employee problems; assists
						subordinates in accomplishing their work-related objectives. Does
						the employee communicate well with subordinates in a clear,
						concise, accurate, and timely manner and make useful suggestions?
					</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='per' value='$val' ";
							        if(isset($evaluation['per']) && $evaluation['per'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="per_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['per']) && isCustomEvVal($evaluation['per']) ? $evaluation['per'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="per_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="per_comments" name="per_comments"
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['per_comments'])) echo $evaluation['per_comments'] ?></textarea>
						</div>
					</div>
					<!--End Page 4 -->
                    
                    <?php } else if($page == 5){?>

					<!-- Page 5 -->

					<label class='rating'>Average Rating: <span><?php if(isset($evaluation['avg_rate'])) echo $evaluation['avg_rate']?></span></label>
                    <input type='hidden' name='avg_rate' value="<?php if(isset($evaluation['avg_rate'])) echo $evaluation['avg_rate']?>">
                    
					<h3>PART V - OVERALL PERFORMANCE</h3>
					<p>Please use this space to describe the overall performance
						rating. The overall rating should be a reflection of the
						performance factors, behavioral traits and supervisory factors.</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<?php foreach ($points as $val){
							        echo "<label class='radio-inline'><input type='radio' name='opr' value='$val' ";
							        if(isset($evaluation['opr']) && $evaluation['opr'] == $val) echo " checked ";
							        echo $m_disabled;
							        echo " /> $val</label>";								        
							    }?>
							    <label class="radio-inline"> Custom Value: <input
									type="number" name="opr_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['opr']) && isCustomEvVal($evaluation['opr']) ? $evaluation['opr'] : ""; ?>">
								</label>


							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="opr_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="opr_comments" name="opr_comments"
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['opr_comments'])) echo $evaluation['opr_comments'] ?></textarea>
						</div>
					</div>

					<h3>PART VI - HUMAN BEING FACTORS</h3>
					<p>Consider the degree of human personality that how he or she is
						received to his colleagues in regard of Social Behavior.</p>
					<div class="form-group rating">
						<label class="col-sm-2 control-label" for="emp_name">Rating</label>
						<div class="col-sm-9">
							<div class="form-inline">
								<label class="radio-inline"><input type="radio"
								name="status" value="1" <?php if(isset($evaluation['hbf']) && $evaluation['hbf'] == 1) echo " checked "; echo $m_disabled; ?> /> 1 (Unsocial)</label>
								<label
								class="radio-inline"><input type="radio" name="hbf"
								value="2" <?php if(isset($evaluation['hbf']) && $evaluation['hbf'] == 2) echo " checked "; echo $m_disabled; ?> /> 2 (Almost Social)</label> 
								<label
								class="radio-inline"><input type="radio" name="hbf"
								value="3" <?php if(isset($evaluation['hbf']) && $evaluation['hbf'] == 3) echo " checked "; echo $m_disabled;?> /> 3 (Social)</label> 
								<label class="radio-inline"><input
								type="radio" name="hbf" value="4" <?php if(isset($evaluation['hbf']) && $evaluation['hbf'] == 4) echo " checked "; echo $m_disabled; ?> /> 4 (More than
								Social)</label> 
								<label class="radio-inline"> <input
									type="radio" name="hbf" value="5" <?php if(isset($evaluation['hbf']) && $evaluation['hbf'] == 5) echo " checked "; echo $m_disabled; ?> /> 5 (Superior)
								</label> <label class="radio-inline"> Custom Value: <input
									type="number" name="hbf_f" step="0.1" min="1" max="5" <?php echo $m_disabled;?> value="<?php echo !empty($evaluation['hbf']) && isCustomEvVal($evaluation['hbf']) ? $evaluation['hbf'] : ""; ?>">
								</label>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="hbf_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="hbf_comments" name ="hbf_comments"
								class="form-control" <?php echo $m_disabled;?>><?php if(isset($evaluation['hbf_comments'])) echo $evaluation['hbf_comments'] ?></textarea>
						</div>
					</div>


					<h3>PART VII - SIGNATURE OF MANAGER</h3>

					<div class="form-group rating">
						<div class='row '>
							<div class="col-sm-12 ">
								<label class="col-sm-5 text-left " for="emp_name">Rater: <input
									type="text" id="" name="" class=" inputDash"
									value="<?php if(isset($evaluation['manager_name'])) echo $evaluation['manager_name']; else if($isManager) echo $myInfo->userName;?>" placeholder="" readonly></label> 
									<input type="hidden" name="manager_id" value="<?php echo $myInfo->userId?>">
									<label
									class="col-sm-4" for="man_sig_date">Date: <input type="text"
									id="man_sig_date" name="man_sig_date" class=" inputDash" value="<?php if(isset($evaluation['man_sig_date'])) echo $evaluation['man_sig_date']; else echo date('Y-m-d'); ?>"
									placeholder="" readonly></label>
							</div>
						</div>
					</div>


					<h3>PART VIII - TO THE EMPLOYEE</h3>
					<p>I have been advised of my performance ratings. I have discussed
						the contents of this review with my Manager. My signature does
						not necessarily imply agreement. My comments are as follows
						(optional) (attach additional sheets if necessary):</p>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="emp_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="emp_comments" name="emp_comments" <?php echo $e_disabled?>
								class="form-control" ><?php if(isset($evaluation['emp_comments'])) echo $evaluation['emp_comments'] ?></textarea>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="emp_attachment">Attachment</label>
						<?php if(empty($e_disabled) && isset($evaluation['emp_attachment']) && empty($evaluation['emp_attachment'])){ ?>							
						<div class="col-sm-5">
							<input type="file" class="" id="emp_attachment" name="emp_attachment" <?php echo $e_disabled?> > 
						</div>
						<div class='col-sm-3'>
							<p class="help-block">( If necessary )</p>
						</div>        
						<?php } else{
						    echo "<div class='col-sm-10'>";
						    
						    if(isset($evaluation['emp_attachment']) && !empty($evaluation['emp_attachment'])){
						        echo $evaluation['emp_attachment'].'&nbsp;';
						        echo "<a type ='button' href = '".base_url()."evaluation/download/".$evaluation['emp_attachment']."' class='btn btn-xs btn-primary downFile' title='Download'>Download</a>";
						    }
						    echo "</div>";    
						} ?>
						
					</div>
					<div class="form-group rating">
						<div class='row '>
							<div class="col-sm-12 ">
								<label class="col-sm-5 text-left" for="emp_name">Signature: <input
									type="text" id="emp_name" name="emp_name" class="inputDash" <?php echo $e_disabled?>
									value="<?php if(isset($evaluation['employee_name'])) echo $evaluation['employee_name']?>" placeholder="" readonly></label>
									
								<label class="col-sm-4" for="emp_sig_date">Date: <input type="text" <?php echo $e_disabled?>
								id="emp_sig_date" name="emp_sig_date" class=" inputDash" 
								value="<?php echo !empty($evaluation['emp_sig_date']) ? $evaluation['emp_sig_date'] : isset($evaluation['employee_name']) ? date('Y-m-d') : ""; ?>" placeholder="" readonly></label>
							</div>
						</div>
					</div>

					<h3>PART IX - VERIFICATION BY ADMIN</h3>
					
					<div class="form-group">
						<label class="col-sm-2 control-label" for="admin_comments">Comments</label>
						<div class="col-sm-10">
							<textarea placeholder="enter..." id="admin_comments" name="admin_comments" 
								class="form-control" <?php echo $a_disabled?>><?php if(isset($evaluation['admin_comments'])) echo $evaluation['admin_comments'] ?></textarea>
						</div>
					</div>
					<div class="form-group rating">
						<div class='row '>
							<div class="col-sm-12 ">
								<label class="col-sm-5 text-left" for="emp_name">Signature: <input
									type="text" id="emp_name" name="emp_name" class=" inputDash" <?php echo $a_disabled?>
									value="<?php if(isset($evaluation['admin_name'])) echo $evaluation['admin_name']; else if(empty($a_disabled)) echo $myInfo->userName?>" placeholder="" readonly></label> 
								
								<label class="col-sm-4" for="emp_name">Date: <input type="text" <?php echo $a_disabled?>
								id="admin_sig_date" name="admin_sig_date" class=" inputDash" value="<?php if(isset($evaluation['admin_sig_date'])) echo $evaluation['admin_sig_date']; else if($a_disabled == "") echo date('Y-m-d')?>"
								placeholder="" readonly></label>
							</div>
						</div>
					</div>
					<!-- end page5 -->												
                    <?php }?>
				</div>
				<!-- /.box-body -->
				<div class="box-footer">
					<nav>
						<ul class="pagination pagination-lg">
						    <?php
						    if($page>1)
						        echo "<li><a class='' id ='prevBtn' href='".base_url()."evaluation/form/$eid/".($page-1)."/$insert_id' aria-label='Previous'> <span aria-hidden='true'>&laquo;</span> Previous</a></li>";
						        
						    if($page == 5) {
						        
						        if($eid != 'nil'){
						            if($isManager){
						                if (!empty($evaluation['emp_sig_date']) && $evaluation['status'] == 'C'){
						                    echo "<li><a class='btn btn-warning' id='mngProceedBtn' href='#' ><b>Evaluation Proceeded</b></a></li>";
						                } elseif (empty($evaluation['admin_id']) && !empty($evaluation['status']) && $evaluation['status'] == 'B'){
						                    echo "<li><a class='btn btn-warning' id='confirmBtn' href='#' ><b>Finally Confirm</b></a></li>";
						                } else{
						                    echo "<li><a class='btn btn-info' id='saveBtn' href='#' ><b>Save as Draft</b></a></li>";
						                    echo "<li><a class='btn btn-warning' id='sendBtn' href='#' ><b>Send</b></a></li>";
						                }
						            }elseif ($isAdmin){
						                echo "<li><a class='btn btn-info' id='approveBtn' href='#' ><b>Approve</b></a></li>";
						            }else {
						                if (isset($evaluation['status']) && $evaluation['status'] == 'A'){
						                    echo "<li><a class='btn btn-warning' id='disputeBtn' href='#' ><b>Have a Dispute</b></a></li>";
						                    echo "<li><a class='btn btn-info' id='proceedBtn' href='#' ><b>Proceed</b></a></li>";
						                }
						            }						            
						        }							        
						    }else							       
						        echo "<li><a class='' id='nextBtn' href='".base_url()."evaluation/form/$eid/".($page+1)."/$insert_id' aria-label='Next'>Next <span aria-hidden='true'>&raquo;</span></a></li>";
						    ?>								
						</ul>
					</nav>
				</div>
				<!-- /.box-footer -->
			</form>
		</div>
	</div>
</div>

<?php if($page == 5){?>
<link href="<?php echo base_url();?>assets/css/progress.css" type="text/css" rel="stylesheet" />
<?php }?>
<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>
<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js" type="text/javascript"></script>

<script type="text/javascript">

var page = <?php echo $page?>; 
var eid = "<?php echo $eid ?>";
var myId = "<?php echo $myInfo->userId ?>";
var insert_id = "<?php echo !empty($insert_id)?$insert_id:"";?>";

$(document).ready(function(){

	$("#eve_from, #eve_to, #man_sig").datepicker({
	    format: 'yyyy-mm-dd'
	});
	$('#eve_from, #eve_to, #man_sig').on('changeDate', function(ev){
	    $(this).datepicker('hide');
	});

// 	$('#nextBtn').click(function(){
//	var url = "<?php //echo base_url()?>evaluation/form/"+eid+"/"+(page+1);
// 		$('#eval_form').attr('action', url).submit();
// 	});


// 	if($("input[type='number']").val()){
// 		console.log(this);
// 	}

	$( "input[type='number']" ).change(function() {
		//console.log(this);
		var checkboxs = $(this).parents('div').find('radio-inline');
		console.log(checkboxs);
	});

    <?php if(empty($m_disabled )) { ?>
	$("#nextBtn").on("click", function(e){

	    e.preventDefault();
		var url = "<?php echo base_url()?>evaluation/form/"+eid+"/"+(page+1)+"/"+insert_id;
 		$('#eval_form').attr('action', url).submit();
	});

	$("#saveBtn").on("click", function(e){
	    e.preventDefault();
		var url = "<?php echo base_url()?>evaluation/save/"+eid+"/"+insert_id;
 		$('#eval_form').attr('action', url).submit();
	});
	
	$("#sendBtn").on("click", function(e){
	    e.preventDefault();
		var url = "<?php echo base_url()?>evaluation/send/"+eid+"/"+insert_id;
 		$('#eval_form').attr('action', url).submit();
 		$("body").append("<div class='loader'>Please wait&hellip;</div>");
    	$('a').attr('disabled','disabled'); 
	});	

<?php }
if(empty($e_disabled )) { ?>
    $("#proceedBtn").on("click", function(e){        	
            e.preventDefault();
        	var url = "<?php echo base_url()?>evaluation/proceed/"+eid+"/"+insert_id;
        	$('#eval_form').attr('action', url).submit();
        	$("body").append("<div class='loader'>Please wait&hellip;</div>");
        	$('a').attr('disabled','disabled'); 
    });

    $("#disputeBtn").on("click", function(e){
        e.preventDefault();
        var empComments = $('#emp_comments').val();
        if(typeof empComments === 'undefined' || empComments == null || empComments == ""){
            alert("Please set your comments");
            return false;
        }else{
        	var url = "<?php echo base_url()?>evaluation/dispute/"+eid+"/"+insert_id;
        	$('#eval_form').attr('action', url).submit();
        	$("body").append("<div class='loader'>Please wait&hellip;</div>");
        	$('a').attr('disabled','disabled'); 
        }
    });
    	
<?php }
if(empty($a_disabled )) { ?>
    $("#approveBtn").on("click", function(e){    
        e.preventDefault();
    	var url = "<?php echo base_url()?>evaluation/approve/"+eid+"/"+insert_id;
    	$('#eval_form').attr('action', url).submit();
    	$("body").append("<div class='loader'>Please wait&hellip;</div>");
    	$('a').attr('disabled','disabled'); 
    });
    
<?php } ?>

    
    $("#confirmBtn").on("click", function(e){
    	e.preventDefault();        
    	var url = "<?php echo base_url()?>evaluation/confirm/"+eid+"/"+insert_id;
    	$('#eval_form').attr('action', url).submit();
    	$("body").append("<div class='loader'>Please wait&hellip;</div>");
    	$('a').attr('disabled','disabled');  	
    });

    $("#mngProceedBtn").on("click", function(e){
	    e.preventDefault();
		var url = "<?php echo base_url()?>evaluation/protoadmin/"+eid+"/"+insert_id;
 		$('#eval_form').attr('action', url).submit();
 		$("body").append("<div class='loader'>Please wait&hellip;</div>");
    	$('a').attr('disabled','disabled'); 
	});
	
});
</script>