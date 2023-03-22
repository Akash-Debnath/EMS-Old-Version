<link href="<?php echo base_url();?>assets/css/main.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/leave.css" type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/lib/bootstrap/css/datepicker3.css" type="text/css" rel="stylesheet" />

<?php 
$todaySmall = date("Y-m-d") == $today ? "today" : "today($today)";
$todayCaptl = date("Y-m-d") == $today ? "Today" : "Today($today)";
?>
<form class="form-horizontal" id='searchForm' action="<?php echo base_url()?>leave/report" method='post'>
<div class='row' style="margin-bottom: 10px;">
    <div class="col-xs-5"  style="width:120px;">
        <input type="text" class="form-control input-sm" placeholder="yyyy-mm-dd" id="today_date" name="today_date" value="<?php echo $today; ?>" required>
    </div>
    <div class='col-xs-2'>
		<input type='submit' class='btn btn-primary input-sm' name='search' value='Search' style="padding: 4px 12px;">
	</div>
</div>
</form>

<div class="col-sm-12 leaveHeader"><?php echo $todayCaptl; ?> in Leave: <?php echo count($will_join); ?></div>

<div class = 'row'>
   <?php
    if(count($will_join)==0) {
        echo "<div class='noFound'>No one in leave $todaySmall.</div>";
    } else {
    	foreach ($will_join as $ary) {
    	?>
    	<div class="col-xs-12 col-sm-4 col-md-3 leaveBox">
    	   <div class ='squareBox'>
        		<?php echo $ary["emp_id"];?><br>
        		<?php echo "<a href='".base_url()."user/detail/".$ary["emp_id"]."'>".$ary["name"]."</a>";?><br>
        		<?php echo $ary["designation"];?><br>
        		<?php echo $ary["dept_name"];?><br>
        		<div class='squareBoxHeader'>Will join on : 
        		<?php 
        		      $join_date = date("Y-m-d",strtotime($ary["leave_end"]." +1 day"));
    	              echo $join_date;
    	        ?>
        		</div>

        		<?php if($hasPrivilege) { ?>
        		<div align="center" style='padding-top:5px'>
        		  <a class = 'btn btn-info btn-xs' href='<?php echo base_url()."leave/request/$ary[id]"; ?>'>Show Leave</span></a>
        		</div>
        		<?php } ?>
    		</div>
    	</div>
    	<?php
    	}
    }
    ?>
</div>

<div class="col-sm-12 leaveHeader">Joined <?php echo $todayCaptl; ?> after spent their leave: <?php echo count($joined_today); ?></div>

<div class = 'row'>
   <?php
    if(count($joined_today)==0) {
        echo "<div class='noFound'>No one joined $todaySmall.</div>";
    } else {
    	foreach ($joined_today as $ary) {
    	?>
    	<div class="col-xs-12 col-sm-4 col-md-3 leaveBox">
    	   <div class ='squareBox'>
        		<?php echo $ary["emp_id"];?><br>
        		<?php echo "<a href='".base_url()."user/detail/".$ary["emp_id"]."'>".$ary["name"]."</a>";?><br>
        		<?php echo $ary["designation"];?><br>
        		<?php echo $ary["dept_name"];?><br>
        		

        		<?php if($hasPrivilege) { ?>
        		<div align="center" style='padding-top:5px'>
        		  <a class = 'btn btn-info btn-xs' href='<?php echo base_url()."leave/request/$ary[id]"; ?>'>Show Leave</span></a>
        		</div>
        		<?php } ?>
    		</div>
    	</div>
    	<?php
    	}
    }
    ?>
</div>

<script src="<?php echo base_url();?>assets/lib/bootstrap/js/bootstrap-datepicker.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/jquery.validate.min.js"></script>
<script src="<?php echo base_url();?>assets/js/additional-methods.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#today_date').datepicker({
	 	format: 'yyyy-mm-dd'
	});

	$('#today_date').on('changeDate', function(ev){
		$(this).datepicker('hide');
	});	
	
 	$('#searchForm').validate({
 		ignore: ":not(select:hidden, input:visible, textarea:visible)",
		rules: {
			today_date: {
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