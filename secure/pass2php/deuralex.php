<?php
if ($status=='Open') {
	if ($d['auto']['s']=='On') {
		fhall();
		if ($d['time']>strtotime('7:00')&&$d['time']<strtotime('10:00')/*&&$d['dag']['s']>0*/) {
			if ($d['ralex']['s']>0) sl('ralex', 0, basename(__FILE__).':'.__LINE__);
			if ($d['rwaskamer']['s']>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['weg']['s']==0) {
				if($d['heating']['s']>0) $zonelevatie=-5.5;
				elseif($d['heating']['s']==0)$zonelevatie=-6.2;
				else $zonelevatie=-7;
				if($d['dag']['s']>$zonelevatie) {
					if ($d['rliving']['s']>0) sl('rliving', 0, basename(__FILE__).':'.__LINE__);
					if ($d['rbureel']['s']>0) sl('rbureel', 0, basename(__FILE__).':'.__LINE__);
					if ($d['rkeukenl']['s']>0) sl('rkeukenl', 0, basename(__FILE__).':'.__LINE__);
					if ($d['rkeukenr']['s']>0) sl('rkeukenr', 0, basename(__FILE__).':'.__LINE__);
				}
				if ($d['bose101']['s']=='On') bosevolume(22, 101, basename(__FILE__).':'.__LINE__);
				if ($d['bose105']['s']=='On') bosevolume(32, 105, basename(__FILE__).':'.__LINE__);
			}
		}
	}
	if ($d['alexslaapt']['s']==1) store('alexslaapt', 0);
} else {
	if ($d['alexslaapt']['s']==0&&$d['alex']['s']==0&&($d['time']>=strtotime('19:30')||$d['time']<strtotime('7:00'))) store('alexslaapt', 1);
}
if ($d['weg']['s']>1) {
	if ($status=='Open') sirene('Deur Alex open');
	else sirene('Deur Alex dicht');
}
