<?php
//lg($d['daikin']['s']);
if ($d['daikin']['m']==1) {
	$totalmin=0;
	foreach (array('living','kamer','alex') as $k) {
		${'dif'.$k}=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
		if (${'dif'.$k}<0) $totalmin-=${'dif'.$k};
	}
	if ($d['weg']['s']>0) $maxpow=40;
	elseif ($totalmin>=2.5) $maxpow=100;
	elseif ($totalmin>=2.0) $maxpow=90;
	elseif ($totalmin>=1.6) $maxpow=80;
	elseif ($totalmin>=1.2) $maxpow=70;
	elseif ($totalmin>=0.8) $maxpow=60;
	elseif ($totalmin>=0.4) $maxpow=50;
	else $maxpow=40;

	

	if ($d['n']>3500&&$maxpow>40) $maxpow=40;
	elseif ($d['n']>3000&&$maxpow>60) $maxpow=60;
	elseif ($d['n']>2500&&$maxpow>80) $maxpow=80;
	
	if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
	elseif ($maxpow>=100) {$maxpow=100;$spmode=0;}
	else $spmode=-1;
	if ($preheating==true) {$maxpow=40;$spmode=-1;}
	
	$pastdaikin=past('daikin');
	$daikinDefaults = ['power'=>99,'mode'=>99,'set'=>99,'fan'=>99,'spmode'=>99];
	$daikin ??= new stdClass();
	foreach (array('living', 'kamer', 'alex') as $k) {
		$daikin->$k ??= (object)$daikinDefaults;
		if ($d[$k.'_set']['s']>10) {
			$dif=$d[$k.'_temp']['s']-$d[$k.'_set']['s'];
			if ($dif>2) $power=0;
			elseif ($dif<=0) $power=1;
			if ($d['daikin']['s']=='On'&&$pastdaikin>70) {
				$rate='A';
					if ($dif >=  3.2) {$set=$d[$k.'_set']['s']-2.0;$spmode=-1;$line=__LINE__;}
				elseif ($dif >=  2.4) {$set=$d[$k.'_set']['s']-1.5;$spmode=-1;$line=__LINE__;}
				elseif ($dif >=  1.6) {$set=$d[$k.'_set']['s']-1.0;$spmode=-1;$line=__LINE__;}
				elseif ($dif >=  0.8) {$set=$d[$k.'_set']['s']-0.5;$spmode=-1;$line=__LINE__;}
				
				elseif ($dif <= -4) {$set=$d[$k.'_set']['s']+2.5;$spmode=1;$line=__LINE__;}
				elseif ($dif <= -3.2) {$set=$d[$k.'_set']['s']+2.0;$spmode=1;$line=__LINE__;}
				elseif ($dif <= -2.4) {$set=$d[$k.'_set']['s']+1.5;$spmode=0;$line=__LINE__;}
				elseif ($dif <= -1.6) {$set=$d[$k.'_set']['s']+1.0;$spmode=0;$line=__LINE__;}
				elseif ($dif <= -0.8) {$set=$d[$k.'_set']['s']+0.5;$spmode=-1;$line=__LINE__;}
				else {$set=$d[$k.'_set']['s'];$spmode=-1;$line=__LINE__;}
				if ($d['weg']['s']>0) $spmode=-1;
				if ($preheating==true) {$maxpow=40;$spmode=-1;}
				if ($k=='living') {
					if($prevSet==0) $set+=-2;
					if ($time>strtotime('19:00')&&$d['media']['s']=='On') $rate='B';
				} elseif ($k=='kamer') {
					$set+=-1.5;
					if ($time<strtotime('10:00')||$d['weg']['s']==1) $rate='B';
				} elseif ($k=='alex') {
					$set+=-1.5;
					if ($d['alexslaapt']['s']==1) $rate='B';
				}
				$set=ceil($set * 2) / 2;
				if ($set>30) $set=30;
				elseif ($set<10) $set=10;
				if (!isset($power)) $power=$daikin->power;
//				if ($k=='living') lg('Daikin living => dif='.$dif.' setpoint='.$d[$k.'_set']['s'].' set='.$set.' spmode='.$spmode.' | '.$line);
				if ($daikin->$k->power!=$power||$daikin->$k->mode!=4||$daikin->$k->set!=$set||$daikin->$k->fan!=$fan||$daikin->$k->spmode!=$spmode) {
					if(daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
						$daikin->$k->power=$power;
						$daikin->$k->mode=4;
						$daikin->$k->fan=$fan;
						$daikin->$k->set=$set;
						$daikin->$k->spmode=$spmode;
						lg(print_r($daikin,true));
					}
				}
			} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off'&&$pastdaikin>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
		} else {
			if ($d['daikin']['s']=='On'&&$pastdaikin>70) {
				if ($daikin->$k->power!=0||$daikin->$k->mode!=4) {
					if(daikinset($k, 0, 4, 10, basename(__FILE__).':'.__LINE__, 'A', -1, $maxpow)) {
						$daikin->$k->power=0;
						$daikin->$k->mode=4;
					}
				}
			}
		}
		unset($power);
	}
}
if ($d['brander']['s']=='On'&&$d['badkamer_temp']['s']>12&&past('brander')>=595) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
elseif ($d['brander']['s']=='Off'&&$d['badkamer_temp']['s']<12&&past('brander')>=595) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
