<?php
	$db_name = "ai_trans_news";
	$link_href = $_SERVER["REQUEST_URI"];
	$lang = "jp";
	if( isset($_POST['lang'])) $lang = $_POST['lang'];
	if( isset($_GET['lang'])) $lang = $_GET['lang'];
	if($lang == "en") $link_href = str_replace("&lang=en", "", $link_href);
	else $link_href .= "&lang=en";

	if( $lang == 'jp' && $category == 1){
		echo '<div class="col-lg-12"><h2>JRA' . iconv("UTF-8", "SJIS", 'ニュースレポート');
	} else{
		echo '<div class="col-lg-12"><h2>JRA News Reports';
	}
	if( $category == 1){
		echo '<span style="font-size:0.5em;"><a href="'.$link_href.'" style="text-decoration: none; color: green;"> ( '.(($lang == "en")?"Japanese":iconv("UTF-8", "SJIS", "英語")).' ) </a></span>';
	}
	echo '</h2></div>';
	switch ($category) {
	case 0:
		include __DIR__ . '/dictionary.php';
		break;
	case 1:
		require_once "library/jra_00.php";
		require_once 'library/trans_lib.php';

		require_once 'library/fpdf/fpdf.php';
		$fName = "";
		if( isset($_POST['fName']))$fName = $_POST['fName'];
		if( isset($_GET['fName'])) $fName = $_GET['fName'];
		if( $fName == ""){
			include __DIR__ . '/jra_news_scrapping.php';
		} else{
			include __DIR__ . '/jra_news_contents.php';
		}
		break;
	case 2:
		// require_once 'library/simple_html_dom.php';
		// $url4File = str_replace("-", "/", $fileName);
		// $pos = strrpos($url4File, "/");
		// $url = substr($url4File, 0, $pos);

		// $pos = strpos($url, "/");
		// $url = substr($url, $pos);

		// $fileContents = @file_get_contents("jranews/logs/" . $fileName);
		// $html = str_get_html($fileContents);
		// $arrHrefs = $html->find("a");
		// foreach ($arrHrefs as $hrefs) {
		// 	$hrefs->href = "http://www.jra.go.jp/" . $url . "/" .  $hrefs->href;
		// }
		// $arrImgs = $html->find("img");
		// foreach ($arrImgs as $imgs) {
		// 	$imgs->src = "http://www.jra.go.jp/" . $url . "/" .  $imgs->src;
		// }

		// print_r($html->__toString());
		break;
	}
?>