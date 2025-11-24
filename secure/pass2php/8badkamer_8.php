<?php
if ($status=='On') {
	lg(basename(__FILE__).':'.__LINE__.' $status='.$status.' badkamerpower='.$d['badkamerpower']['s']);

	if ($d['lichtbadkamer']['s']>0) sl('lichtbadkamer', 0, basename(__FILE__).':'.__LINE__);
	if ($d['weg']['s']==1&&$d['time']>=$t-3600&&$d['time']<$t+3600) {
		huisthuis();
		sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
	} 
	if ($d['badkamer_set']['m']!=0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	if ($d['badkamerpower']['s']!='Off') {
		usleep(999999);
		lg(basename(__FILE__).':'.__LINE__.' $status='.$status);
		sw('badkamerpower', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if (past('lichtbadkamer')>90) exec('curl -s http://192.168.2.20/secure/runsync.php?sync=weegschaal &');
}