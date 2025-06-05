<?php
if ($status==0) {
	if ($d['time']<strtotime('20:00')) {
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
