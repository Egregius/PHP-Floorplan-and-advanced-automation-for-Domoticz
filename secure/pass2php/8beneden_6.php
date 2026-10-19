<?php
if ($status=='On') {
	if(substr($d['boseliving']->m,0,7)=='NoScore') score(290);
	else {
		if ($d['mac']->s=='Off') sw('mac', 'On', basename(__FILE__).':'.__LINE__, true);
		if ($d['dag']->s<1&&$d['bureellinks']->s<30) {
			sl('bureellinks', 30, basename(__FILE__).':'.__LINE__, true);
		} elseif ($d['bureellinks']->s>0) {
			sl('bureellinks', 0, basename(__FILE__).':'.__LINE__, true);
		}
	}
}