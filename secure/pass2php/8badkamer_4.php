<?php
if ($status=='On') {
	if ($d['badkamerpower']['s']!='On') {
		sw('badkamerpower', 'On', basename(__FILE__).':'.__LINE__);
//		usleep(500000);
//		sl('lichtbadkamer', 100, basename(__FILE__).':'.__LINE__);
//		usleep(500000);
//		sl('lichtbadkamer', 100, basename(__FILE__).':'.__LINE__);
		usleep(500000);
	}
	sl('lichtbadkamer', 100, basename(__FILE__).':'.__LINE__);
	store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
	if ($d['weg']['s']==1&&$d['living_set']['m']==0&&$d['time']>$t-7200) {
		if ($d['time']<$t) storemode('living_set', 2, basename(__FILE__) . ':' . __LINE__);
		if ($d['time']<$t+3600&&$d['boseliving']['s']=='Off') sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
	}
}
