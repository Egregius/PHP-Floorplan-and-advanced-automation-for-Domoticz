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
if ($status=='On') {
    sw('lichtbadkamer', 'Off');
    if ($d['auto']['s']=='On') {
        fhall();
        fliving();
        if (TIME>strtotime('20:00')&&$d['Weg']['s']==1&&$d['kamer']['s']>0) {
            telegram('Kamer op slapen gezet');
            storemode('kamer', 1);
        }
        storemode('badkamer_set', 0);
        $d['badkamer_set']['s']=10;
        $d['badkamer_set']['m']=0;
        $d['badkamervuur1']['t']=0;
        $d['badkamervuur2']['t']=0;
        include '_verwarming.php';
        douche();
        if (past('8badkamer-8')>3) {
            $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.102:8090/now_playing'))), true);
            if (!empty($nowplaying)) {
                if (isset($nowplaying['@attributes']['source'])) {
                    if ($nowplaying['@attributes']['source']!='STANDBY') {
                        $volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.102:8090/volume"))), true);
                        $cv=$volume['actualvolume'];
                        echo 'cv='.$cv.'<br>';
                        $usleep=(6000000/$cv)/2;
                        while (1) {
                            echo $cv.'<br>';
                            bosevolume($cv, 102);
                            usleep($usleep);
                            $cv=$cv-3;
                            if ($cv<=3) {
                                break;
                            }
                        }
                        bosekey("POWER", 0, 102);
                        sw('bose102', 'Off');
                    }
                }
            }
            if ($d['Weg']['s']==1) {
                $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
                if (!empty($nowplaying)) {
                    if (isset($nowplaying['@attributes']['source'])) {
                        if ($nowplaying['@attributes']['source']!='STANDBY') {
                            bosekey("POWER", 0, 101);
                            sw('bose101', 'Off');
                        }
                    }
                }
            }
        }
    }
}
resetsecurity();
