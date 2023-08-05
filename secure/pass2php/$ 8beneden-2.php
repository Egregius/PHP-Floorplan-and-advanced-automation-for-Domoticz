<?php
if ($status=='On') {
	if ($d['Rbureel']['s']>30||$d['RkeukenL']['s']>30||$d['RkeukenR']['s']>30) {
		$item='Rbureel';
		if ($d[$item]['s']>30) {
			sl($item, 30, basename(__FILE__).':'.__LINE__);
		}
		$item='RkeukenL';
		if ($d[$item]['s']>30) {
			sl($item, 30, basename(__FILE__).':'.__LINE__);
		}
		$item='RkeukenR';
		if ($d[$item]['s']>30) {
			sl($item, 30, basename(__FILE__).':'.__LINE__);
		}
	} else {
		$level=0;
		$item='Rbureel';
		if ($d[$item]['s']>$level) {
			sl($item, $level, basename(__FILE__).':'.__LINE__);
		}
		$item='RkeukenL';
		if ($d[$item]['s']>$level) {
			sl($item, $level, basename(__FILE__).':'.__LINE__);
		}
		$item='RkeukenR';
		if ($d[$item]['s']>$level) {
			sl($item, $level, basename(__FILE__).':'.__LINE__);
		}
	}
}
