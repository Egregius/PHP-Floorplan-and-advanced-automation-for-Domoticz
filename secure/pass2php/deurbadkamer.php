<?php
if ($status=='Open'&&$d['auto']->s=='On') {
	if ($d['lichtbadkamer']->s<16&&$d['dag']->s<-4&&(!isset($d['8badkamer8'])||(isset($d['8badkamer8'])&&$d['8badkamer8']<$time-10))) {
		if (past('lichtbadkamer')>9) {
			if ($d['time']>$t&&$d['time']<strtotime('21:00')) {
				sl('lichtbadkamer', 16, basename(__FILE__).':'.__LINE__);
			} elseif ($d['lichtbadkamer']->s<9) {
				sl('lichtbadkamer', 9, basename(__FILE__).':'.__LINE__);
			}
		}
	}
	fhall();
}
if ($d['weg']->s>1) {
	if ($status=='Open') sirene('Deur badkamer open');
	else sirene('Deur badkamer dicht');
}
