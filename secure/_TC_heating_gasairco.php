<?php
foreach (array('living','kamer','alex') as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$bigdif) $bigdif=${'dif'.$kamer};
}
if (${'difliving'}<$bigdif) $bigdif=${'difliving'};
$maxpow=floor(20*$bigdif);
if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
elseif ($maxpow>=80) {$maxpow=80;$spmode=0;}
else $spmode=-1;

foreach (array('living'/*,'badkamer'*/) as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$difgas) $difgas=${'dif'.$kamer};
}

$aanna=(1/(21-$d['buiten_temp']['s']))*6000; if ($aanna<1000) $aanna=1000;
$uitna=(21-$d['buiten_temp']['s'])*60; if ($uitna<595) $uitna=595;

if (($bigdif<=-0.2    ||$difgas<=-0.2)&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.6) sw('brander','On' , 'Aan na = '.$aanna*0.6.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif<=-0.1||$difgas<=-0.1)&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.8) sw('brander','On' , 'Aan na = '.$aanna*0.8.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif<=-0  ||$difgas<=0   )&&$d['brander']['s']=="Off"&&past('brander')>$aanna)     sw('brander','On' , 'Aan na = '.$aanna.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>= 0  &&$difgas>=0   )&&$d['brander']['s']=="On" &&(past('brander')>$uitna||$d['living_temp']['icon']>=0.3))     sw('brander','Off', 'Uit na = '.$uitna.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.1&&$difgas>=-0.1)&&$d['brander']['s']=="On" &&(past('brander')>$uitna*1.5||$d['living_temp']['icon']>=0.4))   sw('brander','Off', 'Uit na = '.$uitna*6 .' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.2&&$difgas>=0.2 )&&$d['brander']['s']=="On" &&(past('brander')>$uitna*2||$d['living_temp']['icon']>=0.5))  sw('brander','Off', 'Uit na = '.$uitna*12 .' '.basename(__FILE__).':'.__LINE__);

//if ($bigdif!=$d['bigdif']['m']) storemode('bigdif', $bigdif, basename(__FILE__).':'.__LINE__);
if ($d['daikin']['m']==1) {
	$rates=array('B', 'B', 3, 4, 5, 6, 7,'A');
	$maxpow=floor(50*$bigdif);
	if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
	elseif ($maxpow>=80) {$maxpow=80;$spmode=0;}
	else $spmode=-1;
	$maxpow=floor($maxpow/5)*5;
	if ($d['daikin_kWh']['m']!='Auto') $maxpow=$d['daikin_kWh']['m'];
	elseif ($d['Weg']['s']>0) $maxpow=40;
	foreach (array('living', 'kamer', 'alex') as $k) {
		if ($d[$k.'_set']['s']>10) {
			$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
			if ($dif>=0) $power=0;
			elseif ($dif<=-1) $power=1;
			if ($d['daikin']['s']=='On'&&past('daikin')>90) {
				if ($dif<=-0.5)		{$rate=6;$spmode=1;}
				elseif ($dif<=-0.4)	{$rate=6;$spmode=0;}
				elseif ($dif<=-0.3)	{$rate=5;$spmode=-1;}
				elseif ($dif<=-0.2)	{$rate=4;$spmode=-1;}
				elseif ($dif<=-0.1)	{$rate=3;$spmode=-1;}
				elseif ($dif<=0)	{$rate=2;$spmode=-1;}
				elseif ($dif>0)		{$rate=1;$spmode=-1;}
				if ($k=='living') {
					$set=$d[$k.'_set']['s']-2;
					if (($d['lgtv']['s']=='On'&&$time>strtotime('19:00'))||($d['eettafel']['s']>0)) {
						if ($rate>3)$rate=3;
						if ($rate<0)$rate=0;
					}
				} elseif ($k=='kamer') {
					$set=$d[$k.'_set']['s']-3;
					if ($time<strtotime('8:30')||$time>strtotime('22:00')) {
						$rate=0;
					} else {
						/*if ($rate<3) */$rate=7;
					}
				} elseif ($k=='alex') {
					$set=$d[$k.'_set']['s']-3;
					if ($time<strtotime('8:30')||$time>strtotime('19:30')) {
						$rate=0;
					} else {
						/*if ($rate<3) */$rate=7;
					}
				}
				$set=ceil($set * 2) / 2;
				if ($set>25) $set=25;
				elseif ($set<10) $set=10;
				$daikin=json_decode($d['daikin'.$k]['s']);
				if (!isset($power)) $power=$daikin->power;
				if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=4||$daikin->fan!=$rates[$rate]/*||$daikin->adv!=$adv*/) {
					$data=json_decode($d[$k.'_set']['icon'], true);
					$data['power']=$power;
					$data['mode']=4;
					$data['fan']=$rates[$rate];
					$data['set']=$set;
					$data=json_encode($data);
					if ($d[$k.'_set']['icon']!=$data) storeicon($k.'_set', $data, basename(__FILE__).':'.__LINE__);
					daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $rates[$rate], $spmode, $maxpow);
					//storemode('daikin'.$k, 4);
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
				//storemode('daikin'.$k, 0);
			}
		}
	}
}
