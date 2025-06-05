<?php
if ($status=='On') {
	$item='rkamerl';
	if ($d['raamkamer']['s']=='Open') {
		if ($d[$item]['s']>85) {
			sl($item, 85, basename(__FILE__).':'.__LINE__);
		} elseif ($d[$item]['s']>70) {
			sl($item, 70, basename(__FILE__).':'.__LINE__);
		} elseif ($d[$item]['s']>40) {
			sl($item, 40, basename(__FILE__).':'.__LINE__);
		} elseif ($d[$item]['s']>1) {
			sl($item, 0, basename(__FILE__).':'.__LINE__);
		}
	} else sl($item, 0, basename(__FILE__).':'.__LINE__);
}
