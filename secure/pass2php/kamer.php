<?php
/**
 * Pass2PHP
 * php version 7.3.9-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status==0) {
	if (TIME<strtotime('20:00')) {
		if ($d['bose103']['s']=='On') {
			$status=json_decode(
				json_encode(
					simplexml_load_string(
						@file_get_contents(
							"http://192.168.2.103:8090/now_playing"
						)
					)
				),
				true
			);
			if (!empty($status)) {
				if (isset($status['@attributes']['source'])) {
					if ($status['@attributes']['source']!='STANDBY') {
						sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
						bosekey("POWER", 0, 103);
					}
				}
			}
		}
	}
}
if ($d['kamer']['m']>0&&TIME<strtotime('8:00')) {
	storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
}