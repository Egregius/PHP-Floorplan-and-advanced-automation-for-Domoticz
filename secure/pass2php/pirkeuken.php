<?php
if ($status=='On'&&$d['auto']->s=='On'&&$d['weg']->s==0) {
	if ($status=="On") fkeuken();
	else {
		if ($d['snijplank']->s=='On'&&$d['media']->s=='On'&&$d['time']>=strtotime('19:00')) {
			sw('snijplank', 'Off', basename(__FILE__).':'.__LINE__);
		}
		if ($d['wasbak']->s>0&&$d['media']->s=='On'&&$d['time']>=strtotime('19:00')) {
			sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
		}
	}
} elseif ($status=='On'&&$d['weg']->s>0&&past('weg')>60) sirene('Beweging keuken');