<?php
session_start();
$_SESSION['jtsUserName'] = "";
header('Location: login.php');
?>