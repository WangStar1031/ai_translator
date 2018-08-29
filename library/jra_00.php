<?php

include('common_lib.php');

function __get_page_data_by_guzzle( $url ){
  $body = "";
  try {
    $client = new GuzzleHttp\Client();
    $response = $client->request('GET', $url);
    $body = $response->getBody();
    $body = iconv("SJIS", "UTF-8", $body);
  } catch (GuzzleHttp\Exception\RequestException $e) {
    return "<html></html>";
  }
  return $body;
}

function __get_home_data(){
  return __get_page_data_by_guzzle("http://www.jra.go.jp/");
}

function __get_news_week_cname(){
  $str_html = __get_home_data();
  $menu_html = str_get_html($str_html);
  
  foreach($menu_html->find('a') as $menu_a) {
    if(trim($menu_a->innertext) == "今週の開催お知らせ"){
        $menu_cname = __get_values($menu_a->onclick, ",'", "')");
        return $menu_cname;
    }
  }
  return "";  
}

function __get_odds_cname(){
  $str_html = __get_home_data();
  $menu_html = str_get_html($str_html);
  
  foreach($menu_html->find('a') as $menu_a) {
    if(trim($menu_a->innertext) == "オッズ"){      
        $menu_cname = __get_values($menu_a->onclick, ",'", "')");
        return $menu_cname;
    }
  }
  return "";
}

function __get_result_cname(){
  $str_html = __get_home_data();
  $menu_html = str_get_html($str_html);
  
  foreach($menu_html->find('a') as $menu_a) {
    if(trim($menu_a->innertext) == "レース結果"){      
        $menu_cname = __get_values($menu_a->onclick, ",'", "')");
        return $menu_cname;
    }
  }
  return "";
}

function __get_races_cname(){
  $str_html = __get_home_data();
  $menu_html = str_get_html($str_html);
  
  foreach($menu_html->find('a') as $menu_a) {
    if(trim($menu_a->innertext) == "出馬表"){      
        $menu_cname = __get_values($menu_a->onclick, ",'", "')");
        return $menu_cname;
    }
  }
  return "";
}

function __get_1r_cname($meeting_id, $menu_cname) {
  if($menu_cname){
    $str_html = __get_race_data($menu_cname);
    $menu_html = str_get_html($str_html);
    $menu_a = $menu_html->find('td.racekekkaCol a', 0);
    if($menu_a){
      $menu_cname = __get_values($menu_a->onclick, ",'", "')");
      if($menu_cname) file_put_contents($meeting_id.".cname", $menu_cname);
    }
  }
}

function __get_time_cname($meeting_id, $menu_cname) {
  $arr_changed = array();
 if($menu_cname){
    $str_html = __get_race_data($menu_cname);
    $menu_html = str_get_html($str_html);
    $menu_trs = $menu_html->find('tr');
    foreach ($menu_trs as $menu_tr) {
      $menu_tr_html = str_get_html($menu_tr);
      if($menu_tr_html->find(".hassouColChanged", 0)){
        $time_change = new \stdClass;
        $time_change->event_number = $menu_tr_html->find("td a img.btnRaceNumberImage", 0)->alt;
        $time_change->time = $menu_tr_html->find("td.hassouColChanged strong", 0)->innertext;
        array_push($arr_changed, $time_change);
      }
    }
  } 
  return $arr_changed;
}

function __get_today_result(){
  $arr_schedule = __get_race_schedule();
  $check_cname = true;
  for($i=0; $i<count($arr_schedule); $i++){
    $meeting_id = $arr_schedule[$i]->meeting_id;
    $meeting_name = $arr_schedule[$i]->meeting_name;
    if(file_exists($meeting_id.".cname")) continue;
    $check_cname = false;
  }

  if($check_cname) return;

  $menu_cname = __get_result_cname();
  if($menu_cname){
    $str_html = __get_race_data($menu_cname);
    $menu_html = str_get_html($str_html);

    for($i=0; $i<count($arr_schedule); $i++){
      $meeting_id = $arr_schedule[$i]->meeting_id;
      $meeting_name = $arr_schedule[$i]->meeting_name;
      if(file_exists($meeting_id.".cname")) continue;

      foreach($menu_html->find('.kaisaiBtn a') as $menu_a) {
        $menu_desc = trim($menu_a->innertext);
        if(__get_after_values($menu_desc, "&nbsp;") == $meeting_name){
          $menu_cname = __get_values($menu_a->onclick, ",'", "')");
          __get_1r_cname($meeting_id, $menu_cname);
        }
      }
    }
  }  
}

function __get_ingi_odds($__cname) 
{
  $str_html = __get_race_data($__cname, "O");
  if($str_html){
    $str_html = __get_values($str_html, 'Btn unBtn">', '</td>');
    $menu_html = str_get_html($str_html);
    if($menu_html){
      $a_obj = $menu_html->find('a', 0);
      if($a_obj) return __get_values($a_obj->onclick, ",'", "')");
    }
  }
  return "";
}

