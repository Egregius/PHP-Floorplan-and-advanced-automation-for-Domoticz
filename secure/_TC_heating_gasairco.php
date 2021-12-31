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
$kamers=array('alex','kamer');
foreach ($kamers as $kamer) {
	if (${'dif'.$kamer}<=number_format(($bigdif+ 0.2), 1)&&${'dif'.$kamer}<=0.2) {
		${'RSet'.$kamer}=setradiator($kamer, ${'dif'.$kamer}, true, $d[$kamer.'_set']['s']);
	} else {
		${'RSet'.$kamer}=setradiator($kamer, ${'dif'.$kamer}, false, $d[$kamer.'_set']['s']);
	}
	if (TIME>=strtotime('16:00')&&${'RSet'.$kamer}<15&&$d['raam'.$kamer]['s']=='Closed'&&$d['deur'.$kamer]['s']=='Closed') {
		if ($d[$kamer.'_temp']['s']<14) ${'RSet'.$kamer}=18;
		elseif ($d[$kamer.'_temp']['s']<15) ${'RSet'.$kamer}=17;
		elseif ($d[$kamer.'_temp']['s']<16) ${'RSet'.$kamer}=16;
	}
	if (round($d[$kamer.'Z']['s'], 1)!=round(${'RSet'.$kamer}, 1)) {
//		lg(basename(__FILE__).':'.__LINE__);
		ud($kamer.'Z', 0, round(${'RSet'.$kamer}, 0).'.0', basename(__FILE__).':'.__LINE__);
		store($kamer.'Z', round(${'RSet'.$kamer}, 0).'.0');
//		sl($kamer.'Z', round(${'RSet'.$kamer}, 0).'.0', basename(__FILE__).':'.__LINE__);
	}
}
if ($dif<=-0.2&&$d['brander']['s']=="Off"&&past('brander')>298) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
elseif ($dif<=-0.1&&$d['brander']['s']=="Off"&&past('brander')>498) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
elseif ($dif<= 0&&$d['brander']['s']=="Off"&&past('brander')>748) sw('brander','On', basename(__FILE__).':'.__LINE__);
elseif ($dif>= 0&&$d['brander']['s']=="On"&&past('brander')>298) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($dif>=-0.1&&$d['brander']['s']=="On"&&past('brander')>498) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($dif>=-0.2&&$d['brander']['s']=="On"&&past('brander')>748) sw('brander','Off', basename(__FILE__).':'.__LINE__);

foreach (array('living', 'kamer', 'alex') as $k) {
	if ($d[$k.'_set']['s']>10) {
		$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if ($dif>=0) $power=0;
		elseif ($dif<=-0.7) $power=1;
		if ($d['daikin']['s']=='On'&&past('daikin')>90) {
			$rate='A';
			if ($k=='living') 	$set=$d[$k.'_set']['s']-3.5;
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

