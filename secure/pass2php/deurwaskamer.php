<?php
/*if ($d['auto']['s']=='On'&&$status=='Open'&&$d['weg']['s']==0&&$d['time']>strtotime('6:00')) {
	if (($d['waskamer']['s']<30&&$d['zon']==0&&$d['dag']['s']<0)||$d['rwaskamer']['s']>70) sl('waskamer', 30, basename(__FILE__).':'.__LINE__);
	if ($d['time']<strtotime('10:00')&&$d['ralex']['s']==0&&$d['rwaskamer']>0) sl('rwaskamer', 0, basename(__FILE__).':'.__LINE__);
} */
if ($d['weg']['s']>1) {
	if ($status=='Open') sirene('Deur waskamer open');
	else sirene('Deur waskamer dicht');
}
