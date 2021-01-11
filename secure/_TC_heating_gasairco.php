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
//lg(__FILE__);
$dif=number_format($d['living_temp']['s']-$d['living_set']['s'], 1);

if ($dif<=-0.2&&$d['brander']['s']=="Off"&&past('brander')>298) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
elseif ($dif<=-0.1&&$d['brander']['s']=="Off"&&past('brander')>598) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
elseif ($dif<= 0&&$d['brander']['s']=="Off"&&past('brander')>898) sw('brander','On', basename(__FILE__).':'.__LINE__);
elseif ($dif>= 0&&$d['brander']['s']=="On"&&past('brander')>298) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($dif>=-0.1&&$d['brander']['s']=="On"&&past('brander')>598) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($dif>=-0.2&&$d['brander']['s']=="On"&&past('brander')>898) sw('brander','Off', basename(__FILE__).':'.__LINE__);

foreach (array('living', 'kamer', 'alex') as $k) {
	if ($d[$k.'_set']['s']>10) {
		$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if ($dif>=0) $power=0;
		elseif ($dif<=-0.8) $power=1;
		if (isset($power)&&$d['daikin']['s']=='On'&&past('daikin')>120) {
			$rate='A';
			if ($k=='living') {
				$set=$d[$k.'_set']['s']-3;
			} elseif ($k=='kamer') {
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
			if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=4||$daikin->fan!=$rate) {
				$data=json_decode($d[$k.'_set']['icon'], true);
				$data['power']=1;
				$data['mode']=4;
				$data['fan']=$rate;
				$data['set']=$set;
				storeicon($k.'_set', json_encode($data));
				daikinset($k, 1, 4, $set, basename(__FILE__).':'.__LINE__, $rate);
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

