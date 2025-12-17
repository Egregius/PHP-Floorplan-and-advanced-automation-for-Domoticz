<?php
$user='cron3600';
//lg($user);

if (date('G')==0) {
	apcu_store('alwayson',9999);
//	store('gasvandaag', 0, basename(__FILE__).':'.__LINE__);
//	store('watervandaag', 0, basename(__FILE__).':'.__LINE__);
}

if ($d['weg']['s']==0) {
	foreach (array('living_temp','kamer_temp','alex_temp','badkamer_temp') as $i) {
		if (past($i)>43150) alert($i,$i.' not updated since '.date("G:i:s", $d[$i]['t']),7200);
	}
}

$since=date("Y-m-d G:i:s", $time-86400);
foreach (array('01','02','03','04','06','07','08','09',11,12,13,14,16,17,18,19,21,22,23,24,26,27,28,29,31,32,33,34,36,37,38,39,41,42,43,44,46,47,48,49,51,52,53,54,56,57,58,59) as $x) {
	$query="DELETE FROM `temp` WHERE `stamp` LIKE '%:$x:00' AND `stamp` < '$since'";
	echo $query.PHP_EOL;
	$db->query($query);
}

/* Clean old database records */

$remove=date('Y-m-d H:i:s', $time-(86400*100));
$stmt=$db->query("delete from temp where stamp < '$remove'");

$url = "https://api.sunrise-sunset.org/json?lat=$lat&lng=$lon&formatted=0";
$response = @file_get_contents($url);
$data = json_decode($response, true);
if (isset($data['results'])) {
	$results = $data['results'];
	$CivTwilightStart = isoToLocalTimestamp($results['civil_twilight_begin']);
	$CivTwilightEnd = isoToLocalTimestamp($results['civil_twilight_end']);
	$Sunrise = isoToLocalTimestamp($results['sunrise']);
	$Sunset = isoToLocalTimestamp($results['sunset']);
	setCache('sunrise', json_encode(array(
		'CivTwilightStart' => date('G:i', $CivTwilightStart),
		'CivTwilightEnd' => date('G:i', $CivTwilightEnd),
		'Sunrise' => date('G:i', $Sunrise),
		'Sunset' => date('G:i', $Sunset),
	)));
}



$stamp = date("Y-m-d H:i:s", (int)$d['time'] - 86400*7);
$cols = [
    'buiten' => 'buiten_temp',
];
$query = "SELECT ";
$selects = [];
foreach($cols as $col => $jsonKey) {
    $selects[] = "MIN($col) AS {$col}_min, AVG($col) AS {$col}_avg, MAX($col) AS {$col}_max";
}
$query .= implode(", ", $selects);
$query .= " FROM temp WHERE stamp > :stamp";
$stmt = $db->prepare($query);
$stmt->execute([':stamp' => $stamp]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$thermo_hist = [];
foreach($cols as $col => $jsonKey) {
    $thermo_hist[$jsonKey] = [
        'min' => round((float)$row["{$col}_min"],1),
        'avg' => round((float)$row["{$col}_avg"],1),
        'max' => round((float)$row["{$col}_max"],1),
    ];
}
$cols = [
    'living' => 'living_temp',
    'badkamer' => 'badkamer_temp',
];
foreach($cols as $col => $jsonKey) {
    $thermo_hist[$jsonKey] = [
        'min' => 12,
        'avg' => 20,
        'max' => 24,
    ];
}
$cols = [
    'kamer' => 'kamer_temp',
    'waskamer' => 'waskamer_temp',
    'alex' => 'alex_temp',
];
foreach($cols as $col => $jsonKey) {
    $thermo_hist[$jsonKey] = [
        'min' => 10,
        'avg' => 16-$d['heating']['s'],
        'max' => 20,
    ];
}
echo setCache('thermo_hist',json_encode($thermo_hist));
