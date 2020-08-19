<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$start=microtime(true);
require 'functions.php';
echo strftime("%H:%M", strtotime('11:00'));


require_once '/var/www/proxmox/vendor/autoload.php';
use ProxmoxVE\Proxmox;

$proxmox = new Proxmox($proxmoxcredentials);

$allNodes = $proxmox->get('/nodes/proxmox/qemu');

print_r($allNodes);

$proxmox->create('/nodes/proxmox/qemu/112/status/start');

exit;


	  echo '
	  <style>
	  	table{border-collapse: collapse; }
	  	tr{border:1px solid grey;}
	  	th, td{border:1px solid grey;padding:5px;}
	  	.right{text-align:right;}
	  </style>
	  <table>
	  	<thead>
	  		<tr>
	  			<th>id</th>
	  			<th>Name</th>
	  			<th>Status</th>
	  			<th>Days up</th>
	  			<th>Hours</th>
	  			<th>CPU</th>
	  			<th>Memory</th>
	  			<th>diskread</th>
	  			<th>diskwrite</th>
	  			<th>netin</th>
	  			<th>netout</th>
	  		</tr>
	  	</thead>
	  	<tbody>';
	  	$data=$proxmox->getNodes()->get("proxmox")->getQemu()->Vmlist()->getResponse()->data;
	  	uasort($data, "cmp");
	  foreach ($data as $vm) {
		  echo '
		  	<tr>
		  		<td>'.$vm->vmid .'</td>
		  		<td>'.$vm->name.'</td>
		  		<td>'.$vm->status.'</td>
		  		<td class="right">'.floor($vm->uptime/86400).'d</td>
		  		<td class="right">'.gmdate("G:i", ($vm->uptime%86400)).'</td>
		  		<td class="right">'.number_format($vm->cpu, 2).'</td>
		  		<td class="right">'.human_filesize($vm->mem).'/'.human_filesize($vm->maxmem).'</td>
		  		<td class="right">'.human_filesize($vm->diskread).'</td>
		  		<td class="right">'.human_filesize($vm->diskwrite).'</td>
		  		<td class="right">'.human_filesize($vm->netin).'</td>
		  		<td class="right">'.human_filesize($vm->netout).'</td>
		  	</tr>';
	  }
	  echo '
	  	</tbody>
	  </table>';


function cmp($a, $b) {
    return strcmp($a->name, $b->name);
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
$total=microtime(true)-$start;
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
    $total,
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

function Get_data($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.17 Safari/537.36');
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
