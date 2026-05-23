<?php
$user=basename(__FILE__);
if ($d['brander']->s!='Off') sw('brander', 'Off', $user.':'.__LINE__);
$bigdif=-100;
$daikinDefaults = ['power'=>99,'mode'=>99,'set'=>99,'fan'=>99,'spmode'=>99];
$daikin ??= new stdClass();
foreach (array('living','kamer','alex') as $kamer) {
	$daikin->$k ??= (object)$daikinDefaults;
	if ($d[$kamer.'_set']->s!='D') {
		${'dif'.$kamer}=number_format($d[$kamer.'_temp']->s-$d[$kamer.'_set']->s,1);
		if (${'dif'.$kamer}>$bigdif) $bigdif=${'dif'.$kamer};
	}
}
if ($bigdif>=2) $maxpow=100;
elseif ($bigdif>=0.5) $maxpow=60;
else $maxpow=40;
$maxpow=floor($maxpow/5)*5;
if ($d['weg']->s>0) $maxpow=40;
if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
elseif ($maxpow>=100) {$maxpow=100;$spmode=0;}
else $spmode=-1;
// KAMER
$k='kamer';
$Setkamer=33;
unset($power);


//	if (isset($power)) lg('kamer dif='.$difkamer.' power='.$power); else lg('kamer dif='.$difkamer);
if ($d['kamer_set']->s=='D') {
	if ($d['daikin']->s=='On') {
		$fan='A';
		$mode=2;
		$set='M';
		if (!isset($power)) $power=$daikin->$k->power;
		if (($daikin->$k->set!=$set||$daikin->$k->power!=$power||$daikin->$k->mode!=$mode||$daikin->$k->fan!=$fan)&&$powermode<2) {
		if(daikinset($k, $power, $mode, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
				$daikin->$k->power=$power;
				$daikin->$k->mode=$mode;
				$daikin->$k->fan=$fan;
				$daikin->$k->set=$set;
				$daikin->$k->spmode=$spmode;
				lg(print_r($daikin,true));
			}
		}
	} elseif (isset($power)&&$power==1&&$d['daikin']->s=='Off') {
		if (past('daikin')>900) {
			sw('daikin', 'On', $user.':'.__LINE__);
		}
	}
} elseif(past('raamkamer')>300&&past('deurkamer')>300) {
	$power=0;
	$mode=3;
	if ($daikin->$k->power!=$power||$daikin->$k->mode!=$mode) {
		if(daikinset($k, $power, $mode, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
			$daikin->$k->power=$power;
			$daikin->$k->mode=$mode;
			$daikin->$k->fan=$fan;
			$daikin->$k->set=$set;
			$daikin->$k->spmode=$spmode;
		}
	}
}
unset($power);
// ALEX
$k='alex';
$Setalex=33;


if ($d['alex_set']->s=='D') {
	if ($d['daikin']->s=='On') {
		$fan='A';
		$mode=2;
		$set='M';
		if (!isset($power)) $power=$daikin->$k->power;
		if (($daikin->$k->set!=$set||$daikin->$k->power!=$power||$daikin->$k->mode!=$mode||$daikin->$k->fan!=$fan)&&$powermode<2) {
			if(daikinset($k, $power, $mode, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
				$daikin->$k->power=$power;
				$daikin->$k->mode=$mode;
				$daikin->$k->fan=$fan;
				$daikin->$k->set=$set;
				$daikin->$k->spmode=$spmode;
				lg(print_r($daikin,true));
			}
		}
	} elseif (isset($power)&&$power==1&&$d['daikin']->s=='Off') {
		if (past('daikin')>900) sw('daikin', 'On', $user.':'.__LINE__);
	}
} elseif(past('raamalex')>300&&past('deuralex')>300) {
	$power=0;
	$mode=3;
	if ($daikin->$k->power!=$power||$daikin->$k->mode!=$mode) {
		if(daikinset($k, $power, $mode, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
			$daikin->$k->power=$power;
			$daikin->$k->mode=$mode;
			$daikin->$k->fan=$fan;
			$daikin->$k->set=$set;
			$daikin->$k->spmode=$spmode;
		}
	}
}
unset($power);
// LIVING
$k='living';
$Setliving=33;
//	lg($user.':'.__LINE__);


//	if (isset($power)) lg('living dif='.$dif.' power='.$power); else lg('living dif='.$dif);
if ($d['living_set']->s=='D') {
//	lg($user.':'.__LINE__,'daikin');
	if ($d['daikin']->s=='On') {
//			lg($user.':'.__LINE__);
		$fan='A';
		if ($d['eettafel']->s>0) $fan='B';
		elseif ($d['eettafel']->s>0) $fan='B';
		elseif ($d['lgtv']->s=='On') $fan='B';
		$mode=2;
		$set='M';
		$spmode=-1;
		if (!isset($power)) $power=$daikin->$k->power;

		if (($daikin->$k->set!=$set||$daikin->$k->power!=$power||$daikin->$k->mode!=$mode||$daikin->$k->fan!=$fan)&&$powermode<2) {
			if(daikinset($k, $power, $mode, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
				$daikin->$k->power=$power;
				$daikin->$k->mode=$mode;
				$daikin->$k->fan=$fan;
				$daikin->$k->set=$set;
				$daikin->$k->spmode=$spmode;
				lg(print_r($daikin,true));
			}
		}
	} elseif (isset($power)&&$power==1&&$d['daikin']->s=='Off') {
		if (past('daikin')>900) sw('daikin', 'On', $user.':'.__LINE__);
	}
} elseif (past('raamliving')>300&&past('raamkeuken')>300&&past('deurgarage')>300) {
	$power=0;
	$mode=3;
	if ($daikin->$k->power!=$power||$daikin->$k->mode!=$mode) {
		if(daikinset($k, $power, $mode, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
			$daikin->$k->power=$power;
			$daikin->$k->mode=$mode;
			$daikin->$k->fan=$fan;
			$daikin->$k->set=$set;
			$daikin->$k->spmode=$spmode;
		}
	}
}


require('_Rolluiken_Cooling.php');
require('_TC_badkamer.php');