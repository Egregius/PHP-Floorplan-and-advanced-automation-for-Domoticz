<?php
if ($status>0) {
	if ($d['waskamer']['m']!=0&&TIME>strtotime('6:00')&&TIME<strtotime('12:00')) {
		storemode('waskamer', 0, basename(__FILE__).':'.__LINE__);
	}
	if (TIME>strtotime('6:00')&&TIME<strtotime('10:00')&&$d['deuralex']['s']=='Open') {
		if ($d['Rwaskamer']['s']>0) {
			sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
