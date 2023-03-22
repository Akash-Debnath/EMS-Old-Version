<link href="<?php echo base_url();?>assets/css/main.css"
		type="text/css" rel="stylesheet" />
<link href="<?php echo base_url();?>assets/css/leave.css"
		type="text/css" rel="stylesheet" />

		
<div class="leaveHeader" >Approval Request: <?php echo count($request); ?></div>

<div class ='row'>
   	<?php
    if(count($request)==0) {
    	echo "<div class='noFound'>No approval request found.</div>";
    } else {
    	foreach ($request as $ary) {
    	?>
    	<div class="col-xs-12 col-sm-4 col-md-3 leaveBox">
    	   <div class ='squareBox'>
                <p><a href='<?php echo base_url()."user/detail/".$ary['sender_id']?>'><?php echo $ary['name']?></a> has requested for adding weekends more than as usual. Short brief:</p>

        		<p><label class =''>From: </label> <?php echo $ary["sdate"];?></p>
        		<p><label class =''>To: </label> <?php echo $ary["edate"];?></p>
        		<p><label class =''>Department:</label> <?php echo $ary["dept_name"];?></p>
        		<p><label class =''>Staffs Id:</label> <?php echo $ary["emp_ids"];?></p>
        		<p><label class =''>Reason: </label> <?php echo $ary["reason"];?></p>
        		
        		<div align="center">
        		    <a href="<?php echo base_url()."roster/show/"?>" class='btn btn-info btn-xs'>Details</a> |
        		    <a href="<?php echo base_url()."roster/approve_holiday/".$ary["id"]?>" class='btn btn-primary btn-xs'>Approve</a> | 
        		    <a href="<?php echo base_url()."roster/del_holiday/".$ary["id"]?>" class='btn btn-danger btn-xs'>Delete</a>
        		</div>
        	</div>
    	</div>
    	<?php
    	}
    }
    ?>
</div>

