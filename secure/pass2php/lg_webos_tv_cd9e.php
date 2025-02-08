<?php
if ($status!=$d[$device]['s']) store($device,$status, basename(__FILE__).':'.__LINE__);
//lg(print_r($topic,true));

if ($status=='On') {
	if ($d['auto']['s']=='On') hassinput('media_player','select_source','media_player.lg_webos_tv_cd9e','SHIELD');
	if ($d['dag']<=1&&$d['kristal']['s']=='Off') sw('kristal', 'On', basename(__FILE__).':'.__LINE__);
	$time=time();
	if ($time>=strtotime('18:00')) {
		if ($d['Rliving']['s']<30) sl('Rliving', 30, basename(__FILE__).':'.__LINE__);
		if ($d['Rbureel']['s']<70) sl('Rbureel', 69, basename(__FILE__).':'.__LINE__);
		if ($d['RkeukenL']['s']<55) sl('RkeukenL', 55, basename(__FILE__).':'.__LINE__);
		if ($d['RkeukenR']['s']<55) sl('RkeukenR', 55, basename(__FILE__).':'.__LINE__);
		hass('switch','turn_off','switch.plug7_socket_1');
	}
	if ($d['bose101']['m']==1&&$d['bose101']['s']=='On'&&$d['bose102']['s']=='Off'&&$d['bose103']['s']=='Off'&&$d['bose104']['s']=='Off'&&$d['bose105']['s']=='Off'&&$d['bose106']['s']=='Off'&&$d['bose107']['s']=='Off'&&$d['eettafel']['s']==0) {
		bosekey("POWER", 0, 101, basename(__FILE__).':'.__LINE__);
		sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
	}
}