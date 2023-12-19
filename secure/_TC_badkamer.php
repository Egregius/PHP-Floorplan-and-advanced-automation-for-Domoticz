<?php
$m='';$m2='';
$l=0;
if ($d['badkamer_set']['m']==0) {$set=13;$m2.=__LINE__.' ';}
else {$set=$d['badkamer_set']['s'];$m2.=__LINE__.' ';}
$pastdeurbadkamer=past('deurbadkamer');
if ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer>57&&($d['raamkamer']['s']=='Open'||$d['raamwaskamer']['s']=='Open'||$d['raamalex']['s']=='Open')) {
	$set=5;$m2.=__LINE__.' ';
} elseif ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']>0) {
	if (past('badkamer_set')>14400&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']>13) {
			$set=13;$m2.=__LINE__.' ';
			if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('badkamer_set')>14400&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=13) || ($d['Weg']['s']>=2&&$d['badkamer_set']['s']!=13)) {
		store('badkamer_set', 13, basename(__FILE__).':'.__LINE__);
		$set=13;$m2.=__LINE__.' ';
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']>=1) {
	if ($d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<20&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']!=13) {$set=13;$m2.=__LINE__.' ';}
	}
	$loop=true;
	if ($d['buiten_temp']['s']>-30&&$d['buiten_temp']['s']<50) $factor=(20-$d['buiten_temp']['s'])*100; else $factor=1000;
	for ($x=0;$x<=10;$x+=0.1) {
		if ($loop==true) {
			$t2=$t-($factor*$x);
			if ($time>=$t2&&$time<$t+900) {
				$set=round(22-$x, 1);
				$loop=false;
			}
		} else break;
	}
	
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']<0) {
	if ($d['badkamer_set']['s']!=5) {
		store('badkamer_set', 5, basename(__FILE__).':'.__LINE__);
		$d['badkamer_set']['s']=5;
	}
}
if (isset($set)&&$d['heating']['s']>=0) {
	if ($set!=$d['badkamer_set']['s']) store('badkamer_set', $set, basename(__FILE__).':'.__LINE__.' '.$m2);
	$d['badkamer_set']['s']=$set;
}
if ($d['heating']['s']>=0) {
	if ($time>=$t-7200&&$time<$t+900) $hum=60;
	else $hum=80;
	$loop=true;
	for ($x=0;$x<=35;$x+=1) {
		if ($loop==true) {
			$t2=$t-(360*$x);
			if ($time>=$t2&&$time<$t+900) {
				$hum=35+$x;
				$loop=false;
			}
		} else break;
	}
	if ($d['badkamer_temp']['s']<14) $hum-=30;
	elseif ($d['badkamer_temp']['s']<14.5) $hum-=25;
	elseif ($d['badkamer_temp']['s']<15) $hum-=20;
	elseif ($d['badkamer_temp']['s']<15.5) $hum-=15;
	elseif ($d['badkamer_temp']['s']<16) $hum-=10;
	elseif ($d['badkamer_temp']['s']<16.5) $hum-=5;
	if ($d['badkamer_temp']['m']>$hum&&($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer<90))) {$l=1;$m.=' + '.__LINE__.' badkamer_hum>='.$hum;}
