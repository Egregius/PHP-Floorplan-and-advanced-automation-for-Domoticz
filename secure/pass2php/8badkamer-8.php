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
            if ($d['Weg']['s']==0&&$d['denonpower']['s']=='OFF'&&$d['bureel']['s']=='Off'&&$d['eettafel']['s']==0) {
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
                                    bosekey("NEXT_TRACK", 0, 101);
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
        } elseif ($d['zon']['s']<20&&$d['Weg']['s']==0) {
            sw('hall', 'On');
        }
        if (TIME>strtotime('20:00')&&$d['Weg']['s']==2&&$d['kamer']['s']>0) {
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
                        sw('bose102', 'Off', true);
                    }
                }
            }
        }
    }
    resetsecurity();
}
