<?php
$d=fetchdata($fetch);
$user=basename(__FILE__);
$Setkamer=4;
$Setwaskamer=4;
$Setalex=4;
$time=time();
if ($d['Weg']['s']<=2&&$d['heating']['s']>=1) {
	if ($d['kamer_set']['m']==0) {
		if (
				($d['raamkamer']['s']=='Closed'||$d['RkamerR']['s']==100)
			&&
				(past('raamkamer')>2700||$time>strtotime('19:00'))
			&&
				(
					($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<900))
				||
					(
						($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<900)||$d['raamalex']['s']=='Closed'||$d['Ralex']['s']==100)
					&&
						($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<900)||$d['raamwaskamer']['s']=='Closed'||$d['Rwaskamer']['s']==100)
					&&	$d['raamhall']['s']=='Closed'
					)
				)
		) {
			$Setkamer=12;
		}
	} else $Setkamer=$d['kamer_set']['s'];
	if ($d['alex_set']['m']==0) {
		if (
				($d['raamalex']['s']=='Closed'||$d['Ralex']['s']==100)
			&&
				(past('raamalex')>2700|| $time>strtotime('19:00'))
			&&
				(
					($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<900))
				||
					(
						($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<900)||$d['raamkamer']['s']=='Closed'||$d['RkamerR']['s']==100)
					&&
						($d['deurwaskamer']['s']=='Closed'||($d['deurwaskamer']['s']=='Open'&&past('deurwaskamer')<900)||$d['raamwaskamer']['s']=='Closed'||$d['Rwaskamer']['s']==100)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$Setalex=12;
		}
	} else $Setalex=$d['alex_set']['s'];
} elseif ($d['Weg']['s']>=3) {
	$Setkamer=5;
	$Setwaskamer=5;
	$Setalex=5;
} elseif ($d['heating']['s']>=1) {
	$Setkamer=10;
	$Setwaskamer=10;
	$Setalex=10;
}
if ($d['kamer_set']['m']==1) $Setkamer=$d['kamer_set']['s'];
if ($d['alex_set']['m']==1) $Setalex=$d['alex_set']['s'];

if ($d['kamer_set']['s']!=$Setkamer) {
	store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
	$d['kamer_set']['s']=$Setkamer;
}
if ($d['alex_set']['s']!=$Setalex) {
	store('alex_set', $Setalex, basename(__FILE__).':'.__LINE__);
	$alex_set=$Setalex;
	$d['alex_set']['s']=$Setalex;
}
if ($d['living_set']['m']==0) {
	$Setliving=14;
	if ($d['buiten_temp']['s']<20&&$d['minmaxtemp']['m']<22&&$d['heating']['s']>=1/*&&$d['raamliving']['s']=='Closed'&&$d['deurinkom']['s']=='Closed'&&$d['deurgarage']['s']=='Closed'*/) {
		if ($d['Weg']['s']==0) {
			$Setliving=18;
			if ($dow==1&&$time>=strtotime('15:00')&&$time<strtotime('19:00')) $Setliving=19;
			elseif ($dow==2&&$time>=strtotime('15:00')&&$time<strtotime('19:00')) $Setliving=19;
			elseif ($dow==3&&$time>=strtotime('12:00')&&$time<strtotime('19:00')) $Setliving=19;
			elseif ($dow==4&&$time>=strtotime('15:00')&&$time<strtotime('19:00')) $Setliving=19;
			elseif ($dow==5&&$time>=strtotime('12:00')&&$time<strtotime('19:00')) $Setliving=19;
			elseif ($dow==6&&$time>=strtotime('8:00')&&$time<strtotime('19:00')) $Setliving=19;
			elseif ($dow==0&&$time>=strtotime('8:00')&&$time<strtotime('19:00')) $Setliving=19;
			if ($d['pirliving']['s']=='Off'&&$time<mget('living')-3600) $Setliving-=1;
		} elseif ($d['Weg']['s']==1) {
			$target2=15;
			$target=18;
			$factor=(($Setliving-$d['buiten_temp']['s'])/2)*(($d['living_temp']['s']-$d['buiten_temp']['s'])/2)*120;
			for ($x=0;$x<=5;$x+=0.1) {
				$t2=(int)($t-($factor*$x)-300);
				if ($time>=$t2&&$time<=strtotime('7:45')) {
					$target2=round($target-$x, 1);
					lg(number_format($x,1,'.','').' factor='.$factor.',t2='.date('H:i:s', $t2).' > '.number_format($target2,1,'.',''));
					break;
				}
			}
			if($target2>$Setliving) $Setliving=$target2;
		}
	}
	if ($d['Weg']['s']==2&&$d['heating']['s']>=1) {
		$Setliving=15;
	} elseif ($d['Weg']['s']==3&&$d['heating']['s']>=1) {
		$Setliving=10;
	}
	if ($d['living_set']['s']!=$Setliving) {
		store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
		$living_set=$Setliving;
		$d['living_set']['s']=$Setliving;
	}
}
require('_Rolluiken_Heating.php');
$bigdif=100;
$difgas=100;
if ($d['heating']['s']==1) require ('_TC_heating_airco.php');
elseif ($d['heating']['s']==2) require ('_TC_heating_gasairco.php');
elseif ($d['heating']['s']==3) require ('_TC_heating_gas.php');
require('_TC_badkamer.php');

