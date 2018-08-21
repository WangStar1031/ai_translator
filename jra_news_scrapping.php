<?php
	session_start();
	if( !isset( $_SESSION['jtsUserName']))
		header("Location: login.php");
	require_once 'library/db_user_man.php';
	$userName = $_SESSION['jtsUserName'];
	$userInfo = getUserInfoFromName( $userName);
?>

<link rel="stylesheet" type="text/css" href="assets/css/jra_news_scrapping.css?<?=time();?>">
<title>JRA NEWS - Vision A.I.</title>

<?php
	$file = str_replace(__DIR__, "", __FILE__);
	require_once 'components/topbar.php';
	require_once 'components/' . $file;
?>
