<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
lg('               __CRON300__');
if ($d['auto']['s']=='On'&&past('Weg')>300) {
    $items=array(
        'living_temp',
        'kamer_temp',
        'tobi_temp',
        'alex_temp',
        'zolder_temp'
    );
    $avg=0;
    foreach ($items as $item) {
        $avg=$avg+$d[$item]['s'];
    }
    $avg=$avg/6;
    foreach ($items as $item) {
        if ($d[$item]['s']>$avg+5&&$d[$item]['s']>25) {
            $msg='T '.$item.'='.$d[$item]['s'].'°C. AVG='.round($avg, 1).'°C';
                alert(
                    $item,
                    $msg,
                    3600,
                    false,
                    true
                );
        }
        if (past($item)>43150) {
            alert(
                $item,
                $item.' not updated since '.
                strftime("%k:%M:%S", $d[$item]['t']),
                7200
            );
        }
    }
    $devices=array('tobiZ','alexZ',/*'livingZ','livingZZ',*/'kamerZ');
    foreach ($devices as $device) {
        if (past($device)>43150) {
            alert(
                $device,
                $device.' geen communicatie sinds '.
                strftime("%k:%M:%S", $d[$device]['t']),
                14400
            );
        }
    }
}
if (past('diepvries_temp')>7200) {
    alert(
        'diepvriestemp',
        'Diepvries temp not updated since '.
        strftime("%k:%M:%S", $d['diepvries_temp']['t']),
        7200
    );
}
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
    if (!empty($data['consumptions'])) {
        $vv=$data['consumptions'][0]['consumption']/1000;
        storemode('elec', round($vv, 1));
        $zonvandaag=$data['consumptions'][0]['solar']/1000;
        store('zonvandaag', round($zonvandaag, 1));
        $gas=$d['gasvandaag']['s']/100;
        $water=$d['watervandaag']['s']/1000;

        @file_get_contents(
            $vurl."verbruik=$vv&gas=$gas&water=$water&zon=$zonvandaag"
        );
    }
    curl_close($chconsumption);
}