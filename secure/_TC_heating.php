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
if ($d['living_set']['m']==0) {
	$Setliving = 14;
	$living    = $d['living_temp']['s'];
	$mode      = $d['heating']['s'];
	$weg       = $d['weg']['s'];
	$prevSet   = $d['living_set']['s'] ?? 14;   // ← toegevoegd geheugen
	$leadDataLiving = json_decode($d['leadDataLiving']['s'] ?? '{}', true) ?: [];
	
	$avgMinPerDeg = !empty($leadDataLiving[$mode])
		? round(array_sum($leadDataLiving[$mode]) / count($leadDataLiving[$mode]), 1)
		: 60;
	
	$baseSet = [
		0 => 18,
		1 => 16,
		2 => 16,
		3 => 14
	];
	
	// --- comforttijden per dag ---
	switch ($dow) {
		case 1: $comfortAfternoon = strtotime('12:50'); $comfortEnd = strtotime('19:00'); break;
		case 2: $comfortAfternoon = strtotime('16:00'); $comfortEnd = strtotime('19:00'); break;
		case 3: $comfortAfternoon = strtotime('12:10'); $comfortEnd = strtotime('19:00'); break;
		case 4: $comfortAfternoon = strtotime('16:00'); $comfortEnd = strtotime('19:00'); break;
		case 5: $comfortAfternoon = strtotime('15:00'); $comfortEnd = strtotime('19:30'); break;
		case 6: $comfortAfternoon = strtotime('08:00'); $comfortEnd = strtotime('19:30'); break;
		case 0: $comfortAfternoon = strtotime('08:00'); $comfortEnd = strtotime('19:00'); break;
	}
	
	$target = 20.0;
	$tempDelta   = max(0, $target - $living);
	$leadMinutes = round($avgMinPerDeg * $tempDelta);
	$t_start = $comfortAfternoon - ($leadMinutes * 60);
	if ($d['daikin']['s'] == 'Off' || ($d['daikin']['s'] == 'On' && past('daikin') < 70)) $t_start -= 300;
	$t_end = $comfortEnd;
	
	// --- basis nachtregeling ---
	if ($time >= strtotime('19:30') || $time < strtotime('04:00')) $Setliving = min($baseSet[$weg], 16);
	else $Setliving = $baseSet[$weg];
	
	// --- hysterese / geheugen toegevoegd ---
	if ($prevSet >= $target - 0.2) {
		// al in comfortfase of opwarming: houd target vast
		$Setliving = $target;
	}
	elseif ($time >= $t_start && $time < $comfortAfternoon && $weg <= 1) {
		// startmoment bereikt: begin preheat
		$preheating = true;
		$Setliving = max($Setliving, $target);
	}
	elseif ($time >= $comfortAfternoon && $time < $t_end && $weg == 0) {
		// comfortfase actief
		$Setliving = max($Setliving, $target);
	}
	else {
		// alles buiten comfort: basisregeling
		$Setliving = $Setliving;
	}
	
	// --- leercurve ---
	if ($time >= $comfortAfternoon && $time < $t_end && $weg == 0) {
		if ($living >= $target && past('leadDataLiving') > 43200 && past('8living_8') > 14400) {
			$startTemp = $d['living_start_temp']['s'];
			if ($startTemp && $living > $startTemp) {
				$tempRise    = $living - $startTemp;
				$minutesUsed = round(past('living_start_temp') / 60, 1);
				$minPerDeg   = round($minutesUsed / $tempRise, 1);
				$minPerDeg   = max(10, min(60, $minPerDeg));
				if (!isset($leadDataLiving[$mode])) $leadDataLiving[$mode] = [];
				$leadDataLiving[$mode][] = $minPerDeg;
				$leadDataLiving[$mode] = array_slice($leadDataLiving[$mode], -14);
				store('leadDataLiving', json_encode($leadDataLiving), basename(__FILE__) . ':' . __LINE__, 1);
				lg("_TC_living: ΔT=" . round($tempRise,1) . "° in {$minutesUsed} min → {$minPerDeg} min/°C (gemiddeld nu {$avgMinPerDeg} min/°C)");
			}
		}
	}
	
	// --- starttemp enkel bij echte start ---
	if (abs($time - $t_start) < 10) {
		store('living_start_temp', $living, basename(__FILE__) . ':' . __LINE__, 1);
	}

	// --- 6) Weg en op reis fallback
//	if ($d['weg']['s']==2 && $d['heating']['s']>=1) {
//		$Setliving=16;
//	} elseif ($d['weg']['s']==3 && $d['heating']['s']>=1) {
//		$Setliving=14;
//	}


	if ($d['living_set']['s']!=$Setliving) {
		setpoint('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
		$living_set=$Setliving;
		$d['living_set']['s']=$Setliving;
	}
}
require('_Rolluiken_Heating.php');
$bigdif=100;
$difgas=100;
if ($d['heating']['s']==1) require ('_TC_heating_airco.php');
elseif ($d['heating']['s']==2) require ('_TC_heating_gasairco.php');
elseif ($d['heating']['s']==3) require ('_TC_heating_gas.php');
require('_TC_badkamer.php');

if ($d['brander']['s']=='On'&&past('brander')>1195) sw('brander', 'Off');