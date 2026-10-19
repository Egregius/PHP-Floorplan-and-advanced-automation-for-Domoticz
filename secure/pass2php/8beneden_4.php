<?php
if ($status=='On') {
	if(substr($d['boseliving']->m,0,7)=='NoScore') score(220);
	else sw('lampkast', 'On','',true);
}