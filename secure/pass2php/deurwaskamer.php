<?php
if ($status!=$d['deurwaskamer']['s']) {
	if ($d['auto']['s']=='On'&&$status=='Open'&&$d['Weg']['s']==0&&$time>strtotime('6:00')) {
		if (($d['waskamer']['s']<30&&$d['zon']==0&&$d['dag']==0)||$d['Rwaskamer']['s']>70) sl('waskamer', 30, basename(__FILE__).':'.__LINE__);
		if ($time<strtotime('10:00')&&$d['Ralex']['s']==0&&$d['Rwaskamer']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
	} elseif ($d['Weg']['s']>1) {
		if ($status=='Open') sirene('Deur waskamer open');
		else sirene('Deur waskamer dicht');
	}
}