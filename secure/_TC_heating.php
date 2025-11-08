<?php
$preheating=false;
$Setkamer=4;
$Setwaskamer=4;
$Setalex=4;
if ($d['weg']['s']<=2&&$d['heating']['s']>=1) {
	if ($d['kamer_set']['m']==0) {
		if (
				($d['raamkamer']['s']=='Closed'||$d['rkamerr']['s']==100)
			&&
				(past('raamkamer')>2700||$time>strtotime('19:00'))
			&&
				(
					($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<900))
				||
					(
						($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<900)||$d['raamalex']['s']=='Closed'||$d['ralex']['s']==100)
					&&
						($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<900)||$d['raamwaskamer']['s']=='Closed'||$d['rwaskamer']['s']==100)
					&&	$d['raamhall']['s']=='Closed'
					)
				)
		) {
			$Setkamer=12;
		}
	} else $Setkamer=$d['kamer_set']['s'];
	if ($d['alex_set']['m']==0) {
		if (
				($d['raamalex']['s']=='Closed'||$d['ralex']['s']==100)
			&&
				(past('raamalex')>2700|| $time>strtotime('19:00'))
			&&
				(
					($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<900))
				||
					(
						($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<900)||$d['raamkamer']['s']=='Closed'||$d['rkamerr']['s']==100)
					&&
						($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<900)||$d['raamwaskamer']['s']=='Closed'||$d['rwaskamer']['s']==100)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$Setalex=12;
		}
	} else $Setalex=$d['alex_set']['s'];
}
if ($d['kamer_set']['m']==1) $Setkamer=$d['kamer_set']['s'];
if ($d['alex_set']['m']==1) $Setalex=$d['alex_set']['s'];

if ($d['kamer_set']['s']!=$Setkamer) {
	setpoint('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
	$d['kamer_set']['s']=$Setkamer;
}
if ($d['alex_set']['s']!=$Setalex) {
	setpoint('alex_set', $Setalex, basename(__FILE__).':'.__LINE__);
	$alex_set=$Setalex;
	$d['alex_set']['s']=$Setalex;
}
$Setliving = 14;
if ($d['living_set']['m']==0&&$d['weg']['s']<=1) {
	$living    = $d['living_temp']['s'];
	$mode      = $d['heating']['s'];
	$weg       = $d['weg']['s'];
	$prevSet   = $d['living_start_temp']['m'] ?? 0;   // ← toegevoegd geheugen
	$leadDataLiving = json_decode($d['leadDataLiving']['s'] ?? '{}', true) ?: [];
	
	$avgMinPerDeg = !empty($leadDataLiving[$mode])
		? round(array_sum($leadDataLiving[$mode]) / count($leadDataLiving[$mode]), 1)
		: 60;
	
	$baseSet = [
		0 => 18.5,
		1 => 16,
		2 => 16,
		3 => 14
	];
	$comfortStart = [
		1 => '12:50',
		2 => '16:00',
		3 => '12:10',
		4 => '16:00',
		5 => '15:00',
		6 => '08:00',
		0 => '08:00'
	];
	if ($weekend==true) {
		$comfortAfternoon = strtotime($comfortStart[0]);
		$comfortEnd = strtotime('19:30');
	} else {
	 	if ($d['verlof']['s']>0) $comfortAfternoon = strtotime($comfortStart[0]);
		else $comfortAfternoon = strtotime($comfortStart[$dow]);
		$comfortEnd = strtotime('19:00');
	}
	
	$target = 21;
	$tempDelta   = max(0, $target - $living);
	$leadMinutes = round($avgMinPerDeg * $tempDelta);
	$t_start = $comfortAfternoon - ($leadMinutes * 60);
	if ($d['daikin']['s'] == 'Off' || ($d['daikin']['s'] == 'On' && past('daikin') < 70)) $t_start -= 300;
	$t_end = $comfortEnd;
	
	// --- basis nachtregeling ---
	if ($time >= strtotime('19:30') || $time < strtotime('04:00')) $Setliving = min($baseSet[$weg], 16);
	else $Setliving = $baseSet[$weg];
	
	// --- hysterese / geheugen toegevoegd ---
	if ($prevSet == 1) {
		// al in comfortfase of opwarming: houd target vast
		$Setliving = $target;
		if ($time > $t_end) storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
	}
	elseif ($time >= $t_start && $time < $comfortAfternoon && $weg <= 1) {
		// startmoment bereikt: begin preheat
		$preheating = true;
		$Setliving = max($Setliving, $target);
		storemode('living_start_temp', 1, basename(__FILE__) . ':' . __LINE__);
		$msg="🔥 _TC_living: Start leadMinutes={$leadMinutes}	| avgMinPerDeg={$avgMinPerDeg}";
		lg($msg);
		telegram($msg);
	}
	elseif ($time >= $comfortAfternoon && $time < $t_end && $weg == 0) {
		// comfortfase actief
		$Setliving = max($Setliving, $target);
	}
	else {
		// alles buiten comfort: basisregeling
		$Setliving = $Setliving;
		if ($prevSet != 0) storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
	}
	
	// --- leercurve ---
	if ($prevSet == 1/*$time >= $comfortAfternoon && $time < $t_end && $weg == 0*/ && $living >= $target && past('leadDataLiving') > 43200) {
		$startTemp = $d['living_start_temp']['s'];
		if ($startTemp && $living > $startTemp) {
			$tempRise    = $living - $startTemp;
			$minutesUsed = round(past('living_start_temp') / 60, 1);
			$minPerDeg   = ceil($minutesUsed / $tempRise);
			$minPerDeg = max($avgMinPerDeg - 10, min($avgMinPerDeg + 20, $minPerDeg));
			if (!isset($leadDataLiving[$mode])) $leadDataLiving[$mode] = [];
			$leadDataLiving[$mode][] = $minPerDeg;
			$leadDataLiving[$mode] = array_slice($leadDataLiving[$mode], -14);
			$avgMinPerDeg = round(array_sum($leadDataLiving[$mode]) / count($leadDataLiving[$mode]), 1);
			store('leadDataLiving', json_encode($leadDataLiving), basename(__FILE__) . ':' . __LINE__);
//			storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
			$msg="🔥 _TC_living: Einde ΔT=" . round($tempRise,1) . "° in {$minutesUsed} min → {$minPerDeg} min/°C (gemiddeld nu {$avgMinPerDeg} min/°C)";
			lg($msg);
			telegram($msg);
		}
	}
	
	// --- starttemp enkel bij echte start ---
	if (abs($time - $t_start) < 10) {
		store('living_start_temp', $living, basename(__FILE__) . ':' . __LINE__, 1);
	}
}
if ($d['living_set']['s']!=$Setliving) {
	setpoint('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
	$living_set=$Setliving;
	$d['living_set']['s']=$Setliving;
}
require('_Rolluiken_Heating.php');
$bigdif=100;
$difgas=100;
$user = 'heating';
if ($d['heating']['s']==1) require ('_TC_heating_airco.php');
elseif ($d['heating']['s']==2) require ('_TC_heating_gasairco.php');
elseif ($d['heating']['s']==3) require ('_TC_heating_gas.php');
require('_TC_badkamer.php');

if ($d['brander']['s']=='On'&&past('brander')>1195) sw('brander', 'Off');