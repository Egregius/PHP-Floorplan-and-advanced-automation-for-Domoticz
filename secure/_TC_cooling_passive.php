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
$maxpow=40;$spmode=-1;


foreach (['living','kamer','alex'] as $k) {
	$mode=2;
	$power=0;
	$set=33;
	if ($d[$k.'_set']->s!='Off') store($k.'_set','Off',basename(__FILE__).':'.__LINE__);
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
	} elseif (isset($power) && $power==1 && $d['daikin']->s=='Off' && past('daikin')>900) {
		sw('daikin','On');
	}
	unset($power);
}
require('_Rolluiken_Cooling.php');
require('_TC_badkamer.php');