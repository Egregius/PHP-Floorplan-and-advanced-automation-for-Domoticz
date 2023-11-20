<?php
foreach (array('living','kamer','alex') as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$bigdif) $bigdif=${'dif'.$kamer};
}

foreach (array('badkamer') as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$difgas) $difgas=${'dif'.$kamer};
}

$aanna=(1/(21-$d['buiten_temp']['s']))*6000; if ($aanna<1000) $aanna=1000;
$uitna=(21-$d['buiten_temp']['s'])*60; if ($uitna<595) $uitna=595;

if (($bigdif<=-0.8||$difgas<=-0.2)&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.6) sw('brander', 'On', 'Aan na = '.$aanna*0.6.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif<=-0.6||$difgas<=-0.1)&&$d['brander']['s']=="Off"&&past('brander')>$aanna*0.8) sw('brander', 'On', 'Aan na = '.$aanna*0.8.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif<=-0.4||$difgas<=0)&&$d['brander']['s']=="Off"&&past('brander')>$aanna) sw('brander','On', 'Aan na = '.$aanna.' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=0&&$difgas>=0)&&$d['brander']['s']=="On") sw('brander','Off',basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.4&&$difgas>=0)&&$d['brander']['s']=="On"&&(past('brander')>$uitna||$d['living_temp']['icon']>=0.3)) sw('brander','Off', 'Uit na = '.$uitna*12 .' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.6&&$difgas>=-0.1)&&$d['brander']['s']=="On"&&(past('brander')>$uitna*1.5||$d['living_temp']['icon']>=0.4)) sw('brander', 'Off', 'Uit na = '.$uitna*6 .' '.basename(__FILE__).':'.__LINE__);
elseif (($bigdif>=-0.8&&$difgas>=-0.2)&&$d['brander']['s']=="On"&&(past('brander')>$uitna*2||$d['living_temp']['icon']>=0.5)) sw('brander', 'Off', 'Uit na = '.$uitna.' '.basename(__FILE__).':'.__LINE__);
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
				if ($dif<-1) $rate=6;
				elseif ($dif<-0.4) $rate=5;
				elseif ($dif<0) $rate=4;
				elseif ($dif>=1.6) {$rate=2;$d[$k.'_set']['s']=$d[$k.'_set']['s']-2;}
				elseif ($dif>=1.2) {$rate=3;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1.5;}
				elseif ($dif>=0.8) {$rate=3;$d[$k.'_set']['s']=$d[$k.'_set']['s']-1;}
				elseif ($dif>=0.4) {$rate=4;$d[$k.'_set']['s']=$d[$k.'_set']['s']-0.5;}
				elseif ($dif>=0) $rate=4;
				if ($k=='living') {
					$set=$d[$k.'_set']['s']-2.5;
					if ($maxpow==40&&$set>$d[$k.'_temp']['s']-2.5&&$d[$k.'_temp']['s']>18) $set=$d[$k.'_temp']['s']-2.5;
					if (($d['Media']['s']=='On'&&$time>strtotime('19:00'))||($d['eettafel']['s']>0)) $rate=0;
				} elseif ($k=='kamer') {
					$set=$d['kamer_set']['s']-3;
					if ($maxpow==40&&$set>$d[$k.'_temp']['s']) $set=$d[$k.'_temp']['s']-2;
					if ($time<strtotime('8:30')||$time>strtotime('22:00')) {
						$rate=0;
					} else {
						if ($rate<3) $rate=3;
					}
				} elseif ($k=='alex') {
					$set=$d['alex_set']['s']-3;
					if ($maxpow==40&&$set>$d[$k.'_temp']['s']) $set=$d[$k.'_temp']['s']-2;
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
				storeicon($k.'_set', json_encode($data));
				daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow);
			}
		}
		unset($power);
	}
}
