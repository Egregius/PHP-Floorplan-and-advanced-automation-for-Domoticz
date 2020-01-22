<?php
/**
 * Pass2PHP Passive cooling
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
 
foreach	(array('heater1', 'heater2', 'brander', 'badkamervuur1', 'badkamervuur2', 'zoldervuur') as $i) {
	if ($d[$i]['s']!='Off') sw($i, 'Off', basename(__FILE__).':'.__LINE__);
}

$boven=array('Rtobi','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if ($d['minmaxtemp']['s']>20||$d['minmaxtemp']['m']>22) $warm=true; else $warm=false;
if ($d['minmaxtemp']['s']<5&&$d['minmaxtemp']['m']<5) $koud=true; else $koud=false;
if ($d['auto']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];
$heating=$d['heating']['s'];
if ($d['auto']['s']=='On') {
	if (TIME>=strtotime('6:00')&&TIME<strtotime('10:15')) {
		$dow=date("w");
		if($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:00')) {
				sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerL']['m']>0) {
					storemode('RKamerL', 0, basename(__FILE__).':'.__LINE__);
				}
			}
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:15')) {
				if ($d['Weg']['s']<=1&&$d['bose103']['s']=='Off') {
					bosezone(103, 15);
				}
				sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Weg']['s']<=1&&$d['kamer']['s']==0) {
					sl('kamer', 3);
				}
				if ($d['RkamerR']['m']>0) {
					storemode('RKamerR', 0, basename(__FILE__).':'.__LINE__);
				}
			}
		} else {
			if ($d['RkamerL']['s']>0) {
				sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerL']['m']>0) {
					storemode('RKamerL', 0, basename(__FILE__).':'.__LINE__);
				}
			}
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('6:15')) {
				if ($d['Weg']['s']<=1&&$d['bose103']['s']=='Off') {
					bosezone(103, 15);
				}
				sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Weg']['s']<=1&&$d['kamer']['s']==0) {
					sl('kamer', 3);
				}
				if ($d['RkamerR']['m']>0) {
					storemode('RKamerR', 0, basename(__FILE__).':'.__LINE__);
				}
			}
		}
		if ($dag==true) {
			if ($d['Rtobi']['m']==0&&$d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
			if ($d['Ralex']['m']==0&&$d['Ralex']['s']>0&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$zon==0) {
			foreach ($beneden as $i) {
				if ($d[$i]['m']==0&&$d[$i]['s']>29) sl($i, 29, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['m']==0&&$d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0) {
			foreach ($beneden as $i) {
				if ($d[$i]['m']==0&&$d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['m']==0&&$d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	} 

	elseif (TIME>=strtotime('10:15')&&TIME<strtotime('15:00')) {
		if ($warm) {
			if($zon>2000) {
				if ($d['Rtobi']['m']==0&&$d['raamtobi']['s']=='Closed'&&$d[$i]['s']!=81) sl('Rtobi', 81, basename(__FILE__).':'.__LINE__);
				if ($d['Ralex']['m']==0&&$d['raamalex']['s']=='Closed'&&$d[$i]['s']!=81) sl('Ralex', 81, basename(__FILE__).':'.__LINE__);
			}
		} else {
			if ($dag==true) {
				if ($d['RkamerL']['m']==0&&$d['RkamerL']['s']>0&&($d['deurkamer']['s']=='Open'||$d['kamer']['s']>0)) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerR']['m']==0&&$d['RkamerR']['s']>0&&($d['deurkamer']['s']=='Open'||$d['kamer']['s']>0)) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Rtobi']['m']==0&&$d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Ralex']['m']==0&&$d['Ralex']['s']>0&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			}
		}
	} 

	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('22:00')) {
		if ($warm) {
			if($zon>2000) {
				if ($d['Rtobi']['m']==0&&$d['raamtobi']['s']=='Closed'&&$d['Ralex']['s']!=81) sl('Rtobi', 81, basename(__FILE__).':'.__LINE__);
				if ($d['Ralex']['m']==0&&$d['raamalex']['s']=='Closed'&&$d['Ralex']['s']!=81) sl('Ralex', 81, basename(__FILE__).':'.__LINE__);
				if ($d['Rbureel']['m']==0&&$d['Rbureel']['s']<45) sl('Rbureel', 45, basename(__FILE__).':'.__LINE__);
			}
		} 
	} 

	elseif (TIME>=strtotime('22:00')||TIME<strtotime('6:00')) {
		if ($d['Weg']['s']>0) {
			if ($heating<0) {
				foreach ($benedenall as $i) {
					if ($d[$i]['m']==0&&$d[$i]['s']<84) sl($i, 84, basename(__FILE__).':'.__LINE__);
				}
				foreach ($boven as $i) {
					if ($d[$i]['m']==0&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				}
			} else {
				foreach ($benedenall as $i) {
					if ($d[$i]['m']==0&&$d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
				foreach ($boven as $i) {
					if ($d[$i]['m']==0&&$d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}