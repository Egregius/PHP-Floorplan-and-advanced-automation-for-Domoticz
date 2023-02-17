<?php
if ($status=='Open'&&$d['auto']['s']=='On') {
	fbadkamer();
	fhall();
} else {
	if (past('$ 8badkamer-8')>10&&$d['lichtbadkamer']['s']==0) {
		if($d['zon']['s']==0||(TIME>strtotime('5:00')&& TIME<strtotime('7:30'))) $d['lichtbadkamer']['s']=25;
	}
}
if ($status=='Open') sirene('Deur badkamer open');
else sirene('Deur badkamer dicht');
if (past('deurbadkamer')>60) file_get_contents('https://secure.egregius.be/withings/cli.php');
