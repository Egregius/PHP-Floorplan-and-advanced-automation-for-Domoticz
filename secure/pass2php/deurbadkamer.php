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
$deur=false;
if ($status=='Open'&&$d['auto']['s']) {
    if (past('8badkamer8')>10) {
        $items=array('bose3','bose4','denon','lichtbadkamer','zon');
        foreach ($items as $i) {
            ${$i}=$d[$i]['s'];
        }

        if (TIME>strtotime('5:00')&&TIME<strtotime('12:00')&&$lichtbadkamer<25&&$d['zon']['s']<200) {
            sl('lichtbadkamer', 25);
        } elseif ($lichtbadkamer<18&&$d['zon']['s']<200) {
            sl('lichtbadkamer', 18);
        }
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
    storemode('Weg', TIME);
} else {
    if (past('8badkamer8')>10) {
        if (past('heating')>28800) {
            if (in_array(date('n'), array(4,5,9))) {
                if ($d['buiten_temp']['s']<12) {
                    sw('heating', 3);
                    $d['heating']['s']=3;
                } else {
                    sw('heating', 2);
                    $d['heating']['s']=2;
                }
                store('heatingauto', 'Off');
                $d['heatingauto']['s']='Off';
            } elseif (in_array(date('n'), array(11,12,1,2,3))) {
                if ($d['buiten_temp']['s']<12) {
                    sw('heating', 3);
                    $d['heating']['s']=3;
                } else {
                    sw('heating', 2);
                    $d['heating']['s']=2;
                }
                store('heatingauto', 'On');
                $d['heatingauto']['s']='On';
            } elseif (in_array(date('n'), array(6,7,8))) {
                store('heating', 1);
                $d['heating']['s']=1;
                store('heatingauto', 'Off');
                $d['heatingauto']['s']='Off';
            }
        }
        $d['lichtbadkamer']['s']=25;
        $d['deurbadkamer']['s']='Closed';
        $d['8badkamer7']['t']=0;
        $d['badkamervuur1']['t']=0;
        $d['badkamervuur2']['t']=0;
        require '_verwarming.php';
    }
}