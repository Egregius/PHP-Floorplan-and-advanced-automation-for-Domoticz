<?php
header('Access-Control-Allow-Origin: *');
echo '<pre>';
$start=microtime(true);
$user='test';
require 'functions.php';
//require '/var/www/authentication.php';
//$d=fetchdata();
//$startloop=microtime(true);
//$d['time']=$startloop;
//$db = Database::getInstance();

setNextubeMode('night');


function setNextubeMode(string $mode): bool {
    $url = 'http://192.168.40.93/';
    
    $data = [
        "apps" => [["name" => "app1", "app" => "Clock", "theme" => "RedDigits", "type" => "24H_NS", "clock_tube5" => "weather"]],
        "lcd_brightness" => ($mode === 'day') ? 100 : 55,
        "led_brightness" => 59,
        "backlight_mode" => ($mode === 'day') ? "Off" : "Static",
        "backlight_RGB" => array_fill(0, 6, [128, 0, 0]),
        "enabled_modes" => 1,
        "rotation_enabled" => false,
        "rotation_interval_s" => 60,
        "ssid" => "Egregius_IOT",
        "password" => "DitishetdraadloosnetwerkvooronzeIOTspullen9",
        "hostname" => "nextube-remaster",
        "time_zone" => 2,
        "ntp_server" => "192.168.2.254",
        "weather_source" => "openmeteo",
        "weather_api_key" => "",
        "City" => "Roeselare, Belgium",
        "temperature_formate" => "Celsius",
        "video_site" => "youtube",
        "youtube_id" => "",
        "youtube_key" => "",
        "bili_uid" => "1",
        "volume" => 20,
        "music_file" => "",
        "bell_file" => "/spiffs/audio/bell.wav",
        "tone_file" => "/spiffs/audio/tremolo3.wav",
        "timer_file" => "/spiffs/audio/timer.wav",
        "click_file" => "/spiffs/audio/click.wav",
        "button_sound" => true,
        "default_countdown_time" => 1,
        "pomodoro_work" => 25,
        "pomodoro_break" => 5,
        "weather_panel_ms" => 5000,
        "weather_panel0_en" => true,
        "weather_panel1_en" => false
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode === 200);
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
