<?php
foreach ($leaves as $key=>$row) {
	$emp_id=$row['emp_id'];
	$name=$row['name'];
	$designation=$row['designation'];
	$dept_name=$row['dept_name'];
	$image=$row['image'];
	$leave_start = $row['leave_end'];
	$leave_end = $row['leave_end'];
	
	//$join_date = date("Y-m-d",mktime(0,0,0,substr($leave_end,5,2), substr($leave_end,8,2)+1, substr($leave_end,0,4)));
	//if(date("l",strtotime($join_date))=='Friday') $join_date = date("Y-m-d",strtotime($join_date." +1 days"));
	//if(date("l",strtotime($join_date))=='Saturday') $join_date = date("Y-m-d",strtotime($join_date." +1 days"));

	echo "<div>
			$emp_id<br>
			<a href='".base_url()."user/detail/$emp_id'>$name</a><br>
			$designation<br>
			$dept_name<br>
			From: $leave_start<br>
			To: $leave_end
		</div>";
}
?>