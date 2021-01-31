<?php
/**
 * Pass2PHP Control rollers
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['auto']['s']=='On') {
	if (TIME>=strtotime('5:30')&&TIME<strtotime('10:00')) {
		$dow=date("w");
		if($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rtobi']['s']>0&&TIME>=strtotime('9:00')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&TIME>=strtotime('9:00')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		} else {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('6:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('6:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rtobi']['s']>0&&TIME>=strtotime('8:00')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['s']>0&&TIME>=strtotime('9:00')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ((TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m'])||$d['pirhall']['s']=='On') {
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)&&$d['deuralex']['s']=='Open'&&TIME>=strtotime('7:30')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Ralex']['s']>0&&$d['deuralex']['s']=='Open'&&TIME>=strtotime('7:30')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']&&$d['zon']['s']==0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach (array('Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']&&$d['zon']['s']>0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach (array('Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']>0&&past($i)>7200) sl($i, 0, basename(__FILE__).':'.__LINE__);
			if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('17:00')) {
		if ($d['buiten_temp']['s']<10) {
			foreach (array('tobi', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<16&&past('raam'.$i)>14400&&$d['R'.$i]['s']<100) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['R'.$i]['s']<100) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif (TIME>=strtotime('17:00')&&TIME<strtotime('22:00')) {
		if ($d['zon']['s']==0) {
			foreach (array('Rtobi','Ralex','RkamerL','RkamerR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>0) {
				foreach (array('Rliving','Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
			} else {
				if ($dag==false) {
					foreach (array('Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		} elseif ($d['buiten_temp']['s']<15) {
			foreach (array('tobi', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<16&&past('raam'.$i)>14400&&$d['R'.$i]['s']<100) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['R'.$i]['s']<100) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif (TIME>=strtotime('22:00')||TIME<strtotime('6:00')) {
		if ($d['Weg']['s']>0) {
			foreach (array('Rliving','Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<100&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('Rtobi','Ralex','RkamerL','RkamerR') as $i) if ($d[$i]['s']<100&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
}
