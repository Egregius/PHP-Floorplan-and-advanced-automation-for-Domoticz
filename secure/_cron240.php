<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$user='cron240';
if ($d['auto']['s']=='On') {
	if ($d['Weg']['s']==0){
		$devices=array('tobiZ','alexZ',/*'livingZ','livingZZ',*/'kamerZ');
        foreach ($devices as $device) {
            if (past($device)>43150) {
                alert($device, $device.' geen communicatie sinds '.strftime("%k:%M:%S", $d[$device]['t']), 14400);
            }
        }
        if ($d['living_temp']['s']>22&&$d['brander']['s']=='On') {
			alert('livingtemp', 'Te warm in living, '.$living_temp.' °C. Controleer verwarming', 3600, false);
		}
		if (TIME>strtotime('16:00')) {
			if ($d['raamalex']['s']=='Open'&&$d['alex_temp']['s']<14) {
				alert('raamalex', 'Raam Alex dicht doen, '.$alex_temp.' °C.', 1800,	false);
			}
		}
		if ($d['heating']['s']>=2) { //Heating
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
                    7200,
                    false,
                    2,
                    false
                );
            }
        } elseif ($d['heating']['s']==1) { //Cooling
            if (($d['buiten_temp']['s']>$d['kamer_temp']['s']+1
                &&$d['buiten_temp']['s']>$d['tobi_temp']['s']+1
                &&$d['buiten_temp']['s']>$d['alex_temp']['s']+1)
                &&$d['buiten_temp']['s']>22
                &&($d['kamer_temp']['s']>20
                ||$d['tobi_temp']['s']>20
                ||$d['alex_temp']['s']>20)
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
                    72,
                    false,
                    2,
                    false
                );
            } elseif (($d['buiten_temp']['s']<=$d['kamer_temp']['s']
                ||$d['buiten_temp']['s']<=$d['tobi_temp']['s']
                ||$d['buiten_temp']['s']<=$d['alex_temp']['s'])
                &&($d['kamer_temp']['s']>19
                ||$d['tobi_temp']['s']>19
                ||$d['alex_temp']['s']>19)
                &&($d['raamkamer']['s']=='Closed'
                ||$d['raamtobi']['s']=='Closed'
                ||$d['raamalex']['s']=='Closed')
            ) {
                alert(
                    'ramenboven',
                    'Ramen boven open doen, te warm binnen.
                    Buiten = '.round($d['buiten_temp']['s'], 1).',
                    kamer = '.$d['kamer_temp']['s'].',
                    Tobi = '.$d['tobi_temp']['s'].',
                    Alex = '.$d['alex_temp']['s'],
                    72,
                    false,
                    2,
                    false
                );
            }
        }
	}
}