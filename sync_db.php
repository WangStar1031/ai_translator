<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';

set_time_limit(-1);
require_once 'library/MySql.php';
define("DB_TYPE", "mysql");
define("DB_HOST", "127.0.0.1");
define("DB_USER", "root");
// define("DB_NAME", "ai_trans");

if(@file_get_contents(__DIR__."/library/localhost"))
	define("DB_PASSWORD", "");
else
	define("DB_PASSWORD", "1234567812345678");
$db_cur = new Mysql("ai_trans");
$db_cur->exec("set names utf8");
// $db_cur = Mysql::withDBName("ai_trans");

// define("DB_NAME", "newdatabase");

if(@file_get_contents(__DIR__."/library/localhost"))
	define("DB_PASSWORD", "");
else
	define("DB_PASSWORD", "1234567812345678");
$db_jts = new Mysql("newdatabase");
$db_jts->exec("set names utf8");
// $db_jts = Mysql::withDBName("newdatabase");

function word_sync(){
	global $db_jts;
	global $db_cur;
	$strSql = "select * from tbl_words";
	$result = $db_jts->select($strSql);
	echo "<h4>Word table</h4>" . count($result) . "<br/>";
	foreach ($result as $record) {
		$jpn = $record['jpn'];
		$eng = $record['eng'];
		$word_status = $record['word_status'];
		echo "JPN : " . $jpn . " -- ENG : " . $eng;
		$strSql = "select * from tbl_words where jpn='" . $jpn . "'";
		$result_cur = $db_cur->select($strSql);
		if( $result_cur == false){
			$strSql = "INSERT INTO tbl_words(jpn, eng, word_status) VALUES(?,?,?)";
			$stmt = $db_cur->prepare($strSql);
			$stmt->execute( [$jpn, $eng, $word_status]);
			echo " : inserted.";
		} else{
			$record_cur = $result_cur[0];
			if( $record_cur['eng'] == $eng && $record_cur['word_status'] == $word_status){
				echo " : already exists.";
			} else{
				$strSql = "UPDATE tbl_words SET eng=?, word_status=? WHERE jpn=?";
				$stmt = $db_cur->prepare($strSql);
				$stmt->execute( [$eng, $word_status, $jpn]);
				echo " : updated.";
			}
		}
		echo "<br/>";
	}
}
function sentence_sync(){
	global $db_jts;
	global $db_cur;
	$strSql = "select * from tbl_sentences";
	$result = $db_jts->select($strSql);
	echo "<h4>Sentence table</h4><br/>";
	foreach ($result as $record) {
		$jpn = $record['jpn'];
		$eng = $record['eng'];
		$pos = $record['pos'];
		$word_status = $record['word_status'];
		echo "JPN : " . $jpn . " -- ENG : " . $eng;
		$strSql = "select * from tbl_sentences where jpn='" . $jpn . "'";
		$result_cur = $db_cur->select($strSql);
		if( $result_cur == false){
			$strSql = "INSERT INTO tbl_sentences(jpn, eng, pos, word_status) VALUES(?,?,?,?)";
			$stmt = $db_cur->prepare($strSql);
			$stmt->execute( [$jpn, $eng, $pos, $word_status]);
			echo " : inserted.";
		} else{
			$record_cur = $result_cur[0];
			if( $record_cur['eng'] == $eng && $record_cur['pos'] == $pos && $record_cur['word_status'] == $word_status){
				echo " : already exists.";
			} else{
				$strSql = "UPDATE tbl_sentences SET eng=?, pos=? word_status=? WHERE jpn=?";
				$stmt = $db_cur->prepare($strSql);
				$stmt->execute( [$eng, $pos, $word_status, $jpn]);
				echo " : updated.";
			}
		}
		echo "<br/>";
	}
}
function names_sync(){
	global $db_jts;
	global $db_cur;
	$arrTbls = ['jockey', 'trainer'];
	foreach ($arrTbls as $tblName) {
		$strSql = "select * from " . $tblName;
		$result = $db_jts->select($strSql);
		echo "<h4>" . $tblName . " table</h4><br/>";
		foreach ($result as $record) {
			$chn = $record['chn'];
			$jpn = $record['jpn'];
			$eng = $record['eng'];
			$retired = $record['retired'];
			if( $retired != 1)$retired =  0;
			echo "CHN : " . $chn . " -- JPN : " . $jpn . " -- ENG : " . $eng . " Retired : " . $retired;
			$strSql = "select * from tbl_names where chn='" . $chn . "'";
			$result_cur = $db_cur->select($strSql);
			if( $result_cur == false){
				$strSql = "INSERT INTO tbl_names(chn, jpn, eng, retired) VALUES(?,?,?,?)";
				$stmt = $db_cur->prepare($strSql);
				$stmt->execute( [$chn, $jpn, $eng, $retired]);
				echo " : inserted.";
			} else{
				$record_cur = $result_cur[0];
				if( $record_cur['jpn'] == $jpn && $record_cur['eng'] == $eng && $record_cur['retired'] == $retired){
					echo " : already exists.";
				} else{
					$strSql = "UPDATE tbl_names SET jpn=?, eng=? retired=? WHERE chn=?";
					$stmt = $db_cur->prepare($strSql);
					$stmt->execute( [$jpn, $eng, $retired, $chn]);
					echo " : updated.";
				}
			}
			echo "<br/>";
		}	
	}
}
function horse_sync(){
	global $db_jts;
	global $db_cur;
	$strSql = "select * from horse";
	$result = $db_jts->select($strSql);
	echo "<h4>Horse table</h4><br/>";
	foreach ($result as $record) {
		$href = $record['href'];
		$jpn = $record['jpn'];
		$eng = $record['eng'];
		$word_status = $record['word_status'];
		echo "JPN : " . $jpn . " -- ENG : " . $eng;
		$strSql = "select * from horse where jpn='" . $jpn . "'";
		$result_cur = $db_cur->select($strSql);
		if( $result_cur == false){
			$strSql = "INSERT INTO horse(jpn, eng, href) VALUES(?,?,?)";
			$stmt = $db_cur->prepare($strSql);
			$stmt->execute( [$jpn, $eng, $href]);
			echo " : inserted.";
		} else{
			$record_cur = $result_cur[0];
			if( $record_cur['eng'] == $eng && $record_cur['href'] == $href){
				echo " : already exists.";
			} else{
				$strSql = "UPDATE horse SET eng=?, href=? WHERE jpn=?";
				$stmt = $db_cur->prepare($strSql);
				$stmt->execute( [$eng, $href, $jpn]);
				echo " : updated.";
			}
		}
		echo "<br/>";
	}
}
// word_sync();
// sentence_sync();
// names_sync();
horse_sync();
?>