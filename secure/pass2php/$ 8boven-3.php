<?php
if ($status=='On') {
	$item='Ralex';
	if ($d['heating']['s']>=0) {
		sl($item, 0, basename(__FILE__).':'.__LINE__);
	} else {
		$half=45;
		$lijntjes=78;
		$itemstatus=$d[$item]['s'];
		if ($itemstatus>$half) {
			sl($item, $half, basename(__FILE__).':'.__LINE__);
		} elseif ($itemstatus>$lijntjes) {
			sl($item, $lijntjes, basename(__FILE__).':'.__LINE__);
		} else {
			sl($item, 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
