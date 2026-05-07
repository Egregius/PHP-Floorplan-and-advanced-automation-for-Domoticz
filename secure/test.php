<?php
header('Access-Control-Allow-Origin: *');
echo '<pre>';
$start=microtime(true);
$user='test';
require 'functions.php';
//require '/var/www/authentication.php';
$d=fetchdata();
//$startloop=microtime(true);
//$d['time']=$startloop;
//$db = Database::getInstance();

$res = lmsRequest("slim.request", ["", ["serverstatus", 0, 100]]);

echo '<pre>'; print_r($res); echo '</pre>';

//if(isPlayerOnline('58:7a:62:60:c5:b2')) echo 'ja'; else echo 'nee';

function isPlayerOnline($speaker_mac) {
    $res = lmsRequest("slim.request", ["", ["serverstatus", 0, 100]]);
    if (!isset($res['result']['players_loop'])) return false;

    foreach ($res['result']['players_loop'] as $player) {
        if ($player['playerid'] === $speaker_mac) {
            return ($player['isplaying'] == 1 || $player['connected'] == 1);
        }
    }
    return false;
}


function lmsUnsync($speaker_mac) {
    return lmsRequest("slim.request", [
        $speaker_mac, 
        ["sync", "-"] // De "-" vertelt LMS om de speler uit elke groep te halen
    ]);
}


function lmsJoinLiving($new_speaker_mac) {
    $living_mac = "58:7a:62:60:c5:b2"; // De MAC van je bekabelde Living

    // Het commando 'sync' vertelt de nieuwe speaker om de master te volgen
    return lmsRequest("slim.request", [
        $new_speaker_mac, 
        ["sync", $living_mac]
    ]);
}    
function lmsRequest($method, $params) {
    $host = '192.168.2.6'; // IP van je Debian CT
    $port = 9000;

    $data = json_encode([
        "id" => 1,
        "method" => "slim.request",
        "params" => $params
    ]);

    $ch = curl_init("http://$host:$port/jsonrpc.js");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}



function getMaQueueStatus() {
    $ch = curl_init();
    // We bevragen de MA server direct op poort 8095
    curl_setopt($ch, CURLOPT_URL, 'http://192.168.2.26:8095/api/players/queue/syncgroup_xpctkjzj');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // MA API heeft vaak geen Bearer token nodig als je lokaal zit, 
    // maar check je MA instellingen als je een 401 krijgt.
    
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    // In de MA Queue objecten zit vaak de 'metadata' van de huidige stream
    // of de naam van de actieve lijst.
    return $data;
}


function hassgetgroep() {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.26:8123/api/states/media_player.groep');
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer '.hasstoken()));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	curl_close($ch);
	return $response;
}
function hassplaylist($playlist) {
    $ch = curl_init();
    $payload = json_encode([
        "entity_id" => "media_player.groep",
        "media_id" => $playlist,
        "media_type" => "playlist",
        "enqueue" => "replace_next"
    ]);
    curl_setopt($ch, CURLOPT_URL, 'http://192.168.2.26:8123/api/services/music_assistant/play_media');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . hasstoken()
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) {
        echo "Foutcode: " . $httpCode . " - Respons: " . $data;
    } else {
        echo "Succes!";
    }
}
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
	$db,
	$nasip,
	$kodiurl,
	$kodiurl2,
	$dsapikey,
	$owappid,
	$openuv,
	$owid,
	$lat,
	$lon,
	$vurl,
	$weekend,
	$memcache,
	$homes,
	$cameratoken,

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
