<?php
set_time_limit(-1);
header('content-type: text/html; charset=shift-jis');
$site_Url = 'http://www.jra.go.jp/';
$lang = 'jp';
require_once 'library/simple_html_dom.php';
require_once 'library/api_send_SMS.php';
require_once 'library/trans_lib.php';

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
$contents = file_get_contents($site_Url . "news/");
$html = str_get_html($contents);
$month = $html->find("#month_header .content h3")[0]->text();
$nYear = getFirstNumber($month);
$month = substr($month, strlen($nYear));
$nMonth = getFirstNumber($month);

$lstNews = $html->find(".news_list_area .news_list_unit");

$prevNews = [];
$hrefs = array();
$titles = array();
$patterns = get_word_patterns();
$sentences = get_sentence_patterns();

$link_href = $_SERVER["REQUEST_URI"];
$prevNewsContents = @file_get_contents("jraNews/logs/jraNewsJson.json");
$arrPrevNews = [];
if( $prevNewsContents){
	$arrPrevNews = json_decode($prevNewsContents);
	foreach($arrPrevNews as $pre_News) {
		$prevNews[] = $pre_News->news_Url;
	}
}

$arrNews = [];

foreach ($lstNews as $record) {

	$date = $record->find(".block_sub_header h4")[0];
	$strDate = $date->text();
	$nDate = getFirstNumber( substr($strDate, 2));
	$news_date = $nYear . "-" . $nMonth . "-" . $nDate;
	$news_per_date = $record->find("ul li");
	echo "<h3>" . date("F jS, Y", strtotime($news_date)) . "</h3>";
	$newsNumber = 0;
	echo "<ul>";
	foreach ($news_per_date as $news) {
		echo "<li>";
		$newsNumber++;
		$news_cat = $news->class;
		$href = $news->find(".news_line .txt a")[0]->href;
		$news_href = $site_Url . $href;
		$news_total = $news->find(".news_line .txt a")[0];
		$news_title = iconv("SJIS", "UTF-8", $news_total->text());
		array_push($hrefs, $news_href);
		array_push($titles, $news_title);
		$newsFileName = "JraNews" . str_replace("#","_ID_",str_replace("/", "--", $href));
		if( strrpos($newsFileName, "-") == strlen($newsFileName) - 1){
			$newsFileName = substr($newsFileName, 0, strlen($newsFileName) - 1);
		}
		$newsFileName .= ".log";
		if( array_search($news_href, $prevNews) === false){
			$newsContents = file_get_contents($news_href);
			if( strpos($newsFileName, "_ID_") !== false){
				$_id = substr($href, strpos($href, "#"));
				$html_contents = str_get_html($newsContents);
				$newsContents = $html_contents->find($_id)[0]->innertext();
			} else if( strpos($newsFileName, "other") !== false){
				$html_contents = str_get_html( $newsContents);
				if( count($html_contents->find("#contentsBody"))){
					$newsContents = iconv("SJIS", "UTF-8", $html_contents->find("#contentsBody")[0]->innertext());
				} else{
					$newsContents = iconv("SJIS", "UTF-8", $newsContents);
				}
			} else if( strpos($newsFileName, "special") !== false ){
				continue;
			} else if( strpos($newsFileName, "keiba") ){
				$html_contents = str_get_html( $newsContents);
				if( count($html_contents->find("#contentsBody"))){
					$newsContents = $html_contents->find("#contentsBody")[0]->innertext();//iconv("SJIS", "UTF-8", $html_contents->find("#contentsBody")[0]->innertext());
				} else{
					// $newsContents = iconv("SJIS", "UTF-8", $newsContents);
				}

			} else if( strpos($newsFileName, "index") ){
				$html_contents = str_get_html($newsContents);
				if( count($html_contents->find("#contentsBody"))){
					$newsContents = $html_contents->find("#contentsBody")[0]->innertext();
				} else if( count($html_contents->find("#contents"))){
					// $newsContents = iconv("SJIS", "UTF-8", $html_contents->find("#contents")[0]->innertext());
					$newsContents = $html_contents->find("#contents")[0]->innertext();
				}
			} else if( strpos($newsFileName, "datafile") ){
				$html_contents = str_get_html( $newsContents);
				if( count($html_contents->find("#contentsBody"))){
					$newsContents = iconv("SJIS", "UTF-8", $html_contents->find("#contentsBody")[0]->innertext());
				} else{
					$newsContents = iconv("SJIS", "UTF-8", $newsContents);
				}
			} else if( strpos($newsFileName, "gallery")){
				$html_contents = str_get_html( $newsContents);
				if( count($html_contents->find("#contentsBody"))){
					$newsContents = $html_contents->find("#contentsBody")[0]->innertext();
				}
			} else{
				$html_contents = str_get_html($newsContents);
				$isJraNews = false;
				if( count($html_contents->find("#jra_news"))){
					$newsContents = iconv("SJIS", "UTF-8", $html_contents->find("#jra_news")[0]->innertext());
					$isJraNews = true;
				} else{
					$newsContents = $html_contents->find("#contentsBody")[0]->innertext();
				}
			}
			file_put_contents("jranews/logs/" . $newsFileName, $newsContents);
			// sendSMSByCatagory("JRA_NEWS", $news_title);
		}
		$news_href = str_replace("www.jra.go.jp//", "www.jra.go.jp/", $news_href);
		echo "<p class='newsNumber " . $news_cat ."'><span class='title'><a target='_blank' href='" . $news_href . "'> News " . $newsNumber . "</a>";
		echo "</span>";
			echo "<a href='" . $link_href . "&fName=" . $newsFileName . "'>" . ( $lang == "en" ? process_individual_sentence($news_title) : $news_total->text()) . "</a>";
		echo "</p>";
		echo "</li>";

		$objNews = new \stdClass();
		$objNews->news_Date = $news_date;
		$objNews->news_Number = $newsNumber;
		$objNews->news_Cat = $news_cat;
		$objNews->news_Title = $news_title;
		$objNews->news_Url = $news_href;
		$objNews->news_FileName = $newsFileName;
		$arrNews[] = $objNews;
	}
	echo "</ul>";
}
echo "</div>";
// file_put_contents( "jranews/logs/jraNews.json", json_encode($hrefs));
// print_r($arrNews);
file_put_contents("jranews/logs/jraNewsJson.json", json_encode($arrNews));
?>
<script type="text/javascript">
	setTimeout(function(){
		location.reload();
	}, 600000);
</script>