<?php
	// if(!defined("DB_TYPE"))define("DB_TYPE", "mysql");
	// if(!defined("DB_HOST"))define("DB_HOST", "127.0.0.1");
	// if(!defined("DB_USER"))define("DB_USER", "root");
	// if(!defined("DB_NAME"))define("DB_NAME", "ai_trans");

	// if(@file_get_contents(__DIR__."/localhost"))
	// 	if(!defined("DB_PASSWORD"))define("DB_PASSWORD", "");
	// else
	// 	if(!defined("DB_PASSWORD"))define("DB_PASSWORD", "1234567812345678");

	require_once __DIR__ . '/MySql.php';
	require_once __DIR__ . '/google/vendor/autoload.php';
	require_once __DIR__ . '/google_free/vendor/autoload.php';
	require_once __DIR__ . '/api_send_SMS.php';
	if( isset($db_name) && $db_name != ""){
		$db = new Mysql($db_name);
	} else{
		$db = new Mysql("ai_trans");
	}
    $db->exec("set names utf8");

    function __check_change_status($result, $pattern) {
		$Id = $pattern->Id;
		$jpn = $pattern->Japanese;
		$eng = $pattern->English;
		$word_status = $pattern->Status;
		foreach ($result as $origin_pattern) {
			if($origin_pattern["Id"] == $Id) {
				if(($origin_pattern["jpn"] == $jpn)&&($origin_pattern["eng"] == $eng)&&($origin_pattern["word_status"] == $word_status))
					return true;
				return false;
			}
		}
		return false;
    }

    function __process_import_data($type, $result) {
    	global $db;

    	//$tbl_name = "tbl_".$type."_copy";
    	$tbl_name = "tbl_".$type."";

    	$origin_result = __get_data_for_export("", $type);

    	foreach ($result as $pattern) {
    		if(!($pattern->Japanese)) {
    			continue;
    		}
    		$Id = $pattern->Id;
    		if($Id) {
    			if(($origin_result) && __check_change_status($origin_result, $pattern)) continue;
    			$strsql = "delete from $tbl_name where Id='$Id'";
    			$db->__exec__($strsql);
    		}
    		$strsql = "insert into $tbl_name ";
    		$key_str = "";
    		$value_str = "";
    		$record_values = [];
    		if(($type != "words") && (!isset($pattern->Position))) {
    			$pattern->Position = "";
    		}
    		foreach ($pattern as $key => $value) {
    			if(!($Id) && ($key == "Id")) continue;
    			if(($type == "words") && ($key == "Position")) continue;

    			if($key == "Japanese") $key = "jpn";
    			if($key == "English") $key = "eng";
    			if($key == "Position") $key = "pos";
    			if($key == "Status") $key = "word_status";
    			if($key_str) $key_str .= ", ";
    			if($value_str) $value_str .= ", ";
    			$key_str .= $key;
    			$value_str .= "?";

    			$record_values[] = $value;
    		}
    		$strsql .= "(".$key_str.") values (".$value_str.");";
			$stmt= $db->prepare($strsql);
			$stmt->execute( $record_values );			
			$cn++;
    	}

    	if($type == "words") {
    		$patterns = get_word_patterns(false);
	    	file_put_contents("../library/words.json", json_encode($patterns));
    	} else {
    		$sentences = get_sentence_patterns(false);
	    	file_put_contents("../library/sentences.json", json_encode($sentences));
    	}
    }

    function __get_data_for_export($case="", $type="words") {
    	global $db;

    	$strsql = "select * from tbl_".$type;
    	if($case == "google") $strsql .= " where word_status=0";
    	else if($case == "hand") $strsql .= " where word_status=1";
    	$strsql .= " order by Id";
    	return $db->select($strsql);
    }

	function __get_translate($text, $case="google_free") {
		$text = str_replace('「', '', $text);
		$text = str_replace('」', '', $text);
		$str = "";
		if($case == "google_free")
			$str = Stichoza\GoogleTranslate\TranslateClient::translate('ja', 'en', $text);
		else {
			$translate = new Google\Cloud\Translate\TranslateClient(['keyFilePath' => "google/JRA.json"]);
			$result = $translate->translate($text);
			$str = $result["text"];
		}
		return $str;
	}

    function register_horse_info($jpn, $eng) {
    	global $db;

    	$words = $db->select('select Id from tbl_words where jpn="'.$jpn.'"');
    	if(count($words) && (($words[0]["Id"]))){
    		register_word_pattern($words[0]["Id"], $jpn, $eng, 1);
    	} else {
			$sql = "INSERT INTO tbl_words (jpn, eng, word_status) VALUES ( ?, ?, '1' )";
			$stmt= $db->prepare($sql);
			$stmt->execute([$jpn, $eng]);
    	}
    }

    function insert_new_word($jpn, $eng) {
    	global $db;

    	$words = $db->select('select Id from tbl_words where jpn="'.$jpn.'"');
    	if(count($words) && (($words[0]["Id"]))){
    		register_word_pattern($words[0]["Id"], $jpn, $eng, 1);
    	} else {
			$sql = "INSERT INTO tbl_words (jpn, eng, word_status) VALUES ( ?, ?, '1' )";
			$stmt= $db->prepare($sql);
			$stmt->execute([$jpn, $eng]);
    	}
    }

    function insert_new_sentence($jpn, $eng, $pattern) {
    	global $db;

    	$words = $db->select('select Id from tbl_sentences where jpn="'.$jpn.'"');
    	if(count($words) && (($words[0]["Id"]))){
    		register_sentence_pattern($words[0]["Id"], $jpn, $eng, $pattern,  1);
    	} else {
			$sql = "INSERT INTO tbl_sentences (jpn, eng, pos, word_status) VALUES ( ?, ?, ?, '1' )";
			$stmt= $db->prepare($sql);
			$stmt->execute([$jpn, $eng, $pattern]);
    	}
    }

    function register_word_pattern($Id, $jpn, $eng, $word_status = 0) {
    	global $db;

		if($word_status) {
			$sql = "UPDATE tbl_words SET eng=?, word_status=? WHERE Id=?";
			$stmt= $db->prepare($sql);
			$stmt->execute([$eng, $word_status, $Id]);
		} else {
			$sql = "UPDATE tbl_words SET eng=? WHERE Id=?";
			$stmt= $db->prepare($sql);
			$stmt->execute([$eng, $Id]);
		}

		$patterns = get_word_patterns(false);
		file_put_contents("words.json", json_encode($patterns));
    }

    function register_sentence_pattern($Id, $jpn, $eng, $pos, $word_status = 0) {
    	global $db;

    	if($word_status) {
			$sql = "UPDATE tbl_sentences SET jpn=?, eng=?, pos=?, word_status=? WHERE Id=?";
			$stmt= $db->prepare($sql);
			$stmt->execute([$jpn, $eng, $pos, $word_status, $Id]);
		} else {
			$sql = "UPDATE tbl_sentences SET jpn=?, eng=?, pos=? WHERE Id=?";
			$stmt= $db->prepare($sql);
			$stmt->execute([$jpn, $eng, $pos, $Id]);
		}

	    $sentences = get_sentence_patterns(false);
	    file_put_contents("sentences.json", json_encode($sentences));
    }

    function delete_word_pattern($Id) {
    	global $db;

    	$sql = 'Delete from tbl_words where Id = "'.$Id.'"';
	    $db->__exec__($sql);

	    $patterns = get_word_patterns(false);
	    file_put_contents("words.json", json_encode($patterns));
    }

    function delete_sentence_pattern($Id) {
    	global $db;

    	$sql = 'Delete from tbl_sentences where Id = "'.$Id.'"';
	    $db->__exec__($sql);
	    
	    $sentences = get_sentence_patterns(false);
	    file_put_contents("sentences.json", json_encode($sentences));
    }

	function get_word_patterns_page($page_num, $select_case, $search_key, $sort_field, $offset = 20) {
		global $db;

		$strsql = "select * from tbl_words where (1=1) ";
		if($select_case != -1) $strsql .= " and word_status='".$select_case."'";
		if($search_key) $strsql .= " and (jpn like '%".$search_key."%' or eng like '%".$search_key."%')";
		$strsql .= " order by ".$sort_field;
		$strsql .= " limit ".($page_num * $offset).",".$offset;
		$patterns = $db->select($strsql);
		return $patterns;
	}

	function get_word_patterns_page_count($select_case, $search_key)
	{
		global $db;

		$strsql = "select count(*) cn from tbl_words where (1=1) ";
		if($select_case != -1) $strsql .= " and word_status='".$select_case."'";
		if($search_key) $strsql .= " and (jpn like '%".$search_key."%' or eng like '%".$search_key."%')";
		$patterns = $db->select($strsql);
		if(count($patterns) > 0 && isset($patterns[0]["cn"])) return $patterns[0]["cn"];
		return 0;
	}

	function get_sentence_patterns_page_count($select_case, $search_key)
	{
		global $db;

		$strsql = "select count(*) cn from tbl_sentences where (1=1) ";
		if($select_case != -1) $strsql .= " and word_status='".$select_case."'";
		if($search_key) $strsql .= " and (jpn like '%".$search_key."%' or eng like '%".$search_key."%')";
		$patterns = $db->select($strsql);
		if(count($patterns) > 0 && isset($patterns[0]["cn"])) return $patterns[0]["cn"];
		return 0;
	}


	function get_sentence_pattern_from_id($Id) {
		global $db;

		$strsql = "select * from tbl_sentences where Id = '".$Id."'";
		$patterns = $db->select($strsql);
		if(count($patterns) > 0 && isset($patterns[0]["Id"])) return $patterns[0];
		return false;
	}


	function get_sentence_patterns_page($page_num, $select_case, $search_key, $sort_field, $offset = 10) {
		global $db;

		$strsql = "select * from tbl_sentences where (1=1) ";
		if($select_case != -1) $strsql .= " and word_status='".$select_case."'";
		if($search_key) $strsql .= " and (jpn like '%".$search_key."%' or eng like '%".$search_key."%')";
		$strsql .= " order by ".$sort_field;
		$strsql .= " limit ".($page_num * $offset).",".$offset;
		$patterns = $db->select($strsql);
		return $patterns;
	}

	function get_word_patterns($offline = true) {
		global $db;

		if($offline){
			if(file_exists("words.json")) return json_decode(@file_get_contents("words.json"));
		}

		$patterns = $db->select("select * from tbl_words order by Id");
		return $patterns;
	}

	function get_sentence_patterns($offline = true) {
		global $db;

		if($offline){
			if(file_exists("sentences.json")) return json_decode(@file_get_contents("sentences.json"));
		}

		$sentences = $db->select("select * from tbl_sentences order by Id");
		return $sentences;
	}
	
	function process_date_time_value($event) {		
		$sentences = array(
			array(
				"jpn" => "{}時{}分",
				"eng" => "{}:{}",
				"pos" => "0,1",
			),
			array(
				"jpn" => "平成{}年{}月{}日",
				"eng" => "{}-{}-{}",
				"pos" => "0,1,2",
			)
		);

		foreach ($sentences as $sentence) {
			$arr_jpn = explode("{}", $sentence["jpn"]);
			$arr_eng = explode("{}", $sentence["eng"]);
			$arr_pos = explode(",", $sentence["pos"]);
			$arr_data = [];
			$check_matching = true;

			for($i=0; $i<count($arr_jpn); $i++){
				if(($arr_jpn[$i] != "") && (strpos($event, $arr_jpn[$i]) === false)){
					$check_matching = false;
					break;
				}
				if($i){
					if($arr_jpn[$i-1] != "")
						$arr_data[] = __get_values($event, $arr_jpn[$i-1], $arr_jpn[$i]);
					else
						$arr_data[] = __get_until_values($event, $arr_jpn[$i]);
				}
			}

			if($check_matching){
				if(count($arr_pos) == 3) {
					$arr_data[0] = 1988 + $arr_data[0];					
				}
				$str_matching = "";
				for($i=0; $i<count($arr_eng); $i++){
					$str_matching .= $arr_eng[$i];
					if(count($arr_pos) > $i)
						if(count($arr_data) > $arr_pos[$i]) $str_matching .= $arr_data[$arr_pos[$i]];
				}
				if(count($arr_pos) == 3) 
					$str_matching = date("F jS, Y", strtotime($str_matching));
				return $str_matching;
			}
		}

		return __kata2romaji__($event);
	}

	function __get_number($__str) {
		$ret = "";
		$check_val = substr($__str, -1);
		$__str = substr($__str, 0, strlen($__str) - 1);
		while(is_numeric($check_val) && strlen($__str) > 2){
			$ret = $check_val.$ret;
			$check_val = substr($__str, -1);
			$__str = substr($__str, 0, strlen($__str) - 1);
		}
		return $ret;
	}

	function process_meeting($meeting_name) {
		$arr_meetings = array( '札幌' => "Sapporo", '函館' => "Hakodate", '福島' => "Fukushima", '新潟' => "Niigata", '東京' => "Tokyo", '中山' => "Nakayama", '中京' => "Chukyo", '京都' => "Kyoto", '阪神' => "Hanshin", '小倉' => "Kokura" );
		$arr_split = explode("回", $meeting_name);
		$track_number = $arr_split[0];
		if($track_number == 1) $track_number .= "st";
		else if($track_number == 2) $track_number .= "nd";
		else if($track_number == 3) $track_number .= "rd";
		else $track_number .= "th";
		$arr_split = str_replace("日", "", $arr_split[1]);
		$track_day = __get_number($arr_split);
		if($track_day == 1) $track_day .= "st";
		else if($track_day == 2) $track_day .= "nd";
		else if($track_day == 3) $track_day .= "rd";
		else $track_day .= "th";
		$track_eng = $arr_meetings[substr($arr_split, 0, strlen($arr_split)-strlen($track_day)+2)];
		return $track_eng." ".$track_number." Meeting ".$track_day." Day";
	}

	function process_meeting_for_printing($meeting_name) {
		$arr_meetings = array( '札幌' => "Sapporo", '函館' => "Hakodate", '福島' => "Fukushima", '新潟' => "Niigata", '東京' => "Tokyo", '中山' => "Nakayama", '中京' => "Chukyo", '京都' => "Kyoto", '阪神' => "Hanshin", '小倉' => "Kokura" );
		$arr_split = explode("回", $meeting_name);
		$track_number = $arr_split[0];
		if($track_number == 1) $track_number .= "st";
		else if($track_number == 2) $track_number .= "nd";
		else if($track_number == 3) $track_number .= "rd";
		else $track_number .= "th";
		$arr_split = str_replace("日", "", $arr_split[1]);
		$track_day = __get_number($arr_split);
		if($track_day == 1) $track_day .= "st";
		else if($track_day == 2) $track_day .= "nd";
		else if($track_day == 3) $track_day .= "rd";
		else $track_day .= "th";
		$track_eng = $arr_meetings[substr($arr_split, 0, strlen($arr_split)-strlen($track_day)+2)];
		$arrRet = array();
		array_push($arrRet, $track_eng);
		array_push($arrRet, $track_number);
		array_push($arrRet, $track_day);
		return $arrRet;
		// return $track_eng." - ".$track_day." Day - " . $track_number." Meeting ";
	}

	function calculate_frequency($sentences)
	{
		global $db;

		$strsql = "update tbl_sentences set freq=1 where freq=0";
	    $db->__exec__($strsql);

		$strsql = "select * from tbl_sentences order by Id";
    	$result = $db->select($strsql);
    	if($result) {
    		foreach ($result as $pattern) {
    			if(isset($pattern['Id'])) {
	    			$Id = $pattern['Id'];
	    			$total_matching = calculate($sentences, $pattern);
	    			if($total_matching > 0) {
	    				$freq = $total_matching;
	    				$strsql = "update tbl_sentences set freq = '".$freq."' where Id='".$Id."'";
	    				$db->__exec__($strsql);
	    			}
	    		}
    		}
    	}
	}

	function getSentencesFromId($Id){
		global $db;
		$strsql = "select * from tbl_sentences where Id = " . $Id;
		$result = $db->select($strsql);
		return $result;
	}

	function insertNewTrainerName($chn, $jpn, $eng){
		global $db;
		$retired = false;
		if( strpos( $eng, "[Retired]") !== false){
			$eng = str_replace("[Retired]", "", $eng);
			$retired = true;
		}
		$chn = trim($chn);
		$jpn = trim($jpn);
		$eng = trim($eng);
		$chn = str_replace(" ", "", $chn);
		$strsql = "select * from tbl_names where chn = '" . $chn . "'";
		$result = $db->select($strsql);
		if( !$result ){
			$strsql = "insert into tbl_names(chn, jpn, eng, retired) values( ?, ?, ?, ?)";
			$stmt = $db->prepare($strsql);
			$stmt->execute([$chn, $jpn, $eng, $retired]);
			return 1;
		} else{
			if( ($row['eng'] != $eng) || ($row['retired'] != $retired)){
				$strsql = "update tbl_names set chn = ? , eng = ?, retired = ? where Id = '" . $row['Id'] . "'";
				$stmt->$db->prepare($strsql);
				$stmt->execute([$chn, $eng, $retired]);
				return -1;
			}
			return 0;
		}
	}
	function insertNewJockeyName($chn, $jpn, $eng){
		global $db;
		$chn = trim($chn);
		$jpn = trim($jpn);
		$eng = trim($eng);
		$retired = false;
		if( strpos( $eng, "[Retired] ") !== false){
			$eng = str_replace("[Retired] ", "", $eng);
			$retired = true;
		}
		$chn = str_replace(" ", "", $chn);
		$strsql = "select * from tbl_names where chn = '" . $chn . "'";
		$result = $db->select($strsql);
		if( !$result ){
			$strsql = "insert into tbl_names(chn, jpn, eng, retired) values( ?, ?, ?, ?)";
			$stmt = $db->prepare($strsql);
			$stmt->execute([$chn, $jpn, $eng, $retired]);
			return 1;
		} else{
			$row = $result[0];
			if( $row['eng'] != $eng || $row['retired'] != $retired){
				$strsql = "update tbl_names set chn = ? , eng = ?, retired = ? where Id = '" . $row['Id'] . "'";
				$stmt->$db->prepare($strsql);
				$stmt->execute([$chn, $eng, $retired]);
				return -1;
			}
			return 0;
		}
	}

	function getTrainerJpnName( $chn){
		$chn = str_replace(" ", "", $chn);
		global $db;
		$strsql = "select * from tbl_names where chn = '" . $chn . "'";
		$result = $db->select($strsql);
		if( !$result){
			return "";
		}
		$row = $result[0];
		return $row['jpn'];
	}

	function getJockeyJpnName( $chn){
		$chn = str_replace(" ", "", $chn);
		global $db;
		$strsql = "select * from tbl_names where chn = '" . $chn . "'";
		$result = $db->select($strsql);
		if( !$result){
			return "";
		}
		$row = $result[0];
		return $row['jpn'];
	}

	function getTrainerEngName( $chn){
		$chn = str_replace(" ", "", $chn);
		global $db;
		$strsql = "select * from tbl_names where chn = '" . $chn . "'";
		$result = $db->select($strsql);
		if( !$result){
			return "";
		}
		$row = $result[0];
		return $row['eng'];
	}

	function getJockeyEngName( $chn){
		$chn = str_replace(" ", "", $chn);
		global $db;
		$strsql = "select * from tbl_names where chn = '" . $chn . "'";
		$result = $db->select($strsql);
		if( !$result){
			return "";
		}
		$row = $result[0];
		return $row['eng'];
	}

	function insertNewHorseName($href, $jpn, $eng){
		global $db;
				// $sql = "INSERT INTO tbl_sentences  (jpn, eng) VALUES ( ?, ? )";
				// $stmt= $db->prepare($sql);
				// $stmt->execute([trim($event), $trans_data]);

		$strsql = "select * from horse where jpn = '" . $jpn . "'";
		$result = $db->select($strsql);
		if( !$result ){
			$strsql = "insert into horse(href, jpn, eng) values( ?, ?, ?)";
			$stmt = $db->prepare($strsql);
			$stmt->execute([$href, $jpn, $eng]);
			// $db->__exec__($strsql);
			return 1;
		} else{
			$row = $result[0];
			if( $row['eng'] != $eng){
				$strsql = "update horse set href = ? , eng = ? where Id = '" . $row['Id'] . "'";
				$stmt->$db->prepare($strsql);
				$stmt->execute([$href, $eng]);
				// $db->__exec__($strsql);
				return -1;
			}
			return 0;
		}
	}
	function getHorseEngName( $jpn){
		global $db;
		$strsql = "select * from horse where jpn = '" . $jpn . "'";
		$result = $db->select($strsql);
		if( !$result){
			return "";
		}
		$row = $result[0];
		return $row['eng'];
	}

	function isContains($sentences, $pattern){
		return calculate($sentences, $pattern);
	}

	function check_pattern_matching($sentence, $arr_jpn)
	{
		for($i=0; $i<count($arr_jpn); $i++){
			if(($arr_jpn[$i] != "") && (strpos($sentence, $arr_jpn[$i]) === false)){
				return false;
			}
		}
		return true;
	}
	function processJpnNames($sentence){
		$strBuf = $sentence;
		$needle = "騎手";
		$startPos = 0;
		while( ($startPos = strpos($strBuf, $needle, $startPos)) !== false){
			$endPos = strpos($strBuf, "は", $startPos);
			if( $endPos !== false){
				$strJockeyChnName = substr($strBuf, $startPos + strlen($needle), $endPos - ($startPos + strlen($needle)));
				$strJockeyJpnName = getJockeyJpnName($strJockeyChnName);

				if( $strJockeyJpnName != ""){
					$strBuf = str_replace( $strJockeyChnName, $strJockeyJpnName, $strBuf);
				}
				$startPos = $endPos + strlen("は");
			}
			break;
		}
		$needle = "調教師";
		$startPos = 0;
		while( ($startPos = strpos($strBuf, $needle, $startPos)) !== false){
			$endPos = strpos($strBuf, "は", $startPos);
			if( $endPos !== false){
				$strTrainerChnName = substr($strBuf, $startPos + strlen($needle), $endPos - ($startPos + strlen($needle)));
				$strTrainerJpnName = getTrainerJpnName($strTrainerChnName);

				if( $strTrainerJpnName != ""){
					$strBuf = str_replace( $strTrainerChnName, $strTrainerJpnName, $strBuf);
				}
				$startPos = $endPos + strlen("は");
			}
			break;
		}
		return $strBuf;
	}
	function post_pattern_process($sentence, $patterns, $details) {
		$pos = 0;
		$check_count = 0;
		$arr_pos = [];
		for ($i=0; $i<count($patterns); $i++) {
			$pattern_obj = $patterns[$i];

			if(!isset($pattern_obj["eng"])) continue;
			$pattern = $pattern_obj["eng"];
			$pattern = str_replace('_+_', '', $pattern);
			$pattern = str_replace('+_+', '', $pattern);
			$pattern = str_replace('{}', '', $pattern);
			$words = explode(" ", $pattern);
			$first_word = "";
			$last_word = "";
			$pos_1 = false;
			$pos_2 = false;
			foreach ($words as $word) {
				if(trim($word)) {
					$pos_2 = strpos($sentence, trim($word), $pos);
					if($pos_1 === false) $pos_1 = $pos_2;
					$last_word = trim($word);
					$pos_2 += strlen($last_word);
					$pos = $pos_2;
				}
			}

			if($pos_1 === false) continue;
			if($pos_2 === false) continue;
			
			$pos_info = new \stdClass;
			$pos_info->start = $pos_1;
			$pos_info->end = $pos_2;
			$pos_info->id = $pattern_obj["Id"];
			$pos_info->word_status = $pattern_obj["word_status"];
			$pos_info->detail = $details[$i];
			$arr_pos[] = $pos_info;
			$check_count++;
		}
		if($check_count == 0) return $sentence;

		for($i = count($arr_pos)-1; $i>=0; $i--) {
			$pos_obj = $arr_pos[$i];
			$sentence_01 = substr($sentence, 0, $pos_obj->start);
			$sentence_02 = substr($sentence, $pos_obj->start, $pos_obj->end - $pos_obj->start);
			$sentence_03 = substr($sentence, $pos_obj->end);
			$id = $pos_obj->id;
			$pattern_source = $pos_obj->detail;
			$sentence = $sentence_01.($pos_obj->word_status == '0' ? ' <img src="./assets/imgs/google.png" class="zeroStatus">' : '').'<span class="sentence_pattern" pattern_id="'.$id.'" pattern_source="'.$pattern_source.'">'.$sentence_02.'</span>'.$sentence_03;
		}
		return $sentence;
	}

	function search_pattern_id($sentence, $patterns, $sentence2) {
		foreach ($patterns as $pattern) {
			$arr_jpn = explode("{}", $pattern["jpn"]);
			if(check_pattern_matching($sentence, $arr_jpn)) {
				return $pattern;
			}
		}
		$patterns = get_sentence_patterns(false);
		foreach ($patterns as $pattern) {
			$arr_jpn = explode("{}", $pattern["jpn"]);
			if(check_pattern_matching($sentence, $arr_jpn)) {
				return $pattern;
			}
		}
		return search_pattern_id_from_eng($sentence2, $patterns);
	}

	function search_pattern_id_from_eng($sentence, $patterns) {
		foreach ($patterns as $pattern) {
			$arr_jpn = explode("{}", $pattern["eng"]);
			if(check_pattern_matching($sentence, $arr_jpn)) {
				return $pattern;
			}
		}
		$patterns = get_sentence_patterns(false);
		foreach ($patterns as $pattern_obj) {
			$pattern = $pattern_obj["eng"];
			$pattern = str_replace('_+_', '', $pattern);
			$pattern = str_replace('+_+', '', $pattern);
			$arr_jpn = explode("{}", $pattern);
			if(check_pattern_matching($sentence, $arr_jpn)) {
				return $pattern;
			}
		}
		return false;
	}

	function calculate($sentences, $pattern) {
		$arr_jpn = explode("{}", $pattern["jpn"]);
		$total_matching = 0;
		foreach ($sentences as $sentence) {
			if(check_pattern_matching($sentence, $arr_jpn)) {
				$total_matching++;
			}
		}
		return $total_matching;
	}

	function process_sentence_matching($event) {
		global $sentences, $db;

		foreach ($sentences as $sentence) {
			if($sentence["eng"] == "") continue;
			$arr_jpn = explode("{}", $sentence["jpn"]);
			$arr_eng = explode("{}", $sentence["eng"]);
			$arr_pos = explode(",", $sentence["pos"]);
			if((count($arr_pos) == 1) && ($arr_pos[0] == "")) $arr_pos[0] = 0;
			for($i = count($arr_pos); $i<10; $i++) {
				$arr_pos[] = $i;
			}
			$arr_data = [];
			$check_matching = true;

			for($i=0; $i<count($arr_jpn); $i++){
				if(($arr_jpn[$i] != "") && (strpos($event, $arr_jpn[$i]) === false)){
					$check_matching = false;
					break;
				}
				if($i){
					if($arr_jpn[$i-1] != "")
						$arr_data[] = __get_values($event, $arr_jpn[$i-1], $arr_jpn[$i]);
					else
						$arr_data[] = __get_until_values($event, $arr_jpn[$i]);
				}
			}

			if($check_matching){
				$str_matching = "";
				for($i=0; $i<count($arr_eng); $i++){
					$str_matching .= $arr_eng[$i];
					if(count($arr_pos) > $i)
						if(count($arr_data) > $arr_pos[$i]) $str_matching .= process_date_time_value($arr_data[$arr_pos[$i]]);
				}
				// _+_大江原圭+_+
				$str_jockey = __get_values($str_matching, '_+_', '+_+');
				if($str_jockey) {
					$str_matching = str_replace('_+_'.$str_jockey.'+_+', __kata2romaji__($str_jockey), $str_matching);
				}
				return str_replace("，", ",", $str_matching);
			}
		}

		if(trim($event)){
			if(!check_sentence_exist($sentences, trim($event))) {
				$trans_data = " ".__get_translate(trim($event));
				
				sendSMSByCatagory('NEW_SENTENCE', trim($event));

				$sql = "INSERT INTO tbl_sentences  (jpn, eng) VALUES ( ?, ? )";
				$stmt= $db->prepare($sql);
				$stmt->execute([trim($event), $trans_data]);

		        $new_pattern = [];
		        $new_pattern["jpn"] = trim($event);
		        $new_pattern["eng"] = $trans_data;
		        $new_pattern["pos"] = "";
		        $sentences[] = $new_pattern;
		        return $trans_data;
		    }
	    }

		return $event;
	}

	function check_sentence_exist($patterns, $jpn) {
		foreach ($patterns as $pattern) {
			if($pattern["jpn"] == $jpn) return true;
		}
		return false;
	}

	function check_word_exist($patterns, $jpn) {
		foreach ($patterns as $pattern) {
			if($pattern["jpn"] == $jpn) return true;
		}
		return false;
	}

	function process_event($event) {
		for($i=0; $i<count($event); $i++){
			// if( $event[$i] == 'チクリ号は，競走中に疾病〔右後肢跛行〕を発症したため最後の直線コースで競走中止。'){
			// 	continue;
			// }
			$event[$i] = process_individual($event[$i]);
		}
		return $event;
	}

	function process_individual($event) {
		$num_arrs = ["０" => "0", "１" => "1", "２" => "2", "３" => "3", "４" => "4", "５" => "5", "６" => "6", "７" => "7", "８" => "8", "９" => "9"];
		foreach ($num_arrs as $key => $value) {
			$event = str_replace($key, $value, $event);
		}

		$events = explode("。", $event);
		for($i=0; $i<count($events); $i++)
			$events[$i] = process_individual_sentence($events[$i]);
		return implode(". ", $events);
	}

	function __kata2romaji__($str) {
		global $patterns, $db, $dic_data;

		$num_arrs = ["０" => "0", "１" => "1", "２" => "2", "３" => "3", "４" => "4", "５" => "5", "６" => "6", "７" => "7", "８" => "8", "９" => "9"];
		foreach ($num_arrs as $key => $value) {
			$str = str_replace($key, $value, $str);
		}
		$str = str_replace("，", ",", $str);
		$check_str = str_replace(",", "", $str);
		if(is_numeric($check_str)) return $str;

		if($dic_data){
			if(isset($dic_data->$str)){
				$buff = $dic_data->$str;
				if( strtoupper($buff) == $buff){
					$arrBuf = explode(" ", $buff);
					for( $idx = 0; $idx < count($arrBuf); $idx++){
						$arrBuf[$idx] = ucfirst(strtolower($arrBuf[$idx]));
					}
					return implode(" ", $arrBuf);
				}
				return $dic_data->$str;
			}
		}
		foreach ($patterns as $pattern) {
			if($pattern["eng"] == "") continue;
			if($pattern["jpn"] == $str)
				return $pattern["eng"];
		}

		if(!check_word_exist($patterns, $str)) {
			$trans_data = " ".__get_translate($str);

			$sql = "INSERT INTO tbl_words (jpn, eng) VALUES ( ?, ? )";
			$stmt= $db->prepare($sql);
			$stmt->execute([$str, $trans_data]);

	        $new_pattern = [];
	        $new_pattern["jpn"] = $str;
	        $new_pattern["eng"] = $trans_data;
	        $patterns[] = $new_pattern;

	        return $trans_data;
	    }

		return $str;
	}

	function process_individual_sentence($event, $pattern_only=false) {
		$num_arrs = ["０" => "0", "１" => "1", "２" => "2", "３" => "3", "４" => "4", "５" => "5", "６" => "6", "７" => "7", "８" => "8", "９" => "9"];
		foreach ($num_arrs as $key => $value) {
			$event = str_replace($key, $value, $event);
		}
		return process_horse_info( $event, $pattern_only );
	}
