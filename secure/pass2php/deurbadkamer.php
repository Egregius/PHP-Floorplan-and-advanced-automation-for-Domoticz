<?php
if ($status!=$d['deurbadkamer']['s']) {
	if ($status=='Open'&&$d['auto']['s']=='On') {
		if ($d['lichtbadkamer']['s']<16&&$d['dag']<3) {
			$time=time();
			$last=mget('lichtbadkamer');
			$pastm=$time-$last;
			$past8=past('$ 8badkamer-8');
			lg('pastm='.$pastm.', $past8='.$past8);
			if ($pastm>9&&$past8>9) {
				$t=t();
				if ($time>$t&&$time<strtotime('21:00')) sl('lichtbadkamer', 16, basename(__FILE__).':'.__LINE__);
				elseif ($d['lichtbadkamer']['s']<8) sl('lichtbadkamer', 8, basename(__FILE__).':'.__LINE__);
			}
		}
		fhall();
	}
	if ($d['Weg']['s']>1) {
		if ($status=='Open') sirene('Deur badkamer open');
		else sirene('Deur badkamer dicht');
	}
}