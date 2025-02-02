<?php
$user=basename(__FILE__);
if ($d['daikin']['m']==1) {
	$bigdif=0;
	foreach (array('living','kamer','alex') as $k) {
		${'dif'.$k}=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if (${'dif'.$k}<0&&$d[$k.'_set']['s']>10) $bigdif-=${'dif'.$k};
	}
	$maxpow=floor((50-$d['buiten_temp']['s']-$d['buiten_temp']['s'])*$bigdif);
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
	$net=mget('net');
	if ($net>2500&&$maxpow>40) $maxpow=40;
	$pastdaikin=past('daikin');
	foreach (array('living', 'kamer', 'alex') as $k) {
		if ($d[$k.'_set']['s']>10) {
			$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
			if ($dif>3) $power=0;
			elseif ($dif<=0) $power=1;
			if ($d['daikin']['s']=='On'&&$pastdaikin>70) {
				lg('Daikin '.$k.' dif='.$dif);
				$rate='A';
				      if ($dif>=1.2) $set=$d[$k.'_set']['s']-2;
				elseif ($dif>=0.9) $set=$d[$k.'_set']['s']-1.5;
				elseif ($dif>=0.6) $set=$d[$k.'_set']['s']-1;
				elseif ($dif>=0.3) $set=$d[$k.'_set']['s']-0.5;
				elseif ($dif<=-1.5) $set=$d[$k.'_set']['s']+3;
				elseif ($dif<=-1.2) $set=$d[$k.'_set']['s']+2.5;
				elseif ($dif<=-0.9) $set=$d[$k.'_set']['s']+2;
				elseif ($dif<=-0.6) $set=$d[$k.'_set']['s']+1.5;
				elseif ($dif<=-0.3) $set=$d[$k.'_set']['s']+1;
				if ($k=='living') {
//					if ($maxpow==40&&$set>$d[$k.'_temp']['s']-2&&$d[$k.'_temp']['s']>19.5) $set=(ceil($d[$k.'_temp']['s']*2)/2)-2;
//					elseif ($maxpow==50&&$set>$d[$k.'_temp']['s']-1.5&&$d[$k.'_temp']['s']>19.5) $set=(ceil($d[$k.'_temp']['s']*2)/2)-1.5;
					//if ((($d['Media']['s']=='On'&&$time>strtotime('19:00'))||($d['eettafel']['s']>0&&$time>strtotime('11:45')&&$time>strtotime('13:00'))||($d['eettafel']['s']>0&&$time>strtotime('17:30')&&$time>strtotime('19:00')))) $rate='B';
				} elseif ($k=='kamer') {
					$set-=1;
//					if ($maxpow==40&&$set>$d[$k.'_temp']['s']-2&&$d[$k.'_temp']['s']>14) $set=(ceil($d[$k.'_temp']['s']*2)/2)-2;
//					elseif ($maxpow==40&&$set>$d[$k.'_temp']['s']-1.5&&$d[$k.'_temp']['s']>14) $set=(ceil($d[$k.'_temp']['s']*2)/2)-1.5;
					if ($time<strtotime('10:00')||$time>strtotime('22:00')) $rate='B';
				} elseif ($k=='alex') {
					$set-=1;
//					if ($maxpow==40&&$set>$d[$k.'_temp']['s']-2&&$d[$k.'_temp']['s']>14) $set=(ceil($d[$k.'_temp']['s']*2)/2)-2;
//					elseif ($maxpow==50&&$set>$d[$k.'_temp']['s']-1.5&&$d[$k.'_temp']['s']>14) $set=(ceil($d[$k.'_temp']['s']*2)/2)-1.5;
					if ($time<strtotime('10:00')||$time>strtotime('19:30')) $rate='B';
				}
				$set=ceil($set * 2) / 2;
				if ($set>30) $set=30;
				elseif ($set<10) $set=10;
				$daikin=json_decode($d['daikin'.$k]['s']);
				if (!isset($power)) $power=$daikin->power;
				if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=4||$daikin->fan!=$rate||$d['daikin_kWh']['icon']!=$maxpow) {
//					lg('DAIKIN SET '.$k.' dif='.$dif.' rate='.$rate.' spmode='.$spmode.' maxpow='.$maxpow.' bigdif='.$bigdif);
					$data=json_decode($d[$k.'_set']['icon'], true);
					$data['power']=$power;
					$data['mode']=4;
					$data['fan']=$rate;
					$data['set']=$set;
					$data=json_encode($data);
					if ($d[$k.'_set']['icon']!=$data) {
						storeicon($k.'_set', $data, basename(__FILE__).':'.__LINE__, true);
						$d[$k.'_set']['icon']=$data;
					}
					daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $rate, $spmode, $maxpow);
				}
			} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off'&&$pastdaikin>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
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
if ($d['brander']['s']=='On'&&past('brander')>=600) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
