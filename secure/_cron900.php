<?php
setNextubeMode();

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

$minDailyRuntime = 4 * 3600;
$forceHour = 14;
$needsRuntime = $poolRuntime['seconds'] < $minDailyRuntime;
$mustForceNow = $needsRuntime && (int)date('G') >= $forceHour;

$onWindow  = socAdjustedWindow($d['c'], 12, 3);
$offWindow = socAdjustedWindow($d['c'], 3, 12);

if ($d['steenterras']->s=='Off' && (
        ($d['c']>30 && rollingAbove('b', 0, $onWindow) && rollingBelow('n', -1200, $onWindow))
        || $mustForceNow
    )) {
    sw('steenterras', 'On', basename(__FILE__).':'.__LINE__);
    $steenautomatischaan = true;
} elseif ($d['steenterras']->s=='On' && $steenautomatischaan==true && (rollingAbove('n', 0, $offWindow)||rollingAbove('n', 1000, 90, 'any')||$d['a'] > 1000) && !$needsRuntime) {
    sw('steenterras', 'Off', basename(__FILE__).':'.__LINE__);
    $steenautomatischaan = false;
}

file_put_contents(POOL_RUNTIME_FILE, json_encode($poolRuntime));