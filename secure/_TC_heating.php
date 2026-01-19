<?php
//lg($user);
$prevt_start??=0;
$prevSetTime??=0;
$Setkamer=4;
$Setwaskamer=4;
$Setalex=4;
$spm=[-1=>'Eco',0=>'',1=>'Power'];
if ($d['weg']->s<=2&&$d['heating']->s>=1) {
	if ($d['kamer_set']->m==0) {
		if (
				($d['raamkamer']->s=='Closed'||$d['rkamerr']->s==100)
			&&
				(past('raamkamer')>2700||$time>strtotime('19:00'))
			&&
				(
					($d['deurkamer']->s=='Closed'||($d['deurkamer']->s=='Open'&&past('deurkamer')<900))
				||
					(
						($d['deuralex']->s=='Closed'||($d['deuralex']->s=='Open'&&past('deuralex')<900)||$d['raamalex']->s=='Closed'||$d['ralex']->s==100)
					&&
						($d['deurwaskamer']->s=='Closed'||($d['deurwaskamer']->s=='Open'&&past('deurwaskamer')<900)||$d['raamwaskamer']->s=='Closed'||$d['rwaskamer']->s==100)
					&&	$d['raamhall']->s=='Closed'
					)
				)
		) {
			$Setkamer=4;
		}
	} else $Setkamer=$d['kamer_set']->s;
	if ($d['alex_set']->m==0) {
		if (
				($d['raamalex']->s=='Closed'||$d['ralex']->s==100)
			&&
				(past('raamalex')>2700|| $time>strtotime('19:00'))
			&&
				(
					($d['deuralex']->s=='Closed'||($d['deuralex']->s=='Open'&&past('deuralex')<900))
				||
					(
						($d['deurkamer']->s=='Closed'||($d['deurkamer']->s=='Open'&&past('deurkamer')<900)||$d['raamkamer']->s=='Closed'||$d['rkamerr']->s==100)
					&&
						($d['deurwaskamer']->s=='Closed'||($d['deurwaskamer']->s=='Open'&&past('deurwaskamer')<900)||$d['raamwaskamer']->s=='Closed'||$d['rwaskamer']->s==100)
					&& $d['raamhall']->s=='Closed'
					)
				)
		) {
			$Setalex=4;
		}
	} else $Setalex=$d['alex_set']->s;
}
if ($d['kamer_set']->m==1) $Setkamer=$d['kamer_set']->s;
if ($d['alex_set']->m==1) $Setalex=$d['alex_set']->s;

