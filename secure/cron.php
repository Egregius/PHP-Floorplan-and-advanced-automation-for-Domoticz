#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting cron loop...');
if (isset($argv[0])) include ('_cron'.$argv[1].'.php');
else {
	while (1){
		$time=time();
		if ($time%10==0) include '_cron10.php';
		if ($time%20==0) {
			$s=(int)strftime("%S", $time);
			$dow=date("w");
			if($dow==0||$dow==6) $t=strtotime('7:30');
			elseif($dow==2||$dow==5) $t=strtotime('6:45');
			else $t=strtotime('7:00');

			$dag=0;
			if ($time>=$d['civil_twilight']['s']&&$time<=$d['civil_twilight']['m']) {
				$dag=1;
				if ($time>=$d['Sun']['s']&&$time<=$d['Sun']['m']) {
					if ($time>=$d['Sun']['s']+900&&$time<=$d['Sun']['m']-900) $dag=4;
					else $dag=3;
				} else {
					$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
					$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
					if ($time>=$zonop&&$time<=$zononder) $dag=2;
				}
			}
			if ($d['heating']['s']==-2) include '_TC_cooling_airco.php';
			elseif ($d['heating']['s']==-1) include '_TC_cooling_passive.php';
			elseif ($d['heating']['s']==0) include '_TC_neutral.php';
			elseif ($d['heating']['s']>0) include '_TC_heating.php';
		}
		if ($time%60==0) include '_cron60.php';
		if ($time%86==0) include '_weather.php';
		if ($time%120==0) include '_cron120.php';
		if ($time%240==0) include '_cron240.php';
		if ($time%300==0) include '_cron300.php';
		if ($time%3600==0) include '_cron3600.php';
		sleep(1);
	}
}