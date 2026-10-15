<?php
if ($status=='On') {
	if(substr($d['boseliving']->m,0,7)=='NoScore') score(null);
	 else {
		if (past('pirliving')<300||past('lgtv')<300) {
			sw('zetel', 'On', basename(__FILE__).':'.__LINE__, true);
		}
	}
}
