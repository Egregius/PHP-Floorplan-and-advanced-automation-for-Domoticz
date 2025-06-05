<?php
if ($status=='On') {
	$item='rkamerl';
	if ($d['raamkamer']['s']=='Open') {
		if ($d[$item]['s']<70) {
			sl($item, 70, basename(__FILE__).':'.__LINE__);
		} elseif ($d[$item]['s']<76) {
			sl($item, 76, basename(__FILE__).':'.__LINE__);
		} elseif ($d[$item]['s']<82) {
			sl($item, 82, basename(__FILE__).':'.__LINE__);
		}
	} else {
		if ($d[$item]['s']<100) {
			sl($item, 100, basename(__FILE__).':'.__LINE__);
		}
	}
}