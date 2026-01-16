<?php
$user='cron3600';
//lg($user);
$uur=date('G');
if ($uur==0||LOOP_START>$time-60) {
	lg('🕒 '.$user.' ===================================================================================================================================================');
	if ($uur==0) {
		setCache('alwayson', 9999);
	}
	$url = "https://api.sunrise-sunset.org/json?lat=$lat&lng=$lon&formatted=0";
	$response = @file_get_contents($url);
	$data = json_decode($response, true);
	if (isset($data['results'])) {
		$today = date('Y-m-d');
		$dy = (int)date('z', strtotime($today));
		$range = 15;
		$start = $dy - $range;
		$end   = $dy + $range;
		if ($start < 0 || $end > 365) {
			$stmt = $db->prepare("
				SELECT
					ROUND(AVG(min_buiten),1) AS m,
					ROUND(AVG(avg_buiten),2) AS a,
					ROUND(AVG(max_buiten),1) AS x
				FROM temp_hist
				WHERE (DAYOFYEAR(datum) >= :start OR DAYOFYEAR(datum) <= :end)
			");
			$stmt->execute([
				':start' => $start < 0 ? 365 + $start : $start,
				':end'   => $end > 365 ? $end - 365 : $end
			]);
		} else {
			$stmt = $db->prepare("
				SELECT
					ROUND(AVG(min_buiten),1) AS m,
					ROUND(AVG(avg_buiten),2) AS a,
					ROUND(AVG(max_buiten),1) AS x
				FROM temp_hist
				WHERE DAYOFYEAR(datum) BETWEEN :start AND :end
			");
			$stmt->execute([':start' => $start, ':end' => $end]);
		}
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
			'Ts'=>date('G:i', $CivTwilightStart),
			'Sr'=>date('G:i', $Sunrise),
			'Ss'=>date('G:i', $Sunset),
			'Te'=>date('G:i', $CivTwilightEnd),
			'b'=>$b_hist,
			'pl'=>$map[boseplaylist($time)],
		];
		if(!isset($ddcache)||$ddcache!=$data) {
			publishmqtt('d/d',json_encode($data));
			$ddcache=$data;
		}
	}
	$yesterday = date('Y-m-d', strtotime('-1 day'));
	$stmt = $db->prepare("SELECT 1 FROM temp_hist WHERE datum = :datum");
	$stmt->execute([':datum' => $yesterday]);
	if ($stmt->fetchColumn()) {
		lg( "Data voor $yesterday bestaat al.");
		$since=date("Y-m-d G:i:s", $time-86400);
		foreach (array('01','02','03','04','06','07','08','09',11,12,13,14,16,17,18,19,21,22,23,24,26,27,28,29,31,32,33,34,36,37,38,39,41,42,43,44,46,47,48,49,51,52,53,54,56,57,58,59) as $x) {
			$query="DELETE FROM `temp` WHERE `stamp` LIKE '%:$x:00' AND `stamp` < '$since'";
			echo $query.PHP_EOL;
			$db->query($query);
		}
		$remove=date('Y-m-d H:i:s', $time-(86400*100));
		$stmt=$db->query("delete from temp where stamp < '$remove'");
	} else {
		$stmt = $db->prepare("
			SELECT
				MIN(buiten) AS min_buiten,
				ROUND(AVG(buiten), 2) AS avg_buiten,
				MAX(buiten) AS max_buiten
			FROM temp
			WHERE DATE(stamp) = :datum
		");
		$stmt->execute([':datum' => $yesterday]);
		$data = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($data && $data['min_buiten'] !== null) {
			$insert = $db->prepare("
				INSERT INTO temp_hist (datum, min_buiten, avg_buiten, max_buiten)
				VALUES (:datum, :min_buiten, :avg_buiten, :max_buiten)
			");
			$insert->execute([
				':datum' => $yesterday,
				':min_buiten' => $data['min_buiten'],
				':avg_buiten' => $data['avg_buiten'],
				':max_buiten' => $data['max_buiten']
			]);
			lg("Dagdata voor $yesterday toegevoegd.");
		} else {
			lg("Geen temperatuurdata voor $yesterday gevonden.");
		}
	}
}
if ($d['weg']->s==0) {
	foreach (array('living_temp','kamer_temp','alex_temp','badkamer_temp') as $i) {
		if (past($i)>43150) alert($i,$i.' not updated since '.date("G:i:s", $d[$i]->t),7200);
	}
}
