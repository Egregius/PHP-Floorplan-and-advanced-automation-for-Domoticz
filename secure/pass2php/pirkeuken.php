<?php
if ($status=='On'&&$d['auto']['s']=='On'&&$d['Weg']['s']==0) {
	if ($status=="On") fkeuken();
	else {
		$time=time();
		if ($d['snijplank']['s']=='On'&&$d['Media']['s']=='On'&&$time>=strtotime('19:00')) {
			sw('snijplank', 'Off', basename(__FILE__).':'.__LINE__);
		}
		if ($d['wasbak']['s']>0&&$d['Media']['s']=='On'&&$time>=strtotime('19:00')) {
			sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
if ($status=='On'&&$d['Weg']['s']>0) sirene('Beweging keuken');