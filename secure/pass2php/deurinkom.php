<?php
if ($status!=$d['deurinkom']['s']) {
	if ($status=="Open"&&$d['auto']['s']=='On') {
		finkom();
		fliving();
	}
	if ($d['Weg']['s']>0) {
		if ($status=='Open') sirene('Deur inkom open');
		else sirene('Deur inkom dicht');
	}
}