<?php
header('Access-Control-Allow-Origin: *');
echo '<pre>';
$start=microtime(true);
require 'functions.php';
require '/var/www/authentication.php';
$d=fetchdata(0,'test.php');

//echo hassget();
//echo hassservices();
//hass('light','turn_off','light.bureel1');
//hass('light','turn_on','light.bureel1','"brightness_pct":20,"color_temp_kelvin":3200');
//hassinput('media_player','select_source','media_player.lgtv','HDMI 4');
//hass('input_button','press','input_button.wakeipad');
//hass('backup','create_automatic');
//hass('script', 'turn_on', 'script.alles_uitschakelen_via_label_uit_bij_weg');

//	hassopts('xiaomi_aqara', 'play_ringtone', '', ['gw_mac' => '34ce008d3f60','ringtone_id' => 8,'ringtone_vol' => 50]);
echo kodi('{"jsonrpc": "2.0","method": "GUI.ActivateScreensaver","id": 1}');

//print_r($d);


//echo hass('xiaomi_aqara','play_ringtone',null,['gw_mac'=>'34ce008d3f60','ringtone_id'=>2,'ringtone_vol'=>20]);

//file_get_contents('http://127.0.0.1:8080/json.htm?type=command&param=switchlight&idx=745&switchcmd=Set%20Level&level=90&passcode=');
//hassinput('media_player','select_source','media_player.lgtv','HDMI 4');

/*				
$lights=array(
	1=>'eettafel',
	2=>'bureel',
	3=>'zithoek'
);
for($x=1;$x<=10000;$x++) {
	if ($x%4==0) $lamp=4;
	elseif($x%3==0) $lamp=3;
	elseif($x%2==0) $lamp=2;
	else $lamp=1;
	if ($x%12==0) $device='eettafel';
	elseif ($x%11==0) $device='eettafel';
	elseif ($x%10==0) $device='eettafel';
	elseif ($x%9==0) $device='eettafel';
	elseif ($x%8==0) $device='zithoek';
	elseif ($x%7==0) $device='zithoek';
	elseif ($x%6==0) $device='zithoek';
	elseif ($x%5==0) $device='zithoek';
	elseif ($x%4==0) $device='bureel';
	elseif ($x%3==0) $device='bureel';
	elseif ($x%2==0) $device='bureel';
	elseif ($x%1==0) $device='bureel';
	$device=$device.$lamp;
	$level=rand(0,100);
	$temp=rand(2202,6535);
	sl($device, $level , basename(__FILE__).':'.__LINE__, true, $temp);	
	echo $device.' '.$level.' '.$temp.'<br>';
	usleep(7692);
}
*/
echo '</pre>';
echo '<hr>Time:'.number_format(((microtime(true)-$start)*1000), 6);
unset(
	$_COOKIE,
	$_ENV,
	$_GET,
	$_POST,
	$_FILES,
	$_SERVER,
	$d,
	$dow,
	$start,
	$telegrambot,
	$cookie,
	$telegramchatid1,
	$telegramchatid2,
	$eendag,
	$googleTTSAPIKey,
	$log,
	$page,
	$udevice,
	$local,
	$user,
	$ipaddress,
	$timediff,
	$domainname,
	$domoticzurl,
	$dbname,
	$dbuser,
	$dbpass,
	$_SESSION,
	$zwaveidx,
	$db,
	$nasip,
	$kodiurl,
	$dsapikey,
	$owappid,
	$openuv,
	$owid,
	$lat,
	$lon,
	$vurl,
	$weekend
);
echo '<hr><hr><hr><pre>';print_r(GET_DEFINED_VARS());echo '</pre>';



function Human_kb($bytes,$dec=2) {
	$size=array('kb','Mb','Gb');
	$factor=floor((strlen($bytes)-1)/3);
	return sprintf("%.{$dec}f", $bytes/pow(1000, $factor)).@$size[$factor];
}



/*-------------------------------------------------*/
//require_once 'gcal/google-api-php-client/vendor/autoload.php';
//NL('Druk 6 voor Geert, 7 voor Peter, 8 voor Sandro, 9 voor Gie.');
//FR('Appuyez 6 pour Geert, 7 pour Peter, 8 pour Sandro, 9 pour Guy.');
function NL($sound){
	global $googleTTSAPIKey;
	require_once 'gcal/google-api-php-client/vendor/autoload.php';
	$client=new GuzzleHttp\Client();
	$requestData=['input'=>['text'=>$sound],'voice'=>['languageCode'=>'nl-NL','name'=>'nl-NL-Wavenet-B'],'audioConfig'=>['audioEncoding'=>'LINEAR16','pitch'=>0.00,'speakingRate'=>0.92,'effectsProfileId' => 'telephony-class-application']];
	try {
		$response=$client->request('POST', 'https://texttospeech.googleapis.com/v1beta1/text:synthesize?key='.$googleTTSAPIKey, ['json'=>$requestData]);
		$fileData=json_decode($response->getBody()->getContents(), true);
		$audio=base64_decode($fileData['audioContent']);
		if(strlen($audio)>10) {
			file_put_contents('/var/www/html/3CX/'.$sound.'.wav', $audio);
		}
	} catch (Exception $e) {
		exit('Something went wrong: ' . $e->getMessage());
	}
}
function FR($sound){
	global $googleTTSAPIKey;
	require_once 'gcal/google-api-php-client/vendor/autoload.php';
	$client=new GuzzleHttp\Client();
	$requestData=['input'=>['text'=>$sound],'voice'=>['languageCode'=>'fr-FR','name'=>'fr-FR-Wavenet-B'],'audioConfig'=>['audioEncoding'=>'LINEAR16','pitch'=>0.00,'speakingRate'=>0.92,'effectsProfileId' => 'telephony-class-application']];
	try {
		$response=$client->request('POST', 'https://texttospeech.googleapis.com/v1beta1/text:synthesize?key='.$googleTTSAPIKey, ['json'=>$requestData]);
		$fileData=json_decode($response->getBody()->getContents(), true);
		$audio=base64_decode($fileData['audioContent']);
		if(strlen($audio)>10) {
			file_put_contents('/var/www/html/3CX/'.$sound.'.wav', $audio);
		}
	} catch (Exception $e) {
		exit('Something went wrong: ' . $e->getMessage());
	}
}
/*---------------------------*/