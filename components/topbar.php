
<script type="text/javascript" src="assets/js/jquery.min.js"></script>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<link rel="icon" type="image/png" href="assets/imgs/vision-logo.png">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link rel="stylesheet" type="text/css" href="assets/css/topbar.css?<?= time();?>">
<?php
	$userRole = $userInfo['role'];
	require_once 'library/constants.php';

	$arrRoles = [ROLE_STEWARD, ROLE_NEWS, ROLE_GPMODELS, ROLE_MIZUHOFX, ROLE_WATARI, ROLE_SAKE, ROLE_NDK];
	$arrClass = ["jraStewardReports", "jraNewsReports", "gbpModels", "MizuhoFX", "Watami", "Sake Brewery", "NDK Reports"];
	$arrHrefs = ["jra_stewards_reports.php", "jra_news_reports.php", "#", "#", "#", "#", "#"];
	$arrTitle = ["JRA Stewards", " JRA News", "GP Models", "Mizuho FX", "Watami", "Sake Brewery", "NDK Reports"];
?>
<div class="topBar col-lg-12">
	<a href="dashboard.php">
		<img src="assets/imgs/vision-logo.png">
		<span class="topTitle"><strong>AI</strong> Translation</span>
	</a>
	<div class="topUserInfo">
		<a href="logout.php">Log Out &nbsp&nbsp<span><i class="fa fa-sign-out"></i></span></a>
	</div>
	<div class="topNavMenu">
		<div class="dropdown">
			<a href="javascript:;">Menu <span><i class="fa fa-bars"></i></span></a>
			<ul class="dropdown-content">
		<?php
			echo '<li><a href="account.php">Account</a></li>';
			if( ($userRole & ROLE_STEWARD) == ROLE_STEWARD || ($userRole & ROLE_NEWS) == ROLE_NEWS || ($userRole & ROLE_GPMODELS) == ROLE_GPMODELS || ($userRole & ROLE_MIZUHOFX) == ROLE_MIZUHOFX){
				echo '<li><a href="dashboard.php">Dashboard</a></li>';
			}
			if( ($userRole & ROLE_INVITE) == ROLE_INVITE){
				echo '<li><a href="invite.php">Invite</a></li>';
			}
			if( ($userRole & ROLE_USERMAN) == ROLE_USERMAN){
				echo '<li><a href="userman.php">UserManagement</a></li>';
				echo '<li><a href="dictionary.php">Dictionary</a></li>';
			}
		?>
			</ul>
		</div>
	</div>
</div>