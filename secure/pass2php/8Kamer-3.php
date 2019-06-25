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
    storemode('kamer', 1, basename(__FILE__).':'.__LINE__);
    if ($d['bose101']['s']=='On') {
    	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))), true);
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']!='STANDBY') {
                    bosekey("POWER", 0, 101);
                    sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
                    sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
                    sw('bose104', 'Off', basename(__FILE__).':'.__LINE__);
                    sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
                }
            }
        }
    }
    if ($d['bose103']['s']=='Off') {
    	$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.103:8090/now_playing"))), true);
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']=='STANDBY') {
                    bosekey("POWER", 0, 103);
                    sw('bose103', 'On', basename(__FILE__).':'.__LINE__);
                }
            }
        }
        bosevolume(21, 103);
    	for ($x=1;$x<=10;$x++) {
			$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.103:8090/now_playing"))), true);
			if (!empty($nowplaying)) {
				if (isset($nowplaying['@attributes']['source'])) {
					if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
						if (trim($nowplaying['artist'])=='Paul Kalkbrenner'&&trim($nowplaying['track'])=='Page Two') {
							bosekey("NEXT_TRACK", 0, 103);
							break;
						}
					}
				}
			}
			sleep(1);
		}
    } elseif ($d['bose103']['s']=='On') {
    	bosevolume(21, 103);
    }
    resetsecurity();
}