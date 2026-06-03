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

echo $d['dag']->s;
setNextubeModeB(50,0);

function setNextubeModeB(int $lcd_brightness,int $led_brightness): bool {
	lg('Set nexttube to '.$brightness,'sl');
    $url = 'http://192.168.40.93/api/settings';
	$data = [];
	$data['lcd_brightness'] = $lcd_brightness;
	$data['led_brightness'] = $led_brightness;
	$data['backlight_mode'] = ($led_brightness == 0) ? 'Off':'Static';
	$data=json_encode($data);
	echo $data;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200 && $response !== false) {
        $responseData = json_decode($response, true);
        return (isset($responseData['status']) && $responseData['status'] === 'ok');
    }
    return false;
}


//echo WiimSetEQ([125=>+10,500=>-5,2100=>55]);

function WiimSetEQ(array $bands): string {
    $freqMap = [31, 63, 125, 250, 500, 1000, 2000, 4000, 8000, 16000];
    $eqBand = [];
    foreach ($bands as $inputFreq => $value) {
        $closestFreq = null;
        $minDiff = null;
        foreach ($freqMap as $freq) {
            $diff = abs($inputFreq - $freq);
            if ($minDiff === null || $diff < $minDiff) {
                $minDiff = $diff;
                $closestFreq = $freq;
            }
        }
        $index = array_search($closestFreq, $freqMap);
        if ($value >= -50 && $value <= 50 && ($value < 0 || str_contains((string)$inputFreq, '-') || str_contains((string)$value, '+') || $value == 0)) {
            $wiimValue = round($value + 50);
        } else {
            $wiimValue = round($value);
        }
        $wiimValue = max(0, min(99, $wiimValue));
        $name = ($closestFreq >= 1000) ? ($closestFreq / 1000) . 'khz' : $closestFreq . 'hz';
        $eqBand[$index] = [
            'index' => $index,
            'param_name' => 'band' . $name,
            'value' => (int)$wiimValue
        ];
    }
    if (empty($eqBand)) {
        return 'Error: No valid bands given.';
    }
    ksort($eqBand);
    return Wiim('EQSetBand:' . json_encode(['EQBand' => array_values($eqBand)]));
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
