<?php
store($device,$status, basename(__FILE__).':'.__LINE__);
if ($status=='Playing') {
	if ($d['wasbak']['s']>0) sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
	if ($d['pirliving']['s']=='Off'
		&&$d['pirgarage']['s']=='Off'
		&&$d['bose101']['m']==1
		&&$d['bose101']['s']=='On'
		&&$d['bose102']['s']=='Off'
		&&$d['bose103']['s']=='Off'
		&&$d['bose104']['s']=='Off'
		&&$d['bose105']['s']=='Off'
		&&$d['bose106']['s']=='Off'
		&&$d['bose107']['s']=='Off'
		&&past('bose101')>30
		&&past('bose102')>30
		&&past('bose103')>30
		&&past('bose104')>30
		&&past('bose105')>30
		&&past('bose106')>30
		&&past('bose107')>30
		&&($d['Weg']['s']>0||($d['lgtv']['s']=='On'&&$d['eettafel']['s']==0))
	) {
		$status=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.101:8090/now_playing"))),true);
		if (!empty($status)) {
			if (isset($status['@attributes']['source'])) {
				if ($status['@attributes']['source']!='STANDBY') {
					bosekey("POWER", 0, 101);
					if ($d['bose101']['s']!='Off') sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose102']['s']!='Off') sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose103']['s']!='Off') sw('bose103', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose104']['s']!='Off') sw('bose104', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose105']['s']!='Off') sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose106']['s']!='Off') sw('bose106', 'Off', basename(__FILE__).':'.__LINE__);
					if ($d['bose107']['s']!='Off') sw('bose107', 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
} //elseif ($status=='paused') fkeuken();