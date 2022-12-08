<?php
/**
 * Pass2PHP Temperature Control Airco heating
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
$user='heating';
$Setkamer=4;
$Setspeelkamer=4;
$Setalex=4;
if ($d['Weg']['s']<=2&&$d['heating']['s']>=1) {
	if ($d['kamer_set']['m']==0) {
		if (
				($d['raamkamer']['s']=='Closed'||$d['RkamerR']['s']==100)
			&&
				(past('raamkamer')>10800||TIME>strtotime('19:00')||1==1)
			&&
				(
					($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<900)||1==1)
				||
					(
						($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<900)||$d['raamalex']['s']=='Closed'||$d['Ralex']['s']==100)
					&&
						($d['deurspeelkamer']['s']=='Closed'||($d['deurspeelkamer']['s']=='Open'&&past('deurspeelkamer')<900)||$d['raamspeelkamer']['s']=='Closed'||$d['Rspeelkamer']['s']==100)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$Setkamer=21;
//			if (TIME<strtotime('5:00')||TIME>strtotime('21:00')) $Setkamer=15.5;
		}
	} else $Setkamer=$d['kamer_set']['s'];
	if ($d['alex_set']['m']==0) {
		if (
				($d['raamalex']['s']=='Closed'||$d['Ralex']['s']==100)
			&&
				(past('raamalex')>10800|| TIME>strtotime('19:00')||1==1)
			&&
				(
					($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<900)||1==1)
				||
					(
						($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<900)||$d['raamkamer']['s']=='Closed'||$d['RkamerR']['s']==100)
					&&
						($d['deurspeelkamer']['s']=='Closed'||($d['deurspeelkamer']['s']=='Open'&&past('deurspeelkamer')<900)||$d['raamspeelkamer']['s']=='Closed'||$d['Rspeelkamer']['s']==100)
					&& $d['raamhall']['s']=='Closed'
					)
				)
		) {
			$Setalex=21;
//			if (TIME<strtotime('5:00')||TIME>strtotime('18:00')) $Setalex=15.5;
		}
	} else $Setalex=$d['alex_set']['s'];
} elseif ($d['heating']['s']>=1) {
	$Setkamer=12;
	$Setspeelkamer=12;
	$Setalex=12;
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
if ($d['living_set']['m']==0) {
	if ($d['buiten_temp']['s']<20&&$d['minmaxtemp']['m']<22&&$d['heating']['s']>=1&&$d['raamliving']['s']=='Closed'&&$d['deurinkom']['s']=='Closed'&&$d['deurgarage']['s']=='Closed') {
		$Setliving=16;
		if ($d['Weg']['s']<2) {
			if ($d['heating']['s']>=3) {
				$Setliving=17;
				$dow=date("w");
				if($dow==0||$dow==6) {
					if (TIME>=strtotime('6:30')&&TIME<strtotime('19:00')) $Setliving=20.2;
				} else {
					if (TIME>=strtotime('5:30')&&TIME<strtotime('19:00')) $Setliving=20.2;
				}
			} elseif ($d['Weg']['s']==0) {
				if (TIME>=strtotime('4:00')&&TIME<strtotime('19:00')) $Setliving=20.2;
				if ($d['pirliving']['s']=='Off'&&TIME<apcu_fetch('living')-3600) $Setliving=19;
			}
		}
		if ($Setliving>19.5&&$d['zon']['s']>3000&&$d['buiten_temp']['s']>15&&TIME>=strtotime('11:00')) $Setliving=18;
	}
	if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&past('deurinkom')>60&&past('deurgarage')>60) {
		store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
		$living_set=$Setliving;
		$d['living_set']['s']=$Setliving;
	}
}
require('_Rolluiken_Heating.php');
$bigdif=100;
if ($d['heating']['s']==1) require ('_TC_heating_airco.php');
elseif ($d['heating']['s']==2) require ('_TC_heating_aircogas.php');
elseif ($d['heating']['s']==3) require ('_TC_heating_gasairco.php');
elseif ($d['heating']['s']==4) require ('_TC_heating_gas.php');
require('_TC_heating_badk-zolder.php');

