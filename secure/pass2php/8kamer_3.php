<?php
if ($status=='On') {
	if ($d['kamer1']->s==0) {
		sl('kamer1', 1, basename(__FILE__).':'.__LINE__);
	} else {
		$new=ceil($d['kamer1']->s*1.51);
		if ($new>100) {
			$new=100;
		}
		sl('kamer1', $new, basename(__FILE__).':'.__LINE__);
	}
	resetsecurity();
}