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
if ($status=="Open") {
    if ($d['dampkap']['s']=='On') {
        sw('dampkap', 'Off');
    }
    if ($d['bose105']['m']=='Online') {
        $status=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        "http://192.168.2.105:8090/now_playing"
                    )
                )
            ),
            true
        );
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']=='STANDBY') {
                    bosezone(105);
                    sw('bose105', 'On');
                }
            }
        }
    }
 } else {
    if ($d['Weg']['s']>0&&$d['auto']['s']==1&&$d['Weg']['m']>TIME-178) {
            sw('sirene', 'On');
            telegram('Achterdeur open om '.strftime("%k:%M:%S", TIME), false, 3);
    }
    if ($d['bose105']['m']=='Online') {
        $status=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        "http://192.168.2.105:8090/now_playing"
                    )
                )
            ),
            true
        );
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']!='STANDBY') {
                    bosekey("POWER", 0, 105);
                    sw('bose105', 'Off');
                }
            }
        }
    }
}