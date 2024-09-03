<?php
if ($status!=$d['deurbadkamer']['s']) {
	if ($status=='Open'&&$d['auto']['s']=='On') {
		fbadkamer();
		fhall();
	}
	if ($d['Weg']['s']>1) {
		if ($status=='Open') sirene('Deur badkamer open');
		else sirene('Deur badkamer dicht');
	}
}