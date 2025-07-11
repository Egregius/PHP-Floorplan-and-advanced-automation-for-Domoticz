<?php
//lg('CRON-3600');
$user='cron3600';

if (date('G')==0) {
	mset('alwayson',9999);
	store('gasvandaag', 0, basename(__FILE__).':'.__LINE__);
	store('watervandaag', 0, basename(__FILE__).':'.__LINE__);
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
	mset('sunrise', array(
		'CivTwilightStart' => date('G:i', $CivTwilightStart),
		'CivTwilightEnd' => date('G:i', $CivTwilightEnd),
		'Sunrise' => date('G:i', $Sunrise),
		'Sunset' => date('G:i', $Sunset),
	));
}


$ha_url = 'http://192.168.2.26:8123';
$ha_token = 'Bearer '.hasstoken();
$base_topic = 'homeassistant';
foreach ($d as $device => $i) {
	if (!isset($i['dt'])) continue;
	$entity_id = null;
	$to_publish = [];
	if ($i['dt'] === 'hsw') {
		$entity_id = "switch.$device";
		$url = "$ha_url/api/states/$entity_id";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $ha_token"]);
		$result = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($result, true);
		if (!isset($data['state'])) {
			lg("Fout: kon status van $entity_id niet ophalen");
			continue;
		}
		list($domain, $object_id) = explode('.', $entity_id);
		$state = $data['state'];
		if ($state!=$i['s']) $to_publish[] = [
			'topic' => "$base_topic/$domain/$object_id/state",
			'payload' => $state
		];
	} elseif ($i['dt'] === 'hd') {
		$entity_id = "light.$device";
		$url = "$ha_url/api/states/$entity_id";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: $ha_token"]);
		$result = curl_exec($ch);
		curl_close($ch);
		$data = json_decode($result, true);
		$attributes = $data['attributes'] ?? [];
		if (!array_key_exists('brightness', $attributes)) {
			lg("Geen brightness attribuut voor $entity_id");
			continue;
		}
		list($domain, $object_id) = explode('.', $entity_id);
		$brightness = $attributes['brightness'] ?? 0;
		if ($brightness === null) $brightness = 0;
		if ($brightness!=$i['s']) $to_publish[] = [
			'topic' => "$base_topic/$domain/$object_id/brightness",
			'payload' => $brightness
		];
	} else continue;
	usleep(50000);
	foreach ($to_publish as $pub) {
		$payload = [
			'topic' => $pub['topic'],
			'payload' => $pub['payload'],
			'retain' => true
		];
		$ch = curl_init("$ha_url/api/services/mqtt/publish");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: $ha_token",
			"Content-Type: application/json"
		]);
		$response = curl_exec($ch);
		curl_close($ch);
		lg("Herpublicatie: $entity_id → {$pub['payload']} → {$pub['topic']}");
		usleep(50000);
	}
}
