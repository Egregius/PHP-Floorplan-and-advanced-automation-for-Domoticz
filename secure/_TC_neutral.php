<?php
/**
 * Pass2PHP Temperature Control Neutral
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/

if ($d['brander']['s']!='Off') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['daikin']['s']=='On') {
	foreach (array('living', 'kamer', 'alex') as $k) {
		$daikin=json_decode($d['daikin'.$k]['s']);
		if ($daikin->pow!=0&&$daikin->mode!=2) {
			daikinset($k, 0, 3, 20, basename(__FILE__).':'.__LINE__);
			storemode('daikin'.$k, 0);
			storeicon($k.'_set', 'Off');
		}
	}
}

$boven=array('Rspeelkamer','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if ($d['minmaxtemp']['s']>20||$d['minmaxtemp']['m']>22) $warm=true; else $warm=false;
if ($d['minmaxtemp']['s']<5&&$d['minmaxtemp']['m']<5) $koud=true; else $koud=false;
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;

$zon=$d['zon']['s'];
$heating=$d['heating']['s'];
if ($d['auto']['s']=='On') {
	if (TIME>=strtotime('5:30')&&TIME<strtotime('10:00')) {
		$dow=date("w");
		if($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>1&&TIME>=strtotime('7:30')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>1&&TIME>=strtotime('7:30')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dow==2||$dow==5) {
			if ($d['RkamerL']['s']>1&&TIME>=strtotime('6:45')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>1&&TIME>=strtotime('6:45')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		} else {
			if ($d['RkamerL']['s']>1&&TIME>=strtotime('7:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>1&&TIME>=strtotime('7:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true||$d['pirhall']['s']=='On') {
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Rspeelkamer']['s']>0&&TIME>=strtotime('7:30')&&($d['deurspeelkamer']['s']=='Open'||$d['speelkamer']['s']>0)) sl('Rspeelkamer', 0, basename(__FILE__).':'.__LINE__);
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Ralex']['s']>0&&TIME>=strtotime('7:30')&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)&&past('raamalex')>175) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$zon==0&&$d['Weg']['s']!=1&&$d['lgtv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>1) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0&&$d['lgtv']['s']=='Off'&&($d['Ralex']['s']==0||TIME>=strtotime('7:30'))) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>1) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0&&$d['lgtv']['s']=='Off'&&($d['Ralex']['s']<=1||TIME>=strtotime('7:30'))) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	}
	elseif (TIME>=strtotime('17:00')&&TIME<strtotime('22:00')) {
		if ($heating>0) {
			if ($zon==0) {
				foreach ($boven as $i) {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
				if ($dag==false) {
					foreach ($beneden as $i) {
						if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
					}
				}
			} elseif ($d['buiten_temp']['s']<16) {
				$items=array('speelkamer', 'alex');
				foreach ($items as $i) {
					if ($d['raam'.$i]['s']=='Open'&&$d[$i.'_temp']['s']<17&&past('R'.$i)>14400&&past('raam'.$i)>14400&&$d['R'.$i]['s']<100) {
						sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
					}
				}
				$items=array('kamerL', 'kamerR');
				foreach ($items as $i) {
					if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<17&&past('R'.$i)>14400&&past('raamkamer')>14400&&$d['R'.$i]['s']<100) {
						sl('R'.$i, 100, basename(__FILE__).':'.__LINE__);
					}
				}
			}
		} elseif ($heating>0) {
			if ($zon==0&&$dag==false) {
				foreach ($beneden as $i) {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
			} elseif ($zon==0) {
				foreach ($boven as $i) {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
				foreach ($beneden as $i) {
					if ($d[$i]['s']<30) sl($i, 30, basename(__FILE__).':'.__LINE__);
				}
			}
		} elseif ($heating==0&&$dag==false) {
			if ($zon==0) {
				foreach ($boven as $i) {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
				foreach ($beneden as $i) {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	elseif (TIME>=strtotime('22:00')||TIME<strtotime('3:00')) {
		if ($d['Weg']['s']>0) {
			if ($heating<0) {
				foreach ($benedenall as $i) {
					if ($d[$i]['s']<84) sl($i, 84, basename(__FILE__).':'.__LINE__);
				}
				foreach ($boven as $i) {
					if ($d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				}
			} else {
				foreach ($benedenall as $i) {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
				foreach ($boven as $i) {
					if ($i=='RkamerR') {
						if ($d['Weg']['s']>=2&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
					} else {
						if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
					}
				}
			}
		}
	}
}
require('_TC_badkamer.php');
