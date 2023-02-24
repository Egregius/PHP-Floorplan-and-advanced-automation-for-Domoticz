<?php
foreach (array('living','kamer','alex') as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$bigdif) $bigdif=${'dif'.$kamer};
}

foreach (array('badkamer') as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$difgas) $difgas=${'dif'.$kamer};
}

$aanna=(1/(21-$d['buiten_temp']['s']))*6000; if ($aanna<595) $aanna=595;
$uitna=(21-$d['buiten_temp']['s'])*60; if ($uitna<475) $uitna=475;

if (($bigdif<=-0.8||$difgas<=-0.2)&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.6) sw('brander', 'On', 'Aan na = '.$aanna*0.6.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif<=-0.6||$difgas<=-0.1)&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.8) sw('brander', 'On', 'Aan na = '.$aanna*0.8.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif<=-0.4||$difgas<=0)&&$d['brander']['s']=="Off"&&past('brander')>$aanna) sw('brander','On', 'Aan na = '.$aanna.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.8&&$difgas>=-0.2)&&$d['brander']['s']=="On"&&past('brander')>$uitna*12) sw('brander', 'Off', 'Uit na = '.$uitna.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.6&&$difgas>=-0.1)&&$d['brander']['s']=="On"&&past('brander')>$uitna*6) sw('brander', 'Off', 'Uit na = '.$uitna*6 .' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.4&&$difgas>=0)&&$d['brander']['s']=="On"&&past('brander')>$uitna) sw('brander','Off', 'Uit na = '.$uitna*12 .' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=0&&$difgas>=0)&&$d['brander']['s']=="On") sw('brander','Off',basename(__FILE__).':'.__LINE__);
//if ($bigdif!=$d['bigdif']['m']) storemode('bigdif', $bigdif, basename(__FILE__).':'.__LINE__);

if ($d['daikin']['m']==1) {
	$bigdif=0;
	foreach (array('living','kamer','alex') as $k) {
		${'dif'.$k}=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if (${'dif'.$k}<0&&$d[$k.'_set']['s']>10) $bigdif-=${'dif'.$k};
	}
	$rates=array('B', 'B', 3, 4, 5, 6, 7);
	$maxpow=floor(60*$bigdif);
	if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
	elseif ($maxpow>=100) {$maxpow=100;$spmode=0;}
	else $spmode=-1;
	$maxpow=floor($maxpow/10)*10;
	if ($d['daikin_kWh']['m']!='Auto') $maxpow=$d['daikin_kWh']['m'];
	elseif ($d['Weg']['s']>0) $maxpow=40;
	foreach (array('living', 'kamer', 'alex') as $k) {
		if ($d[$k.'_set']['s']>10) {
			$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
			if ($dif>1.5) $power=0;
			elseif ($dif<=0) $power=1;
			if ($d['daikin']['s']=='On'&&past('daikin')>70) {
				if     ($dif<=-3.49)	{$rate=6;$line=__LINE__;}
				elseif ($dif<=-2.49)	{$rate=6;$line=__LINE__;}
				elseif ($dif<=-1.29)	{$rate=6;$line=__LINE__;}
				elseif ($dif<=-0.29)	{$rate=6;$line=__LINE__;}
				elseif ($dif<=-0.19)	{$rate=6;$line=__LINE__;}
				elseif ($dif<=-0.09)	{$rate=6;$line=__LINE__;}
				elseif ($dif<=0)	{$rate=6;$line=__LINE__;}
				elseif ($dif>0)		{$rate=5;$line=__LINE__;}
				elseif ($dif>0.2)		{$rate=4;$line=__LINE__;$d[$k.'_set']['s']=$d[$k.'_set']['s']-0.5;}
				if ($k=='living') {
					$set=$d[$k.'_set']['s']-0;
					if ($d['living_temp']['s']>$d[$k.'_set']['s']) $set-=($d['living_temp']['s']-$d[$k.'_set']['s'])*5;
					if (($d['lgtv']['s']=='On'&&TIME>strtotime('19:00'))||($d['eettafel']['s']>0)) {
						if ($rate>4)$rate=$rate-1;
						if ($rate<0)$rate=0;
					}
				} elseif ($k=='kamer') {
					$set=$d[$k.'_set']['s']-3;
					if (TIME<strtotime('8:30')||TIME>strtotime('22:00')) {
						$rate=0;
					} else {
						if ($rate<3) $rate=3;
					}
				} elseif ($k=='alex') {
					$set=$d[$k.'_set']['s']-3;
					if (TIME<strtotime('8:30')||TIME>strtotime('19:30')) {
						$rate=0;
					} else {
						if ($rate<3) $rate=3;
					}
				}
//				lg ($k.' => rate'.$rate.'='.$rates[$rate].' power='.$power);
				$set=ceil($set * 2) / 2;
				if ($set>25) $set=25;
				elseif ($set<10) $set=10;
//				$set=number_format($set,1);
				$daikin=json_decode($d['daikin'.$k]['s']);
				if (!isset($power)) $power=$daikin->power;
//				lg(print_r($daikin, true));
//				lg ($k.' => rate'.$rate.'='.$rates[$rate].' dif='.$dif.' power='.$power);
//				lg ($k.'	'.$daikin->set.' '.$set.'	'.$daikin->power.' '.$power.'	'.$daikin->mode.' '. 4 .'	'.$daikin->fan.' '.$rates[$rate].'	'.$d['daikin_kWh']['icon'].' '.$maxpow);
				if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=4||$daikin->fan!=$rates[$rate]||$d['daikin_kWh']['icon']!=$maxpow) {
					lg('DAIKIN SET '.$k.' line='.$line.' dif='.$dif.' rate='.$rate.' spmode='.$spmode.' maxpow='.$maxpow.' bigdif='.$bigdif);
					$data=json_decode($d[$k.'_set']['icon'], true);
					$data['power']=$power;
					$data['mode']=4;
					$data['fan']=$rates[$rate];
					$data['set']=$set;
					$data=json_encode($data);
					if ($d[$k.'_set']['icon']!=$data) storeicon($k.'_set', $data, basename(__FILE__).':'.__LINE__);
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
		unset($power);
	}
}
