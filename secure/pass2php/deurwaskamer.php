<?php
if ($d['auto']['s']=='On') {
	if ($status=='Open') {
		$dag=dag();
		if (($d['waskamer']['s']<30&&$d['zon']['s']==0&&$dag==0)||$d['Rwaskamer']['s']>70) sl('waskamer', 30, basename(__FILE__).':'.__LINE__);
		fhall();
		if ($time>strtotime('6:00')&&$time<strtotime('10:00')&&$d['Ralex']['s']==0&&$d['Rwaskamer']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
	}
}
if ($d['Weg']['s']>1) {
	if ($status=='Open') sirene('Deur waskamer open');
	else sirene('Deur waskamer dicht');
}