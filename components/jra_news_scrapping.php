<?php
set_time_limit(-1);
header('content-type: text/html; charset=shift-jis');
$site_Url = 'http://www.jra.go.jp/';

require_once 'library/simple_html_dom.php';
require_once 'library/api_send_SMS.php';

function getFirstNumber($_string){
	$retVal = "";
	$isFound = false;
	for( $i = 0; $i < strlen($_string); $i++){
		$buf = substr($_string, $i, 1);
		if( is_numeric($buf)){
			$retVal .= $buf;
			$isFound = true;
		} else if($isFound == true){
			return $retVal;
		}
	}
}
echo "<div class='jra_news'>";
echo "<h2>JRA News Reports</h2>";
$contents = file_get_contents($site_Url . "news/");
$html = str_get_html($contents);
$month = $html->find("#month_header .content h3")[0]->text();
$nYear = getFirstNumber($month);
$month = substr($month, strlen($nYear));
$nMonth = getFirstNumber($month);

$lstNews = $html->find(".news_list_area .news_list_unit");

$prevContents = @file_get_contents( "jranews/logs/jraNews.json");
$prevNews = [];
if( $prevContents){
	$prevNews = json_decode($prevContents);
}
$hrefs = array();
$titles = array();
foreach ($lstNews as $record) {
	$date = $record->find(".block_sub_header h4")[0];
	$strDate = $date->text();
	$nDate = getFirstNumber( substr($strDate, 2));
	$news_date = $nYear . "-" . $nMonth . "-" . $nDate;
	// echo "<h3>" . $date->innertext() . "</h3>";
	$news_per_date = $record->find("ul li");
	// print_r($news_per_date);
	echo "<h3>" . date("F jS, Y", strtotime($news_date)) . "</h3>";
	$newsNumber = 0;
	echo "<ul>";
	foreach ($news_per_date as $news) {
		echo "<li>";
		$newsNumber++;
		$news_cat = $news->class;//other, race, event
		$href = $news->find(".news_line .txt a")[0]->href;
		$news_href = $site_Url . $href;
		$news_total = $news->find(".news_line .txt a")[0];
		$news_title = iconv("SJIS", "UTF-8", $news_total->text());
		array_push($hrefs, $news_href);
		array_push($titles, $news_title);
		$newsFileName = "JraNews" . str_replace("#","_ID_",str_replace("/", "-", $href)) . ".log";
		if( array_search($news_href, $prevNews) === false){
			$newsContents = file_get_contents($news_href);
			if( ($pos = strpos($newsFileName, "_ID_")) !== false){
				$_id = substr($href, strpos($href, "#"));
				$html_contents = str_get_html($newsContents);
				$newsContents = $html_contents->find($_id)[0]->innertext();
			} else if( ($pos = strpos($newsFileName, "other")) !== false){

			} else if( ($pos = strpos($newsFileName, "special")) ){

			} else if( ($pos = strpos($newsFileName, "index")) ){

			} else if( ($pos = strpos($newsFileName, "keiba")) ){

			} else if( ($pos = strpos($newsFileName, "datafile")) ){

			} else{
				$html_contents = str_get_html($newsContents);
				if( count($html_contents->find("#jra_news"))){
					$newsContents = iconv("SJIS", "UTF-8", $html_contents->find("#jra_news")[0]->innertext());
				} else{
					$newsContents = $html_contents->find("#contentsBody")[0]->innertext();
				}
			}
			file_put_contents("jranews/logs/" . $newsFileName, $newsContents);
			// sendSMSByCatagory("JRA_NEWS", $news_title);
		}
		echo "<p class='newsNumber " . $news_cat ."'><span class='title'><a target='_blank' href='" . $news_href . "'> News " . $newsNumber . "</a>";
		echo "</span>";
			echo "<a target='_blank' href='jra_news_reports.php?fName=" . $newsFileName . "'>" . $news_total->text() . "</a>";
		echo "</p>";
		echo "</li>";
	}
	echo "</ul>";
}
echo "</div>";
file_put_contents( "jranews/logs/jraNews.json", json_encode($hrefs));
file_put_contents( "jranews/logs/jraNewsTitles.json", json_encode($titles));
?>
<script type="text/javascript">
	setTimeout(function(){
		location.reload();
	}, 600000);
</script>