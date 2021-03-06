<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
//lg(__FILE__.':'.$s);
$user='cron240';
if ($d['auto']['s']=='On') {
	if ($d['Weg']['s']==0){
		if ($d['living_temp']['s']>22&&$d['brander']['s']=='On') alert('livingtemp', 'Te warm in living, '.$living_temp.' °C. Controleer verwarming', 3600, false);
		if (TIME>strtotime('16:00')) {
			if ($d['raamalex']['s']=='Open'&&$d['alex_temp']['s']<14) alert('raamalex', 'Raam Alex dicht doen, '.$alex_temp.' °C.', 1800,	false);
		}
		if ($d['heating']['s']>0) { //Heating
			if ($d['buiten_temp']['s']<$d['kamer_temp']['s']
				&&$d['buiten_temp']['s']<$d['tobi_temp']['s']
				&&$d['buiten_temp']['s']<$d['alex_temp']['s']
				&&($d['raamkamer']['s']=='Open'
				||$d['raamtobi']['s']=='Open'
				||$d['raamalex']['s']=='Open')
				&&($d['kamer_temp']['s']<10
				||$d['tobi_temp']['s']<10
				||$d['alex_temp']['s']<10)
			) {
				alert(
					'ramenboven',
					'Ramen boven dicht doen, te koud buiten.
					Buiten = '.round($d['buiten_temp']['s'], 1).',
					kamer = '.$d['kamer_temp']['s'].',
					Tobi = '.$d['tobi_temp']['s'].',
					Alex = '.$d['alex_temp']['s'],
					3600,
					false,
					2,
					false
				);
			}
		} elseif ($d['heating']['s']<0) { //Cooling
			if (($d['buiten_temp']['s']>$d['kamer_temp']['s']
				&&$d['buiten_temp']['s']>$d['tobi_temp']['s']
				&&$d['buiten_temp']['s']>$d['alex_temp']['s'])
				&&$d['buiten_temp']['s']>=18
				&&($d['kamer_temp']['s']>=18
				||$d['tobi_temp']['s']>=18
				||$d['alex_temp']['s']>=18)
				&&($d['raamkamer']['s']=='Open'
				||$d['raamtobi']['s']=='Open'
				||$d['raamalex']['s']=='Open')
			) {
				alert(
					'ramenboven',
					'Ramen boven dicht doen, te warm buiten.
					Buiten = '.round($d['buiten_temp']['s'], 1).',
					kamer = '.$d['kamer_temp']['s'].',
					Tobi = '.$d['tobi_temp']['s'].',
					Alex = '.$d['alex_temp']['s'],
					3600,
					false,
					2,
					false
				);
			}
		}
	}
}
