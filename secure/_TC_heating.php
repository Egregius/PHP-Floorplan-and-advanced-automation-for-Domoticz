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
			$Setkamer=6;
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
			$Setalex=6;
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
$prevSet   = $d['living_start_temp']['m'] ?? 0;
if (($d['living_set']['m']==0&&$d['weg']['s']<=1)||($d['living_set']['m']==2&&$d['weg']['s']<=1)) {
	$living    = $d['living_temp']['s'];
	$mode      = $d['heating']['s'];
	$weg       = $d['weg']['s'];
	if ($d['living_set']['m'] == 2) $weg = 0;
	$buitenTempStart = (floor($d['buiten_temp']['s'] / 2)) * 2;
	if(!isset($leadDataLiving)) $leadDataLiving=json_decode(file_get_contents('/var/www/html/secure/leadDataLiving.json'),true);
	if(!isset($lastWriteleadDataLiving)) $lastWriteleadDataLiving=filemtime('/var/www/html/secure/leadDataLiving.json');
	$avgMinPerDeg = null;
	if (!empty($leadDataLiving[$mode])) {
		if (!empty($leadDataLiving[$mode][$buitenTempStart])) {
			$data = $leadDataLiving[$mode][$buitenTempStart];
		} else {
			$temps = array_keys($leadDataLiving[$mode]);
			usort($temps, function ($a, $b) use ($buitenTempStart) {
				$da = abs($a - $buitenTempStart);
				$db = abs($b - $buitenTempStart);
				if ($da !== $db) {
					return $da <=> $db;
				}
				return $a <=> $b;
			});
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
		1 => '11:00',
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
		else {
			$comfortAfternoon = strtotime($comfortStart[$dow]);
//			$comfortAfternoon = strtotime('7:45');
		}
			$comfortEnd = strtotime('19:00');
	}

	
	$target = 21;
	$tempDelta   = max(0, $target - $living);
	$leadMinutes = round($avgMinPerDeg * $tempDelta);
	$t_start ??= $comfortAfternoon - ($leadMinutes * 60);
	$t_start=strtotime('5:42');
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
//		$Setliving = max($Setliving, $target);
		$Setliving = $target;
		storesmi('living_start_temp', $living, 1, $buitenTempStart, basename(__FILE__) . ':' . __LINE__);
		$msg="🔥 _TC_living: Start leadMinutes={$leadMinutes}	| avgMinPerDeg={$avgMinPerDeg} | buitenTempStart={$buitenTempStart}";
		lg($msg);
	}
	elseif ($time >= $comfortAfternoon && $time < $t_end && $weg == 0) {
		$Setliving = max($Setliving, $target);
	}
	else {
		$Setliving = $Setliving;
		if ($prevSet != 0) storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
	}
//	lg(date("G:i",$t_start));
//	if($prevSet == 1) lg(basename(__FILE__) . ':' . __LINE__);
//	if ($living>=$target ) lg(basename(__FILE__) . ':' . __LINE__.' '.$living.' '.$target); else lg("$living>=$target");
//	if ($time >= $t_start) lg(basename(__FILE__) . ':' . __LINE__);
//	if ($time < $comfortAfternoon+7200) lg(basename(__FILE__) . ':' . __LINE__);
//	if ($lastWriteleadDataLiving < $time-43200) lg(basename(__FILE__) . ':' . __LINE__);
	if ($prevSet == 1 && ($living>=$target||($preheating===true&&$living>=$target-0.1)) /*&& $lastWriteleadDataLiving < $time-43200*/) {
//		lg(basename(__FILE__) . ':' . __LINE__);
		$startTemp = $d['living_start_temp']['s'];
		$tempRise    = $living - $startTemp;
		if ($tempRise>0.5) {
			$buitenTempStart = $d['living_start_temp']['i'];
			$minutesUsed = round(past('living_start_temp') / 60,1);
			$minPerDeg   = $minutesUsed / $tempRise;
			$minPerDeg = round(max($avgMinPerDeg - 10, min($avgMinPerDeg + 20, $minPerDeg)),1);
			$leadDataLiving[$mode][$buitenTempStart][] = $minPerDeg;
			$leadDataLiving[$mode][$buitenTempStart] = array_slice($leadDataLiving[$mode][$buitenTempStart], -7);
			$avgMinPerDeg = round(array_sum($leadDataLiving[$mode][$buitenTempStart]) / count($leadDataLiving[$mode][$buitenTempStart]),1);
			ksort($leadDataLiving, SORT_NUMERIC);
			foreach ($leadDataLiving as &$innerArray) {
				ksort($innerArray, SORT_NUMERIC);
			}
			unset($innerArray); 
			file_put_contents('/var/www/html/secure/leadDataLiving.json', json_encode($leadDataLiving), LOCK_EX);
			$lastWriteleadDataLiving=$time;
			storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
			$msg="🔥 _TC_living: Einde ΔT=" . round($tempRise,1) . "° in {$minutesUsed} min → {$minPerDeg} min/°C (gemiddeld nu {$avgMinPerDeg} min/°C | buitenTempStart={$buitenTempStart})";
			lg($msg);
			telegram($msg.PHP_EOL.print_r($leadDataLiving,true));
			unset($t_start);
		}
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