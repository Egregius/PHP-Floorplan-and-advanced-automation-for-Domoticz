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
if ($d['kamer_set']['m']==0) $d['kamer_set']['s']=4;
if ($d['alex_set']['m']==0) $d['alex_set']['s']=4;
$rates=array('B', 'B', 3, 3, 4, 5, 6, 7);

foreach (array('living', 'kamer', 'alex') as $k) {
	if ($d[$k.'_set']['s']>10) {
		$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if ($dif>1) $power=0;
		elseif ($dif<=0.5) $power=1;
		if ($d['daikin']['s']=='On'&&past('daikin')>90) {
			$adv='12/13';
			if ($dif<=-0.5)		{$rate=7;$adv='2/13';}
			elseif ($dif<=-0.3)	{$rate=6;$adv='13';}
			elseif ($dif<=-0.1)	{$rate=5;$adv='12/13';}
			elseif ($dif<=0)	{$rate=4;$adv='12/13';}
			elseif ($dif<=0.1)	{$rate=3;$adv='12/13';}
			elseif ($dif>0.1)	{$rate=1;$adv='12/13';}
/*
'' = niks
12 = eco
13 = streamer
12/13 = eco & streamer
2 = power
2/13 = power & streamer
*/
			if ($k=='living') {
				$set=$d[$k.'_set']['s']-2.5;
				if ($d['lgtv']['s']=='On'||$d['eettafel']['s']>0) {$rate=$rate-1;$adv='12';}
			} elseif ($k=='kamer') {
				$set=$d[$k.'_set']['s']-3;
				if (TIME<strtotime('8:30')||TIME>strtotime('22:30')) {$rate=0;$adv='12';}
			} elseif ($k=='alex') {
				$set=$d[$k.'_set']['s']-3;
				if (TIME<strtotime('8:30')||TIME>strtotime('19:30')) {$rate=0;$adv='12';}
			}
			$set=ceil($set * 2) / 2;
			if ($set>25) $set=25;
			elseif ($set<10) $set=10;
			$daikin=json_decode($d['daikin'.$k]['s']);
			if (!isset($power)) $power=$daikin->power;
			lg ($daikin->adv.' - '.$adv);
			if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=4||$daikin->fan!=$rates[$rate]||$daikin->adv!=$adv) {
				$data=json_decode($d[$k.'_set']['icon'], true);
				$data['power']=$power;
				$data['mode']=4;
				$data['fan']=$rates[$rate];
				$data['set']=$set;
		//		storeicon($k.'_set', json_encode($data));
		//		daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $rates[$rate], $adv);
				//storemode('daikin'.$k, 4, basename(__FILE__).':'.__LINE__);
			}
		} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off'&&past('daikin')>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
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
			//storemode('daikin'.$k, 0);
		}
	}
}
if ($d['brander']['s']=='On'&&past('brander')>230) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
