<?php
header('Access-Control-Allow-Origin: *');
echo '<pre>';
$start=microtime(true);
require 'functions.php';
require '/var/www/authentication.php';
//$d=fetchdata();



echo time().'<br>';
echo microtime(true).'<br>';
$message="
	18 @ =E2=82=AC 5.00 =3D =E2=82=AC 90.00<br>
	17 @ =E2=82=AC 10.00 =3D =E2=82=AC 170.00<br>
	14 @ =E2=82=AC 20.00 =3D =E2=82=AC 280.00<br>
	5 @ =E2=82=AC 50.00 =3D =E2=82=AC 250.00<br>
	Total: 54 Notes =3D =E2=82=AC 790.00<br>";
$reps=array(
	' @ '=>' x ',
	'=E2=82=AC'=>'	€',
	' =3D '=>'	= ',
	'.'=>',',
	'0.00'=>'0',
	'5.00'=>'5',
);
//$message=str_replace(' @ ',' x ',$message);
//$message=str_replace('=E2=82=AC','€',$message);
//$message=str_replace(' =3D ',' = ',$message);
//$message=str_replace('.00',',00',$message);
$message=strtr($message,$reps);
echo $message;

//$status='Off';
//include 'pass2php/$ remoteauto.php';





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
