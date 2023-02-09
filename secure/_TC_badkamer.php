<?php
/**
 * Pass2PHP Temperature Control
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
$m='';$m2='';
$l=0;$m.=' base';
if ($d['badkamer_set']['m']==0) {$set=16.2;$m2.=__LINE__.' ';}
else {$set=$d['badkamer_set']['s'];$m2.=__LINE__.' ';}
if ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>57) {
	$set=5;$m2.=__LINE__.' ';
} elseif ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']>0) {
	if (past('badkamer_set')>14400&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']>16.2) {
			$set=16.2;$m2.=__LINE__.' ';
			if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('badkamer_set')>14400&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=16.2) || ($d['Weg']['s']>=2&&$d['badkamer_set']['s']!=16.2)) {
		store('badkamer_set', 16.2, basename(__FILE__).':'.__LINE__);
		$set=16.2;$m2.=__LINE__.' ';
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']>=1) {
	if ($d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<20&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']!=16.2) {
			$set=16.2;$m2.=__LINE__.' ';
		}
	}
	if($dow==0||$dow==6) {
		if (TIME>=strtotime('7:30')&&TIME<strtotime('7:45')) $set=$set+5.8;
		elseif (TIME>=strtotime('7:20')&&TIME<strtotime('7:45')) $set=$set+5.5;
		elseif (TIME>=strtotime('7:10')&&TIME<strtotime('7:45')) $set=$set+5.2;
		elseif (TIME>=strtotime('7:00')&&TIME<strtotime('7:45')) $set=$set+4.9;
		elseif (TIME>=strtotime('6:50')&&TIME<strtotime('7:45')) $set=$set+4.6;
		elseif (TIME>=strtotime('6:40')&&TIME<strtotime('7:45')) $set=$set+4.3;
		elseif (TIME>=strtotime('6:30')&&TIME<strtotime('7:45')) $set=$set+4;
		elseif (TIME>=strtotime('6:20')&&TIME<strtotime('7:45')) $set=$set+3.7;
		elseif (TIME>=strtotime('6:10')&&TIME<strtotime('7:45')) $set=$set+3.4;
		elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:45')) $set=$set+3.1;
		elseif (TIME>=strtotime('5:50')&&TIME<strtotime('7:45')) $set=$set+2.8;
		elseif (TIME>=strtotime('5:40')&&TIME<strtotime('7:45')) $set=$set+2.5;
		elseif (TIME>=strtotime('5:30')&&TIME<strtotime('7:45')) $set=$set+2.2;
		elseif (TIME>=strtotime('5:20')&&TIME<strtotime('7:45')) $set=$set+1.9;
		elseif (TIME>=strtotime('5:10')&&TIME<strtotime('7:45')) $set=$set+1.6;
		elseif (TIME>=strtotime('5:00')&&TIME<strtotime('7:45')) $set=$set+1.3;
		elseif (TIME>=strtotime('4:50')&&TIME<strtotime('7:45')) $set=$set+1;
		elseif (TIME>=strtotime('4:40')&&TIME<strtotime('7:45')) $set=$set+0.7;
		elseif (TIME>=strtotime('4:30')&&TIME<strtotime('7:45')) $set=$set+0.4;
	} else {
		if (TIME>=strtotime('6:30')&&TIME<strtotime('7:15')) $set=$set+5.8;
		elseif (TIME>=strtotime('6:20')&&TIME<strtotime('7:15')) $set=$set+5.5;
		elseif (TIME>=strtotime('6:10')&&TIME<strtotime('7:15')) $set=$set+5.2;
		elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:15')) $set=$set+4.9;
		elseif (TIME>=strtotime('5:50')&&TIME<strtotime('7:15')) $set=$set+4.6;
		elseif (TIME>=strtotime('5:40')&&TIME<strtotime('7:15')) $set=$set+4.3;
		elseif (TIME>=strtotime('5:30')&&TIME<strtotime('7:15')) $set=$set+4;
		elseif (TIME>=strtotime('5:20')&&TIME<strtotime('7:15')) $set=$set+3.7;
		elseif (TIME>=strtotime('5:10')&&TIME<strtotime('7:15')) $set=$set+3.4;
		elseif (TIME>=strtotime('5:00')&&TIME<strtotime('7:15')) $set=$set+3.1;
		elseif (TIME>=strtotime('4:50')&&TIME<strtotime('7:15')) $set=$set+2.8;
		elseif (TIME>=strtotime('4:40')&&TIME<strtotime('7:15')) $set=$set+2.5;
		elseif (TIME>=strtotime('4:30')&&TIME<strtotime('7:15')) $set=$set+2.2;
		elseif (TIME>=strtotime('4:20')&&TIME<strtotime('7:15')) $set=$set+1.9;
		elseif (TIME>=strtotime('4:10')&&TIME<strtotime('7:15')) $set=$set+1.6;
		elseif (TIME>=strtotime('4:00')&&TIME<strtotime('7:15')) $set=$set+1.3;
		elseif (TIME>=strtotime('3:50')&&TIME<strtotime('7:15')) $set=$set+1;
		elseif (TIME>=strtotime('3:40')&&TIME<strtotime('7:15')) $set=$set+0.7;
		elseif (TIME>=strtotime('3:30')&&TIME<strtotime('7:15')) $set=$set+0.4;
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']<1) {
	if ($d['badkamer_set']['s']!=5) {
		store('badkamer_set', 5, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=5;
	}
}
if (isset($set)) {
	if ($set!=$d['badkamer_set']['s']) store('badkamer_set', $set, basename(__FILE__).':'.__LINE__.' '.$m2);
	$d['badkamer_set']['s']=$set;
}

$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];

if ($d['badkamer_temp']['m']>=55&&($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')<60))) {$l=1;$m.=' + '.__LINE__.' badkamer_hum>=55';}
if ($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>60) {
	if ($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Closed'&&$d['kamer_temp']['m']>=55)) {$l=1;$m.=' + '.__LINE__.' kamer_hum>=55';}
	if ($d['deurspeelkamer']['s']=='Closed'||($d['deurspeelkamer']['s']=='Open'&&$d['raamspeelkamer']['s']=='Closed'&&$d['speelkamer_temp']['m']>=55)) {$l=1;$m.=' + '.__LINE__.' speelkamer_hum>=55';}
	if ($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Closed'&&$d['alex_temp']['m']>=55)) {$l=1;$m.=' + '.__LINE__.' alex_hum>=55';}
}
if ($difbadkamer<=-0.6) {$l=3;$m.=' + '.__LINE__.' difbadkamer<=-0.6';}
elseif ($difbadkamer<=-0.3) {$l=2;$m.=' + '.__LINE__.' difbadkamer<=-0.3';}
elseif ($difbadkamer< 0) {$l=1;$m.=' + '.__LINE__.' difbadkamer<0';}
else {
	if ($difbadkamer>0.2&&$d['badkamer_set']['s']>19) {
		if ($d['badkamer_set']['s']>16.2) {
			store('badkamer_set', 16.2, basename(__FILE__).':'.__LINE__);
		
		}
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
}

if ($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>60) {
	if ($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur kamer en raam kamer open';}
	if ($d['deurspeelkamer']['s']=='Open'&&$d['raamspeelkamer']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur speelkamer en raam speelkamer open';}
	if ($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur kamer Alex en raam Alex kamer open';}
}
if ($d['badkamer_set']['m']==0&&$l>2&&$d['lichtbadkamer']['s']==0) {$l=2;$m.=' + '.__LINE__.' auto';}
elseif ($d['badkamer_set']['m']==1&&$l>2&&$d['lichtbadkamer']['s']==0) {$l=2;$m.=' + '.__LINE__.' slow';}


if ($d['Weg']['s']>2) {$l=0;$m.=' + '.__LINE__.' base';}

//if (strlen($m)>5) lg($m);

if ($l==0) {
	if ($d['luchtdroger']['s']=='On') {
		if ($d['luchtdroger1']['s']=='On') sw('luchtdroger1', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger2']['s']=='On') sw('luchtdroger2', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger1']['s']=='Off'&&$d['luchtdroger2']['s']=='Off'&&past('luchtdroger1')>115&&past('luchtdroger2')>115) sw('luchtdroger', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
	}
} elseif ($l==1) {
	if ($d['luchtdroger']['s']=='Off') sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	else {
		if ($d['luchtdroger1']['s']=='Off') sw('luchtdroger1', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger2']['s']=='On') sw('luchtdroger2', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
	}
} elseif ($l==2) {
	if ($d['luchtdroger']['s']=='Off') sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	else {
		if ($d['luchtdroger1']['s']=='On') sw('luchtdroger1', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger2']['s']=='Off') sw('luchtdroger2', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	}
} elseif ($l==3) {
	if ($d['luchtdroger']['s']=='Off') sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	else {
		if ($d['luchtdroger1']['s']=='Off') sw('luchtdroger1', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger2']['s']=='Off') sw('luchtdroger2', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	}
} 
