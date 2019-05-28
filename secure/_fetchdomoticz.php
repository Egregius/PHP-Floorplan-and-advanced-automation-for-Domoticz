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
$domoticz=json_decode(
    file_get_contents(
        $domoticzurl.'/json.htm?type=devices&used=true'
    ),
    true
);
if ($domoticz) {
    foreach ($domoticz['result'] as $dom) {
        $name=$dom['Name'];
        $idx=$dom['idx'];
        if (isset($dom['SwitchType'])) {
            $switchtype=$dom['SwitchType'];
        } else {
            $switchtype='none';
        }
        if($switchtype='On/Off')$type='switch';
        $type=$switchtype;
        if ($dom['Type']=='Temp') {
            $status=$dom['Temp'];
            $type='thermometer';
        } elseif ($dom['Type']=='Temp + Humidity') {
            $status=$dom['Temp'];
            $type='thermometer';
        } elseif ($dom['TypeImg']=='current') {
            $status=str_replace(' Watt', '', $dom['Data']);
        } elseif ($name=='luifel') {
            $status=str_replace('%', '', $dom['Level']);
            $type='luifel';
        } elseif ($switchtype=='Dimmer') {
            if ($dom['Data']=='Off') {
                $status=0;
            } elseif ($dom['Data']=='On') {
                $status=100;
            } else {
                $status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
            }
            $type='dimmer';
        } elseif ($switchtype=='Blinds Percentage') {
            if ($dom['Data']=='Open') {
                $status=0;
            } elseif ($dom['Data']=='Closed') {
                $status=100;
            } else {
                $status=filter_var($dom['Data'], FILTER_SANITIZE_NUMBER_INT);
            }
            $type='rollers';
        } elseif ($name=='achterdeur') {
            if ($dom['Data']=='Open') {
                $status='Closed';
            } else {
                $status='Open';
            }
        } else {
            $status=$dom['Data'];
        }
        $time=TIME;
        $db->query("INSERT INTO devices (n,i,s,t,dt) VALUES ('$name','$idx','$status','$time','$type') ON DUPLICATE KEY UPDATE s='$status',i='$idx',t='$time',dt='$type';");
        echo $idx.' '.$name.' = '.$status.'<br>';
    }
}