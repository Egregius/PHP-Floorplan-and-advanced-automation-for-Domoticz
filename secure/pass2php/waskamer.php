<?php
if ($status>0) {
	$time=time();
	if ($d['waskamer']['m']!=0&&$time>strtotime('6:00')&&$time<strtotime('12:00')) {
		storemode('waskamer', 0, basename(__FILE__).':'.__LINE__);
	}
	if ($time>strtotime('6:00')&&$time<strtotime('10:00')&&$d['deuralex']['s']=='Open') {
		if ($d['Rwaskamer']['s']>0) {
			sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
