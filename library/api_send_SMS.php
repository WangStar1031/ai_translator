<?php
require __DIR__ . '/twilio-php-master/Twilio/autoload.php';

// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;
$isLocal = false;
if(@file_get_contents(__DIR__."/localhost")){
	echo "Local Computer.";
	$isLocal = true;
}
function sendSMS2Me($_body){
	$sid = 'AC9ae4b531096141d62284f2937c34459c';
	$token = '71958f8d516569b2c944c93fa4b51a03';
	$client = new Client($sid, $token);

	$toNumber = '+8613130646607';
	// Use the client to do fun stuff like send text messages!
	$client->messages->create(
		// the number you'd like to send the message to
		$toNumber,
	    array(
	        // A Twilio phone number you purchased at twilio.com/console
	        'from' => '+15802385722',
	        // the body of the text message you'd like to send
	        'body' => $_body
	    )
	);
}
function sendSMS4Twilio($_body){
	// Your Account SID and Auth Token from twilio.com/console
	global $isLocal;
	if( $isLocal){
		sendSMS2Me($_body);
		return;
	}
	$sid = 'ACeb645d3093ef267dc8e0cb825b7884be';
	$token = '6834b5c8300e95e91d543644eaa5e782';
	$client = new Client($sid, $token);

	$toNumber = '+819083466576';
	// Use the client to do fun stuff like send text messages!
	$client->messages->create(
	    // the number you'd like to send the message to
	    $toNumber,
	    array(
	        // A Twilio phone number you purchased at twilio.com/console
	        'from' => '+14012989544',
	        // the body of the text message you'd like to send
	        'body' => $_body
	    )
	);

}
function sendSMSByCatagory($_cat, $_data=""){
	$_body = "";
	switch ($_cat) {
		case 'NEW_SENTENCE':
			$_body = "New Sentence arrived.\n\n";
			$_url = "http://dataminer.jts.ec/ai/translation/api_title_trans.php?TITLE=" . urlencode($_data);
			$_body .= $_url;
			sendSMS4Twilio($_body);
			break;
		case 'JRA_NEWS':
			$_body = "JRA News Updated.\n\n";
			$_url = "http://dataminer.jts.ec/ai/translation/api_title_trans.php?TITLE=" . urlencode($_data);
			$_body .= $_url;
			sendSMS4Twilio($_body);
			break;
		default:
			break;
	}
}
$sms_contents = "";
if( isset($_POST['sms_contents']))$sms_contents = $_POST['sms_contents'];
if( isset($_GET['sms_contents']))$sms_contents = $_GET['sms_contents'];
if( $sms_contents != ""){
	sendSMS4Twilio($sms_contents);
}

?>