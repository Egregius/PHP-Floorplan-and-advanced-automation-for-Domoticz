<?php
if ($status=='On') {
	if ($d['eettafel']->s==0) {
		if ($d['time']<=strtotime('9:00')) sl('eettafel', 35, basename(__FILE__).':'.__LINE__);
		else sl('eettafel', 80, basename(__FILE__).':'.__LINE__);
	} else {
		$new=ceil($d['eettafel']->s*1.2);
		if ($new>100) $new=100;
		sl('eettafel', $new);
		$d['eettafel']->s=$new;
	}
}