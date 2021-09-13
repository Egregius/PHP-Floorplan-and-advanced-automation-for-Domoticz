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
			if (TIME<strtotime('4:30')||TIME>strtotime('21:00')) $Setkamer=10;
		}
		if ($d['kamer_set']['s']!=$Setkamer) {
			store('kamer_set', $Setkamer, basename(__FILE__).':'.__LINE__);
			$d['kamer_set']['s']=$Setkamer;
		}
	}
	if ($d['speelkamer_set']['m']==0) {
		if ($d['buiten_temp']['s']<14&&$d['minmaxtemp']['m']<15&&($d['deurspeelkamer']['s']=='Closed'||($d['deurspeelkamer']['s']=='Open'&&past('deurspeelkamer')<600))&&$d['raamspeelkamer']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamspeelkamer')>7198 || TIME>strtotime('20:00'))) {
			$Setspeelkamer=10;
			if ($d['gcal']['s']) {
				if (TIME<strtotime('4:30')||TIME>strtotime('21:00')) $Setspeelkamer=14;
			}
		}
		if ($d['speelkamer_set']['s']!=$Setspeelkamer) {
			store('speelkamer_set', $Setspeelkamer, basename(__FILE__).':'.__LINE__);
			$speelkamer_set=$Setspeelkamer;
			$d['speelkamer_set']['s']=$Setspeelkamer;
		}
	}
	if ($d['alex_set']['m']==0) {
		if ($d['buiten_temp']['s']<16&&$d['minmaxtemp']['m']<15&&($d['deuralex']['s']=='Closed'||($d['deuralex']['s']=='Open'&&past('deuralex')<600))&&$d['raamalex']['s']=='Closed'&&$d['heating']['s']>=1&&(past('raamalex')>1800 || TIME>strtotime('19:00'))) {
			$Setalex=10;
			if (TIME<strtotime('4:30')||TIME>strtotime('19:00')) $Setalex=14;
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
	if ($d['buiten_temp']['s']<20&&$d['minmaxtemp']['m']<20&&$d['heating']['s']>=1&&$d['raamliving']['s']=='Closed'&&$d['deurinkom']['s']=='Closed'&&$d['deurgarage']['s']=='Closed') {
		$Setliving=14;
		if ($d['Weg']['s']==0) {
			if ($d['heating']['s']>=3) {
				if (TIME>=strtotime('17:00')&&TIME<strtotime('18:30')) $Setliving=20.9;
				elseif (TIME>=strtotime('15:30')&&TIME<strtotime('18:30')) $Setliving=20.8;
				elseif (TIME>=strtotime('14:00')&&TIME<strtotime('18:30')) $Setliving=20.7;
				elseif (TIME>=strtotime('12:30')&&TIME<strtotime('18:30')) $Setliving=20.6;
				elseif (TIME>=strtotime('8:00')&&TIME<strtotime('18:30')) $Setliving=20.5;
				elseif (TIME>=strtotime('6:30')&&TIME<strtotime('18:30')) $Setliving=20.4;
				elseif (TIME>=strtotime('6:00')&&TIME<strtotime('18:30')) $Setliving=20.3;
				elseif (TIME>=strtotime('5:30')&&TIME<strtotime('18:30')) $Setliving=20.2;
				elseif (TIME>=strtotime('5:00')&&TIME<strtotime('18:30')) $Setliving=20.1;
				elseif (TIME>=strtotime('4:00')&&TIME<strtotime('18:30')) $Setliving=20.0;
			} else {
				if (TIME>=strtotime('5:00')&&TIME<strtotime('20:00')) $Setliving=20.5;
				if ($d['pirliving']['s']=='Off'&&TIME<apcu_fetch('living')-3600) $Setliving=19;
			}
		} elseif ($d['Weg']['s']==1&&$d['heating']['s']>=3) {
			$dow=date("w");
			if($dow==0||$dow==6) {
				if (TIME>=strtotime('8:00')&&TIME<strtotime('12:00')) $Setliving=20.0;
				elseif (TIME>=strtotime('7:50')&&TIME<strtotime('12:00')) $Setliving=19.9;
				elseif (TIME>=strtotime('7:40')&&TIME<strtotime('12:00')) $Setliving=19.8;
				elseif (TIME>=strtotime('7:30')&&TIME<strtotime('12:00')) $Setliving=19.7;
				elseif (TIME>=strtotime('7:20')&&TIME<strtotime('12:00')) $Setliving=19.6;
				elseif (TIME>=strtotime('7:10')&&TIME<strtotime('12:00')) $Setliving=19.5;
				elseif (TIME>=strtotime('7:00')&&TIME<strtotime('12:00')) $Setliving=19.4;
				elseif (TIME>=strtotime('6:50')&&TIME<strtotime('12:00')) $Setliving=19.3;
				elseif (TIME>=strtotime('6:40')&&TIME<strtotime('12:00')) $Setliving=19.2;
				elseif (TIME>=strtotime('6:30')&&TIME<strtotime('12:00')) $Setliving=19.1;
				elseif (TIME>=strtotime('6:20')&&TIME<strtotime('12:00')) $Setliving=19.0;
				elseif (TIME>=strtotime('6:10')&&TIME<strtotime('12:00')) $Setliving=18.9;
				elseif (TIME>=strtotime('6:00')&&TIME<strtotime('12:00')) $Setliving=18.8;
				elseif (TIME>=strtotime('5:50')&&TIME<strtotime('12:00')) $Setliving=18.6;
				elseif (TIME>=strtotime('5:40')&&TIME<strtotime('12:00')) $Setliving=18.4;
				elseif (TIME>=strtotime('5:30')&&TIME<strtotime('12:00')) $Setliving=18.2;
				elseif (TIME>=strtotime('5:20')&&TIME<strtotime('12:00')) $Setliving=18.0;
				elseif (TIME>=strtotime('5:10')&&TIME<strtotime('12:00')) $Setliving=17.7;
				elseif (TIME>=strtotime('5:00')&&TIME<strtotime('12:00')) $Setliving=17.4;
				elseif (TIME>=strtotime('4:45')&&TIME<strtotime('12:00')) $Setliving=17.1;
				elseif (TIME>=strtotime('4:30')&&TIME<strtotime('12:00')) $Setliving=16.8;
				elseif (TIME>=strtotime('4:15')&&TIME<strtotime('12:00')) $Setliving=16.5;
				elseif (TIME>=strtotime('4:00')&&TIME<strtotime('12:00')) $Setliving=16.2;
				elseif (TIME>=strtotime('3:45')&&TIME<strtotime('12:00')) $Setliving=15.9;
				elseif (TIME>=strtotime('3:30')&&TIME<strtotime('12:00')) $Setliving=15.6;
				elseif (TIME>=strtotime('3:15')&&TIME<strtotime('12:00')) $Setliving=15.3;
				elseif (TIME>=strtotime('3:00')&&TIME<strtotime('12:00')) $Setliving=15.0;
			} else {
				if (TIME>=strtotime('7:00')&&TIME<strtotime('12:00')) $Setliving=20.0;
				elseif (TIME>=strtotime('6:50')&&TIME<strtotime('12:00')) $Setliving=19.9;
				elseif (TIME>=strtotime('6:40')&&TIME<strtotime('12:00')) $Setliving=19.8;
				elseif (TIME>=strtotime('6:30')&&TIME<strtotime('12:00')) $Setliving=19.7;
				elseif (TIME>=strtotime('6:20')&&TIME<strtotime('12:00')) $Setliving=19.6;
				elseif (TIME>=strtotime('6:10')&&TIME<strtotime('12:00')) $Setliving=19.5;
				elseif (TIME>=strtotime('6:00')&&TIME<strtotime('12:00')) $Setliving=19.4;
				elseif (TIME>=strtotime('5:50')&&TIME<strtotime('12:00')) $Setliving=19.3;
				elseif (TIME>=strtotime('5:40')&&TIME<strtotime('12:00')) $Setliving=19.2;
				elseif (TIME>=strtotime('5:30')&&TIME<strtotime('12:00')) $Setliving=19.1;
				elseif (TIME>=strtotime('5:20')&&TIME<strtotime('12:00')) $Setliving=19.0;
				elseif (TIME>=strtotime('5:10')&&TIME<strtotime('12:00')) $Setliving=18.9;
				elseif (TIME>=strtotime('5:00')&&TIME<strtotime('12:00')) $Setliving=18.8;
				elseif (TIME>=strtotime('4:50')&&TIME<strtotime('12:00')) $Setliving=18.6;
				elseif (TIME>=strtotime('4:40')&&TIME<strtotime('12:00')) $Setliving=18.4;
				elseif (TIME>=strtotime('4:30')&&TIME<strtotime('12:00')) $Setliving=18.2;
				elseif (TIME>=strtotime('4:20')&&TIME<strtotime('12:00')) $Setliving=18.0;
				elseif (TIME>=strtotime('4:10')&&TIME<strtotime('12:00')) $Setliving=17.7;
				elseif (TIME>=strtotime('4:00')&&TIME<strtotime('12:00')) $Setliving=17.4;
				elseif (TIME>=strtotime('3:45')&&TIME<strtotime('12:00')) $Setliving=17.1;
				elseif (TIME>=strtotime('3:30')&&TIME<strtotime('12:00')) $Setliving=16.8;
				elseif (TIME>=strtotime('3:15')&&TIME<strtotime('12:00')) $Setliving=16.5;
				elseif (TIME>=strtotime('3:00')&&TIME<strtotime('12:00')) $Setliving=16.2;
				elseif (TIME>=strtotime('2:45')&&TIME<strtotime('12:00')) $Setliving=15.9;
				elseif (TIME>=strtotime('2:30')&&TIME<strtotime('12:00')) $Setliving=15.6;
				elseif (TIME>=strtotime('2:15')&&TIME<strtotime('12:00')) $Setliving=15.3;
				elseif (TIME>=strtotime('2:00')&&TIME<strtotime('12:00')) $Setliving=15.0;
			}
		}
		if ($Setliving>19.5&&TIME>=strtotime('11:00')&&$d['zon']['s']>3000&&$d['buiten_temp']['s']>15) $Setliving=19.5;
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

