<?php
if ($status>0) {
	$time=time();
	if ($time>strtotime('6:00')&&$time<strtotime('10:00')&&$d['deuralex']['s']=='Open') {
		if ($d['Rwaskamer']['s']>0) {
			sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
