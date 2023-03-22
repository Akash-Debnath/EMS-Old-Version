<?php 
$all=$employee=$archive=$settings=$department=$designation=$updateRequest="";
$privilege=$facility= $temp_title=$permission_privilege= "";
$board=$remark=$notice =$attachment=$policy=$job_description="";
$holiday=$incident=$office_time = $note = "";
$leave = $glance = $all_leave = $leave_list=$pending="";
$category = $store=$requisition=$purchase=$item= "";
$attendance=$report=$upload=$att_request=$req_pending= $missing_req_pending= "";
$roster_set = $office_schedule = $roster_request="";

$dept_code_to_add = "all";

foreach ($departments as $key=>$item) {
    ${strtolower($key)} = "";
    if($key==$title) {
        $dept_code_to_add = $key;
        $temp_title = $item;
    }
}

$menu = strtolower($menu);
$title = strtolower($title);

//echo $menu.":".$title;


if(isset($isPanelMenuOpen) && !$isPanelMenuOpen){
    
    ${$menu} = "";
    ${$title} = "";
}else{
    ${$menu} = "active";
    ${$title} = "active";
}

// if(!(isset($notSelect) && $notSelect)){
//     ${$menu} = "active";
//     ${$title} = "active";
// }

if(!empty($temp_title)) $title = $temp_title;

$skin = "skin-blue";
?>
<!DOCTYPE html>
<html lang="en" spellcheck="true">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="google-signin-scope" content="profile email">
<meta name="google-signin-client_id" content="152628580978-stbc9394kd9ttflf7m17e9og009mngft.apps.googleusercontent.com">


<title>EMS</title>
<meta
	content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no'
	name='viewport'>
<link rel="stylesheet"
	href="<?php echo base_url();?>assets/lib/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet"
	href="<?php echo base_url();?>assets/lib/bootstrap/css/font-awesome.min.css">
<link rel="stylesheet"
	href="<?php echo base_url();?>assets/lib/bootstrap/css/ionicons.min.css">
<link rel="stylesheet"
	href="<?php echo base_url();?>assets/lib/bootstrap/css/datatables/dataTables.bootstrap.css">
<link rel="stylesheet"
	href="<?php echo base_url();?>assets/lib/theme/css/AdminLTE.css">
<link rel="stylesheet"
	href="<?php echo base_url();?>assets/css/main.css">


<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
<script src="<?php echo base_url();?>assets/js/jquery-1.11.2.min.js"></script>

<style type="text/css">
.skin-blue .left-side {
	background: none repeat scroll 0 0 #54667a;
}

.skin-blue .sidebar>.sidebar-menu>li {
	border-bottom: 1px solid #000;
}

.skin-blue .sidebar>.sidebar-menu>li {
	border-top: 1px solid #708296;
}

.skin-blue .sidebar a {
	color: #fff;
}

.skin-blue .sidebar>.sidebar-menu>li>a:hover,.skin-blue .sidebar>.sidebar-menu>li.active>a
	{
	background: none repeat scroll 0 0 #798b9f
}

.skin-blue .sidebar>.sidebar-menu>li>.treeview-menu {
	background: none repeat scroll 0 0 #798b9f;
}

.skin-blue .treeview-menu>li>a {
	color: #fff;
}

.user-panel {
	padding: 10px 10px 9px 10px;
	background-color: #DDE2E5;
	box-shadow: -3px 0 8px -4px rgba(0, 0, 0, 0.3) inset;
}

.skin-blue .left-side {
	box-shadow: -3px 0 8px -4px rgba(0, 0, 0, 1) inset;
}

.skin-blue .sidebar>.sidebar-menu>li:first-of-type {
	border-top: none;
}

.nameLink{
	font-size: 13px !important;
	font-weight: bold !important;
}

.imageLink{
    height: 45px;
    width: 45px;	
	border: 2px solid #3C8DBC;
}

html, body{
	font-family: Tahoma,Verdana,Segoe,sans-serif !important;
	font-size: 13px;
}

