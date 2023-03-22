<link href="<?php echo base_url()?>assets/css/roster.css"
	type="text/css" rel="stylesheet" />

<link href="<?php echo base_url();?>assets/css/main.css" type="text/css"
	rel="stylesheet" />
<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap-select.css"
	type="text/css" rel="stylesheet" />

<link
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css"
	type="text/css" rel="stylesheet" />	

<div class="box box-primary">
	<div class="box-header">
		<div class="col-xs-12 upper-padding">
			<form class="form-horizontal" id='searchForm'
				action="<?php echo base_url()?>roster/show" method='post'>
				<div class='row'>
                <?php if(count($departmentLists) > 0) { ?>
                    <div class='col-md-3 col-sm-6'>
						<div class="form-group">
							<label class="col-sm-4 control-label" for="select_dept">Department</label>
							<div class="col-sm-8">
								<select name="select_dept" class="selectpicker"
									id='select_dept' data-width="100%" data-live-search="true">
									<option value=''>---Select Department---</option>
                    			<?php foreach ($departments as $dept_code=>$dept_name) {
                    			    if($sel_dept == $dept_code)
                    			        echo "<option value='".$dept_code."' selected='selected'>".$dept_name."</option>";
                    				else
                        			    echo "<option value='".$dept_code."'>".$dept_name."</option>";
                    		    } ?> 
                    		    </select>
							</div>
						</div>

					</div>
                <?php }?>
                <?php if(count($staff_array) > 0) { ?>
                    <div class='col-md-3 col-sm-6'>
						<div class="form-group">
							<label class="col-sm-3 control-label" for="select_staff">Staff</label>
							<div class="col-sm-9">
								<select name="select_staff" class="selectpicker"
									id='select_staff' data-width="100%" data-live-search="true"
									required>
									<option value=''>---Select Staff---</option>
                    			<?php foreach ($staff_array['all'] as $obj) {
                    			    if($sel_emp_id == $obj->emp_id )
                    			        echo "<option value='".$obj->emp_id."' selected='selected' >".$obj->emp_id." - ".$obj->name."</option>";
                    				else 
                    			        echo "<option value='".$obj->emp_id."'>".$obj->emp_id." - ".$obj->name."</option>";
                    		    } ?>
                    		    </select>
							</div>
						</div>
					</div>
                <?php }?>    
                    <div class='col-md-6 col-sm-12'>
						<div class='row'>
							<div class='col-sm-4'>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="dateFrom">From</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="dateFrom"
											name="dateFrom" placeholder="yyyy-mm-dd"
											value="<?php echo $sel_sdate?>" required>
									</div>
								</div>
							</div>
							<div class='col-sm-4'>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="dateTo">To</label>
									<div class="col-sm-9">
										<input type="text" class="form-control" id="dateTo"
											name="dateTo" placeholder="yyyy-mm-dd"
											value="<?php echo $sel_edate?>" required>
									</div>
								</div>
							</div>
							<div class='col-sm-4'>
								<input type='submit' class='btn btn-primary' name='search'
									value='Search'>
							</div>
						</div>

					</div>
				</div>
			</form>

		</div>

	</div>
	<div class="box-body">
	
        <?php if($isNonRoster){ ?>
        
            <table class="col-xs-12 table-bordered table-striped table-condensed text-center">
    			<thead>
    				<tr>
    					<th>Date</th>
    					<th>Day</th>
    					<th>From</th>
    					<th>To</th>
    				</tr>
    			</thead>
    			<tbody>
    			<?php
				//var_dump($officeSchedules);
    			    foreach ($officeSchedules as $date=>$ary){
    			        
    			        
    			        echo "<tr><td>";
    			        echo $date;
    			        echo "</td><td>";
    			        echo date('l', strtotime($date));
    			        echo "</td>";
    			        
    			        if(isset($ary['weekend'])){
    			            
    			            echo "<td colspan = '2' class='text-success'>".$ary['weekend']."</td>";
    			        }else{
    			            
    			            echo "<td>";
    			            echo date('h:i a', strtotime($ary['from']));
    			            echo "</td><td>";
    			            echo date('h:i a', strtotime($ary['to']));
    			            echo "</td>";
    			        }
        	        }
    			?>
    
    			</tbody>
    		</table>
    		
    		
        <?php } elseif($isRegRoster) { ?>
    		<table
    			class="col-xs-12 table-bordered table-striped table-condensed text-center">
    			<thead>
    				<tr>
    					<th>Date</th>
    					<th>Day</th>
    					<th>From</th>
    					<th>To</th>
    				</tr>
    			</thead>
    			<tbody id='tBody'>
    			<?php						
    			    $firstItem = reset($rosterData);
    			    $lastItem = end($rosterData);
    			    
    			    $startTime = strtotime(date('Y-m-d', strtotime($firstItem['stime'])));
    			    $endTime =  strtotime(date('Y-m-d', strtotime($lastItem['stime'])));
    			    
    			    for($idate=$sel_sdate; $idate<=$sel_edate; ) {
    			        
    			        echo "<tr><td>";
    			        echo date('Y-m-d', strtotime($idate));
    			        echo "</td><td>";
    			        echo date('l', strtotime($idate));
    			        echo "</td>";						        
    			        
    			        $rowTime = strtotime($idate);
    			        
    			        if( $rowTime >= $startTime && $rowTime <= $endTime){
    			            // working or weekend day
    			            $rosterAry = isset($rosterData[$idate]) ? $rosterData[$idate] : array();
    			            
    			            if(isset($rosterAry) && count($rosterAry)>0){
    			                // working day
    			                
    			                echo "<td>";
    			                echo date('h:i a', strtotime($rosterAry['stime']));
    			                echo "</td><td>";
    			                echo date('h:i a', strtotime($rosterAry['etime']));
    			                echo "</td>";
    			            
    			            }else{
    			                
    			                //weekend day
    			                if(isset($weekendData[$idate]) && $weekendData[$idate]){
    			                    echo "<td colspan = '2' class='text-success'>Weekend</td>";
    			                }
    			                						            
    			            }
    			            
    			        } else {
    			            
    			            // undefined
    			            
    			            echo "<td colspan = '2' class='text-warning'>Not Set</td>";						        
    			        }
    			        echo "</tr>";
    			                        	            
        	            $idate = date("Y-m-d",strtotime($idate." +1 day"));
        	        }
    			?>
    
    			</tbody>
    		</table>
    	<?php } elseif ($isSlotRoster){ ?>
            <div class='col-sm-12'>
                <div class='row'>
                    <?php               
                        $slotCount = count($rosterSlot);             
                    
                        foreach ($rosterSlot as $slotNo=>$array) {
                        	$from = date("h:i a",strtotime($array["from"]));
                        	$to =  date("h:i a",strtotime($array["to"]));
                        	$key = $from." ".$to;
                            
                        	if($slotCount <=3 ){
                        	    echo "<div class='col-sm-3 nopadding'>";
                        	} else if($slotCount >3 ){
                        	    echo "<div class='col-sm-2 nopadding'>";
                        	}
                        	//echo "<div class='col-sm-3 nopadding'>";
                        	echo "<div class='text-head text-center'><b>".$from."</b> to <b>".$to."</b></div>";
                        	
                        	
                        	if(isset($rosterData[$key])){
                        	    
                        	    $firstItemAry = reset($rosterData[$key]);
                        	    $lastItemAry = end($rosterData[$key]);
                        	     
                        	    $firstObj = $firstItemAry[0];
                        	    $lastObj = $lastItemAry[0];
                        	    
                        	    $startTime = strtotime(date('Y-m-d', strtotime($firstObj->stime)));
                        	    $endTime = strtotime(date('Y-m-d', strtotime($lastObj->stime)));                            	     
                        	}else{
                        	    $startTime = 0;
                        	    $endTime = 0;
                        	}
                        	
                        	for ($idate = $sel_sdate; $idate <= $sel_edate;) {
                        	    
                        	    
                        	    $rowTime = strtotime($idate);
                        	    $nexDate = date("Y-m-d", strtotime($idate." +1 day"));
                        	    echo "<div class='box nopadding ".$idate."'>";
    
                        	    //echo $slotNo;
                        	    
                        	    if($slotNo == 1 ){
                        	        echo "<div class='box-header box-header-bg text-center hidden-xs'>".$idate."(<i>".date("D",strtotime($idate))."</i>) &nbsp;&nbsp; to &nbsp;&nbsp; ".$nexDate."(<i>".date("D",strtotime($nexDate))."</i>)</div>";
                        	    }else{
                        	        echo "<div class='box-header box-header-bg hidden-xs'>&nbsp;</div>";
                        	    }
                        	    
    				            echo "<div class='box-header box-header-bg text-center hidden-lg hidden-sm hidden-md'>".$idate."(<i>".date("D",strtotime($idate))."</i>) &nbsp;&nbsp; to &nbsp;&nbsp; ".$nexDate."(<i>".date("D",strtotime($nexDate))."</i>)</div>";
    				            
    				            echo "<div class='box-body '>";
    				            
    				            if( $rowTime >= $startTime && $rowTime <= $endTime){
    				                // working or weekend day
    				                $rosterAry = isset($rosterData[$idate]) ? $rosterData[$idate] : array();
    				                 
    				                if(isset($rosterData[$key][$idate]) && count($rosterData[$key][$idate])> 0){
    				                    // working day
    				                    $staffs = $rosterData[$key][$idate];
    				                    
    				                    echo "<ol>";
    				                    foreach ($staffs as $obj){
    				                        echo "<li><b>".$obj->name."</b> ";
                                            if($obj->is_incharge =='Y')
                                                echo "<span class='text-danger'> (incharge) </span>";
    				                        echo "</li>";
    				                    }
    				                    echo "</ol>";
    				                    
    				                } else {
    				                    //weekend day
    				                    echo "<div class='text-center text-warning'>Not Set</div>";
    				                }
    				                 
    				            } else {						                 
    				                // Not Set						                 
    				                echo "<div class='text-center text-warning'>Not Set</div>";
    				            }
    				            
    				            
    				            echo "</div>";      //box-body                            	    
                        	    echo "</div>";     //box
                        	    
                        	    
                        	    
                        	    $idate = date("Y-m-d", strtotime($idate . " +1 day"));
                        	}    
                        	
                        	echo "</div>";
                        }
                        
                        // Weekend
                        if($slotCount <=3 ){
                            echo "<div class='col-sm-3 nopadding'>";
                        } else if($slotCount >3 ){
                            echo "<div class='col-sm-2 nopadding'>";
                        }
                        echo "<div class='text-head text-center'><b>Weekend</b></div>";
                        
    			        for ($idate = $sel_sdate; $idate <= $sel_edate;) {
                        	    
    			            $nexDate = date("Y-m-d", strtotime($idate." +1 day"));
    			            
                    	    echo "<div class='box nopadding ".$idate."'>";
                    	    echo "<div class='box-header box-header-bg hidden-xs'>&nbsp;</div>";
    			            echo "<div class='box-header box-header-bg text-center hidden-lg hidden-sm hidden-md'>".$idate."(<i>".date("D",strtotime($idate))."</i>) &nbsp;&nbsp; to &nbsp;&nbsp; ".$nexDate."(<i>".date("D",strtotime($nexDate))."</i>)</div>";					            
    			            echo "<div class='box-body'>";
    			            
    			            if(isset($weekendData[$idate])){
    			                echo "<ol>";
    			                foreach ($weekendData[$idate] as $name){
    			                    
    			                    echo "<li><b>".$name."</b></li>";
    			                }
    			                echo "</ol>";
    			                
    			            }else{
    			                //echo "<div class='text-center text-warning'>Not Set</div>";
    			            }					            
    			            
    			            echo "</div>";      //box-body                            	    
                    	    echo "</div>";     //box
                    	    
                    	    $idate = date("Y-m-d", strtotime($idate . " +1 day"));
                    	}
                    	echo "</div>";     // ending div of column
                    ?>
                    
                </div>
            </div>
    
                <?php
                    for ($idate = $sel_sdate; $idate <= $sel_edate;) {
                        
                        $nexDate = date("Y-m-d", strtotime($idate." +1 day"));
                        //echo "<th><td colspan='".count($rosterSlot)."'>".$idate."(<i>".date("l",strtotime($idate))."</i>) &nbsp;&nbsp;&nbsp;&nbsp; to &nbsp;&nbsp;&nbsp;&nbsp; <b>".$nexDate."</b>(<i>".date("l",strtotime($nexDate))."</i>)</th>";
                        
                        $nexDate = date("Y-m-d", strtotime($idate . " +1 day"));
                        
                        foreach ($rosterSlot as $array) {
                            //create column
                            $from = date("h:i:s a", strtotime($array["from"]));
                            $to = date("h:i a", strtotime($array["to"]));
                            
                            $chkid = date("his", strtotime($array["from"]));
                                                           
                            if (isset($rosterData[$idate][$from])) {
                                $staff = $rosterData[$idate][$from];
                                foreach ($staff as $obj) {
                                    
                                    $chk = $obj->is_incharge == "Y" ? "checked" : "";
                                    if ($isManager) {} else {}
                                }
                            }
                            
                            $dtfrom = $idate . " " . $array["from"];
                            $dtto = strtotime($idate . " " . $array["from"]) > strtotime($idate . " " . $array["to"]) ? $nexDate . " " . $array["to"] : $idate . " " . $array["to"];
                        }
                        
                        $idate = date("Y-m-d", strtotime($idate . " +1 day"));
                    }
                ?>
    	<?php }?>
    	<div class='clearfix'></div>	
	</div>
