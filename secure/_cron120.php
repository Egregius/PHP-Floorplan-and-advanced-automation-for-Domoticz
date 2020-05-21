<?php
/**
 * Pass2PHP 
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$user='cron120';

if ($d['auto']['s']=='On') {
	$windhist=json_decode($d['wind']['m']);
	$x=0;
	foreach ($windhist as $y) {
		$x=$y+$x;
		$windhist=round($x/4, 2);
	}
	if (	(($d['heating']['s']==-1&&$d['living_temp']['s']>=20.5)||($d['heating']['s']==-2	&&$d['living_temp']['s']>=19.5))&&$d['zon']['s']>1000&&TIME>=strtotime("10:00")&&$d['buien']['s']<10) { //Cooling
		if ($d['wind']['s']>=30) 	 $luifel=0;
		elseif ($d['wind']['s']>=25) $luifel=25;
		elseif ($d['wind']['s']>=20) $luifel=30;
		elseif ($d['wind']['s']>=15) $luifel=35;
		elseif ($d['wind']['s']>=10) $luifel=40;
		else $luifel=45;
		if (		past('luifel')>900&&$d['luifel']['s']<$luifel&&$d['luifel']['m']==0&&$d['wind']['s']<$windhist) {
			sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		} elseif (	past('luifel')>300&&$d['luifel']['s']>$luifel&&$d['luifel']['m']==0) {
			sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	} elseif ($d['heating']['s']==0	&&$d['living_temp']['s']>22&&$d['zon']['s']>2000&&TIME>=strtotime("10:00")&&$d['buien']['s']<10) { //Neutral
		if ($d['wind']['s']>=30) 	 $luifel=0;
		elseif ($d['wind']['s']>=25) $luifel=25;
		elseif ($d['wind']['s']>=20) $luifel=30;
		elseif ($d['wind']['s']>=15) $luifel=30;
		elseif ($d['wind']['s']>=10) $luifel=30;
		else $luifel=40;
		if (		past('luifel')>900&&$d['luifel']['s']<$luifel&&$d['luifel']['m']==0&&$d['wind']['s']<$windhist) {
			sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		} elseif (	past('luifel')>300&&$d['luifel']['s']>$luifel&&$d['luifel']['m']==0) {
			sl('luifel', $luifel, basename(__FILE__).':'.__LINE__);
		}
	} 
	
	if ($d['buien']['s']>=10) {
		sl('luifel', 0, basename(__FILE__).':'.__LINE__);
	}
	if ($d['luifel']['m']==1) {
		if (past('luifel')>3600&&$luifel<30&&$d['achterdeur']['s']=='Closed') {
			storemode('luifel', 0, basename(__FILE__).':'.__LINE__);
			$d['luifel']['m']=1;
		} elseif (past('luifel')>28800) {
			storemode('luifel', 0, basename(__FILE__).':'.__LINE__);
			$d['luifel']['m']=1;
		}
	}
}
