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
//lg('               __CRON180__');
if ($d['bose103']['s']=='On'&&(TIME>=strtotime('22:00')||TIME<=strtotime('6:00')) {
    $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.103:8090/now_playing'))), true);
    if (!empty($nowplaying)) {
        if (isset($nowplaying['@attributes']['source'])) {
            if ($nowplaying['@attributes']['source']!='STANDBY') {
                $volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.103:8090/volume"))), true);
                $cv=$volume['actualvolume']-1;
                if ($cv==0) {
                    bosekey("POWER", 0, 103);
                    sw('bose103', 'Off', true);
                } else {
                    bosevolume($cv, 103);
                }
            } else {
                sw('bose103', 'Off', true);
            }
        }
    }
}