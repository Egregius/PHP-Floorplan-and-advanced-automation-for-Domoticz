<?php
if ($d['tv']['s']=='Off'||$d['sony']['s']=='Off'||$d['nvidia']['s']=='Off') {
	if ($d['nas']['s']=='Off') shell_exec('/var/www/html/secure/wakenas.sh &');
	if ($d['sony']['s']!='On') {
		sw('sony', 'On', basename(__FILE__).':'.__LINE__);
//		sleep(10);
	}
	if ($d['tv']['s']!='On') {
		sw('tv', 'On', basename(__FILE__).':'.__LINE__);
		sw('lgtv', 'On', basename(__FILE__).':'.__LINE__);
//		sleep(50);
	}
	if ($d['nvidia']['s']!='On') {
		sw('nvidia', 'On', basename(__FILE__).':'.__LINE__);
	}
	if ($d['tv']['s']=='On'&&$d['nvidia']['s']=='On') lgcommand('on');
	if ($d['bose101']['s']=='On'&&$d['bose101']['m']==1&&$d['eettafel']['s']==0&&$d['bose102']['s']=='Off'&&$d['bose104']['s']=='Off'&&$d['bose105']['s']=='Off') {
		sw('bose101', 'Off');
		bosekey("POWER");
		foreach (array('bose102', 'bose103', 'bose104', 'bose105') as $i) {
			if ($d[$i]['s']=='On') {
				sw($i, 'Off');
			}
		}
		if ($d['bose101']['s']=='On'&&$d['bose101']['m']==1) {
			bosevolume(0, 101, basename(__FILE__).':'.__LINE__);
		}
	}
}
store('Weg', 0, basename(__FILE__).':'.__LINE__);
