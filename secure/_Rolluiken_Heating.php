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
$boven=array('Rspeelkamer','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];
$heating=$d['heating']['s'];

if ($d['auto']['s']=='On') {
	if (TIME>=strtotime('6:30')&&TIME<strtotime('10:00')) {
		$dow=date("w");
		if($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:30')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:30')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		} else {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('6:30')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('6:30')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true||$d['pirhall']['s']=='On') {
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Rspeelkamer']['s']>0&&TIME>=strtotime('7:30')&&($d['deurspeelkamer']['s']=='Open'||$d['speelkamer']['s']>0)) sl('Rspeelkamer', 0, basename(__FILE__).':'.__LINE__);
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Ralex']['s']>0&&TIME>=strtotime('7:30')&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$zon==0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0&&$d['tv']['s']=='Off'&&$d['Ralex']['s']==0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0&&$d['Ralex']['s']==0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('17:00')) {
		if ($d['buiten_temp']['s']<10) {
			foreach (array('speelkamer', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<16&&past('raam'.$i)>14400&&$d['R'.$i]['s']<=70) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['R'.$i]['s']<=70) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif (TIME>=strtotime('17:00')&&TIME<strtotime('22:00')) {
		if ($d['zon']['s']==0) {
			foreach (array('Rspeelkamer','Ralex','RkamerL','RkamerR') as $i) if ($d[$i]['s']<=70) sl($i, 100, basename(__FILE__).':'.__LINE__);
			if ($d['Weg']['s']>0) {
				foreach (array('Rliving','Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<=70) sl($i, 100, basename(__FILE__).':'.__LINE__);
			} else {
				if (TIME<$d['civil_twilight']['s']||TIME>$d['civil_twilight']['m']) {
					foreach (array('Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<=70) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		} elseif ($d['buiten_temp']['s']<15) {
			foreach (array('speelkamer', 'alex') as $i) if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<16&&past('raam'.$i)>14400&&$d['R'.$i]['s']<=70) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('kamerL', 'kamerR') as $i) if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['R'.$i]['s']<=70) sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif (TIME>=strtotime('22:00')||TIME<strtotime('6:30')) {
		if ($d['Weg']['s']>0) {
			foreach (array('Rliving','Rbureel','RkeukenL','RkeukenR') as $i) if ($d[$i]['s']<=70&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
			foreach (array('Rspeelkamer','Ralex','RkamerL','RkamerR') as $i) if ($d[$i]['s']<=70&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
		}
	}
}
