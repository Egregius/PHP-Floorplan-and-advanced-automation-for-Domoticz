<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$start=microtime(true);
require 'functions.php';
//echo '<pre>';
$url='https://graphdata.buienradar.nl/2.0/forecast/geo/Rain3Hour?lat='.$lat.'&lon='.$lon;
//$data=curl($url);
//echo $data;
include('curl.php');
$curl = new Curl\Curl();
$curl->get($url);

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
    $iftttkey,
    $ifttttoken,
    $start,
    $users,
    $homes,
    $telegrambot,
    $smappeeclient_id,
    $smappeeclient_secret,
    $smappeeusername,
    $smappeepassword,
    $smappeeserviceLocationId,
    $LogFile,
    $cookie,
    $telegramchatid,
    $telegramchatid2,
    $smappeeip,
    $authenticated,
    $zongarage,
    $zonkeuken,
    $zoninkom,
    $zonmedia,
    $Usleep,
    $eendag,
    $Weg,
    $garmintoken,
    $googleTTSAPIKey,
    $home,
    $log,
    $offline,
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
    $denonip,
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
    $appledevice,
    $appleid,
    $applepass,
    $vurl,
    $vpsip,
    $weekend,
    $ringusername,
    $ringpassword,
    $proxmoxcredentials,
    $urlnas,
    $urlnas2,
    $urlfilms
    
    );
echo '<hr><hr><hr><pre>';print_r(GET_DEFINED_VARS());echo '</pre>';



function Human_kb($bytes,$dec=2)
{
    $size=array('kb','Mb','Gb');
    $factor=floor((strlen($bytes)-1)/3);
    return sprintf("%.{$dec}f", $bytes/pow(1000, $factor)).@$size[$factor];
}

function curl($url)
{
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FRESH_CONNECT,true);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$data=curl_exec($ch);
	curl_close($ch);
	return $data;
}


