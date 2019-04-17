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
    sw('lichtbadkamer', 'Off');
    if ($d['auto']['s']==true) {
        if ((TIME>strtotime('5:00')-($d['auto']['m']==true?3600:0)&&TIME<strtotime('10:00')-($d['auto']['m']==true?3600:0))&&$d['Weg']['s']==1) {
            sw('hall', 'On');
        } elseif ($d['zon']['s']<20&&$d['Weg']['s']==0) {
            sw('hall', 'On');
        }
        if (TIME>strtotime('20:00')&&$d['Weg']['s']==2&&$d['kamer']['s']==5) {
            storemode('kamer', 1);
        }
        storemode('badkamer_set', 0);
        douche();
        if (past('8badkamer-8')>3) {
            $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.4:8090/now_playing'))), true);
            if (!empty($nowplaying)) {
                if (isset($nowplaying['@attributes']['source'])) {
                    if ($nowplaying['@attributes']['source']!='STANDBY') {
                        $volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.4:8090/volume"))), true);
                        $cv=$volume['actualvolume'];
                        echo 'cv='.$cv.'<br>';
                        $usleep=(6000000/$cv)/2;
                        while (1) {
                            echo $cv.'<br>';
                            bosevolume($cv, 4);
                            usleep($usleep);
                            $cv=$cv-3;
                            if ($cv<=3) {
                                break;
                            }
                        }
                        bosekey("POWER", 0, 4);
                        sw('bose4', 'Off', true);
                    }
                }
            }
        }
    }
    resetsecurity();
}
