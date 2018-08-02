<?php
	require_once __DIR__ . '/MySql.php';
	define("DB_TYPE", "mysql");
	define("DB_HOST", "127.0.0.1");
	define("DB_NAME", "newdatabase");
	define("DB_USER", "root");

	if(@file_get_contents(__DIR__."/localhost"))
		define("DB_PASSWORD", "");
	else
		define("DB_PASSWORD", "1234567812345678");
	$db = new Mysql();
	function userVerification( $_userName, $_password){
		global $db;
		$strSql = "select * from user where (nickname = '$_userName' or email = '$_userName') and password = '$_password'";
		$result = $db->select($strSql);
		if( $result == false){
			return false;
		}
		$_user = $result[0];
		return $_user["nickname"];
	}
	function getUserInfoFromName( $_userName){
		global $db;
		$strSql = "select * from user where nickname = '$_userName'";
		$result = $db->select($strSql);
		if( $result == false){
			return null;
		}
		$_user = $result[0];
		return $_user;
	}
	function saveFirstName( $_userName, $_firstName){
		global $db;
		$sql = "UPDATE user SET firstname=? WHERE nickname=?";
		$stmt= $db->prepare($sql);
		$stmt->execute([$_firstName, $_userName]);
	}
	function saveLastName( $_userName, $_lastName){
		global $db;
		$sql = "UPDATE user SET lastname=? WHERE nickname=?";
		$stmt= $db->prepare($sql);
		$stmt->execute([$_lastName, $_userName]);
	}
	function saveEmail( $_userName, $_eMail){
		global $db;
		$sql = "UPDATE user SET email=? WHERE nickname=?";
		$stmt= $db->prepare($sql);
		$stmt->execute([$_eMail, $_userName]);
	}
?>