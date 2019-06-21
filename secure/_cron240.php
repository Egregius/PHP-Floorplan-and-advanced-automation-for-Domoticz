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
	}
}