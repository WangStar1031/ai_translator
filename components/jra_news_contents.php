<?php
header('content-type: text/html; charset=shift-jis');
$fContents = @file_get_contents("jranews/logs/" . $fName);
echo "<div class='jra_news_contents col-lg-12'>";
echo "<a href='jra_news_reports.php?category=1'>&lt;&lt; Back</a><br/>";
$strPrinted = "";
if( strpos($fName, "index")){
	$strPrinted = $fContents;
} else if( strpos($fName, "gallery")){
	$strPrinted = $fContents;
} else{
	$strPrinted = iconv("UTF-8", "SJIS", $fContents);
}

require_once "library/jra_news_parser.php";
$strPrinted = adjustImgUrl($fName, $strPrinted);

if( strpos($fName, "_ID_")){
	$strPrinted = parseID($fName, $strPrinted);
} else if( strpos($fName, "other")){
	$strPrinted = parseOther($fName, $strPrinted);
} else if( strpos($fName, "special")){

} else if( strpos($fName, "keiba")){
	$strPrinted = parseKeiba($fName, $strPrinted);
} else if( strpos($fName, "index")){
	$strPrinted = parseIndex($fName, $strPrinted);
} else if( strpos($fName, "datafile")){
	$strPrinted = parseDatafile($fName, $strPrinted);
} else if( strpos($fName, "gallery")){
	$strPrinted = parseGallery($fName, $strPrinted);
} else {
	$strPrinted = parseNormal($fName, $strPrinted);
}
print_r($strPrinted);
echo "</div>";
?>
<style type="text/css">
	.jra_news_contents a, .jra_news_contents a:hover{
		color: green;
		text-decoration: none;
	}
</style>