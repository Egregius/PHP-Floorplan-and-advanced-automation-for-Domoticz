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
if ($d['kamer_set']['m']==0) $Setkamer=4;
if ($d['speelkamer_set']['m']==0) $Setspeelkamer=4;
if ($d['alex_set']['m']==0) $Setalex=4;


$dif=$d['living_temp']['s']-$d['living_set']['s'];

if ($d['Weg']['s']==1) {
	if ($dif<=-0.2&&$d['brander']['s']=="Off"&&past('brander')>298) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($dif<=-0.1&&$d['brander']['s']=="Off"&&past('brander')>598) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($dif<=-0&&$d['brander']['s']=="Off"&&past('brander')>898) sw('brander','On', basename(__FILE__).':'.__LINE__);
	elseif ($dif>=-0&&$d['brander']['s']=="On"&&past('brander')>298) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($dif>=-0.1&&$d['brander']['s']=="On"&&past('brander')>598) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($dif>=-0.2&&$d['brander']['s']=="On"&&past('brander')>898) sw('brander','Off', basename(__FILE__).':'.__LINE__);
} else {
	if ($dif<=-0.7&&$d['brander']['s']=="Off"&&past('brander')>298) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($dif<=-0.6&&$d['brander']['s']=="Off"&&past('brander')>598) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
	elseif ($dif<=-0.5&&$d['brander']['s']=="Off"&&past('brander')>898) sw('brander','On', basename(__FILE__).':'.__LINE__);
	elseif ($dif>=-0.5&&$d['brander']['s']=="On"&&past('brander')>298) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($dif>=-0.6&&$d['brander']['s']=="On"&&past('brander')>598) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
	elseif ($dif>=-0.7&&$d['brander']['s']=="On"&&past('brander')>898) sw('brander','Off', basename(__FILE__).':'.__LINE__);
}

foreach (array('living', 'kamer', 'alex') as $k) {
	if ($d[$k.'_set']['s']>10) {
		$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if ($dif>1.5) $power=0;
		elseif ($dif<=0.5) $power=1;
		if ($d['daikin']['s']=='On'&&past('daikin')>90) {
			$rate='A';
			if ($k=='living') $set=$d[$k.'_set']['s']-2.5;
			elseif ($k=='kamer') {
				$set=$d[$k.'_set']['s']-3;
				if (TIME<strtotime('8:30')||TIME>strtotime('22:30'))$rate='B';
			} elseif ($k=='alex') {
				$set=$d[$k.'_set']['s']-3;
				if (TIME<strtotime('8:30')||TIME>strtotime('19:30'))$rate='B';
			}
			$set=ceil($set * 2) / 2;
			if ($set>25) $set=25;
			elseif ($set<10) $set=10;
			$daikin=json_decode($d['daikin'.$k]['s']);
			if (!isset($power)) $power=$daikin->power;
			if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=4||$daikin->fan!=$rate) {
				$data=json_decode($d[$k.'_set']['icon'], true);
				$data['power']=$power;
				$data['mode']=4;
				$data['fan']=$rate;
				$data['set']=$set;
				storeicon($k.'_set', json_encode($data));
				daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $rate);
				storemode('daikin'.$k, 4);
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off') {
			if (past('daikin')>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
		}
	} else {
		$daikin=json_decode($d['daikin'.$k]['s']);
		if ($daikin->power!=0||$daikin->mode!=4) {
			$data=json_decode($d[$k.'_set']['icon'], true);
			$data['power']=0;
			$data['mode']=4;
			$data['fan']='A';
			$data['set']=10;
			storeicon($k.'_set', json_encode($data));
			daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__);
			storemode('daikin'.$k, 0);
		}
	}
}
