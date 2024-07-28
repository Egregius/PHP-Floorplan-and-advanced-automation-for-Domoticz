<?php
if ($status!=$d['deurkamer']['s']) {
	if ($d['auto']['s']=='On') {
		if ($status=='Open') {
			fhall();
		}
	}
	if ($d['Weg']['s']>1) {
		if ($status=='Open') sirene('Deur kamer open');
		else sirene('Deur kamer dicht');
	}
}