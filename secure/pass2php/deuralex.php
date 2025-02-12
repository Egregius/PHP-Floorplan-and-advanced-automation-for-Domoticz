<?php
if ($status!=$d['deuralex']['s']) {
	if ($d['auto']['s']=='On') {
		if ($status=='Open') {
			fhall();
			if ($time>strtotime('7:00')&&$time<strtotime('10:00')&&$d['dag']>0) {
				if ($d['Ralex']['s']>0) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Rwaskamer']['s']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Weg']['s']==0) {
					if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
					if ($d['Rbureel']['s']>0) sl('Rbureel', 0, basename(__FILE__).':'.__LINE__);
					if ($d['RkeukenL']['s']>0) sl('RkeukenL', 0, basename(__FILE__).':'.__LINE__);
					if ($d['RkeukenR']['s']>0) sl('RkeukenR', 0, basename(__FILE__).':'.__LINE__);
					bosevolume(20, 101, basename(__FILE__).':'.__LINE__);
				}
			} elseif ($time>strtotime('7:00')&&$time<strtotime('9:00') bosevolume(20, 101, basename(__FILE__).':'.__LINE__);
		}
	}
	if ($d['Weg']['s']>1) {
		if ($status=='Open') sirene('Deur Alex open');
		else sirene('Deur Alex dicht');
	}
}