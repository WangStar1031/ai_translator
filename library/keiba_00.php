<?php

	include('common_lib.php');

	$__root_url_00 = "http://www2.keiba.go.jp";
	$__proc_url_00 = "http://www2.keiba.go.jp/KeibaWeb/TodayRaceInfo/TodayRaceInfoTop";

	function __get_odds_track() {
		global $__root_url_00, $__proc_url_00;

		$ret = array();
		$__result = __call_safe_url($__proc_url_00);
		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_tracks = $ret_html->find('.courseInfo .course td');
	  		if(count($arr_tracks) < 3) return $ret;
	  		$info_track = $arr_tracks[2];
			$track_html = str_get_html($info_track->innertext);
			$tracks_array = $track_html->find('a.courseName');
			foreach ($tracks_array as $track_item) {
				$track_obj = new \stdClass;
				$track_obj->track_name = $track_item->innertext;
				$track_obj->race_id = __get_last_values($track_item->href, "=");
				$track_obj->track_href = $__root_url_00.$track_item->href;
				array_push($ret, $track_obj);
			}
		}
		return $ret;
	}

	function __get_track_info($__race_href) {
		global $__root_url_00;

		$race_obj = new \stdClass;
		$ret = array();
		$events = [];
		$__result = __call_safe_url($__race_href);
		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_races = $ret_html->find('.raceTable tr.data');
			foreach($arr_races as $info_race) {
				$race_html = str_get_html($info_race->innertext);
				$race_datas = $race_html->find('td');
				if(count($race_datas) < 9) continue;
				$track_obj = new \stdClass;
				$track_obj->id = trim(str_replace("R", "", $race_datas[0]->innertext));
				$track_obj->race_id = $track_obj->id;
				$race_name_html = str_get_html( $race_datas[4]->innertext );
				$track_obj->name = trim($race_name_html->find('a', 0)->innertext);
				if(substr($race_name_html->find('a', 0)->href, 0, 2) == "..")
					$track_obj->href = "http://www2.keiba.go.jp/KeibaWeb".substr($race_name_html->find('a', 0)->href,2);
				else
					$track_obj->href = $__root_url_00.$race_name_html->find('a', 0)->href;
				if(__get_values($race_datas[1]->innertext, '<span class="timechange">', '</span>'))
					$track_obj->time = trim(__get_values($race_datas[1]->innertext, '<span class="timechange">', '</span>'));
				else
					$track_obj->time = trim($race_datas[1]->innertext);
				/*if(trim($race_datas[2]->innertext) != '<span class="timechange"></span>') {
					$track_obj->time = str_get_html(trim($race_datas[2]->innertext))->innertext;
				}*/
				$track_obj->count = trim($race_datas[8]->innertext);
				if($track_obj->count) array_push($ret, $track_obj);
			}

	  		$arr_races = $ret_html->find('.changeInfo tr.data');
			foreach($arr_races as $info_race) {
				$race_html = str_get_html($info_race->innertext);
				$race_datas = $race_html->find('td');
				if(count($race_datas) != 6) continue;
				$event_obj = new \stdClass;
				$event_obj->id = $race_datas[0]->innertext;
				$event_obj->number = $race_datas[1]->innertext;
				$event_obj->horse = $race_datas[2]->innertext;
				$event_obj->content = $race_datas[3]->innertext;
				$events[] = $event_obj;
			}
		}
		$race_obj->races = $ret;
		$race_obj->events = $events;
		return $race_obj;
	}

	function __get_payout($__arr_repay, $__kind) {
		$ret = new \stdClass;
		for($i=0; $i<count($__arr_repay); $i++){
			if($__arr_repay[$i]->kind == $__kind){
				$number = $__arr_repay[$i]->number;
				$money = $__arr_repay[$i]->money;
				$number = str_replace("-", "/", $number);
				$money = str_replace("円", "", $money);
				$money = str_replace(",", "", $money);
				$__arr_number = explode("<br>", $number);
				$__arr_money = explode("<br>", $money);
				for($j=0; $j<count($__arr_number); $j++){
					if(count($__arr_money) > $j){
						$__fld_name = trim($__arr_number[$j]);
						$__fld_value = trim($__arr_money[$j]);
						if(is_numeric($__fld_value)){
							$__fld_value = round($__fld_value / 100, 2);
						} else {
							$__fld_value = 0;
						}
						$payout_obj = new \stdClass;
						$payout_obj->payout = $__fld_value;
						if($__fld_name) $ret->$__fld_name = $payout_obj;
					}
				}
			}
		}
		return $ret;
	}

	function __regen_race_id_from_href($href_val){
		$now_time = GetCurrentJapanTime();
		$str = date("Ymd", strtotime($now_time));
		$str .= sprintf("%02d", __get_last_values($href_val, "k_babaCode="));
		return $str;
	}

	function __get_schedule_change() {
		$ret = __get_race_datas();
		$ret_new = __get_race_datas_4_check();

	 	$result = [];
		for($i=0; $i<count($ret); $i++){
			if($ret[$i]->meeting_id == 0) continue;
			for($j=0; $j<count($ret[$i]->races); $j++){
				if($ret[$i]->races[$j]->time != $ret_new[$i]->races[$j]->time){
					$race_obj = new \stdClass;
					$race_obj->meeting_id = $ret[$i]->meeting_id;
					$race_obj->meeting_name = $ret[$i]->track_name;
					$race_obj->event_number = $ret[$i]->races[$j]->id;
					$race_obj->time = $ret[$i]->races[$j]->time;
					$race_obj->new_time = $ret_new[$i]->races[$j]->time;					
					$race_obj->race_id = __regen_race_id_from_href( $ret[$i]->races[$j]->href );					
					$result[] = $race_obj;
				}
			}
		}
		return $result;
	}

	function __get_today_odds_cname_4ext(){
	 	$ret = __get_race_datas();
	 	$result = [];
		for($i=0; $i<count($ret); $i++){
			if($ret[$i]->meeting_id == 0) continue;
			for($j=0; $j<count($ret[$i]->races); $j++){
				$race_obj = new \stdClass;
				$race_obj->meeting_id = $ret[$i]->meeting_id;
				$race_obj->event_number = $ret[$i]->races[$j]->id;
				$race_obj->time = $ret[$i]->races[$j]->time;
				$race_obj->race_id = __regen_race_id_from_href( $ret[$i]->races[$j]->href );
				
				$result[] = $race_obj;
			}
		}
		echo json_encode($result);
	}

	function __get_race_datas_4_check() {
		$now_time = GetCurrentJapanTime();
		$file_name = date("Ymd", strtotime($now_time))."_keiba_00.json";
		
		$arr_meetings = __get_nar_meeting_ids_00();

		$results = array();
		$tracks = __get_odds_track();
		for($i=0; $i<count($tracks); $i++){
			$track_obj = $tracks[$i];
			$track_info = __get_track_info($track_obj->track_href);
			$track_info->race_id = $track_obj->race_id;
			$track_info->track_name = $track_obj->track_name;
			$track_info->meeting_id = __get_nar_meeting_id($arr_meetings, $track_info->race_id);
			$track_info->meeting_name = __get_nar_meeting_name_00($track_info->race_id);
			$track_info->venue_info = __get_nar_venue_info($track_info->race_id);
			array_push($results, $track_info);
		}

		file_put_contents($file_name, json_encode( $results ));

		return $results;
	}

	function __get_race_datas($proc_date="") {
		$now_time = GetCurrentJapanTime();
		$file_name = date("Ymd", strtotime($now_time))."_keiba_00.json";

		if(($proc_date != "") && ($proc_date != date("Ymd", strtotime($now_time)))){
			$results = array();

			$file_name = "logs/backup/".$proc_date."_keiba_00.json";
			if(file_exists($file_name))
				return json_decode(@file_get_contents($file_name));

			return $results;
		}
		
		if(file_exists($file_name)){
			$results = json_decode(@file_get_contents($file_name));
		} else {		
			$arr_meetings = __get_nar_meeting_ids_00();

			$results = array();
			$tracks = __get_odds_track();
			for($i=0; $i<count($tracks); $i++){
				$track_obj = $tracks[$i];
				$track_info = __get_track_info($track_obj->track_href);
				$track_info->race_id = $track_obj->race_id;
				$track_info->track_name = $track_obj->track_name;
				$track_info->meeting_id = __get_nar_meeting_id($arr_meetings, $track_info->race_id);
				$track_info->meeting_name = __get_nar_meeting_name_00($track_info->race_id);
				$track_info->venue_info = __get_nar_venue_info($track_info->race_id);
				array_push($results, $track_info);
			}

			file_put_contents($file_name, json_encode( $results ));
		}

		return $results;
	}

	function __get_race_results($__race_id) {
		$ret_obj = new \stdClass;

		$race_num = substr($__race_id, 6, strlen($__race_id) - 6);
		$meeting_id = substr($__race_id, 0, 6);
		$results = __get_race_datas();

		$__url = "";
		for($ii=0; $ii<count($results); $ii++){
		  if($results[$ii]->meeting_id != $meeting_id) continue;
		  for($i=0; $i<count($results[$ii]->races); $i++){
		  	if($results[$ii]->meeting_id.$results[$ii]->races[$i]->race_id == $__race_id){
		  		$__url = $results[$ii]->races[$i]->href;
		  		break;
		  	}
		  }
		}

		$ret = array();

		if($__url == "") {
			$ret_obj->result = $ret;
			$ret_obj->repay = new \ stdClass;

			return $ret_obj;			
		}

		$__url = str_replace("DebaTable", "RaceMarkTable", $__url);
		$__result = __call_safe_url_00($__url);

		$__arr_repay = array();

		$__config_check = array( "馬連単", "三連複", "三連単", "単勝", "複勝", "ワイド", "馬連複" );
		$__config_pays = array( "馬連単" => "EXA", "三連複" => "TRO", "三連単" => "TRI", "単勝" => "WIN", "複勝" => "PLC", "ワイド" => "QNP", "馬連複" => "QNL" );

		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_races = $ret_html->find('td.containerMain table.cover table.bs td.dbtbl table.bs tr');

			foreach($arr_races as $info_race) {
				$race_html = str_get_html($info_race->innertext);
				if(count($race_html->find('td')) == 15){
					if($race_html->find('td span.bold', 0)){
						$race_obj = new \stdClass;
						$race_obj->meeting_id = intval($meeting_id);
						$race_obj->event_number = intval(trim($race_num));
						$race_obj->runner_number = trim($race_html->find('td', 2)->innertext);
						$position_obj = new \stdClass;
						$position_obj->finish_position = trim($race_html->find('td span.bold', 0)->innertext);
						$race_obj->race_data = $position_obj;
						array_push($ret, $race_obj);
					}
				}
			}
			$arr_tables = $ret_html->find('table.cover table');
			$__pos__ = 0;
			foreach ($arr_tables as $obj_table) {
				$obj_table_html = str_get_html($obj_table->innertext);
				$trs = $obj_table_html->find('tr.dbitem');
				if(count($trs) == 2){
					if($__pos__) break;
					if($obj_table_html->find('tr.dbdata', 0)){
						$obj_trs_html = str_get_html($trs[0]->innertext);
						$obj_trs_html_2 = str_get_html($obj_table_html->find('tr.dbdata', 0)->innertext);
						$tr_detail_items = $obj_trs_html_2->find('td');
						$__pos__ = 0;
						foreach ($obj_trs_html->find("td") as $tr_main_item) {
							if($tr_main_item->colspan == 3){
								$pay_obj = new \stdClass;
								$pay_key = trim(str_get_html($tr_main_item->innertext)->find("b", 0)->innertext);
								if(isset($__config_pays[$pay_key])){
									$pay_obj->kind = $__config_pays[$pay_key];
									$pay_obj->number = trim($tr_detail_items[3 * $__pos__ + 1]->innertext);
									$pay_obj->money = trim($tr_detail_items[3 * $__pos__ + 2]->innertext);
									array_push($__arr_repay, $pay_obj);							
								}
									
								$__pos__++;
							}
						}
					}
				}
			}
		}

		//@file_put_contents(date("YmdHis"), json_encode($__arr_repay));

		$repay_obj = new \stdClass;
		$repay_obj->meeting_id = intval($meeting_id);
		$repay_obj->event_number = intval(trim($race_num));
		$repay_detail_arr = new \stdClass;
		$repay_detail_arr->WIN = __get_payout($__arr_repay, "WIN");
		$repay_detail_arr->EXA = __get_payout($__arr_repay, "EXA");
		$repay_detail_arr->PLC = __get_payout($__arr_repay, "PLC");
		$repay_detail_arr->QNP = __get_payout($__arr_repay, "QNP");
		$repay_detail_arr->TRO = __get_payout($__arr_repay, "TRO");
		$repay_detail_arr->TRI = __get_payout($__arr_repay, "TRI");
		$repay_detail_arr->QNL = __get_payout($__arr_repay, "QNL");

		$repay_detail_obj = new \stdClass;
		$repay_detail_obj->japan_nar = $repay_detail_arr;
		$repay_obj->provider_results_data = $repay_detail_obj;

		$ret_obj->result = $ret;
		$ret_obj->repay = $repay_obj;

		return $ret_obj;
	}


	function __get_race_odds_init($__race_id, $__result = "") {
		$race_num = substr($__race_id, 6, strlen($__race_id) - 6);
		$meeting_id = substr($__race_id, 0, 6);
		$event_number = intval(trim($race_num));
		$results = __get_race_datas();

		$__url = "";
		for($ii=0; $ii<count($results); $ii++){
		  if($results[$ii]->meeting_id != $meeting_id) continue;
		  for($i=0; $i<count($results[$ii]->races); $i++){
		  	if($results[$ii]->meeting_id.$results[$ii]->races[$i]->race_id == $__race_id){
		  		$__url = $results[$ii]->races[$i]->href;
		  		break;
		  	}
		  }
		}
		$__url = str_replace("DebaTable", "OddsTanFuku", $__url);
		$ret = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == "") {
			$__result = __call_safe_url($__url);
		}		

		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_races = $ret_html->find('td.dbtbl table.bs tr');
			foreach($arr_races as $info_race) {
				$race_html = str_get_html($info_race->innertext);
				if($race_html->find('.dbdata', 0)){
					$oddsWin = ($race_html->find('.plus1bold01', 1))?trim($race_html->find('.plus1bold01', 1)->innertext):0;
					$oddsPlace = ($race_html->find('.plus1bold01', 2))?trim($race_html->find('.plus1bold01', 2)->innertext):0;
					$oddsPlace_arr = explode("-", $oddsPlace);
					$oddsPlace_min = $oddsPlace_arr[0];
					$oddsPlace_max = 0;
					if($oddsWin == "&nbsp;") $oddsWin = "";
					if(count($oddsPlace_arr) > 1) $oddsPlace_max = $oddsPlace_arr[1];
					array_push($ret, floatval($oddsWin));
					array_push($prices_min, floatval($oddsPlace_min));
					array_push($prices_max, floatval($oddsPlace_max));
				}
			}
		}

		$post_tanfuku = new \stdClass;
		$market = array();
		$market['prices'] = $ret;
		$market['timestamp'] = time();
		$market['total'] = 0;
		$win = array();
		$win['currency'] = 'JPY';
		$win['market'] = $market;

		$post_tanfuku->win = PrepMarketData($meeting_id, $event_number, 'WIN', $win);

		$market = array();
		$market['prices_min'] = $prices_min;
		$market['prices_max'] = $prices_max;
		$market['timestamp'] = time();
		$market['total'] = 0;

		$place = array();
		$place['currency'] = 'JPY';
		$place['market'] = $market;

		$post_tanfuku->plc = PrepMarketData($meeting_id, $event_number, 'PLC', $place);

		return $post_tanfuku;
	}

	function __get_race_odds_qnl($__race_id, $__result = "") {
		$race_num = substr($__race_id, 6, strlen($__race_id) - 6);
		$meeting_id = substr($__race_id, 0, 6);
		$event_number = intval(trim($race_num));
		$results = __get_race_datas();

		$__url = "";
		for($ii=0; $ii<count($results); $ii++){
		  if($results[$ii]->meeting_id != $meeting_id) continue;
		  for($i=0; $i<count($results[$ii]->races); $i++){
		  	if($results[$ii]->meeting_id.$results[$ii]->races[$i]->race_id == $__race_id){
		  		$__url = $results[$ii]->races[$i]->href;
		  		break;
		  	}
		  }
		}
		$__url = str_replace("DebaTable", "OddsUmLenFuku", $__url);
		$__url .= "&odds_flg=0";
		$ret = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == "") {
			$__result = __call_safe_url($__url);
		}	

		$ret_html = str_get_html($__result);
		if($ret_html){
  			$arr_races = $ret_html->find("tr.dbdata");
  			$arr_groups = [];
  			$col_number = 0;
			foreach($arr_races as $tr_obj) {
  				$group_pos = 0;
				foreach (str_get_html($tr_obj->innertext)->find('td') as $td_obj) {
					if($td_obj->colspan) {
						$arr_groups[$group_pos / 2] = intval($td_obj->innertext);
						$group_pos+=$td_obj->colspan;
					} else {
						if($group_pos % 2){
							if($td_obj->innertext != "&nbsp;"){
								$odds_value = floatval($td_obj->innertext);
								$group_number = $arr_groups[$group_pos / 2];
								if($col_number){
									if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
									else {
										$ret[$group_number] = [];
										$ret[$group_number][$col_number] = $odds_value;
									}
								}
							}
						} else {
							$col_number = intval($td_obj->innertext);
						}
						$group_pos++;
					}
				}
			}
		}
		
		$horseOdds = array();
		foreach($ret as $key => $price)
		{
			ksort($price);
			$horseOdds[$key] = $price;
		}
		ksort($horseOdds);

		$market = array();
		$market['prices'] = $horseOdds;
		$market['timestamp'] = time();
		$market['total'] = 0;
		$win = array();
		$win['currency'] = 'JPY';
		$win['market'] = $market;

		$post_umafuku = PrepMarketData($meeting_id, $event_number, 'QNL', $win);

		return $post_umafuku;
	}

	function __get_race_odds_exa($__race_id, $__result = "") {
		$race_num = substr($__race_id, 6, strlen($__race_id) - 6);
		$meeting_id = substr($__race_id, 0, 6);
		$event_number = intval(trim($race_num));
		$results = __get_race_datas();

		$__url = "";
		for($ii=0; $ii<count($results); $ii++){
		  if($results[$ii]->meeting_id != $meeting_id) continue;
		  for($i=0; $i<count($results[$ii]->races); $i++){
		  	if($results[$ii]->meeting_id.$results[$ii]->races[$i]->race_id == $__race_id){
		  		$__url = $results[$ii]->races[$i]->href;
		  		break;
		  	}
		  }
		}
		$__url = str_replace("DebaTable", "OddsUmLenTan", $__url);
		$__url .= "&odds_flg=0";
		$ret = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == "") {
			$__result = __call_safe_url($__url);
		}	

		$ret_html = str_get_html($__result);
		if($ret_html){
  			$arr_races = $ret_html->find("tr.dbdata");
  			$arr_groups = [];
  			$col_number = 0;
			foreach($arr_races as $tr_obj) {
  				$group_pos = 0;
				foreach (str_get_html($tr_obj->innertext)->find('td') as $td_obj) {
					if($td_obj->colspan) {
						$arr_groups[$group_pos / 2] = intval($td_obj->innertext);
						$group_pos+=$td_obj->colspan;
					} else {
						if($group_pos % 2){
							if($td_obj->innertext != "&nbsp;"){
								$odds_value = floatval($td_obj->innertext);
								$group_number = $arr_groups[$group_pos / 2];
								if($col_number){
									if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
									else {
										$ret[$group_number] = [];
										$ret[$group_number][$col_number] = $odds_value;
									}
								}
							}
						} else {
							$col_number = intval($td_obj->innertext);
						}
						$group_pos++;
					}
				}
			}
		}

		$horseOdds = array();
		foreach($ret as $key => $price)
		{
			ksort($price);
			$horseOdds[$key] = $price;
		}
		ksort($horseOdds);

		$market = array();
		$market['prices'] = $horseOdds;
		$market['timestamp'] = time();
		$market['total'] = 0;
		$win = array();
		$win['currency'] = 'JPY';
		$win['market'] = $market;

		$post_umafuku = PrepMarketData($meeting_id, $event_number, 'EXA', $win);

		return $post_umafuku;
	}

	function __get_race_odds_wide($__race_id, $__result = "") {
		$race_num = substr($__race_id, 6, strlen($__race_id) - 6);
		$meeting_id = substr($__race_id, 0, 6);
		$event_number = intval(trim($race_num));
		$results = __get_race_datas();

		$__url = "";
		for($ii=0; $ii<count($results); $ii++){
		  if($results[$ii]->meeting_id != $meeting_id) continue;
		  for($i=0; $i<count($results[$ii]->races); $i++){
		  	if($results[$ii]->meeting_id.$results[$ii]->races[$i]->race_id == $__race_id){
		  		$__url = $results[$ii]->races[$i]->href;
		  		break;
		  	}
		  }
		}
		$__url = str_replace("DebaTable", "OddsWide", $__url);
		$__url .= "&odds_flg=0";
		$ret = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == "") {
			$__result = __call_safe_url($__url);
		}

		$ret_html = str_get_html($__result);
		if($ret_html){
  			$arr_races = $ret_html->find("tr.dbdata");
  			$arr_groups = [];
  			$col_number = 0;
			foreach($arr_races as $tr_obj) {
  				$group_pos = 0;
				foreach (str_get_html($tr_obj->innertext)->find('td') as $td_obj) {
					if($td_obj->colspan) {
						$arr_groups[$group_pos / 2] = intval($td_obj->innertext);
						$group_pos+=$td_obj->colspan;
					} else {
						if($group_pos % 2){
							if($td_obj->innertext != "&nbsp;"){
								$odds_value = trim($td_obj->innertext);
								$group_number = $arr_groups[$group_pos / 2];
								if($col_number){
									$odds_value_arr = explode("-", $odds_value);
									$odds_min_value = floatval( $odds_value_arr[0] );
									$odds_max_value = 0;
									if(count($odds_value_arr) > 1) $odds_max_value = floatval( $odds_value_arr[1] );
									if(isset($prices_min[$group_number])) $prices_min[$group_number][$col_number] = $odds_min_value;
									else {
										$prices_min[$group_number] = [];
										$prices_min[$group_number][$col_number] = $odds_min_value;
									}
									if(isset($prices_max[$group_number])) $prices_max[$group_number][$col_number] = $odds_max_value;
									else {
										$prices_max[$group_number] = [];
										$prices_max[$group_number][$col_number] = $odds_max_value;
									}
								}
							}
						} else {
							$col_number = intval($td_obj->innertext);
						}
						$group_pos++;
					}
				}
			}
		}
		
		$market = array();

		$horseOdds = array();
		foreach($prices_min as $key => $price)
		{
			ksort($price);
			$horseOdds[$key] = $price;
		}
		ksort($horseOdds);

		$market['prices_min'] = $horseOdds;

		$horseOdds = array();
		foreach($prices_max as $key => $price)
		{
			ksort($price);
			$horseOdds[$key] = $price;
		}
		ksort($horseOdds);

		$market['prices_max'] = $horseOdds;

		$market['timestamp'] = time();
		$market['total'] = 0;
		$win = array();
		$win['currency'] = 'JPY';
		$win['market'] = $market;

		$post_umafuku = PrepMarketData($meeting_id, $event_number, 'QNP', $win);

		return $post_umafuku;
	}

	function __get_race_odds_tri($__race_id, $__result = "") {
		$race_num = substr($__race_id, 6, strlen($__race_id) - 6);
		$meeting_id = substr($__race_id, 0, 6);
		$event_number = intval(trim($race_num));
		$results = __get_race_datas();

		$__url = "";
		for($ii=0; $ii<count($results); $ii++){
		  if($results[$ii]->meeting_id != $meeting_id) continue;
		  for($i=0; $i<count($results[$ii]->races); $i++){
		  	if($results[$ii]->meeting_id.$results[$ii]->races[$i]->race_id == $__race_id){
		  		$__url = $results[$ii]->races[$i]->href;
		  		break;
		  	}
		  }
		}
		$__url = str_replace("DebaTable", "Odds3LenTan", $__url);
		$__url .= "&odds_flg=0";
		$ret = array();

		$horse_count = 0;
		$main_idx = 1;
		$__p_url = $__url."&k_selHoseNo=".$main_idx;
		$__result = __call_safe_url($__p_url);
		$ret_html = str_get_html($__result);
		if($ret_html){
			$ret = array();				
			$ret_html = str_get_html($__result);
			if($ret_html){
	  			$arr_races = $ret_html->find("tr.dbdata");
	  			$arr_horses = $ret_html->find("tr.dbdata td table tr td");
	  			$horse_count = count($arr_horses);
	  			$arr_groups = [];
	  			$col_number = 0;
				foreach($arr_races as $tr_obj) {
	  				$group_pos = 0;
					foreach (str_get_html($tr_obj->innertext)->find('td') as $td_obj) {
						if(!($td_obj->class)) continue;
						if((substr($td_obj->class, 0, 6) != "dbdata") && (substr($td_obj->class, 0, 6) != "dbitem")) continue;
						if($td_obj->colspan) {
							$arr_groups[$group_pos / 2] = intval($td_obj->find('span', 0)->innertext);
							$group_pos+=$td_obj->colspan;
						} else {
							if($group_pos % 2){
								if($td_obj->find('span', 0)->innertext != "&nbsp;"){
									$odds_value = floatval($td_obj->find('span', 0)->innertext);
									$group_number = $arr_groups[$group_pos / 2];
									if($col_number){
										if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
										else {
											$ret[$group_number] = [];
											$ret[$group_number][$col_number] = $odds_value;
										}
									}
								}
							} else {
								$col_number = intval($td_obj->find('span', 0)->innertext);
							}
							$group_pos++;
						}
					}
				}
			}
			$horseOdds = array();
			foreach($ret as $key => $price)
			{
				ksort($price);
				$horseOdds[$key] = $price;
			}
			ksort($horseOdds);
			$prices[$main_idx] = $horseOdds;
		}

		for($main_idx = 2; $main_idx <= $horse_count; $main_idx++){
			$__p_url = $__url."&k_selHoseNo=".$main_idx;
			$__result = __call_safe_url($__p_url);
			$ret_html = str_get_html($__result);
			if($ret_html){
				$ret = array();				
				$ret_html = str_get_html($__result);
				if($ret_html){
		  			$arr_races = $ret_html->find("tr.dbdata");
		  			$arr_groups = [];
		  			$col_number = 0;
					foreach($arr_races as $tr_obj) {
		  				$group_pos = 0;
						foreach (str_get_html($tr_obj->innertext)->find('td') as $td_obj) {
							if(!($td_obj->class)) continue;
							if((substr($td_obj->class, 0, 6) != "dbdata") && (substr($td_obj->class, 0, 6) != "dbitem")) continue;
							if($td_obj->colspan) {
								$arr_groups[$group_pos / 2] = intval($td_obj->find('span', 0)->innertext);
								$group_pos+=$td_obj->colspan;
							} else {
								if($group_pos % 2){
									if($td_obj->find('span', 0)->innertext != "&nbsp;"){
										$odds_value = floatval($td_obj->find('span', 0)->innertext);
										$group_number = $arr_groups[$group_pos / 2];
										if($col_number){
											if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
											else {
												$ret[$group_number] = [];
												$ret[$group_number][$col_number] = $odds_value;
											}
										}
									}
								} else {
									$col_number = intval($td_obj->find('span', 0)->innertext);
								}
								$group_pos++;
							}
						}
					}
				}
				$horseOdds = array();
				foreach($ret as $key => $price)
				{
					ksort($price);
					$horseOdds[$key] = $price;
				}
				ksort($horseOdds);
				$prices[$main_idx] = $horseOdds;
			}
		}

		$market = array();
		$market['prices'] = $prices;
		$market['timestamp'] = time();
		$market['total'] = 0;
		$win = array();
		$win['currency'] = 'JPY';
		$win['market'] = $market;

		$post_umafuku = PrepMarketData($meeting_id, $event_number, 'TRI', $win);

		return $post_umafuku;
	}

	function __get_race_odds_tro($__race_id, $__result = "") {

		$race_num = substr($__race_id, 6, strlen($__race_id) - 6);
		$meeting_id = substr($__race_id, 0, 6);
		$event_number = intval(trim($race_num));
		$results = __get_race_datas();

		$__url = "";
		for($ii=0; $ii<count($results); $ii++){
		  if($results[$ii]->meeting_id != $meeting_id) continue;
		  for($i=0; $i<count($results[$ii]->races); $i++){
		  	if($results[$ii]->meeting_id.$results[$ii]->races[$i]->race_id == $__race_id){
		  		$__url = $results[$ii]->races[$i]->href;
		  		break;
		  	}
		  }
		}
		$__url = str_replace("DebaTable", "Odds3LenFuku", $__url);
		$__url .= "&odds_flg=0";
		$ret = array();

		$horse_count = 0;
		$main_idx = 1;
		$__p_url = $__url."&k_selHoseNo=".$main_idx;
		$__result = __call_safe_url($__p_url);
		$ret_html = str_get_html($__result);
		if($ret_html){
			$ret = array();				
			$ret_html = str_get_html($__result);
			if($ret_html){
	  			$arr_races = $ret_html->find("td.dbtbl tr");
	  			$arr_horses = $ret_html->find("tr.dbdata td table tr td");
	  			$horse_count = (count($arr_horses) + 1) / 2;
	  			$arr_groups = [];
	  			$col_number = 0;
				foreach($arr_races as $tr_obj) {
	  				$group_pos = 0;
					foreach (str_get_html($tr_obj->innertext)->find('td') as $td_obj) {
						if(!($td_obj->class)) continue;
						if((substr($td_obj->class, 0, 6) != "dbdata") && (substr($td_obj->class, 0, 6) != "dbitem")) continue;
						if($td_obj->colspan) {
							$arr_groups[$group_pos / 2] = intval($td_obj->innertext);
							$group_pos+=$td_obj->colspan;
						} else {
							if($group_pos % 2){
								if($td_obj->innertext != "&nbsp;"){
									$odds_value = floatval($td_obj->innertext);
									$group_number = $arr_groups[$group_pos / 2];
									if($col_number){
										if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
										else {
											$ret[$group_number] = [];
											$ret[$group_number][$col_number] = $odds_value;
										}
									}
								}
							} else {
								$col_number = intval($td_obj->find('span', 0)->innertext);
							}
							$group_pos++;
						}
					}
				}
			}
			$horseOdds = array();
			foreach($ret as $key => $price)
			{
				ksort($price);
				$horseOdds[$key] = $price;
			}
			ksort($horseOdds);
			$prices[$main_idx] = $horseOdds;
		}

		for($main_idx = 2; $main_idx <= $horse_count; $main_idx++){
			$__p_url = $__url."&k_selHoseNo=".$main_idx;
			$__result = __call_safe_url($__p_url);
			$ret_html = str_get_html($__result);
			if($ret_html){
				$ret = array();				
				$ret_html = str_get_html($__result);
				if($ret_html){
		  			$arr_races = $ret_html->find("td.dbtbl tr");
		  			$arr_groups = [];
		  			$col_number = 0;
					foreach($arr_races as $tr_obj) {
		  				$group_pos = 0;
						foreach (str_get_html($tr_obj->innertext)->find('td') as $td_obj) {
							if(!($td_obj->class)) continue;
							if((substr($td_obj->class, 0, 6) != "dbdata") && (substr($td_obj->class, 0, 6) != "dbitem")) continue;
							if($td_obj->colspan) {
								$arr_groups[$group_pos / 2] = intval($td_obj->innertext);
								$group_pos+=$td_obj->colspan;
							} else {
								if($group_pos % 2){
									if($td_obj->innertext != "&nbsp;"){
										$odds_value = floatval($td_obj->innertext);
										$group_number = $arr_groups[$group_pos / 2];
										if($col_number){
											if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
											else {
												$ret[$group_number] = [];
												$ret[$group_number][$col_number] = $odds_value;
											}
										}
									}
								} else {
									$col_number = intval($td_obj->find('span', 0)->innertext);
								}
								$group_pos++;
							}
						}
					}
				}
				$horseOdds = array();
				foreach($ret as $key => $price)
				{
					ksort($price);
					$horseOdds[$key] = $price;
				}
				ksort($horseOdds);
				$prices[$main_idx] = $horseOdds;
			}
		}

		$market = array();
		$market['prices'] = $prices;
		$market['timestamp'] = time();
		$market['total'] = 0;
		$win = array();
		$win['currency'] = 'JPY';
		$win['market'] = $market;

		$post_umafuku = PrepMarketData($meeting_id, $event_number, 'TRO', $win);

		return $post_umafuku;
	}

	function get_odds_win_race_4ext($__odds, $meeting_id, $event_number)
	{
	  $win_ret = array();
	  $plc_min_ret = array();
	  $plc_max_ret = array();

	  for ($i=0; $i<count($__odds); $i++) {	  	
	    $win_obj = explode("-", $__odds[$i]);
	    $horse_number = $win_obj[0];
	    $win_ret[$horse_number] = floatval( $win_obj[1] );
	    $plc_min_ret[$horse_number] = floatval( $win_obj[2] );
	    $plc_max_ret[$horse_number] = floatval( $win_obj[3] );
	  }

	  ksort($win_ret);
	  ksort($plc_min_ret);
	  ksort($plc_max_ret);

	  $prices = [];
	  foreach ($win_ret as $value) {
	    $prices[] = $value;
	  }

	  $post_tanfuku = new \stdClass;
	  $market = array();
	  $market['prices'] = $prices;
	  $market['timestamp'] = time();
	  $market['total'] = 0;
	  $win = array();
	  $win['currency'] = 'JPY';
	  $win['market'] = $market;

	  $post_data = PrepMarketData($meeting_id, $event_number, 'WIN', $win, 'japan_nar');
	  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "win", "keiba");

	  $market = array();
	  $prices = [];
	  foreach ($plc_min_ret as $value) {
	    $prices[] = $value;
	  }
	  $market['prices_min'] = $prices;
	  $prices = [];
	  foreach ($plc_max_ret as $value) {
	    $prices[] = $value;
	  }
	  $market['prices_max'] = $prices;
	  $market['timestamp'] = time();
	  $market['total'] = 0;

	  $place = array();
	  $place['currency'] = 'JPY';
	  $place['market'] = $market;

	  $post_data = PrepMarketData($meeting_id, $event_number, 'PLC', $place, 'japan_nar');
	  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "plc", "keiba");

	  return true;
	}

	function get_odds_qnp_race_4ext($__odds, $meeting_id, $event_number)
	{
	  $prices = array();
	  $prices2 = array();
	  for ($i=0; $i<count($__odds); $i++) {
	    $group_info = $__odds[$i];
	    $arr_group = explode("-", $group_info);
	    if(count($arr_group) == 4){
	      $key1 = $arr_group[0];
	      $key2 = $arr_group[1];
	      $group_value = $arr_group[2];
	      $group_value2 = $arr_group[3];
	      if(!isset($prices[$key1])) $prices[$key1] = [];
	      if(!isset($prices2[$key1])) $prices2[$key1] = [];
	      $prices[$key1][$key2] = floatval( $group_value );
	      $prices2[$key1][$key2] = floatval( $group_value2 );
	    } 
	  }

	  $horseOdds = array();
	  foreach($prices as $key => $price)
	  {
	    ksort($price);
	    $horseOdds[$key] = $price;
	  }
	  ksort($horseOdds);

	  $horseOdds2 = array();
	  foreach($prices2 as $key => $price)
	  {
	    ksort($price);
	    $horseOdds2[$key] = $price;
	  }
	  ksort($horseOdds2);

	  $post_tanfuku = new \stdClass;
	  $market = array();
	  $market['prices_min'] = $horseOdds;
	  $market['prices_max'] = $horseOdds2;
	  $market['timestamp'] = time();
	  $market['total'] = 0;
	  $win = array();
	  $win['currency'] = 'JPY';
	  $win['market'] = $market;

	  $post_data = PrepMarketData($meeting_id, $event_number, 'QNP', $win, 'japan_nar');
	  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "qnp", "keiba");

	  return true;
	}

	function get_odds_qnl_race_4ext($__odds, $meeting_id, $event_number)
	{
	  $prices = array();
	  for ($i=0; $i<count($__odds); $i++) {
	    $group_info = $__odds[$i];
	    $arr_group = explode("-", $group_info);
	    if(count($arr_group) == 3){
	      $key1 = $arr_group[0];
	      $key2 = $arr_group[1];
	      $group_value = $arr_group[2];
	      if(!isset($prices[$key1])) $prices[$key1] = [];
	      $prices[$key1][$key2] = floatval( $group_value );
	    } 
	  }

	  $horseOdds = array();
	  foreach($prices as $key => $price)
	  {
	    ksort($price);
	    $horseOdds[$key] = $price;
	  }
	  ksort($horseOdds);

	  $post_tanfuku = new \stdClass;
	  $market = array();
	  $market['prices'] = $horseOdds;
	  $market['timestamp'] = time();
	  $market['total'] = 0;
	  $win = array();
	  $win['currency'] = 'JPY';
	  $win['market'] = $market;

	  $post_data = PrepMarketData($meeting_id, $event_number, 'QNL', $win, 'japan_nar');
	  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "qnl", "keiba");

	  return true;
	}

	function get_odds_exa_race_4ext($__odds, $meeting_id, $event_number)
	{
	  $prices = array();
	  for ($i=0; $i<count($__odds); $i++) {
	    $group_info = $__odds[$i];
	    $arr_group = explode("-", $group_info);
	    if(count($arr_group) == 3){
	      $key1 = $arr_group[0];
	      $key2 = $arr_group[1];
	      $group_value = $arr_group[2];
	      if(!isset($prices[$key1])) $prices[$key1] = [];
	      $prices[$key1][$key2] = floatval( $group_value );
	    } 
	  }

	  $horseOdds = array();
	  foreach($prices as $key => $price)
	  {
	    ksort($price);
	    $horseOdds[$key] = $price;
	  }
	  ksort($horseOdds);

	  $post_tanfuku = new \stdClass;
	  $market = array();
	  $market['prices'] = $horseOdds;
	  $market['timestamp'] = time();
	  $market['total'] = 0;
	  $win = array();
	  $win['currency'] = 'JPY';
	  $win['market'] = $market;

	  $post_data = PrepMarketData($meeting_id, $event_number, 'EXA', $win, 'japan_nar');
	  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "exa", "keiba");

	  return true;
	}

	function get_odds_tro_race_4ext($__odds, $meeting_id, $event_number)
	{
	  $prices = array();
	  for ($i=0; $i<count($__odds); $i++) {
	    $group_info = $__odds[$i];
	    $arr_group = explode("-", $group_info);
	    if(count($arr_group) == 4){
	      $key1 = $arr_group[0];
	      $key2 = $arr_group[1];
	      $key3 = $arr_group[2];
	      $group_value = $arr_group[3];
	      if(!isset($prices[$key1])) $prices[$key1] = [];
	      if(!isset($prices[$key1][$key2])) $prices[$key1][$key2] = [];
	      $prices[$key1][$key2][$key3] = floatval( $group_value );
	    } 
	  }

	  $horseOdds = array();
	  foreach($prices as $key => $price)
	  {
	    $horseOdds2 = array();
	    foreach($price as $key2 => $price2)
	    {
	      ksort($price2);
	      $horseOdds2[$key2] = $price2;
	    }
	    ksort($horseOdds2);
	    $horseOdds[$key] = $horseOdds2;
	  }
	  ksort($horseOdds);

	  $post_tanfuku = new \stdClass;
	  $market = array();
	  $market['prices'] = $horseOdds;
	  $market['timestamp'] = time();
	  $market['total'] = 0;
	  $win = array();
	  $win['currency'] = 'JPY';
	  $win['market'] = $market;

	  $post_data = PrepMarketData($meeting_id, $event_number, 'TRO', $win, 'japan_nar');
	  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "tro", "keiba");

	  return true;
	}

	function get_odds_tri_race_4ext($__odds, $meeting_id, $event_number)
	{
	  $prices = array();
	  for ($i=0; $i<count($__odds); $i++) {
	    $group_info = $__odds[$i];
	    $arr_group = explode("-", $group_info);
	    if(count($arr_group) == 4){
	      $key1 = $arr_group[0];
	      $key2 = $arr_group[1];
	      $key3 = $arr_group[2];
	      $group_value = $arr_group[3];
	      if(!isset($prices[$key1])) $prices[$key1] = [];
	      if(!isset($prices[$key1][$key2])) $prices[$key1][$key2] = [];
	      $prices[$key1][$key2][$key3] = floatval( $group_value );
	    } 
	  }

	  $horseOdds = array();
	  foreach($prices as $key => $price)
	  {
	    $horseOdds2 = array();
	    foreach($price as $key2 => $price2)
	    {
	      ksort($price2);
	      $horseOdds2[$key2] = $price2;
	    }
	    ksort($horseOdds2);
	    $horseOdds[$key] = $horseOdds2;
	  }
	  ksort($horseOdds);

	  $post_tanfuku = new \stdClass;
	  $market = array();
	  $market['prices'] = $horseOdds;
	  $market['timestamp'] = time();
	  $market['total'] = 0;
	  $win = array();
	  $win['currency'] = 'JPY';
	  $win['market'] = $market;

	  $post_data = PrepMarketData($meeting_id, $event_number, 'TRI', $win, 'japan_nar');

	  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "tri", "keiba");

	  return true;
	}

	function __get_race_odds_data_full($__race_id, $spec = false){
		$race_num = substr($__race_id, 6, strlen($__race_id) - 6);
		$meeting_id = substr($__race_id, 0, 6);
		$event_number = intval(trim($race_num));

		if(__check_meeting_race_spec($meeting_id, $event_number, $spec)) return false;
		$send_check = __check_send_data_config($spec);
		$log_check = __check_log_data_config($spec);

		$post_data = __get_race_odds_init($__race_id);
		$post_data->qnl = __get_race_odds_qnl($__race_id);
		$post_data->exa = __get_race_odds_exa($__race_id);
		$post_data->qnp = __get_race_odds_wide($__race_id);
		$post_data->tri = __get_race_odds_tri($__race_id);
		$post_data->tro = __get_race_odds_tro($__race_id);

		if(!($post_data->win)) return false;
		
		__log_and_debug_and_send_data($meeting_id, $event_number, $post_data, $log_check, $send_check, "keiba");

		return $post_data;
	}

	function __get_race_odds_overall($spec = false){
		$max_time_diff = 3600 * 10;
		if($spec){
			if(isset($spec->duration)) $max_time_diff = $spec->duration * 3600;
		}
		$ret = __get_race_datas();
		for($i=0; $i<count($ret); $i++){
			if($ret[$i]->meeting_id == 0) continue;
			for($j=0; $j<count($ret[$i]->races); $j++){
				$race_time = $ret[$i]->races[$j]->time;
				$now_time = GetCurrentJapanTime();
				$race_time = date("Y-m-d", strtotime($now_time)).$race_time.":00";
				$time_diff = strtotime($race_time) - strtotime($now_time);
				if(($time_diff > $max_time_diff) || ($time_diff < -300)) continue;
				$__race_id = $ret[$i]->meeting_id.$ret[$i]->races[$j]->race_id;
				__show_debug_info__("Start Checking NAR Odds Info ...");
				__show_debug_info__("Race ID: ".$__race_id);
				__get_race_odds_data_full($__race_id, $spec);
				__show_debug_info__("End Checking NAR Odds Info ...");
			}
		}
	}

	function __get_race_odds_overall2($spec = false){
		while (true) {
			__show_debug_info__("Checking Race Times ...");
			__get_race_odds_overall($spec);
			sleep(1);
		}
	}

	function check_race_time_change() {
		$arr_result = [];

		$new_tracks = __get_race_datas_4_check();

		for($i=0; $i<count($new_tracks); $i++){
			if(!($new_tracks[$i]->venue_info)) continue;

			$new_races = $new_tracks[$i]->races;
			for($j=0; $j<count($new_races); $j++) {
				$change_event = new \stdClass;
				$change_event->venue_id = $new_tracks[$i]->venue_info->venue_id;
				$change_event->venue_type = $new_tracks[$i]->venue_info->venue_type;
				$change_event->venue_name = $new_tracks[$i]->venue_info->venue_name;
				$change_event->meeting_date = $new_tracks[$i]->venue_info->meeting_date;
				$change_event->number = $new_races[$j]->id;
				$change_event->time = $new_races[$j]->time;
				$change_event->type = "time";
				$arr_result[] = $change_event;
			}
			$new_events = $new_tracks[$i]->events;
			for($j=0; $j<count($new_events); $j++) {
				if(($new_events[$j]->content == "競走除外") || ($new_events[$j]->content == "出走取消") || ($new_events[$j]->content == "競走中止")){
					$changed_type = "scratched";
					if($new_events[$j]->content == "競走除外") $changed_type = "withdrawn";
					else if($new_events[$j]->content == "競走中止") $changed_type = "disqualified";

					$change_event = new \stdClass;
					$change_event->venue_id = $new_tracks[$i]->venue_info->venue_id;
					$change_event->venue_type = $new_tracks[$i]->venue_info->venue_type;
					$change_event->venue_name = $new_tracks[$i]->venue_info->venue_name;
					$change_event->meeting_date = $new_tracks[$i]->venue_info->meeting_date;
					$change_event->event_number = intval(str_replace("R", "", $new_events[$j]->id));
					$change_event->number = $new_events[$j]->number;
					$change_event->horse = $new_events[$j]->horse;
					$change_event->type = $changed_type;
					$arr_result[] = $change_event;
				}
			}
		}

		$now_time = GetCurrentJapanTime();
		$file_name = date("Ymd", strtotime($now_time))."_realtime_nar.dat";

		$old_data = @file_get_contents($file_name);
		@file_put_contents($file_name, json_encode($arr_result));
		if(json_encode($arr_result) != $old_data) {
			@file_put_contents($file_name, json_encode($arr_result));
			return $arr_result;	
		}
		return array();
	}

	function __get_race_results_for_scratch() {
		$ret = [];

		$results = __get_race_datas();

		$__url = "";
		for($ii=0; $ii<count($results); $ii++){
			$meeting_id = $results[$ii]->meeting_id;
			if(!($results[$ii]->venue_info)) continue;
		  	for($i=0; $i<count($results[$ii]->races); $i++){
		  		$race_num = $results[$ii]->races[$i]->race_id;

				$race_time = $results[$ii]->races[$i]->time;
				$now_time = GetCurrentJapanTime();
				$race_time = date("Y-m-d", strtotime($now_time)).$race_time.":00";
				$time_diff = strtotime($now_time) - strtotime($race_time);
				if(($time_diff < 0) || ($time_diff > 3600)) continue;

		  		$__url = $results[$ii]->races[$i]->href;

				$__url = str_replace("DebaTable", "RaceMarkTable", $__url);
				$__result = __call_safe_url_00($__url);

				$ret_html = str_get_html($__result);
				if($ret_html){
			  		$arr_races = $ret_html->find('td.containerMain table.cover table.bs td.dbtbl table.bs tr');

					foreach($arr_races as $info_race) {
						$race_html = str_get_html($info_race->innertext);
						if(count($race_html->find('td')) == 15){
							if($race_html->find('td span.bold', 0)){
								$race_obj = new \stdClass;
								$race_obj->meeting_id = intval($meeting_id);
								$race_obj->event_number = intval(trim($race_num));
								$race_obj->runner_number = trim($race_html->find('td', 2)->innertext);
								$position_obj = new \stdClass;
								$finish_position = trim($race_html->find('td span.bold', 0)->innertext);
								
								if(!is_numeric($finish_position)){
									$race_obj->reason = trim($race_html->find('td', 12)->innertext);
									$horse_elem = $race_html->find('td', 3);
									$race_obj->horse_name = $horse_elem->find('span a',0)->innertext;

					$changed_type = "scratched";
					if($race_obj->reason == "除外") $changed_type = "withdrawn";
					else if($race_obj->reason == "中止") $changed_type = "disqualified";

					$change_event = new \stdClass;
					$change_event->venue_id = $results[$ii]->venue_info->venue_id;
					$change_event->venue_type = $results[$ii]->venue_info->venue_type;
					$change_event->venue_name = $results[$ii]->venue_info->venue_name;
					$change_event->meeting_date = $results[$ii]->venue_info->meeting_date;
					$change_event->event_number = $race_obj->event_number;
					$change_event->number = $race_obj->runner_number;
					$change_event->horse = $race_obj->horse_name;
					$change_event->type = $changed_type;
					$ret[] = $change_event;

								}
							}
						}
					}
				}
		  	}
		}


		$now_time = GetCurrentJapanTime();
		$file_name = date("Ymd", strtotime($now_time))."_realtime_nar2.dat";

		$old_data = @file_get_contents($file_name);
		@file_put_contents($file_name, json_encode($ret));
		if(json_encode($ret) != $old_data) {
			@file_put_contents($file_name, json_encode($ret));
			return $ret;	
		}
		return array();
	}
?>