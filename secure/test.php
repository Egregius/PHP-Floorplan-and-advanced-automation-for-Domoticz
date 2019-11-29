<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$start=microtime(true);
require 'functions.php';
require 'gcal/google-api-php-client/vendor/autoload.php';




    $articleText = 'Het is 1 uur. Het wordt tussen 5 en 9 graden met verspreide wolken.';
    $client = new GuzzleHttp\Client();
    $client->setAuthConfig('/var/www/homeegregius-71b574e2c5f1-tts.json');
    $requestData = [
        'input' =>[
            'text' => $articleText
        ],
        'voice' => [
            'languageCode' => 'en-US',
            'name' => 'en-US-Wavenet-F'
        ],
        'audioConfig' => [
            'audioEncoding' => 'MP3',
            'pitch' => 0.00,
            'speakingRate' => 1.00
        ]
    ];
    try {
        $response=$client->request('POST', 'https://texttospeech.googleapis.com/v1beta1/text:synthesize?key='.$googleTTSAPIKey, ['json'=>$requestData]);
    } catch (Exception $e) {
    	echo $e->getMessage();
    	print_r($e);
    	exit;
//        die('Something went wrong: ' . $e->getMessage());
    }
    $fileData = json_decode($response->getBody()->getContents(), true);
    file_put_contents('/var/www/html/sounds/tts.mp3', base64_decode($fileData['audioContent']));
    
    
    
echo '<pre>';
$d=fetchdata();
/*-------------------------------------------------*/
$data=file_get_contents('https://texttospeech.googleapis.com/v1beta1/text:synthesize');
print_r($data);
/*---------------------------*/
echo '</pre>';
$total=microtime(true)-$start;
echo '<hr>Time:'.number_format(((microtime(true)-$start)*1000), 6);
unset(
    $_COOKIE,
    $_GET,
    $_POST,
    $_FILES,
    $_SERVER,
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
