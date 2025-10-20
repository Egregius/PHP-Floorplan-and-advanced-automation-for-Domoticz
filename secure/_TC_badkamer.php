<?php
$user='TC_badkamer';
$m='';$m2='';
$l=0;

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
	$set = 13;
	$target = 20.5;
	$buiten = $d['buiten_temp']['s'];
	$badkamer = $d['badkamer_temp']['s'];
	
	// --- adaptive lead_base uit DB (JSON)
	$leadDataBath = json_decode($d['leadDataBath']['s'] ?? '{}', true) ?: [];
	$lead_base = !empty($leadDataBath[1]) ? round(array_sum($leadDataBath[1])/count($leadDataBath[1])) : 90; // start 90 min
	
	// --- dynamische leadberekening
	$leadMinutes = $lead_base 
				 - ($buiten * 0.5)         // koude buitenlucht → vroeger starten
				 + (($target - $badkamer) * 2); // hoe kouder binnen, hoe vroeger starten
	
	$t_start = $t - ($leadMinutes * 60);
	$t_end   = $t + 1800; // 30 min na doelmoment
	
	// --- bepalen setpoint
	if ($time < $t_start) {
		$set = 13; // nog niet starten
	} elseif ($time >= $t_start && $time < $t) {
		$set = $target; // vol verwarmen
		if ($badkamer>=$target&&past('leadDataBath')>43200&&past('8badkamer_8')>14400) {
			$newLead=round(past('badkamer_set')/60,0);
			$minLead = $lead_base - 10;
			$maxLead = $lead_base + 10;
			$newLead = max($minLead, min($maxLead, $newLead));
			if (!isset($leadDataBath[1])) $leadDataBath[1] = [];
			$leadDataBath[1][] = $newLead;
			$leadDataBath[1] = array_slice($leadDataBath[1], -14); // max 14 dagen bewaren
			store('leadDataBath', json_encode($leadDataBath),basename(__FILE__).':'.__LINE__,1);
			lg("_TC_badkamer: target={$target}, actual={$badkamer}, diff=" . round($diff,1) . "° → new lead_base={$newLead} min");
		}
	} elseif ($time >= $t && $time <= $t_end) {
		$set = $target; // nog even aanhouden
		if ($badkamer>=$target&&past('leadDataBath')>43200&&past('8badkamer_8')>14400) {
			$newLead=round(past('badkamer_set')/60,0);
			$minLead = $lead_base - 10;
			$maxLead = $lead_base + 10;
			$newLead = max($minLead, min($maxLead, $newLead));
			if (!isset($leadDataBath[1])) $leadDataBath[1] = [];
			$leadDataBath[1][] = $newLead;
			$leadDataBath[1] = array_slice($leadDataBath[1], -14); // max 14 dagen bewaren
			store('leadDataBath', json_encode($leadDataBath),basename(__FILE__).':'.__LINE__,1);
			lg("_TC_badkamer: target={$target}, actual={$badkamer}, diff=" . round($diff,1) . "° → new lead_base={$newLead} min");
		}
	} else {
		$set = 13; // daarna terug omlaag
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
$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
if ($difbadkamer<=-0.5) {$l=2;$m.=' + '.__LINE__.' difbadkamer<=-0.3';}
elseif ($difbadkamer< 0) {$l=1;$m.=' + '.__LINE__.' difbadkamer<0';}

if ($d['badkamer_set']['m']==1&&$l>2&&$d['lichtbadkamer']['s']==0) {$l=2;$m.=' + '.__LINE__.' slow';}
if ($time>$t+2700&&$l>1&&past('badkamer_set')>3600) {$l=1;$m.=' + '.__LINE__.' weg';}
if ($d['weg']['s']>=2) {$l=0;$m.=' + '.__LINE__.' weg';}
$p1=past('badkamervuur1');

if ($l==0) {
	if ($d['badkamervuur1']['s']=='On'&&$p1>=300) sw('badkamervuur1', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
	if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
} elseif ($l==1&&$d['heating']['s']>=0&&$set>=13) {
	if ($d['badkamervuur1']['s']=='Off'&&$d['deurbadkamer']['s']=='Closed'&&$p1>=300) sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	if ($d['badkamervuur2']['s']=='On'&&($d['badkamer_set']['m']==2||$d['badkamer_set']['m']==0)) sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
} elseif ($l==2&&$d['heating']['s']>=0) {
	if ($d['badkamervuur1']['s']=='Off'&&$d['deurbadkamer']['s']=='Closed'&&$p1>=300) sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	if ($d['badkamervuur2']['s']=='Off'&&$d['deurbadkamer']['s']=='Closed'&&$p1>=300&&$d['badkamer_set']['m']==2&&$set>=19) sw('badkamervuur2', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	elseif ($d['badkamervuur2']['s']=='On'&&$d['badkamer_set']['m']<2) sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
} 
//if ($d['wasdroger']['s']=='On') {
//	if (($d['waskamer_temp']['m']<65&&past('wasdroger')>3595)||$d['raamwaskamer']['s']=='Open') sw('wasdroger', 'Off', basename(__FILE__).':'.__LINE__);
//} else {
//	if ($d['waskamer_temp']['m']>80&&$d['raamwaskamer']['s']=='Closed'&&$d['deurwaskamer']['s']=='Closed'&&past('wasdroger')>595) sw('wasdroger', 'On', basename(__FILE__).':'.__LINE__);
//}
