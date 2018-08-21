<?php
	require_once __DIR__ . '/MySql.php';
	define("DB_TYPE", "mysql");
	define("DB_HOST", "127.0.0.1");
	define("DB_USER", "root");
	define("DB_NAME", "ai_trans");

	if(@file_get_contents(__DIR__."/localhost"))
		define("DB_PASSWORD", "");
	else
		define("DB_PASSWORD", "1234567812345678");
	$db = new Mysql();
	$db->exec("set names utf8");
	function userVerification( $_userName, $_password){
		global $db;
		$strSql = "select * from user where (nickname = '$_userName' or email = '$_userName') and binary(password) = binary('$_password')";
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
	function savePassword( $_userName, $_password){
		global $db;
		$sql = "UPDATE user SET password=? WHERE nickname = ?";
		$stmt = $db->prepare($sql);
		$stmt->execute([$_password, $_userName]);
	}
	function getAllUsersExceptMe($_userName){
		global $db;
		$sql = "select * from user where nickname <> '$_userName' and nickname <> 'admin'";
		$result = $db->select($sql);
		if( $result == false){
			return null;
		}
		return $result;
	}
	function insertInvitedUser($_email, $_from, $_veriCode, $_firstName, $_lastName, $_role){
		global $db;
		$sql = "select * from user where email ='$_email'";
		$result = $db->select($sql);
		if( $result == false){
			$sql = "insert into user(nickname, email, firstname, lastname, role, inviteurl, invitefrom) values(?, ?,?,?,?,?,?)";
			$stmt = $db->prepare($sql);
			$stmt->execute([$_email, $_email, $_firstName, $_lastName, $_role, $_veriCode, $_from]);
			return "invited.";
		}
		$record = $result[0];
		if( $record['inviteurl'] == ""){
			return "already exists.";
		}
		$sql = "update user set nickname=?, firstname=?, lastname=?, role=?, inviteurl=?, invitefrom=? where email=?";
		$stmt = $db->prepare($sql);
		$stmt->execute([$_email, $_firstName, $_lastName, $_role, $_veriCode, $_from, $_email]);
		return "updated.";
	}
	function setUserRole($_userId, $_userRole){
		global $db;
		$sql = "UPDATE user SET role=? WHERE Id=?";
		$stmt = $db->prepare($sql);
		$stmt->execute([$_userRole, $_userId]);
		return true;
	}
	function deleteUser($_userId){
		global $db;
		$sql = "DELETE FROM user WHERE Id=" . $_userId;
		$db->__exec__($sql);
		return $sql;
	}
	function getAllUsersInvitedFromMe($_userId){
		global $db;
		$sql = "select * from user where invitefrom='$_userId'";
		$result = $db->select($sql);
		if( $result == false){
			return array();
		}
		return $result;
	}
?>