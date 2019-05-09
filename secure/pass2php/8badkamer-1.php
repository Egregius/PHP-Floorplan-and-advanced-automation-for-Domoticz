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
    sl('lichtbadkamer', 20);
    douche();
    resetsecurity();
    if ($d['bose101']['s']=='Off'&&$d['bose102']['s']=='Off') {
        bosekey("POWER", 0, 101);
        sw('bose101', 'On');
        sw('bose102', 'On');
        if ($d['denonpower']['s']=='ON'||$Weg>0) {
            bosevolume(0, 101);
        } else {
            bosevolume(25, 101);
        }
        $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.102">C4F312F65070</member></zone>';
        bosepost('setZone', $xml, 101);
        if (TIME>strtotime('6:00')&&TIME<strtotime('21:00')) {
            bosevolume(35, 102);
        } else {
            bosevolume(18, 102);
        }
        for ($x=1;$x<=10;$x++) {
            $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.101:8090/now_playing"))), true);
            if (!empty($nowplaying)) {
                if (isset($nowplaying['@attributes']['source'])) {
                    if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
                        if (trim($nowplaying['artist'])=='Paul Kalkbrenner'&&trim($nowplaying['track'])=='Page Two') {
                               bosekey("NEXT_TRACK", 0, 101);
                               break;
                        }
                    }
                }
            }
            sleep(1);
        }
    } elseif ($bose102=='Off') {
        sw('bose102', 'On');
        $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.102">C4F312F65070</member></zone>';
        bosepost('setZone', $xml, 101);
        if (TIME>strtotime('6:00')&&TIME<strtotime('21:00')) {
            bosevolume(35, 102);
        } else {
            bosevolume(18, 102);
        }
    }
}