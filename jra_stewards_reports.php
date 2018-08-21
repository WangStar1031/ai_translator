<?php
	session_start();
	if( !isset( $_SESSION['jtsUserName']))
		header("Location: login.php");
	require_once 'library/db_user_man.php';
	$userName = $_SESSION['jtsUserName'];
	$userInfo = getUserInfoFromName( $userName);
?>

<title>JRA Stewards Reports Total - Vision A.I.</title>

<link rel="stylesheet" type="text/css" href="assets/css/report_total.css">

<?php
	$file = str_replace(__DIR__, "", __FILE__);
	require_once 'components/topbar.php';
	require_once 'components/category_bar.php';
	require_once 'components/' . $file;
?>