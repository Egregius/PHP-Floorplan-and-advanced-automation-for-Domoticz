#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
lg('Starting CRON loop...',9);
$t = null;
$weekend = null;

$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$time=time();
define('LOOP_START', $time);

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

$last10 = $last60 = $last120 = $last180 = $last240 = $last300 = $last3600 = $last450 = $last100 = 0;

while (true) {
    $start = microtime(true);
    $time = time();

    // --- 10s interval ---
    if (($time % 10 === 0) && $last10 !== $time) {
        $last10 = $time;
        $d = fetchdata($lastfetch, basename(__FILE__).':'.__LINE__);
        $d['time'] = $time;
        $lastfetch = $time - 20;
        include '_cron10.php';
        $user='heating';
        updateWekker($t, $weekend);

        if ($time % 20 === 0) {
            if ($d['heating']['s']==-2) include '_TC_cooling_airco.php';
            elseif ($d['heating']['s']==-1) include '_TC_cooling_passive.php';
            elseif ($d['heating']['s']==0) include '_TC_neutral.php';
            elseif ($d['heating']['s']>0) include '_TC_heating.php';
        }
    }

    // --- overige intervallen ---
    if (($time % 60 === 0) && $last60 !== $time) { $last60 = $time; include '_cron60.php'; }
    if (($time % 120 === 0) && $last120 !== $time) { $last120 = $time; include '_cron120.php'; }
    if (($time % 180 === 0) && $last180 !== $time) { $last180 = $time; include '_cron180.php'; }
    if (($time % 240 === 0) && $last240 !== $time) { $last240 = $time; include '_cron240.php'; }
    if (($time % 300 === 0) && $last300 !== $time) { $last300 = $time; include '_cron300.php'; }
    if (($time % 3600 === 0) && $last3600 !== $time) { $last3600 = $time; include '_cron3600.php'; }
    if (($time % 450 === 0) && $last450 !== $time) { $last450 = $time; include '_cron450.php'; }
    if (($time % 100 === 0) && $last100 !== $time) { $last100 = $time; include '_weather.php'; }

    stoploop($d);

    // --- CPU-vriendelijk --- 
    $elapsed = microtime(true) - $start;
    if ($elapsed < 1) usleep((int)((1 - $elapsed) * 1000000));

}


function stoploop($d) {
    global $db;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('functions.php gewijzigd → restarting cron loop...');
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime(__DIR__ . '/cron.php') > LOOP_START) {
        lg('cron.php gewijzigd → restarting cron loop...');
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
	if ($d['weg']['m']==2) {
		lg('Stopping CRON Loop...');
		$db->query("UPDATE devices SET m=0 WHERE n ='weg';");
		exit('Stop');
		die('Stop');
	}
}