function __get_today_odds(){
  $arr_schedule = __get_race_schedule();
  $check_cname = true;
  for($i=0; $i<count($arr_schedule); $i++){
    $meeting_id = $arr_schedule[$i]->meeting_id;
    $meeting_name = $arr_schedule[$i]->meeting_name;
    if(file_exists($meeting_id.".odds.cname")) continue;
    $check_cname = false;
  }

  if($check_cname) return;

  $menu_cname = __get_odds_cname();
  if($menu_cname){
    $str_html = __get_race_data($menu_cname, "O");
    $menu_html = str_get_html($str_html);

    for($i=0; $i<count($arr_schedule); $i++){
      $meeting_id = $arr_schedule[$i]->meeting_id;
      $meeting_name = $arr_schedule[$i]->meeting_name;
      if(file_exists($meeting_id.".odds.cname")) continue;

      $arr_race_info = $arr_schedule[$i]->races;

      foreach($menu_html->find('.kaisaiBtn a') as $menu_a) {
        $menu_desc = trim($menu_a->innertext);
        if(__get_after_values($menu_desc, ">") == $meeting_name){
          $arr_odds = [];
          $menu_cname = __get_values($menu_a->onclick, ",'", "')");
          $str_html = __get_race_data($menu_cname, "O");
          $arr_races = str_get_html($str_html)->find('.raceList2 tr');
          foreach ($arr_races as $race_obj) {
            $race_obj_html = str_get_html($race_obj->innertext);
            if($race_obj_html->find('td.raceNo', 0)){
              $race_number = intval( $race_obj_html->find('td.raceNo a img', 0)->alt );
              $odds_obj = new \stdClass;
              $odds_obj->meeting_id = $meeting_id;
              $odds_obj->race_number = $race_number;
              $race_time = "";
              foreach ($arr_race_info as $race_info) {
                if($race_info->race_id == $race_number)
                  $race_time = $race_info->time;
              }
              $odds_obj->time = $race_time;
              $WIN = ""; 
              $QNL = ""; 
              $QNP = ""; 
              $EXA = ""; 
              $TRO = ""; 
              $TRI = ""; 
              $arr_a = $race_obj_html->find('td a');
              foreach ($arr_a as $a_obj) {
                if(strpos($a_obj->innertext, "単勝複勝") !== false) {
                  $WIN_ORI = __get_values($a_obj->onclick, ",'", "')");
                  $WIN = __get_ingi_odds($WIN_ORI);
                }
                if(strpos($a_obj->innertext, "馬連") !== false) $QNL = __get_ingi_odds(__get_values($a_obj->onclick, ",'", "')"));
                if(strpos($a_obj->innertext, "ワイド") !== false) $QNP = __get_ingi_odds(__get_values($a_obj->onclick, ",'", "')"));
                if(strpos($a_obj->innertext, "馬単") !== false) $EXA = __get_ingi_odds(__get_values($a_obj->onclick, ",'", "')"));
                if(strpos($a_obj->innertext, "３連複") !== false) $TRO = __get_ingi_odds(__get_values($a_obj->onclick, ",'", "')"));
                if(strpos($a_obj->innertext, "３連単") !== false) $TRI = __get_ingi_odds(__get_values($a_obj->onclick, ",'", "')"));
              }
              $odds_obj->WIN_ORI = $WIN_ORI;
              $odds_obj->WIN = $WIN;
              $odds_obj->QNL = $QNL;
              $odds_obj->QNP = $QNP;
              $odds_obj->EXA = $EXA;
              $odds_obj->TRO = $TRO;
              $odds_obj->TRI = $TRI;
              $arr_odds[] = $odds_obj;
            }            
          }
          file_put_contents($meeting_id.".odds.cname", json_encode($arr_odds));
        }
      }
    }
  }  
}

function __get_schedule_change(){
  $now_time = GetCurrentJapanTime();
  $file_name = date("Ymd", strtotime($now_time));

  $arr_schedule = __get_race_schedule();
  $menu_cname = __get_races_cname();
  $arr_result = array();
  $arr_meetings = __get_meeting_ids();
  $str_html = __get_race_data($menu_cname, "D");

  $menu_html = str_get_html($str_html);
  
  for($i=0; $i<count($arr_schedule); $i++){
    $meeting_id = $arr_schedule[$i]->meeting_id;
    $meeting_name = $arr_schedule[$i]->meeting_name;
    $arr_schedule[$i]->confirmed = 0;

    foreach($menu_html->find('.kaisaiBtn a') as $menu_a) {
      $menu_desc = trim($menu_a->innertext);
      if(__get_after_values($menu_desc, "&nbsp;") == $meeting_name){
        $arr_schedule[$i]->confirmed = 1;
        $menu_cname = __get_values($menu_a->onclick, ",'", "')");
        $change_obj = new \stdClass;
        $change_obj->races = __get_time_cname($meeting_id, $menu_cname);
        $change_obj->meeting_id = $meeting_id;
        array_push($arr_result, $change_obj);
        for($j=0; $j<count($change_obj->races); $j++){
         $time_diff = $change_obj->races[$j]->time;
         $event_number = $change_obj->races[$j]->event_number;
         for($k=0; $k<count($arr_schedule[$i]->races); $k++){
          if($arr_schedule[$i]->races[$k]->id == intval($event_number)){
            $arr_schedule[$i]->races[$k]->time = $time_diff;
            $arr_schedule[$i]->races[$k]->changed = 1;
          }
         }
        }
      }
    }
  }

  for($i=count($arr_schedule)-1; $i>=0; $i--){
    if($arr_schedule[$i]->confirmed == 0) {
      unset($arr_schedule[$i]);
    }
  }

  file_put_contents($file_name."_jra.json", json_encode($arr_schedule));

  if(file_exists($file_name."_netkeiba.json")){
    $arr_schedule = json_decode(file_get_contents($file_name."_netkeiba.json"));
    for($i=0; $i<count($arr_schedule); $i++){
      $meeting_id = $arr_schedule[$i]->meeting_id;
      $meeting_name = $arr_schedule[$i]->meeting_name;
      for($jj=0; $jj<count($arr_result); $jj++){
        $change_obj = $arr_result[$jj];
        if($meeting_id != $change_obj->meeting_id) continue;
        for($j=0; $j<count($change_obj->races); $j++){
         $time_diff = $change_obj->races[$j]->time;
         $event_number = $change_obj->races[$j]->event_number;

         for($k=0; $k<count($arr_schedule[$i]->races); $k++){
          if($arr_schedule[$i]->races[$k]->id == intval($event_number)){
            $arr_schedule[$i]->races[$k]->time = $time_diff;
            $arr_schedule[$i]->races[$k]->changed = 1;
          }
         }
        }
      }
    } 

    file_put_contents($file_name."_netkeiba.json", json_encode($arr_schedule));
  } 
  file_put_contents($file_name.".time.change.json", json_encode($arr_result));
  return $arr_result;
}

