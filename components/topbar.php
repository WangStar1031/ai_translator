<link rel="stylesheet" type="text/css" href="assets/css/topbar.css?<?= time();?>">
<div class="topBar col-lg-12">
	<a href="#">
		<img src="assets/imgs/vision-logo.png">
		<span class="topTitle"><strong>JTS</strong> dashboard</span>
	</a>
	<div class="topUserInfo">
		<a href="logout.php">Log Out &nbsp&nbsp<span><i class="fa fa-sign-out"></i></span></a>
	</div>
	<div class="topNavMenu">
		<div class="dropdown">
			<a href="javascript:void()">Menu <span><i class="fa fa-bars"></i></span></a>
			<ul class="dropdown-content">
		<?php
			if( ($userRole & ROLE_ACCOUNT) == ROLE_ACCOUNT){
				echo '<li><a href="#">Account</a></li>';
			}
			if( ($userRole & ROLE_DASHBOARD) == ROLE_DASHBOARD){
				echo '<li><a href="#">Dashboard</a></li>';
			}
			if( ($userRole & ROLE_INVITE) == ROLE_INVITE){
				echo '<li><a href="#">Invite</a></li>';
			}
			if( ($userRole & ROLE_USERMAN) == ROLE_USERMAN){
				echo '<li><a href="#">UserManagement</a></li>';
			}
		?>
			</ul>
		</div>
	</div>
</div>