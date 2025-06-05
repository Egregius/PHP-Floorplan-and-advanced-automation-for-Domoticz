<?php
if ($d['achterdeur']['s']=='Open'&&$d['bose102']['s']=='On'&&$d['bose103']['s']=='On'&&$d['bose105']['s']=='On') {
	foreach(array(102, 103, 105) as $i) {
		$volume=json_decode(
			json_encode(
				simplexml_load_string(
					file_get_contents("http://192.168.2.$i:8090/volume")
				)
			),
			true
		);
		if ($status=='On') {
			$cv=floor($volume['actualvolume']*1.1);
		} else {
			$cv=floor($volume['actualvolume']*0.9);
		}
		bosevolume($cv, $i);
	}
} else {
	if ($status=='On') {
		if ($d['water']['s']=='Off') sw('water', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['water']['m']==0) storemode('water', 300, basename(__FILE__).':'.__LINE__);
		elseif ($d['water']['m']==300) storemode('water', 1800, basename(__FILE__).':'.__LINE__);
		elseif ($d['water']['m']==1800) storemode('water', 7200, basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['water']['s']=='On') sw('water', 'Off', basename(__FILE__).':'.__LINE__);
	}
}