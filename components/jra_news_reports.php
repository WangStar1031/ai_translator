<?php
	switch ($category) {
	case 0:
		include __DIR__ . '/dictionary.php';
		break;
	case 1:
		include __DIR__ . '/jra_news_scrapping.php';
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