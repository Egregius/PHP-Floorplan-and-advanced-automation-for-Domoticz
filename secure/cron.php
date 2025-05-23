#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
if (!isset($d)) $d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$lastfetch=time();
$user='CRONstart';

$items=array('badkamervuur2','badkamervuur1','water');
foreach ($items as $i) {
	sw($i, 'Off', basename(__FILE__).':'.__LINE__,true);
}
if ($d['Weg']['s']>0) {
	$items=array('boseliving','bosekeuken','ipaddock','mac','media');
	foreach ($items as $i) {
		sw($i, 'Off', basename(__FILE__).':'.__LINE__,true);
	}
}

if (isset($argv[1])) {
	echo 'Executing _cron'.$argv[1].'.php'.PHP_EOL.PHP_EOL;
	include '_cron10.php';
	include ('_cron'.$argv[1].'.php');
} else {
	lg('Starting CRON loop...',9);
	while (1){
		$start=microtime(true);
		$time=time();
		$d=fetchdata($lastfetch,basename(__FILE__).':'.__LINE__);
		$lastfetch=$time;
		$crontime=$time;
		if ($crontime%10==0) {
			include '_cron10.php';
			$user=' TC '.$d['heating']['s'];
//			$s=date('s');
			$t=t();
			if ($d['heating']['s']==-2) include '_TC_cooling_airco.php';
			elseif ($d['heating']['s']==-1) include '_TC_cooling_passive.php';
			elseif ($d['heating']['s']==0) include '_TC_neutral.php';
			elseif ($d['heating']['s']>0) include '_TC_heating.php';
		}
		if ($crontime%60==0) include '_cron60.php';
		if ($crontime%120==0) include '_cron120.php';
		if ($crontime%180==0) include '_cron180.php';
		if ($crontime%240==0) include '_cron240.php';
		if ($crontime%300==0) include '_cron300.php';
		if ($crontime%450==0) include '_cron450.php';
		if ($crontime%3600==0) include '_cron3600.php';
		if ($crontime%105==0) include '_weather.php';
		$time_elapsed_secs=microtime(true)-$start;
		if ($time_elapsed_secs<1) usleep(round(1000*(1000-($time_elapsed_secs*1000))));
	}
}
