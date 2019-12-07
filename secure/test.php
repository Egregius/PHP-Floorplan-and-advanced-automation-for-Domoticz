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

    
    
echo '<pre>';
/*-------------------------------------------------*/
$d=fetchdata();

$timefrom=TIME-86400;
$chauth = curl_init(
    'https://app1pub.smappee.net/dev/v1/oauth2/token?grant_type=password&client_id='.
    $smappeeclient_id.'&client_secret='.
    $smappeeclient_secret.'&username='.
    $smappeeusername.'&password='.
    $smappeepassword.''
);
curl_setopt($chauth, CURLOPT_AUTOREFERER, true);
curl_setopt($chauth, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($chauth, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($chauth, CURLOPT_VERBOSE, 0);
curl_setopt($chauth, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($chauth, CURLOPT_SSL_VERIFYPEER, false);
$objauth=json_decode(curl_exec($chauth));
if (!empty($objauth)) {
    $access=$objauth->{'access_token'};
    curl_close($chauth);
    $chconsumption=curl_init('');
    curl_setopt($chconsumption, CURLOPT_HEADER, 0);
    $headers=array('Authorization: Bearer '.$access);
    curl_setopt($chconsumption, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($chconsumption, CURLOPT_AUTOREFERER, true);
    curl_setopt(
        $chconsumption,
        CURLOPT_URL,
        'https://app1pub.smappee.net/dev/v1/servicelocation/'.
        $smappeeserviceLocationId.'/consumption?aggregation=3&from='.
        $timefrom.'000&to='.
        TIME.'000'
    );
    curl_setopt($chconsumption, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($chconsumption, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($chconsumption, CURLOPT_VERBOSE, 0);
    curl_setopt($chconsumption, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($chconsumption, CURLOPT_SSL_VERIFYPEER, false);
    $data=json_decode(curl_exec($chconsumption), true);
    print_r($data);
    /*if (!empty($data['consumptions'])) {
        $vv=round($data['consumptions'][0]['consumption']/1000, 1);
        if ($d['el']['m']!=$vv) {
	        storemode('el', $vv, basename(__FILE__).':'.__LINE__);
	    }
        $zonvandaag=round($data['consumptions'][0]['solar']/1000, 1);
        if ($d['zonvandaag']['s']!=$zonvandaag) {
	        store('zonvandaag', $zonvandaag, basename(__FILE__).':'.__LINE__);
	    }
        $gas=$d['gasvandaag']['s']/100;
        $water=$d['watervandaag']['s']/1000;

        @file_get_contents(
            $vurl."verbruik=$vv&gas=$gas&water=$water&zon=$zonvandaag"
        );
    }*/
    curl_close($chconsumption);
}


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
