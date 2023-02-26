<?php
header('Access-Control-Allow-Origin: *');
$start=microtime(true);
require 'functions.php';
require '/var/www/authentication.php';
$d=fetchdata();
/*
$sl=array('kamer','lichtbadkamer','kamer','waskamer','alex','hall','inkom','zithoek','eettafel','wasbak','snijplank','terras');
$sw=array('wc','tuin','garage','garageled','zolderg','kristal','lamp kast','bureel');
$cmd=0;
if ($cmd==1) {
	sl($sl, 20);
	sw($sw, 'On');
} else {
	sl($sl, 0);
	sw($sw, 'Off');
}
*/

echo '<pre>';
$dow=date("w");

if($dow==0||$dow==6) $t=strtotime('7:30');
elseif($dow==2||$dow==5) $t=strtotime('6:45');
else $t=strtotime('7:00');
	
$base=19;
			$loop=true;
			for ($x=0;$x<=3;$x+=0.1) {
				if ($loop==true) {
					$t2=$t-(6000*$x);
					if (TIME>=$t2&&TIME<strtotime('19:00')) {
						$Setliving=$base-$x;
						$loop=false;
					}
				} else break;
				echo strftime("%F %T", $t2).'	'.(isset($Setliving)?$Setliving:'').'<br>';
			}
	


/*NL('Rook gedetecteerd in badkamer!');
NL('Rook gedetecteerd in kamer!');
NL('Rook gedetecteerd in living!');
NL('Rook gedetecteerd in waskamer!');
NL('Rook gedetecteerd op zolder!');
NL('Rook gedetecteerd bij Alex!');*/

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
	$smappeeclient_id,
	$smappeeclient_secret,
	$smappeeusername,
	$smappeepassword,
	$smappeeserviceLocationId,
	$cookie,
	$telegramchatid,
	$telegramchatid2,
	$smappeeip,
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
	$lgtvip,
	$lgtvmac,
	$nasip,
	$shieldip,
	$kodiurl,
	$dsapikey,
	$owappid,
	$openuv,
	$owid,
	$lat,
	$lon,
	$calendarApp,
	$calendarId,
	$calendarIdMirom,
	$calendarIdTobi,
	$calendarIdVerlof,
	$telegramchatid1,
	$appleid,
	$applepass,
	$vurl,
	$weekend
	);
echo '<hr><hr><hr><pre>';print_r(GET_DEFINED_VARS());echo '</pre>';



function Human_kb($bytes,$dec=2)
{
	$size=array('kb','Mb','Gb');
	$factor=floor((strlen($bytes)-1)/3);
	return sprintf("%.{$dec}f", $bytes/pow(1000, $factor)).@$size[$factor];
}
