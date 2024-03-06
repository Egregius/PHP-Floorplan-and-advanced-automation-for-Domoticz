<?php
if ($status=='Open'&&$d['auto']['s']=='On') {
	fbadkamer();
	fhall();
}
if ($d['Weg']['s']>1) {
	if ($status=='Open') sirene('Deur badkamer open');
	else sirene('Deur badkamer dicht');
}
if (past('deurbadkamer')>60) file_get_contents('https://secure.egregius.be/withings/cli.php');
