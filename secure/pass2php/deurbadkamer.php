<?php
	lg(__LINE__);
//if ($status!=$d['deurbadkamer']['s']) {
	lg(__LINE__);
//	if ($status=='Open'&&$d['auto']['s']=='On') {
			lg(__LINE__);
		if ($d['lichtbadkamer']['s']<16/*&&$d['dag']<3*/) {
				lg(__LINE__);
			$time=time();
			$last=mget('lichtbadkamer');
			$past=$time-$last;
			lg('last='.$last.', pastm='.$pastm;
			if ($pastm>9) {
				$t=t();
				if ($time>$t&&$time<strtotime('21:00')) sl('lichtbadkamer', 16, basename(__FILE__).':'.__LINE__);
				elseif ($d['lichtbadkamer']['s']<8) sl('lichtbadkamer', 8, basename(__FILE__).':'.__LINE__);
			}
		}
		fhall();
//	}
	if ($d['Weg']['s']>1) {
		if ($status=='Open') sirene('Deur badkamer open');
		else sirene('Deur badkamer dicht');
	}
//}