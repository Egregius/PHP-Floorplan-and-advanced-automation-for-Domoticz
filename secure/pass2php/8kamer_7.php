<?php
if ($status=='On') {
	if ($d['kamer1']->s==0) {
		sl('kamer1', 1, basename(__FILE__).':'.__LINE__);
	} elseif ($d['kamer1']->s>1) {
		$new=floor($d['kamer1']->s*0.65);
		sl('kamer1', $new, basename(__FILE__).':'.__LINE__,true);
	} else sl('kamer1', 0, basename(__FILE__).':'.__LINE__,true);
	resetsecurity();
}
