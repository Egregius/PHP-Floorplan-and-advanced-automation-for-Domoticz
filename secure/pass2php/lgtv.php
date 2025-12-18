<?php
if ($status=='On') {
	if ($d['bose101']['m']==1&&$d['boseliving']['s']=='On'&&$d['bose101']['s']=='On'&&$d['bose102']['s']=='Off'&&$d['bose103']['s']=='Off'&&$d['bose104']['s']=='Off'&&$d['bose105']['s']=='Off'&&$d['bose106']['s']=='Off'&&$d['bose107']['s']=='Off'&&$d['eettafel']['s']==0) {
		bosekey("POWER", 0, 101, basename(__FILE__).':'.__LINE__);
		storesm('bose101', 'Off', 'Offline', basename(__FILE__).':'.__LINE__);
		sw('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
	}
} elseif ($status=='Off') {
	if($d['boseliving']['s']=='Off') {
		$am=strtotime('6:00');
		$pm=strtotime('20:00');
		if($time>=$am&&$time<$pm) sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
	}
}