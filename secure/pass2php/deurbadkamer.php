<?php
if ($status=='Open'&&$d['auto']['s']=='On') {
	fbadkamer();
	fhall();
} else {
	if (past('$ 8badkamer-8')>10&&$d['lichtbadkamer']['s']==0) {
		$time=time();
		if($d['zon']['s']==0||($time>strtotime('5:00')&& $time<strtotime('7:30'))) $d['lichtbadkamer']['s']=25;
	}
}
if ($d['Weg']['s']>1) {
	if ($status=='Open') sirene('Deur badkamer open');
	else sirene('Deur badkamer dicht');
}
if (past('deurbadkamer')>60) file_get_contents('https://secure.egregius.be/withings/cli.php');
