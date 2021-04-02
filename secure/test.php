<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
$start=microtime(true);
require 'functions.php';
echo '<pre>';


print_r(getEachIpInRange ( '172.16.0.0/25'));

function getIpRange(  $cidr) {

    list($ip, $mask) = explode('/', $cidr);

    $maskBinStr =str_repeat("1", $mask ) . str_repeat("0", 32-$mask );      //net mask binary string
    $inverseMaskBinStr = str_repeat("0", $mask ) . str_repeat("1",  32-$mask ); //inverse mask

    $ipLong = ip2long( $ip );
    $ipMaskLong = bindec( $maskBinStr );
    $inverseIpMaskLong = bindec( $inverseMaskBinStr );
    $netWork = $ipLong & $ipMaskLong;

    $start = $netWork+1;//ignore network ID(eg: 192.168.1.0)

    $end = ($netWork | $inverseIpMaskLong) -1 ; //ignore brocast IP(eg: 192.168.1.255)
    return array('firstIP' => $start, 'lastIP' => $end );
}

function getEachIpInRange ( $cidr) {
    $ips = array();
    $range = getIpRange($cidr);
    for ($ip = $range['firstIP']; $ip <= $range['lastIP']; $ip++) {
        $ips[] = long2ip($ip);
    }
    return $ips;
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
	$users,
	$homes,
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
	$authenticated,
	$eendag,
	$googleTTSAPIKey,
	$home,
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
	$appleid,
	$applepass,
	$vurl,
	$weekend,
	$proxmoxcredentials
	);
echo '<hr><hr><hr><pre>';print_r(GET_DEFINED_VARS());echo '</pre>';



function Human_kb($bytes,$dec=2)
{
	$size=array('kb','Mb','Gb');
	$factor=floor((strlen($bytes)-1)/3);
	return sprintf("%.{$dec}f", $bytes/pow(1000, $factor)).@$size[$factor];
}
