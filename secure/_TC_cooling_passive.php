<?php
if ($d['brander']['s']=='On') sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
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
require('_Rolluiken_Cooling.php');
require('_TC_badkamer.php');
