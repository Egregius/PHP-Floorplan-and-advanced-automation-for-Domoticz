<?php
if ($status=='On') {
	if ($d['badkamerpower']['s']!='On') {
		sw('badkamerpower', 'On', basename(__FILE__).':'.__LINE__);
//		usleep(500000);
//		sl('lichtbadkamer', 50, basename(__FILE__).':'.__LINE__);
//		usleep(500000);
//		sl('lichtbadkamer', 50, basename(__FILE__).':'.__LINE__);
		usleep(500000);
	}
	sl('lichtbadkamer', 50, basename(__FILE__).':'.__LINE__);
	store('deurbadkamer', $d['deurbadkamer']['s'], basename(__FILE__).':'.__LINE__);
}
