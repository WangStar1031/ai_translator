<?php
require_once __DIR__ . '/simple_html_dom.php';
function adjustImgUrl( $_fName, $_strHtml){
	$arrUrls = explode("--", $_fName);
	$realUrlPrefix = "http://www.jra.go.jp/";
	for( $i = 1; $i < count($arrUrls) - 1; $i++){
		$realUrlPrefix .= $arrUrls[$i] . "/";
	}
	$_strHtml = str_replace('<img src="im', '<img src="' . $realUrlPrefix . "im", $_strHtml);
	$_strHtml = str_replace('<img src="./', '<img src="' . $realUrlPrefix, $_strHtml);
	$_strHtml = str_replace('<img src="/', '<img src="http://www.jra.go.jp/', $_strHtml);
	while( strpos($_strHtml, '<img src="../') !== false){
		$pos = strpos($_strHtml, '<img src="../') + strlen('<img src="');
		$before = substr($_strHtml, 0, $pos);
		$after = substr($_strHtml, $pos);
		$nCount = 0;
		while( strpos(substr($before, $nCount * 3, ($nCount + 1) * 3), "../")){
			$nCount++;
		}
		$realUrlPrefix = "http://www.jra.go.jp/";
		for( $i = 1; $i < count($arrUrls) - $nCount - 1; $i++){
			$realUrlPrefix .= $arrUrls[$i];
		}
		$_strHtml = $before . $realUrlPrefix . substr($after, $nCount * 3);
	}
	return $_strHtml;
}
function removeID($_strHtml, $_ID){
	$html = str_get_html($_strHtml);
	if( count($html->find("#" . $_ID))){
		$ID = $html->find("#" . $_ID)[0]->innertext();
		$_strHtml = str_replace( $ID, '', $_strHtml);
	}
	return $_strHtml;
}
function removeClass($_strHtml, $_Class){
	$html = str_get_html($_strHtml);
	if( count($html->find("." . $_Class))){
		$Class = $html->find("." . $_Class)[0]->innertext();
		$_strHtml = str_replace( $Class, '', $_strHtml);
	}
	return $_strHtml;
}
function parseID( $_fName, $_strHtml){
	$_strHtml = removeID( $_strHtml, "page_top_btn");
	return $_strHtml;
}
function parseOther($_fName, $_strHtml){
	$_strHtml = removeClass( $_strHtml, "option_data");
	$_strHtml = removeID( $_strHtml, "page_top_btn");
	return $_strHtml;
}
function parseKeiba( $_fName, $_strHtml){
	$_strHtml = removeID( $_strHtml, "page_top_btn");
	$_strHtml = removeClass( $_strHtml, "nav");
	$_strHtml = removeClass( $_strHtml, "link_list");
	return $_strHtml;
}
function parseIndex( $_fName, $_strHtml){
	$_strHtml = removeClass( $_strHtml, "c-kv__inner__share");
	return $_strHtml;
}
function parseGallery( $_fName, $_strHtml){
	$_strHtml = removeID( $_strHtml, "page_top_btn");
	return $_strHtml;
}
function parseDatafile( $_fName, $_strHtml){
	$_strHtml = removeID($_strHtml, "page_top_btn");
	return $_strHtml;
}
function parseNormal( $_fName, $_strHtml){
	$_strHtml = removeClass( $_strHtml, "option_data");
	$_strHtml = removeClass( $_strHtml, "headBlock");
	$_strHtml = removeID( $_strHtml, "page_top_btn");
	$_strHtml = removeID( $_strHtml, "pageTop");
	return $_strHtml;
}
function parseNormal_1( $_strHtml){
	$html = str_get_html($_strHtml);
	$strTitle = $html->find(".news_title h2")[0]->innertext();
	$html_Body = $html->find(".news_body")[0];
	$html_Block_Unit = $html_Body->find(".block_unit");
	$arrUnits = [];
	foreach ($html_Block_Unit as $unit) {
		$objUnit = new \stdClass;
		if( !count($unit->find(".block_sub_header h4")))
			continue;
		$objUnit->strTitle = $unit->find(".block_sub_header h4")[0]->innertext();
		$objUnit->lstEvents = [];
		$events = $unit->find(".event_list li");
		foreach ($events as $event) {
			$event_Title = $event->find("h5")[0]->innertext();
			$details_Header = $event->find("h6");
			$details_Contents = $event->find("p");
			$event_Details = [];
			for($i = 0; $i < count($details_Header); $i++){
				$header = $details_Header[$i]->innertext();
				$contents = explode("<br>", $details_Contents[$i]->innertext());
				$objEvent = new \stdClass;
				$objEvent->header = $header;
				$objEvent->contents = $contents;
				$event_Details[] = $objEvent;
			}
		}
		$objUnit->extraTitle = "";
		if( count($unit->find("h5.level5.mt20"))){
			$objUnit->extraTitle = $unit->find("h5.level5.mt20")[0]->innertext();
		}
		$objUnit->extraDetails = "";
		if( count($unit->find("p.mt10"))){
			$objUnit->extraDetails = $unit->find("p.mt10")[0]->innertext();
		}
		$objUnit->comment = [];
		foreach ($unit->find(".comment_unit li") as $comment) {
			$objComment = new \stdClass;
			$objComment->title = $comment->find("p")[0]->innertext();
			$objComment->contents = $comment->find("p")[1]->innertext();
			$objUnit->comment[] = $objComment;
		}
		$arrUnits[] = $objUnit;
		// print_r($objUnit);
		// echo "<br>";
		// echo "<br>";
	}
	$objRet = new \stdClass;
	$objRet->title = $strTitle;
	$objRet->arrUnits = $arrUnits;
	return $objRet;
}
?>