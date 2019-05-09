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
if ($status=='Open'&&$d['auto']['s']=='On') {
    if ($d['Weg']['s']==0&&($d['zon']['s']<$zongarage||TIME<strtotime('9:00')-($d['auto']['m']==true?3600:0)||TIME>strtotime('21:00')-($d['auto']['m']==true?3600:0))&&$d['garage']['s']=='Off'&&$d['garageled']['s']=='Off') {
        sw('garageled', 'On');
    }
    if (TIME<strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']<$zonkeuken||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        sw('keuken', 'On');
    } elseif (TIME>=strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']<$zonkeuken||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        if ($d['tv']['s']=='On'||$d['jbl']['s']=='On') {
            sw('keuken', 'On');
        }
    }
    if ($d['Weg']['s']>0&&$d['Weg']['m']>TIME-178&&$d['poortrf']['s']=='Off') {
        sw('sirene', 'On');
        shell_exec('../ios.sh "Deur garage open" > /dev/null 2>/dev/null &');
        telegram('Deur garage open om '.strftime("%k:%M:%S", TIME), false, 2);
    }
    if ($d['Weg']['s']==0) {
        if ($d['Weg']['s']==0&&$d['denonpower']['s']=='OFF'&&$d['bose101']['s']=='Off'&&TIME<strtotime('21:00')-($d['auto']['m']==true?3600:0)) {
            bosekey("POWER", 0, 101);
            sw('bose101', 'On');
            bosevolume(25, 101);
            for ($x=1;$x<=10;$x++) {
                $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.101:8090/now_playing"))), true);
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
        } elseif ($d['bose101']['s']=='On'&&$d['denonpower']['s']=='OFF') {
            $volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.101:8090/volume"))), true);
            if (isset($volume['actualvolume'])) {
                $cv=$volume['actualvolume'];
                //lg('Boseliving volume = '.$cv);
                if ($cv<10) {
                    bosevolume(10, 101);
                }
            }
        }
        if ($d['bose101']['s']=='Off'&&$d['bose104']['s']=='Off') {
            bosekey("POWER", 0, 101);
            sw('bose101', 'On');
            sw('bose104', 'On');
            if ($d['denonpower']['s']=='ON') {
                bosevolume(0, 101);
            } else {
                bosevolume(25, 101);
            }
            bosepost('setZone', '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.104">C4F312DCE637</member></zone>', 101);
            if (TIME>strtotime('6:00')-($d['auto']['m']==true?3600:0)&&TIME<strtotime('22:00')-($d['auto']['m']==true?3600:0)) {
                bosevolume(35, 104);
            } else {
                bosevolume(22, 104);
            }
            for ($x=1;$x<=10;$x++) {
                $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.101:8090/now_playing"))), true);
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
        } elseif ($d['bose104']['s']=='Off') {
            sw('bose104', 'On');
            bosepost('setZone', '<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.104">C4F312DCE637</member></zone>', 101);
            if (TIME>strtotime('6:00')-($d['auto']['m']==true?3600:0)&&TIME<strtotime('22:00')-($d['auto']['m']==true?3600:0)) {
                bosevolume(35, 104);
            } else {
                bosevolume(18, 104);
            }
        }
    }
    storemode('Weg', TIME);
}