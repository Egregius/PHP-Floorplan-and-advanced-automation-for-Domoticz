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
	$buiten    = $d['buiten_temp']['s'];
	$living    = $d['living_temp']['s'];
	$mode      = $d['heating']['s']; // bv. 1 = normal, 2 = eco, ...
	$status    = $d['weg']['s'];
	$leadData  = json_decode($d['leadDataLiving']['s'] ?? '{}', true) ?: [];
	
	// Gemiddelde lead per mode
	$lead_modes = [];
	$known = [];
	
	// Eerst de bekende modi verzamelen
	foreach ([1,2,3] as $m) {
		if (!empty($leadData[$m])) {
			$avg = round(array_sum($leadData[$m]) / count($leadData[$m]));
			$lead_modes[$m] = $avg;
			$known[] = $avg;
		}
	}
	
	// Bereken gemiddelde van bekende modi (of default 45 als geen enkele bekend is)
	$avgKnown = !empty($known) ? round(array_sum($known) / count($known)) : 45;
	
	// Vul ontbrekende modi aan met gemiddelde
	foreach ([1,2,3] as $m) {
		if (!isset($lead_modes[$m])) {
			$lead_modes[$m] = $avgKnown;
		}
	}
	
	if ($buiten < 20 && $d['minmaxtemp']['m'] < 22 && $mode >= 1) {
	
		// --- Basis-setpoints afhankelijk van status
		switch ($status) {
			case 0: $baseSet = 18; break; // thuis en wakker
			case 1: $baseSet = 16; break; // slapen
			case 2: $baseSet = 16; break; // weg
			case 3: $baseSet = 14; break; // op reis
			default: $baseSet = 14; break;
		}
	
		// --- Tijdschema
		$comfortMorning = $t;
		switch ($dow) {
			case 1: $comfortAfternoon = strtotime('12:50'); $comfortEnd = strtotime('19:00'); break;
			case 2: $comfortAfternoon = strtotime('16:00'); $comfortEnd = strtotime('19:00'); break;
			case 3: $comfortAfternoon = strtotime('12:10'); $comfortEnd = strtotime('19:00'); break;
			case 4: $comfortAfternoon = strtotime('16:00'); $comfortEnd = strtotime('19:00'); break;
			case 5: $comfortAfternoon = strtotime('15:00'); $comfortEnd = strtotime('19:30'); break;
			case 6: $comfortAfternoon = strtotime('08:00'); $comfortEnd = strtotime('19:30'); break;
			case 0: $comfortAfternoon = strtotime('08:00'); $comfortEnd = strtotime('19:00'); break;
		}
	
		$nacht = ($time >= strtotime('19:30') || $time < strtotime('04:00'));
		if ($nacht) $Setliving = min($baseSet, 16);
		else $Setliving = $baseSet;
	
		// --- Comfortperioden (ochtend & middag)
		$periods = [
			['start'=>$comfortMorning, 'target'=>18],
			['start'=>$comfortAfternoon, 'target'=>20]
		];
	
		foreach ($periods as $p) {
			$comfortStart = $p['start'];
			$target       = $p['target'];
			$lead_base    = $lead_modes[$mode] ?? 45; // fallback
	
			// dynamisch aanpassen op basis van huidige toestand
			$leadMinutes = $lead_base
						 - ($buiten * 0.4)
						 + (($target - $living) * 2);
	
			$t_start = $comfortStart - ($leadMinutes * 60);
			$t_end   = $comfortStart + 1800;
	
			// Verwarmen vóór comfortStart
			if ($time >= $t_start && $time < $comfortStart && ($status == 0 || $status == 1)) {
				$preheating=true;
				$Setliving = max($Setliving, $target);
				if ($living>=$target&&past('leadDataLiving')>14400) {
					$newLead=round(past('living_set')/60,0);
					$minLead = $lead_base - 30;
					$maxLead = $lead_base + 30;
					$newLead = max($minLead, min($maxLead, $newLead));
					if (!isset($leadData[$mode])) $leadData[$mode] = [];
					$leadData[$mode][] = $newLead;
					$leadData[$mode] = array_slice($leadData[$mode], -14);
					store('leadDataLiving', json_encode($leadData));
					lg("_TC_living: Mode $mode, target={$target}, actual={$living}, diff=" . round($diff,1) . "° → new lead_base={$newLead} min");
				}
			}
	
			// Handhaven tijdens comfortperiode
			if ($time >= $comfortStart && $time < $t_end && $status == 0) {
				$Setliving = max($Setliving, $target);
				if ($living>=$target&&past('leadDataLiving')>43200) {
					$newLead=round(past('living_set')/60,0);
					$minLead = $lead_base - 30;
					$maxLead = $lead_base + 30;
					$newLead = max($minLead, min($maxLead, $newLead));
					if (!isset($leadData[$mode])) $leadData[$mode] = [];
					$leadData[$mode][] = $newLead;
					$leadData[$mode] = array_slice($leadData[$mode], -14);
					store('leadDataLiving', json_encode($leadData));
					lg("_TC_living: Mode $mode, target={$target}, actual={$living}, diff=" . round($diff,1) . "° → new lead_base={$newLead} min");
				}
			}
		}
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