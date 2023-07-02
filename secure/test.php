<?php
header('Access-Control-Allow-Origin: *');
echo '<pre>';
$start=microtime(true);
require 'functions.php';
require '/var/www/authentication.php';
$d=fetchdata();

$domoticz=json_decode(
	file_get_contents(
		$domoticzurl.'/json.htm?type=devices&used=true'
	),
	true
);
if ($domoticz) {
	foreach ($domoticz['result'] as $dom) {
		$update=false;
		$name=$dom['Name'];
		if (isset($dom['SwitchType'])) $switchtype=$dom['SwitchType'];
		elseif (isset($dom['SubType'])) $switchtype=$dom['SubType'];
		if($switchtype=='On/Off') $update=true;
		elseif($switchtype=='Switch') $update=true;
		elseif($switchtype=='Contact') $update=true;
		elseif($switchtype=='Door Contact') $update=true;
		elseif($switchtype=='Motion Sensor') $update=true;
		elseif($switchtype=='Push On Button') $update=true;
		elseif($switchtype=='X10 Siren') $update=true;
		elseif($switchtype=='Smoke Detector') $update=true;
		elseif($switchtype=='Selector') $update=true;
		elseif($switchtype=='Blinds Inverted') $update=true;
		if ($dom['Type']=='Temp') {
			$status=$dom['Temp'];
			 $update=false;
		} elseif ($dom['Type']=='Temp + Humidity') {
			$status=$dom['Temp'];
			 $update=false;
		} elseif ($dom['TypeImg']=='current') {
			$status=str_replace(' Watt', '', $dom['Data']);
			 $update=false;
		} elseif ($name=='luifel') {
			$status=str_replace('%', '', $dom['Level']);
			 $update=true;
		} elseif ($switchtype=='Dimmer') {
			if ($dom['Data']=='Off') $status=0;
			elseif ($dom['Data']=='On') $status=100;
			else $status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
			 $update=true;
		} elseif ($switchtype=='Blinds Percentage') {
			if ($dom['Data']=='Open') $status=0;
			elseif ($dom['Data']=='Closed') $status=100;
			else $status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
			$update=true;
		} elseif ($name=='achterdeur') {
			if ($dom['Data']=='Open') $status='Closed';
			else $status='Open';
		} else $status=$dom['Data'];
		if ($update==true) {
			if ($status!=$d[$name]['s']) {
				echo $name.'	= '.$status.'<br>';
				$query="UPDATE devices SET s=:status WHERE n=:name;";
				$stmt=$db->prepare($query);
				$stmt->execute(array(':status'=>$status, ':name'=>$name));
			}
		}
	}
}




/*
$response=file_get_contents(
	'http://192.168.2.19:8123/api/states',
	false,
	stream_context_create(
		array(
			'http'=>array(
				'header'=>array(
					'Content-Type: application/json',
					'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJjZDM1MDc5MzJmMDY0MWZmODRlMzhlNTExNmM1NDFlMSIsImlhdCI6MTY4MTk3NjMwNywiZXhwIjoxOTk3MzM2MzA3fQ.Dthf5CqY06vfsnCruEclAKfds6h11EjyPsXNwZgT_vU'
				),
				'method'=>'GET',
				
				
			)
		)
	)
);
*/

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
