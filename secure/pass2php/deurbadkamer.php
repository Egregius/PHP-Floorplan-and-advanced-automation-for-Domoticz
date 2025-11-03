<?php
if ($status!=$d['deurbadkamer']['s']) {
	if ($status=='Open'&&$d['auto']['s']=='On') {
		if ($d['lichtbadkamer']['s']<16&&$d['dag']['s']<-4) {
			$time=time();
			$last=getCache('lichtbadkamertijd');
			$past=$time-$last;
			if ($past>9) {
				if ($time>$t&&$time<strtotime('21:00')) sl('lichtbadkamer', 16, basename(__FILE__).':'.__LINE__);
				elseif ($d['lichtbadkamer']['s']<8) sl('lichtbadkamer', 8, basename(__FILE__).':'.__LINE__);
			}
		}
		fhall();
	}
	if ($d['weg']['s']>1) {
		if ($status=='Open') sirene('Deur badkamer open');
		else sirene('Deur badkamer dicht');
	}
}