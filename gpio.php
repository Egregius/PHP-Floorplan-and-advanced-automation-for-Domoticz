<?php
/**
 * Pass2PHP
 * php version 7.3.9-1
 *
 * This file is called by a secondary Domoticz running on a Rasperry Pi
 * It handles some GPIO's that has sensors on it for gas and water meter counting
 * Also the garage door is connected to it.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
if (isset($_REQUEST['gpio'])) {
	require_once 'secure/functions.php';
	$d=fetchdata();
	$gpio=$_REQUEST['gpio'];
	if ($gpio==20) {
		store('gasvandaag', $d['gasvandaag']['s']+1, basename(__FILE__).':'.__LINE__);
		if ($d['lichtbadkamer']['s']>0&&((past('gasvandaag')<240&&past('watervandaag')<240&&$d['heating']['s']>=2)||$d['heating']['s']<=1)) {
			store('douche', $d['douche']['s']+1, basename(__FILE__).':'.__LINE__);
			$d['douche']['s']=$d['douche']['s']+1;
		} elseif ($d['brander']['s']=='On'&&$d['living_temp']['s']>$d['living_set']['s']) sw('brander', 'Off',basename(__FILE__).':'.__LINE__);
	} elseif ($gpio==21) {
		store('watervandaag', $d['watervandaag']['s']+1, basename(__FILE__).':'.__LINE__);
		if ($d['lichtbadkamer']['s']>0&&((past('gasvandaag')<240&&past('watervandaag')<240&&$d['heating']['s']>=2)||$d['heating']['s']<=1)) {
			storemode('douche', $d['douche']['m']+1, basename(__FILE__).':'.__LINE__, 1);
			$d['douche']['m']=$d['douche']['m']+1;
		}
	} elseif ($gpio==19) {
		if ($_REQUEST['action']=='on') store('poort', 'Closed', basename(__FILE__).':'.__LINE__);
		else {
			store('poort', 'Open', basename(__FILE__).':'.__LINE__);
			if ($d['voordeur']['s']=='On') sw('voordeur', 'Off',basename(__FILE__).':'.__LINE__);
			if ($d['dampkap']['s']=='On') sw('dampkap', 'Off',basename(__FILE__).':'.__LINE__);
			sirene('Poort open');
			fgarage();
		}
	} else die('Unknown');
	echo 'ok';
}