function __get_schedule_data( $date_str = "" ){
  if($date_str == "") {
    $now_time = GetCurrentJapanTime();
    $date_str = date("Y/n/md", strtotime($now_time));
  }
  return __get_page_data_by_guzzle( "http://www.jra.go.jp/keiba/calendar/".$date_str.".html" );
}

function __check_meeting_exists($__arr_meetings, $__meeting_id) {
  for($i=0; $i<count($__arr_meetings); $i++){
    if($__arr_meetings[$i]->meeting_id == $__meeting_id) return false;
  }
  return true;
}

function __get_race_schedule($proc_date = ""){

  $arr_result = array();

  $now_time = GetCurrentJapanTime();
  $file_name = date("Ymd", strtotime($now_time))."_jra.json";

  if(($proc_date != "") && ($proc_date != date("Ymd", strtotime($now_time)))){
    $results = array();

    $file_name = "logs/backup/".$proc_date."_jra.json";
    if(file_exists($file_name))
      return json_decode(@file_get_contents($file_name));

    return $results;
  }

  if(file_exists($file_name)){
    return json_decode(file_get_contents($file_name));
  } 

  $arr_meetings = __get_meeting_ids();
  $str_html = __get_schedule_data();
  $race_name = "";
  
  $race_html = str_get_html($str_html);
  foreach($race_html->find('table#rece-data td') as $race_table) {
    $race_table_string = str_get_html($race_table->innertext);
    if($race_table_string->find('.cal-racec-cell')){
      $race_name = $race_table_string->find('.cal-racec-cell', 0)->innertext;
      foreach($race_table_string->find('table.race-detail') as $race_table) {
        $race_table_string = str_get_html($race_table->innertext);
          foreach($race_table_string->find('tr') as $race_row) { 
            $race_row_string = str_get_html($race_row->innertext);
            if(!($race_row_string)) continue;
            if(!($race_row_string->find('th', 0))) continue;
            $race_obj = new \stdClass;
            $race_obj->race_num = $race_row_string->find('th', 0)->innertext;
            $race_obj->race_name = $race_name;
            $race_obj->race_time = $race_row_string->find('td.race-time', 0)->innertext;
            $race_obj->race_time = str_replace("時", ":", $race_obj->race_time);
            $race_obj->race_time = str_replace("分", ":00", $race_obj->race_time);
            $race_obj->race_time = date("H:i", strtotime($race_obj->race_time));
            $race_obj->now_time = date("H:i:s", strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9+10);
            $race_obj->diff_time = date("H:i:s", strtotime($race_obj->race_time) - (strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9+10));

            $send_obj = new \stdClass;
            $send_obj->meeting_id = __get_meeting_id($arr_meetings, $race_name);
            $send_obj->meeting_name = $race_name;
            $send_obj->venue_info = __get_jra_venue_info($send_obj->meeting_id);
            $send_obj->races = array();

            if(__check_meeting_exists($arr_result, $send_obj->meeting_id)) 
              array_push($arr_result, $send_obj);

            $race_objs = new \stdClass;
            $race_objs->race_id =  intval(str_replace("R", "", $race_obj->race_num));
            $race_objs->id =  $race_objs->race_id;
            $race_objs->time = $race_obj->race_time;
            $races = $arr_result[count($arr_result) - 1]->races;
            array_push($races, $race_objs);
            $arr_result[count($arr_result) - 1]->races = $races;
          }
      }
    }
  }

  file_put_contents($file_name, json_encode($arr_result));

  return $arr_result;
}


function __get_race_data($__id, $__pre = "S"){
  $body = "";
  try{
    $client = new GuzzleHttp\Client();
    $response = $client->request('POST', "http://www.jra.go.jp/JRADB/access".$__pre.".html", [ 'form_params' => [ 'cname' => $__id ] ]);
    $body = $response->getBody();
    $body = iconv("SJIS", "UTF-8", $body);
  } catch (GuzzleHttp\Exception\RequestException $e) {
    return "<html></html>";
  }
  return $body;
}

