<?php
$m='';$m2='';
$l=0;$m.=' base';
if ($d['badkamer_set']['m']==0) {$set=15;$m2.=__LINE__.' ';}
else {$set=$d['badkamer_set']['s'];$m2.=__LINE__.' ';}
if ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>57&&($d['raamkamer']['s']=='Open'||$d['raamwaskamer']['s']=='Open'||$d['raamalex']['s']=='Open')) {
	$set=5;$m2.=__LINE__.' ';
} elseif ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']>0) {
	if (past('badkamer_set')>14400&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']>16) {
			$set=15;$m2.=__LINE__.' ';
			if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('badkamer_set')>14400&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=15) || ($d['Weg']['s']>=2&&$d['badkamer_set']['s']!=15)) {
		store('badkamer_set', 16, basename(__FILE__).':'.__LINE__);
		$set=15;$m2.=__LINE__.' ';
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']>=1) {
	if ($d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<20&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']!=15) {
			$set=15;$m2.=__LINE__.' ';
		}
	}
	if($dow==0||$dow==6) {
		if (TIME>=strtotime('7:30')&&TIME<strtotime('7:45')) {$set+=8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('7:20')&&TIME<strtotime('7:45')) {$set+=7.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('7:10')&&TIME<strtotime('7:45')) {$set+=7.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('7:00')&&TIME<strtotime('7:45')) {$set+=7.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:50')&&TIME<strtotime('7:45')) {$set+=7.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:40')&&TIME<strtotime('7:45')) {$set+=7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:30')&&TIME<strtotime('7:45')) {$set+=6.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:20')&&TIME<strtotime('7:45')) {$set+=6.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:10')&&TIME<strtotime('7:45')) {$set+=6.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:45')) {$set+=6.1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:50')&&TIME<strtotime('7:45')) {$set+=5.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:40')&&TIME<strtotime('7:45')) {$set+=5.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:30')&&TIME<strtotime('7:45')) {$set+=5.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:20')&&TIME<strtotime('7:45')) {$set+=4.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:10')&&TIME<strtotime('7:45')) {$set+=4.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:00')&&TIME<strtotime('7:45')) {$set+=4.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:50')&&TIME<strtotime('7:45')) {$set+=4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:40')&&TIME<strtotime('7:45')) {$set+=3.7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:30')&&TIME<strtotime('7:45')) {$set+=3.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:20')&&TIME<strtotime('7:45')) {$set+=3.1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:10')&&TIME<strtotime('7:45')) {$set+=2.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:00')&&TIME<strtotime('7:45')) {$set+=2.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:50')&&TIME<strtotime('7:45')) {$set+=2.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:40')&&TIME<strtotime('7:45')) {$set+=1.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:30')&&TIME<strtotime('7:45')) {$set+=1.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:20')&&TIME<strtotime('7:45')) {$set+=1.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:10')&&TIME<strtotime('7:45')) {$set+=1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:00')&&TIME<strtotime('7:45')) {$set+=0.5;$m.=' '.__LINE__;}
	} elseif($dow==2||$dow==5) {
		if (TIME>=strtotime('6:45')&&TIME<strtotime('7:00')) {$set+=8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:35')&&TIME<strtotime('7:00')) {$set+=7.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:25')&&TIME<strtotime('7:00')) {$set+=7.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:15')&&TIME<strtotime('7:00')) {$set+=7.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:05')&&TIME<strtotime('7:00')) {$set+=7.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:55')&&TIME<strtotime('7:00')) {$set+=7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:45')&&TIME<strtotime('7:00')) {$set+=6.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:35')&&TIME<strtotime('7:00')) {$set+=6.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:25')&&TIME<strtotime('7:00')) {$set+=6.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:15')&&TIME<strtotime('7:00')) {$set+=6.1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:05')&&TIME<strtotime('7:00')) {$set+=5.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:55')&&TIME<strtotime('7:00')) {$set+=5.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:45')&&TIME<strtotime('7:00')) {$set+=5.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:35')&&TIME<strtotime('7:00')) {$set+=4.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:25')&&TIME<strtotime('7:00')) {$set+=4.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:15')&&TIME<strtotime('7:00')) {$set+=4.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:05')&&TIME<strtotime('7:00')) {$set+=4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:55')&&TIME<strtotime('7:00')) {$set+=3.7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:45')&&TIME<strtotime('7:00')) {$set+=3.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:35')&&TIME<strtotime('7:00')) {$set+=3.1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:25')&&TIME<strtotime('7:00')) {$set+=2.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:15')&&TIME<strtotime('7:00')) {$set+=2.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:05')&&TIME<strtotime('7:00')) {$set+=2.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('2:55')&&TIME<strtotime('7:00')) {$set+=1.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('2:45')&&TIME<strtotime('7:00')) {$set+=1.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('2:35')&&TIME<strtotime('7:00')) {$set+=1.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('2:25')&&TIME<strtotime('7:00')) {$set+=1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('2:15')&&TIME<strtotime('7:00')) {$set+=0.5;$m.=' '.__LINE__;}
	} else {
		if (TIME>=strtotime('7:00')&&TIME<strtotime('7:15')) {$set+=8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:50')&&TIME<strtotime('7:15')) {$set+=7.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:40')&&TIME<strtotime('7:15')) {$set+=7.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:30')&&TIME<strtotime('7:15')) {$set+=7.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:20')&&TIME<strtotime('7:15')) {$set+=7.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:10')&&TIME<strtotime('7:15')) {$set+=7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:15')) {$set+=6.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:50')&&TIME<strtotime('7:15')) {$set+=6.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:40')&&TIME<strtotime('7:15')) {$set+=6.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:30')&&TIME<strtotime('7:15')) {$set+=6.1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:20')&&TIME<strtotime('7:15')) {$set+=5.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:10')&&TIME<strtotime('7:15')) {$set+=5.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('5:00')&&TIME<strtotime('7:15')) {$set+=5.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:50')&&TIME<strtotime('7:15')) {$set+=4.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:40')&&TIME<strtotime('7:15')) {$set+=4.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:30')&&TIME<strtotime('7:15')) {$set+=4.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:20')&&TIME<strtotime('7:15')) {$set+=4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:10')&&TIME<strtotime('7:15')) {$set+=3.7;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('4:00')&&TIME<strtotime('7:15')) {$set+=3.4;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:50')&&TIME<strtotime('7:15')) {$set+=3.1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:40')&&TIME<strtotime('7:15')) {$set+=2.8;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:30')&&TIME<strtotime('7:15')) {$set+=2.5;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:20')&&TIME<strtotime('7:15')) {$set+=2.2;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:10')&&TIME<strtotime('7:15')) {$set+=1.9;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('3:00')&&TIME<strtotime('7:15')) {$set+=1.6;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('2:50')&&TIME<strtotime('7:15')) {$set+=1.3;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('2:40')&&TIME<strtotime('7:15')) {$set+=1;$m.=' '.__LINE__;}
		elseif (TIME>=strtotime('2:30')&&TIME<strtotime('7:15')) {$set+=0.5;$m.=' '.__LINE__;}
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
$hum=65;
if($dow==0||$dow==6) {
	if (TIME>=strtotime('7:30')&&TIME<strtotime('7:45')) {$hum-=30;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('7:20')&&TIME<strtotime('7:45')) {$hum-=28;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('7:10')&&TIME<strtotime('7:45')) {$hum-=26;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('7:00')&&TIME<strtotime('7:45')) {$hum-=24;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:50')&&TIME<strtotime('7:45')) {$hum-=22;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:40')&&TIME<strtotime('7:45')) {$hum-=20;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:30')&&TIME<strtotime('7:45')) {$hum-=18;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:20')&&TIME<strtotime('7:45')) {$hum-=16;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:10')&&TIME<strtotime('7:45')) {$hum-=14;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:45')) {$hum-=12;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:50')&&TIME<strtotime('7:45')) {$hum-=10;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:40')&&TIME<strtotime('7:45')) {$hum-=8;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:30')&&TIME<strtotime('7:45')) {$hum-=6;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:20')&&TIME<strtotime('7:45')) {$hum-=4;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:10')&&TIME<strtotime('7:45')) {$hum-=2;$m.=' '.__LINE__;}
} elseif($dow==2||$dow==5)  {
	if (TIME>=strtotime('6:45')&&TIME<strtotime('7:00')) {$hum-=30;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:35')&&TIME<strtotime('7:00')) {$hum-=28;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:25')&&TIME<strtotime('7:00')) {$hum-=26;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:15')&&TIME<strtotime('7:00')) {$hum-=24;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:05')&&TIME<strtotime('7:00')) {$hum-=22;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:55')&&TIME<strtotime('7:00')) {$hum-=20;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:45')&&TIME<strtotime('7:00')) {$hum-=18;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:35')&&TIME<strtotime('7:00')) {$hum-=16;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:25')&&TIME<strtotime('7:00')) {$hum-=14;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:15')&&TIME<strtotime('7:00')) {$hum-=12;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:05')&&TIME<strtotime('7:00')) {$hum-=10;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:55')&&TIME<strtotime('7:00')) {$hum-=8;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:45')&&TIME<strtotime('7:00')) {$hum-=6;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:35')&&TIME<strtotime('7:00')) {$hum-=4;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:25')&&TIME<strtotime('7:00')) {$hum-=2;$m.=' '.__LINE__;}
} else {
	if (TIME>=strtotime('7:00')&&TIME<strtotime('7:15')) {$hum-=30;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:50')&&TIME<strtotime('7:15')) {$hum-=28;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:40')&&TIME<strtotime('7:15')) {$hum-=26;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:30')&&TIME<strtotime('7:15')) {$hum-=24;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:20')&&TIME<strtotime('7:15')) {$hum-=22;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:10')&&TIME<strtotime('7:15')) {$hum-=20;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('6:00')&&TIME<strtotime('7:15')) {$hum-=18;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:50')&&TIME<strtotime('7:15')) {$hum-=16;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:40')&&TIME<strtotime('7:15')) {$hum-=14;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:30')&&TIME<strtotime('7:15')) {$hum-=12;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:20')&&TIME<strtotime('7:15')) {$hum-=10;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:10')&&TIME<strtotime('7:15')) {$hum-=8;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('5:00')&&TIME<strtotime('7:15')) {$hum-=6;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:50')&&TIME<strtotime('7:15')) {$hum-=4;$m.=' '.__LINE__;}
	elseif (TIME>=strtotime('4:40')&&TIME<strtotime('7:15')) {$hum-=2;$m.=' '.__LINE__;}
}
$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
if ($d['badkamer_temp']['s']<16) $hum-=10;

