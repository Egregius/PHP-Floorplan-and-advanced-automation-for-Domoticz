<?php
$data=apcu_fetch('8kamer_8');
if ($data===false) {
	if ($status=='On') {
		if ($d['kamer']['s']==0) {
			sl('kamer', 1, basename(__FILE__).':'.__LINE__);
		} elseif ($d['kamer']['s']>1) {
			$new=floor($d['kamer']['s']*0.65);
			sl('kamer', $new, basename(__FILE__).':'.__LINE__,true);
		} else sl('kamer', 0, basename(__FILE__).':'.__LINE__,true);
		resetsecurity();
	}
	apcu_store('8kamer_8', 1, 1);
}
