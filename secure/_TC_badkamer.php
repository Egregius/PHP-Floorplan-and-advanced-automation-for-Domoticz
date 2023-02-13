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
if ($d['badkamer_set']['m']==0) {$set=15.2;$m2.=__LINE__.' ';}
else {$set=$d['badkamer_set']['s'];$m2.=__LINE__.' ';}
if ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>57) {
	$set=5;$m2.=__LINE__.' ';
} elseif ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']>0) {
	if (past('badkamer_set')>14400&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']>15.2) {
			$set=15.2;$m2.=__LINE__.' ';
			if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('badkamer_set')>14400&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=15.2) || ($d['Weg']['s']>=2&&$d['badkamer_set']['s']!=15.2)) {
		store('badkamer_set', 15.2, basename(__FILE__).':'.__LINE__);
		$set=15.2;$m2.=__LINE__.' ';
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']>=1) {
	if ($d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<20&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']!=15.2) {
			$set=15.2;$m2.=__LINE__.' ';
		}
	}
	if($dow==0||$dow==6) {
		if (TIME>=strtotime('7:30')&&TIME<strtotime('7:35')) {$set+=5.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('7:21')&&TIME<strtotime('7:35')) {$set+=5.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('7:12')&&TIME<strtotime('7:35')) {$set+=5.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('7:03')&&TIME<strtotime('7:35')) {$set+=4.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:54')&&TIME<strtotime('7:35')) {$set+=4.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:45')&&TIME<strtotime('7:35')) {$set+=4.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:36')&&TIME<strtotime('7:35')) {$set+=4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:27')&&TIME<strtotime('7:35')) {$set+=3.7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:18')&&TIME<strtotime('7:35')) {$set+=3.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:09')&&TIME<strtotime('7:35')) {$set+=3.1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:35')) {$set+=2.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:51')&&TIME<strtotime('7:35')) {$set+=2.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:42')&&TIME<strtotime('7:35')) {$set+=2.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:33')&&TIME<strtotime('7:35')) {$set+=1.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:24')&&TIME<strtotime('7:35')) {$set+=1.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:15')&&TIME<strtotime('7:35')) {$set+=1.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:06')&&TIME<strtotime('7:35')) {$set+=1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:57')&&TIME<strtotime('7:35')) {$set+=0.7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:48')&&TIME<strtotime('7:35')) {$set+=0.4;$m.=' '.__LINE__;}
	} elseif($dow==2||$dow==5) {
		if (TIME>=strtotime('6:30')&&TIME<strtotime('7:05')) {$set+=5.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:21')&&TIME<strtotime('7:05')) {$set+=5.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:12')&&TIME<strtotime('7:05')) {$set+=5.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:03')&&TIME<strtotime('7:05')) {$set+=4.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:54')&&TIME<strtotime('7:05')) {$set+=4.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:45')&&TIME<strtotime('7:05')) {$set+=4.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:36')&&TIME<strtotime('7:05')) {$set+=4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:27')&&TIME<strtotime('7:05')) {$set+=3.7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:18')&&TIME<strtotime('7:05')) {$set+=3.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:09')&&TIME<strtotime('7:05')) {$set+=3.1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:00')&&TIME<strtotime('7:05')) {$set+=2.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:51')&&TIME<strtotime('7:05')) {$set+=2.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:42')&&TIME<strtotime('7:05')) {$set+=2.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:33')&&TIME<strtotime('7:05')) {$set+=1.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:24')&&TIME<strtotime('7:05')) {$set+=1.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:15')&&TIME<strtotime('7:05')) {$set+=1.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:06')&&TIME<strtotime('7:05')) {$set+=1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:57')&&TIME<strtotime('7:05')) {$set+=0.7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:48')&&TIME<strtotime('7:05')) {$set+=0.4;$m.=' '.__LINE__;}
	} else {
		if (TIME>=strtotime('6:45')&&TIME<strtotime('7:20')) {$set+=5.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:36')&&TIME<strtotime('7:20')) {$set+=5.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:27')&&TIME<strtotime('7:20')) {$set+=5.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:18')&&TIME<strtotime('7:20')) {$set+=4.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:09')&&TIME<strtotime('7:20')) {$set+=4.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:20')) {$set+=4.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:51')&&TIME<strtotime('7:20')) {$set+=4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:42')&&TIME<strtotime('7:20')) {$set+=3.7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:33')&&TIME<strtotime('7:20')) {$set+=3.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:24')&&TIME<strtotime('7:20')) {$set+=3.1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:15')&&TIME<strtotime('7:20')) {$set+=2.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:06')&&TIME<strtotime('7:20')) {$set+=2.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:57')&&TIME<strtotime('7:20')) {$set+=2.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:48')&&TIME<strtotime('7:20')) {$set+=1.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:39')&&TIME<strtotime('7:20')) {$set+=1.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:30')&&TIME<strtotime('7:20')) {$set+=1.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:21')&&TIME<strtotime('7:20')) {$set+=1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:12')&&TIME<strtotime('7:20')) {$set+=0.7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:03')&&TIME<strtotime('7:20')) {$set+=0.4;$m.=' '.__LINE__;}
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
$hum=60;
if($dow==0||$dow==6) {
	if (TIME>=strtotime('7:30')&&TIME<strtotime('7:20')) {$hum-=30;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('7:21')&&TIME<strtotime('7:20')) {$hum-=28;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('7:12')&&TIME<strtotime('7:20')) {$hum-=26;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('7:03')&&TIME<strtotime('7:20')) {$hum-=24;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:54')&&TIME<strtotime('7:20')) {$hum-=22;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:45')&&TIME<strtotime('7:20')) {$hum-=20;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:36')&&TIME<strtotime('7:20')) {$hum-=18;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:27')&&TIME<strtotime('7:20')) {$hum-=16;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:18')&&TIME<strtotime('7:20')) {$hum-=14;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:09')&&TIME<strtotime('7:20')) {$hum-=12;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:20')) {$hum-=10;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:51')&&TIME<strtotime('7:20')) {$hum-=8;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:42')&&TIME<strtotime('7:20')) {$hum-=6;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:33')&&TIME<strtotime('7:20')) {$hum-=4;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:24')&&TIME<strtotime('7:20')) {$hum-=2;$m.=' '.__LINE__;}
} elseif($dow==2||$dow==5)  {
	if (TIME>=strtotime('6:30')&&TIME<strtotime('7:00')) {$hum-=30;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:21')&&TIME<strtotime('7:00')) {$hum-=28;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:12')&&TIME<strtotime('7:00')) {$hum-=26;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:03')&&TIME<strtotime('7:00')) {$hum-=24;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:54')&&TIME<strtotime('7:00')) {$hum-=22;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:45')&&TIME<strtotime('7:00')) {$hum-=20;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:36')&&TIME<strtotime('7:00')) {$hum-=18;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:27')&&TIME<strtotime('7:00')) {$hum-=16;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:18')&&TIME<strtotime('7:00')) {$hum-=14;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:09')&&TIME<strtotime('7:00')) {$hum-=12;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:00')&&TIME<strtotime('7:00')) {$hum-=10;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:51')&&TIME<strtotime('7:00')) {$hum-=8;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:42')&&TIME<strtotime('7:00')) {$hum-=6;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:33')&&TIME<strtotime('7:00')) {$hum-=4;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:24')&&TIME<strtotime('7:00')) {$hum-=2;$m.=' '.__LINE__;}
} else {
	if (TIME>=strtotime('6:45')&&TIME<strtotime('7:05')) {$hum-=30;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:36')&&TIME<strtotime('7:05')) {$hum-=28;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:27')&&TIME<strtotime('7:05')) {$hum-=26;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:18')&&TIME<strtotime('7:05')) {$hum-=24;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:09')&&TIME<strtotime('7:05')) {$hum-=22;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:05')) {$hum-=20;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:51')&&TIME<strtotime('7:05')) {$hum-=18;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:42')&&TIME<strtotime('7:05')) {$hum-=16;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:33')&&TIME<strtotime('7:05')) {$hum-=14;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:24')&&TIME<strtotime('7:05')) {$hum-=12;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:15')&&TIME<strtotime('7:05')) {$hum-=10;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:06')&&TIME<strtotime('7:05')) {$hum-=8;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:57')&&TIME<strtotime('7:05')) {$hum-=6;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:48')&&TIME<strtotime('7:05')) {$hum-=4;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:39')&&TIME<strtotime('7:05')) {$hum-=2;$m.=' '.__LINE__;}
}
$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];

