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
if ($status=='On'&&$d['auto']['s']) {
    if ($d['Weg']['s']==0&&($d['zon']['s']<$zongarage||TIME<strtotime('9:00')-($d['auto']['m']==true?3600:0)||TIME>strtotime('21:00')-($d['auto']['m']==true?3600:0))&&$d['garageled']['s']=='Off') {
        sw('garageled', 'On');
    }
    if ($d['Weg']['s']>0&&$d['Weg']['m']>TIME-178&&$d['poortrf']['s']=='Off') {
        sw('sirene', 'On');
        shell_exec('../ios.sh "Beweging Garage" > /dev/null 2>/dev/null &');
        telegram('Beweging garage om '.strftime("%k:%M:%S", TIME), false, 2);
    }
    if ($d['Weg']['s']==0) {
        if ($d['bose3']['s']=='Off'&&$d['bose5']['s']=='Off') {
            bosekey("POWER", 0, 3);
            sw('bose3', 'On');
            sw('bose5', 'On');
            if ($d['denon']['s']=='On') {
                bosevolume(0, 3);
            } else {
                bosevolume(25, 3);
            }
            bosepost('setZone', '<zone master="587A6260C5B2" senderIPAddress="192.168.2.3"><member ipaddress="192.168.2.5">C4F312DCE637</member></zone>', 3);
            if (TIME>strtotime('6:00')-($d['auto']['m']==true?3600:0)&&TIME<strtotime('22:00')-($d['auto']['m']==true?3600:0)) {
                bosevolume(35, 5);
            } else {
                bosevolume(22, 5);
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
        } elseif ($d['bose5']['s']=='Off') {
            sw('bose5', 'On');
            bosepost('setZone', '<zone master="587A6260C5B2" senderIPAddress="192.168.2.3"><member ipaddress="192.168.2.5">C4F312DCE637</member></zone>', 3);
            if (TIME>strtotime('6:00')-($d['auto']['m']==true?3600:0)&&TIME<strtotime('22:00')-($d['auto']['m']==true?3600:0)) {
                bosevolume(35, 5);
            } else {
                bosevolume(18, 5);
            }
        }
    }
    storemode('Weg', TIME);
}
lgsql('pir',$device,$status);