.shrtn-text {
    -o-text-overflow: ellipsis;   /* Opera */
    text-overflow:    ellipsis;   /* IE, Safari (WebKit) */
    overflow:hidden;              /* don't show excess chars */
    white-space:nowrap;           /* force single line */
    width: 155px;    /* fixed width */
	padding-left: 10px !important;
}


</style>
</head>
<body class="<?php echo isset($skin) ? $skin : ""; ?>">
	<!-- header logo: style can be found in header.less -->
	<header class="header">
		<a href="<?php echo base_url();?>user/detail/<?php echo $myInfo->userId;?>" class="logo"> EMS </a>
		<!-- Header Navbar: style can be found in header.less -->
		<nav class="navbar navbar-static-top" role="navigation">
			<!-- Sidebar toggle button-->
			<a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas"
				role="button"> <span class="sr-only">Toggle navigation</span> <span
				class="icon-bar"></span> <span class="icon-bar"></span> <span
				class="icon-bar"></span>
			</a>
			<div class="navbar-right">
				<ul class="nav navbar-nav">
					<!-- Messages: style can be found in dropdown.less-->
					<!--  li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope"></i>
                                <span class="label label-success">4</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 4 messages</li>
                                <li>
                                    
                                    <ul class="menu">
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="<?php //echo $myInfo->userImage;?>" class="img-circle" alt="User Image"/>
                                                </div>
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="<?php //echo $myInfo->userImage;?>" class="img-circle" alt="user image"/>
                                                </div>
                                                <h4>
                                                    AdminLTE Design Team
                                                    <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="../../img/avatar.png" class="img-circle" alt="user image"/>
                                                </div>
                                                <h4>
                                                    Developers
                                                    <small><i class="fa fa-clock-o"></i> Today</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="../../img/avatar2.png" class="img-circle" alt="user image"/>
                                                </div>
                                                <h4>
                                                    Sales Department
                                                    <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="../../img/avatar.png" class="img-circle" alt="user image"/>
                                                </div>
                                                <h4>
                                                    Reviewers
                                                    <small><i class="fa fa-clock-o"></i> 2 days</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">See All Messages</a></li>
                            </ul>
                        </li-->
                        
                        
                        
                        <?php 
							$leaveCount = $controller->getLeaveRequestCount();
							$updateReqCount=$controller->getTotalUpdateRequestCount();
							// var_dump($updateReqCount);
							// die();
                        	
                        	$todaysLeaveCount = $controller->tadaysLeaveCount();
                        	$tadaysJoinCount = $controller->tadaysJoinCount();
                        	
                        	
                        	$noticeCount = $controller->getUnreadNoticeCount($controller->myEmpId);
                        	$attachmentCount = $controller->getUnreadAtachmentCount($controller->myEmpId);
                        	$attendanceRequestCount = $controller->getAttendanceRequestCount($controller->myEmpId);
                        	
                        	$rosterPendingCount = $controller->getRosterPendingCount();
                        	
                        	//$requisitionCount = $controller->getRequisitionCount();
                        	//$purchaseCount = $controller->getPurchaseCount();
                        	$requisitionCount = 0;
                        	$purchaseCount = 0;
                        	
                        	
                        	$totalNotification = $leaveCount + $noticeCount + $attachmentCount + $attendanceRequestCount + $requisitionCount + $purchaseCount +$rosterPendingCount+$updateReqCount;
                        ?>
                                                

					<!-- Notifications: style can be found in dropdown.less -->
					<?php if($uType=='A'){?>
						<li class="dropdown notifications-menu">								
							<ul class="dropdown-menu">
								<li class="header">You have <?php echo $totalNotification;?> notifications</li>
								<li>
									<!-- inner menu: contains the actual data -->
									<?php
									if($totalNotification>0) {
										
										echo  "<ul class='menu'>";
										
										if($rosterPendingCount > 0) {
										
											echo "<li><a href='".base_url()."roster/holiday_request'> <i class='fa fa-exclamation-circle danger'></i>".$rosterPendingCount." New Roster Request</a></li>";
										}
										
										//echo "<li><a href='".base_url()."leave/report'> <i class='fa fa-users warning'></i>".$todaysLeaveCount."/".$tadaysJoinCount." Today's Leave/Join</a></li>";
										
										if($leaveCount > 0) {
											
											echo "<li><a href='".base_url()."leave/pending'> <i class='fa fa-users warning'></i>".$leaveCount." Leave Request</a></li>";
										}
										
										if($attendanceRequestCount > 0) {
										
											echo "<li><a href='".base_url()."attendance/pending'> <i class='fa fa-users warning'></i>".$attendanceRequestCount." Attendance Request</a></li>";
										}
										if($noticeCount > 0) {
										
											echo "<li><a href='".base_url()."remark/notice'> <i class='fa fa-envelope info'></i>".$noticeCount." New Notice</a></li>";
										}
										if($attachmentCount > 0) {
										
											echo "<li><a href='".base_url()."remark/attachment'> <i class='fa fa-paperclip success'></i>".$attachmentCount." New Attachment</a></li>";
										}
										
										if($requisitionCount > 0) {
										
											echo "<li><a href='".base_url()."requisition/lists'> <i class='fa fa-file-o warning'></i>".$requisitionCount." New Requisition</a></li>";
										}
										
										if($purchaseCount > 0) {
										
											echo "<li><a href='".base_url()."requisition/purchase'> <i class='fa fa-file-text danger'></i>".$purchaseCount." New Purchase</a></li>";
										}                        		    

										echo "</ul>";
										
									} else{
										
										echo "<ul><li>No Notification Found</li></ul>";
									}    
									?>
								</li>
							</ul>
						</li>
					<?php }?>
					
					<li class="dropdown notifications-menu">
						
					    <a href="#"
						class="dropdown-toggle" data-toggle="dropdown"> <i class="fa fa-bell"></i>
                		<?php if($totalNotification>0) { ?> 
                			<span class="label label-danger"><?php echo $totalNotification; ?></span>
                		<?php } ?>
                        </a>
                        	
						<ul class="dropdown-menu">
							<li class="header">You have <?php echo $totalNotification;?> notifications</li>
							<li>
                                <!-- inner menu: contains the actual data -->
                        		<?php
                        		if($totalNotification>0) {
                        		    
                        		    echo  "<ul class='menu'>";
                        		    
                        		    if($rosterPendingCount > 0) {
                        		    
                        		        echo "<li><a href='".base_url()."roster/holiday_request'> <i class='fa fa-exclamation-circle danger'></i>".$rosterPendingCount." New Roster Request</a></li>";
                        		    }
                        		    
                        		    //echo "<li><a href='".base_url()."leave/report'> <i class='fa fa-users warning'></i>".$todaysLeaveCount."/".$tadaysJoinCount." Today's Leave/Join</a></li>";
                        		    
                        		    if($leaveCount > 0) {
                        		        
                                        echo "<li><a href='".base_url()."leave/pending'> <i class='fa fa-users warning'></i>".$leaveCount." Leave Request</a></li>";
									}
									if($uType=='A'){
										if($updateReqCount > 0) {
											
											echo "<li><a href='".base_url()."user/see_update_request'> <i class='fa fa-edit primary'></i>".$updateReqCount." Profile Update Request</a></li>";
										}
									}
                        		    
                        		    if($attendanceRequestCount > 0) {
                        		    
                        		        echo "<li><a href='".base_url()."attendance/pending'> <i class='fa fa-users warning'></i>".$attendanceRequestCount." Attendance Request</a></li>";
                        		    }
                        		    if($noticeCount > 0) {
                        		    
                        		        echo "<li><a href='".base_url()."remark/notice'> <i class='fa fa-envelope info'></i>".$noticeCount." New Notice</a></li>";
                        		    }
                        		    if($attachmentCount > 0) {
                        		    
                        		        echo "<li><a href='".base_url()."remark/attachment'> <i class='fa fa-paperclip success'></i>".$attachmentCount." New Attachment</a></li>";
                        		    }
                        		    
                        		    if($requisitionCount > 0) {
                        		    
                        		        echo "<li><a href='".base_url()."requisition/lists'> <i class='fa fa-file-o warning'></i>".$requisitionCount." New Requisition</a></li>";
                        		    }
                        		    
                        		    if($purchaseCount > 0) {
                        		    
                        		        echo "<li><a href='".base_url()."requisition/purchase'> <i class='fa fa-file-text danger'></i>".$purchaseCount." New Purchase</a></li>";
                        		    }                        		    

                        		    echo "</ul>";
                        		    
                        		} else{
                        		    
                        		    echo "<ul><li>No Notification Found</li></ul>";
                        		}    
                        		?>
							</li>
						</ul>
					</li>





					<!-- Tasks: style can be found in dropdown.less -->
					<!-- li class="dropdown tasks-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-tasks"></i>
                                <span class="label label-danger">9</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 9 tasks</li>
                                <li>
                                    
                                    <ul class="menu">
                                        <li>
                                            <a href="#">
                                                <h3>
                                                    Design some buttons
                                                    <small class="pull-right">20%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">20% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <h3>
                                                    Create a nice theme
                                                    <small class="pull-right">40%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">40% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <h3>
                                                    Some task I need to do
                                                    <small class="pull-right">60%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">60% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <h3>
                                                    Make beautiful transitions
                                                    <small class="pull-right">80%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">80% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a href="#">View all tasks</a>
                                </li>
                            </ul>
                        </li-->
					<!-- User Account: style can be found in dropdown.less -->
					<li class="dropdown user user-menu"><a href="#"
						class="dropdown-toggle" data-toggle="dropdown"> <i
							class="glyphicon glyphicon-user"></i> <span><?php echo $myInfo->userName;?> <i
								class="caret"></i></span>
					</a>
						<ul class="dropdown-menu">
							<!-- User image -->
							<li class="user-header bg-light-blue"><img
								src="<?php echo $myInfo->userImage."?v=".date("d");?>" class="img-circle"
								alt="User Image" />
								<p>
                                    	<?php echo $myInfo->userId;?><br>
                                        <?php echo $myInfo->userName;?>
                                        <small><?php echo $myInfo->userDesignation.", ".$myInfo->userDepartment;?></small>
								</p></li>
							<!-- Menu Body -->
							<!-- li class="user-body">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Followers</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Sales</a>
                                    </div>
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Friends</a>
                                    </div>
                                </li>
                                
                                <!-- Menu Footer-->
							<li class="user-footer">
								<div class="pull-left">
									<a
										href="<?php echo base_url();?>user/detail/<?php echo $myInfo->userId;?>"
										class="btn btn-default btn-flat">Profile</a>
								</div>
								<div class="pull-right">
									<a href="<?php echo base_url();?>user/logout"
										class="btn btn-default btn-flat">Sign out</a>
								</div>
							</li>
						</ul></li>
				</ul>
			</div>
		</nav>
	</header>
	<div class="wrapper row-offcanvas row-offcanvas-left">
		<!-- Left side column. contains the logo and sidebar -->
		<aside class="left-side sidebar-offcanvas">
			<!-- sidebar: style can be found in sidebar.less -->
			<section class="sidebar">
				<!-- Sidebar user panel -->
				<div class="user-panel">
					<div class="pull-left image">
					    <a class='nameLink' href="<?php echo base_url();?>user/detail/<?php echo $myInfo->userId;?>">
					    	<img class ='img-circle imageLink' src="<?php echo $myInfo->userImage."?v=".date("d");?>" alt="User Image"/></a>
							
					</div>
					<div class="pull-left info shrtn-text">
						<p>
							<small>Welcome</small>
						</p>
							<a class='nameLink' href="<?php echo base_url();?>user/detail/<?php echo $myInfo->userId;?>"><?php echo $myInfo->userName;?></a> 
                            <!-- a href="#"><i class="fa fa-circle text-success"></i> Online</a-->
					</div>
				</div>
				<!-- search form -->
				<!--form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search..."/>
                            <span class="input-group-btn">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form-->
				<!-- /.search form -->
				<!-- sidebar menu: : style can be found in sidebar.less -->
				<ul class="sidebar-menu">
					<li class="treeview <?php echo $employee;?>"><a href="#"
						style='border: none;'> <i class="fa fa-dashboard"></i> <span>Employee</span>
							<i class="fa fa-angle-left pull-right"></i>
					</a>
						<ul class="treeview-menu">
							<li class="<?php echo $all;?>"><a
								href="<?php echo base_url()?>user/show"><i
									class="fa fa-angle-double-right"></i> All</a></li>
							<?php if($uType=='A'){?>
								<li class="<?php echo $updateRequest; ?>"><a href="<?php echo base_url();?>user/see_update_request"><i class="fa fa-angle-double-right"></i>Profile Update History</a></li>
							<?php } ?>
							
									
                                <?php foreach ($departments as $dcode=>$dname) {
                                    
                                    echo "<li class='".${strtolower($dcode)}."'><a href='".base_url()."user/show/".$dcode."/1'><i class='fa fa-angle-double-right'></i>".$dname."</a></li>";                                                                        
                                }
                                    ?>
                            
							</ul></li>
							
					<li class="treeview <?php echo $remark;?>"><a href="#"> <i
							class="fa fa-laptop"></i> <span>Board</span> <i
							class="fa fa-angle-left pull-right"></i>
					</a>
						<ul class="treeview-menu">
							<li class="<?php echo $notice;?>"><a
								href="<?php echo base_url();?>remark/notice"><i
									class="fa fa-angle-double-right"></i> Notice</a></li>
							<li class="<?php echo $attachment;?>"><a
								href="<?php echo base_url();?>remark/attachment"><i
									class="fa fa-angle-double-right"></i> Attachment</a></li>
							<li class="<?php echo $policy;?>"><a
								href="<?php echo base_url();?>remark/policy"><i
									class="fa fa-angle-double-right"></i> Policy</a></li>
                            <li class="<?php echo $holiday;?>"><a
								href="<?php echo base_url();?>settings/holiday"><i
									class="fa fa-angle-double-right"></i> Holiday</a></li>
							<li class="<?php echo $incident;?>"><a
								href="<?php echo base_url();?>settings/incident"><i
									class="fa fa-angle-double-right"></i> Incident</a></li>
																		
							<li class="<?php echo $job_description;?>"><a
								href="<?php echo base_url();?>remark/jdboard"><i
									class="fa fa-angle-double-right"></i> Job Description</a></li>
						</ul></li>
					<li class="treeview <?php echo $leave;?>"><a href="#"> <i
							class="fa fa-laptop"></i> <span>Leave</span> <i
							class="fa fa-angle-left pull-right"></i>
					</a>
						<ul class="treeview-menu">
							<li class="<?php echo $leave_list;?>"><a
								href="<?php echo base_url();?>leave/show"><i
									class="fa fa-angle-double-right"></i>Leave List</a></li>
                            <?php if($uType == "A" || $uType == "B" || $uType == "M" ){ ?>
                                <li class="<?php echo $glance;?>"><a
								href="<?php echo base_url();?>leave/glance"><i
									class="fa fa-angle-double-right"></i> At a Glance</a></li>
							<li class="<?php echo $all_leave;?>"><a
								href="<?php echo base_url();?>leave/all"><i
									class="fa fa-angle-double-right"></i> Yearly Leave Report</a></li>
									
							<?php } if ($controller->leave_access){ ?>
							<li class="<?php echo $pending;?>"><a
								href="<?php echo base_url();?>leave/pending"><i
									class="fa fa-angle-double-right"></i> Pending</a></li>
							<?php } ?>		
                            <li
								class="<?php echo $req_pending;?>"><a
								href="<?php echo base_url();?>leave/report"><i
									class="fa fa-angle-double-right"></i>Today's Leave<span class="label label-primary pull-right"><?php echo $todaysLeaveCount."/".$tadaysJoinCount; ?></span></a></li>    
                            </ul></li>
					<li class="treeview <?php echo $archive;?>"><a href="#"> <i
							class="fa fa-laptop"></i> <span>Archive</span> <i
							class="fa fa-angle-left pull-right"></i>
					</a>
						<ul class="treeview-menu">
							<li class="<?php echo $archive;?>"><a
								href="<?php echo base_url();?>user/archive"><i
									class="fa fa-angle-double-right"></i> All</a></li>

						</ul></li>
                        <?php if( $isManagement || $isAdmin || $isManager ){ ?>
                         <li class="treeview <?php echo $settings;?>"><a href="#"> <i class="fa fa-laptop"></i> <span>Settings</span> <i
							class="fa fa-angle-left pull-right"></i>
					    </a>
						<ul class="treeview-menu">
						<?php if($isAdmin ){ ?>
							<li class="<?php echo $department;?>"><a
								href="<?php echo base_url();?>settings/department"><i
									class="fa fa-angle-double-right"></i> Department</a></li>
							<li class="<?php echo $designation;?>"><a
								href="<?php echo base_url();?>settings/designation"><i
									class="fa fa-angle-double-right"></i> Designation</a></li>
							
															
									
							<li class="<?php echo $facility;?>"><a
								href="<?php echo base_url();?>settings/facility"><i
									class="fa fa-angle-double-right"></i> Facility</a></li>
							<li class="<?php echo $note;?>"><a
								href="<?php echo base_url();?>settings/note"><i
									class="fa fa-angle-double-right"></i> Note</a></li>
						<?php }
						if( $isManagement || $isAdmin ){
						?>								    
						    <li class="<?php echo $privilege;?>"><a
								href="<?php echo base_url();?>settings/privilege"><i
									class="fa fa-angle-double-right"></i>Administrator Privilege</a></li>
									
							<li class="<?php echo $permission_privilege;?>"><a
								href="<?php echo base_url();?>settings/permission_priv"><i
									class="fa fa-angle-double-right"></i>Permission Privilege</a></li>	
						<?php } ?>
							
							<li class="<?php echo $office_time;?>"><a
								href="<?php echo base_url();?>settings/office_time"><i
									class="fa fa-angle-double-right"></i> Office Time</a></li>
						</ul></li>
                    <?php } ?>

					<?php if($uType=='A'){?><li class="treeview">
					<a href="#"> <i class="fa fa-moon-o"></i> <span>Set Ramadan Time</span> <i
						class="fa fa-angle-left pull-right"></i></a>
						<ul class="treeview-menu">
							<li class=""><a href="<?= base_url() ?>ramadan/edit_ramadan"><i class="fa fa-angle-double-right"></i>See Ramadan List</a></li>
						</ul>
					</li>
					<?php }?>
                        
                    <li class="treeview <?php echo $attendance;?>">
					<a href="#"> <i class="fa fa-table"></i> <span>Attendance</span> <i
						class="fa fa-angle-left pull-right"></i>
					</a>
						<ul class="treeview-menu">
							<li class="<?php echo $report;?>"><a
								href="<?php echo base_url();?>attendance/show"><i
									class="fa fa-angle-double-right"></i>Report</a></li>
							<li class="<?php echo $report;?>"><a
							href="<?php echo base_url();?>attendance/todays_employee/"><i
							class="fa fa-angle-double-right"></i>Today's Employee</a></li>
                                <?php if($uType=='A' || $uType=='B'){?><li
								class="<?php echo $upload;?>"><a
								href="<?php echo base_url();?>attendance/upload"><i
									class="fa fa-angle-double-right"></i>Upload</a></li><?php }?>
                                <li class="<?php echo $att_request;?>"><a
								href="<?php echo base_url();?>attendance/request"><i
									class="fa fa-angle-double-right"></i>Late/Early Request</a></li>
                                <?php if($uType=='A' || $uType=='B' || $uType=='M'){?><li
								class="<?php echo $req_pending;?>"><a
								href="<?php echo base_url();?>attendance/pending"><i
									class="fa fa-angle-double-right"></i>Late/Early Request Pending</a></li><?php }?>                                
                                <?php if($uType=='A' || $uType=='B' || $uType=='M'){?><li
								class="<?php echo $missing_req_pending;?>"><a
								href="<?php echo base_url();?>attendance/missing_pending"><i
									class="fa fa-angle-double-right"></i>Missing Att. Req. Pending</a></li><?php }?>
                                <li class="<?php echo $office_schedule;?>"><a
								href="<?php echo base_url();?>roster/show"><i
									class="fa fa-angle-double-right"></i>Office Schedule</a></li>
									
                                <?php if($controller->roster_setting_access || $this->session->HasRosterPrev($myInfo->userId)){ ?><li
								class="<?php echo $roster_set;?>"><a
								href="<?php echo base_url();?>roster/set"><i
									class="fa fa-angle-double-right"></i>Roster Set</a></li><?php }?>
									
                                <?php if($uType=='A' || $uType=='B'){?><li
								class="<?php echo $roster_request;?>"><a
								href="<?php echo base_url();?>roster/holiday_request"><i
									class="fa fa-angle-double-right"></i>Roster Pending</a></li><?php }?>   
								
                            </ul>
					</li>
					
					
					<?php 
					
					/* ############### Tempo check until requisition workdone ########### */
					if(false){?>
					
					<li class="treeview <?php echo $store?>">
                        <a href="#">
                            <i class="fa fa-table"></i>
                            <span>Store</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                         
                        <?php if($isAdmin){ ?>
                            
                            <li class="<?php echo $category;?>"><a href="<?php echo base_url();?>requisition/category"><i class="fa fa-angle-double-right"></i> Category</a></li>
                            <li class="<?php echo $item;?>"><a href="<?php echo base_url();?>requisition/item"><i class="fa fa-angle-double-right"></i> Item</a></li>
                            
                        <?php } if( $controller->purchase_access ) { ?>
                            <li class="<?php echo $purchase;?>"><a href="<?php echo base_url();?>requisition/purchase"><i class="fa fa-angle-double-right"></i> Purchase</a></li>
                            
                        <?php } ?>            
                            <li class="<?php echo $requisition;?>"><a href="<?php echo base_url();?>requisition/lists"><i class="fa fa-angle-double-right"></i> Requisition</a></li>
                        </ul>
                    </li>
                    <?php } ?>
                    
                        
				</ul>
			</section>
			<!-- /.sidebar -->
		</aside>

		<!-- Right side column. Contains the navbar and content of the page -->
		<aside class="right-side">
			<!-- Content Header (Page header) -->
			<section class="content-header">
                    <?php if(isset($sub_title) && !empty($sub_title)){ ?><h1><?php echo ucfirst($sub_title); ?></h1><?php } ?>
                    <ol class="breadcrumb">
                        <?php if(isset($menu) && !empty($menu)){ ?><li><i
						class="fa fa-dashboard"></i> <?php echo ucfirst($menu); ?></li><?php } ?>
                        <?php if(isset($title) && !empty($title)){ ?><li><?php echo ucfirst($title); ?></li><?php } ?>
                        <?php if(isset($sub_title) && !empty($sub_title)){ ?><li
						class="active"><?php echo $sub_title; ?></li><?php } ?>
                    </ol>
			</section>

			<!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-xs-12">





<script>
	$(document).ready(function() {

//Date Correction    
	
	// $('#dateFrom1').on('changeDate', function(ev){
	// $(this).datepicker('hide');				
	// $('#dateTo1').datepicker('setStartDate',$(this).val());

	//displayDate ();
	// $("#sendform1").click(function () {
	// 	if (data.code === 1)
	// 		{
	// 			alert('wrong');
	// 		}
	// }
	});	

</script>