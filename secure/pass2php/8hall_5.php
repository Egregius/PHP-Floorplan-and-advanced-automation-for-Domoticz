<?php
if ($status=='On') {
	if ($d['auto']['s']=='On') {
		$d['weg']['s']==0;
		finkom(true);
		fhall();
	}
	huisthuis();
	$t=t();
	if ($d['weg']['s']==1&&$d['time']>=$t-3600&&$d['time']<$t+3600) sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
}