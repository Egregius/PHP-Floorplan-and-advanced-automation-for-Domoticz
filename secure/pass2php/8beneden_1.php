<?php
if ($status=='On') {
	if ($d['eettafel']->s==0) {
		sl('eettafel', 50, basename(__FILE__).':'.__LINE__);
	} else {
		$new=ceil($d['eettafel']->s*1.1);
		if ($new>100) $new=100;
		sl('eettafel', $new);
		$d['eettafel']->s=$new;
	}
}