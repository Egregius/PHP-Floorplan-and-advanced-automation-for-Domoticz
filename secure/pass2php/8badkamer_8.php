<?php
if ($status=='On') {
	setCache('lichtbadkamertijd', $d['time']);
	if ($d['lichtbadkamer']['s']>0) sl('lichtbadkamer', 0, basename(__FILE__).':'.__LINE__);
	if ($d['weg']['s']==1&&$d['time']>$t-1800&&$d['time']<$t+2700) {
		huisthuis();
	} 
	if ($d['time']>$t-3600&&$d['time']<$t+1800) {
		if ($d['badkamer_set']['m']!=0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
		if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['badkamervuur1']['s']=='On') sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['weg']['s']==1&&$d['time']>=$t-3600&&$d['time']<$t+3600) {
		sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
//		sw('mac', 'On', basename(__FILE__).':'.__LINE__);
	}
	if (past('lichtbadkamer')>90) exec('curl -s http://192.168.2.20/secure/runsync.php?sync=weegschaal &');
}