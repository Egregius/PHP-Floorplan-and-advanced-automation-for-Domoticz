<?php
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
} elseif ($d['weg']['s']>=3) {
	$Setkamer=5;
	$Setwaskamer=5;
	$Setalex=5;
} elseif ($d['heating']['s']>=1) {
	$Setkamer=10;
	$Setwaskamer=10;
	$Setalex=10;
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
	$buiten    = $d['buiten_temp']['s'];
	$living    = $d['living_temp']['s'];
	$mode      = $d['heating']['s'];
	$status    = $d['weg']['s'];
	if ($buiten < 20 && $d['minmaxtemp']['m'] < 22 && $mode >= 1) {
		// --- 1) Basis setpoints per status
		switch ($status) {
			case 0: $baseSet = 18; break; // thuis en wakker
			case 1: $baseSet = 16; break; // slapen
			case 2: $baseSet = 16; break; // weg
			case 3: $baseSet = 14; break; // op reis
			default: $baseSet = 14; break;
		}
		// --- 2) Comforturen per dag
		switch ($dow) {
			case 1: $comfortStart = strtotime('12:50'); $comfortEnd = strtotime('19:00'); break;
			case 2: $comfortStart = strtotime('16:00'); $comfortEnd = strtotime('19:00'); break;
			case 3: $comfortStart = strtotime('12:10'); $comfortEnd = strtotime('19:00'); break;
			case 4: $comfortStart = strtotime('16:00'); $comfortEnd = strtotime('19:00'); break;
			case 5: $comfortStart = strtotime('15:00'); $comfortEnd = strtotime('19:30'); break;
			case 6: $comfortStart = strtotime('8:00');  $comfortEnd = strtotime('19:30'); break;
			case 0: $comfortStart = strtotime('8:00');  $comfortEnd = strtotime('19:00'); break;
		}
		$nacht = ($time >= strtotime('00:00') && $time < strtotime('04:00'));
		$comfort = ($time >= $comfortStart && $time < $comfortEnd);
		// --- 3) Nachtstand
		if ($nacht) $Setliving = min($baseSet, 16);
		else $Setliving = $baseSet;
		// --- 4) Comforturen voor wakker thuis
		if ($status == 0 && $comfort) $Setliving = 20;
		// --- 5) Voorspellende opwarming naar volgende doel
		$nextTarget = null;
		if ($status == 1) { // slapen
			$nextTarget = 18; // morgendoel
		} elseif ($status == 0 && !$comfort) { // thuis wakker, comfortperiode komt eraan
			$nextTarget = 20; // comfortdoel
		}
		if ($nextTarget !== null && $time < $comfortStart) {
			// Lead per verwarmingsmodus
			$lead_modes = [
				1 => 100, // Airco
				2 => 100, // Gas-Airco
				3 => 100 // Gas
			];
			$lead_base = $lead_modes[$mode] ?? 50;
			// Dynamische lead afhankelijk van temp verschil en buiten
			$tempDiff = max(0, $nextTarget - $living);
			$lead = $lead_base + ($tempDiff * 3) - ($buiten * 0.5);
			$lead = max(10, min(60, $lead)); // minuten limiet
			$t_start = $comfortStart - ($lead * 60);
			if ($time >= $t_start) {
				$progress = ($time - $t_start) / ($comfortStart - $t_start);
				$curve = ($buiten < 5) ? 0.7 : 0.85; // snellere opwarming bij kouder weer
				$preheat = $living + ($nextTarget - $living) * pow($progress, $curve);
				if ($preheat > $Setliving) $Setliving = round($preheat, 1);
			}
		}
		$Setliving = round($Setliving, 1);
	}
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