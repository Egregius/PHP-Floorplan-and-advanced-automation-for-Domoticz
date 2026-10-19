<?php
if ($status=='On') {
	if(substr($d['boseliving']->m,0,7)=='NoScore') score(180);
	else {
		sw('media', 'On', basename(__FILE__).':'.__LINE__, true);
		sw('zetel', 'On', basename(__FILE__).':'.__LINE__, true);
		if ($d['rbureel']->s<100&&$d['time']>=strtotime('18:00')) sl('rbureel', 100, basename(__FILE__).':'.__LINE__, true);
	}
}