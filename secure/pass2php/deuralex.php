<?php
if ($d['auto']['s']=='On') {
	if ($status=='Open') {
		fhall();
		if ($time>strtotime('7:00')&&$time<strtotime('10:00')) {
			if ($d['Ralex']['s']>0) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rwaskamer']['s']>0) sl('Rwaskamer', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
if ($status=='Open') sirene('Deur Alex open');
else sirene('Deur Alex dicht');
