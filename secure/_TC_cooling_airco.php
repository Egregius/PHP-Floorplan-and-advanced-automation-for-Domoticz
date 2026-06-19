<?php
$user=basename(__FILE__);
if ($d['brander']->s!='Off') sw('brander', 'Off', $user.':'.__LINE__);
$bigdif=-100;
$daikinDefaults = ['power'=>99,'mode'=>99,'set'=>99,'fan'=>99,'spmode'=>99];
$daikin ??= new stdClass();
foreach (['living','kamer','alex'] as $k) {
	$daikin->$k ??= (object)$daikinDefaults;
	if ($d[$k.'_set']->s!='D'&&$d[$k.'_set']->s!='Off') {
		${'dif'.$k}=number_format($d[$k.'_temp']->s-$d[$k.'_set']->s,1);
		if (${'dif'.$k}>$bigdif) $bigdif=${'dif'.$k};
	}
}
if ($bigdif>=2) $maxpow=100;
elseif ($bigdif>=1.5) $maxpow=80;
elseif ($bigdif>=1) $maxpow=60;
elseif ($bigdif>=0.5) $maxpow=50;
else $maxpow=40;
if ($d['weg']->s>0) $maxpow=40;
if ($maxpow<=40) {$maxpow=40;$spmode=-1;}
elseif ($maxpow>=100) {$maxpow=100;$spmode=0;}
else $spmode=-1;


foreach (['living','kamer','alex'] as $k) {
	$fan='A';
	if (($d[$k.'_set']->m==0||$d[$k.'_set']->m==2)&&($d['raam'.$k]->s=='Closed'||($d['raam'.$k]->s=='Open'&&past('raam'.$k)<=60))) {
		$mode=2;
		$power=1;
		$set=22;
		if ($d[$k.'_set']->s!='D') store($k.'_set','D',basename(__FILE__).':'.__LINE__);
	} elseif ($d[$k.'_set']->m==1&&$d[$k.'_set']->s<33&&($d['raam'.$k]->s=='Closed'||($d['raam'.$k]->s=='Open'&&past('raam'.$k)<=60))) {
		$mode=3;
		$power=1;
		if($d[$k.'_set']->s==1) {
			$fan=3;
			$set=18;
		} elseif($d[$k.'_set']->s==2) {
			$fan=4;
			$set=18;
			$maxpow=50;
		} elseif($d[$k.'_set']->s==3) {
			$fan=5;
			$set=18;
			$maxpow=60;
		} elseif($d[$k.'_set']->s==4) {
			$fan=6;
			$set=18;
			$spmode=0;
			$maxpow=80;
		} elseif($d[$k.'_set']->s==5) {
			$fan=7;
			$set=18;
			$spmode=1;
			$maxpow=100;
		}
		$set=$d[$k.'_set']->s;
	} else {
		$mode=2;
		$power=0;
		$set=33;
		if ($d[$k.'_set']->s!='Off') store($k.'_set','Off',basename(__FILE__).':'.__LINE__);
	}
	if ($d['daikin']->s=='On') {
		if ((($daikin->$k->set!=$set||$daikin->$k->power!=$power||$daikin->$k->mode!=$mode||$daikin->$k->fan!=$fan)&&$spmode<2)||(($d['daikin']->s=='On'&&$power!=0&&$daikin->$k->lastset <= $time-281)||($d['daikin']->s=='On'&&$power==0&&$daikin->$k->lastset <= $time-281))) {
			if(daikinset($k, $power, $mode, $set, basename(__FILE__).':'.__LINE__, $fan, $spmode, $maxpow)) {
				$daikin->$k->power=$power;
				$daikin->$k->mode=$mode;
				$daikin->$k->fan=$fan;
				$daikin->$k->set=$set;
				$daikin->$k->spmode=$spmode;
				$daikin->$k->lastset=$time;
	
			}
		}
	} elseif ($power==1 && $d['daikin']->s=='Off' && past('daikin')>900) {
		sw('daikin','On');
	}
}


require('_Rolluiken_Cooling.php');
require('_TC_badkamer.php');