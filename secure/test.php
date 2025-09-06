<?php
header('Access-Control-Allow-Origin: *');
echo '<pre>';
$start=microtime(true);
require 'functions.php';
require '/var/www/authentication.php';
$d=fetchdata(0,'test.php');

$ha_url = 'http://192.168.2.26:8123';
$ha_token = 'Bearer '.hasstoken();
$base_topic = 'homeassistant';
foreach ($d as $device => $i) {
	if (!isset($i['dt'])) continue;
	$entity_id = null;
	$to_publish = [];
	if ($i['dt'] === 'hsw') {
		$entity_id = "switch.$device";
		$url = "$ha_url/api/states/$entity_id";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $ha_token"]);
		$result = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($result, true);
		if (!isset($data['state'])) {
			lg("Fout: kon status van $entity_id niet ophalen");
			continue;
		}
		list($domain, $object_id) = explode('.', $entity_id);
		$state = ucfirst($data['state']);
		if ($state!=$i['s']) $to_publish[] = [
			'topic' => "$base_topic/$domain/$object_id/state",
			'payload' => $state
		];
	} elseif ($i['dt'] === 'hd') {
		$entity_id = "light.$device";
		$url = "$ha_url/api/states/$entity_id";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $ha_token"]);
		$result = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($result, true);
		$attributes = $data['attributes'] ?? [];
		if (!array_key_exists('brightness', $attributes)) {
			lg("Geen brightness attribuut voor $entity_id");
			continue;
		}
		list($domain, $object_id) = explode('.', $entity_id);
		$brightness = $attributes['brightness'] ?? 0;
		$brightness=round((float)$brightness / 2.55);
		if ($brightness!=$i['s']) $to_publish[] = [
			'topic' => "$base_topic/$domain/$object_id/brightness",
			'payload' => $brightness
		];
	} else continue;
	usleep(50000);
	foreach ($to_publish as $pub) {
		$payload = [
			'topic' => $pub['topic'],
			'payload' => $pub['payload'],
			'retain' => true
		];
		$ch = curl_init("$ha_url/api/services/mqtt/publish");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: $ha_token",
			"Content-Type: application/json"
		]);
		$response = curl_exec($ch);
		curl_close($ch);
		lg("Herpublicatie: $entity_id → {$pub['payload']} → {$pub['topic']}");
		usleep(50000);
	}
}

//hassopts('xiaomi_aqara', 'play_ringtone', '', ['gw_mac' => '34ce008d3f60','ringtone_id' => 0,'ringtone_vol' => 10]);
//shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php" > /dev/null 2>/dev/null &');

//sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
//waarschuwing('test');
//echo daikinstatus('living');
//hassnotify('Titel', 'Bericht');


//echo hassget();
//echo hassservices();
//hass('light','turn_off','light.bureel1');
//hass('light','turn_on','light.bureel1','"brightness_pct":20,"color_temp_kelvin":3200');
//hassinput('media_player','select_source','media_player.lgtv','HDMI 4');
//hass('input_button','press','input_button.wakeipad');
//hass('backup','create_automatic');

//hass('script', 'turn_on', 'script.alles_uitschakelen');

//	hassopts('xiaomi_aqara', 'play_ringtone', '', ['gw_mac' => '34ce008d3f60','ringtone_id' => 8,'ringtone_vol' => 50]);
//echo kodi('{"jsonrpc": "2.0","method": "GUI.ActivateScreensaver","id": 1}');
//sw('shieldpower', 'On','',true);
//hassrepublishEntityState('switch.lampkast');

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