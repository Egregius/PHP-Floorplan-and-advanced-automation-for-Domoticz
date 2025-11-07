<?php
if ($status=='On') {
	sw('mac', 'On', basename(__FILE__).':'.__LINE__, true);
	if ($d['dag']['s']<5&&($d['bureel1']['s']<24||$d['bureel2']['s']<24||$d['mac']['s']=='Off')) {
		sl('bureel1', 24, basename(__FILE__).':'.__LINE__, true);
		sl('bureel2', 24, basename(__FILE__).':'.__LINE__, true);
	} elseif ($d['bureel1']['s']>0||$d['bureel2']['s']>0) {
		sl('bureel', 0, basename(__FILE__).':'.__LINE__, true);
	}
}