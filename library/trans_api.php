<?php
	$db_name = "";
	if( isset($_POST['db_name'])) $db_name = $_POST['db_name'];
	if( isset($_GET['db_name'])) $db_name = $_GET['db_name'];

	require_once 'trans_lib.php';

	$case = 0;
	if(isset($_POST['case'])) $case = $_POST['case'];
	if(isset($_GET['case'])) $case = $_GET['case'];

	function __get_horse_from_db() {
		$horses = [];

		$data = @file_get_contents("../logs/backup/horse_db.json");
		if($data)
			$horses = json_decode($data, true);
		return $horses;
	}

	function __get_horse_ids_from_db() {
		$horses = __get_horse_from_db();
		$ret = [];
		foreach ($horses as $key => $value) {
			$ret[] = $key;
		}
		return $ret;
	}

	function __process_horse_db($data) {
		$horses = __get_horse_from_db();
		$new_horses = json_decode($data);
		if($data) {
			foreach ($new_horses as $horse_info) {
				$href = $horse_info->href;
				$jpn = $horse_info->jpn;
				$eng = $horse_info->eng;
				$horse_id = str_replace("/", "", $href);
				if(!isset($horses[$horse_id])) {
					$new_horse_info = new \stdClass;
					$new_horse_info->id = $horse_id;
					$new_horse_info->jpn = $jpn;
					$new_horse_info->eng = $eng;
					$horses[$horse_id] = $new_horse_info;
				}
			}
			@file_put_contents("../logs/backup/horse_db.json", json_encode($horses));
			echo "Total Horse Count: ".count($horses)."<br>Completed!";
		}
	}

	if($case == 1){
		$page_num = 0;
		if(isset($_POST['page_num'])) $page_num = $_POST['page_num'];
		if(isset($_GET['page_num'])) $page_num = $_GET['page_num'];

		$select_case = -1;
		if(isset($_POST['select_case'])) $select_case = $_POST['select_case'];
		if(isset($_GET['select_case'])) $select_case = $_GET['select_case'];
		if($select_case == "google") $select_case = 0;
		else if($select_case == "hand") $select_case = 1;
		else $select_case = -1;
		$search_key = $_POST['search_key'];
		$sort_field = $_POST['sort_field'];

		$patterns = get_word_patterns_page($page_num, $select_case, $search_key, $sort_field);
		if($patterns){
			foreach ($patterns as $pattern) {
	?>
		<tr class="data_<?=$pattern["Id"]?><?php if($pattern["word_status"] == 1) echo " hand_data"; else echo " google_data"?>" value="<?=$pattern["Id"]?>" aria="data_<?=$pattern["Id"]?>"><td style="text-align: center;"><?=$pattern["Id"]?></td><td><input type=text class="jpn_data" value="<?=$pattern["jpn"]?>" readonly></td><td><input type=text class="txt_data" value="<?=$pattern["eng"]?>"></td><td style="text-align: center;"><input type=button value="Edit" class="btn_edit"> <input type=button value="Save" class="btn_save"> <input type=button value="Remove" class="btn_remove"></td></tr>
	<?php
			}
		}
	} else if($case == 11){
		$page_num = 0;
		if(isset($_POST['page_num'])) $page_num = $_POST['page_num'];
		if(isset($_GET['page_num'])) $page_num = $_GET['page_num'];

		$select_case = -1;
		if(isset($_POST['select_case'])) $select_case = $_POST['select_case'];
		if(isset($_GET['select_case'])) $select_case = $_GET['select_case'];
		if($select_case == "google") $select_case = 0;
		else if($select_case == "hand") $select_case = 1;
		else $select_case = -1;
		$search_key = $_POST['search_key'];
		$sort_field = $_POST['sort_field'];

		$result = get_word_patterns_page_count($select_case, $search_key);
		
		$total = 0;
		if($result) $total = ($result - 1 - ($result - 1) % 20) / 20 + 1;
		echo 'Page <input id="goto_page_word" type=text value="'.($page_num+1).'" size=3 style="text-align: center; height: 26px; line-height:26px;"> / '.$total.' Pages <span class="gap"></span>';
		
		for($i=0; $i<$total; $i++) {
			if(abs($page_num - $i) > 2 && ($i > 2) && ($i + 3 < $total)) continue;
			if($i == $page_num) echo '<span class="cur_page">'.($i+1).'</span>';
			else echo '<span class="page_num" onclick = "load_words('.$i.')">'.($i+1).'</span>';
		}
		
	} else if($case == 2){
		$page_num = 0;
		if(isset($_POST['page_num'])) $page_num = $_POST['page_num'];
		if(isset($_GET['page_num'])) $page_num = $_GET['page_num'];

		$select_case = -1;
		if(isset($_POST['select_case'])) $select_case = $_POST['select_case'];
		if(isset($_GET['select_case'])) $select_case = $_GET['select_case'];
		if($select_case == "google") $select_case = 0;
		else if($select_case == "hand") $select_case = 1;
		else $select_case = -1;
		$search_key = $_POST['search_key'];
		$sort_field = $_POST['sort_field'];

		$sentences = get_sentence_patterns_page($page_num, $select_case, $search_key, $sort_field);
		if($sentences) {
			foreach ($sentences as $pattern) {
	?>
		<tr class="data2_<?=$pattern["Id"]?><?php if($pattern["word_status"] == 1) echo " hand_data"; else echo " google_data"?>" value="<?=$pattern["Id"]?>" aria="data2_<?=$pattern["Id"]?>"><td rowspan=2 style="text-align: center;"><?=$pattern["Id"]?></td><td><input type=text class="jpn_data" value="<?=$pattern["jpn"]?>"></td><td style="width:100px;"><input type=text class="pos_data" value="<?=$pattern["pos"]?>" style="width:90px;"></td><td rowspan=2 style="text-align: center;"><?=$pattern["freq"]?></td><td style="text-align: center;" rowspan="2"><input type=button value="Report Link" class="btn_link"></td><td style="text-align: center;" rowspan="2"><input type=button value="Edit" class="btn_edit"> <input type=button value="Save" class="btn_save"> <input type=button value="Remove" class="btn_remove"></td></tr>
		<tr class="data2_<?=$pattern["Id"]?><?php if($pattern["word_status"] == 1) echo " hand_data"; else echo " google_data"?>" value="<?=$pattern["Id"]?>" aria="data2_<?=$pattern["Id"]?>"><td colspan="2"><input type=text class="txt_data" value="<?=str_replace('"', "&quot;", $pattern["eng"])?>"></td></tr>
	<?php
			}
		}
	} else if($case == 21){
		$page_num = 0;
		if(isset($_POST['page_num'])) $page_num = $_POST['page_num'];
		if(isset($_GET['page_num'])) $page_num = $_GET['page_num'];

		$select_case = -1;
		if(isset($_POST['select_case'])) $select_case = $_POST['select_case'];
		if(isset($_GET['select_case'])) $select_case = $_GET['select_case'];
		if($select_case == "google") $select_case = 0;
		else if($select_case == "hand") $select_case = 1;
		else $select_case = -1;
		$search_key = $_POST['search_key'];
		$sort_field = $_POST['sort_field'];

		$result = get_sentence_patterns_page_count($select_case, $search_key);
		$total = 0;
		if($result) $total = ($result - 1 - ($result - 1) % 10) / 10 + 1;
		echo 'Page <input type=text id="goto_page_sentence" value="'.($page_num+1).'" size=3 style="text-align: center; height: 26px; line-height:26px;"> / '.$total.' Pages <span class="gap"></span>';
		
		for($i=0; $i<$total; $i++) {
			if(abs($page_num - $i) > 2 && ($i > 2) && ($i + 3 < $total)) continue;
			if($i == $page_num){
 				echo '<span class="cur_page">'.($i+1).'</span>'; 				
			}
			else echo '<span class="page_num" onclick = "load_sentences('.$i.')">'.($i+1).'</span>';
		}
		
	} else if($case == 3){
		$data = "";
		if(isset($_POST['data'])) $data = $_POST['data'];
		$arr_data = explode("_", $data);
		if(count($arr_data) == 2){
			if($arr_data[0] == "data"){
				$Id = $arr_data[1];
				delete_word_pattern($Id);
			} else if($arr_data[0] == "data2"){
				$Id = $arr_data[1];
				delete_sentence_pattern($Id);
			}
		}
	} else if($case == 4){
		header('Content-Type: text/html; charset=utf-8');

		$data = "";
		if(isset($_POST['data'])) $data = $_POST['data'];
		$jpn_data = "";
		if(isset($_POST['jpn_data'])) $jpn_data = $_POST['jpn_data'];
		$eng_data = "";
		if(isset($_POST['eng_data'])) $eng_data = $_POST['eng_data'];
		$pos_data = "";
		if(isset($_POST['pos_data'])) $pos_data = $_POST['pos_data'];
		$check_hand = 0;
		if(isset($_POST['check_hand'])) $check_hand = $_POST['check_hand'];
		$arr_data = explode("_", $data);
		if(count($arr_data) == 2){
			if($arr_data[0] == "data"){
				$Id = $arr_data[1];
				register_word_pattern($Id, $jpn_data, $eng_data, $check_hand);
				if($check_hand) echo "data_".$Id;
			} else if($arr_data[0] == "data2"){
				$Id = $arr_data[1];
				register_sentence_pattern($Id, $jpn_data, $eng_data, $pos_data, $check_hand);
				if($check_hand) echo "data2_".$Id;
			}
		}
	} else if($case == 5){
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		$data = "";
		if(isset($_POST['data'])) $data = $_POST['data'];
		if($data){			
			$datas = json_decode($data);
			foreach ($datas as $horse) {
				register_horse_info($horse->jpn, $horse->eng);
			}
		}
	} else if($case == 6){
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		$date = "";
		$data = "";
		if(isset($_POST['date'])) $date = $_POST['date'];
		if(isset($_POST['data'])) $data = $_POST['data'];
		if($data){			
			$date_val = date("Ymd", strtotime($date));
			file_put_contents("../logs/backup/competitors_".$date_val.".json", $data);
		}
	} else if($case == 7){
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		$date = date("Y-m-d");
		if(isset($_GET['date'])) $date = $_GET['date'];
		$date_val = date("Ymd", strtotime($date));
		
		$data = @file_get_contents("../logs/backup/competitors_".$date_val.".json");
		$competitors = json_decode($data);
		foreach ($competitors as $race) {
			echo "<font color=mediumblue>".$race->track_name." ( ID: ".$race->meeting_id." ) R ".$race->event_number."</font><br>";
			echo "<table>";
			foreach ($race->competitors as $race_info) {
				echo "<tr><td>".$race_info->number."</td><td>".$race_info->name."</td><td>".$race_info->jockey."</td><td>".$race_info->trainer."</td></tr>";
			}
			echo "<table>";
		}
	} else if($case == 8){
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		$date = "";
		$data = "";
		if(isset($_POST['date'])) $date = $_POST['date'];
		if(isset($_POST['data'])) $data = $_POST['data'];
		if($data){			
			$date_val = date("Ymd", strtotime($date));
			file_put_contents("../logs/backup/competitors_".$date_val."_jbis.json", $data);
		}
	} else if($case == 80){
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		$date = "";
		$data = "";
		if(isset($_POST['date'])) $date = $_POST['date'];
		if(isset($_POST['data'])) $data = $_POST['data'];
		if($data){
			file_put_contents("../logs/backup/horse_".$date."_jbis.json", $data);
			__process_horse_db($data);
		}
	} else if($case == 81){
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		if(isset($_POST['date'])) $date = $_POST['date'];
		if(isset($_GET['date'])) $date = $_GET['date'];

		$data = @file_get_contents("../logs/backup/horse_".$date."_jbis.json");
		__process_horse_db($data);
	} else if($case == 82){
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		echo json_encode(__get_horse_ids_from_db());
	} else if($case == 9){
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		$date = date("Y-m-d");
		if(isset($_GET['date'])) $date = $_GET['date'];
		$date_val = date("Ymd", strtotime($date));
		
		$data = @file_get_contents("../logs/backup/competitors_".$date_val."_jbis.json");
		$competitors = json_decode($data);
		echo "<table>";
		foreach ($competitors as $race_info) {
			$race_href = intval(substr($race_info->race, -7));
			if(strpos($race_info->jpn, "(") !== false) $race_info->jpn = substr($race_info->jpn, 0, strpos($race_info->jpn, "("));
			if(strpos($race_info->eng, "(") !== false) $race_info->eng = substr($race_info->eng, 0, strpos($race_info->eng, "("));
			echo "<tr><td>".$race_href."</td><td>".$race_info->number."</td><td>".$race_info->jpn."</td><td>".$race_info->eng."</td><td>".$race_info->jockey."</td><td>".$race_info->trainer."</td></tr>";
		}
		echo "<table>";
	} else if($case == 10){
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

		$date = date("Y-m-d");
		if(isset($_GET['date'])) $date = $_GET['date'];
		$date_val = date("Ymd", strtotime($date));

		$data = @file_get_contents("../logs/backup/competitors_".$date_val."_jbis.json");
		$jbis_competitors = json_decode($data);
		$compare_datas = [];
		foreach ($jbis_competitors as $race_info) {
			$race_href = intval(substr($race_info->race, -7));
			if(strpos($race_info->jpn, "(") !== false) $race_info->jpn = substr($race_info->jpn, 0, strpos($race_info->jpn, "("));
			if(strpos($race_info->eng, "(") !== false) $race_info->eng = substr($race_info->eng, 0, strpos($race_info->eng, "("));
			$arr_data = [];
			$arr_data["event_number"] = $race_href;
			$arr_data["number"] = $race_info->number;
			$arr_data["eng"] = $race_info->eng;
			$arr_data["jpn"] = $race_info->jpn;
			$arr_data["jockey"] = $race_info->jockey;
			$arr_data["trainer"] = $race_info->trainer;
			if(!isset($compare_datas[strtoupper(str_replace(" ", "", $race_info->eng))])) $compare_datas[strtoupper(str_replace(" ", "", $race_info->eng))] = [];
			$compare_datas[strtoupper(str_replace(" ", "", $race_info->eng))][$race_href] = $arr_data;
		}

		$arr_trans = [];

		$data = @file_get_contents("../logs/backup/competitors_".$date_val.".json");
		$competitors = json_decode($data);		
		echo "<table>";
		foreach ($competitors as $race) {
			echo "<tr><td colspan=7><font color=mediumblue>".$race->track_name." ( ID: ".$race->meeting_id." ) R ".$race->event_number."</font></td></tr>";
			foreach ($race->competitors as $race_info) {
				$eng_name = $race_info->name;
				$jpn_name = "";
				$jpn_jockey = "";
				$jpn_trainer = "";
				if(isset($compare_datas[strtoupper(str_replace(" ", "", $eng_name))][$race->event_number])) {
					$sel_data = $compare_datas[strtoupper(str_replace(" ", "", $eng_name))][$race->event_number];
					$jpn_name = $sel_data["jpn"];
					$jpn_jockey = $sel_data["jockey"];
					$jpn_trainer = $sel_data["trainer"];
					if(!isset($arr_trans[$jpn_name])) $arr_trans[$jpn_name] = $race_info->name;
					if(!isset($arr_trans[$jpn_jockey])) $arr_trans[$jpn_jockey] = $race_info->jockey;
					if(!isset($arr_trans[$jpn_trainer])) $arr_trans[$jpn_trainer] = $race_info->trainer;
				}

				echo "<tr".(($jpn_name)?"":" style='background-color: yellow;'")."><td>".$race_info->number."</td><td>".$race_info->name."</td><td>".$jpn_name."</td><td>".$race_info->jockey."</td><td>".$jpn_jockey."</td><td>".$race_info->trainer."</td><td>".$jpn_trainer."</td></tr>";
			}
		}
		echo "<table>";

		@file_put_contents("../logs/backup/competitors_".$date_val."_dic.json", json_encode($arr_trans));
	} else if($case == 1001){
		$Id = 0;
		if(isset($_POST['Id'])) $Id = $_POST['Id'];
		if(isset($_GET['Id'])) $Id = $_GET['Id'];

		$pattern = get_sentence_pattern_from_id($Id);
		echo json_encode($pattern);
	} else if($case == 1002){
		require_once "jra_00.php";
		$pattern = "";
		if(isset($_POST['pattern'])) $pattern = $_POST['pattern'];
		if(isset($_GET['pattern'])) $pattern = $_GET['pattern'];

		if($pattern){
			$pattern_obj = json_decode($pattern);
			register_sentence_pattern($pattern_obj->Id, $pattern_obj->jpn, $pattern_obj->eng, $pattern_obj->pos, 1);
			
			$patterns = get_word_patterns(false);
			$sentences = [];
			$sentences[] = json_decode($pattern, true);
			$trans_source = $pattern_obj->trans_source;
			$Id = $pattern_obj->Id;
			$trans_result = process_individual_sentence($trans_source, true);
			$ret = new \stdClass;
			$ret->Id = $Id;
			$ret->result = $trans_result;
			echo json_encode($ret);
		}
	} else if( $case == 2001){ // new word
		$src = '';
		if( isset($_POST['src'])) $src = $_POST['src'];
		if( isset($_GET['src'])) $src = $_GET['src'];
		$dst = '';
		if( isset($_POST['dst'])) $dst = $_POST['dst'];
		if( isset($_GET['dst'])) $dst = $_GET['dst'];
		insert_new_word( $src, $dst);
		echo "new words";
	} else if( $case == 2002){ // new sentence
		$src = '';
		if( isset($_POST['src'])) $src = $_POST['src'];
		if( isset($_GET['src'])) $src = $_GET['src'];
		$dst = '';
		if( isset($_POST['dst'])) $dst = $_POST['dst'];
		if( isset($_GET['dst'])) $dst = $_GET['dst'];
		$pattern = '';
		if( isset($_POST['pattern'])) $pattern = $_POST['pattern'];
		if( isset($_GET['pattern'])) $pattern = $_GET['pattern'];
		insert_new_sentence( $src, $dst, $pattern);
		echo "new sentence";
	} else if( $case == 2101){ // insert new horse name to db
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		$href = '';
		if( isset($_POST['href'])) $href = $_POST['href'];
		if( isset($_GET['href'])) $href = $_GET['href'];
		$jpn = '';
		if( isset($_POST['jpn'])) $jpn = $_POST['jpn'];
		if( isset($_GET['jpn'])) $jpn = $_GET['jpn'];
		$eng = '';
		if( isset($_POST['eng'])) $eng = $_POST['eng'];
		if( isset($_GET['eng'])) $eng = $_GET['eng'];
		echo "<a href='http://www.jbis.or.jp/" . $href . "'>" . $jpn . " - " . $eng . "</a>";
		switch(insertNewHorseName($href, $jpn, $eng)){
			case 1: echo " : successfuly inserted."; break;
			case 0: echo " : already exists."; break;
			case -1: echo " : updated."; break;
		}
	} else if( $case == 2201){ // insert new jockey name to db
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		$chn = '';
		if( isset($_POST['chn'])) $chn = $_POST['chn'];
		if( isset($_GET['chn'])) $chn = $_GET['chn'];
		$jpn = '';
		if( isset($_POST['jpn'])) $jpn = $_POST['jpn'];
		if( isset($_GET['jpn'])) $jpn = $_GET['jpn'];
		$eng = '';
		if( isset($_POST['eng'])) $eng = $_POST['eng'];
		if( isset($_GET['eng'])) $eng = $_GET['eng'];
		switch(insertNewJockeyName($chn, $jpn, $eng)){
			case 1: echo "inserted."; break;
			case 0: echo "already exists."; break;
			case -1: echo "updated."; break;
		}
	} elseif ( $case = 2202) { // insert new trainer name to db
		header("Access-Control-Allow-Origin: *");
		header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		$chn = '';
		if( isset($_POST['chn'])) $chn = $_POST['chn'];
		if( isset($_GET['chn'])) $chn = $_GET['chn'];
		$jpn = '';
		if( isset($_POST['jpn'])) $jpn = $_POST['jpn'];
		if( isset($_GET['jpn'])) $jpn = $_GET['jpn'];
		$eng = '';
		if( isset($_POST['eng'])) $eng = $_POST['eng'];
		if( isset($_GET['eng'])) $eng = $_GET['eng'];
		switch(insertNewTrainerName($chn, $jpn, $eng)){
			case 1: echo "inserted."; break;
			case 0: echo "already exists."; break;
			case -1: echo "updated."; break;
		}
	}
?>