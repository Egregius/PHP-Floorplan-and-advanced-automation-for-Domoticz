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
$rates=array('B', 'B', 3, 4, 5, 6, 7);
$bigdif=0;
foreach (array('living','kamer','alex') as $k) {
	${'dif'.$k}=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
	if (${'dif'.$k}<0&&$d[$k.'_set']['s']>10) $bigdif-=${'dif'.$k};
}
$maxpow=floor(40*$bigdif);
if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
elseif ($maxpow>=80) {$maxpow=80;$spmode=0;}
else $spmode=-1;
foreach (array('living', 'kamer', 'alex') as $k) {
	if ($d[$k.'_set']['s']>10) {
		$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if ($dif>1) $power=0;
		elseif ($dif<=0.5) $power=1;
		if ($d['daikin']['s']=='On'&&past('daikin')>70) {
			if     ($dif<=-3.49)	{$rate=6;$line=__LINE__;}
			elseif ($dif<=-2.49)	{$rate=6;$line=__LINE__;}
			elseif ($dif<=-1.29)	{$rate=6;$line=__LINE__;}
			elseif ($dif<=-0.29)	{$rate=5;$line=__LINE__;}
			elseif ($dif<=-0.19)	{$rate=4;$line=__LINE__;}
			elseif ($dif<=-0.09)	{$rate=3;$line=__LINE__;}
			elseif ($dif<=0)	{$rate=2;$line=__LINE__;}
			elseif ($dif>0)		{$rate=1;$line=__LINE__;}
			elseif ($dif>0.2)		{$rate=1;$line=__LINE__;$d[$k.'_set']['s']=$d[$k.'_set']['s']-0.5;}
			if ($k=='living') {
				$set=$d[$k.'_set']['s']-2.5;
				if (($d['lgtv']['s']=='On'&&TIME>strtotime('19:00'))||($d['eettafel']['s']>0/*&&TIME>strtotime('18:00')*/)) {if ($rate>4)$rate=$rate-1;if ($rate<0)$rate=0;}
			} elseif ($k=='kamer') {
				$set=$d[$k.'_set']['s']-2.5;
				if (TIME<strtotime('8:30')||TIME>strtotime('22:00')) {
					$rate=0;
				} else {
					if ($rate<2) $rate=2;
				}
			} elseif ($k=='alex') {
				$set=$d[$k.'_set']['s']-2.5;
				if (TIME<strtotime('8:30')||TIME>strtotime('19:25')) {
					$rate=0;
				} else {
					if ($rate<2) $rate=2;
				}
			}
//			lg ($k.' => rate'.$rate.'='.$rates[$rate]);
			$set=ceil($set * 2) / 2;
			if ($set>25) $set=25;
			elseif ($set<10) $set=10;
			$daikin=json_decode($d['daikin'.$k]['s']);
			if (!isset($power)) $power=$daikin->power;
			if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=4||$daikin->fan!=$rates[$rate]) {
				lg('DAIKIN SET '.$k.' line='.$line.' dif='.$dif.' rate='.$rate.' spmode='.$spmode.' maxpow='.$maxpow.' bigdif='.$bigdif);
				$data=json_decode($d[$k.'_set']['icon'], true);
				$data['power']=$power;
				$data['mode']=4;
				$data['fan']=$rates[$rate];
				$data['set']=$set;
				storeicon($k.'_set', json_encode($data));
				daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $rates[$rate], $spmode, $maxpow);
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
			daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow);
		}
	}
}
foreach (array('kamer','alex') as $kamer) {
	if ((TIME>=strtotime('12:00')||TIME<=strtotime('4:00'))&&$d['raam'.$kamer]['s']=='Closed'&&past('raam'.$kamer)>1800&&($d['deur'.$kamer]['s']=='Closed'||($d['deur'.$kamer]['s']=='Open'&&past('deur'.$kamer)<900))) {
		$RSetkamer=14.0;
		$RSetalex=15.5;
	} else ${'RSet'.$kamer}=4;
	if (round($d[$kamer.'Z']['s'], 1)!=round(${'RSet'.$kamer}, 1)) {
		ud($kamer.'Z', 0, round(${'RSet'.$kamer}, 1), basename(__FILE__).':'.__LINE__);
		store($kamer.'Z', round(${'RSet'.$kamer}, 1), basename(__FILE__).':'.__LINE__);
	}
}
$uitna=(21-$d['buiten_temp']['s'])*75; if ($uitna<295) $uitna=295;
if ($d['brander']['s']=='On'&&past('brander')>$uitna) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
