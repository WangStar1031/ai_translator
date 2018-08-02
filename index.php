<?php
	session_start();
	if( !isset( $_SESSION['jtsUserName']))
		header("Location: login.php");
	require_once 'library/db_user_man.php';
	$userName = $_SESSION['jtsUserName'];
	if( isset($_POST['firstName'])){
		saveFirstName($userName, $_POST['firstName']);
	}
	if( isset($_POST['lastName'])){
		saveLastName($userName, $_POST['lastName']);
	}
	if( isset($_POST['eMail'])){
		saveEmail($userName, $_POST['eMail']);
	}
	if( isset($_POST['curPass'])){
		if( isset($_POST['newPass'])){

		}
		if( isset($_POST['conPass'])){

		}
	}
	$userInfo = getUserInfoFromName( $userName);
?>
<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/dashboard.css">
  <link rel="icon" type="image/png" href="assets/imgs/vision-logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="topBar col-lg-12">
	<a href="#">
		<img src="assets/imgs/vision-logo.png">
		<span class="topTitle"><strong>JTS</strong> dashboard</span>
	</a>
	<div class="topUserInfo">
		<a href="logout.php">Log Out &nbsp&nbsp<span><i class="fa fa-sign-out"></i></span></a>
	</div>
</div>
<div class="mainContents row">
	<div class="col-lg-6">
		<h3>Account Details</h3>
		<form class="col-lg-10" method="POST">
			<label>User Name</label><br/>
			<input type="text" value="<?= $userInfo['nickname'];?>" readonly><br/>
			<label for="firstName">First Name</label><br/>
			<input type="text" name="firstName" value="<?= $userInfo['firstname'];?>"><br/>
			<label for="lastName">Last Name</label><br/>
			<input type="text" name="lastName" value="<?= $userInfo['lastname'];?>"><br/>
			<label for="eMail">Email</label><br/>
			<input type="text" name="eMail" value="<?= $userInfo['email'];?>"><br/>
			<button class="btn btn-primary">Save</button>
		</form>
	</div>
	<div class="col-lg-6">
		<h3>Change Password</h3>
		<form>
			<label for="curPass">Current Password</label><br/>
			<input type="password" name="curPass"><br/>
			<a href="javascript:forgotPassword();">forgot Password</a><br/>
			<label for="firstName">New Password</label><br/>
			<input type="password" name="newPass" onkeyup="NewPassChange()"><br/>
			<p>
				<table>
					<tr>
						<td class="LowerCaseTd"><i class="fa fa-check-circle-o HideItem"></i><i class="fa fa-dot-circle-o"></i> One lowercase charactor</td>
						<td class="SpecChrTd"><i class="fa fa-check-circle-o HideItem"></i><i class="fa fa-dot-circle-o"></i> One special charactor</td>
					</tr>
					<tr>
						<td class="UpperCaseTd"><i class="fa fa-check-circle-o HideItem"></i><i class="fa fa-dot-circle-o"></i> One uppercase charactor</td>
						<td class="StrLenCaseTd"><i class="fa fa-check-circle-o HideItem"></i><i class="fa fa-dot-circle-o"></i> 8 charactors minimum</td>
					</tr>
					<tr>
						<td class="NumberCaseTd"><i class="fa fa-check-circle-o HideItem"></i><i class="fa fa-dot-circle-o"></i> One number</td>
					</tr>
				</table>
			</p>
			<label for="lastName">Confirm Password</label><br/>
			<input type="password" name="conPass"><br/>
			<div class="btn btn-primary">Save</div>
		</form>
	</div>
</div>
<div class="row reports">
	<div class="jraStewardReports col-lg-4">
		<h3>Stewards Reports</h3>
		<?php	
			$events = glob( "logs/backup/*.event");
			$today_events = glob("*.event");
			$events = array_merge($events, $today_events);
			$cur_year = date("Y");
			$cur_month = date("n");
			if(isset($_POST["year"])) $cur_year = $_POST["year"];
			if(isset($_POST["month"])) $cur_month = $_POST["month"];
		?>
		<div class="reportsContents">
			<form method=post id="frmList" name="frmList">
				<select name="year" id="year" onchange="javascript: frmList.submit();">
					<?php for($i=2017; $i<=date("Y"); $i++){?>
					<option value="<?=$i?>"<?php if($cur_year == $i) echo ' selected';?>><?=$i?></option>
					<?php }?>
				</select>
				/
				<select name="month" id="month" onchange="javascript: frmList.submit();">
					<?php for($i=1; $i<=12; $i++){?>
					<option value="<?=$i?>"<?php if($cur_month == $i) echo ' selected';?>><?=$i?></option>
					<?php }?>
				</select>
			</form>
			<?php
				$link_href = "http://dataminer.jts.ec/JTS/api_keiba_notice.php?c=14&d_val=";
				foreach ($events as $event_data) {
					$event_data = substr($event_data, 12, 8);
					$event_date = substr($event_data,0,4).'-'.substr($event_data,4,2).'-'.substr($event_data,6,2);
					//@file_get_contents($link_href.$event_data.'&lang=en');
					if($cur_year != date("Y", strtotime($event_date))) continue;
					if($cur_month != date("n", strtotime($event_date))) continue;
					echo '<a href="'.$link_href.$event_data.'&lang=en" target="_blank">Report ('.date("F jS, Y", strtotime($event_date)).')</a><br>';
				}
			?>
		</div>
	</div>
	<div class="jraStewardReports col-lg-4">
		<h3>JRA News</h3>
	</div>
	<div class="jraStewardReports col-lg-4">
		<h3>GP Models</h3>
	</div>
</div>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/dashboard.js"></script>