if ($d['badkamer_temp']['m']>=$hum&&($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')<60))) {$l=1;$m.=' + '.__LINE__.' badkamer_hum>='.$hum;}
if ($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>60) {
	if ($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Closed'&&$d['kamer_temp']['m']>=60)) {$l=1;$m.=' + '.__LINE__.' kamer_hum>=55';}
	if ($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&$d['raamwaskamer']['s']=='Closed'&&$d['waskamer_temp']['m']>=60)) {$l=1;$m.=' + '.__LINE__.' waskamer_hum>=55';}
	if ($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Closed'&&$d['alex_temp']['m']>=60)) {$l=1;$m.=' + '.__LINE__.' alex_hum>=55';}
}
if ($difbadkamer<=-0.6) {$l=3;$m.=' + '.__LINE__.' difbadkamer<=-0.6';}
elseif ($difbadkamer<=-0.3) {$l=2;$m.=' + '.__LINE__.' difbadkamer<=-0.3';}
elseif ($difbadkamer< 0) {$l=1;$m.=' + '.__LINE__.' difbadkamer<0';}
else {
	if ($difbadkamer>0.2&&$d['badkamer_set']['s']>19) {
		if ($d['badkamer_set']['s']>15.2) {
			store('badkamer_set', 15.2, basename(__FILE__).':'.__LINE__);
		
		}
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
}

if ($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>60) {
	if ($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur kamer en raam kamer open';}
	if ($d['deurwaskamer']['s']=='Open'&&$d['raamwaskamer']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur waskamer en raam waskamer open';}
	if ($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur kamer Alex en raam Alex kamer open';}
}
if ($d['badkamer_set']['m']==0&&$l>2&&$d['lichtbadkamer']['s']==0) {$l=2;$m.=' + '.__LINE__.' auto';}
elseif ($d['badkamer_set']['m']==1&&$l>2&&$d['lichtbadkamer']['s']==0) {$l=2;$m.=' + '.__LINE__.' slow';}


if ($d['Weg']['s']>=3) {$l=0;$m.=' + '.__LINE__.' Weg';}

//if (strlen($m)>5) lg('['.$m.']');

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

if ($d['wasdroger']['s']=='On') {
	if ($d['waskamer_temp']['s']<16.5&&($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<60))) {
		if ($d['waskamervuur1']['s']=='Off'&&past('wakamervuur1')>295) sw('waskamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['waskamervuur2']['s']=='On'&&past('wakamervuur1')>295) sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['waskamervuur1']['s']=='On'&&past('wakamervuur1')>295) sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	}
} else {
	if ($d['waskamervuur2']['s']=='On'&&past('wakamervuur1')>295) sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['waskamervuur1']['s']=='On'&&past('wakamervuur1')>295) sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
}
