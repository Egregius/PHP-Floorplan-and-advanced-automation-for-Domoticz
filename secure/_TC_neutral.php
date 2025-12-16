<?php

if ($d['brander']['s']!='Off') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['daikin']['s']=='On'&&$d['daikin']['m']==1) {
	$daikinDefaults = ['power'=>99,'mode'=>99,'set'=>99,'fan'=>99,'spmode'=>99];
	$daikin ??= new stdClass();
	foreach (array('living', 'kamer', 'alex') as $k) {
		$daikin->$k ??= (object)$daikinDefaults;
		if ($daikin->$k->power!=0&&$daikin->$k->mode!=3) {
			if(daikinset($k, 0, 3, 20, basename(__FILE__).':'.__LINE__)) {
				$daikin->$k->power=0;
				$daikin->$k->mode=3;
			}
		}
	}
}

$boven=array('rwaskamer','ralex','rkamerl','rkamerr');
$beneden=array('rbureel','rkeukenl','rkeukenr');
$benedenall=array('rliving','rbureel','rkeukenl','rkeukenr');
if ($d['minmaxtemp']['s']>20||$d['minmaxtemp']['m']>22) $warm=true; else $warm=false;
if ($d['minmaxtemp']['s']<5&&$d['minmaxtemp']['m']<5) $koud=true; else $koud=false;

require('_Rolluiken_Neutral.php');
require('_TC_badkamer.php');
