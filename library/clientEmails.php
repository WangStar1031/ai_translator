<?php

	// require_once './html2fpdf/html2fpdf.php';
	// $htmlFile = "http://dataminer.jts.ec/JTS/api_keiba_notice.php?c=14&d_val=20180714&lang=en";
	// $buffer = file_get_contents($htmlFile);
	// print_r($buffer);
	// $pdf = new HTML2FPDF('P', 'mm', 'Legal');
	// $pdf->AddPage();
	// $pdf->WriteHTML($buffer);
	// $pdf->Output('my.pdf', 'F');
	// echo "done";

function getCustomerInfos(){
	$_contents = @file_get_contents("customerInfo.txt");
	return $_contents;
}
function setCustomerInfos($_contents){
	@file_put_contents("customerInfo.txt", ($_contents));
}
$_data = array();
if( isset($_POST['ClientDatas'])){
	setCustomerInfos( $_POST['ClientDatas']);
}
if( isset($_POST['getCustomers'])){
	echo getCustomerInfos();
}
?>