if ($d['badkamer_temp']['m']>$hum&&($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')<60))) {$l=1;$m.=' + '.__LINE__.' badkamer_hum>='.$hum;}
if ($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>60) {
	if ($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Closed'&&$d['kamer_temp']['m']>65) {$l=1;$m.=' + '.__LINE__.' kamer_hum>60';}
	if ($d['deurwaskamer']['s']=='Open'&&$d['raamwaskamer']['s']=='Closed'&&$d['waskamer_temp']['m']>65) {$l=1;$m.=' + '.__LINE__.' waskamer_hum>60';}
	if ($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Closed'&&$d['alex_temp']['m']>65) {$l=1;$m.=' + '.__LINE__.' alex_hum>60';}
}
if ($difbadkamer<=-0.6) {$l=2;$m.=' + '.__LINE__.' difbadkamer<=-0.6';}
elseif ($difbadkamer<=-0.3) {$l=2;$m.=' + '.__LINE__.' difbadkamer<=-0.3';}
elseif ($difbadkamer< 0) {$l=1;$m.=' + '.__LINE__.' difbadkamer<0';}
else {
	if ($difbadkamer>0.2&&$d['badkamer_set']['s']>19) {
		if ($d['badkamer_set']['s']>16) store('badkamer_set', 16, basename(__FILE__).':'.__LINE__);
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
if ($d['luchtdroger']['m']!='Auto') {$l=$d['luchtdroger']['m'];$m.=' + '.__LINE__.' Fixed';}
if ($l==0) {
	if ($d['luchtdroger']['s']=='On') {
		if ($d['luchtdroger1']['s']=='On') sw('luchtdroger1', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger2']['s']=='On') sw('luchtdroger2', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger1']['s']=='Off'&&$d['luchtdroger2']['s']=='Off'&&past('luchtdroger')>595&&past('luchtdroger1')>115&&past('luchtdroger2')>115) sw('luchtdroger', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
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
	if ($d['waskamer_temp']['s']<17.5&&($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<60))) {
		if ($d['waskamervuur1']['s']=='Off'&&past('waskamervuur1')>595) sw('waskamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['waskamervuur2']['s']=='On'&&past('waskamervuur1')>595) sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['waskamervuur1']['s']=='On'&&past('waskamervuur1')>595) sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['waskamer_temp']['m']<50&&past('wasdroger')>595) sw('wasdroger', 'Off', basename(__FILE__).':'.__LINE__);
} else {
	if ($d['waskamervuur2']['s']=='On'&&past('waskamervuur1')>595) sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['waskamervuur1']['s']=='On'&&past('waskamervuur1')>595) sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['waskamer_temp']['m']>65&&$d['raamwaskamer']['s']=='Closed'&&$d['deurwaskamer']['s']=='Closed'&&past('wasdroger')>595) sw('wasdroger', 'Off', basename(__FILE__).':'.__LINE__);
}