if ($d['kamer_set']->s!=$Setkamer) {
	setpoint('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
	$d['kamer_set']->s=$Setkamer;
}
if ($d['alex_set']->s!=$Setalex) {
	setpoint('alex_set', $Setalex, basename(__FILE__).':'.__LINE__);
	$alex_set=$Setalex;
	$d['alex_set']->s=$Setalex;
}
$Setliving = 14;
$prevSet   = $d['living_start_temp']->m ?? 0;
if (($d['living_set']->m==0 || $d['living_set']->m==2) && $d['weg']->s<=1) {
	$living    = $d['living_temp']->s;
	$mode      = $d['heating']->s;
	$weg       = $d['weg']->s;
	if ($d['living_set']->m == 2) $weg = 0;
	$buitenTempStart = round($d['buiten_temp']->s / 0.5) * 0.5;
	if(!isset($leadDataLiving)) {
		$content = @file_get_contents('/var/www/html/secure/leadDataLiving.json');
		$leadDataLiving = $content ? json_decode($content, true) ?? [] : [];
		lg('leadDataLiving read from file');
	}
	if (!empty($leadDataLiving[$mode])) {
		$temps = array_keys($leadDataLiving[$mode]);
		sort($temps);
		$allData = [];
		$keyExists = in_array($buitenTempStart, $temps);
		if ($keyExists) {
			$currentIndex = array_search($buitenTempStart, $temps);
			if ($currentIndex > 0) {
				$allData = array_merge($allData, $leadDataLiving[$mode][$temps[$currentIndex - 1]]);
			}
			$allData = array_merge($allData, $leadDataLiving[$mode][$temps[$currentIndex]]);
			if ($currentIndex < count($temps) - 1) {
				$allData = array_merge($allData, $leadDataLiving[$mode][$temps[$currentIndex + 1]]);
			}
		} else {
			$lower = null;
			$higher = null;
			foreach ($temps as $temp) {
				if ($temp < $buitenTempStart) {
					$lower = $temp;
				} elseif ($temp > $buitenTempStart && $higher === null) {
					$higher = $temp;
					break;
				}
			}
			if ($lower !== null) {
				$allData = array_merge($allData, $leadDataLiving[$mode][$lower]);
			}
			if ($higher !== null) {
				$allData = array_merge($allData, $leadDataLiving[$mode][$higher]);
			}
		}
		if (!empty($allData)) {
			$avgMinPerDeg = round(array_sum($allData) / count($allData), 2);
		}
	}
	$avgMinPerDeg ??= 14;
	$baseSet = [
		0 => 19,
		1 => 16,
		2 => 16,
		3 => 14
	];
	$comfortStart = [
		1 => '13:00',// 13:00
		2 => '16:05',// 16:05
		3 => '12:15',// 12:15
		4 => '16:05',// 16:05
		5 => '13:35',// 15:05
		6 => '08:00',// 8:00
		0 => '08:00' // 8:00
	];
	if ($weekend==true) {
		$comfortAfternoon = strtotime($comfortStart[0]);
		$comfortEnd = strtotime('19:30');
	} else {
	 	if ($d['verlof']->s>0) $comfortAfternoon = strtotime($comfortStart[0]);
		else {
			$comfortAfternoon = strtotime($comfortStart[$dow]);
//			$comfortAfternoon = strtotime('7:45');
		}
			$comfortEnd = strtotime('19:00');
	}
	$target = 21;
	$tempDelta   = max(0, $target - $living);
	$leadMinutes = $avgMinPerDeg * $tempDelta;
	$t_start = round($comfortAfternoon - ($leadMinutes * 60));
	if ($daikin->living->power!=1) $t_start -= 300;
	if ($time >= $comfortEnd || $time < strtotime('04:00')) $Setliving = min($baseSet[$weg], 16);
	else $Setliving = $baseSet[$weg];
	if ($prevSet >= 1) {
		$Setliving = $target;
		if ($time > $comfortEnd) {
			storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
			$prevSet=0;
		}
		if ($d['living_set']->m==2) storemode('living_set', 0, basename(__FILE__) . ':' . __LINE__);
	}
	elseif ($time >= $t_start && $time < $comfortAfternoon && $weg <= 1) {
		$Setliving = max($Setliving, $target);
		if ($daikin->living->power!=1) {
			storesmip('living_start_temp', $living, 1, $buitenTempStart, 300, basename(__FILE__) . ':' . __LINE__);
		} else {
			storesmip('living_start_temp', $living, 1, $buitenTempStart, 0, basename(__FILE__) . ':' . __LINE__);
		}
		$prevSet=1;
		lg('🔥 _TC_living: '.
			' Cstart='.date("G:i",$comfortAfternoon).
			' t_start='.date("G:i:s",$t_start).
			' End='.date("G:i",$comfortEnd).
			' Setliving='.$Setliving.
			' bTempStart='.$buitenTempStart.
			' living='.$living.
			' target='.$target.
			' tempDelta='.$tempDelta.
			' avgMinPerDeg='.$avgMinPerDeg.
			' leadMinutes='.$leadMinutes
		);
//		lg('GET_DEFINED_VARS='.print_r(GET_DEFINED_VARS(),true));
	}
	elseif ($time >= $comfortAfternoon && $time < $comfortEnd && $weg == 0) {
		$Setliving = max($Setliving, $target);
	}
	else {
		$Setliving = $Setliving;
		if ($prevSet != 0) {
			storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
			$prevSet=0;
		}
	}
	if($prevt_start!=$t_start && $time >= $t_start -3600 && $prevSet < 2 && $time < $comfortAfternoon && 1==1) {
		$prevt_start=$t_start;
		lg('prevSet='.$prevSet.
			' Afternoon='.date("G:i",$comfortAfternoon).
			' t_start='.date("G:i:s",$t_start).
			' End='.date("G:i",$comfortEnd).
			' Setliving='.$Setliving.
			' bTempStart='.$buitenTempStart.
			' living='.$living.
			' target='.$target.
			' tempDelta='.$tempDelta.
			' avgMinPerDeg='.$avgMinPerDeg.
			' leadMinutes='.$leadMinutes
		);
	}
	if ($prevSet == 1 && $living >= $target-0.2) {
//		lg(basename(__FILE__) . ':' . __LINE__);
		$startTemp = $d['living_start_temp']->s;
		$tempRise    = $living - $startTemp;
		if ($tempRise > 1 && $prevSet == 1) {
//			lg(basename(__FILE__) . ':' . __LINE__);
			$buitenTempStart = $d['living_start_temp']->i;
			$startupDelay = $d['living_start_temp']->p ?? 0;
			$minutesUsed = (past('living_start_temp') - $startupDelay) / 60;
			$minPerDeg   = $minutesUsed / $tempRise;
			$minPerDeg = round(max($avgMinPerDeg - 10, min($avgMinPerDeg + 10, $minPerDeg)),1);
			$leadDataLiving[$mode][$buitenTempStart][] = $minPerDeg;
			$leadDataLiving[$mode][$buitenTempStart] = array_slice($leadDataLiving[$mode][$buitenTempStart], -5);
			$avgMinPerDeg = round(array_sum($leadDataLiving[$mode][$buitenTempStart]) / count($leadDataLiving[$mode][$buitenTempStart]),1);
			ksort($leadDataLiving, SORT_NUMERIC);
			foreach ($leadDataLiving as &$innerArray) {
				ksort($innerArray, SORT_NUMERIC);
			}
			unset($innerArray);
			file_put_contents('/var/www/html/secure/leadDataLiving.json', json_encode($leadDataLiving), LOCK_EX);
			$lastWriteleadDataLiving=$time;
			$minutesUsed=round($minutesUsed,1);
			$msg="🔥 _TC_living: Einde ΔT=" . round($tempRise,1) . "° in {$minutesUsed} min → ".round($minutesUsed / $tempRise,1)." min/°C (gemiddeld nu {$avgMinPerDeg} min/°C | buitenTempStart={$buitenTempStart})";
			lg($msg);
			telegram($msg.PHP_EOL.print_r($leadDataLiving,true));
			unset($t_start);
		}
		storemode('living_start_temp', 2, basename(__FILE__) . ':' . __LINE__);
		$prevSet=2;
		$$prevSetTime=$time;
	}
}
if ($d['living_set']->m==1) $Setliving=$d['living_set']->s;
if ($d['living_set']->s!=$Setliving) {
	setpoint('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
	$living_set=$Setliving;
	$d['living_set']->s=$Setliving;
}

require('_Rolluiken_Heating.php');
$bigdif=100;
$difgas=100;
$user = 'heating';
if ($d['heating']->s==1) require ('_TC_heating_airco.php');
elseif ($d['heating']->s==2) require ('_TC_heating_gasairco.php');
elseif ($d['heating']->s==3) require ('_TC_heating_gas.php');
require('_TC_badkamer.php');

if ($d['heating']->s<=1&&$d['brander']->s=='On'&&past('brander')>1195) sw('brander', 'Off');
