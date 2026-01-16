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

function randomValue() {
    return rand(0, 1000);
}
$keys=[];
$l='aaaa';
for($x=0;$x<1000;$x++) {
	$keys[]=$l;
	$l++;
}

// ----------------------------
// 1️⃣ Array-model
// ----------------------------
$arrayData = [];
foreach ($keys as $k) {
    $arrayData[$k] = [
        'n'  => 'dev_'.$k,
        's'  => randomValue(),
        't'  => randomValue(),
        'd'  => randomValue(),
        'm'  => 'meta'.$k,
        'i'  => randomValue(),
        'p'  => randomValue(),
        'rt' => randomValue(),
        'f'  => randomValue(),
    ];
}

// Benchmark array reads
$start = microtime(true);
for ($i=0; $i<10000; $i++) {
    foreach ($keys as $k) {
        $val = $arrayData[$k]['s']; // voorbeeld toegang
        $val2 = $arrayData[$k]['t'];
    }
}
$end = microtime(true);
echo "Array model: ".(($end-$start)*1000)." ms\n";

// ----------------------------
// 2️⃣ Object-model
// ----------------------------
$objectData = [];
foreach ($keys as $k) {
    $dev = new Device();
    $dev->n  = 'dev_'.$k;
    $dev->s  = randomValue();
    $dev->t  = randomValue();
    $dev->d  = randomValue();
    $dev->m  = 'meta'.$k;
    $dev->i  = randomValue();
    $dev->p  = randomValue();
    $dev->rt = randomValue();
    $dev->f  = randomValue();
    $objectData[$k] = $dev;
}

// Benchmark object reads
$start = microtime(true);
for ($i=0; $i<10000; $i++) {
    foreach ($keys as $k) {
        $val = $objectData[$k]->s; // voorbeeld toegang
        $val2 = $objectData[$k]->t;
    }
}
$end = microtime(true);
echo "Object model: ".(($end-$start)*1000)." ms\n";

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
