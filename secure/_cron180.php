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
$user='cron180';
if ($d['bose103']['s']=='On'&&$d['Weg']['s']==1) {
    $nowplaying=json_decode(
        json_encode(
            simplexml_load_string(
                file_get_contents('http://192.168.2.103:8090/now_playing')
            )
        ),
        true
    );
    if (!empty($nowplaying)) {
        if (isset($nowplaying['@attributes']['source'])) {
            if ($nowplaying['@attributes']['source']!='STANDBY') {
                $volume=json_decode(
                    json_encode(
                        simplexml_load_string(
                            file_get_contents("http://192.168.2.103:8090/volume")
                        )
                    ),
                    true
                );
                $cv=$volume['actualvolume']-1;
                if ($cv<=5) {
                    bosekey("POWER", 0, 103);
                    sw('bose103', 'Off');
                } else {
                    bosevolume($cv, 103);
                }
            } else {
                sw('bose103', 'Off');
            }
        }
    }
}