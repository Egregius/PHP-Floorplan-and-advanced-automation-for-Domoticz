<?php
$user='TC_badkamer';
$m='';$m2='';
$preheatbath=false;
if ($d['badkamer_set']['m']==0) {$set=13;$m2.=__LINE__.' ';}
else {$set=$d['badkamer_set']['s'];$m2.=__LINE__.' ';}
$pastdeurbadkamer=past('deurbadkamer');
if ($d['weg']['s']>=2) $set=10;
elseif ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer>57&&($d['raamkamer']['s']=='Open'||$d['raamwaskamer']['s']=='Open'||$d['raamalex']['s']=='Open')) {
	$set=5;$m2.=__LINE__.' ';
} elseif ($d['badkamer_set']['m']==0&&($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer<57))&&$d['badkamer_set']['m']>0) {
	if (past('badkamer_set')>=14400&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['weg']['s']<2) {
		if ($d['badkamer_set']['s']>13) {
			$set=13;$m2.=__LINE__.' ';
			if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0);
		}
	} elseif (past('badkamer_set')>=14400&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=13) || ($d['weg']['s']>=2&&$d['badkamer_set']['s']!=13)) {
		setpoint('badkamer_set', 13);
		$set=13;$m2.=__LINE__.' ';
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0);
	}
} elseif (($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer<57))&&$d['badkamer_set']['m']==0&&$d['heating']['s']>=0) {
	if ($d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<20&&$d['weg']['s']<2) {
		if ($d['badkamer_set']['s']!=13) {$set=13;$m2.=__LINE__.' ';}
	}
	$set       = 13;
	$target    = 21;
	$badkamer  = $d['badkamer_temp']['s'];
	$buitenTempStart = (floor($d['buiten_temp']['s'] / 2)) * 2;
	$mode      = $d['heating']['s'];
	$prevSet   = $d['badkamer_start_temp']['m'] ?? 0;
	if(!isset($leadDataBath)) $leadDataBath=json_decode(file_get_contents('/var/www/html/secure/leadDataBath.json'),true);
	if(!isset($lastWriteleadDataBath)) $lastWriteleadDataBath=filemtime('/var/www/html/secure/leadDataBath.json');
	$avgMinPerDeg = null;
	if (!empty($leadDataBath[$mode])) {
		if (!empty($leadDataBath[$mode][$buitenTempStart])) {
			$data = $leadDataBath[$mode][$buitenTempStart];
		} else {
			$temps = array_keys($leadDataBath[$mode]);
			usort($temps, function ($a, $b) use ($buitenTempStart) {
				$da = abs($a - $buitenTempStart);
				$db = abs($b - $buitenTempStart);
				if ($da !== $db) {
					return $da <=> $db;
				}
				return $a <=> $b;
			});
			$closestTemp = $temps[0];
			$data = $leadDataBath[$mode][$closestTemp];
		}
		if (!empty($data)) {
			$avgMinPerDeg = round(array_sum($data) / count($data), 1);
		}
	}
	$avgMinPerDeg ??= 20;
	$tempDelta   = max(0, $target - $badkamer);
	$leadMinutes = round($avgMinPerDeg * $tempDelta);
	$t_start     = $t - ($leadMinutes * 60);
	$t_end       = $t + 1800;
	if ($prevSet == 1) {
		$set = $target;
		if ($time > $t_end) storemode('badkamer_start_temp', 0);
		$preheatbath=true;
	} elseif ($time >= $t_start && $time < $t) {
		$set = $target;
		$msg="_TC_bath: Start leadMinutes={$leadMinutes}	| avgMinPerDeg={$avgMinPerDeg}";
		lg($msg);
		storemode('badkamer_start_temp', 1);
		storeicon('badkamer_start_temp', $buitenTempStart);
		$preheatbath=true;
	} elseif ($time >= $t && $time <= $t_end) {
		$set = $target;
		$preheatbath=false;
	} else {
		$set = 13;
		if ($prevSet != 0) storemode('badkamer_start_temp', 0);
		$preheatbath=false;
	}
	if ($prevSet == 1 && $badkamer >= $target && $lastWriteleadDataBath > $time-43200) {
		$startTemp = $d['badkamer_start_temp']['s'];
		if ($startTemp && $badkamer >= $startTemp) {
			$tempRise    = $badkamer - $startTemp;
			if ($tempRise>1) {
				$buitenTempStart = $d['badkamer_start_temp']['icon'];
				$minutesUsed = round(past('badkamer_start_temp') / 60, 1);
				$minPerDeg = ceil($minutesUsed / $tempRise);
				$minPerDeg = round(max($avgMinPerDeg - 10, min($avgMinPerDeg + 20, $minPerDeg)),1);
				if (!isset($leadDataBath[$mode][$buitenTempStart])) $leadDataBath[$mode][$buitenTempStart] = [];
				$leadDataBath[$mode][$buitenTempStart][] = $minPerDeg;
				$leadDataBath[$mode][$buitenTempStart] = array_slice($leadDataBath[$mode][$buitenTempStart], -7);
				$avgMinPerDeg = floor(array_sum($leadDataBath[$mode][$buitenTempStart]) / count($leadDataBath[$mode][$buitenTempStart]));
				file_put_contents('/var/www/html/secure/leadDataBath.json', json_encode($leadDataBath), LOCK_EX);
				$lastWriteleadDataBath=$time;
				$msg="_TC_bath: Einde ΔT=" . round($tempRise,1) . "° in {$minutesUsed} min → {$minPerDeg} min/°C (gemiddeld nu {$avgMinPerDeg} min/°C)";
				lg($msg);
			}
		}
	}
	if (abs($time - $t_start) < 10) {
		store('badkamer_start_temp', $badkamer);
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']<0) {
	if ($d['badkamer_set']['s']!=5) {
		setpoint('badkamer_set', 5);
	}
}
if (isset($set)&&$d['heating']['s']>=0) {
	if ($set!=$d['badkamer_set']['s']) {
		setpoint('badkamer_set', $set, $m2);
	}
}
if ($d['heating']['s']>=2) {
	if ($d['weg']['s']<=1&&$d['badkamer_temp']['s']<14) {
		if ($d['badkamer_set']['icon']!=true) {
			hassopts('climate','set_temperature','climate.zbadkamer',['temperature' => 28]);
			storeicon('badkamer_set',true);
		}
		if ($d['brander']['s']=='Off'&&past('brander')>900) sw('brander', 'On');
	} elseif (($set>13&&$d['weg']['s']<=1)||$d['living_set']['s']<=17) {
		if ($d['badkamer_set']['icon']!=true) {
			hassopts('climate','set_temperature','climate.zbadkamer',['temperature' => 28]);
			storeicon('badkamer_set',true);
		}
	} else {
		if($d['badkamer_set']['icon']!=false&&$d['living_set']['s']>17&&$d['brander']['s']=='On') {
			hassopts('climate','set_temperature','climate.zbadkamer',['temperature' => 15]);
			storeicon('badkamer_set',false);
		}
	}
}
if ($d['weg']['s']<2) {
	$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
	if ($difbadkamer<=-0.5||($preheatbath==true&&$difbadkamer<=-0.1)) {
		if ($d['badkamervuur1']['s']=='Off'&&$d['deurbadkamer']['s']=='Closed') sw('badkamervuur1', 'On');
		if ($d['badkamervuur1']['s']=='On'&&$d['badkamervuur2']['s']=='Off'&&$d['deurbadkamer']['s']=='Closed') sw('badkamervuur2', 'On');
	}
	elseif ($difbadkamer<=0||($preheatbath==true&&$difbadkamer<=+0.1)) {
		if ($d['badkamervuur1']['s']=='Off'&&$d['deurbadkamer']['s']=='Closed') sw('badkamervuur1', 'On');
		if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off');
	}
	else {
		if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off');
		if ($d['badkamervuur2']['s']=='Off'&&$d['badkamervuur1']['s']=='On') sw('badkamervuur1', 'Off');
	}
}
else {
	if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off');
	if ($d['badkamervuur2']['s']=='Off'&&$d['badkamervuur1']['s']=='On') sw('badkamervuur1', 'Off');
}
//if ($d['wasdroger']['s']=='On') {
//	if (($d['waskamer_temp']['m']<65&&past('wasdroger')>3595)||$d['raamwaskamer']['s']=='Open') sw('wasdroger', 'Off');
//} else {
//	if ($d['waskamer_temp']['m']>80&&$d['raamwaskamer']['s']=='Closed'&&$d['deurwaskamer']['s']=='Closed'&&past('wasdroger')>595) sw('wasdroger', 'On');
//}
