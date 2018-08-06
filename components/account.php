<?php
	$passMatching = true;
	$equalPass = true;
	if( isset($_POST['userName'])){
		if( isset($_POST['firstName'])){
			saveFirstName($userName, $_POST['firstName']);
		}
		if( isset($_POST['lastName'])){
			saveLastName($userName, $_POST['lastName']);
		}
		if( isset($_POST['eMail'])){
			saveEmail($userName, $_POST['eMail']);
		}
	} else if( isset($_POST['password'])){
		$_post_curPass = "";
		$_post_newPass = "";
		$_post_conPass = "";
		$passMatching = false;
		if( isset($_POST['curPass'])){
			$_post_curPass = $_POST['curPass'];
			$_curPass = $userInfo['password'];
			if( $_curPass === $_post_newPass){
				$passMatching = true;
			}
			if( isset($_POST['newPass'])){
				$_post_newPass = $_POST['newPass'];
			}
			if( isset($_POST['conPass'])){
				$_post_conPass = $_POST['conPass'];
			}
			$equalPass = false;
			if( $_post_newPass === $_post_conPass){
				$equalPass = true;
			}
			if( $passMatching == true && $equalPass == true){
				savePassword($userName, $_post_newPass);
			}
		}
	}
?>
<link rel="stylesheet" type="text/css" href="assets/css/account.css">
<div class="mainContents row">
	<div class="col-lg-6">
		<h3>Account Details</h3>
		<form class="col-lg-10" method="POST">
			<label>User Name</label><br/>
			<input type="text" name="userName" value="<?= $userInfo['nickname'];?>" readonly><br/>
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
		<form class="passForm" method="POST">
			<input type="hidden" name="password" value="password">
			<label for="curPass">Current Password 
			<?php
				if( $passMatching == false)
					echo 'span class="curPassErrMsg">* Not matching password</span>';
			?>
			</label><br/>
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
			<button class="btn btn-primary">Save</button>
		</form>
	</div>
</div>


<script type="text/javascript" src="assets/js/account.js"></script>