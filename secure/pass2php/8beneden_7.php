<?php
if ($status=='On') {
	if(substr($d['boseliving']->m,0,7)=='NoScore') score(350);
	else {
		sw('boseliving', 'On', basename(__FILE__).':'.__LINE__);
		storemode('bose101', 1, basename(__FILE__).':'.__LINE__);
	}
}