<?php
if ($status=='On') {
	if ($d['lichtbadkamer']->s>0) sl('lichtbadkamer', 0, basename(__FILE__).':'.__LINE__);
	if ($d['weg']->s==1&&$d['time']>=$t-3600&&$d['time']<$t+3600) {
		huisthuis();
		if($d['boseliving']->s=='Off') sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
	}
	if ($d['badkamer_set']->m!=0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	if ($d['badkamerpower']->s!='Off') {
		sw('badkamerpower', 'Off', basename(__FILE__).':'.__LINE__);
		if($d['bose103']->s=='On') storesm('bose103','Off',0, basename(__FILE__).':'.__LINE__);
	}
	if (!isset($weegschaal)||$weegschaal<$time-300) {
		lg('Fetch weegschaal');
		exec('curl -4 http://192.168.20.21:9000/hooks/weegschaal -H "Content-Type: application/json" &');
		$weegschaal=$time;
	}
}
