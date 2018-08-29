<?php

	include('common_lib.php');

	$__proc_url = "http://race.netkeiba.com/?pid=race_list";
	$__result_url = "http://race.netkeiba.com/?pid=race&mode=result&id=";
	$__odds_url = "http://race.netkeiba.com/?pid=put_raceinfo&rid=";

	function __get_race_datas($proc_date = "") {
		$arr_meetings = __get_meeting_ids();

		$now_time = GetCurrentJapanTime();
		$d = date("Y-m-d", strtotime($now_time));
		$file_name = date("Ymd", strtotime($now_time))."_netkeiba.json";

		if(($proc_date != "") && ($proc_date != date("Ymd", strtotime($now_time)))){
			$results = array();

			$file_name = "logs/backup/".$proc_date."_netkeiba.json";
			if(file_exists($file_name))
				return json_decode(@file_get_contents($file_name));

			return $results;
		}
		
		if(file_exists($file_name)){
			$results = json_decode(@file_get_contents($file_name));
		} else {
			$results = array();
			$tracks = __get_odds_track($d);
			for($i=0; $i<count($tracks); $i++){
				$track_obj = $tracks[$i];
	      		$track_obj->meeting_id = __get_meeting_id($arr_meetings, $track_obj->meeting_name);
				array_push($results, $track_obj);
			}
			
			file_put_contents($file_name, json_encode( $results ));
		}
		return $results;
	}

	function __get_odds_track($__date = null) {
		global $__proc_url;

		$ret = array();

		$now_time = GetCurrentJapanTime();
		
		$__result = __call_safe_url($__proc_url."&id=c".date("md", strtotime($now_time)));
		$__result = iconv("EUC-JP", "UTF-8", $__result);
		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_tracks = $ret_html->find('.race_top_hold_list');
			foreach($arr_tracks as $info_track) {
				$track_html = str_get_html($info_track->innertext);
				if($track_html->find('.kaisaidata', 0)){
					$a_obj = $track_html->find('.kaisaidata', 0);
					$track_obj = new \stdClass;
					$track_obj->meeting_name = $a_obj->innertext;
					if(__get_until_values($track_obj->meeting_name, "<span")) $track_obj->meeting_name = __get_until_values($track_obj->meeting_name, "<span");
					$race_array = array();

					$arr_races = $track_html->find('.race_top_data_info');
					foreach($arr_races as $info_race) {
						$race_obj = new \stdClass;
						$race_html = str_get_html($info_race->innertext);
						$race_obj->race_text = $race_html->find('a', 0)->title;
						$race_obj->race_id =  __get_after_values(trim($race_html->find('a', 1)->href), '&id=');
						if(__get_until_values($race_obj->race_id, "&")) $race_obj->race_id = __get_until_values($race_obj->race_id, "&");
						$race_obj->event_number = str_replace("R", "", trim($race_html->find('a img', 0)->alt));
						$race_obj->id = $race_obj->event_number;
						$race_obj->time = trim(__get_until_values($race_html->find('.racedata', 0)->innertext, '&nbsp;'));
						if(strlen($race_obj->time) > 8) $race_obj->time = trim(__get_after_values($race_obj->time, '>'));
						if(strlen($race_obj->time) > 8) {
							$race_obj->time = trim(__get_last_values($race_obj->time, ' '));
						}
						array_push($race_array, $race_obj);
					}
					$track_obj->races = $race_array;
					array_push($ret, $track_obj);
				}
			}
		}
		return $ret;
	}

	function __get_payout($__arr_repay, $__kind) {
		$ret = new \stdClass;
		$check_no_exist = true;
		for($i=0; $i<count($__arr_repay); $i++){
			if($__arr_repay[$i]->kind == $__kind){
				$number = $__arr_repay[$i]->number;
				$money = $__arr_repay[$i]->money;
				$number = str_replace(" - ", "/", $number);
				$number = str_replace(" → ", "/", $number);				
				$money = str_replace("円", "", $money);
				$money = str_replace(",", "", $money);
				$__arr_number = explode("<br />", $number);
				$__arr_money = explode("<br />", $money);
				for($j=0; $j<count($__arr_number); $j++){
					if(count($__arr_money) > $j){
						$__fld_name = trim($__arr_number[$j]);
						$__fld_name_00 = explode("/", $__fld_name);
						for($k=0; $k<count($__fld_name_00); $k++)
							$__fld_name_00[$k] = intval($__fld_name_00[$k]);
						$__fld_name = implode("/", $__fld_name_00);
						$__fld_value = trim($__arr_money[$j]);
						if(is_numeric($__fld_value)){
							$__fld_value = round($__fld_value / 100, 2);
						} else {
							$__fld_value = 0;
						}
						$payout_obj = new \stdClass;
						$payout_obj->payout = $__fld_value;
						$ret->$__fld_name = $payout_obj;
						$check_no_exist = false;
					}
				}
			}
		}
		if($check_no_exist) return false;
		return $ret;
	}

	function __get_today_odds_cname_4ext(){
		$ret = __get_race_datas();
		$result = [];
		for($i=0; $i<count($ret); $i++){
			if($ret[$i]->meeting_id == 0) continue;
			for($j=0; $j<count($ret[$i]->races); $j++){
				$race_obj = new \stdClass;

				$race_obj->meeting_id = $ret[$i]->meeting_id;
				$race_obj->race_number = $ret[$i]->races[$j]->event_number;
				$race_obj->time = $ret[$i]->races[$j]->time;
				$race_obj->race_id = $ret[$i]->races[$j]->race_id;

				$result[] = $race_obj;
			}
		}
	  	echo json_encode($result);
	}

	function __get_race_results($__race_id) {
		global $__result_url;

		$ret_obj = new \stdClass;

		$race_num = substr($__race_id, -2);
		$race_datas = __get_race_datas();
		$meeting_id = "";

		for($i=0; $i<count($race_datas); $i++){
			$track_obj = $race_datas[$i];
			for($j=0; $j<count($track_obj->races); $j++){
				if($track_obj->races[$j]->race_id == $__race_id){
					$meeting_id = $track_obj->meeting_id;
				}
			}
		}

		$ret = array();
		
		$__url = $__result_url.$__race_id;
		$__result = __call_safe_url($__url);
		$__result = iconv("EUC-JP", "UTF-8", $__result);

		$__arr_repay = array();

		$__config_check = array( "馬単", "三連複", "三連単", "単勝", "複勝", "ワイド", "馬連" );
		$__config_pays = array( "馬単" => "EXA", "三連複" => "TRO", "三連単" => "TRI", "単勝" => "WIN", "複勝" => "PLC", "ワイド" => "QNP", "馬連" => "QNL" );

		$ret_html = str_get_html($__result);
		if($ret_html){
			$result_table = $ret_html->find('.race_table_01', 0);
			if($result_table){
		  		$arr_races = str_get_html($result_table)->find('tr');
				foreach($arr_races as $info_race) {
					$race_html = str_get_html($info_race->innertext);
					if($race_html->find('td.result_rank', 0)){
				          $race_obj = new \stdClass;
				          $race_obj->meeting_id = intval($meeting_id);
				          $race_obj->event_number = intval(trim($race_num));
				          $race_obj->runner_number = trim($race_html->find('td', 2)->innertext);
				          $position_obj = new \stdClass;
				          $position_obj->finish_position = trim($race_html->find('td.result_rank', 0)->innertext);
				          $race_obj->race_data = $position_obj;
						array_push($ret, $race_obj);
					}
				}
				
				$arr_pays = $ret_html->find('.pay_table_01 tr');
				foreach($arr_pays as $info_pay) {
					$pay_html = str_get_html($info_pay->innertext);
					if($pay_html->find('th', 0)){
						$th_1 = $pay_html->find('th', 0);
						if($th_1->innertext){
							if(in_array(trim($th_1->innertext), $__config_check)){
					          $pay_obj = new \stdClass;
					          $pay_obj->kind = $__config_pays[trim($th_1->innertext)];
					          $pay_obj->number = trim($pay_html->find('td', 0)?$pay_html->find('td', 0)->innertext:"");
					          $pay_obj->money = trim($pay_html->find('td', 1)?$pay_html->find('td', 1)->innertext:"");
							  array_push($__arr_repay, $pay_obj);
							}
						}
					}
				}
			}
		}

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

		if(!(__get_payout($__arr_repay, "WIN"))) {
			$ret = array();
		}

		$repay_detail_obj = new \stdClass;
		$repay_detail_obj->japan_jra = $repay_detail_arr;
		$repay_obj->provider_results_data = $repay_detail_obj;

		$ret_obj->result = $ret;
		$ret_obj->repay = $repay_obj;

		return $ret_obj;
	}

	function __get_race_odds($__race_id) {
		global $__odds_url;

		$race_num = substr($__race_id, -2);
		$race_datas = __get_race_datas();
		$meeting_id = "";

		for($i=0; $i<count($race_datas); $i++){
			$track_obj = $race_datas[$i];
			for($j=0; $j<count($track_obj->races); $j++){
				if($track_obj->races[$j]->race_id == $__race_id){
					$meeting_id = $track_obj->meeting_id;
				}
			}
		}
		$event_number = intval(trim($race_num));

		$__url = $__odds_url.substr($__race_id,1);
		$__result = __call_safe_url($__url);
		$race_info = json_decode($__result, true);
		$odds = $race_info["odds"];

		if(isset($odds[1])){
			$win_ret = [];
			$win_arr = $odds[1];
			foreach ($win_arr as $key => $value) {
				$win_ret[$key] = floatval( $value["win"] );
			}
			$win_ret = patch_array($win_ret);
			$post_tanfuku = new \stdClass;
			$market = array();
			$market['prices'] = $win_ret;
			$market['timestamp'] = time();
			$market['total'] = 0;
			$win = array();
			$win['currency'] = 'JPY';
			$win['market'] = $market;

			$post_tanfuku->win = PrepMarketData($meeting_id, $event_number, 'WIN', $win, 'japan_jra');
		} else {
			$post_tanfuku->win = false;
		}

		if(isset($odds[2])){
			$min_ret = [];
			$max_ret = [];
			$plc_arr = $odds[2];
			foreach ($plc_arr as $key => $value) {
				$min_ret[$key] = floatval( $value["min"] );
				$max_ret[$key] = floatval( $value["max"] );
			}
			$min_ret = patch_array($min_ret);
			$max_ret = patch_array($max_ret);
			$market = array();
			$market['prices_min'] = $min_ret;
			$market['prices_max'] = $max_ret;
			$market['timestamp'] = time();
			$market['total'] = 0;

			$place = array();
			$place['currency'] = 'JPY';
			$place['market'] = $market;

			$post_tanfuku->plc = PrepMarketData($meeting_id, $event_number, 'PLC', $place, 'japan_jra');
		} else {
			$post_tanfuku->plc = false;
		}

		if(isset($odds[4])){
			$qnl_ret = [];
			$qnl_arr = $odds[4];
			foreach ($qnl_arr as $key => $value) {
				$arr_key = explode("_", $key);
				if(count($arr_key) == 2){
					$key1 = $arr_key[0];
					$key2 = $arr_key[1];
					if(!isset($qnl_ret[$key1])) $qnl_ret[$key1] = [];
					$qnl_ret[$key1][$key2] = floatval( $value );
				}
			}

			$market = array();
			$market['prices'] = $qnl_ret;
			$market['timestamp'] = time();
			$market['total'] = 0;
			$win = array();
			$win['currency'] = 'JPY';
			$win['market'] = $market;

			$post_tanfuku->qnl = PrepMarketData($meeting_id, $event_number, 'QNL', $win, 'japan_jra');
		} else {
			$post_tanfuku->qnl = false;
		}

		if(isset($odds[5])){
			$min_ret = [];
			$max_ret = [];
			$qnp_arr = $odds[5];
			foreach ($qnp_arr as $key => $value) {
				$arr_key = explode("_", $key);
				if(count($arr_key) == 2){
					$key1 = $arr_key[0];
					$key2 = $arr_key[1];
					if(!isset($min_ret[$key1])) $min_ret[$key1] = [];
					if(!isset($max_ret[$key1])) $max_ret[$key1] = [];
					$min_ret[$key1][$key2] = floatval( $value["min"] );
					$max_ret[$key1][$key2] = floatval( $value["max"] );
				}
			}
			$market = array();
			$market['prices_min'] = $min_ret;
			$market['prices_max'] = $max_ret;
			$market['timestamp'] = time();
			$market['total'] = 0;

			$place = array();
			$place['currency'] = 'JPY';
			$place['market'] = $market;

			$post_tanfuku->qnp = PrepMarketData($meeting_id, $event_number, 'QNP', $place, 'japan_jra');
		} else {
			$post_tanfuku->qnp = false;
		}

		if(isset($odds[6])){
			$exa_ret = [];
			$exa_arr = $odds[6];
			foreach ($exa_arr as $key => $value) {
				$arr_key = explode("_", $key);
				if(count($arr_key) == 2){
					$key1 = $arr_key[0];
					$key2 = $arr_key[1];
					if(!isset($exa_ret[$key1])) $exa_ret[$key1] = [];
					$exa_ret[$key1][$key2] = floatval( $value );
				}
			}

			$market = array();
			$market['prices'] = $exa_ret;
			$market['timestamp'] = time();
			$market['total'] = 0;
			$win = array();
			$win['currency'] = 'JPY';
			$win['market'] = $market;

			$post_tanfuku->exa = PrepMarketData($meeting_id, $event_number, 'EXA', $win, 'japan_jra');
		} else {
			$post_tanfuku->exa = false;
		}

		if(isset($odds[7])){
			$tro_ret = [];
			$tro_arr = $odds[7];
			foreach ($tro_arr as $key => $value) {
				$arr_key = explode("_", $key);
				if(count($arr_key) == 3){
					$key1 = $arr_key[0];
					$key2 = $arr_key[1];
					$key3 = $arr_key[2];
					if(!isset($tro_ret[$key1])) $tro_ret[$key1] = [];
					if(!isset($tro_ret[$key1][$key2])) $tro_ret[$key1][$key2] = [];
					$tro_ret[$key1][$key2][$key3] = floatval( $value );
				}
			}

			$market = array();
			$market['prices'] = $tro_ret;
			$market['timestamp'] = time();
			$market['total'] = 0;
			$win = array();
			$win['currency'] = 'JPY';
			$win['market'] = $market;

			$post_tanfuku->tro = PrepMarketData($meeting_id, $event_number, 'TRO', $win, 'japan_jra');
		} else {
			$post_tanfuku->tro = false;
		}

		if(isset($odds[8])){
			$tri_ret = [];
			$tri_arr = $odds[8];
			foreach ($tri_arr as $key => $value) {
				$arr_key = explode("_", $key);
				if(count($arr_key) == 3){
					$key1 = $arr_key[0];
					$key2 = $arr_key[1];
					$key3 = $arr_key[2];
					if(!isset($tri_ret[$key1])) $tri_ret[$key1] = [];
					if(!isset($tri_ret[$key1][$key2])) $tri_ret[$key1][$key2] = [];
					$tri_ret[$key1][$key2][$key3] = floatval( $value );
				}
			}

			$market = array();
			$market['prices'] = $tri_ret;
			$market['timestamp'] = time();
			$market['total'] = 0;
			$win = array();
			$win['currency'] = 'JPY';
			$win['market'] = $market;

			$post_tanfuku->tri = PrepMarketData($meeting_id, $event_number, 'TRI', $win, 'japan_jra');
		} else {
			$post_tanfuku->tri = false;
		}

		return $post_tanfuku;
	}

	function __get_race_odds_data_full($__race_id, $spec = false){

		$race_num = substr($__race_id, -2);
		$race_datas = __get_race_datas();
		$meeting_id = "";

		for($i=0; $i<count($race_datas); $i++){
			$track_obj = $race_datas[$i];
			for($j=0; $j<count($track_obj->races); $j++){
				if($track_obj->races[$j]->race_id == $__race_id){
					$meeting_id = $track_obj->meeting_id;
				}
			}
		}
		$event_number = intval(trim($race_num));

		if(__check_meeting_race_spec($meeting_id, $event_number, $spec)) return false;
		$send_check = __check_send_data_config($spec);
		$log_check = __check_log_data_config($spec);

		$post_data = __get_race_odds($__race_id);

		if(!($post_data->win)) return false;

		__log_and_debug_and_send_data($meeting_id, $event_number, $post_data, $log_check, $send_check, "netkeiba");
		
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
				$__race_id = $ret[$i]->races[$j]->race_id;
				__show_debug_info__("Start Checking JRA - Backup Odds Info ...");
				__show_debug_info__("Race ID: ".$__race_id);
				__get_race_odds_data_full($__race_id, $spec);
				__show_debug_info__("End Checking JRA - Backup Odds Info ...");
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
?>