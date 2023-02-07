<?php
/**
 * Pass2PHP Temperature Control Passive cooling
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
foreach	(array(/*'zoldervuur1', 'zoldervuur2', */'brander', 'badkamervuur1', 'badkamervuur2') as $i) {
	if ($d[$i]['s']!='Off') sw($i, 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['daikin']['s']=='On'&&$d['daikin']['m']==1) {
	foreach (array('living', 'kamer', 'alex') as $k) {
		$daikin=json_decode($d['daikin'.$k]['s']);
		if (isset($daikin->pow)&&$daikin->pow!=0&&$daikin->mode!=2) {
			daikinset($k, 0, 3, 20, basename(__FILE__).':'.__LINE__);
			storemode('daikin'.$k, 0);
			storeicon($k.'_set', 'Off');
		}
	}
}

$boven=array('Rspeelkamer','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;
$zon=$d['zon']['s'];
$heating=$d['heating']['s'];
if ($d['auto']['s']=='On') {
	if (TIME>=strtotime('6:00')&&TIME<strtotime('10:00')) {
		$dow=date("w");
		if ($dow==0||$dow==6) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:30')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:30')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dow==2||$dow==5) {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('6:45')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('6:45')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		} else {
			if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
			if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true||$d['pirhall']['s']=='On') {
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Rspeelkamer']['s']>1&&($d['deurspeelkamer']['s']=='Open'||$d['speelkamer']['s']>0)) sl('Rspeelkamer', 0, basename(__FILE__).':'.__LINE__);
			if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Ralex']['s']>1&&($d['deuralex']['s']=='Open'||$d['alex']['s']>1)&&past('raamalex')>175) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
		if ($dag==true&&$zon==0&&$d['Weg']['s']!=1&&$d['lgtv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>1&&past($i)>14400) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0&&$d['lgtv']['s']=='Off'&&($d['Ralex']['s']<=1||TIME>=strtotime('7:30'))) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
			foreach ($beneden as $i) {
				if ($d[$i]['s']>1&&past($i)>14400) sl($i, 0, basename(__FILE__).':'.__LINE__);
			}
			if ($d['Rliving']['s']>0&&$d['lgtv']['s']=='Off'&&($d['Ralex']['s']<=1||TIME>=strtotime('7:30'))) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('11:00')&&TIME<strtotime('15:00')) {
		if($zon>2000) {
			if ($d['raamspeelkamer']['s']=='Closed'&&$d['Rspeelkamer']['s']<82) sl('Rspeelkamer', 82, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<82) sl('Ralex', 82, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('15:00')&&TIME<strtotime('22:00')) {
		if($zon>2000) {
			if ($d['raamspeelkamer']['s']=='Closed'&&$d['Ralex']['s']<82) sl('Rspeelkamer', 82, basename(__FILE__).':'.__LINE__);
			if ($d['raamalex']['s']=='Closed'&&$d['Ralex']['s']<82) sl('Ralex', 82, basename(__FILE__).':'.__LINE__);
			if ($d['Rbureel']['s']<40) sl('Rbureel', 40, basename(__FILE__).':'.__LINE__);
		}
	}

	elseif (TIME>=strtotime('22:00')||TIME<strtotime('3:00')) {
		if ($d['Weg']['s']>0) {
			foreach ($benedenall as $i) {
				if ($d[$i]['s']<88) sl($i, 88, basename(__FILE__).':'.__LINE__);
			}
			foreach ($boven as $i) {
				if ($i=='Rspeelkamer') {
					if ($d['deurspeelkamer']['s']=='Closed'&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerL') {
					if ($d[$i]['s']<100) sl($i, 100, basename(__FILE__).':'.__LINE__);
				} elseif ($i=='RkamerR') {
					if ($d['Weg']['s']>=2&&$d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				} else {
					if ($d[$i]['s']<82) sl($i, 82, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}
require('_TC_badkamer.php');
