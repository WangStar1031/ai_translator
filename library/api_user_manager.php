<?php
	require_once __DIR__ . '/db_user_man.php';
	require_once __DIR__ . '/constants.php';
	require_once __DIR__ . '/mailer/mailer.lib.php';

	$siteUrl = "http://dataminer.jts.ec/ai/translation/";

	function inviteUser($_email, $_fromUser, $_firstName = "", $_lastName = "", $_role = ROLE_DEFAULT_USER){
		global $siteUrl;
		$authUrl = $_email . time() . $_email;
		$authUrl = crypt( $authUrl, '');
		$title = "Welcome to ai translation.";
		$content = "<h3>Hello, " . $_firstName . "</h3>";
		$content .= "<p>You are invited to AI Translation System.</p><br>";
		$content .= "<a href='" . $siteUrl . "confirmInvitation.php?auth=" . $authUrl . "'>To join us, Please click here.</a><br/>";
		$_fromUserInfo = getUserInfoFromName( $_fromUser);
		$content .= "<b>" . $_fromUserInfo['firstname'] . "</b>";
		$respond = custom_mail_send($_email, $title, $content, "");
		if( $respond == "OK"){
			return insertInvitedUser($_email, $_fromUserInfo['Id'], $authUrl, $_firstName, $_lastName, $_role);
		}
		return $respond;
	}
	if(isset($_POST['inviteUser'])){
		$inviteUserMail = $_POST['inviteUser'];
		$fromUser = $_POST['fromUser'];
		$firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$role = $_POST['role'];
		echo inviteUser($inviteUserMail, $fromUser, $firstName, $lastName, $role);
	}
	if( isset($_POST['userRole'])){
		$_userId = $_POST['userRole'];
		$_userRole = $_POST['role'];
		setUserRole($_userId, $_userRole);
	}
	if(isset($_POST['deleteUser'])){
		$_userIds =  $_POST['deleteUser'];
		$_arrUserIds = explode(",", $_userIds);
		for($i = 0; $i < count($_arrUserIds); $i++){
			echo deleteUser($_arrUserIds[$i]);
		}
	}

?>