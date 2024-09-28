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

foreach (array(/*'living',*/'badkamer') as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$difgas) $difgas=${'dif'.$kamer};
}

$aanna=(1/(21-$d['buiten_temp']['s']))*6000; if ($aanna<1000) $aanna=1000;
$uitna=(21-$d['buiten_temp']['s'])*60; if ($uitna<595) $uitna=595;

if (($bigdif<=-0.5    ||$difgas<=-0.2)&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.6) sw('brander','On' , 'Aan na = '.$aanna*0.6.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif<=-0.4||$difgas<=-0.1)&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.8) sw('brander','On' , 'Aan na = '.$aanna*0.8.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif<=-0.3  ||$difgas<=0   )&&$d['brander']['s']=="Off"&&past('brander')>$aanna)     sw('brander','On' , 'Aan na = '.$aanna.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>= 0  &&$difgas>=0   )&&$d['brander']['s']=="On" &&(past('brander')>$uitna||$d['living_temp']['icon']>=0.3))     sw('brander','Off', 'Uit na = '.$uitna.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.1&&$difgas>=-0.1)&&$d['brander']['s']=="On" &&(past('brander')>$uitna*1.5||$d['living_temp']['icon']>=0.4))   sw('brander','Off', 'Uit na = '.$uitna*6 .' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.2&&$difgas>=0.2 )&&$d['brander']['s']=="On" &&(past('brander')>$uitna*2||$d['living_temp']['icon']>=0.5))  sw('brander','Off', 'Uit na = '.$uitna*12 .' '.basename(__FILE__).':'.__LINE__);

//if ($bigdif!=$d['bigdif']['m']) storemode('bigdif', $bigdif, basename(__FILE__).':'.__LINE__);
if ($d['daikin']['m']==1) {
	$bigdif=0;
	foreach (array('living','kamer','alex') as $k) {
		${'dif'.$k}=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if (${'dif'.$k}<0&&$d[$k.'_set']['s']>10) $bigdif-=${'dif'.$k};
	}
	$rates=array('B', 'B', 3, 4, 5, 6, 7);
	$maxpow=floor((30-$d['buiten_temp']['s']-$d['buiten_temp']['s'])*$bigdif);
	$maxpow=floor($maxpow/10)*10;
	if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
	elseif ($maxpow>=100) {$maxpow=100;$spmode=0;}
	else $spmode=-1;
	if ($d['daikin_kWh']['m']!='Auto') $maxpow=$d['daikin_kWh']['m'];
	elseif ($d['Weg']['s']>0) $maxpow=40;
//	if ($d['living_set']['m']==0) {
		if ($dow==1&&$time>=strtotime('8:00')&&$time<strtotime('16:50')) $maxpow=40;
		elseif ($dow==2&&$time>=strtotime('8:00')&&$time<strtotime('17:20')) $maxpow=40;
		elseif ($dow==3&&$time>=strtotime('8:00')&&$time<strtotime('12:00')) $maxpow=40;
		elseif ($dow==4&&$time>=strtotime('8:00')&&$time<strtotime('17:20')) $maxpow=40;
		elseif ($dow==5&&$time>=strtotime('8:00')&&$time<strtotime('12:30')) $maxpow=40;
//	}
	if ($d['el']['s']>5000&&$maxpow>40) $maxpow=40;
	elseif ($d['el']['s']>4500&&$maxpow>50) $maxpow=50;
	elseif ($d['el']['s']>4000&&$maxpow>60) $maxpow=60;
	elseif ($d['el']['s']>3500&&$maxpow>70) $maxpow=70;
	elseif ($d['el']['s']>3000&&$maxpow>80) $maxpow=80;
	foreach (array('living', 'kamer', 'alex') as $k) {
		if ($d[$k.'_set']['s']>10) {
			$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
			if ($dif>3) $power=0;
			elseif ($dif<=0) $power=1;
			if ($d['daikin']['s']=='On'&&past('daikin')>70) {
				if ($dif<-2) $rate=6;
				elseif ($dif<-1) $rate=5;
				elseif ($dif<0) $rate=4;
				elseif ($dif>=1.6) {$rate=2;$d[$k.'_set']['s']=$d[$k.'_set']['s']-2;}
				elseif ($dif>=1.2) {$rate=3;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1.5;}
				elseif ($dif>=0.8) {$rate=3;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1;}
				elseif ($dif>=0.4) {$rate=4;$d[$k.'_set']['s']=$d[$k.'_set']['s']-0.5;}
				elseif ($dif>=0) $rate=4;
				if ($k=='living') {
					$set=$d[$k.'_set']['s']-2;
					if ($maxpow==40&&$set>$d[$k.'_temp']['s']-2&&$d[$k.'_temp']['s']>19.5) $set=(ceil($d[$k.'_temp']['s']*2)/2)-2;
					elseif ($maxpow==50&&$set>$d[$k.'_temp']['s']-1.5&&$d[$k.'_temp']['s']>19.5) $set=(ceil($d[$k.'_temp']['s']*2)/2)-1.5;
					if ((($d['Media']['s']=='On'&&$time>strtotime('19:00'))||($d['eettafel']['s']>0&&$time>strtotime('11:45')&&$time>strtotime('13:00'))||($d['eettafel']['s']>0&&$time>strtotime('17:30')&&$time>strtotime('19:00')))&&$rate>0) $rate=$rate-1;
				} elseif ($k=='kamer') {
					$set=$d['kamer_set']['s']-2;
					if ($maxpow==40&&$set>$d[$k.'_temp']['s']-2&&$d[$k.'_temp']['s']>14) $set=(ceil($d[$k.'_temp']['s']*2)/2)-2;
					elseif ($maxpow==40&&$set>$d[$k.'_temp']['s']-1.5&&$d[$k.'_temp']['s']>14) $set=(ceil($d[$k.'_temp']['s']*2)/2)-1.5;
					if ($time<strtotime('8:30')||$time>strtotime('22:00')) {
						$rate=0;
					} else {
						if ($rate<3) $rate=3;
					}
				} elseif ($k=='alex') {
					$set=$d['alex_set']['s']-2;
					if ($maxpow==40&&$set>$d[$k.'_temp']['s']-2&&$d[$k.'_temp']['s']>14) $set=(ceil($d[$k.'_temp']['s']*2)/2)-2;
					elseif ($maxpow==50&&$set>$d[$k.'_temp']['s']-1.5&&$d[$k.'_temp']['s']>14) $set=(ceil($d[$k.'_temp']['s']*2)/2)-1.5;
					if ($time<strtotime('8:30')||$time>strtotime('19:30')) {
						$rate=0;
					} else {
						if ($rate<3) $rate=3;
					}
				}
				$set=ceil($set * 2) / 2;
				if ($set>25) $set=25;
				elseif ($set<10) $set=10;
				$daikin=json_decode($d['daikin'.$k]['s']);
				if (!isset($power)) $power=$daikin->power;
				if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=4||$daikin->fan!=$rates[$rate]||$d['daikin_kWh']['icon']!=$maxpow) {
//					lg('DAIKIN SET '.$k.' dif='.$dif.' rate='.$rate.' spmode='.$spmode.' maxpow='.$maxpow.' bigdif='.$bigdif);
					$data=json_decode($d[$k.'_set']['icon'], true);
					$data['power']=$power;
					$data['mode']=4;
					$data['fan']=$rates[$rate];
					$data['set']=$set;
					$data=json_encode($data);
					if ($d[$k.'_set']['icon']!=$data) {
						storeicon($k.'_set', $data, basename(__FILE__).':'.__LINE__, true);
						$d[$k.'_set']['icon']=$data;
					}
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
				storeicon($k.'_set', json_encode($data), basename(__FILE__).':'.__LINE__, true);
				daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow);
			}
		}
		unset($power);
	}
}
