<?php
if ($status=='On') {
	if(substr($d['boseliving']->m,0,7)=='NoScore') score(180);
	else sw('ipaddock', 'On');
}