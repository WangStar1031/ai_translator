<?php
//	use PHPMailer\PHPMailer\PHPMailer;
//	use PHPMailer\PHPMailer\Exception;

	include('guzzle/autoload.php');
	include('simple_html_dom.php');
/*
	require('src/PHPMailer.php');
	require('src/SMTP.php');
	require('src/Exception.php');
*/
	require('sendgrid/autoload.php');

	function __send_email($__to_mail, $__mail_title, $__mail_content){
		$apiKey = "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";
		$__from_name = "JTS Support Team";
		$__from_mail = "support@jts.ec";
		$__to_name = "Dear Client";

		$from = new SendGrid\Email($__from_name, $__from_mail);
		$subject = $__mail_title;
		$to = new SendGrid\Email($__to_name, $__to_mail);
		$content = new SendGrid\Content("text/html", $__mail_content);
		$mail = new SendGrid\Mail($from, $subject, $to, $content);

		$sg = new \SendGrid($apiKey);

		$response = $sg->client->mail()->send()->post($mail);
		return $response;
	}

/*
	function __send_email_back($__to_email_address, $__title_mail, $__content_mail) {		
		$ret = new \stdClass;
		$ret->status = "";

		$mail = new PHPMailer(true);
		try {
		    //$mail->SMTPDebug = 2;
		    $mail->isSMTP();
		    $mail->Host = 'smtp.gmail.com';
		    $mail->SMTPAuth = true;
		    $mail->Username = 'mark.oriend.philip@gmail.com';
		    $mail->Password = '1qaz2wsx!QAZ';
		    $mail->SMTPSecure = 'tls';
		    $mail->Port = 587;

		    //Recipients
		    $mail->setFrom('mark.oriend.philip@gmail.com', 'Assistant');
		    $mail->addAddress($__to_email_address);

		    $mail->isHTML(true);
		    $mail->Subject = $__title_mail;
		    $mail->Body    = $__content_mail;
		    $mail->AltBody = $__content_mail;

		    $mail->SMTPOptions = array(
		        'ssl' => array(
		            'verify_peer' => false,
		            'verify_peer_name' => false,
		            'allow_self_signed' => true
		        )
		    );
		    $mail->send();
		    $ret->status = 'Message has been sent';
		} catch (Exception $e) {
		    $ret->status = 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
		}

		return $ret;
	}
*/

	function __kata2romaji($__str){
	  $__str = str_replace("ん", "n", $__str);
	  $__str = str_replace("つ", "tsu", $__str);
	  $__str = str_replace("きゃ", "kya", $__str);
	  $__str = str_replace("きゅ", "kyu", $__str);
	  $__str = str_replace("きょ", "kyo", $__str);
	  $__str = str_replace("にゃ", "nya", $__str);
	  $__str = str_replace("にゅ", "nyu", $__str);
	  $__str = str_replace("にょ", "nyo", $__str);
	  $__str = str_replace("しゃ", "sha", $__str);
	  $__str = str_replace("し", "shi", $__str);
	  $__str = str_replace("しゅ", "shu", $__str);
	  $__str = str_replace("しょ", "sho", $__str);
	  $__str = str_replace("ち", "chi", $__str);
	  $__str = str_replace("ちゃ", "cha", $__str);
	  $__str = str_replace("ちゅ", "chu", $__str);
	  $__str = str_replace("ちょ", "cho", $__str);
	  $__str = str_replace("ひゃ", "hya", $__str);
	  $__str = str_replace("ひゅ", "hyu", $__str);
	  $__str = str_replace("ひょ", "hyo", $__str);
	  $__str = str_replace("みゃ", "mya", $__str);
	  $__str = str_replace("みゅ", "myu", $__str);
	  $__str = str_replace("みょ", "myo", $__str);
	  $__str = str_replace("りゃ", "rya", $__str);
	  $__str = str_replace("りゅ", "ryu", $__str);
	  $__str = str_replace("りょ", "ryo", $__str);
	  $__str = str_replace("ぎゃ", "gya", $__str);
	  $__str = str_replace("ぎゅ", "gyu", $__str);
	  $__str = str_replace("ぎょ", "gyo", $__str);
	  $__str = str_replace("びゃ", "bya", $__str);
	  $__str = str_replace("びゅ", "byu", $__str);
	  $__str = str_replace("びょ", "byo", $__str);
	  $__str = str_replace("ぴゃ", "pya", $__str);
	  $__str = str_replace("ぴゅ", "pyu", $__str);
	  $__str = str_replace("ぴょ", "pyo", $__str);
	  $__str = str_replace("じゃ", "ja", $__str);
	  $__str = str_replace("じゅ", "ju", $__str);
	  $__str = str_replace("じょ", "jo", $__str);
	  $__str = str_replace("ば", "ba", $__str);
	  $__str = str_replace("だ", "da", $__str);
	  $__str = str_replace("が", "ga", $__str);
	  $__str = str_replace("は", "ha", $__str);
	  $__str = str_replace("か", "ka", $__str);
	  $__str = str_replace("ま", "ma", $__str);
	  $__str = str_replace("な", "na", $__str);
	  $__str = str_replace("ぱ", "pa", $__str);
	  $__str = str_replace("ら", "ra", $__str);
	  $__str = str_replace("さ", "sa", $__str);
	  $__str = str_replace("た", "ta", $__str);
	  $__str = str_replace("わ", "wa", $__str);
	  $__str = str_replace("や", "ya", $__str);
	  $__str = str_replace("ざ", "za", $__str);
	  $__str = str_replace("あ", "a", $__str);
	  $__str = str_replace("べ", "be", $__str);
	  $__str = str_replace("で", "de", $__str);
	  $__str = str_replace("げ", "ge", $__str);
	  $__str = str_replace("へ", "he", $__str);
	  $__str = str_replace("け", "ke", $__str);
	  $__str = str_replace("め", "me", $__str);
	  $__str = str_replace("ね", "ne", $__str);
	  $__str = str_replace("ぺ", "pe", $__str);
	  $__str = str_replace("れ", "re", $__str);
	  $__str = str_replace("せ", "se", $__str);
	  $__str = str_replace("て", "te", $__str);
	  $__str = str_replace("ゑ", "we", $__str);
	  $__str = str_replace("ぜ", "ze", $__str);
	  $__str = str_replace("え", "e", $__str);
	  $__str = str_replace("び", "bi", $__str);
	  $__str = str_replace("ぎ", "gi", $__str);
	  $__str = str_replace("ひ", "hi", $__str);
	  $__str = str_replace("き", "ki", $__str);
	  $__str = str_replace("み", "mi", $__str);
	  $__str = str_replace("に", "ni", $__str);
	  $__str = str_replace("ぴ", "pi", $__str);
	  $__str = str_replace("り", "ri", $__str);
	  $__str = str_replace("ゐ", "wi", $__str);
	  $__str = str_replace("じ", "ji", $__str);
	  $__str = str_replace("い", "i", $__str);
	  $__str = str_replace("ぼ", "bo", $__str);
	  $__str = str_replace("ど", "do", $__str);
	  $__str = str_replace("ご", "go", $__str);
	  $__str = str_replace("ほ", "ho", $__str);
	  $__str = str_replace("こ", "ko", $__str);
	  $__str = str_replace("も", "mo", $__str);
	  $__str = str_replace("の", "no", $__str);
	  $__str = str_replace("ぽ", "po", $__str);
	  $__str = str_replace("ろ", "ro", $__str);
	  $__str = str_replace("そ", "so", $__str);
	  $__str = str_replace("と", "to", $__str);
	  $__str = str_replace("を", "wo", $__str);
	  $__str = str_replace("よ", "yo", $__str);
	  $__str = str_replace("ぞ", "zo", $__str);
	  $__str = str_replace("お", "o", $__str);
	  $__str = str_replace("ぶ", "bu", $__str);
	  $__str = str_replace("ぐ", "gu", $__str);
	  $__str = str_replace("ふ", "fu", $__str);
	  $__str = str_replace("く", "ku", $__str);
	  $__str = str_replace("む", "mu", $__str);
	  $__str = str_replace("ぬ", "nu", $__str);
	  $__str = str_replace("ぷ", "pu", $__str);
	  $__str = str_replace("る", "ru", $__str);
	  $__str = str_replace("す", "su", $__str);
	  $__str = str_replace("ゆ", "yu", $__str);
	  $__str = str_replace("ず", "zu", $__str);
	  $__str = str_replace("う", "u", $__str);
	  $__str = str_replace("ゔ", "v", $__str);
	  $__str = str_replace("ぢ", "ji", $__str);
	  $__str = str_replace("づ", "zu", $__str);
	  $__str = str_replace("ン", "n", $__str);
	  $__str = str_replace("シ", "shi", $__str);
	  $__str = str_replace("チ", "chi", $__str);
	  $__str = str_replace("ツ", "tsu", $__str);
	  $__str = str_replace("キャ", "kya", $__str);
	  $__str = str_replace("キュ", "kyu", $__str);
	  $__str = str_replace("キョ", "kyo", $__str);
	  $__str = str_replace("ニャ", "nya", $__str);
	  $__str = str_replace("ニュ", "nyu", $__str);
	  $__str = str_replace("ニョ", "nyo", $__str);
	  $__str = str_replace("シャ", "sha", $__str);
	  $__str = str_replace("シュ", "shu", $__str);
	  $__str = str_replace("ショ", "sho", $__str);
	  $__str = str_replace("チャ", "cha", $__str);
	  $__str = str_replace("チュ", "chu", $__str);
	  $__str = str_replace("チョ", "cho", $__str);
	  $__str = str_replace("ヒャ", "hya", $__str);
	  $__str = str_replace("ヒュ", "hyu", $__str);
	  $__str = str_replace("ヒョ", "hyo", $__str);
	  $__str = str_replace("ミャ", "mya", $__str);
	  $__str = str_replace("ミュ", "myu", $__str);
	  $__str = str_replace("ミョ", "myo", $__str);
	  $__str = str_replace("リャ", "rya", $__str);
	  $__str = str_replace("リュ", "ryu", $__str);
	  $__str = str_replace("リョ", "ryo", $__str);
	  $__str = str_replace("ギャ", "gya", $__str);
	  $__str = str_replace("ギュ", "gyu", $__str);
	  $__str = str_replace("ギョ", "gyo", $__str);
	  $__str = str_replace("ビャ", "bya", $__str);
	  $__str = str_replace("ビュ", "byu", $__str);
	  $__str = str_replace("ビョ", "byo", $__str);
	  $__str = str_replace("ピャ", "pya", $__str);
	  $__str = str_replace("ピュ", "pyu", $__str);
	  $__str = str_replace("ピョ", "pyo", $__str);
	  $__str = str_replace("ジャ", "ja", $__str);
	  $__str = str_replace("ジュ", "ju", $__str);
	  $__str = str_replace("ジョ", "jo", $__str);
	  $__str = str_replace("バ", "ba", $__str);
	  $__str = str_replace("ダ", "da", $__str);
	  $__str = str_replace("ガ", "ga", $__str);
	  $__str = str_replace("ハ", "ha", $__str);
	  $__str = str_replace("カ", "ka", $__str);
	  $__str = str_replace("マ", "ma", $__str);
	  $__str = str_replace("ナ", "na", $__str);
	  $__str = str_replace("パ", "pa", $__str);
	  $__str = str_replace("ラ", "ra", $__str);
	  $__str = str_replace("サ", "sa", $__str);
	  $__str = str_replace("タ", "ta", $__str);
	  $__str = str_replace("ワ", "wa", $__str);
	  $__str = str_replace("ヤ", "ya", $__str);
	  $__str = str_replace("ザ", "za", $__str);
	  $__str = str_replace("ア", "a", $__str);
	  $__str = str_replace("ベ", "be", $__str);
	  $__str = str_replace("デ", "de", $__str);
	  $__str = str_replace("ゲ", "ge", $__str);
	  $__str = str_replace("ヘ", "he", $__str);
	  $__str = str_replace("ケ", "ke", $__str);
	  $__str = str_replace("メ", "me", $__str);
	  $__str = str_replace("ネ", "ne", $__str);
	  $__str = str_replace("ペ", "pe", $__str);
	  $__str = str_replace("レ", "re", $__str);
	  $__str = str_replace("セ", "se", $__str);
	  $__str = str_replace("テ", "te", $__str);
	  $__str = str_replace("ヱ", "we", $__str);
	  $__str = str_replace("ゼ", "ze", $__str);

	  $__str = str_replace("ァ", "a", $__str);
	  $__str = str_replace("ィ", "i", $__str);	  
	  $__str = str_replace("ォ", "o", $__str);
	  $__str = str_replace("ェ", "e", $__str);
	  $__str = str_replace("ャ", "ya", $__str);
	  $__str = str_replace("ョ", "yo", $__str);
	  $__str = str_replace("ヴ", "vu", $__str);
	     
	  
	  $__str = str_replace("エ", "e", $__str);
	  $__str = str_replace("ビ", "bi", $__str);
	  $__str = str_replace("ギ", "gi", $__str);
	  $__str = str_replace("ヒ", "hi", $__str);
	  $__str = str_replace("キ", "ki", $__str);
	  $__str = str_replace("ミ", "mi", $__str);
	  $__str = str_replace("ニ", "ni", $__str);
	  $__str = str_replace("ピ", "pi", $__str);
	  $__str = str_replace("リ", "ri", $__str);
	  $__str = str_replace("ヰ", "wi", $__str);
	  $__str = str_replace("ジ", "ji", $__str);
	  $__str = str_replace("イ", "i", $__str);
	  $__str = str_replace("ボ", "bo", $__str);
	  $__str = str_replace("ド", "do", $__str);
	  $__str = str_replace("ゴ", "go", $__str);
	  $__str = str_replace("ホ", "ho", $__str);
	  $__str = str_replace("コ", "ko", $__str);
	  $__str = str_replace("モ", "mo", $__str);
	  $__str = str_replace("ノ", "no", $__str);
	  $__str = str_replace("ポ", "po", $__str);
	  $__str = str_replace("ロ", "ro", $__str);
	  $__str = str_replace("ソ", "so", $__str);
	  $__str = str_replace("ト", "to", $__str);
	  $__str = str_replace("ヲ", "wo", $__str);
	  $__str = str_replace("ヨ", "yo", $__str);
	  $__str = str_replace("ゾ", "zo", $__str);
	  $__str = str_replace("オ", "o", $__str);
	  $__str = str_replace("ブ", "bu", $__str);
	  $__str = str_replace("グ", "gu", $__str);
	  $__str = str_replace("フ", "fu", $__str);
	  $__str = str_replace("ク", "ku", $__str);
	  $__str = str_replace("ム", "mu", $__str);
	  $__str = str_replace("ヌ", "nu", $__str);
	  $__str = str_replace("プ", "pu", $__str);
	  $__str = str_replace("ル", "ru", $__str);
	  $__str = str_replace("ス", "su", $__str);
	  $__str = str_replace("ユ", "yu", $__str);
	  $__str = str_replace("ズ", "zu", $__str);
	  $__str = str_replace("ウ", "u", $__str);
	  //$__str = str_replace("oo", "ō", $__str);
	  //$__str = str_replace("ou", "ō", $__str);
	  //$__str = str_replace("uu", "ū", $__str);
	  $__str = str_replace("ッk", "kk", $__str);
	  $__str = str_replace("ッs", "ss", $__str);
	  $__str = str_replace("ッt", "tt", $__str);
	  $__str = str_replace("ッn", "nn", $__str);
	  $__str = str_replace("ッm", "mm", $__str);
	  $__str = str_replace("ッr", "rr", $__str);
	  $__str = str_replace("ッg", "gg", $__str);
	  $__str = str_replace("ッd", "dd", $__str);
	  $__str = str_replace("ッb", "bb", $__str);
	  $__str = str_replace("ッp", "pp", $__str);
	  $__str = str_replace("ッf", "ff", $__str);
	  $__str = str_replace("ッj", "jj", $__str);
	  $__str = str_replace("ッ", "\!", $__str);
	  $__str = str_replace("ー", "=", $__str);
	  
	  $obj_text = $__str;
	  while(strpos($obj_text, "=") !== false){
	      $pos_i = strpos($obj_text, "=");
	      $switch_char = substr($obj_text, $pos_i - 1, 1);
	      $obj_text = substr($obj_text, 0, $pos_i).$switch_char.substr($obj_text, $pos_i + 1);
	  }
	  return strtoupper($obj_text);
	}

	function __get_values($__str_data, $__pre_pattern, $__post_pattern) {
	    $__pos = strpos($__str_data, $__pre_pattern);
	    if($__pos !== false){
	        $__str_data = substr($__str_data, $__pos + strlen($__pre_pattern));
	        $__pos = strpos($__str_data, $__post_pattern);
	        if($__pos !== false) {
	            return substr($__str_data, 0, $__pos);
	        } else 
	            return false;
	    } else
	    return false;
	}

	function __get_until_values($__str_data, $__post_pattern){
	    $__pos = strpos($__str_data, $__post_pattern);
	    if($__pos !== false)
	        return substr($__str_data, 0, $__pos);
	    return false;    
	}

	function __get_after_values($__str_data, $__post_pattern){
	    $__pos = strpos($__str_data, $__post_pattern);
	    if($__pos !== false)
	        return substr($__str_data, $__pos + strlen($__post_pattern));
	    return false;
	}

	function __get_last_values($__str_data, $__post_pattern){
	    $__pos = strrpos($__str_data, $__post_pattern);
	    if($__pos !== false)
	        return substr($__str_data, $__pos + strlen($__post_pattern));
	    return false;
	}

	function __call_safe_url($__url, $proxy = "") {
		/*
		if((strpos($__url, "https://keiba.rakuten.co.jp") !== false) && (strpos($_SERVER['HTTP_HOST'], "www.test.com") === false)){
			if(file_exists("proxies_japan")){
				$proxy = @file_get_contents("proxies_japan");
				return __call_safe_url_for_test__($__url, $proxy);
			}
		}
		*/
		return __call_safe_url_00( $__url, $proxy );
	}

	function __call_safe_url_for_test__($__url, $proxy = "") {
		$__url = str_replace("&amp;", "&", $__url);
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $__url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			CURLOPT_HTTPHEADER => array(
			  "cache-control: no-cache",
			  "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36"
			),
		));
		if($proxy != "")
			curl_setopt($curl, CURLOPT_PROXY, $proxy);

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
		return $response;
	}

	function __call_safe_url_00($__url, $proxy = "") {
		$__url = str_replace("&amp;", "&", $__url);
		$body = "";
		try {
			$client = new GuzzleHttp\Client();
			if($proxy)
				$response = $client->request('GET', $__url, ['proxy' => "tcp://".$proxy]);
			else
				$response = $client->request('GET', $__url);
			$body = $response->getBody();
		} catch (GuzzleHttp\Exception\RequestException $e) {
			return "<html></html>";
		}
		return $body;
	}

	function GetCurrentJapanTimeStamp()
	{
		return strtotime( date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9) );
	}

	function GetCurrentJapanTime()
	{
		return date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9);
	}

	function GetMeetingsFile($date = "")
	{
		$data = null;
		$now_time = GetCurrentJapanTime();

		if ($date == "") $date = date('Ymd', strtotime($now_time));
		if(file_exists($date.".meetings")) return @file_get_contents($date.".meetings");

		$url = "http://dw-staging-elb-1068016683.ap-southeast-2.elb".
			".amazonaws.com/api/meetings?filters[meeting_date]=$date&".
			"filters[countries][]=JPN";

		$data = @file_get_contents($url);
		file_put_contents($date.".meetings", $data);
		return $data;
	}

	function __get_jra_venue_info($__meeting_id) {
		$meetings = GetMeetingsFile();
		$meetings_obj = json_decode($meetings, true);
		$arr_data = $meetings_obj["data"]["meetings"];
		foreach($arr_data as $meeting_detail){
		    if($meeting_detail["venue"]["host_market"] == "japan_jra"){
		    	$meeting_id =  $meeting_detail["meeting_id"];
	    		if($meeting_id == $__meeting_id) {
	    			$venue_obj = new \stdClass;
	    			$venue_obj->venue_id = $meeting_detail["venue"]["venue_id"];
	    			$venue_obj->venue_name = $meeting_detail["venue"]["venue_name"];
	    			$venue_obj->venue_type = $meeting_detail["venue"]["venue_type"];
	    			$venue_obj->meeting_date = $meeting_detail["meeting_date"];
	    			return $venue_obj;
	    		}
		    }
		}
		return false;
	}

	function __get_meeting_ids() {
	  $arr_meetings = array();
	  $meetings = GetMeetingsFile();

	  $now_time = GetCurrentJapanTime();
	  $date_str = date("Y-m-d", strtotime($now_time));

		$tracks = array(
			"Sapporo" => '札幌',
			"Hakodate" => '函館',
			"Fukushima" => '福島',
			"Niigata" => '新潟',
			"Tokyo" => '東京',
			"Nakayama" => '中山',
			"Chukyo" => '中京',
			"Kyoto" => '京都',
			"Hanshin" => '阪神',
			"Kokura" => '小倉',
		);

	  $meetings_obj = json_decode($meetings, true);
	  $arr_data = $meetings_obj["data"]["meetings"];
	  foreach($arr_data as $meeting_detail){
	    if($meeting_detail["venue"]["host_market"] == "japan_jra"){
	    	$venue_name = $meeting_detail["venue"]["venue_name"];
	    	if(isset($tracks[$venue_name])){
	    		$venue_key = $tracks[$venue_name];
	    		$arr_meetings[$venue_key] = $meeting_detail["meeting_id"];
	    	}
	    }
	  }
	  return $arr_meetings;
	}

	function __get_nar_meeting_ids() {
	  $arr_meetings = array();
	  $meetings = GetMeetingsFile();

	  $now_time = GetCurrentJapanTime();
	  $date_str = date("Y-m-d", strtotime($now_time));
	  	/*
		$tracks = array(
			"Obihiro" => '0304',
			"Morioka" => '1006',
			"Mizusawa" => '1106',
			"Urawa" => '1813',
			"Funabashi" => '1914',
			"Oi" => '2015',
			"Kawasaki" => '2135',
			"Kanazawa" => '2218',
			"Kasamatsu" => '2320',
			"Nagoya" => '2433',
			"Sonoda" => '2726',
			"Himeji" => '2826',
			"Fukuyama" => '3028',
			"Kochi" => '3129',
			"Saga" => '3230',
			"Arao" => '3331',
			"Monbetsu" => '3601',
		);*/
		$tracks = array(
			"Obihiro" => '03',
			"Morioka" => '10',
			"Mizusawa" => '11',
			"Urawa" => '18',
			"Funabashi" => '19',
			"Oi" => '20',
			"Tokyo City Keiba" => '20',
			"Kawasaki" => '21',
			"Kanazawa" => '22',
			"Kasamatsu" => '23',
			"Nagoya" => '24',
			"Sonoda" => '27',
			"Himeji" => '28',
			"Fukuyama" => '30',
			"Kochi" => '31',
			"Saga" => '32',
			"Arao" => '33',
			"Monbetsu" => '36',
		);

		$now_dtime = date("Ymd", strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9);
	  $arr_meetings['0304'] = substr($now_dtime, 2);

	  $meetings_obj = json_decode($meetings, true);
	  $arr_data = $meetings_obj["data"]["meetings"];
	  foreach($arr_data as $meeting_detail){
	    if($meeting_detail["venue"]["host_market"] == "japan_nar"){
	    	$venue_name = $meeting_detail["venue"]["venue_name"];
	    	if(isset($tracks[$venue_name])){
	    		$venue_key = $tracks[$venue_name];
	    		$arr_meetings[$venue_key] = $meeting_detail["meeting_id"];
	    	}
	    }
	  }
	  return $arr_meetings;
	}

	function __get_nar_venue_info($__venue_key) {
		  $arr_meetings = array();
		  $meetings = GetMeetingsFile();

		  $now_time = GetCurrentJapanTime();
		  $date_str = date("Y-m-d", strtotime($now_time));

			$tracks = array(
				"Obihiro" => '03',
				"Morioka" => '10',
				"Mizusawa" => '11',
				"Urawa" => '18',
				"Funabashi" => '19',
				"Oi" => '20',
				"Tokyo City Keiba" => '20',
				"Kawasaki" => '21',
				"Kanazawa" => '22',
				"Kasamatsu" => '23',
				"Nagoya" => '24',
				"Sonoda" => '27',
				"Himeji" => '28',
				"Fukuyama" => '30',
				"Kochi" => '31',
				"Saga" => '32',
				"Arao" => '33',
				"Monbetsu" => '36',
			);
			
		  $now_dtime = date("Ymd", strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9);
		  $arr_meetings['03'] = substr($now_dtime, 2);

		  $meetings_obj = json_decode($meetings, true);
		  $arr_data = $meetings_obj["data"]["meetings"];
		  foreach($arr_data as $meeting_detail){
		    if($meeting_detail["venue"]["host_market"] == "japan_nar"){
		    	$venue_name = $meeting_detail["venue"]["venue_name"];
		    	if(isset($tracks[$venue_name])){
		    		$venue_key = $tracks[$venue_name];
		    		if($venue_key == $__venue_key) {
		    			$venue_obj = new \stdClass;
		    			$venue_obj->venue_id = $meeting_detail["venue"]["venue_id"];
		    			$venue_obj->venue_name = $meeting_detail["venue"]["venue_name"];
		    			$venue_obj->venue_type = $meeting_detail["venue"]["venue_type"];
		    			$venue_obj->meeting_date = $meeting_detail["meeting_date"];
		    			return $venue_obj;
		    		}
		    	}
		    }
		  }
		  return false;
	}

	function __get_nar_meeting_ids_00() {
	  $arr_meetings = array();
	  $meetings = GetMeetingsFile();

	  $now_time = GetCurrentJapanTime();
	  $date_str = date("Y-m-d", strtotime($now_time));

		$tracks = array(
			"Obihiro" => '03',
			"Morioka" => '10',
			"Mizusawa" => '11',
			"Urawa" => '18',
			"Funabashi" => '19',
			"Oi" => '20',
			"Tokyo City Keiba" => '20',
			"Kawasaki" => '21',
			"Kanazawa" => '22',
			"Kasamatsu" => '23',
			"Nagoya" => '24',
			"Sonoda" => '27',
			"Himeji" => '28',
			"Fukuyama" => '30',
			"Kochi" => '31',
			"Saga" => '32',
			"Arao" => '33',
			"Monbetsu" => '36',
		);
		
		$now_dtime = date("Ymd", strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9);
	  $arr_meetings['03'] = substr($now_dtime, 2);

	  $meetings_obj = json_decode($meetings, true);
	  $arr_data = $meetings_obj["data"]["meetings"];
	  foreach($arr_data as $meeting_detail){
	    if($meeting_detail["venue"]["host_market"] == "japan_nar"){
	    	$venue_name = $meeting_detail["venue"]["venue_name"];
	    	if(isset($tracks[$venue_name])){
	    		$venue_key = $tracks[$venue_name];
	    		$arr_meetings[$venue_key] = $meeting_detail["meeting_id"];
	    	}
	    }
	  }
	  return $arr_meetings;
	}

	function __get_nar_meeting_id($__arr_meetings, $__race_name) {
		if(strlen($__race_name) == 1) $__race_name = sprintf("%02d", $__race_name);
		if(isset($__arr_meetings[$__race_name]))
			return $__arr_meetings[$__race_name];
	  return "";
	}

	function __get_nar_meeting_name($__race_name) {
		/*
		$__arr_meetings = array(
			'0304' => "Obihiro",
			'1006' => "Morioka",
			'1106' => "Mizusawa",
			'1813' => "Urawa",
			'1914' => "Funabashi",
			'2015' => "Oi",
			'2135' => "Kawasaki",
			'2218' => "Kanazawa",
			'2320' => "Kasamatsu",
			'2433' => "Nagoya",
			'2726' => "Sonoda",
			'2826' => "Himeji",
			'3028' => "Fukuyama",
			'3129' => "Kochi",
			'3230' => "Saga",
			'3331' => "Arao",
			'3601' => "Monbetsu",
		);
		*/
		$__arr_meetings = array(
			'03' => "Obihiro",
			'10' => "Morioka",
			'11' => "Mizusawa",
			'18' => "Urawa",
			'19' => "Funabashi",
			'20' => "Oi",
			'21' => "Kawasaki",
			'22' => "Kanazawa",
			'23' => "Kasamatsu",
			'24' => "Nagoya",
			'27' => "Sonoda",
			'28' => "Himeji",
			'30' => "Fukuyama",
			'31' => "Kochi",
			'32' => "Saga",
			'33' => "Arao",
			'36' => "Monbetsu",
		);
		if(isset($__arr_meetings[$__race_name]))
			return $__arr_meetings[$__race_name];
	  return "";
	}

	function __get_nar_meeting_name_00($__race_name) {
		$__race_name = sprintf("%02d", $__race_name);
		$__arr_meetings = array(
			'03' => "Obihiro",
			'10' => "Morioka",
			'11' => "Mizusawa",
			'18' => "Urawa",
			'19' => "Funabashi",
			'20' => "Oi",
			'21' => "Kawasaki",
			'22' => "Kanazawa",
			'23' => "Kasamatsu",
			'24' => "Nagoya",
			'27' => "Sonoda",
			'28' => "Himeji",
			'30' => "Fukuyama",
			'31' => "Kochi",
			'32' => "Saga",
			'33' => "Arao",
			'36' => "Monbetsu",
		);
		if(isset($__arr_meetings[$__race_name]))
			return $__arr_meetings[$__race_name];
	  return "";
	}

	function __get_meeting_id($__arr_meetings, $__race_name) {
	  foreach ($__arr_meetings as $key => $value) {
	    if(strpos($__race_name, $key) !== false)
	      return $value;
	  }
	  return "";
	}

	function millitime() {
	  $microtime = microtime();
	  $comps = explode(' ', $microtime);
	  return ".".sprintf('%03d', $comps[0] * 1000);
	}

	function __api_send2__($__api, $__method, $obj, $pre_log, $log_obj = false){
		$now_dtime = date("Ymd", strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9);
		if(isset($obj->meeting_id)) {
			if($obj->meeting_id == substr($now_dtime, 2)) {
				return '{"status":"OK"}';
			}
		}

		if(file_exists("api_send")){
			if($log_obj)
				__log_message2__(json_encode($log_obj), $pre_log);
			else
		  		__log_message2__(json_encode($obj), $pre_log);

		  	if(strpos($pre_log, "disqualified") !== false) return "";

		  $curl = curl_init();

		  curl_setopt_array($curl, array(
		    CURLOPT_URL => $__api,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 30,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => $__method,
		    CURLOPT_POSTFIELDS => json_encode($obj),
		    CURLOPT_HTTPHEADER => array(
		      "cache-control: no-cache",
		      "content-type: application/json",
		    ),
		  ));

		  $response = curl_exec($curl);
		  $err = curl_error($curl);
		  $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		  curl_close($curl);

		  $response .= " (Code:".$httpcode.")";

		  __log_message2__($response, $pre_log);

		  if ($err) {
		  //  echo "cURL Error #:" . $err;
		  } else {
		    return $response;
		  }

		  return "";
		}
		  return '{"status":"OK"}';
	}

	function __api_send__($__api, $__method, $obj, $__store = false){
		$now_dtime = date("Ymd", strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9);
		if(isset($obj->meeting_id)) {
			if($obj->meeting_id == substr($now_dtime, 2)) {
				return '{"status":"OK"}';
			}
		}

		if(file_exists("api_send")){
		  __log_message2__(json_encode($obj));

		  $curl = curl_init();

		  curl_setopt_array($curl, array(
		    CURLOPT_URL => $__api,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 30,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => $__method,
		    CURLOPT_POSTFIELDS => json_encode($obj),
		    CURLOPT_HTTPHEADER => array(
		      "cache-control: no-cache",
		      "content-type: application/json",
		    ),
		  ));

		  $response = curl_exec($curl);
		  $err = curl_error($curl);

		  curl_close($curl);

		  __log_message2__($response);

		  if ($err) {
		  //  echo "cURL Error #:" . $err;
		  } else {
		    return $response;
		  }

		  return "";
		}
		  return '{"status":"OK"}';
	}


	function PrepMarketData($meetingId, $raceNumber, $type, $data, $key = 'japan_nar')
	{
		$debug = json_encode($data);
		$japanNar = array();
		$japanNar[$type] = $data;

		$marketData = array();
		$marketData[$key] = $japanNar;

		$dataPoints = array();
		$dataPoints['event_number'] = $raceNumber;
		$dataPoints['meeting_id'] = $meetingId;
		$dataPoints['provider_market_data'] = $marketData;

		$debug = json_encode($dataPoints);
		return $dataPoints;
	}

	function multiRequest($data) {
	  $curly = array();
	  $result = array();
	 
	  $mh = curl_multi_init();
	 
	  foreach ($data as $id => $d) {
	 
	    $curly[$id] = curl_init();
 
	    $url = $d;

		curl_setopt_array($curly[$id], array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_HTTPHEADER => array(
			  "cache-control: no-cache",
			  "content-type: application/x-www-form-urlencoded",
			  "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36"
			),
		));
	 
	    curl_multi_add_handle($mh, $curly[$id]);
	  }
	 
	  $running = null;
	  do {
	    curl_multi_exec($mh, $running);
	  } while($running > 0);
	 
	  foreach($curly as $id => $c) {
	    $result[$id] = curl_multi_getcontent($c);
	    curl_multi_remove_handle($mh, $c);
	  }
	 
	  curl_multi_close($mh); 
	  return $result;
	}	

    function __send_slack_function($url, $message = "Betia Token is empty, please check about that.") {
        $req = new \stdClass;
        $req->text = $message;

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($req),
          CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Content-Type: application/json"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        return $response;
    }

	function __api_send_multiple__($__api, $__method, $objs, $__store = false){

		if(file_exists("api_send")){
		  $token_obj = __get_bet_token();

		  if(($token_obj) && (isset($token_obj->token)) && ($token_obj->token)) {
		  	@file_put_contents("token_status", "1");
		  } else {
		  	$token_status = @file_get_contents("token_status");
		  	if($token_status == 1) {
				__send_slack_function("https://hooks.slack.com/services/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");
		  	}

		  	@file_put_contents("token_status", "0");
		  	$__api = "https://staging.dw.xtradeiom.com/api/markets/";
		  }

		  $curly = array();
		  $result = array();
		  $mh = curl_multi_init();

		  __log_message__(json_encode($objs));

		  $arr_kind = ["japan_jra", "japan_nar"];
		  $arr_key = ["WIN", "PLC", "QNL", "QNP", "TRI", "TRO", "EXA"];

	  	foreach ($objs as $id => $obj) { 
	  		$now_dtime = date("Ymd", strtotime(date("Y-m-d H:i:s"))-date("Z")+3600*9);
	  		if($obj["meeting_id"] == substr($now_dtime, 2)) continue;
	  		/*
	  		foreach ($arr_kind as $kind_value) {
		  		if(isset($obj["provider_market_data"][$kind_value])){
		  			foreach ($arr_key as $key_value) {
		  				if(isset($obj["provider_market_data"][$kind_value][$key_value])){
		  					$test_obj = $obj["provider_market_data"][$kind_value][$key_value]["market"];
					  		//unset($test_obj["timestamp"]);
					  		$obj["provider_market_data"][$kind_value][$key_value] = $test_obj;
		  				}
		  			}
		  		}	
	  		}
			*/
	  		//file_put_contents("odds.sample.jspn", json_encode($obj));
	    	$curly[$id] = curl_init();
			$curl = curl_init();

		  curl_setopt_array($curly[$id], array(
		    CURLOPT_URL => $__api,
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => "",
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 30,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_HEADER => true,
		    CURLOPT_CUSTOMREQUEST => $__method,
		    CURLOPT_POSTFIELDS => json_encode($obj),
		    CURLOPT_HTTPHEADER => array(
		      "cache-control: no-cache",
		      "content-type: application/json",
		      "authorization: Bearer ".$token_obj->token,
		    ),
		  ));
		  curl_multi_add_handle($mh, $curly[$id]);
		}
		 
		  $running = null;
		  do {
		    curl_multi_exec($mh, $running);
		  } while($running > 0);
		 
		  foreach($curly as $id => $c) {
		    $result[$id] = curl_multi_getcontent($c);
		    curl_multi_remove_handle($mh, $c);
		  }
		  	 
		  curl_multi_close($mh); 

			__show_debug_info__("Result : ");
			__show_debug_info__(json_encode($result));

		__log_message__(json_encode($result));

		  return $result;
		}
		return true;
	}

	function __get_race_status($race_id, $first_check=false){
		$info = __get_race_results($race_id);
		$result = $info->result;
		$repay = $info->repay;

		if($first_check){
		  if(count($result)){
		    for($i=0; $i<count($result); $i++)
		    {
		      if($i >= 5) break;
		      __api_send__("https://staging.dw.xtradeiom.com/api/event_competitors/18095615", "PATCH", $result[$i]);
		    }
		    __api_send__("https://staging.dw.xtradeiom.com/api/markets_results/", "POST", $repay, true);
		  }
		}

		for($i=5; $i<count($result); $i++)
		{
		  __api_send__("https://staging.dw.xtradeiom.com/api/event_competitors/18095615", "PATCH", $result[$i]);
		}

		if(count($result) > 5){
		  
		  $final = new \stdClass;
		  $final->meeting_id = $result[0]->meeting_id;
		  $final->event_number = $result[0]->event_number;
		  $final->status = "FINAL";
		  __api_send__("https://staging.dw.xtradeiom.com/api/events/xxx/status", "PUT", $final);

		  $ret = new \stdClass;
		  $ret->check_fp5 = true;
		  $ret->check_pay = true;
		  $ret->check_fpall = true;
		  $ret->check_final = true;
		  $ret->datas = $result;
		  $ret->race_id = $race_id;
		  $ret->repay = $repay;
		  return $ret;
		}

		$ret = new \stdClass;
		$ret->check_fp5 = (count($result) > 0);
		$ret->check_pay = (count($result) > 0);
		$ret->check_fpall = false;
		$ret->check_final = false;
	    $ret->datas = $result;
	    $ret->race_id = $race_id;
	    $ret->repay = $repay;
		return $ret;
	}

	function __log_message2__($message, $pre_log = "app")
	{
		$time = GetCurrentJapanTime();
		$logFile = date("Ymd")."_".$pre_log.".log";

		if($pre_log != "app") $time .= millitime();
		file_put_contents($logFile, $time.' '.$message.PHP_EOL, FILE_APPEND | LOCK_EX);
	}

	function __log_message__($message)
	{
		$time = GetCurrentJapanTime();
		$logFile = date("Ymd")."_odds.log";
		file_put_contents($logFile, $time.' '.$message.PHP_EOL, FILE_APPEND | LOCK_EX);
	}

	function __show_debug_info__($__str)
	{
		echo $__str."<br /><br />\r\n\r\n";
		if (ob_get_level() > 0)
		{
			ob_flush();
		}
		flush();
	}
	function __check_dir__($__dir_name)
	{
		if(!file_exists($__dir_name)) mkdir($__dir_name);	
	}
	function __log_json_result($site_case, $data_case, $meeting_id, $race_number, $data)
	{
		__check_dir__("logs");
		__check_dir__("logs/".$site_case);
		__check_dir__("logs/".$site_case."/".$data_case);
		__check_dir__("logs/".$site_case."/".$data_case."/".$meeting_id);
		__check_dir__("logs/".$site_case."/".$data_case."/".$meeting_id."/".$race_number);
		$old_data = "logs/".$site_case."/".$data_case."/".$meeting_id."/".$race_number."/log.json";
		if(file_exists($old_data)) {
			$old_data_str = @file_get_contents($old_data);
			if(__get_until_values($old_data_str, "timestamp") == __get_until_values(json_encode($data), "timestamp")) return false;
		}

		file_put_contents($old_data, json_encode($data));
		file_put_contents("logs/".$site_case."/".$data_case."/".$meeting_id."/".$race_number."/".time().".json", json_encode($data));

		return true;
	}

	function __get_race_id($__json_file, $meeting_id, $event_number)
	{
		if(file_exists($__json_file)){
			$data = json_decode(@file_get_contents($__json_file));
			foreach ($data as $meetings) {
				if($meetings->meeting_id == $meeting_id){
					foreach ($meetings->races as $race_obj) {
						if($race_obj->id == $event_number)
							return $race_obj->race_id;
					}
				}
			}
		}
		if(file_exists("logs/backup/".$__json_file)){
			$data = json_decode(@file_get_contents("logs/backup/".$__json_file));
			foreach ($data as $meetings) {
				if($meetings->meeting_id == $meeting_id){
					foreach ($meetings->races as $race_obj) {
						if($race_obj->id == $event_number)
							return $race_obj->race_id;
					}
				}
			}
		}
		return "";
	}

	function __check_send_data_config($spec = false)
	{
	  $send_check = ["win" => 0, "plc" => 0, "qnl" => 0, "qnp" => 0, "exa" => 0, "tri" => 0, "tro" => 0];
	  if($spec){    
	    if(isset($spec->send_data)) {
	      $send_data_config = $spec->send_data;
	      if(isset($send_data_config["win"])) $send_check["win"] = $send_data_config["win"];
	      if(isset($send_data_config["plc"])) $send_check["plc"] = $send_data_config["plc"];
	      if(isset($send_data_config["qnl"])) $send_check["qnl"] = $send_data_config["qnl"];
	      if(isset($send_data_config["exa"])) $send_check["exa"] = $send_data_config["exa"];
	      if(isset($send_data_config["tro"])) $send_check["tro"] = $send_data_config["tro"];
	      if(isset($send_data_config["tri"])) $send_check["tri"] = $send_data_config["tri"];
	      if(isset($send_data_config["qnp"])) $send_check["qnp"] = $send_data_config["qnp"];
	    }
	  }
	  return $send_check;
	}

	function __check_log_data_config($spec = false)
	{
	  $log_check = ["win" => 0, "plc" => 0, "qnl" => 0, "qnp" => 0, "exa" => 0, "tri" => 0, "tro" => 0];
	  if($spec){    
	    if(isset($spec->log_data)) {
	      $log_data_config = $spec->log_data;
	      if(isset($log_data_config["win"])) $log_check["win"] = $log_data_config["win"];
	      if(isset($log_data_config["plc"])) $log_check["plc"] = $log_data_config["plc"];
	      if(isset($log_data_config["qnl"])) $log_check["qnl"] = $log_data_config["qnl"];
	      if(isset($log_data_config["exa"])) $log_check["exa"] = $log_data_config["exa"];
	      if(isset($log_data_config["tro"])) $log_check["tro"] = $log_data_config["tro"];
	      if(isset($log_data_config["tri"])) $log_check["tri"] = $log_data_config["tri"];
	      if(isset($log_data_config["qnp"])) $log_check["qnp"] = $log_data_config["qnp"];
	    }
	  }
	  return $log_check;
	}

	function __check_meeting_race_spec($meeting_id, $__race_id, $spec = false)
	{
	  if($spec){
	    if(isset($spec->meeting_id)){
	      if($spec->meeting_id != $meeting_id) return true;
	    }
	    if(isset($spec->event_number)){
	      if($spec->event_number != $__race_id) return true;
	    }
	  }
	  return false;
	}

	function __log_and_debug_and_send_data($meeting_id, $__race_id, $post_data, $log_check, $send_check, $__dir_name = "jra")
	{
		$comp_win = __log_json_result($__dir_name, "win", $meeting_id, $__race_id, $post_data->win);
		$comp_plc = __log_json_result($__dir_name, "plc", $meeting_id, $__race_id, $post_data->plc);
		$comp_qnl = __log_json_result($__dir_name, "qnl", $meeting_id, $__race_id, $post_data->qnl);
		$comp_exa = __log_json_result($__dir_name, "exa", $meeting_id, $__race_id, $post_data->exa);
		$comp_tro = __log_json_result($__dir_name, "tro", $meeting_id, $__race_id, $post_data->tro);
		$comp_tri = __log_json_result($__dir_name, "tri", $meeting_id, $__race_id, $post_data->tri);
		$comp_qnp = __log_json_result($__dir_name, "qnp", $meeting_id, $__race_id, $post_data->qnp);

		$send_datas = [];
		$send_datas[] = $post_data->win;

		__show_debug_info__("WIN: ".json_encode($post_data->win));
		//if(($log_check["win"] == 1) && $comp_win) __show_debug_info__("WIN: ".json_encode($post_data->win));
		if(($log_check["plc"] == 1) && $comp_plc) __show_debug_info__("PLC: ".json_encode($post_data->plc));
		if(($log_check["qnl"] == 1) && $comp_qnl) __show_debug_info__("QNL: ".json_encode($post_data->qnl));
		if(($log_check["qnp"] == 1) && $comp_qnp) __show_debug_info__("QNP: ".json_encode($post_data->qnp));
		if(($log_check["exa"] == 1) && $comp_exa) __show_debug_info__("EXA: ".json_encode($post_data->exa));
		if(($log_check["tro"] == 1) && $comp_tro) __show_debug_info__("TRO: ".json_encode($post_data->tro));
		if(($log_check["tri"] == 1) && $comp_tri) __show_debug_info__("TRI: ".json_encode($post_data->tri));

		if(($send_check["win"] == 1) && $comp_win) 
			if($__dir_name == "jra")
				__api_send_multiple__("https://api.betia.co/providers/japan_jra/markets", "POST", $send_datas);
			else
				__api_send_multiple__("https://api.betia.co/providers/japan_nar/markets", "POST", $send_datas);

		$send_datas2 = [];
/*
		if($post_data->plc) if(($send_check["plc"] == 1) && $comp_plc) $send_datas2[] = $post_data->plc;
		if($post_data->exa) if(($send_check["exa"] == 1) && $comp_exa) $send_datas2[] = $post_data->exa;
		if($post_data->qnl) if(($send_check["qnl"] == 1) && $comp_qnl) $send_datas2[] = $post_data->qnl;
		if($post_data->qnp) if(($send_check["qnp"] == 1) && $comp_qnp) $send_datas2[] = $post_data->qnp;
		if($post_data->tri) if(($send_check["tri"] == 1) && $comp_tri) $send_datas2[] = $post_data->tri;
		if($post_data->tro) if(($send_check["tro"] == 1) && $comp_tro) $send_datas2[] = $post_data->tro;
*/
		if($comp_plc) $send_datas2[] = $post_data->plc;
		if($comp_exa) $send_datas2[] = $post_data->exa;
		if($comp_qnl) $send_datas2[] = $post_data->qnl;
		if($comp_qnp) $send_datas2[] = $post_data->qnp;
		if($comp_tri) $send_datas2[] = $post_data->tri;
		if($comp_tro) $send_datas2[] = $post_data->tro;

		if(count($send_datas2) > 0)			
			if($__dir_name == "jra")
				__api_send_multiple__("https://api.betia.co/providers/japan_jra/markets", "POST", $send_datas2);
			else
				__api_send_multiple__("https://api.betia.co/providers/japan_nar/markets", "POST", $send_datas2);

	}

	function __log_and_debug_and_send_data_4ext($meeting_id, $__race_id, $post_data, $type, $__dir_name = "jra")
	{
		$comp = __log_json_result($__dir_name, $type, $meeting_id, $__race_id, $post_data);

		$send_datas = [];
		$send_datas[] = $post_data;

		if($comp)
			if($__dir_name == "jra")
				__api_send_multiple__("https://api.betia.co/providers/japan_jra/markets", "POST", $send_datas);
			else
				__api_send_multiple__("https://api.betia.co/providers/japan_nar/markets", "POST", $send_datas);

	}

	function __log_and_debug_and_send_data_origin($meeting_id, $__race_id, $post_data, $log_check, $send_check, $__dir_name = "jra")
	{
		$comp_win = __log_json_result($__dir_name, "win", $meeting_id, $__race_id, $post_data->win);
		$comp_plc = __log_json_result($__dir_name, "plc", $meeting_id, $__race_id, $post_data->plc);
		$comp_qnl = __log_json_result($__dir_name, "qnl", $meeting_id, $__race_id, $post_data->qnl);
		$comp_exa = __log_json_result($__dir_name, "exa", $meeting_id, $__race_id, $post_data->exa);
		$comp_tro = __log_json_result($__dir_name, "tro", $meeting_id, $__race_id, $post_data->tro);
		$comp_tri = __log_json_result($__dir_name, "tri", $meeting_id, $__race_id, $post_data->tri);
		$comp_qnp = __log_json_result($__dir_name, "qnp", $meeting_id, $__race_id, $post_data->qnp);

		$send_datas = [];
		$send_datas[] = $post_data->win;

		__show_debug_info__("WIN: ".json_encode($post_data->win));
		//if(($log_check["win"] == 1) && $comp_win) __show_debug_info__("WIN: ".json_encode($post_data->win));
		if(($log_check["plc"] == 1) && $comp_plc) __show_debug_info__("PLC: ".json_encode($post_data->plc));
		if(($log_check["qnl"] == 1) && $comp_qnl) __show_debug_info__("QNL: ".json_encode($post_data->qnl));
		if(($log_check["exa"] == 1) && $comp_qnp) __show_debug_info__("QNP: ".json_encode($post_data->qnp));
		if(($log_check["tro"] == 1) && $comp_exa) __show_debug_info__("EXA: ".json_encode($post_data->exa));
		if(($log_check["tri"] == 1) && $comp_tro) __show_debug_info__("TRO: ".json_encode($post_data->tro));
		if(($log_check["qnp"] == 1) && $comp_tri) __show_debug_info__("TRI: ".json_encode($post_data->tri));

		if(($send_check["win"] == 1) && $comp_win) __api_send_multiple__("https://staging.dw.xtradeiom.com/api/markets/", "POST", $send_datas);

		$send_datas2 = [];

		if($post_data->plc) if(($send_check["plc"] == 1) && $comp_plc) $send_datas2[] = $post_data->plc;
		if($post_data->exa) if(($send_check["exa"] == 1) && $comp_exa) $send_datas2[] = $post_data->exa;
		if($post_data->qnl) if(($send_check["qnl"] == 1) && $comp_qnl) $send_datas2[] = $post_data->qnl;
		if($post_data->qnp) if(($send_check["qnp"] == 1) && $comp_qnp) $send_datas2[] = $post_data->qnp;
		if($post_data->tri) if(($send_check["tri"] == 1) && $comp_tri) $send_datas2[] = $post_data->tri;
		if($post_data->tro) if(($send_check["tro"] == 1) && $comp_tro) $send_datas2[] = $post_data->tro;

		if(count($send_datas2) > 0)
			__api_send_multiple__("https://staging.dw.xtradeiom.com/api/markets/", "POST", $send_datas2);

	}

	function __check_bet_token_info($refresh_limit = 15)
	{
		$check_regen = true;
		$check_refresh = false;

		if(file_exists("bet_token")){
			$token_live = filemtime("bet_token") + 20 * 60 - time();
			if($token_live > 60){
				$check_regen = false;
			} else if($token_live > $refresh_limit){
				$check_regen = false;
				$check_refresh = true;
			}
		}
		if($check_regen) {
			__generate_new_bet_token();
		} else if($check_refresh) {
			__refresh_bet_token();
		}
	}

	function __get_bet_token()
	{
		__check_bet_token_info();

		$token_data = @file_get_contents("bet_token");
		$token_obj = json_decode($token_data);
		$token_obj->live = filemtime("bet_token") + 20 * 60 - time();

		return $token_obj;
	}

	function __refresh_bet_token()
	{
		$token_data = @file_get_contents("bet_token");
		$token_obj = json_decode($token_data);

		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://auth.betia.co/auth/refresh",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "authorization: Bearer ".$token_obj->token,
		    "content-type: application/json"
		  ),
		));

		$response = curl_exec($curl);

		file_put_contents("bet_token", $response);

		__show_debug_info__("Refresh Betia Token");
	}

	function __generate_new_bet_token()
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://auth.betia.co/auth",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => "{\"email\":\"scraper@betia.co\",\"password\":\"L9x?E63h4H=6\"}",
		  CURLOPT_HTTPHEADER => array(
		    "content-type: application/json",
		  ),
		));

		$response = curl_exec($curl);

		file_put_contents("bet_token", $response);

		__show_debug_info__("New Betia Token: ".$response);
	}

	function patch_array($arr) {
	  $max_num = 0;
	  foreach ($arr as $key => $value) {
	    if($key > $max_num) $max_num = $key;
	  }
	  for($i=1; $i<$max_num; $i++) {
	    if(isset($arr[$i])) {
	    	if($arr[$i] < 0) $arr[$i] = 0;
	    } else $arr[$i] = 0;
	  }
	  return $arr;
	}

	// Modules for FP & Payout Feature
	function __betia_race_close( $meeting_id, $event_number, $status = "CLOSED") {
		$obj = new \stdClass;
		$obj->meeting_id = $meeting_id;
		$obj->event_number = $event_number;
		$obj->status = $status;
		if($obj->status == "CLOSED") $obj->force = true;

		$ret = __api_send__("https://staging.dw.xtradeiom.com/api/events/xxx/status", "PUT", $obj);
		$ret = json_decode($ret);
		return $ret;
	}

	function __check_race_status($meeting_id, $event_number, $case="keiba") {
		$curTime = GetCurrentJapanTimeStamp();
		$log_file = date("Ymd", $curTime)."_".$case."_app_status.log";

		$data = @file_get_contents($log_file);
		$ret = new \stdClass;
		$ret->meeting_id = $meeting_id;
		$ret->event_number = $event_number;
		$ret->closed = false;
		$ret->fp5 = false;
		$ret->is_first = false;
		$ret->fp = false;
		$ret->payout = false;
		$ret->final = false;

		if($data) {
			$arr_status = json_decode($data);
			foreach ($arr_status as $race_status) {
				if(($race_status->meeting_id == $meeting_id) && ($race_status->event_number == $event_number))
					return $race_status;
			}
		}
		return $ret;
	}

	function __update_race_status($meeting_id, $event_number, $case="keiba", $properties=array()) {
		$curTime = GetCurrentJapanTimeStamp();
		$log_file = date("Ymd", $curTime)."_".$case."_app_status.log";

		$data = @file_get_contents($log_file);
		$arr_status = [];
		$ret = new \stdClass;
		$ret->meeting_id = $meeting_id;
		$ret->event_number = $event_number;
		foreach($properties as $property) {
			$ret->$property = true;
		}
		$check_noexist = true;

		if($data) {
			$arr_status = json_decode($data);
			for ($i=0; $i<count($arr_status); $i++) {
				$race_status = $arr_status[$i];
				if(($race_status->meeting_id == $meeting_id) && ($race_status->event_number == $event_number)) {
					$check_noexist = false;
					foreach($properties as $property) {
						$arr_status[$i]->$property = true;
					}
				}
			}
		}

		if($check_noexist) {
			$ret->final = false;
			$arr_status[] = $ret;
		}
		@file_put_contents($log_file, json_encode($arr_status));
	}

	function __betia_race_after_processing($meeting_id, $event_number, $status, $site_case_name="keiba") {
		$race_id = $meeting_id.$race_id;
		$info = __get_race_results($race_id);
		$result = $info->result;
		$repay = $info->repay;
		$check_first_send = false;
		$limit_5fp = 5;
		if($site_case_name == "keiba") $limit_5fp = 4;

		$first_check = true;
		if(isset($status->is_first)) $first_check = false;
		if($first_check){
			if(count($result)){
		    	__log_json_result($site_case_name, "5fp", $meeting_id, $event_number, $result);
		    	__log_json_result($site_case_name, "payout", $meeting_id, $event_number, $repay);

				for($i=0; $i<count($result); $i++)
				{
					if($i >= $limit_5fp) break;
					__api_send__("https://staging.dw.xtradeiom.com/api/event_competitors/18095615", "PATCH", $result[$i]);
				}
				$repay_out = __api_send__("https://staging.dw.xtradeiom.com/api/markets_results/", "POST", $repay, true);
				if($repay_out){
					$repay_out = json_decode($repay_out);
					if($repay_out->status == "OK"){
						$check_first_send = true;
						__log_json_result($site_case_name, "payout_check", $meeting_id, $event_number, $repay);
					}
				}
			}
		}

		for($i=$limit_5fp; $i<count($result); $i++)
		{
			__api_send__("https://staging.dw.xtradeiom.com/api/event_competitors/18095615", "PATCH", $result[$i]);
		}

		if(count($result) > $limit_5fp){
			__log_json_result($site_case_name, "fp", $meeting_id, $event_number, $result);
			__betia_race_close($meeting_id, $event_number, "FINAL");

			__update_race_status($meeting_id, $event_number, $site_case_name, ["fp5", "fp", "payout", "final"]);
			return true;
		}
		if($check_first_send && (count($result) > 0)) {
			__update_race_status($meeting_id, $event_number, $site_case_name, ["fp5", "payout", "is_first"]);
		}
		return true;
	}
?>