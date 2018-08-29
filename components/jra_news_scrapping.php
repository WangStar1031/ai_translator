<?php
set_time_limit(-1);
header('content-type: text/html; charset=shift-jis');
$site_Url = 'http://www.jra.go.jp/';

$arrNews = [];
$newsContents = @file_get_contents("jranews/logs/jraNewsJson.json");
if( $newsContents){
	$arrNews = json_decode($newsContents);
} else{
	echo "No News.";
	exit();
}
echo "<div class='jra_news'>";
$link_href = $_SERVER["REQUEST_URI"];
$preDate = "";
$newsNumber = 0;

require_once "library/trans_lib.php";

foreach ($arrNews as $news) {
	if( $preDate != $news->news_Date){
		if( $preDate != ""){
			echo "</ul>";
		}
		if( $lang == 'jp'){
			echo "<h3>" . iconv("UTF-8", "SJIS", date("Y年n月j日", strtotime($news->news_Date))) . "</h3>";
		} else{
			echo "<h3>" . date("F jS, Y", strtotime($news->news_Date)) . "</h3>";
		}
		echo "<ul>";
		$preDate = $news->news_Date;
		$newsNumber = 0;
	}
	echo "<li>";
	$newsNumber++;
	if( $lang == "jp"){
		$title = iconv("UTF-8", "SJIS", $news->news_Title);
	} else{
		$patterns = get_word_patterns(false);
		$sentences = get_sentence_patterns(false);
		$title = process_individual( $news->news_Title);
	}
	echo "<p class='newsNumber " . $news->news_Cat . "'><span class='title'><a target='_blank' href='" . $news->news_Url . "'> ";
	if( $lang == 'jp'){
		echo iconv("UTF-8", "SJIS", "ニュース ");
	} else {
		echo "News ";
	}
	echo $news->news_Number . "</a></span><a href='" . $link_href . "&fName=" . $news->news_FileName . "'>" . $title . "</a></p>";
	echo "</li>";
}
echo "</div>";
?>