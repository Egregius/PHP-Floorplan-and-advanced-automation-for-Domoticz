<?php
$user='heating';
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
			$Setkamer=13;
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
			$Setalex=13;
		}
	} else $Setalex=$d['alex_set']['s'];
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
$Setliving=15;
if ($d['Weg']['s']<=2&&$d['heating']['s']>=3) $Setliving=17;
if ($d['living_set']['m']==0) {
	if ($d['buiten_temp']['s']<20&&$d['minmaxtemp']['m']<22&&$d['heating']['s']>=1/*&&$d['raamliving']['s']=='Closed'&&$d['deurinkom']['s']=='Closed'&&$d['deurgarage']['s']=='Closed'*/) {
		if ($d['Weg']['s']<2) {
			$base=20;
			$loop=true;
			if ($d['buiten_temp']['s']>-30&&$d['buiten_temp']['s']<50) $factor=(20-$d['buiten_temp']['s'])*100; else $factor=1000;
			for ($x=0;$x<=5;$x+=0.1) {
				if ($loop==true) {
					if ($d['heating']['s']>=2) $t2=$t-($factor*$x);
					else $t2=$t;
					if ($time>=$t2&&$time<strtotime('20:00')) {
						$Setliving=round($base-$x, 1);
						$loop=false;
					}
				} else break;
			}
			if ($d['Weg']['s']==0) {
				if ($time>=strtotime('4:00')&&$time<strtotime('20:00')) $Setliving=$base;
				if ($dow==1&&$time>=strtotime('8:15')&&$time<strtotime('15:50')) $Setliving=$base-3;
				elseif ($dow==2&&$time>=strtotime('8:15')&&$time<strtotime('15:50')) $Setliving=$base-3;
				elseif ($dow==3&&$time>=strtotime('8:15')&&$time<strtotime('11:20')) $Setliving=$base-3;
				elseif ($dow==4&&$time>=strtotime('8:15')&&$time<strtotime('15:50')) $Setliving=$base-3;
				elseif ($dow==5&&$time>=strtotime('8:15')&&$time<strtotime('12:00')) $Setliving=$base-3;
				if ($d['pirliving']['s']=='Off'&&$time<mget('living')-3600) $Setliving=$base-3;
			}
		}
		if ($Setliving>18&&$d['zon']['s']>3000&&$d['buiten_temp']['s']>15&&$time>=strtotime('11:00')) $Setliving=$base-1;
	}
	if ($d['living_set']['s']!=$Setliving/*&&past('raamliving')>60&&past('deurinkom')>60&&past('deurgarage')>60*/) {
		store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
		$living_set=$Setliving;
		$d['living_set']['s']=$Setliving;
	}
}
require('_Rolluiken_Heating.php');
$bigdif=100;
$difgas=100;
if ($d['heating']['s']==1) require ('_TC_heating_airco.php');
elseif ($d['heating']['s']==2) require ('_TC_heating_aircogas.php');
elseif ($d['heating']['s']==3) require ('_TC_heating_gasairco.php');
elseif ($d['heating']['s']==4) require ('_TC_heating_gas.php');
require('_TC_badkamer.php');

