<?php
if ($status=='On') {
	if ($d['eettafel']->s==0) {
		sl('eettafel', 35, basename(__FILE__).':'.__LINE__);
	} else {
		$new=floor($d['eettafel']->s*0.75);
		if($new<10) $new=0;
		sl('eettafel', $new);
	}
}