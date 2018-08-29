<?php
ini_set('max_execution_time', 3600);
set_time_limit(0);

require_once __DIR__.'/vendor/autoload.php'; // load composer
require_once '../library/trans_lib.php';

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

function __open_xls($__file_name){
	$__return = array();
	$__headers = array();
	
	$reader = new Xls();
	$objPHPExcel = $reader->load($__file_name);
	$cur_sheet = $objPHPExcel->getSheet(0);
	
	$highestColumm = $cur_sheet->getHighestColumn();
	$highestRowIndex = $cur_sheet->getHighestRow();
	$highestColumnIndex = Coordinate::columnIndexFromString($highestColumm);

	for($i=1; $i<=$highestColumnIndex; $i++)
	{
		$__headers[$i] = $cur_sheet->getCellByColumnAndRow($i, 1)->getValue();
	}

	for($j=2; $j<=$highestRowIndex; $j++){
		$__obj = new \stdClass;
		for($i=1; $i<=count($__headers); $i++){
			$__fld_name = $__headers[$i];
			$__obj->$__fld_name = $cur_sheet->getCellByColumnAndRow($i, $j)->getValue();
		}
		array_push($__return, $__obj);
	}
	return $__return;
}

$case = "";
if(isset($_GET['case'])) $case = $_GET['case'];
if(isset($_POST['case'])) $case = $_POST['case'];
$type = "words";
if(isset($_GET['type'])) $type = $_GET['type'];
if(isset($_POST['type'])) $type = $_POST['type'];
$type = strtolower($type);

$file_name = $_FILES["uploadedFile"]["tmp_name"];
$result = __open_xls($file_name);
__process_import_data($type, $result);

?>