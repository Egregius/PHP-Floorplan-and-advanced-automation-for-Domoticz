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


// === CONFIG ===
$iterations = 50000;
$keyBase = 'bench_';
$value = microtime(true);


// === IMPLEMENTATIE LOCAL CACHE ===
static $localCache = [];


// ================================
// === TEST setCache (FILE) ======
// ================================
$start1 = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    setCache($keyBase.$i, $value);
}

$end1 = microtime(true);
$timeSetFile = $end1 - $start1;


// ================================
// === TEST getCache (FILE) ======
// ================================
$start2 = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    getCache($keyBase.$i);
}

$end2 = microtime(true);
$timeGetFile = $end2 - $start2;


// ================================
// === TEST setCacheFast (LOCAL) ==
// ================================
$start3 = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    setCacheFast($keyBase.$i, $value);
}

$end3 = microtime(true);
$timeSetFast = $end3 - $start3;


// ================================
// === TEST getCacheFast (LOCAL) ==
// ================================
$start4 = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    getCacheFast($keyBase.$i);
}

$end4 = microtime(true);
$timeGetFast = $end4 - $start4;


// ================================
// === TEST apcu_store ============
// ================================
$start5 = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    apcu_store($keyBase.$i, $value);
}

$end5 = microtime(true);
$timeSetApcu = $end5 - $start5;


// ================================
// === TEST apcu_fetch ============
// ================================
$start6 = microtime(true);

for ($i = 0; $i < $iterations; $i++) {
    apcu_fetch($keyBase.$i);
}

$end6 = microtime(true);
$timeGetApcu = $end6 - $start6;


// ================================
// === RESULT ======================
// ================================

echo "Iterations: $iterations\n\n";

function fmt($label, $time, $iterations) {
    echo str_pad($label, 18) . ": "
        . number_format($time, 6) . " sec total ("
        . number_format($time / $iterations * 1e6, 2) . " µs/call)\n";
}


// WRITE
fmt("setCache (file)",   $timeSetFile, $iterations);
fmt("setFast (local)",   $timeSetFast, $iterations);
fmt("apcu_store",        $timeSetApcu, $iterations);

echo "\n";

// READ
fmt("getCache (file)",   $timeGetFile, $iterations);
fmt("getFast (local)",   $timeGetFast, $iterations);
fmt("apcu_fetch",        $timeGetApcu, $iterations);


// RATIOS
echo "\n--- RATIOS ---\n\n";

function ratio($label, $a, $b) {
    if ($b > 0) {
        echo str_pad($label, 30) . ": " . number_format($a / $b, 2) . "x\n";
    }
}

ratio("getCache / getFast",   $timeGetFile, $timeGetFast);
ratio("getCache / apcu",      $timeGetFile, $timeGetApcu);
ratio("getFast / apcu",       $timeGetFast, $timeGetApcu);

ratio("setCache / setFast",   $timeSetFile, $timeSetFast);
ratio("setCache / apcu",      $timeSetFile, $timeSetApcu);
ratio("setFast / apcu",       $timeSetFast, $timeSetApcu);



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