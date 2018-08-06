
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
<script type="text/javascript" src="assets/js/bootstrap.min.js"></script>

<link rel="icon" type="image/png" href="assets/imgs/vision-logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link rel="stylesheet" type="text/css" href="assets/css/topbar.css?<?= time();?>">
<?php
	$userRole = $userInfo['role'];
	define( "ROLE_ACCOUNT", 1);
	define( "ROLE_DASHBOARD", 2);
	define( "ROLE_INVITE", 4);
	define( "ROLE_USERMAN", 8);
?>
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
			<a href="javascript:;">Menu <span><i class="fa fa-bars"></i></span></a>
			<ul class="dropdown-content">
		<?php
			if( ($userRole & ROLE_ACCOUNT) == ROLE_ACCOUNT){
				echo '<li><a href="account.php">Account</a></li>';
			}
			if( ($userRole & ROLE_DASHBOARD) == ROLE_DASHBOARD){
				echo '<li><a href="dashboard.php">Dashboard</a></li>';
			}
			if( ($userRole & ROLE_INVITE) == ROLE_INVITE){
				echo '<li><a href="invite.php">Invite</a></li>';
			}
			if( ($userRole & ROLE_USERMAN) == ROLE_USERMAN){
				echo '<li><a href="userman.php">UserManagement</a></li>';
			}
		?>
			</ul>
		</div>
	</div>
</div>