//	if ($d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer>60) {
//		if ($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Closed'&&$d['kamer_temp']['m']>70) {$l=1;$m.=' + '.__LINE__.' kamer_hum>60';}
//		if ($d['deurwaskamer']['s']=='Open'&&$d['raamwaskamer']['s']=='Closed'&&$d['waskamer_temp']['m']>70) {$l=1;$m.=' + '.__LINE__.' waskamer_hum>60';}
//		if ($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Closed'&&$d['alex_temp']['m']>70) {$l=1;$m.=' + '.__LINE__.' alex_hum>60';}
//	}
}
$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
if ($difbadkamer<=-0.6) {$l=3;$m.=' + '.__LINE__.' difbadkamer<=-0.6';}
elseif ($difbadkamer<=-0.3) {$l=2;$m.=' + '.__LINE__.' difbadkamer<=-0.3';}
elseif ($difbadkamer< 0) {$l=1;$m.=' + '.__LINE__.' difbadkamer<0';}
else {
	if ($difbadkamer>0.5&&$d['badkamer_set']['s']>19) {
		if ($d['badkamer_set']['s']>15) store('badkamer_set', 15, basename(__FILE__).':'.__LINE__.' $difbadkamer='.$difbadkamer);
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
}
if ($d['deurbadkamer']['s']=='Open'&&$pastdeurbadkamer>60) {
	if ($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur kamer en raam kamer open';}
	if ($d['deurwaskamer']['s']=='Open'&&$d['raamwaskamer']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur waskamer en raam waskamer open';}
	if ($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur kamer Alex en raam Alex kamer open';}
}

if ($d['badkamer_set']['m']==1&&$l>2&&$d['lichtbadkamer']['s']==0) {$l=2;$m.=' + '.__LINE__.' slow';}
if ($d['Weg']['s']>=3) {$l=0;$m.=' + '.__LINE__.' Weg';}
if ($d['luchtdroger']['m']!='Auto') {$l=$d['luchtdroger']['m'];$m.=' + '.__LINE__.' Fixed';}

if ($l==0) {
	if ($d['luchtdroger']['s']=='On') {
		if ($d['luchtdroger1']['s']=='On'&&past('luchtdroger1')>895) sw('luchtdroger1', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger2']['s']=='On') sw('luchtdroger2', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger1']['s']=='Off'&&$d['luchtdroger2']['s']=='Off'&&past('luchtdroger')>75&&past('luchtdroger1')>95&&past('luchtdroger2')>115) sw('luchtdroger', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
	}
} elseif ($l==1) {
	if ($d['luchtdroger']['s']=='Off'/*&&$time>=strtotime('3:00')&&$time<=strtotime('18:00')*/) sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	else {
		if ($d['luchtdroger1']['s']=='Off'/*&&$time>=strtotime('3:00')&&$time<=strtotime('18:00')*/) sw('luchtdroger1', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger2']['s']=='On'&&past('luchtdroger2')>95) sw('luchtdroger2', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
	}
} elseif ($l==2) {
	if ($d['luchtdroger']['s']=='Off'/*&&$time>=strtotime('3:00')&&$time<=strtotime('18:00')*/) sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	else {
		if ($d['luchtdroger1']['s']=='On'&&past('luchtdroger1')>175) sw('luchtdroger1', 'Off', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger2']['s']=='Off'/*&&$time>=strtotime('3:00')&&$time<=strtotime('18:00')*/) sw('luchtdroger2', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	}
} elseif ($l==3) {
	if ($d['luchtdroger']['s']=='Off'/*&&$time>=strtotime('3:00')&&$time<=strtotime('18:00')*/) sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	else {
		if ($d['luchtdroger1']['s']=='Off'/*&&$time>=strtotime('3:00')&&$time<=strtotime('18:00')*/) sw('luchtdroger1', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
		if ($d['luchtdroger2']['s']=='Off'/*&&$time>=strtotime('3:00')&&$time<=strtotime('18:00')*/) sw('luchtdroger2', 'On', basename(__FILE__).':'.__LINE__.' -> '.$m);
	}
} 
if ($d['luchtdroger']['s']=='Off') {
	if ($d['luchtdroger1']['s']=='On') store('luchtdroger1', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['luchtdroger2']['s']=='On') store('luchtdroger2', 'Off', basename(__FILE__).':'.__LINE__);
}
if ($d['wasdroger']['s']=='On') {
/*	if ($d['waskamer_temp']['s']<18&&($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<60))) {
		if ($d['waskamervuur1']['s']=='Off'&&past('waskamervuur1')>595) sw('waskamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['waskamervuur2']['s']=='On'&&past('waskamervuur1')>595) sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['waskamervuur1']['s']=='On'&&past('waskamervuur1')>595) sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	}*/
	if (($d['waskamer_temp']['m']<65&&past('wasdroger')>3595)||$d['raamwaskamer']['s']=='Open') sw('wasdroger', 'Off', basename(__FILE__).':'.__LINE__);
} else {
	if ($d['waskamer_temp']['m']>80&&$d['raamwaskamer']['s']=='Closed'&&$d['deurwaskamer']['s']=='Closed'&&past('wasdroger')>595) sw('wasdroger', 'On', basename(__FILE__).':'.__LINE__);
//	if ($d['waskamervuur2']['s']=='On') sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
//	if ($d['waskamervuur1']['s']=='On') sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
}
