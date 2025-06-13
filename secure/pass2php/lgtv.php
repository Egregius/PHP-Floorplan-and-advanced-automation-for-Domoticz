<?php
if ($status=='On') {
	if ($d['auto']['s']=='On') hassinput('media_player','select_source','media_player.lgtv','HDMI 4');
//	$d['time']=time();
//	if ($d['time']>=strtotime('19:00')) {
//		if ($d['kristal']['s']=='Off'&&$d['Buiten_temp']['s']<10&) sw('kristal', 'On', basename(__FILE__).':'.__LINE__);
//		if ($d['heating']['s']>0&&$d['Buiten_temp']['s']<10&&$d['rliving']['s']<100) sl('rliving', 100, basename(__FILE__).':'.__LINE__);
//		elseif ($d['rliving']['s']<25) sl('rliving', 25, basename(__FILE__).':'.__LINE__);
//		if ($d['rbureel']['s']<70&&$d['Buiten_temp']['s']<10) sl('rbureel', 69, basename(__FILE__).':'.__LINE__);
//		if ($d['rkeukenl']['s']<55&&$d['Buiten_temp']['s']<10) sl('rkeukenl', 55, basename(__FILE__).':'.__LINE__);
//		if ($d['rkeukenr']['s']<55&&$d['Buiten_temp']['s']<10) sl('rkeukenr', 55, basename(__FILE__).':'.__LINE__);
//		hass('switch','turn_off','switch.plug7_socket_1');
//	}
	if ($d['bose101']['m']==1&&$d['bose101']['s']=='On'&&$d['bose102']['s']=='Off'&&$d['bose103']['s']=='Off'&&$d['bose104']['s']=='Off'&&$d['bose105']['s']=='Off'&&$d['bose106']['s']=='Off'&&$d['bose107']['s']=='Off'&&$d['eettafel']['s']==0) {
		bosekey("POWER", 0, 101, basename(__FILE__).':'.__LINE__);
		sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
	}
}