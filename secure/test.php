<?php
header('Access-Control-Allow-Origin: *');
echo '<pre>';
$start=microtime(true);
$user='test';
//require 'functions.php';
//require '/var/www/authentication.php';
//$d=fetchdata();
//$startloop=microtime(true);
//$d['time']=$startloop;
//$db = Database::getInstance();



define('WIIM_IP', '192.168.2.9');
define('BOSE_IP', '192.168.2.101');
define('BELL_URL', 'http://192.168.2.2/sounds/doorbell.mp3');

// Basisfunctie voor de WiiM
function Wiim(string $cmd) {
    $url = "https://" . WIIM_IP . "/httpapi.asp?command=$cmd";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Functie voor reguliere Bose API (poort 8090)
function bosePost(string $endpoint, string $xml) {
    $url = "http://" . BOSE_IP . ":8090/" . $endpoint;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/xml']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// Functie voor DLNA/UPnP SOAP-calls naar Bose (poort 8091)
function boseDlnaCall(string $action, string $arguments) {
    $url = "http://" . BOSE_IP . ":8091/MediaRenderer/AVTransport/Control";
    
    $xml = '<?xml version="1.0" encoding="utf-8"?>
    <s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
        <s:Body>
            <u:' . $action . ' xmlns:u="urn:schemas-upnp-org:service:AVTransport:1">
                <InstanceID>0</InstanceID>
                ' . $arguments . '
            </u:' . $action . '>
        </s:Body>
    </s:Envelope>';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: text/xml; charset="utf-8"',
        'SOAPACTION: "urn:schemas-upnp-org:service:AVTransport:1#' . $action . '"'
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

// --- HOOFDSCRIPT ---

// 1. Check of de WiiM speelt, zo ja: pauzeer Spotify netjes
$statusJson = Wiim("getPlayerStatus");
$status = json_decode($statusJson, true);
$wasPlaying = ($status && isset($status['status']) && $status['status'] === 'play');

if ($wasPlaying) {
    Wiim("setPlayerCmd:pause");
}

// 2. Zet het volume op de Bose alvast goed via de basisfunctie
$volumeXml = @file_get_contents("http://" . BOSE_IP . ":8090/volume");
$currentVolume = 17;
if ($volumeXml) {
    $xmlObj = simplexml_load_string($volumeXml);
    if ($xmlObj && isset($xmlObj->actualvolume)) {
        $currentVolume = (int)$xmlObj->actualvolume;
    }
}
$targetVolume = ($currentVolume < 60) ? 60 : $currentVolume;
//bosePost('volume', "<volume>{$targetVolume}</volume>");

// 3. DLNA: Geef de deurbel URL door aan de SoundTouch renderer (Poort 8091)
$argsSet = '<CurrentURI>' . htmlspecialchars(BELL_URL) . '</CurrentURI><CurrentURIMetaData></CurrentURIMetaData>';
boseDlnaCall('SetAVTransportURI', $argsSet);

// 4. DLNA: Start het afspelen direct
$argsPlay = '<Speed>1</Speed>';
boseDlnaCall('Play', $argsPlay);

// 5. Wacht exact de tijd dat de deurbel nodig heeft om af te spelen
sleep(9);

// 6. Sla de deurbel stream plat via DLNA Stop
boseDlnaCall('Stop', '');

// 7. Dwing de Bose via de reguliere API terug naar de AUX input (WiiM)
bosePost('key', '<key state="press" sender="GAV">AUX_INPUT</key>');
bosePost('key', '<key state="release" sender="GAV">AUX_INPUT</key>');

// 8. Herstel het volume naar het oude niveau
bosePost('volume', "<volume>{$currentVolume}</volume>");

// 9. Start Spotify op de WiiM weer op
if ($wasPlaying) {
    Wiim("setPlayerCmd:resume");
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
