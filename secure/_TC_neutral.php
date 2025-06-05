<?php
$user=basename(__FILE__);
if ($d['brander']['s']!='Off') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);

if ($d['daikin']['s']=='On'&&$d['daikin']['m']==1) {
	foreach (array('living', 'kamer', 'alex') as $k) {
		$daikin=json_decode($d['daikin'.$k]['s']);
		if ($daikin->power!=0&&$daikin->mode!=3) {
			daikinset($k, 0, 3, 20, basename(__FILE__).':'.__LINE__);
			storemode('daikin'.$k, 0);
			storeicon($k.'_set', 'Off');
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
