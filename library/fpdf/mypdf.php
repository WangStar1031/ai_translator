<?php
require_once('library/fpdf/fpdf.php');
/**
 * 
 */
class CustomPDF extends FPDF
{
	function Footer()
	{
		$this->SetY(-15);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell(0,20, 'Copyright Vision Translation Inc. - The information contained in this translation is non-transferrable and not for redistribution. For other parties interested in this information please contact c.green@translate.vision.');
	}
	function Header()
	{
		$pdf->Image("app/img/vision-logo.jpg", 12, 12, 10);
	}
}
?>