<?php
$user='cron300';
//lg('🕒 | Variabelen: ' . convertbytes($total_var_size) . ' | Intern: ' . convertbytes(memory_get_usage(false)) . ' | Systeem: ' . convertbytes(memory_get_usage(true)).' ---------------------------------------------------------','cron300');

// BEGIN EERSTE BLOK INDIEN ZWEMBAD
/*if ($d['steenterras']->s=='On') {
	if (past('steenterras')>10700
		&&$time>strtotime("16:00")
		&&$d['houtterras']->s=='Off'
		&&$d['buiten_temp']->s<27
	) {
		sw('steenterras','Off', basename(__FILE__).':'.__LINE__);
	}
}else{
	if (
			(past('steenterras')>10700&&$time>strtotime("12:59")&&$time<strtotime("15:59"))
			||
			(past('steenterras')>10700&&$d['buiten_temp']->s>27)
	   ) {
	   	sw('steenterras','On', basename(__FILE__).':'.__LINE__);
	}
}
if ($d['houtterras']->s=='On') {
	if (past('houtterras')>86398) {
		sw('houtterras','Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['steenterras']->s=='Off') {
		sw('steenterras','On', basename(__FILE__).':'.__LINE__);
	}
}*/
//EINDE EERSTE BLOK INDIEN ZWEMBAD

// BEGIN TWEEDE BLOK INDIEN GEEN ZWEMBAD
//if ($d['achterdeur']->s=='Open') {
//	if ($d['steenterras']->s=='Off') sw('steenterras','On', basename(__FILE__).':'.__LINE__);
//	if ($d['houtterras']->s=='Off') sw('houtterras','On', basename(__FILE__).':'.__LINE__);
//} else {
//	if ($d['steenterras']->s=='On') sw('steenterras','Off', basename(__FILE__).':'.__LINE__);
//	if ($d['houtterras']->s=='On') sw('houtterras','Off', basename(__FILE__).':'.__LINE__);
//}
//EINDE TWEEDE BLOK INDIEN GEEN ZWEMBAD

if ($d['weg']->s>0) {
	if ($d['kookplaat']->s=='On') sw('kookplaat', 'Off', basename(__FILE__).':'.__LINE__);
	if ($d['dysonlader']->s=='On') sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
//	if ($d['steenterras']->s=='On') sw('steenterras','Off', basename(__FILE__).':'.__LINE__);
	if ($d['tuintafel']->s=='On') sw('tuintafel','Off', basename(__FILE__).':'.__LINE__);
	if ($d['weg']->s>1) {
		foreach (['living_set','alex_set','kamer_set','badkamer_set'/*,'eettafel','zithoek'*/,'luifel'] as $i) {
			if ($d[$i]->m!=0&&$d[$i]->s!='D'&&past($i)>43200) storemode($i, 0, basename(__FILE__).':'.__LINE__);
		}
	}
} else {
	if ($d['dysonlader']->s=='On'&&past('dysonlader')>3600) sw('dysonlader', 'Off', basename(__FILE__).':'.__LINE__);
}


if ($d['auto']->s!='On'&&past('auto')>43200) {
	sw('auto', 'On', basename(__FILE__).':'.__LINE__);
	alert('AUTO','AUTO ingeschakeld na 12 uur',60,false,3);
}

if ($d['zolderg']->s=='On'&&past('zolderg')>7200&&past('pirgarage')>7200) sw('zolderg', 'Off', basename(__FILE__).':'.__LINE__);



republishmqtt();


if ($d['weg']->s==0&&$d['badkamerpower']->s=='Off'&&$d['Egregius']->s!=0&&$time>=$t&&$time<strtotime('9:00')) {
	shell_exec('php /var/www/setSSID.php \'{"main24":0}\' > /dev/null 2>&1 &');
	store('Egregius',0,basename(__FILE__).':'.__LINE__);
	lg('main24 uitgeschakeld','cron300');
}

if ($poolRuntime['date'] !== date('Y-m-d')) {
    $poolRuntime = ['date' => date('Y-m-d'), 'seconds' => 0, 'lastCheck' => $time];
}

$now = $time;
$sinceLastCheck = $now - $poolRuntime['lastCheck'];

if ($d['steenterras']->s == 'On') {
    $onDuration = min($sinceLastCheck, past('steenterras'));
    $poolRuntime['seconds'] += $onDuration;
}
$poolRuntime['lastCheck'] = $now;

$minDailyRuntime = 3 * 3600;
$forceHour = 13;
$needsRuntime = $poolRuntime['seconds'] < $minDailyRuntime;
$mustForceNow = $needsRuntime && (int)date('G') >= $forceHour;

$onWindow  = socAdjustedWindow($d['c'], 12, 3);
$offWindow = socAdjustedWindow($d['c'], 3, 12);

if ($d['steenterras']->s=='Off' && (
        	($d['c']>30 && rollingAbove('b', 0, $onWindow) && rollingBelow('n', -1200, $onWindow))
		||	($d['c']>30 && rollingAbove('b', 200, $onWindow) && rollingBelow('n', -1000, $onWindow))
		||	($d['c']>30 && rollingAbove('b', 400, $onWindow) && rollingBelow('n', -800, $onWindow))
		||	($d['c']>30 && rollingAbove('b', 600, $onWindow) && rollingBelow('n', -600, $onWindow))
        ||	$mustForceNow
    )) {
    sw('steenterras', 'On', basename(__FILE__).':'.__LINE__);
    $steenautomatischaan = true;
} elseif ($d['steenterras']->s=='On' && $steenautomatischaan==true && (rollingAbove('n', 0, $offWindow)||rollingAbove('n', 1000, 90, 6)||$d['a'] > 1000) && !$needsRuntime) {
    sw('steenterras', 'Off', basename(__FILE__).':'.__LINE__);
    $steenautomatischaan = false;
}

file_put_contents('/dev/shm/cache/poolRuntime.json', json_encode($poolRuntime));