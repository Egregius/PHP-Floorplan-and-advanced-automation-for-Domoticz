<?php
if ($status=='On') {
	if ($d['kamer']->s==0) {
		sl('kamer', 1, basename(__FILE__).':'.__LINE__);
	} else {
		$new=ceil($d['kamer']->s*1.51);
		if ($new>100) {
			$new=100;
		}
		sl('kamer', $new, basename(__FILE__).':'.__LINE__);
	}
	resetsecurity();
}