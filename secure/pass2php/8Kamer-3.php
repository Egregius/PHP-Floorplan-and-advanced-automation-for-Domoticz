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
if ($d['kamer']['m']==2) {
	sl('kamer', (1+$d['kamer']['s']), basename(__FILE__).':'.__LINE__);
	$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.103:8090/volume'))), true);
	bosevolume((1+$volume['actualvolume']), 103);           
} elseif ($status=='On') {
	if (TIME>strtotime('20:00')) {
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
		$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.103:8090/now_playing"))), true);
		if (!empty($status)) {
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']=='STANDBY') {
					bosekey("PRESET_5", 0, 103);
					sw('bose103', 'On', basename(__FILE__).':'.__LINE__);
					bosevolume(17, 103);
				} else {
					bosevolume(17, 103);
				}
			}
		}
	} else {
		bosezone(103);
	}
}
resetsecurity();