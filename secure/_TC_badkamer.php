<?php
$user='TC_badkamer';
$m='';$m2='';
$preheatbath=false;
if ($d['badkamer_set']->m==0) {$setBath=10;$m2.=__LINE__.' ';}
else {$setBath=$d['badkamer_set']->s;$m2.=__LINE__.' ';}
$pastdeurbadkamer=past('deurbadkamer');
if ($d['weg']->s>=2) $setBath=10;
elseif ($d['badkamer_set']->m==0&&$d['deurbadkamer']->s=='Open'&&$pastdeurbadkamer>57&&($d['raamkamer']->s=='Open'||$d['raamwaskamer']->s=='Open'||$d['raamalex']->s=='Open')) {
	$setBath=5;$m2.=__LINE__.' ';
} elseif ($d['badkamer_set']->m==0&&($d['deurbadkamer']->s=='Closed'||($d['deurbadkamer']->s=='Open'&&$pastdeurbadkamer<57))&&$d['badkamer_set']->m>0) {
	if (past('badkamer_set')>=14400&&$d['lichtbadkamer']->s==0&&$d['buiten_temp']->s<21&&$d['weg']->s<2) {
		if ($d['badkamer_set']->s>130) {
			$setBath=10;$m2.=__LINE__.' ';
			if ($d['badkamer_set']->m>0) storemode('badkamer_set', 0);
		}
	} elseif (past('badkamer_set')>=14400&&($d['lichtbadkamer']->s==0&&$d['badkamer_set']->s!=13) || ($d['weg']->s>=2&&$d['badkamer_set']->s!=13)) {
		setpoint('badkamer_set', 10);
		$setBath=10;$m2.=__LINE__.' ';
		if ($d['badkamer_set']->m>0) storemode('badkamer_set', 0);
	}
} elseif (($d['deurbadkamer']->s=='Closed'||($d['deurbadkamer']->s=='Open'&&$pastdeurbadkamer<57))&&$d['badkamer_set']->m==0&&$d['heating']->s>=0) {
	if ($d['lichtbadkamer']->s==0&&$d['buiten_temp']->s<20&&$d['weg']->s<2) {
		if ($d['badkamer_set']->s!=10) {$setBath=10;$m2.=__LINE__.' ';}
	}
	$setBath       = 10;
	$target    = 20.5;
	$badkamer  = $d['badkamer_temp']->s;
	$buitenTempStart = round($d['buiten_temp']->s / 0.5) * 0.5;
	$mode      = $d['heating']->s;
	$prevSetbath   = $d['badkamer_start_temp']->m ?? 0;
	if(!isset($leadDataBath)) {
		$contentBath = @file_get_contents('/var/www/leadDataBath.json');
		$leadDataBath = $contentBath ? json_decode($contentBath, true) ?? [] : [];
		lg('leadDataBath read from file');
	}
	if(!isset($lastWriteleadDataBath)) $lastWriteleadDataBath=@filemtime('/var/www/leadDataBath.json')??0;
	if (!empty($leadDataBath[$mode])) {
		$temps = array_keys($leadDataBath[$mode]);
		sort($temps);
		$allData = [];
		$keyExists = in_array($buitenTempStart, $temps);
		if ($keyExists) {
			$currentIndex = array_search($buitenTempStart, $temps);
			if ($currentIndex > 0) {
				$allData = array_merge($allData, $leadDataBath[$mode][$temps[$currentIndex - 1]]);
			}
			$allData = array_merge($allData, $leadDataBath[$mode][$temps[$currentIndex]]);
			if ($currentIndex < count($temps) - 1) {
				$allData = array_merge($allData, $leadDataBath[$mode][$temps[$currentIndex + 1]]);
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
				$allData = array_merge($allData, $leadDataBath[$mode][$lower]);
			}
			if ($higher !== null) {
				$allData = array_merge($allData, $leadDataBath[$mode][$higher]);
			}
		}
		if (!empty($allData)) {
			$avgMinPerDegBath = round(array_sum($allData) / count($allData), 2);
		}
	}
	$avgMinPerDegBath ??= 20;
	$tempDelta   = max(0, $target - $badkamer);
	$leadMinutes = $avgMinPerDegBath * $tempDelta;
	$t_start     = round($t - ($leadMinutes * 60));
	$t_end       = $t + 1800;
	if ($prevSetbath >= 1) {
		$setBath = $target;
		if ($time > $t_end) {
			storemode('badkamer_start_temp', 0);
			$prevSetbath=0;
		}
		$preheatbath=true;
	} elseif ($time >= $t_start && $time < $t) {
		$preheatbath=true;
		$setBath = $target;
		storesmi('badkamer_start_temp', $badkamer, 1, $buitenTempStart);
		$prevSetbath=1;
		$msg="_TC_bath: Start leadMinutes={$leadMinutes}	| avgMinPerDegBath={$avgMinPerDegBath}";
		lg($msg);
	} elseif ($time >= $t && $time <= $t_end) {
		$setBath = $target;
		$preheatbath=false;
	} else {
		$setBath = 10;
		if ($prevSetbath != 0) {
			storemode('badkamer_start_temp', 0);
			$prevSetbath=0;
		}
		$preheatbath=false;
	}
//	lg(print_r($leadDataBath,true));
//	lg(print_r($temps,true));

	if ($prevSetbath >= 1 && $badkamer >= $target-0.2 /*&& $lastWriteleadDataBath < $time-43200*/) {
		$startTemp = $d['badkamer_start_temp']->s;
		$tempRise    = $badkamer - $startTemp;
		if ($tempRise>1&&$prevSetbath == 1) {
			$buitenTempStart = $d['badkamer_start_temp']->i;
			$minutesUsed = past('badkamer_start_temp') / 60;
			$minPerDeg = $minutesUsed / $tempRise;
			$minPerDeg = round(clamp($minPerDeg,$avgMinPerDegBath - 5,$avgMinPerDegBath +5),1);
			$leadDataBath[$mode][$buitenTempStart][] = round($minPerDeg,1);
			$leadDataBath[$mode][$buitenTempStart] = array_slice($leadDataBath[$mode][$buitenTempStart], -10);
			$avgMinPerDegBath = floor(array_sum($leadDataBath[$mode][$buitenTempStart]) / count($leadDataBath[$mode][$buitenTempStart]));
			ksort($leadDataBath, SORT_NUMERIC);
			foreach ($leadDataBath as &$innerArray) {
				ksort($innerArray, SORT_NUMERIC);
			}
			unset($innerArray);
			file_put_contents('/var/www/leadDataBath.json', json_encode($leadDataBath), LOCK_EX);
			$lastWriteleadDataBath=$time;
			$minutesUsed=round($minutesUsed,1);
			$msg="_TC_bath: Einde ΔT=" . round($tempRise,1) . "° in {$minutesUsed} min → ".round($minutesUsed / $tempRise,1)." min/°C (gemiddeld nu {$avgMinPerDegBath} min/°C | buitenTempStart={$buitenTempStart})";
			lg($msg);
			telegram($msg.PHP_EOL.print_r($leadDataBath,true));
		}
		storemode('badkamer_start_temp', 2, basename(__FILE__) . ':' . __LINE__);
		$prevSetbath=2;
	}
} elseif ($d['deurbadkamer']->s=='Closed'&&$d['badkamer_set']->m==0&&$d['heating']->s<0) {
	if ($d['badkamer_set']->s!=5) {
		setpoint('badkamer_set', 5);
	}
}
if (isset($setBath)&&$d['heating']->s>=0) {
	if ($setBath!=$d['badkamer_set']->s) {
		setpoint('badkamer_set', $setBath, $m2);
	}
}
if ($d['heating']->s>=2) {
	if ($d['weg']->s<=1&&$d['badkamer_temp']->s<13.5) {
		if ($d['badkamer_set']->i!=true) {
			hass('climate','set_temperature','climate.zbadkamer',['temperature' => 28]);
			storeicon('badkamer_set',true);
		}
		if($d['badkamer_temp']->s<11&&$d['brander']->s=='Off'&&past('brander')>900) sw('brander', 'On');
	} elseif (($setBath>13&&$d['weg']->s<=1)||$d['living_set']->s<=17) {
		if ($d['badkamer_set']->i!=true) {
			hass('climate','set_temperature','climate.zbadkamer',['temperature' => 28]);
			storeicon('badkamer_set',true);
		}
	} else {
		if($d['badkamer_set']->i!=false&&$d['living_set']->s>17&&$d['brander']->s=='On') {
			hass('climate','set_temperature','climate.zbadkamer',['temperature' => 15]);
			storeicon('badkamer_set',false);
		}
	}
}
if ($d['weg']->s<2) {
	$difbadkamer=$d['badkamer_temp']->s-$d['badkamer_set']->s;
	if ($difbadkamer<=-0.5||($prevSetbath==1&&$difbadkamer<=0.1)) {
		if ($d['badkamervuur1']->s=='Off'&&$d['deurbadkamer']->s=='Closed') sw('badkamervuur1', 'On');
		if ($d['badkamervuur1']->s=='On'&&$d['badkamervuur2']->s=='Off'&&$d['deurbadkamer']->s=='Closed') sw('badkamervuur2', 'On');
	} elseif ($difbadkamer<=0||($prevSetbath==1&&$difbadkamer<=0)) {
		if ($d['badkamervuur1']->s=='Off'&&$d['deurbadkamer']->s=='Closed') sw('badkamervuur1', 'On');
		if ($d['badkamervuur2']->s=='On') sw('badkamervuur2', 'Off');
	} else {
		if ($d['badkamervuur2']->s=='On') sw('badkamervuur2', 'Off');
		if ($d['badkamervuur2']->s=='Off'&&$d['badkamervuur1']->s=='On') sw('badkamervuur1', 'Off');
	}
} else {
	if ($d['badkamervuur2']->s=='On') sw('badkamervuur2', 'Off');
	if ($d['badkamervuur2']->s=='Off'&&$d['badkamervuur1']->s=='On') sw('badkamervuur1', 'Off');
}
//if ($d['wasdroger']->s=='On') {
//	if (($d['waskamer_temp']->m<65&&past('wasdroger')>3595)||$d['raamwaskamer']->s=='Open') sw('wasdroger', 'Off');
//} else {
//	if ($d['waskamer_temp']->m>80&&$d['raamwaskamer']->s=='Closed'&&$d['deurwaskamer']->s=='Closed'&&past('wasdroger')>595) sw('wasdroger', 'On');
//}
