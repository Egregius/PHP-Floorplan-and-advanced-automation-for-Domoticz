<?php
if ($status!=$d['deurgarage']['s']) {
	if ($status=='Open'&&$d['auto']['s']=='On') {
		fgarage();
		fkeuken();
		fliving();
	}
	if ($d['Weg']['s']>0) {
		if ($status=='Open') sirene('Deur garage open');
		else sirene('Deur garage dicht');
	}
}