<?php
$user='TC_badkamer';
$m='';$m2='';
$preheatbath=false;
if ($d['badkamer_set']['m']==0) {$set=13;$m2.=__LINE__.' ';}
else {$set=$d['badkamer_set']['s'];$m2.=__LINE__.' ';}
$pastdeurbadkamer=past('deurbadkamer');

//if (in_array($dow, array(0,2,4,6,7))) $dday=true; else $dday=false;
$dday=true;
if ($d['weg']['s']>=2) $set=10;
elseif ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer>57&&($d['raamkamer']['s']=='Open'||$d['raamwaskamer']['s']=='Open'||$d['raamalex']['s']=='Open')) {
	$set=5;$m2.=__LINE__.' ';
} elseif ($d['badkamer_set']['m']==0&&($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer<57))&&$d['badkamer_set']['m']>0) {
	if (past('badkamer_set')>=14400&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['weg']['s']<2) {
		if ($d['badkamer_set']['s']>13) {
			$set=13;$m2.=__LINE__.' ';
			if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('badkamer_set')>=14400&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=13) || ($d['weg']['s']>=2&&$d['badkamer_set']['s']!=13)) {
		setpoint('badkamer_set', 13, basename(__FILE__).':'.__LINE__);
		$set=13;$m2.=__LINE__.' ';
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
} elseif (($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer<57))&&$d['badkamer_set']['m']==0&&$d['heating']['s']>=0) {
	if ($d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<20&&$d['weg']['s']<2) {
		if ($d['badkamer_set']['s']!=13) {$set=13;$m2.=__LINE__.' ';}
	}
//	$t = strtotime('9:20');
	$set       = 13;
	$target    = 21;
	$badkamer  = $d['badkamer_temp']['s'];
	$prevSet   = $d['badkamer_start_temp']['m'] ?? 0;
	$leadDataBath = json_decode($d['leadDataBath']['s'] ?? '{}', true) ?: [];
	
	$avgMinPerDeg = !empty($leadDataBath)
		? round(array_sum($leadDataBath) / count($leadDataBath), 1)
		: 60;
	
	$tempDelta   = max(0, $target - $badkamer);
	$leadMinutes = round($avgMinPerDeg * $tempDelta);
	$t_start     = $t - ($leadMinutes * 60);
	$t_end       = $t + 1800;
	
	// --- logica met hysterese / geheugen ---
	if ($prevSet == 1) {
//		lg(basename(__FILE__) . ':' . __LINE__);
		// al bezig met opwarmen → behoud target tot einde
		$set = $target;
		if ($time > $t_end) storemode('badkamer_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
		$preheatbath=true;
	}
	elseif ($time >= $t_start && $time < $t) {
//		lg(basename(__FILE__) . ':' . __LINE__);
		// startmoment bereikt → begin opwarmen
		$set = $target;
		$msg="_TC_bath: Start leadMinutes={$leadMinutes}	| avgMinPerDeg={$avgMinPerDeg}";
		lg($msg);
		telegram($msg);
		storemode('badkamer_start_temp', 1, basename(__FILE__) . ':' . __LINE__);
		$preheatbath=true;
	}
	elseif ($time >= $t && $time <= $t_end) {
//		lg(basename(__FILE__) . ':' . __LINE__);
		// comfortfase → target behouden
		$set = $target;
		$preheatbath=false;
	} 
	else {
//		lg(basename(__FILE__) . ':' . __LINE__);
		// buiten comfortperiode en niet in opwarming → lage stand
		$set = 13;
		if ($prevSet != 0) storemode('badkamer_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
		$preheatbath=false;
	}
	
	// --- update leercurve ---
//	if ($prevSet == 1) lg(__LINE__);
//	if ($time >= $t_start) lg(__LINE__);
//	if ($time <= $t_end) lg(__LINE__);
//	if ($badkamer >= $target) lg(__LINE__);
//	if (past('leadDataBath') > 432) lg(__LINE__);
//	if (past('8badkamer_8') > 1800) lg(__LINE__);
	if ($prevSet == 1 && /*$time >= $t_start && $time <= $t_end && */$badkamer >= $target && past('leadDataBath') > 43200) {
		lg(__LINE__);
		$startTemp = $d['badkamer_start_temp']['s'];
		if ($startTemp && $badkamer >= $startTemp) {
			lg(__LINE__);
			$tempRise    = $badkamer - $startTemp;
			if ($tempRise>0) {
				$minutesUsed = round(past('badkamer_start_temp') / 60, 1);
				$minPerDeg   = ceil($minutesUsed / $tempRise);
				$minPerDeg = max($avgMinPerDeg - 10, min($avgMinPerDeg + 20, $minPerDeg));
				$leadDataBath[] = $minPerDeg;
				$leadDataBath = array_slice($leadDataBath, -14);
				$avgMinPerDeg = ceil(array_sum($leadDataBath) / count($leadDataBath));
				store('leadDataBath', json_encode($leadDataBath), basename(__FILE__) . ':' . __LINE__);
				$msg="_TC_bath: Einde ΔT=" . round($tempRise,1) . "° in {$minutesUsed} min → {$minPerDeg} min/°C (gemiddeld nu {$avgMinPerDeg} min/°C)";
				lg($msg);
				telegram($msg);
			}
	//		if ($prevSet != 0) storemode('badkamer_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
		}
	}
	
	// --- starttemp enkel opslaan bij exact startmoment ---
	if (abs($time - $t_start) < 10) {
		store('badkamer_start_temp', $badkamer,basename(__FILE__) . ':' . __LINE__, 1);
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']<0) {
	if ($d['badkamer_set']['s']!=5) {
		setpoint('badkamer_set', 5, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=5;
	}
}
if (isset($set)&&$d['heating']['s']>=0) {
	if ($set!=$d['badkamer_set']['s']) setpoint('badkamer_set', $set, basename(__FILE__).':'.__LINE__.' '.$m2);
	$d['badkamer_set']['s']=$set;
}

if ($d['weg']['s']<2) {
	$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
	if ($difbadkamer<=-0.3||($preheatbath==true&&$difbadkamer<=-0.1)) {
		if ($d['badkamervuur1']['s']=='Off'&&$d['deurbadkamer']['s']=='Closed') sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['badkamervuur1']['s']=='On'&&$d['badkamervuur2']['s']=='Off'&&$d['deurbadkamer']['s']=='Closed') sw('badkamervuur2', 'On', basename(__FILE__).':'.__LINE__);
	}
	elseif ($difbadkamer<=0||($preheatbath==true&&$difbadkamer<=+0.1)) {
		if ($d['badkamervuur1']['s']=='Off'&&$d['deurbadkamer']['s']=='Closed') sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	}
	else {
		if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['badkamervuur2']['s']=='Off'&&$d['badkamervuur1']['s']=='On') sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	}
}
else {
	if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['badkamervuur2']['s']=='Off'&&$d['badkamervuur1']['s']=='On') sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
}


//if ($d['wasdroger']['s']=='On') {
//	if (($d['waskamer_temp']['m']<65&&past('wasdroger')>3595)||$d['raamwaskamer']['s']=='Open') sw('wasdroger', 'Off', basename(__FILE__).':'.__LINE__);
//} else {
//	if ($d['waskamer_temp']['m']>80&&$d['raamwaskamer']['s']=='Closed'&&$d['deurwaskamer']['s']=='Closed'&&past('wasdroger')>595) sw('wasdroger', 'On', basename(__FILE__).':'.__LINE__);
//}