function __get_race_results($race_id){
  $ret = new \stdClass;

  $ret->result = __get_race_result(substr($race_id, 0, 6), substr($race_id, 6));
  $ret->repay = __get_race_payouts(substr($race_id, 0, 6), substr($race_id, 6));
  return $ret;
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

function __get_race_payouts($meeting_id, $race_num){
  $ret_obj = new \stdClass;
  $__arr_repay = array();
  $__config_check = array( "馬単", "３連複", "３連単", "単勝", "複勝", "ワイド", "馬連" );
  $__config_pays = array( "馬単" => "EXA", "３連複" => "TRO", "３連単" => "TRI", "単勝" => "WIN", "複勝" => "PLC", "ワイド" => "QNP", "馬連" => "QNL" );

  $str_html = @file_get_contents($meeting_id.".result");
  if($str_html == "") return $ret_obj;
  $race_html = str_get_html($str_html);
  $pay_key = "test";

  foreach ($race_html->find(".haraimodoshiOutDiv tr") as $pay_tr) {
    if(str_get_html($pay_tr->innertext)->find("th", 0)){
      $pay_obj = new \stdClass;
      $pay_key = trim(str_get_html($pay_tr->innertext)->find("th", 0)->innertext);
      if(isset($__config_pays[$pay_key])){
        $pay_obj->kind = $__config_pays[$pay_key];
        $pay_obj->number = trim(str_get_html($pay_tr->innertext)->find("td", 0)->innertext);;
        $pay_obj->money = trim(str_get_html($pay_tr->innertext)->find("td", 1)->innertext);;
        array_push($__arr_repay, $pay_obj); 
      }
    } else {
      $pay_obj = new \stdClass;
      if(isset($__config_pays[$pay_key])){
        $pay_obj->kind = $__config_pays[$pay_key];
        $pay_obj->number = trim(str_get_html($pay_tr->innertext)->find("td", 0)->innertext);;
        $pay_obj->money = trim(str_get_html($pay_tr->innertext)->find("td", 1)->innertext);;
        array_push($__arr_repay, $pay_obj); 
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
  $repay_detail_obj->japan_jra = $repay_detail_arr;
  $repay_obj->provider_results_data = $repay_detail_obj;

  return $repay_obj;
}

function __get_race_result($meeting_id, $race_number, $horse_add = false){
  $last_cname = "";
  $__id = @file_get_contents($meeting_id.".cname");
  if($__id == "") {
    __get_today_result();
    return array();
  }
  /*
  $arr_skip = array();
  if(file_exists("skip_jra")){
    $arr_skip = json_decode(file_get_contents("skip_jra"));
  }
  */
  $arr_result = array();
  $race_num = substr($__id, 19, 2);
  $str_html = __get_race_data($__id);
  file_put_contents($meeting_id.".result", $str_html);
  $race_html = str_get_html($str_html);
  
  $check_next = false;
  $arr_cname = array();
  $td_count = count($race_html->find('.raceSelect td')) / 2;
  $pos_race = 0;
  $select_pos = 0;
  $disable_pos = 100;

  foreach($race_html->find('.raceSelect td') as $race_td) {
    if($pos_race >= $td_count) break;
    $pos_race++;
    if($race_td->class == "select"){
      array_push($arr_cname, "select");
      $race_row_string = str_get_html($race_td->innertext);
      $select_pos = __get_until_values($race_row_string->find('img', 0)->alt, 'レース');
      $check_next = true;
    } else if($race_td->class == "btnspace"){
      $check_next = false;
      if($pos_race < $disable_pos) $disable_pos = $pos_race;
    //  array_push($arr_cname, "space");
    } else {
      $race_row_string = str_get_html($race_td->innertext);
      if($race_row_string->find('a', 0)){
        array_push($arr_cname, __get_values($race_row_string->find('a', 0)->onclick, ",'", "');"));
        if($check_next) $last_cname = $arr_cname[count($arr_cname) - 1];
      } else {
        array_push($arr_cname, "exception");
      }
      $check_next = false;      
    }
  }

  /*
  for($i=0; $i<count($arr_skip); $i++){
    if(($arr_skip[$i]->meeting_id == $meeting_id) && ($arr_skip[$i]->race_number < $race_number)){
      $disable_pos++;
      $select_pos++;
    }
  }
  */

  if($last_cname) file_put_contents($meeting_id.".cname", $last_cname);
  if($select_pos != $race_number){
    if($disable_pos <= $race_number) return array();
    if($select_pos < $race_number) return __get_race_result($meeting_id, $race_number, $horse_add);
    else if($select_pos > $race_number) return array();
  }

  foreach($race_html->find('table.mainList') as $race_table) {
    $race_table_string = str_get_html($race_table->innertext);
      foreach($race_table_string->find('tr') as $race_row) { 
        $race_row_string = str_get_html($race_row->innertext);
        if($race_row_string->find('td.chakuCol', 0)){
          $chakuCol = $race_row_string->find('td.chakuCol', 0)->innertext;
          $umabanCol = $race_row_string->find('td.umabanCol', 0)->innertext;
          $race_obj = new \stdClass;
          $race_obj->meeting_id = intval($meeting_id);
          $race_obj->event_number = intval(trim($race_num));
          $race_obj->runner_number = trim($umabanCol);
          if($horse_add) {
            $umameiCol = $race_row_string->find('td.umameiCol a', 0)->innertext;
            $race_obj->runner_name = trim($umameiCol);
          }
          $position_obj = new \stdClass;
          $position_obj->finish_position = trim($chakuCol);
          $race_obj->race_data = $position_obj;
          array_push($arr_result, $race_obj);
        }
      }
  }

  return $arr_result;
}

function get_odds_win_race($__cname, $meeting_id, $event_number)
{
  $str_html = __get_race_data($__cname, "O");
  $menu_html = str_get_html($str_html);
  $horse_arr = $menu_html->find("div.ozTanfukuTable tr");
  $win_ret = array();
  $plc_min_ret = array();
  $plc_max_ret = array();

  foreach ($horse_arr as $horse_obj) {
    $horse_obj_html = str_get_html($horse_obj->innertext);
    if($horse_obj_html->find('th.umaban', 0)){
      $win_obj = new \stdClass;
      $win_obj->horse_number = $horse_obj_html->find('th.umaban', 0)->innertext;
      $win_obj->price = trim($horse_obj_html->find('td.oztan', 0)->innertext);
      $win_obj->minPrice = trim($horse_obj_html->find('td.fukuMin', 0)->innertext);
      $win_obj->maxPrice = trim($horse_obj_html->find('td.fukuMax', 0)->innertext);
      $win_ret[$win_obj->horse_number] = floatval( $win_obj->price );
      $plc_min_ret[$win_obj->horse_number] = floatval( $win_obj->minPrice );
      $plc_max_ret[$win_obj->horse_number] = floatval( $win_obj->maxPrice );
    }
  }
  $win_ret = patch_array($win_ret);
  $plc_min_ret = patch_array($plc_min_ret);
  $plc_max_ret = patch_array($plc_max_ret);
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

  $post_tanfuku->win = PrepMarketData($meeting_id, $event_number, 'WIN', $win, 'japan_jra');

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

  $post_tanfuku->plc = PrepMarketData($meeting_id, $event_number, 'PLC', $place, 'japan_jra');

  return $post_tanfuku;
}

function get_odds_qnl_race($__cname, $meeting_id, $event_number)
{
  $str_html = __get_race_data($__cname, "O");
  $menu_html = str_get_html($str_html);
  $horse_arr = $menu_html->find("div.ozNinkiINTable tr");
  $prices = array();

  foreach ($horse_arr as $horse_obj) {
    $horse_obj_html = str_get_html($horse_obj->innertext);
    if($horse_obj_html->find('th.thninki', 0)){
      $group_info = trim($horse_obj_html->find('th.thkumi', 0)->innertext);
      $group_value = trim($horse_obj_html->find('td.tdoz', 0)->innertext);
      $arr_group = explode("-", $group_info);
      if(count($arr_group) == 2){
        $key1 = $arr_group[0];
        $key2 = $arr_group[1];
        if(!isset($prices[$key1])) $prices[$key1] = [];
        $prices[$key1][$key2] = floatval( $group_value );
      }
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

  return PrepMarketData($meeting_id, $event_number, 'QNL', $win, 'japan_jra');
}

function get_odds_qnp_race($__cname, $meeting_id, $event_number)
{
  $str_html = __get_race_data($__cname, "O");
  $menu_html = str_get_html($str_html);
  $horse_arr = $menu_html->find("div.ozNinkiINTable tr");
  $prices = array();
  $prices2 = array();

  foreach ($horse_arr as $horse_obj) {
    $horse_obj_html = str_get_html($horse_obj->innertext);
    if($horse_obj_html->find('th.thninki', 0)){
      if(($horse_obj_html->find('th.thkumi', 0)) && ($horse_obj_html->find('td.wideMin', 0)) && ($horse_obj_html->find('td.wideMax', 0))){
        $group_info = trim($horse_obj_html->find('th.thkumi', 0)->innertext);
        $group_value = trim($horse_obj_html->find('td.wideMin', 0)->innertext);
        $group_value2 = trim($horse_obj_html->find('td.wideMax', 0)->innertext);
        $arr_group = explode("-", $group_info);
        if(count($arr_group) == 2){
          $key1 = $arr_group[0];
          $key2 = $arr_group[1];
          if(!isset($prices[$key1])) $prices[$key1] = [];
          if(!isset($prices2[$key1])) $prices2[$key1] = [];
          $prices[$key1][$key2] = floatval( $group_value );
          $prices2[$key1][$key2] = floatval( $group_value2 );
        }
      }
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

  return PrepMarketData($meeting_id, $event_number, 'QNP', $win, 'japan_jra');
}

function get_odds_exa_race($__cname, $meeting_id, $event_number)
{
  $str_html = __get_race_data($__cname, "O");
  $str_html = str_replace("票数なし", "0", $str_html);
  $menu_html = str_get_html($str_html);
  $horse_arr = $menu_html->find("div.ozNinkiINTable tr");
  $prices = array();

  foreach ($horse_arr as $horse_obj) {
    $horse_obj_html = str_get_html($horse_obj->innertext);
    if($horse_obj_html->find('th.thninki', 0)){
      $group_info = trim($horse_obj_html->find('th.thkumi', 0)->innertext);
      $group_value = trim($horse_obj_html->find('td.tdoz', 0)->innertext);
      $arr_group = explode("-", $group_info);
      if(count($arr_group) == 2){
        $key1 = $arr_group[0];
        $key2 = $arr_group[1];
        if(!isset($prices[$key1])) $prices[$key1] = [];
        $prices[$key1][$key2] = floatval( $group_value );
      }
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

  return PrepMarketData($meeting_id, $event_number, 'EXA', $win, 'japan_jra');
}

function get_odds_tro_race($__cname, $meeting_id, $event_number)
{
  $str_html = __get_race_data($__cname, "O");
  $menu_html = str_get_html($str_html);
  $horse_arr = $menu_html->find("div.ozNinkiINTable tr");
  $prices = array();

  foreach ($horse_arr as $horse_obj) {
    $horse_obj_html = str_get_html($horse_obj->innertext);
    if($horse_obj_html->find('th.thninki', 0)){
      $group_info = trim($horse_obj_html->find('th.thkumi', 0)->innertext);
      $group_value = trim($horse_obj_html->find('td.tdoz', 0)->innertext);
      $arr_group = explode("-", $group_info);
      if(count($arr_group) == 3){
        $key1 = $arr_group[0];
        $key2 = $arr_group[1];
        $key3 = $arr_group[2];
        if(!isset($prices[$key1])) $prices[$key1] = [];
        if(!isset($prices[$key1][$key2])) $prices[$key1][$key2] = [];
        $prices[$key1][$key2][$key3] = floatval( $group_value );
      }
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

  return PrepMarketData($meeting_id, $event_number, 'TRO', $win, 'japan_jra');
}

function get_odds_tri_race($__cname, $meeting_id, $event_number)
{
  $str_html = __get_race_data($__cname, "O");
  
  $str_html = __get_values($str_html, "oddslistArea", 'oddsSSIArea'); // Optimization
  $str_html = str_replace("票数なし", "0", $str_html);
  $str_html = str_replace("  ", " ", $str_html);
  while(strpos($str_html, "  ") !== false) $str_html = str_replace("  ", " ", $str_html);
  $str_html = __get_after_values($str_html, "oddsTop100Area");  
  $menu_html = str_get_html($str_html);

  $prices = array();
  if($menu_html){
    $horse_arr = $menu_html->find("div.ozNinkiINTable tr");
    foreach ($horse_arr as $horse_obj) {
      $horse_obj_html = str_get_html($horse_obj->innertext);
      if($horse_obj_html->find('th.thninki', 0)){
        $group_info = trim($horse_obj_html->find('th.thkumi', 0)->innertext);
        $group_value = trim($horse_obj_html->find('td.tdoz', 0)->innertext);
        $arr_group = explode("-", $group_info);
        if(count($arr_group) == 3){
          $key1 = $arr_group[0];
          $key2 = $arr_group[1];
          $key3 = $arr_group[2];
          if(!isset($prices[$key1])) $prices[$key1] = [];
          if(!isset($prices[$key1][$key2])) $prices[$key1][$key2] = [];
          $prices[$key1][$key2][$key3] = floatval( $group_value );
        }
      }
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

  return PrepMarketData($meeting_id, $event_number, 'TRI', $win, 'japan_jra');
}

function get_odds_win_race_4ext($__odds, $meeting_id, $event_number)
{
  $win_ret = array();
  $plc_min_ret = array();
  $plc_max_ret = array();

  for ($i=0; $i<count($__odds); $i++) {
    $win_obj = $__odds[$i];
    $win_ret[$win_obj->horse_number] = floatval( $win_obj->price );
    $plc_min_ret[$win_obj->horse_number] = floatval( $win_obj->minPrice );
    $plc_max_ret[$win_obj->horse_number] = floatval( $win_obj->maxPrice );
  }

  $win_ret = patch_array($win_ret);
  $plc_min_ret = patch_array($plc_min_ret);
  $plc_max_ret = patch_array($plc_max_ret);

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

  $post_data = PrepMarketData($meeting_id, $event_number, 'WIN', $win, 'japan_jra');
  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "win");


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

  $post_data = PrepMarketData($meeting_id, $event_number, 'PLC', $place, 'japan_jra');
  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "plc");

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

  $post_data = PrepMarketData($meeting_id, $event_number, 'QNP', $win, 'japan_jra');
  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "qnp");

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

  $post_data = PrepMarketData($meeting_id, $event_number, 'QNL', $win, 'japan_jra');
  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "qnl");

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

  $post_data = PrepMarketData($meeting_id, $event_number, 'EXA', $win, 'japan_jra');
  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "exa");

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

  $post_data = PrepMarketData($meeting_id, $event_number, 'TRO', $win, 'japan_jra');
  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "tro");

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

  $post_data = PrepMarketData($meeting_id, $event_number, 'TRI', $win, 'japan_jra');

  __log_and_debug_and_send_data_4ext($meeting_id, $event_number, $post_data, "tri");

  return true;
}

function __get_race_odds_data_full($meeting_id, $__race_id, $__race_odds, $spec = false){
  if(__check_meeting_race_spec($meeting_id, $__race_id, $spec)) return false;
  $send_check = __check_send_data_config($spec);
  $log_check = __check_log_data_config($spec);

  $post_data = get_odds_win_race($__race_odds->WIN, $meeting_id, $__race_id);
  $post_data->qnl = get_odds_qnl_race($__race_odds->QNL, $meeting_id, $__race_id);
  $post_data->qnp = get_odds_qnp_race($__race_odds->QNP, $meeting_id, $__race_id);
  $post_data->exa = get_odds_exa_race($__race_odds->EXA, $meeting_id, $__race_id);
  $post_data->tro = get_odds_tro_race($__race_odds->TRO, $meeting_id, $__race_id);
  $post_data->tri = get_odds_tri_race($__race_odds->TRI, $meeting_id, $__race_id);

  if(!($post_data->win)) return false;

  __log_and_debug_and_send_data($meeting_id, $__race_id, $post_data, $log_check, $send_check, "jra");

  return $post_data;
}

function __get_today_odds_cname_4ext(){
  __get_today_odds();

  $file_name = date("Ymd", GetCurrentJapanTimeStamp()).".odds.json";
  if(file_exists($file_name)) {
    echo @file_get_contents($file_name);
    exit();
  }
  $result = array();
  $ret = __get_race_schedule();
  for($i=0; $i<count($ret); $i++){
    if($ret[$i]->meeting_id == 0) continue;
    if(file_exists($ret[$i]->meeting_id.".odds.cname")){
      $races = json_decode(@file_get_contents($ret[$i]->meeting_id.".odds.cname"));
      $result = array_merge($result, $races);
    }
  }
  file_put_contents($file_name, json_encode($result));
  echo json_encode($result);
}

function __get_race_odds_overall($spec = false){
  __get_today_odds();
  $max_time_diff = 3600 * 10;
  if($spec){
    if(isset($spec->duration)) $max_time_diff = $spec->duration * 3600;
  }
  $ret = __get_race_schedule();
  for($i=0; $i<count($ret); $i++){
    if($ret[$i]->meeting_id == 0) continue;
    if(file_exists($ret[$i]->meeting_id.".odds.cname")){
      $races = json_decode(@file_get_contents($ret[$i]->meeting_id.".odds.cname"));
      for($j=0; $j<count($races); $j++){
        $race_time = $races[$j]->time;
        $now_time = GetCurrentJapanTime();
        $race_time = date("Y-m-d", strtotime($now_time)).$race_time.":00";
        $time_diff = strtotime($race_time) - strtotime($now_time);
        if(($time_diff > $max_time_diff) || ($time_diff < -300)) continue;
        __show_debug_info__("Start Checking JRA - Backup Odds Info ...");
        __show_debug_info__("Race ID: ".$races[$j]->race_number);
        __get_race_odds_data_full($races[$j]->meeting_id, $races[$j]->race_number, $races[$j], $spec);
        __show_debug_info__("End Checking JRA - Backup Odds Info ...");
      }      
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

function __get_race_event($meeting_id){
  $__id = @file_get_contents($meeting_id.".cname");
  if(!($__id)) return array();

  $arr_result = [];
  $race_num = substr($__id, 19, 2);
  $str_html = __get_race_data($__id);
  file_put_contents($meeting_id.".result", $str_html);
  $race_html = str_get_html($str_html);
  $races = $race_html->find('.raceSelect td');
  for($i=0; $i<count($races)/2; $i++){
    $race_row_string = str_get_html($races[$i]->innertext);
    $event_number = __get_until_values($race_row_string->find('img', 0)->alt, 'レース');
    $race_obj = new \stdClass;
    $race_obj->meeting_id = $meeting_id;
    $race_obj->event_number = $event_number;

    if($races[$i]->class == "select"){
      $race_obj->cname = $__id;
    } else {
      $cname = __get_values($race_row_string->find('a', 0)->onclick, ",'", "');");
      $race_obj->cname = $cname;
    }
    $arr_result[] = $race_obj;
  }
  return $arr_result;
}

function __get_extra_events($bForce = false, $date_str = ""){
  if($bForce && ($date_str)){
    $file_name = $date_str.".event";
    if(file_exists($file_name)) return json_decode(@file_get_contents($file_name));
    if(file_exists("logs/backup/".$file_name)) return json_decode(@file_get_contents("logs/backup/".$file_name));
    return array();
  }

  $now_time = GetCurrentJapanTime();
  $file_name = date("Ymd", strtotime($now_time)).".event";

  //if(file_exists($file_name)) return json_decode(@file_get_contents($file_name));
  //if(file_exists("logs/backup/".$file_name)) return json_decode(@file_get_contents("logs/backup/".$file_name));

  __get_today_result();

  $arr_cnames = array();
  $arr_schedule = __get_race_schedule();
  for($i=0; $i<count($arr_schedule); $i++){
    $meeting_id = $arr_schedule[$i]->meeting_id;
    $arr_cnames = array_merge($arr_cnames, __get_race_event($meeting_id));
  }

  for($i=0; $i<count($arr_cnames); $i++){
    $str_html = __get_race_data($arr_cnames[$i]->cname);
    $html = str_get_html($str_html);
    $event = [];
    foreach($html->find(".kijiRowDiv .kijiCellDiv") as $event_cell){
      $event[] = $event_cell->innertext;
    }
    $arr_cnames[$i]->event = $event;
  }

  file_put_contents($file_name, json_encode($arr_cnames));
  return $arr_cnames;
}

function __test_data__(){
  $str_html = file_get_contents("9.log");
  $str_html = __get_after_values($str_html, "oddslistArea");
  $str_html = str_replace("票数なし", "0", $str_html);
  $str_html = str_replace("  ", "", $str_html);  
  $menu_html = str_get_html($str_html);

  $prices = array();
  if($menu_html){
    $horse_arr = $menu_html->find("div.ozNinkiINTable tr");
    foreach ($horse_arr as $horse_obj) {
      $horse_obj_html = str_get_html($horse_obj->innertext);
      if($horse_obj_html->find('th.thninki', 0)){
        $group_info = trim($horse_obj_html->find('th.thkumi', 0)->innertext);
        $group_value = trim($horse_obj_html->find('td.tdoz', 0)->innertext);
        $arr_group = explode("-", $group_info);
        if(count($arr_group) == 3){
          $key1 = $arr_group[0];
          $key2 = $arr_group[1];
          $key3 = $arr_group[2];
          if(!isset($prices[$key1])) $prices[$key1] = [];
          if(!isset($prices[$key1][$key2])) $prices[$key1][$key2] = [];
          $prices[$key1][$key2][$key3] = floatval( $group_value );
        }
      }
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

  print_r($horseOdds);
}

function __get_schedule_info_from($arr_schedule, $__meeting_name){
  for($i=0; $i<count($arr_schedule); $i++){
    $meeting_id = $arr_schedule[$i]->meeting_id;
    $meeting_name = $arr_schedule[$i]->meeting_name;
    if($meeting_name == $__meeting_name)
      return $arr_schedule[$i]->venue_info;
  }
  return false;
}

function check_race_time_change2(){
  
  $arr_schedule = __get_race_schedule();
  $menu_cname = __get_news_week_cname();
  $arr_result = array();
  $str_html = __get_race_data($menu_cname, "I");
  $menu_html = str_get_html($str_html);
  $arr_tracks = [];

  foreach ($menu_html->find('tr') as $tr_obj) {
    if($tr_obj->find('td', 0)->innertext == "R") {
      $arr_tracks = [];
      $tds = $tr_obj->find('td');
      for($i=0; $i<count($tds); $i+=2) {
        $track_name = $tds[$i+1]->find('strong font', 0)->innertext;
        $arr_track = explode("</strong>", $track_name);
        $track_name = str_get_html($track_name)->find('strong font', 0)->innertext."".$arr_track[1];
        $arr_tracks[$i / 2] = $track_name;
      }
      continue;
    }
    if(!isset($tr_obj->valign)) continue;
    if($tr_obj->valign != "top") continue;
    $tds = $tr_obj->find('td');
    for($i=0; $i<count($tds); $i+=2) {
      $race_num = $tds[$i]->innertext;
      $race_event = $tds[$i+1]->innertext;
      if(trim($race_event)){
        $race_event_items = explode("<strong>", $race_event);
        foreach ($race_event_items as $race_event_item) {
          $race_event_item = "<strong>".$race_event_item;
          $event_number = $race_num;
          $event_type = str_get_html($race_event_item)->find('strong', 0)->innertext;
          $arr_race_event = explode("<br>", $race_event_item);
          for($k=1; $k<count($arr_race_event)-1; $k++) {
            $event_content = $arr_race_event[$k];
            if(count($arr_tracks) < $i / 2) continue;
            $venue_info = __get_schedule_info_from($arr_schedule, $arr_tracks[$i / 2]);
            $change_event = new \stdClass;
            $change_event->venue_id = $venue_info->venue_id;
            $change_event->venue_type = $venue_info->venue_type;
            $change_event->venue_name = $venue_info->venue_name;
            $change_event->meeting_date = $venue_info->meeting_date;
            $change_event->type = "other";
            if(strpos($event_type, "発走時刻") !== false) {
              $arr_temp = explode("→", $event_content);
              if(count($arr_temp) < 2) continue;
              $event_content = trim($arr_temp[1]);
              $change_event->number = $event_number;
              $change_event->time = $event_content;
              $change_event->type = "time";
            } else if(strpos($event_type, "競走除外") !== false) {
              $change_event->event_number = $event_number;
              $change_event->number = intval($event_content);
              $change_event->horse = $event_content;
              $change_event->type = "withdrawn";
            } else if(strpos($event_type, "競走中止") !== false) {
              $change_event->event_number = $event_number;
              $change_event->number = intval($event_content);
              $change_event->horse = $event_content;
              $change_event->type = "disqualified";
            } else if(strpos($event_type, "出走取消") !== false) {
              $change_event->event_number = $event_number;
              $change_event->number = intval($event_content);
              $change_event->horse = $event_content;
              $change_event->type = "scratched";
            }
            if($change_event->type == "other") continue;
            $arr_result[] = $change_event;
          }
        }
      }
    }
  }
  
  $now_time = GetCurrentJapanTime();
  $file_name = date("Ymd", strtotime($now_time))."_realtime_jra2.dat";

  $old_data = @file_get_contents($file_name);
  @file_put_contents($file_name, json_encode($arr_result));
  if(json_encode($arr_result) != $old_data) {
    @file_put_contents($file_name, json_encode($arr_result));
    return $arr_result; 
  }

  return [];
}

function check_race_time_change(){
  $arr_schedule = __get_race_schedule();
  $menu_cname = __get_races_cname();
  $arr_result = array();
  $str_html = __get_race_data($menu_cname, "D");

  $menu_html = str_get_html($str_html);
  
  for($i=0; $i<count($arr_schedule); $i++){
    $meeting_id = $arr_schedule[$i]->meeting_id;
    $meeting_name = $arr_schedule[$i]->meeting_name;
    for($j=0; $j<count($arr_schedule[$i]->races); $j++){
      $race_obj = $arr_schedule[$i]->races[$j];
      $event_number = $race_obj->race_id;      
      $race_result = __get_race_result($meeting_id, $event_number, true);
      foreach ($race_result as $result_val) {
        if(!is_numeric($result_val->race_data->finish_position)){
          $runner_number = $result_val->runner_number;
          $runner_name = $result_val->runner_name;
          $price = $result_val->race_data->finish_position;         
          $changed_type = "scratched";
          if(strpos($price, "除外") !== false) $changed_type = "withdrawn";
          else if(strpos($price, "中止") !== false) $changed_type = "disqualified";

          $change_event = new \stdClass;
          $change_event->venue_id = $arr_schedule[$i]->venue_info->venue_id;
          $change_event->venue_type = $arr_schedule[$i]->venue_info->venue_type;
          $change_event->venue_name = $arr_schedule[$i]->venue_info->venue_name;
          $change_event->meeting_date = $arr_schedule[$i]->venue_info->meeting_date;
          $change_event->event_number = $event_number;
          $change_event->number = $runner_number;
          $change_event->horse = $runner_name;
          $change_event->type = $changed_type;
          $arr_result[] = $change_event;
        }
      }
    }
    foreach($menu_html->find('.kaisaiBtn a') as $menu_a) {
      $menu_desc = trim($menu_a->innertext);
      if(__get_after_values($menu_desc, "&nbsp;") == $meeting_name){
        $menu_cname = __get_values($menu_a->onclick, ",'", "')");
        $change_races = __get_time_cname($meeting_id, $menu_cname);
        
        for($j=0; $j<count($change_races); $j++){
          $time_diff = $change_races[$j]->time;
          $event_number = $change_races[$j]->event_number;

          $change_event = new \stdClass;
          $change_event->venue_id = $arr_schedule[$i]->venue_info->venue_id;
          $change_event->venue_type = $arr_schedule[$i]->venue_info->venue_type;
          $change_event->venue_name = $arr_schedule[$i]->venue_info->venue_name;
          $change_event->meeting_date = $arr_schedule[$i]->venue_info->meeting_date;
          $change_event->number = $event_number;
          $change_event->time = $time_diff;
          $change_event->type = "time";
          $arr_result[] = $change_event;
        }
      }
    }
  }

  $scratched_result = __check_scratched_info();
  $arr_result = array_merge($arr_result, $scratched_result);

  $now_time = GetCurrentJapanTime();
  $file_name = date("Ymd", strtotime($now_time))."_realtime_jra.dat";

  $old_data = @file_get_contents($file_name);
  @file_put_contents($file_name, json_encode($arr_result));
  if(json_encode($arr_result) != $old_data) {
    @file_put_contents($file_name, json_encode($arr_result));
    return $arr_result; 
  }

  return [];
}

function __check_scratched_info(){
  __get_today_odds();
  $arr_result = [];
  $ret = __get_race_schedule();
  for($i=0; $i<count($ret); $i++){
    if($ret[$i]->meeting_id == 0) continue;
    if(file_exists($ret[$i]->meeting_id.".odds.cname")){
      $races = json_decode(@file_get_contents($ret[$i]->meeting_id.".odds.cname"));
      for($j=0; $j<count($races); $j++){
        $str_html = __get_race_data($races[$j]->WIN_ORI, "O");
        $menu_html = str_get_html($str_html);
        $horse_arr = $menu_html->find("div.ozTanfukuTableUma tr");
        $win_ret = array();
        $max_num = 0;
        foreach ($horse_arr as $horse_obj) {
          $horse_obj_html = str_get_html($horse_obj->innertext);
          if($horse_obj_html->find('th.umaban', 0)){
            $horse_number = $horse_obj_html->find('th.umaban', 0)->innertext;
            $price = trim($horse_obj_html->find('td.oztan', 0)->innertext);
            if(!is_numeric($price)){              
              $changed_type = "scratched";
              if(strpos($price, "除外") !== false) $changed_type = "withdrawn";
              else if(strpos($price, "中止") !== false) $changed_type = "disqualified";
              $change_event = new \stdClass;
              $change_event->venue_id = $ret[$i]->venue_info->venue_id;
              $change_event->venue_type = $ret[$i]->venue_info->venue_type;
              $change_event->venue_name = $ret[$i]->venue_info->venue_name;
              $change_event->meeting_date = $ret[$i]->venue_info->meeting_date;
              $change_event->event_number = intval(str_replace("R", "", $races[$j]->race_number));
              $change_event->number = $horse_number;
              
              $bamei = trim($horse_obj_html->find('td.bamei a', 0)->innertext);

              $change_event->horse = $bamei;
              $change_event->type = $changed_type;
              $arr_result[] = $change_event;
            }
          }
        }
      }      
    }
  }
  return $arr_result;
}

?>