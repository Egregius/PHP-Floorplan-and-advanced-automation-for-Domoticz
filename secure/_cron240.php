<?php
$d=fetchdata();
$user='cron240';
if ($d['auto']['s']=='On') {
	if ($d['Weg']['s']==0){
		if ($d['living_temp']['s']>22&&$d['living_temp']['s']>$d['living_set']['s']+1&&$d['brander']['s']=='On') alert('livingtemp', 'Te warm in living, '.$d['living_temp']['s'].' °C. Controleer verwarming', 3600, false);
		if ($time>=strtotime('16:00')) {
			if ($d['raamalex']['s']=='Open'&&$d['alex_temp']['s']<12) alert('raamalex', 'Raam Alex dicht doen, '.$d['alex_temp']['s'].' °C.', 1799,	false);
			if ($d['raamkamer']['s']=='Open'&&$d['alex_temp']['s']<10) alert('raamkamer', 'Raam kamer dicht doen, '.$d['kamer_temp']['s'].' °C.', 1799,	false);
		}
		if ($d['heating']['s']>0) { //Heating
			if ($d['buiten_temp']['s']<$d['kamer_temp']['s']
				&&$d['buiten_temp']['s']<$d['waskamer_temp']['s']
				&&$d['buiten_temp']['s']<$d['alex_temp']['s']
				&&($d['raamkamer']['s']=='Open'
				||$d['raamwaskamer']['s']=='Open'
				||$d['raamalex']['s']=='Open')
				&&($d['kamer_temp']['s']<10
				||$d['waskamer_temp']['s']<10
				||$d['alex_temp']['s']<10)
			) {
				alert(
					'ramenboven',
					'Ramen boven dicht doen, te koud buiten.
					Buiten = '.round($d['buiten_temp']['s'], 1).',
					kamer = '.$d['kamer_temp']['s'].',
					waskamer = '.$d['waskamer_temp']['s'].',
					Alex = '.$d['alex_temp']['s'],
					3600,
					false,
					2,
				);
			}
		} elseif ($d['heating']['s']<0) { //Cooling
			if (($d['buiten_temp']['s']>$d['kamer_temp']['s']
				&&$d['buiten_temp']['s']>$d['waskamer_temp']['s']
				&&$d['buiten_temp']['s']>$d['alex_temp']['s'])
				&&$d['buiten_temp']['s']>=18
				&&($d['kamer_temp']['s']>=18
				||$d['waskamer_temp']['s']>=18
				||$d['alex_temp']['s']>=18)
				&&($d['raamkamer']['s']=='Open'
				||($d['raamwaskamer']['s']=='Open'&&($d['deurkamer']['s']=='Open'||$d['deuralex']['s']=='Open'))
				||$d['raamalex']['s']=='Open')
			) {
				alert(
					'ramenboven',
					'Ramen boven dicht doen, te warm buiten.
					Buiten = '.round($d['buiten_temp']['s'], 1).',
					kamer = '.$d['kamer_temp']['s'].',
					waskamer = '.$d['waskamer_temp']['s'].',
					Alex = '.$d['alex_temp']['s'],
					3600,
					false,
					2,
				);
			}
		}
		if ($d['wasdroger']['s']=='On') {
			if (past('wasdroger_kWh')>600) {
				if ($d['wasdroger_kWh']['s']<10) {
					alert('wasdrogervol','Wasdroger vol',60,false,2);
					sw('wasdroger', 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}