$prevHorseStr = '';
	function process_horse_info($event, $pattern_only=false) {
		global $prevHorseStr;
		$horse_part = __get_until_values($event, "は，");
		if($horse_part) 
			$action_part = __get_values($event."ののの", "は，", "ののの");
		else
			$action_part = $event;
		if( $event == 'チクリ号は，競走中に疾病〔右後肢跛行〕を発症したため最後の直線コースで競走中止。'){

		}
		if($horse_part) {
			$horse_arr = explode("・", $horse_part);
			foreach ($horse_arr as $key => $horse_obj) {
				$horse_obj_detail = explode("号", $horse_obj);
				if(count($horse_obj_detail) != 2) 
					array_splice($horse_arr, $key, 1);
				else {
					$horse_name = $horse_obj_detail[0];
					$horse_eng_name = "";
					$horse_dic_name = getHorseEngName($horse_name);
					if( $horse_dic_name == ""){
						$horse_eng_name = __kata2romaji__($horse_name);
					} else{
						$horse_eng_name = $horse_dic_name;
					}
					if(strpos($horse_obj_detail[1], "の騎手") !== false){
						$jockey_name = __get_values($horse_obj_detail[1]."ののの", "の騎手", "ののの");
						$jockey_name = getJockeyEngName($jockey_name);
						if( $jockey_name == ""){
							$jockey_name = __kata2romaji__($jockey_name);
						}
						$horse_arr[$key] = $horse_eng_name."'s jockey ". $jockey_name;
					} else if(strpos($horse_obj_detail[1], "の調教師") !== false){
						$horse_arr[$key] = $horse_eng_name."'s trainer ".__kata2romaji__(__get_values($horse_obj_detail[1]."ののの", "の調教師", "ののの"));
					} else {
						$horse_arr[$key] = $horse_eng_name;
					}
				}
				
			}
		}

		$affected_part = false;
		
		$affected_part = __get_values($event, "（被害馬：", "）");
		if($affected_part){
			$action_part = __get_until_values($event, "（被害馬：");
		}		
		// return $action_part;
		$action_part = process_sentence_matching($action_part);
		if($pattern_only) return $action_part;
		$str_data = "";
		if($horse_part) $str_data .= implode(", ", $horse_arr)." ";
		$horse_str = $str_data;
		if( $horse_str != ""){
			$prevHorseStr = $horse_str;
			$arrBuf = explode(" ", $horse_str);
			for( $idx = 0; $idx < count($arrBuf); $idx++){
				if( $arrBuf[$idx] == "jockey" || $arrBuf[$idx] == "trainer"){
					continue;
				}
				$arrBuf[$idx] = ucfirst(strtolower($arrBuf[$idx]));
			}
			$horse_str = implode(" ", $arrBuf);
		}
		$str_data = $action_part;
		if($affected_part){
			$str_data .= "(obstructing the course of the number ".implode(", number ", explode("・", str_replace("番", "", $affected_part)))." horse).";
			$str_data = str_replace(". (obstructing", "(obstructing", $str_data);
		}
		if( strpos($str_data, "was ") === 0){
			if( $horse_str == "")
				return $prevHorseStr . " " . $str_data;
			return $horse_str. $str_data;
		}
		return $horse_str . $str_data;
	}

?>