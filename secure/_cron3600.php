<?php
$user='cron3600';
//lg($user);

if (date('G')==0||LOOP_START>$time-60) {
	lg($user);
	if (date('G')==0) {
		apcu_store('alwayson',9999);
		setCache('alwayson', 9999);
	}
//	store('gasvandaag', 0, basename(__FILE__).':'.__LINE__);
//	store('watervandaag', 0, basename(__FILE__).':'.__LINE__);




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
		$stamp = date("Y-m-d H:i:s", (int)$d['time'] - 86400*7);
		$query = "SELECT MIN(buiten) AS min, ROUND(AVG(buiten), 2) AS avg, MAX(buiten) AS max FROM temp WHERE stamp > :stamp";
		$stmt = $db->prepare($query);
		$stmt->execute([':stamp' => $stamp]);
		$b_hist = $stmt->fetch(PDO::FETCH_ASSOC);
		$results = $data['results'];
		$map = [
			'PRESET_1' => 'EDM-1',
			'PRESET_2' => 'EDM-2',
			'PRESET_3' => 'EDM-3',
			'PRESET_4' => 'MIX-1',
			'PRESET_5' => 'MIX-2',
			'PRESET_6' => 'MIX-3',
		];
		$CivTwilightStart = isoToLocalTimestamp($results['civil_twilight_begin']);
		$CivTwilightEnd = isoToLocalTimestamp($results['civil_twilight_end']);
		$Sunrise = isoToLocalTimestamp($results['sunrise']);
		$Sunset = isoToLocalTimestamp($results['sunset']);
		$data=[
			'Tstart'=>date('G:i', $CivTwilightStart),
			'Srise'=>date('G:i', $Sunrise),
			'Sset'=>date('G:i', $Sunset),
			'Tend'=>date('G:i', $CivTwilightEnd),
			'b_hist'=>json_encode($b_hist),
			'pl'=>$map[boseplaylist($time)],
		];
		publishmqtt('d/daily',json_encode($data));
	}
}

if ($d['weg']['s']==0) {
	foreach (array('living_temp','kamer_temp','alex_temp','badkamer_temp') as $i) {
		if (past($i)>43150) alert($i,$i.' not updated since '.date("G:i:s", $d[$i]['t']),7200);
	}
}