<?php
if ($status!=$d['deuralex']['s']) {
	if ($d['auto']['s']=='On') {
		if ($status=='Open') {
			fhall();
			if ($d['time']>strtotime('7:00')&&$d['time']<strtotime('10:00')&&$d['dag']['s']>0) {
				if ($d['ralex']['s']>0) sl('ralex', 0, basename(__FILE__).':'.__LINE__);
				if ($d['rwaskamer']['s']>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
				if ($d['weg']['s']==0) {
					if ($d['rliving']['s']>0) sl('rliving', 0, basename(__FILE__).':'.__LINE__);
					if ($d['rbureel']['s']>0) sl('rbureel', 0, basename(__FILE__).':'.__LINE__);
					if ($d['rkeukenl']['s']>0) sl('rkeukenl', 0, basename(__FILE__).':'.__LINE__);
					if ($d['rkeukenr']['s']>0) sl('rkeukenr', 0, basename(__FILE__).':'.__LINE__);
					bosevolume(20, 101, basename(__FILE__).':'.__LINE__);
				}
			} elseif ($d['time']>strtotime('7:00')&&$d['time']<strtotime('9:00')) bosevolume(20, 101, basename(__FILE__).':'.__LINE__);
		}
	}
	if ($d['weg']['s']>1) {
		if ($status=='Open') sirene('Deur Alex open');
		else sirene('Deur Alex dicht');
	}
}