<?php
foreach (array('living','badkamer') as $kamer) {
	${'dif'.$kamer}=number_format($d[$kamer.'_temp']['s']-$d[$kamer.'_set']['s'],1);
	if (${'dif'.$kamer}<$difgas) $difgas=${'dif'.$kamer};
}

$aanna=(1/(21-$d['buiten_temp']['s']))*6000; if ($aanna<1000) $aanna=1000;
$uitna=(21-$d['buiten_temp']['s'])*60; 
if ($uitna<595) $uitna=595;
elseif ($uitna>1795) $uitna=1795;
$pastbrander=past('brander');
//lg('difgas='.$difgas.' pastbrander='.$pastbrander);
if (	$difgas<=-1.8&&$d['brander']['s']=="Off"&&$pastbrander>$aanna*0.5&&$d['n']>-500&&$d['buiten_temp']['s']<=5) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.5);
elseif ($difgas<=-1.5&&$d['brander']['s']=="Off"&&$pastbrander>$aanna*0.6&&$d['n']>-500&&$d['buiten_temp']['s']<=4) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.6);
elseif ($difgas<=-1.2&&$d['brander']['s']=="Off"&&$pastbrander>$aanna*0.7&&$d['n']>-500&&$d['buiten_temp']['s']<=3) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.7);
elseif ($difgas<=-0.9&&$d['brander']['s']=="Off"&&$pastbrander>$aanna*0.8&&$d['n']>-500&&$d['buiten_temp']['s']<=2) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.8);
elseif ($difgas<=-0.6&&$d['brander']['s']=="Off"&&$pastbrander>$aanna*0.9&&$d['n']>-500&&$d['buiten_temp']['s']<=1) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna*0.9);
elseif ($difgas<=-0.3   &&$d['brander']['s']=="Off"&&$pastbrander>$aanna    &&$d['n']>-500&&$d['buiten_temp']['s']<=0) sw('brander','On' , 'difgas = '.$difgas.' Aan na = '.$aanna);
elseif ($difgas>=0   &&$d['brander']['s']=="On" &&$pastbrander>$uitna)     sw('brander','Off', 'Uit na = '.$uitna);
elseif ($difgas>=-0.1&&$d['brander']['s']=="On" &&$pastbrander>$uitna*1.5) sw('brander','Off', 'Uit na = '.$uitna*6);
elseif ($difgas>=-0.2 &&$d['brander']['s']=="On" &&$pastbrander>$uitna*2)   sw('brander','Off', 'Uit na = '.$uitna*12);

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

//	$daikin->living->fan=4;

	if ($d['n']>3500&&$maxpow>40) $maxpow=40;
	elseif ($d['n']>3000&&$maxpow>60) $maxpow=60;
	elseif ($d['n']>2500&&$maxpow>80) $maxpow=80;
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
			else $power=$daikin->$k->power;
			if ($d['daikin']['s']=='On') {
				$fan='A';
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
					else $set=28;
					if ($time>strtotime('19:00')&&$d['media']['s']=='On') $fan='B';
				} elseif ($k=='kamer') {
					$set+=-1.5;
					if ($time<strtotime('10:00')||$d['weg']['s']==1) $fan='B';
				} elseif ($k=='alex') {
					$set+=-1.5;
					if ($d['alexslaapt']['s']==1) $fan='B';
				}
				$set=ceil($set * 2) / 2;
				if ($set>28) $set=28;
				elseif ($set<10) $set=10;
				if ($daikin->$k->power!=$power||$daikin->$k->mode!=4||$daikin->$k->set!=$set||$daikin->$k->fan!=$fan||$daikin->$k->spmode!=$spmode) {
					if(daikinset($k, $power, 4, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
						$daikin->$k->power=$power;
						$daikin->$k->mode=4;
						$daikin->$k->fan=$fan;
						$daikin->$k->set=$set;
						$daikin->$k->spmode=$spmode;
					}
				}
			} elseif (isset($power)&&$power==1&&$d['daikin']['s']=='Off'&&$pastdaikin>900) sw('daikin', 'On');
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
	
//	unset($daikin);
}
if ($difgas>=0&&$d['brander']['s']=='On'&&$d['badkamer_temp']['s']>12&&past('brander')>=595) sw('brander', 'Off');
elseif ($d['brander']['s']=='Off'&&$d['badkamer_temp']['s']<12&&past('brander')>=595) sw('brander', 'On');
