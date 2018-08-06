<?php
	session_start();
	if( !isset( $_SESSION['jtsUserName']))
		header("Location: login.php");
	header("Location: dashboard.php");
?>