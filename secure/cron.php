#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';

$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$time=time();
$lastfetch=$time-20;
$user='CRONstart';

$items=array('badkamervuur2','badkamervuur1','water');
foreach ($items as $i) {
	sw($i, 'Off', basename(__FILE__).':'.__LINE__,true);
}
if ($d['weg']['s']>0) {
	$items=array('boseliving','bosekeuken','ipaddock','mac','media','zetel');
	foreach ($items as $i) {
		sw($i, 'Off', basename(__FILE__).':'.__LINE__,true);
	}
}

lg('Starting CRON loop...',9);
while (1){
	$start=microtime(true);
	$time=time();
	if ($time%10==0) {
		$d=fetchdata($lastfetch,basename(__FILE__).':'.__LINE__);
		$d['time']=$time;
		$lastfetch=$time-20;
		include '_cron10.php';
		$user='heating';
//			$s=date('s');
		$t=t();
		if ($d['heating']['s']==-2) include '_TC_cooling_airco.php';
		elseif ($d['heating']['s']==-1) include '_TC_cooling_passive.php';
		elseif ($d['heating']['s']==0) include '_TC_neutral.php';
		elseif ($d['heating']['s']>0) include '_TC_heating.php';
		if ($time%60==0) {
			include '_cron60.php';
			if ($time%120==0) include '_cron120.php';
			if ($time%180==0) include '_cron180.php';
			if ($time%240==0) include '_cron240.php';
			if ($time%300==0) include '_cron300.php';
			if ($time%3600==0) include '_cron3600.php';
			if ($time%1800==0) sync_devices_if_changed($db, $d);
		}
		if ($time%450==0) include '_cron450.php';
		if ($time%100==0) include '_weather.php';
	}
	
	$time_elapsed_secs=microtime(true)-$start;
	if ($time_elapsed_secs<1) usleep(round(1000*(1000-($time_elapsed_secs*1000))));
}
