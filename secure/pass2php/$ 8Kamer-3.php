<?php
if ($status=='On') {
	$time=time();
	if ($time>strtotime('20:00')||$time<strtotime('4:00')||$d['Weg']['s']==1) {
		storemode('kamer', 1, basename(__FILE__).':'.__LINE__);
		if ($d['bose101']['s']=='On') {
			$data=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))), true);
			if (!empty($data)) {
				if (isset($data['@attributes']['source'])) {
					if ($data['@attributes']['source']!='STANDBY') {
						bosekey("POWER", 0, 101);
						sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
						sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
						sw('bose104', 'Off', basename(__FILE__).':'.__LINE__);
						sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
					}
				}
			}
		}
		$data=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.103:8090/now_playing"))), true);
		if (!empty($data)) {
			if (isset($data['@attributes']['source'])) {
				if ($data['@attributes']['source']=='STANDBY') {
					$week=strftime('%v', $time);
					if ((int)$week % 2 == 0) $preset='PRESET_2';
					else $preset='PRESET_1';
					sw('bose103', 'On', basename(__FILE__).':'.__LINE__);
					bosekey($preset, 10000, 103);
					bosevolume(17, 103);
				} else {
					bosevolume(17, 103);
				}
			}
		}
	} else {
		bosezone(103, true);
	}
}