</div>


<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-select.js"
	type="text/javascript"></script>
<script
	src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js"
	type="text/javascript"></script>


<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script
	src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>

<script>
                            		    
var staffs = <?php echo json_encode($staff_array); ?>;
var startDate = "<?php echo $sel_sdate; ?>";
var endDate = "<?php echo $sel_edate; ?>";

$(document).ready(function(){

	var start = new Date(startDate);
    var end = new Date(endDate);

    while(start < end){
    	var date = start.toISOString().substring(0, 10);
    	
    	var heights = $("div."+date).map(function ()
		    {
		        return $(this).height();
		    }).get();

		var maxHeight = Math.max.apply(null, heights);

		$('div.'+date).each(function(){
			$(this).height(maxHeight);
		});	

	    //increment	
        var newDate = start.setDate(start.getDate() + 1);
        start = new Date(newDate);
    }


	

    //Date Correction    
	$('#dateFrom, #dateTo').datepicker({
	 	format: 'yyyy-mm-dd'
	});

	$('#dateFrom').on('changeDate', function(ev){
		$(this).datepicker('hide');				
		$('#dateTo').datepicker('setStartDate',$(this).val());
		
		//displayDate ();
	});	
	$('#dateTo').on('changeDate', function(ev){
	    $(this).datepicker('hide');
	    $('#dateFrom').datepicker('setEndDate',$(this).val());
	    
	    //displayDate ();	    
	});

	
    $("#select_dept").change(function(){
        
        var dept_code = $(this).val();
        $("#select_staff").empty();

        if(dept_code != ""){
        	var selectedStaffs = staffs[dept_code];

        }else{
        	var selectedStaffs = staffs['all'];
        	var option = "<option value=''>---Select Staff---</option>";
            $("#select_staff").append(option);   	    
        }	

        for(i=0; i<selectedStaffs.length; i++) {           
            var emp = selectedStaffs[i];
            if(emp!=null) {
                var option = "<option value='"+emp.emp_id+"'>"+emp.emp_id+" - "+emp.name+"</option>";
                $("#select_staff").append(option);   	    
            }
        }    
       $('#select_staff').selectpicker('refresh');
    });

 	$('#searchForm').validate({ // initialize the plugin
 		ignore: ":not(select:hidden, input:visible, textarea:visible)",
		rules: {
			select_staff: {
				required: true,
			},
			dateFrom: {
			    required: true,
			    date: true
			},
			dateTo: {
			    required: true,
			    date: true
			}
		},
        submitHandler : function(event) {
            return true;
        }
	});
 	
    
});

</script>


<!--  form class="form-horizontal">
		<div class="row">
			<div class='col-sm-4'>
				<div class='form-group'>
					<label class="col-sm-3 control-label" for="sdate">Department:</label>
					<div class="col-sm-9">
						<select class='selectpicker form-control'
							data-live-search='true'>
						</select>
					</div>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group'>
					<label class="col-sm-3 control-label" for="edate">Staff:</label>
					<div class="col-sm-9">
						<select class='selectpicker form-control'
							data-live-search='true'>
						</select>
					</div>
				</div>
			</div>
			<div class='col-sm-4'>
				<div class='form-group'>
					<div class="col-sm-9">
						<input type='submit' name='search' value='Show'
							class="btn btn-primary">
					</div>
				</div>
			</div>
		</div>
	</form -->
