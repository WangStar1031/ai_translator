<?php

	include('common_lib.php');

	$__proc_url = "https://keiba.rakuten.co.jp/odds/tanfuku/RACEID/";
	$__result_url = "https://keiba.rakuten.co.jp/race_performance/list/RACEID/";

	function __get_odds_track($__date = null, $proxy = "") {
		global $__proc_url;

		$ret = array();
		if(is_null($__date)) $__date = date("Y-m-d");
		$__url = $__proc_url.(date("Ymd", strtotime($__date)))."0000000000";
		$__result = __call_safe_url($__url, $proxy);
		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_tracks = $ret_html->find('.raceTrack li');
			foreach($arr_tracks as $info_track) {
				$track_html = str_get_html($info_track->innertext);
				if($track_html->find('a', 0)){
					$a_obj = $track_html->find('a', 0);
					$track_obj = new \stdClass;
					$track_obj->track_id = $info_track->class;
					$track_obj->track_name = $a_obj->innertext;
					$track_obj->track_race_id = __get_last_values($a_obj->href, "/");
					array_push($ret, $track_obj);
				}
			}
		}
		return $ret;
	}

	function __get_track_info($__race_id, $proxy = "") {
		global $__proc_url;

		$race_obj = new \stdClass;
		$race_obj->id = $__race_id;
		$ret = array();
		$__url = $__proc_url.$__race_id;
		$__result = __call_safe_url($__url, $proxy);

		$ret_html = str_get_html($__result);
		if($ret_html){
			$race_obj->title = $ret_html->find(".headline h2", 0)->innertext;

	  		$arr_races = $ret_html->find('tbody.raceState tr');
			foreach($arr_races as $info_race) {
				$race_html = str_get_html($info_race->innertext);
				if($race_html->find('.number .race', 0)){
					$track_obj = new \stdClass;
					$track_obj->id = __get_until_values($race_html->find('.number .race', 0)->innertext, "<span");
					$track_obj->race_id = __get_last_values($race_html->find('.raceName a', 0)->href, "/");
					$track_obj->name = $race_html->find('.raceName a', 0)->innertext;
					$track_obj->time = $race_html->find('td', 0)->innertext;
					$track_obj->distance = __get_last_values($race_html->find('td.distance', 0)->innertext, "</span>");
					$track_obj->count = $race_html->find('td', 3)->innertext;
					array_push($ret, $track_obj);
				}
			}
		}
		$race_obj->races = $ret;
		return $race_obj;
	}

	function __get_race_info($__race_id, $proxy = "") {
		global $__proc_url;

		$race_obj = new \stdClass;
		$race_obj->id = $__race_id;
		$ret = array();
		$__url = $__proc_url.$__race_id;
		$__result = __call_safe_url($__url, $proxy);

		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_races = $ret_html->find('#oddsField #wakuUmaBanJun .selectWrap tr');
			foreach($arr_races as $info_race) {
				$race_html = str_get_html($info_race->innertext);
				if($race_html->find('.number', 0)){
					$horse_obj = new \stdClass;
					$horse_obj->position = $race_html->find('.number', 0)->innertext;
					$horse_obj->number = $race_html->find('.number', 0)->innertext;
					if(!is_numeric($horse_obj->number)) $horse_obj->number = "除外";
					$detail_obj = new \stdClass;
					$detail_obj->katakana = $race_html->find('.horse a', 0)->innertext;
					$detail_obj->romaji = __kata2romaji($detail_obj->katakana);
					$horse_obj->horse = $detail_obj;
					$horse_obj->horseid = __get_last_values($race_html->find('.horse a', 0)->href, "=");
					$horse_obj->state = explode("<br>", $race_html->find('.state', 0)->innertext);
					$horse_obj->jockey = explode("<br>", $race_html->find('.jockey', 0)->innertext);
					$horse_obj->jockey[0] = __get_values($horse_obj->jockey[0], ">", "<");
					$horse_obj->jockey[1] = trim($horse_obj->jockey[1]);
					$horse_obj->jockey[2] = trim($horse_obj->jockey[2]);
					$horse_obj->weight = implode("", explode("<br>", $race_html->find('.weight', 0)->innertext));
					$horse_obj->weightDistance = explode("<br>", $race_html->find('.weightDistance', 0)->innertext);
					$horse_obj->weightDistance[0] = trim($horse_obj->weightDistance[0]);
					$horse_obj->oddsWin = ($race_html->find('.oddsWin span', 0))?$race_html->find('.oddsWin span', 0)->innertext:$race_html->find('.oddsWin', 0)->innertext;
					$horse_obj->oddsPlace = ($race_html->find('.oddsPlace span', 0))?$race_html->find('.oddsPlace span', 0)->innertext.(($race_html->find('.oddsPlace span', 1))?" - ".$race_html->find('.oddsPlace span', 1)->innertext:""):$race_html->find('.oddsPlace', 0)->innertext;
					$horse_obj->rank = $race_html->find('.rank', 0)->innertext;
					$horse_obj->graf = (__get_values($race_html->find('.graf .greenGraph', 0)->style, "width:","%")?__get_values($race_html->find('.graf .greenGraph', 0)->style, "width:","%"):"0")."%";

					array_push($ret, $horse_obj);
				}
			}
		}
		$race_obj->races = $ret;
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
						$ret->$__fld_name = $payout_obj;
					}
				}
			}
		}
		return $ret;
	}

	function __get_race_datas($proc_date = "") {
		$arr_meetings = __get_nar_meeting_ids();

		$now_time = GetCurrentJapanTime();
		$d = date("Y-m-d", strtotime($now_time));
		$file_name = date("Ymd", strtotime($now_time))."_keiba.json";

		if(($proc_date != "") && ($proc_date != date("Ymd", strtotime($now_time)))){
			$results = array();

			$file_name = "logs/backup/".$proc_date."_keiba.json";
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
				$track_info = __get_track_info($track_obj->track_race_id);
				$track_info->track_name = $track_obj->track_name;
	      $track_info->meeting_id = __get_nar_meeting_id($arr_meetings, substr($track_info->id, 8, 2));
	      $track_info->meeting_name = __get_nar_meeting_name(substr($track_info->id, 8, 2));
				array_push($results, $track_info);
			}
			
			file_put_contents($file_name, json_encode( $results ));
		}

		return $results;
	}

	function __get_race_results($__race_id, $proxy = "") {
		global $__result_url;

		$ret_obj = new \stdClass;

		$race_num = substr($__race_id, -2);
		$arr_meetings = __get_nar_meeting_ids();
		$meeting_id = __get_nar_meeting_id($arr_meetings, substr($__race_id, 8, 2));

		$ret = array();
		$__url = $__result_url.$__race_id;
		$__result = __call_safe_url($__url, $proxy);

		$__arr_repay = array();

		$__config_check = array( "馬単", "三連複", "三連単", "単勝", "複勝", "ワイド", "馬複" );
		$__config_pays = array( "馬単" => "EXA", "三連複" => "TRO", "三連単" => "TRI", "単勝" => "WIN", "複勝" => "PLC", "ワイド" => "QNP", "馬複" => "QNL" );

		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_races = $ret_html->find('#oddsField tbody.record tr');
			foreach($arr_races as $info_race) {
				$race_html = str_get_html($info_race->innertext);
				if($race_html->find('td.order', 0)){
			          $race_obj = new \stdClass;
			          $race_obj->meeting_id = intval($meeting_id);
			          $race_obj->event_number = intval(trim($race_num));
			          $race_obj->runner_number = trim($race_html->find('td.number', 0)->innertext);
			          $position_obj = new \stdClass;
			          $position_obj->finish_position = trim($race_html->find('td.order', 0)->innertext);
			          $race_obj->race_data = $position_obj;
					array_push($ret, $race_obj);
				}
			}

			$arr_pays = $ret_html->find('tbody.repay tr');
			foreach($arr_pays as $info_pay) {
				$pay_html = str_get_html($info_pay->innertext);
				if($pay_html->find('th', 0)){
					$th_1 = $pay_html->find('th', 0);
					if($th_1->innertext){
						if(in_array(trim($th_1->innertext), $__config_check)){
				          $pay_obj = new \stdClass;
				          $pay_obj->kind = $__config_pays[trim($th_1->innertext)];
				          $pay_obj->number = trim($pay_html->find('td.number', 0)?$pay_html->find('td.number', 0)->innertext:"");
				          $pay_obj->money = trim($pay_html->find('td.money', 0)?$pay_html->find('td.money', 0)->innertext:"");
						  array_push($__arr_repay, $pay_obj);
						}
					}
				}
				if($pay_html->find('th', 1)){
					$th_1 = $pay_html->find('th', 1);
					if($th_1->innertext){
						if(in_array(trim($th_1->innertext), $__config_check)){
				          $pay_obj = new \stdClass;
				          $pay_obj->kind = $__config_pays[trim($th_1->innertext)];
				          $pay_obj->number = trim($pay_html->find('td.number', 1)?$pay_html->find('td.number', 1)->innertext:"");
				          $pay_obj->money = trim($pay_html->find('td.money', 1)?$pay_html->find('td.money', 1)->innertext:"");
						  array_push($__arr_repay, $pay_obj);
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

		$repay_detail_obj = new \stdClass;
		$repay_detail_obj->japan_nar = $repay_detail_arr;
		$repay_obj->provider_results_data = $repay_detail_obj;

		$ret_obj->result = $ret;
		$ret_obj->repay = $repay_obj;

		return $ret_obj;
	}

	function __get_race_odds_init($__race_id, $__result = "") {
		global $__proc_url;

		$race_num = substr($__race_id, -2);
		$arr_meetings = __get_nar_meeting_ids();
		$meeting_id = __get_nar_meeting_id($arr_meetings, substr($__race_id, 8, 2));
		$meeting_id = intval($meeting_id);
		$event_number = intval(trim($race_num));

		$ret = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == "") {
			$__url = $__proc_url.$__race_id;
			$__result = __call_safe_url($__url);
		}

		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_races = $ret_html->find('#oddsField #wakuUmaBanJun .selectWrap tr');
			foreach($arr_races as $info_race) {
				$race_html = str_get_html($info_race->innertext);
				if($race_html->find('.number', 0)){
					$oddsWin = ($race_html->find('.oddsWin span', 0))?$race_html->find('.oddsWin span', 0)->innertext:$race_html->find('.oddsWin', 0)->innertext;
					if(!($race_html->find('.oddsPlace span', 0))) {
						$oddsPlace = $race_html->find('.oddsPlace', 0)->innertext;
						$oddsPlace_val = explode("-", $oddsPlace);
						$oddsPlace_min = $oddsPlace_val[0];
						if(count($oddsPlace_val)) $oddsPlace_max = $oddsPlace_val[1];
						else $oddsPlace_max = 0;
					} else {
						$oddsPlace_min = ($race_html->find('.oddsPlace span', 0))?$race_html->find('.oddsPlace span', 0)->innertext:0;
						$oddsPlace_max = ($race_html->find('.oddsPlace span', 1))?$race_html->find('.oddsPlace span', 1)->innertext:0;
					}
					
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
		global $__proc_url;

		$race_num = substr($__race_id, -2);
		$arr_meetings = __get_nar_meeting_ids();
		$meeting_id = __get_nar_meeting_id($arr_meetings, substr($__race_id, 8, 2));
		$meeting_id = intval($meeting_id);
		$event_number = intval(trim($race_num));

		$ret = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == ""){
			$__url = str_replace("tanfuku", "umafuku", $__proc_url).$__race_id;
			$__result = __call_safe_url($__url);
		}

		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_tables = $ret_html->find('div#wakuUmaBanJun tbody.oddsList', 0);
	  		if($arr_tables){
	  			$arr_races = str_get_html($arr_tables->innertext)->find("td");
	  			$arr_ths = str_get_html($arr_tables->innertext)->find("th");
	  			$pos_i = 0;
	  			foreach($arr_ths as $th_obj) {
	  				if($th_obj->colspan == 2) $pos_i++;
	  				else break;
	  			}
	  			$fld_name = "data-grouping";
				foreach($arr_races as $td_obj) {
					if($td_obj->colspan >= 2) continue;
					$group_number = $td_obj->$fld_name;
					if(str_get_html($td_obj)->find('span')){
						$odds_value = floatval( str_get_html($td_obj)->find('span', 0)->innertext );
					} else {
						$odds_value = floatval( $td_obj->innertext );
					}
					$col_number = $arr_ths[$pos_i]->innertext;
					if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
					else {
						$ret[$group_number] = [];
						$ret[$group_number][$col_number] = $odds_value;
					}
					$pos_i++;
					if(count($arr_ths) > $pos_i){
						if($arr_ths[$pos_i]->colspan == 2) $pos_i++;
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
		global $__proc_url;

		$race_num = substr($__race_id, -2);
		$arr_meetings = __get_nar_meeting_ids();
		$meeting_id = __get_nar_meeting_id($arr_meetings, substr($__race_id, 8, 2));
		$meeting_id = intval($meeting_id);
		$event_number = intval(trim($race_num));

		$ret = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == ""){
			$__url = str_replace("tanfuku", "umatan", $__proc_url).$__race_id;
			$__result = __call_safe_url($__url);
		}

		$ret_html = str_get_html($__result);
		$add_pos = 0;
		$min_pos = 0;
		if($ret_html){
	  		foreach ($ret_html->find('div#wakuUmaBanJun tbody.oddsList') as $arr_tables){
	  			$arr_races = str_get_html($arr_tables->innertext)->find("td");
	  			$arr_ths = str_get_html($arr_tables->innertext)->find("th");
	  			$pos_i = 0;
	  			foreach($arr_ths as $th_obj) {
	  				if($th_obj->colspan == 2) $pos_i++;
	  				else break;
	  			}
	  			$fld_name = "data-grouping";
				foreach($arr_races as $td_obj) {
					if($td_obj->colspan >= 2) continue;
					$group_number = $td_obj->$fld_name;
					if(str_get_html($td_obj)->find('span')){
						$odds_value = floatval( str_get_html($td_obj)->find('span', 0)->innertext );
					} else {
						$odds_value = floatval( $td_obj->innertext );
					}
					$col_number = $arr_ths[$pos_i]->innertext;
					if($td_obj->innertext != "-"){
						if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
						else {
							$ret[$group_number] = [];
							$ret[$group_number][$col_number] = $odds_value;
						}
					}
					$pos_i++;
					if(count($arr_ths) > $pos_i){
						if($arr_ths[$pos_i]->colspan == 2) $pos_i++;
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
		global $__proc_url;

		$race_num = substr($__race_id, -2);
		$arr_meetings = __get_nar_meeting_ids();
		$meeting_id = __get_nar_meeting_id($arr_meetings, substr($__race_id, 8, 2));
		$meeting_id = intval($meeting_id);
		$event_number = intval(trim($race_num));

		$ret = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == ""){
			$__url = str_replace("tanfuku", "wide", $__proc_url).$__race_id;
			$__result = __call_safe_url($__url);
		}

		$ret_html = str_get_html($__result);
		if($ret_html){
	  		$arr_tables = $ret_html->find('div#wakuUmaBanJun tbody.oddsList', 0);
	  		if($arr_tables){
	  			$arr_races = str_get_html($arr_tables->innertext)->find("td");
	  			$arr_ths = str_get_html($arr_tables->innertext)->find("th");
	  			$pos_i = 0;
	  			foreach($arr_ths as $th_obj) {
	  				if($th_obj->colspan == 2) $pos_i++;
	  				else break;
	  			}
	  			$fld_name = "data-grouping";
				foreach($arr_races as $td_obj) {
					if($td_obj->colspan >= 2) continue;
					$group_number = $td_obj->$fld_name;
					$td_html = $td_obj->innertext;
					$td_datas = explode('<span>-</span>', $td_html);
					$odds_value_min = 0;
					if(str_get_html($td_datas[0])->find('span')){
						$odds_value_min = floatval( str_get_html($td_datas[0])->find('span', 0)->innertext );
					} else {
						$odds_value_min = floatval( $td_datas[0] );
					}
					$odds_value_max = 0;
					if(count($td_datas) > 1){
						if(str_get_html($td_datas[1])->find('span')){
							$odds_value_max = floatval( str_get_html($td_datas[1])->find('span', 0)->innertext );
						} else {
							$odds_value_max = floatval( $td_datas[1] );
						}
					}
					$col_number = $arr_ths[$pos_i]->innertext;
					if(isset($prices_min[$group_number])) $prices_min[$group_number][$col_number] = $odds_value_min;
					else {
						$prices_min[$group_number] = [];
						$prices_min[$group_number][$col_number] = $odds_value_min;
					}
					if(isset($prices_max[$group_number])) $prices_max[$group_number][$col_number] = $odds_value_max;
					else {
						$prices_max[$group_number] = [];
						$prices_max[$group_number][$col_number] = $odds_value_max;
					}
					$pos_i++;
					if(count($arr_ths) > $pos_i){
						if($arr_ths[$pos_i]->colspan == 2) $pos_i++;
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
		global $__proc_url;

		$race_num = substr($__race_id, -2);
		$arr_meetings = __get_nar_meeting_ids();
		$meeting_id = __get_nar_meeting_id($arr_meetings, substr($__race_id, 8, 2));
		$meeting_id = intval($meeting_id);
		$event_number = intval(trim($race_num));

		$prices = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == ""){
			$__url = str_replace("tanfuku", "sanrentan", $__proc_url).$__race_id;
			$__result = __call_safe_url($__url);
		}

		$main_html = str_get_html($__result);

		foreach ($main_html->find('div#oddsField div.rateField div') as $div_obj) {
			$div_id = "";
			if($div_obj->id) $div_id = $div_obj->id;
			if(strlen($div_id) < 8) continue;
			if(substr($div_id, 0, 7) != "odds_1_") continue;

			$main_idx = substr($div_id, 7);
			$add_pos = 0;
			$min_pos = 0;
			$ret_html = str_get_html($div_obj->innertext);
			if($ret_html){
				$ret = array();
		  		foreach ($ret_html->find('table') as $arr_tables){
		  			$arr_races = str_get_html($arr_tables->innertext)->find("td");
		  			$arr_ths = str_get_html($arr_tables->innertext)->find("th");
		  			$pos_i = 0;
		  			foreach($arr_ths as $th_obj) {
		  				if($th_obj->colspan == 2) $pos_i++;
		  				else break;
		  			}
		  			$fld_name = "data-grouping";
					foreach($arr_races as $td_obj) {
						if($td_obj->colspan >= 2) continue;
						$group_number = $td_obj->$fld_name;
						if(str_get_html($td_obj)->find('span')){
							$odds_value = floatval( str_get_html($td_obj)->find('span', 0)->innertext );
						} else {
							$odds_value = floatval( $td_obj->innertext );
						}
						$col_number = $arr_ths[$pos_i]->innertext;
						if($td_obj->innertext != "-"){
							if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
							else {
								$ret[$group_number] = [];
								$ret[$group_number][$col_number] = $odds_value;
							}
						}
						$pos_i++;
						if(count($arr_ths) > $pos_i){
							if($arr_ths[$pos_i]->colspan == 2) $pos_i++;
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
		global $__proc_url;

		$race_num = substr($__race_id, -2);
		$arr_meetings = __get_nar_meeting_ids();
		$meeting_id = __get_nar_meeting_id($arr_meetings, substr($__race_id, 8, 2));
		$meeting_id = intval($meeting_id);
		$event_number = intval(trim($race_num));

		$prices = array();
		$prices_min = array();
		$prices_max = array();

		if($__result == ""){
			$__url = str_replace("tanfuku", "sanrenfuku", $__proc_url).$__race_id;
			$__result = __call_safe_url($__url);
		}

		$main_html = str_get_html($__result);

		foreach ($main_html->find('div#oddsField div.rateField div') as $div_obj) {
			$div_id = "";
			if($div_obj->id) $div_id = $div_obj->id;
			if(strlen($div_id) < 6) continue;
			if(substr($div_id, 0, 5) != "odds_") continue;

			$main_idx = substr($div_id, 5);
			$add_pos = 0;
			$min_pos = 0;
			$ret_html = str_get_html($div_obj->innertext);
			if($ret_html){
				$ret = array();

	  			$arr_races = $ret_html->find("td");
	  			$arr_ths = $ret_html->find("th");
	  			$pos_i = 0;
	  			foreach($arr_ths as $th_obj) {
	  				if($th_obj->colspan == 2) $pos_i++;
	  				else break;
	  			}
	  			$fld_name = "data-grouping";
				foreach($arr_races as $td_obj) {
					if($td_obj->colspan >= 2) continue;
					$group_number = $td_obj->$fld_name;
					if(str_get_html($td_obj)->find('span')){
						$odds_value = floatval( str_get_html($td_obj)->find('span', 0)->innertext );
					} else {
						$odds_value = floatval( $td_obj->innertext );
					}
					$col_number = $arr_ths[$pos_i]->innertext;
					if($td_obj->innertext != "-"){
						if(isset($ret[$group_number])) $ret[$group_number][$col_number] = $odds_value;
						else {
							$ret[$group_number] = [];
							$ret[$group_number][$col_number] = $odds_value;
						}
					}
					$pos_i++;
					if(count($arr_ths) > $pos_i){
						if($arr_ths[$pos_i]->colspan == 2) $pos_i++;
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
				$race_obj->race_id = $ret[$i]->races[$j]->race_id;
				$result[] = $race_obj;
			}
		}
		echo json_encode($result);
	}

	function __get_race_odds_overall($spec = false){
		global $__proc_url;

		$max_time_diff = 3600 * 10;
		if($spec){
			if(isset($spec->duration)) $max_time_diff = $spec->duration * 3600;
		}

		$send_check = __check_send_data_config($spec);
		$log_check = __check_log_data_config($spec);

		$now_time = GetCurrentJapanTime();
		$arr_lists = [];
		$arr_races = [];
		$ret = __get_race_datas();
 
		$num_mul = 6;

		for($i=0; $i<count($ret); $i++){
			if($ret[$i]->meeting_id == 0) continue;
			for($j=0; $j<count($ret[$i]->races); $j++){
				$race_time = $ret[$i]->races[$j]->time;
				$race_time = date("Y-m-d", strtotime($now_time)).$race_time.":00";
				$time_diff = strtotime($race_time) - strtotime($now_time);
				if(($time_diff > $max_time_diff) || ($time_diff < -300)) continue;
				$__race_id = $ret[$i]->races[$j]->race_id;
				$arr_lists[] = $__proc_url.$__race_id;
				$arr_lists[] = str_replace("tanfuku", "umafuku", $__proc_url).$__race_id;
				$arr_lists[] = str_replace("tanfuku", "umatan", $__proc_url).$__race_id;
				$arr_lists[] = str_replace("tanfuku", "wide", $__proc_url).$__race_id;
				$arr_lists[] = str_replace("tanfuku", "sanrentan", $__proc_url).$__race_id;
				$arr_lists[] = str_replace("tanfuku", "sanrenfuku", $__proc_url).$__race_id;
				$arr_races[] = $__race_id;
			}
		}

		$result = multiRequest($arr_lists);
		
		$post_result = array();
		for($pos_race = 0; $pos_race < count($result)/$num_mul; $pos_race++){
			$__race_id = $arr_races[$pos_race];
			$race_num = substr($__race_id, -2);
			$arr_meetings = __get_nar_meeting_ids();
			$meeting_id = __get_nar_meeting_id($arr_meetings, substr($__race_id, 8, 2));
			$meeting_id = intval($meeting_id);
			$event_number = intval(trim($race_num));

			$post_data = __get_race_odds_init($__race_id, $result[$num_mul * $pos_race]);
			$post_data->qnl = __get_race_odds_qnl($__race_id, $result[$num_mul * $pos_race + 1]);
			$post_data->exa = __get_race_odds_exa($__race_id, $result[$num_mul * $pos_race + 2]);
			$post_data->qnp = __get_race_odds_wide($__race_id, $result[$num_mul * $pos_race + 3]);
			$post_data->tri = __get_race_odds_tri($__race_id, $result[$num_mul * $pos_race + 4]);
			$post_data->tro = __get_race_odds_tro($__race_id, $result[$num_mul * $pos_race + 5]);
			
			$pos_race++;

			$post_data->comp_win = __log_json_result("rakuten", "win", $meeting_id, $event_number, $post_data->win);
			$post_data->comp_plc = __log_json_result("rakuten", "plc", $meeting_id, $event_number, $post_data->plc);
			$post_data->comp_qnl = __log_json_result("rakuten", "qnl", $meeting_id, $event_number, $post_data->qnl);
			$post_data->comp_exa = __log_json_result("rakuten", "exa", $meeting_id, $event_number, $post_data->exa);
			$post_data->comp_tro = __log_json_result("rakuten", "tro", $meeting_id, $event_number, $post_data->tro);
			$post_data->comp_tri = __log_json_result("rakuten", "tri", $meeting_id, $event_number, $post_data->tri);
			$post_data->comp_qnp = __log_json_result("rakuten", "qnp", $meeting_id, $event_number, $post_data->qnp);

			$post_result[] = $post_data;
		}

		$send_datas = [];
		$send_datas2 = [];
		for($i=0; $i<count($post_result); $i++){
			if(($send_check["win"] == 1) && $post_result[$i]->comp_win) $send_datas[] = $post_result[$i]->win;
		}
		if(count($send_datas) > 0)
			__api_send_multiple__("https://staging.dw.xtradeiom.com/api/markets/", "POST", $send_datas);
		for($i=0; $i<count($post_result); $i++){
			if(($send_check["plc"] == 1) && $post_result[$i]->comp_plc) $send_datas2[] = $post_result[$i]->plc;
			if(($send_check["exa"] == 1) && $post_result[$i]->comp_exa) $send_datas2[] = $post_result[$i]->exa;
			if(($send_check["qnl"] == 1) && $post_result[$i]->comp_qnl) $send_datas2[] = $post_result[$i]->qnl;
			if(($send_check["qnp"] == 1) && $post_result[$i]->comp_qnp) $send_datas2[] = $post_result[$i]->qnp;
			if(($send_check["tri"] == 1) && $post_result[$i]->comp_tri) $send_datas2[] = $post_result[$i]->tri;
			if(($send_check["tro"] == 1) && $post_result[$i]->comp_tro) $send_datas2[] = $post_result[$i]->tro;
		}
		if(count($send_datas2) > 0)
			__api_send_multiple__("https://staging.dw.xtradeiom.com/api/markets/", "POST", $send_datas2);
		return $post_result;
	}

	function __get_race_odds_overall2($spec = false){
	  while (true) {
	    __show_debug_info__("Checking Race Odds ...");
	    __get_race_odds_overall($spec);
	    sleep(60);
	  }
	}

	function __get_race_fp_overall($spec = false){
		$ret = __get_race_datas();
		$arr_races = [];
		for($i=0; $i<count($ret); $i++){
			if($ret[$i]->meeting_id == 0) continue;
			for($j=0; $j<count($ret[$i]->races); $j++){
				$obj_race = new \stdClass;
				$obj_race->time = $ret[$i]->races[$j]->time;
				$obj_race->race_id = $ret[$i]->races[$j]->race_id;
				$obj_race->first = true;
				$obj_race->final = false;
				$arr_races[] = $obj_race;
			}
		}
		$check_end = true;
		while ($check_end) {
			sleep(5);
			__show_debug_info__("Start Checking NAR - RAKUTEN Final Positions...");
			$check_end = false;
			for($i=0; $i<count($arr_races); $i++){
				$race_time = $arr_races[$i]->time;
				$now_time = GetCurrentJapanTime();
				$race_time = date("Y-m-d", strtotime($now_time)).$race_time.":00";
				$time_diff = strtotime($race_time) - strtotime($now_time);
				if($time_diff > 0) {
					$check_end = true;
					continue;
				}
				if($arr_races[$i]->final) continue;
				$check_race = __get_race_status($arr_races[$i]->race_id, $arr_races[$i]->first);

				__show_debug_info__(json_encode($check_race));

				if($check_race->check_fp5) $arr_races[$i]->first = true;
				if($check_race->check_final)
					$arr_races[$i]->final = true;
				else
					$check_end = true;
			}
			__show_debug_info__("End Checking NAR - RAKUTEN Final Positions...");
		}
	}
?>