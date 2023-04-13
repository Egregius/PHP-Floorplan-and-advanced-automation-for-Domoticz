<?php
require '/var/www/html/secure/functions.php';
$d=fetchdata();
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) {
	$dag=1;
	if (TIME>=$d['Sun']['s']&&TIME<=$d['Sun']['m']) {
		if (TIME>=$d['Sun']['s']+900&&TIME<=$d['Sun']['m']-900) $dag=4;
		else $dag=3;
	} else {
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		if (TIME>=$zonop&&TIME<=$zononder) $dag=2;
	}
}

$a=$_REQUEST['a'];
if ($a=='kodi_paused') {
	fkeuken();
} elseif ($a=='kodi_play') {
	if ($d['wasbak']['s']>0&&TIME>strtotime('10:00')) {
		sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
		sleep(1);
		sl('wasbak', 0, basename(__FILE__).':'.__LINE__);
	}
}