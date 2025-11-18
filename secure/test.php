<?php
header('Access-Control-Allow-Origin: *');
echo '<pre>';
$start=microtime(true);
require 'functions.php';
require '/var/www/authentication.php';
$d=fetchdata(0,'test.php');
$startloop=microtime(true);
$d['time']=$startloop;
$db = dbconnect();

$stamp = date("Y-m-d H:i:s", (int)$d['time'] - 86400*14);
$cols = [
    'living' => 'living_temp',
    'kamer' => 'kamer_temp',
    'badkamer' => 'badkamer_temp',
    'alex' => 'alex_temp',
    'buiten' => 'buiten_temp',
    'waskamer' => 'waskamer_temp'
];
$query = "SELECT ";
$selects = [];
foreach($cols as $col => $jsonKey) {
    $selects[] = "MIN($col) AS {$col}_min, AVG($col) AS {$col}_avg, MAX($col) AS {$col}_max";
}
$query .= implode(", ", $selects);
$query .= " FROM temp WHERE stamp > :stamp";
$stmt = $db->prepare($query);
$stmt->execute([':stamp' => $stamp]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$thermo_hist = [];
foreach($cols as $col => $jsonKey) {
    $thermo_hist[$jsonKey] = [
        'min' => isset($row["{$col}_min"]) ? round((float)$row["{$col}_min"],1) : null,
        'avg' => isset($row["{$col}_avg"]) ? round((float)$row["{$col}_avg"],1) : null,
        'max' => isset($row["{$col}_max"]) ? round((float)$row["{$col}_max"],1) : null,
    ];
}
echo setCache('thermo_hist',json_encode($thermo_hist));



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