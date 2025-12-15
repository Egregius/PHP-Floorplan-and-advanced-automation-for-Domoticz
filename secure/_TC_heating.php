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
if (($d['living_set']['m']==0&&$d['weg']['s']<=1)||($d['living_set']['m']==2&&$d['weg']['s']<=1)) {
	$living    = $d['living_temp']['s'];
	$mode      = $d['heating']['s'];
	$weg       = $d['weg']['s'];
	if ($d['living_set']['m'] == 2) $weg = 0;
	$prevSet   = $d['living_start_temp']['m'] ?? 0;
	$buitenTempStart = (floor($d['buiten_temp']['s'] / 2)) * 2;
	$leadDataLiving = json_decode($d['leadDataLiving']['s'] ?? '{}', true) ?: [];
	$avgMinPerDeg = null;
	if (!empty($leadDataLiving[$mode])) {
		if (!empty($leadDataLiving[$mode][$buitenTempStart])) {
			$data = $leadDataLiving[$mode][$buitenTempStart];
		} else {
			$temps = array_keys($leadDataLiving[$mode]);
			usort($temps, fn($a, $b) =>
				abs($a - $buitenTempStart) <=> abs($b - $buitenTempStart)
			);
			$closestTemp = $temps[0];
			$data = $leadDataLiving[$mode][$closestTemp];
		}
		if (!empty($data)) {
			$avgMinPerDeg = round(array_sum($data) / count($data), 1);
		}
	}
	$avgMinPerDeg ??= 20;
	$baseSet = [
		0 => 19,
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
	
	if ($time >= strtotime('19:30') || $time < strtotime('04:00')) $Setliving = min($baseSet[$weg], 16);
	else $Setliving = $baseSet[$weg];
	if ($prevSet == 1) {
		$Setliving = $target;
		if ($time > $t_end) {
			storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
		}
		if ($d['living_set']['m']==2) storemode('living_set', 0, basename(__FILE__) . ':' . __LINE__);
	}
	elseif ($time >= $t_start && $time < $comfortAfternoon && $weg <= 1) {
		$preheating = true;
		$Setliving = max($Setliving, $target);
		storemode('living_start_temp', 1, basename(__FILE__) . ':' . __LINE__);
		$msg="🔥 _TC_living: Start leadMinutes={$leadMinutes}	| avgMinPerDeg={$avgMinPerDeg}";
		lg($msg);
	}
	elseif ($time >= $comfortAfternoon && $time < $t_end && $weg == 0) {
		$Setliving = max($Setliving, $target);
	}
	else {
		$Setliving = $Setliving;
		if ($prevSet != 0) storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
	}
	
	if ($prevSet == 1/*$time >= $comfortAfternoon && $time < $t_end && $weg == 0*/ && $living >= $target && past('leadDataLiving') > 43200) {
		$startTemp = $d['living_start_temp']['s'];
		$buitenTempStart = $d['badkamer_start_temp']['icon'];
		if ($startTemp && $living > $startTemp) {
			$tempRise    = $living - $startTemp;
			$minutesUsed = round(past('living_start_temp') / 60, 1);
			$minPerDeg   = ceil($minutesUsed / $tempRise);
			$minPerDeg = round(max($avgMinPerDeg - 10, min($avgMinPerDeg + 20, $minPerDeg)),1);
			if (!isset($leadDataLiving[$mode])) $leadDataLiving[$mode] = [];
			$leadDataLiving[$mode][$buitenTempStart][] = round($minPerDeg,1);
			$leadDataLiving[$mode][$buitenTempStart] = array_slice($leadDataLiving[$mode][$buitenTempStart], -7);
			$avgMinPerDeg = round(array_sum($leadDataLiving[$mode]) / count($leadDataLiving[$mode]), 1);
			store('leadDataLiving', json_encode($leadDataLiving), basename(__FILE__) . ':' . __LINE__);
//			storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
			$msg="🔥 _TC_living: Einde ΔT=" . round($tempRise,1) . "° in {$minutesUsed} min → {$minPerDeg} min/°C (gemiddeld nu {$avgMinPerDeg} min/°C)";
			lg($msg);
		}
	}
	
	// --- starttemp enkel bij echte start ---
	if (abs($time - $t_start) < 10) {
		store('living_start_temp', $living, basename(__FILE__) . ':' . __LINE__, 1);
	}
}
if ($d['living_set']['m']==1) $Setliving=$d['living_set']['s'];
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

if ($d['heating']['s']<=1&&$d['brander']['s']=='On'&&past('brander')>1195) sw('brander', 'Off');