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
    if ($d['Weg']['s']==0&&$d['denonpower']['s']=='Off'&&$d['bureel']['s']=='Off'&&$d['eettafel']['s']==0) {
        if ($d['zon']['s']==0) {
            if ($d['keuken']['s']=='Off') {
                sw('keuken', 'On');
            }
            if ($d['bureel']['s']=='Off') {
                sw('bureel', 'On');
            }
            if ($d['jbl']['s']=='Off') {
                sw('jbl', 'On');
            }
        }
        $d['pirliving']['t']=TIME;
        include '_rolluiken.php';
    }
    if ($d['Weg']['s']>0&&$d['Weg']['m']>TIME-178) {
        sw('sirene', 'On');
        shell_exec('../ios.sh "Beweging Living" > /dev/null 2>/dev/null &');
        telegram('Beweging living om '.strftime("%k:%M:%S", TIME), false, 2);
    }
    if ($d['Weg']['s']==0&&$d['denon']['s']=='Off'&&$d['bose3']['s']=='Off'&&TIME<strtotime('21:00')-($d['auto']['m']==true?3600:0)) {
        bosekey("POWER", 0, 3);
        sw('bose3', 'On');
        bosevolume(25, 3);
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
    } elseif ($d['bose3']['s']=='On'&&$d['denon']['s']=='Off') {
        $volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.3:8090/volume"))), true);
        if (isset($volume['actualvolume'])) {
            $cv=$volume['actualvolume'];
            //lg('Boseliving volume = '.$cv);
            if ($cv<10) {
                bosevolume(10, 3);
            }
        }
    }
    storemode('Weg', TIME);
}