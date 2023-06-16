#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';

if (isset($argv[1])) {
	if ($argv[1]=='test') {
		mset('lgtv-offline', 10);
		echo mget('lgtv-offline');
	} else {
		lg('Executing _cron'.$argv[1].'.php');
		include ('_cron'.$argv[1].'.php');
	}
} else {
	lg('Starting cron loop...');
	while (1){
		$time=time();
		$crontime=$time;
		if ($crontime%10==0) include '_cron10.php';
		if ($crontime%20==0) {
			$s=(int)strftime("%S", $time);
			$dow=date("w");
			if($dow==0||$dow==6) $t=strtotime('7:30');
			elseif($dow==2||$dow==5) $t=strtotime('6:45');
			else $t=strtotime('7:00');

			$dag=dag();
			if ($d['heating']['s']==-2) include '_TC_cooling_airco.php';
			elseif ($d['heating']['s']==-1) include '_TC_cooling_passive.php';
			elseif ($d['heating']['s']==0) include '_TC_neutral.php';
			elseif ($d['heating']['s']>0) include '_TC_heating.php';
		}
		if ($crontime%60==0) include '_cron60.php';
		if ($crontime%86==0) include '_weather.php';
		if ($crontime%120==0) include '_cron120.php';
		if ($crontime%240==0) include '_cron240.php';
		if ($crontime%300==0) include '_cron300.php';
		if ($crontime%3600==0) include '_cron3600.php';
		sleep(1);
	}
}
