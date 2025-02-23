<?php
$user=basename(__FILE__);
if ($d['daikin']['m']==1) {
	$totalmin=0;
	foreach (array('living','kamer','alex') as $k) {
		${'dif'.$k}=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if (${'dif'.$k}<0) $totalmin-=${'dif'.$k};
	}
	if ($totalmin>=1.8) $maxpow=100;
	elseif ($totalmin>=1.5) $maxpow=90;
	elseif ($totalmin>=1.2) $maxpow=80;
	elseif ($totalmin>=0.9) $maxpow=70;
	elseif ($totalmin>=0.6) $maxpow=60;
	elseif ($totalmin>=0.3) $maxpow=50;
	else $maxpow=40;

	if ($d['daikin_kWh']['m']!='Auto') $maxpow=$d['daikin_kWh']['m'];
	elseif ($d['Weg']['s']>0) $maxpow=40;
/*	if ($d['living_set']['m']==0) {
		if ($dow==1&&$time>=strtotime('8:00')&&$time<strtotime('15:00')) $maxpow=40;
		elseif ($dow==2&&$time>=strtotime('8:00')&&$time<strtotime('15:00')) $maxpow=40;
		elseif ($dow==3&&$time>=strtotime('8:00')&&$time<strtotime('12:00')) $maxpow=40;
		elseif ($dow==4&&$time>=strtotime('8:00')&&$time<strtotime('15:00')) $maxpow=40;
		elseif ($dow==5&&$time>=strtotime('8:00')&&$time<strtotime('12:00')) $maxpow=40;
	}*/

	if ($d['net']>3500&&$maxpow>40) $maxpow=40;
	elseif ($d['net']>3000&&$maxpow>60) $maxpow=60;
	elseif ($d['net']>2500&&$maxpow>80) $maxpow=80;
	$pastdaikin=past('daikin');
	foreach (array('living', 'kamer', 'alex') as $k) {
		if ($d[$k.'_set']['s']>10) {
			$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
			if ($dif>3) $power=0;
			elseif ($dif<=0) $power=1;
			if ($d['daikin']['s']=='On'&&$pastdaikin>70) {
				$rate='A';
				      if ($dif >=  1.2) {$set=$d[$k.'_set']['s']-2;$spmode=-1;$line=__LINE__;}
				  elseif ($dif >=  0.9) {$set=$d[$k.'_set']['s']-1.5;$spmode=-1;$line=__LINE__;}
				  elseif ($dif >=  0.6) {$set=$d[$k.'_set']['s']-1;$spmode=-1;$line=__LINE__;}
				  elseif ($dif >=  0.3) {$set=$d[$k.'_set']['s']-0.5;$spmode=-1;$line=__LINE__;}

				  elseif ($dif <= -1.5) {$set=$d[$k.'_set']['s']+2.5;$spmode=1;$line=__LINE__;}
				  elseif ($dif <= -1.2) {$set=$d[$k.'_set']['s']+2;$spmode=1;$line=__LINE__;}
				  elseif ($dif <= -0.9) {$set=$d[$k.'_set']['s']+1.5;$spmode=1;$line=__LINE__;}
				  elseif ($dif <= -0.6) {$set=$d[$k.'_set']['s']+1;$spmode=0;$line=__LINE__;}
				  elseif ($dif <= -0.3) {$set=$d[$k.'_set']['s']+0.5;$spmode=0;$line=__LINE__;}
				  else {$set=$d[$k.'_set']['s'];$spmode=-1;$line=__LINE__;}

				if ($k=='living') {
					$cor=3;
					$set+=$cor;
					if ($time>strtotime('20:00')) $rate='B';
				} elseif ($k=='kamer') {
					$cor=1;
					$set+=$cor;
					if ($time<strtotime('10:00')||$time>strtotime('22:00')) $rate='B';
				} elseif ($k=='alex') {
					$cor=1;
					$set+=$cor;
					if ($time<strtotime('10:00')||$time>strtotime('19:30')) $rate='B';
				}
				$set=ceil($set * 2) / 2;
				if ($set>30) $set=30;
				elseif ($set<10) $set=10;
				$daikin=json_decode($d['daikin'.$k]['s']);
				if (!isset($power)) $power=$daikin->power;
					if ($daikin->set!=$set||$daikin->power!=$power||$daikin->mode!=4||$daikin->fan!=$rate||$d['daikin_kWh']['icon']!=$maxpow) {
						lg('Living => dif='.$dif.' setpoint='.$d[$k.'_set']['s'].' set='.$set.' spmode='.$spmode.' | '.$line);
						if ($set>$d[$k.'_set']['s']+$cor) lg('DAIKIN '.$k.' hoger met '.$set-$d[$k.'_set']['s'].' omdat het te koud is, dif='.$dif);
						elseif ($set<$d[$k.'_set']['s']+$cor) lg('DAIKIN '.$k.' lager met '.$d[$k.'_set']['s']-$set.' omdat het te warm is, $dif='.$dif);
						if ($spmode==1) $spmodetxt='POWER';
						elseif ($spmode==0) $spmodetxt='';
						elseif ($spmode==-1) $spmodetxt='eco';
						lg('DAIKIN SET '.$k.' set '.$daikin->set.'>'.$set.' | power '.$daikin->power.'>'.$power.' | mode '.$daikin->mode.'>4 | fan '.$daikin->fan.'>'.$rate.' | maxpow '.$d['daikin_kWh']['icon'].'>'.$maxpow.' | dif '.$dif.' | '.$spmodetxt);
						$data=json_decode($d[$k.'_set']['icon'], true);
						$data['power']=$power;
						$data['mode']=4;
						$data['fan']=$rate;
						$data['set']=$set;
						$data['spmode']=$spmodetxt;
						$data['maxpow']=($maxpow>40?$maxpow:'');
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
				$data['spmode']=$daikin->spmode;
				$data['maxpow']=$daikin->maxpow;
				storeicon($k.'_set', json_encode($data), basename(__FILE__).':'.__LINE__, true);
				daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow);
			}
		}
		unset($power);
	}
}
if ($d['brander']['s']=='On'&&$d['badkamer_temp']['s']>12&&past('brander')>=595) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($d['brander']['s']=='Off'&&$d['badkamer_temp']['s']<12&&past('brander')>=595) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
