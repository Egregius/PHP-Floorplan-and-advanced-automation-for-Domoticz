<?php
$user='cron300';
if ($d['weg']->s>0) {
	if ($d['kookplaat']->s=='On') sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['dysonlader']->s=='On') sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['steenterras']->s=='On') sw('steenterras','Off', basename(__FILE__).':'.__LINE__);
	if ($d['tuintafel']->s=='On') sw('tuintafel','Off', basename(__FILE__).':'.__LINE__);
	if ($d['weg']->s>1) {
		foreach (['living_set','alex_set','kamer_set','badkamer_set'/*,'eettafel','zithoek'*/,'luifel'] as $i) {
			if ($d[$i]->m!=0&&$d[$i]->s!='D'&&past($i)>43200) storemode($i, 0, basename(__FILE__).':'.__LINE__);
		}
	}
} else {
	if ($d['dysonlader']->s=='On'&&past('dysonlader')>3600) sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
}

if ($d['auto']->s!='On'&&past('auto')>43200) {
	sw('auto', 'On', basename(__FILE__).':'.__LINE__);
	alert('AUTO','AUTO ingeschakeld na 12 uur',60,false,3);
}

if ($d['zolderg']->s=='On'&&past('zolderg')>7200&&past('pirgarage')>7200) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);

republishmqtt();
