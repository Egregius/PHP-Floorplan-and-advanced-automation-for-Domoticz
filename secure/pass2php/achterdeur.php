<?php
if ($status=="Open") {
	// BOSE BUITEN
/*	if ($d['weg']['s']==0&&$d['bose106']['icon']=='Online') {
		$status=json_decode(
			json_encode(
				simplexml_load_string(
					@file_get_contents(
						"http://192.168.2.106:8090/now_playing"
					)
				)
			),
			true
		);
		if (!empty($status)) {
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']=='STANDBY') {
					bosezone(106);
					sw('bose106', 'On', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	if ($d['weg']['s']==0&&$d['bose107']['icon']=='Online') {
		$status=json_decode(
			json_encode(
				simplexml_load_string(
					@file_get_contents(
						"http://192.168.2.107:8090/now_playing"
					)
				)
			),
			true
		);
		if (!empty($status)) {
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']=='STANDBY') {
					bosezone(107);
					sw('bose107', 'On', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}*/
	// END BOSE BUITEN
	if ($d['weg']['s']>0) sirene('Achterdeur open');
 } else {
	if ($d['weg']['s']>0&&$d['auto']['s']==1&&past('weg')>178) {
			sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
			telegram('Achterdeur dicht om '.date('G:i:s'), false, 3);
	}
/*	// BOSE BUITEN
	if ($d['weg']['s']==0&&$d['bose106']['icon']=='Online') {
		$status=json_decode(
			json_encode(
				simplexml_load_string(
					@file_get_contents(
						"http://192.168.2.106:8090/now_playing"
					)
				)
			),
			true
		);
		if (!empty($status)) {
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']!='STANDBY') {
					bosekey("POWER", 0, 106);
					sw('bose106', 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	if ($d['weg']['s']==0&&$d['bose107']['icon']=='Online') {
		$status=json_decode(
			json_encode(
				simplexml_load_string(
					@file_get_contents(
						"http://192.168.2.107:8090/now_playing"
					)
				)
			),
			true
		);
		if (!empty($status)) {
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']!='STANDBY') {
					bosekey("POWER", 0, 107);
					sw('bose107', 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	// END BOSE BUITEN*/
}

// Indien geen zwembad

//if ($status=="Open") {
//	if ($d['steenterras']['s']=='Off') sw('steenterras','On', basename(__FILE__).':'.__LINE__);
//	if ($d['houtterras']['s']=='Off') sw('houtterras','On', basename(__FILE__).':'.__LINE__);
//}
