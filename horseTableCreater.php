<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

echo '<meta http-equiv="content-type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">';

set_time_limit(-1);
require_once 'library/trans_lib.php';
$dir = "./logs/backup/JBIS/*_jbis.json";
$files = glob($dir);
foreach ($files as $file) {
	echo "<a href='$file'>" . $file . "</a>";
	array_push($arrFiles, $file);
	$contents = json_decode( file_get_contents($file));
	// print_r($contents[0]);
	echo "<br/>";
	foreach ($contents as $row) {
		$href = $row->href;
		$jpn = $row->jpn;
		$eng = $row->eng;
		$retVal = insertNewHorseName($href, $jpn, $eng);
		switch ($retVal) {
			case 1: 
				echo " Horse Info : <a href='http://www.jbis.or.jp".$href."'>japan name : " . $jpn . " , english name : " . $eng . "</a>";
				echo " : successfuly inserted."; 
				echo "<br/>";
				break;
			case 0: break;
			case -1: break;
		}
	}
	echo "<br/>";
}
?>