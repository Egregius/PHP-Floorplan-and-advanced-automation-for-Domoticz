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
if ($d['Weg']['s']<2&&$d['heating']['s']>=3) {
	if ($d['kamer_set']['m']==0) {
		if ($d['buiten_temp']['s']<10&&$d['minmaxtemp']['m']<10&&($d['deurkamer']['s']=='Closed'||($d['deurkamer']['s']=='Open'&&past('deurkamer')<600))&&$d['raamkamer']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamkamer')>7198 || TIME>strtotime('21:00'))) {
			if (TIME<strtotime('3:00')||TIME>strtotime('17:30')) $Setkamer=15;
		}
		if ($d['kamer_set']['s']!=$Setkamer) {
			store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
			$d['kamer_set']['s']=$Setkamer;
		}
	}
	if ($d['alex_set']['m']==0) {
		if ($d['buiten_temp']['s']<16&&$d['minmaxtemp']['m']<15&&($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<600))&&$d['raamalex']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamalex')>1800 || TIME>strtotime('19:00'))) {
			$Setalex=10;
			if (TIME<strtotime('3:00')||TIME>strtotime('17:30')) $Setalex=15;
		}
		if ($d['alex_set']['s']!=$Setalex) {
			store('alex_set', $Setalex, basename(__FILE__).':'.__LINE__);
			$alex_set=$Setalex;
			$d['alex_set']['s']=$Setalex;
		}
	}
}
$Setliving=10;
if ($d['living_set']['m']==0) {
	if ($d['buiten_temp']['s']<20&&$d['minmaxtemp']['m']<22&&$d['heating']['s']>=1&&$d['raamliving']['s']=='Closed'&&$d['deurinkom']['s']=='Closed'&&$d['deurgarage']['s']=='Closed') {
		$Setliving=10;
		if ($d['Weg']['s']<2) {
			if ($d['heating']['s']>=3) {
				$Setliving=18;
				$dow=date("w");
				if($dow==0||$dow==6) {
					if (TIME>=strtotime('5:30')&&TIME<strtotime('18:30')) $Setliving=21.0;
				} else {
					if (TIME>=strtotime('4:30')&&TIME<strtotime('18:30')) $Setliving=21.0;
				}
			} else {
				if (TIME>=strtotime('4:00')&&TIME<strtotime('19:00')) $Setliving=21.0;
				if ($d['pirliving']['s']=='Off'&&TIME<apcu_fetch('living')-3600) $Setliving=19;
			}
		}
		if ($Setliving>19.5&&$d['zon']['s']>3000&&$d['buiten_temp']['s']>15&&TIME>=strtotime('11:00')) $Setliving=19.5;
	}
	if ($d['living_set']['s']!=$Setliving&&past('raamliving')>60&&past('deurinkom')>60&&past('deurgarage')>60) {
		store('living_set', $Setliving, basename(__FILE__).':'.__LINE__);
		$living_set=$Setliving;
		$d['living_set']['s']=$Setliving;
	}
}
$bigdif=100;
if ($d['heating']['s']==1) require ('_TC_heating_airco.php');
elseif ($d['heating']['s']==2) require ('_TC_heating_aircogas.php');
elseif ($d['heating']['s']==3) require ('_TC_heating_gasairco.php');
elseif ($d['heating']['s']==4) require ('_TC_heating_gas.php');
require('_TC_heating_badk-zolder.php');
require('_Rolluiken_Heating.php');

