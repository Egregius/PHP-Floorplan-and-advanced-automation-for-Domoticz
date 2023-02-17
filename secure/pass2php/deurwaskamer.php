<?php
if ($d['auto']['s']=='On') {
	if ($status=='Open') {
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		if (($d['waskamer']['s']<100&&$d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder))||$d['Rwaskamer']['s']>70) sl('waskamer', 100, basename(__FILE__).':'.__LINE__);
		fhall();
		if (TIME>strtotime('6:00')&&TIME<strtotime('10:00')&&$d['Ralex']['s']==0&&$d['Rwaskamer']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
	}
}
if ($status=='Open') sirene('Deur waskamer open');
else sirene('Deur waskamer dicht');
