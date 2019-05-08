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
if ($status=='On') {
    sl('lichtbadkamer', 26);
    douche();
    resetsecurity();
    if ($bose3=='Off'&&$bose4=='Off') {
        bosekey("POWER", 0, 3);
        sw('bose3', 'On');
        sw('bose4', 'On');
        if ($denon=='On'||$Weg>0) {
            bosevolume(0, 3);
        } else {
            bosevolume(25, 3);
        }
        $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.3"><member ipaddress="192.168.2.4">C4F312F65070</member></zone>';
        bosepost('setZone', $xml, 3);
        if (TIME>strtotime('6:00')&&TIME<strtotime('21:00')) {
            bosevolume(35, 4);
        } else {
            bosevolume(18, 4);
        }
        for ($x=1;$x<=10;$x++) {
            $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.3:8090/now_playing"))), true);
            if (!empty($nowplaying)) {
                if (isset($nowplaying['@attributes']['source'])) {
                    if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
                        if (trim($nowplaying['artist'])=='Paul Kalkbrenner'&&trim($nowplaying['track'])=='Page Two') {
                               bosekey("NEXT_TRACK", 0, 3);
                               break;
                        }
                    }
                }
            }
            sleep(1);
        }
    } elseif ($bose4=='Off') {
        sw('bose4', 'On');
        $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.3"><member ipaddress="192.168.2.4">C4F312F65070</member></zone>';
        bosepost('setZone', $xml, 3);
        if (TIME>strtotime('6:00')&&TIME<strtotime('21:00')) {
            bosevolume(35, 4);
        } else {
            bosevolume(18, 4);
        }
    }
}