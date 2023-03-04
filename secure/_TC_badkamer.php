<?php
$m='';$m2='';
$l=0;$m.=' base';
if ($d['badkamer_set']['m']==0) {$set=12;$m2.=__LINE__.' ';}
else {$set=$d['badkamer_set']['s'];$m2.=__LINE__.' ';}

if ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>57&&($d['raamkamer']['s']=='Open'||$d['raamwaskamer']['s']=='Open'||$d['raamalex']['s']=='Open')) {
	$set=5;$m2.=__LINE__.' ';
} elseif ($d['badkamer_set']['m']==0&&$d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']>0) {
	if (past('badkamer_set')>14400&&$d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<21&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']>12) {
			$set=12;$m2.=__LINE__.' ';
			if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
		}
	} elseif (past('badkamer_set')>14400&&($d['lichtbadkamer']['s']==0&&$d['badkamer_set']['s']!=12) || ($d['Weg']['s']>=2&&$d['badkamer_set']['s']!=12)) {
		store('badkamer_set', 12, basename(__FILE__).':'.__LINE__);
		$set=12;$m2.=__LINE__.' ';
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
} elseif ($d['deurbadkamer']['s']=='Closed'&&$d['badkamer_set']['m']==0&&$d['heating']['s']>=1) {
	if ($d['lichtbadkamer']['s']==0&&$d['buiten_temp']['s']<20&&$d['Weg']['s']<2) {
		if ($d['badkamer_set']['s']!=12) {$set=12;$m2.=__LINE__.' ';}
	}
	$loop=true;
	for ($x=0;$x<=11;$x+=0.1) {
		if ($loop==true) {
			$t2=$t-(1500*$x);
			if (TIME>=$t2&&TIME<$t+900) {
				$set=round(23-$x, 1);
				$loop=false;
			}
		} else break;
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
$loop=true;
for ($x=0;$x<=40;$x+=1) {
	if ($loop==true) {
		$t2=$t-(50*$x);
		if (TIME>=$t2&&TIME<$t+900) {
			$hum=25+$x;
			$loop=false;
		}
	} else break;
}

$difbadkamer=$d['badkamer_temp']['s']-$d['badkamer_set']['s'];
if ($d['badkamer_temp']['s']<16) $hum-=10;

if ($d['badkamer_temp']['m']>$hum&&($d['deurbadkamer']['s']=='Closed'||($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')<60))) {$l=1;$m.=' + '.__LINE__.' badkamer_hum>='.$hum;}
if ($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>60) {
	if ($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Closed'&&$d['kamer_temp']['m']>65) {$l=1;$m.=' + '.__LINE__.' kamer_hum>60';}
	if ($d['deurwaskamer']['s']=='Open'&&$d['raamwaskamer']['s']=='Closed'&&$d['waskamer_temp']['m']>65) {$l=1;$m.=' + '.__LINE__.' waskamer_hum>60';}
	if ($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Closed'&&$d['alex_temp']['m']>65) {$l=1;$m.=' + '.__LINE__.' alex_hum>60';}
}
if ($difbadkamer<=-0.6) {$l=3;$m.=' + '.__LINE__.' difbadkamer<=-0.6';}
elseif ($difbadkamer<=-0.3) {$l=2;$m.=' + '.__LINE__.' difbadkamer<=-0.3';}
elseif ($difbadkamer< 0) {$l=1;$m.=' + '.__LINE__.' difbadkamer<0';}
else {
	if ($difbadkamer>0.5&&$d['badkamer_set']['s']>19) {
		if ($d['badkamer_set']['s']>15) store('badkamer_set', 15, basename(__FILE__).':'.__LINE__);
		if ($d['badkamer_set']['m']>0) storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
	}
}
if ($d['deurbadkamer']['s']=='Open'&&past('deurbadkamer')>60) {
	if ($d['deurkamer']['s']=='Open'&&$d['raamkamer']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur kamer en raam kamer open';}
	if ($d['deurwaskamer']['s']=='Open'&&$d['raamwaskamer']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur waskamer en raam waskamer open';}
	if ($d['deuralex']['s']=='Open'&&$d['raamalex']['s']=='Open') {$l=0;$m.=' + '.__LINE__.' deur badkamer, deur kamer Alex en raam Alex kamer open';}
}
//if ($d['badkamer_set']['m']==0&&$l>2&&$d['lichtbadkamer']['s']==0) {$l=2;$m.=' + '.__LINE__.' auto';}
//else
if ($d['badkamer_set']['m']==1&&$l>2&&$d['lichtbadkamer']['s']==0) {$l=2;$m.=' + '.__LINE__.' slow';}
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
	if ($d['waskamer_temp']['s']<18&&($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<60))) {
		if ($d['waskamervuur1']['s']=='Off'&&past('waskamervuur1')>595) sw('waskamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	} else {
		if ($d['waskamervuur2']['s']=='On'&&past('waskamervuur1')>595) sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
		if ($d['waskamervuur1']['s']=='On'&&past('waskamervuur1')>595) sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['waskamer_temp']['m']<58&&past('wasdroger')>1795) sw('wasdroger', 'Off', basename(__FILE__).':'.__LINE__);
} else {
	if ($d['waskamer_temp']['m']>65&&$d['raamwaskamer']['s']=='Closed'&&$d['deurwaskamer']['s']=='Closed'&&past('wasdroger')>595) sw('wasdroger', 'On', basename(__FILE__).':'.__LINE__);
	if ($d['waskamervuur2']['s']=='On') sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['waskamervuur1']['s']=='On') sw('waskamervuur1', 'Off', basename(__FILE__).':'.__LINE__);
}
