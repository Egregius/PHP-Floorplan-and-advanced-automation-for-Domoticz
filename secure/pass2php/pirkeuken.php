<?php
if ($d['auto']['s']=='On') {
	if ($status=="On") {
		fkeuken();
		sirene('Beweging keuken');
	} else {
		if ($d['snijplank']['s']=='On'&&$d['lgtv']['s']=='On'&&TIME>=strtotime('19:00')) {
			sw('snijplank', 'Off', basename(__FILE__).':'.__LINE__);
		}
		if ($d['wasbak']['s']>0&&$d['lgtv']['s']=='On'&&TIME>=strtotime('19:00')) {
			sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
