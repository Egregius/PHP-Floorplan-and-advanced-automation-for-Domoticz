#!/usr/bin/php
<?php
pcntl_async_signals(true);

$saveAndExit = function ($signo) use (&$history) {
	lg("🛑 Signaal $signo ontvangen. Geschiedenis opslaan en afsluiten...", 'cron2');
	if (!empty($history)) {
		file_put_contents('/var/www/spotifyhistory.json', json_encode($history));
	}
	exit;
};
gc_enable();
pcntl_signal(SIGTERM, $saveAndExit);
pcntl_signal(SIGINT, $saveAndExit);

require '/var/www/html/secure/functions.php';
lg('🟢 Starting cron2 loop...','cron2');
$time=time();
$d=fetchdata();
$lastcheck=$time;
$lastping=$time;
define('LOOP_START', $time);
$invalidcounter=$lastplay=$playlisttries=$wiimunknown=$historyruns=$cron2runs=0;

$skipped=false;
$history = file_exists('/var/www/spotifyhistory.json') ? json_decode(file_get_contents('/var/www/spotifyhistory.json'), true) : [];
$prevcleantitle = !empty($history) ? array_key_last($history) : null;

$ctx=stream_context_create(array('http'=>array('timeout' =>0.5)));
$devices = [
	101 => 20,
	102 => 25,
	103 => 32,
	104 => 32,
	105 => 32,
	106 => 25,
	107 => 32,
	108 => 32,
	109 => 32,
];
$user='cron2B';
$boses=array(
	101=>'Living',
	102=>'102',
	103=>'Boven',
	104=>'Garage',
	105=>'10-Wit',
	106=>'Buiten20',
	107=>'Keuken',
	108=>'ST-30-2',
	109=>'ST-20-2',
);
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require '/var/www/vendor/autoload.php';
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt')
	->setKeepAliveInterval(60);
$mqtt=new MqttClient('192.168.30.22',1883,basename(__FILE__),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);
$toplist=[];
$db = Database::getInstance();
$stmt=$db->query("SELECT clean_title FROM track_mapping WHERE playlist_id='4O0G5e4lsBRG5CV485iolD';");
while ($row=$stmt->fetch(PDO::FETCH_NUM)) {
	if (isset($row[0])) $toplist[]=$row[0];
}
while (1) {
	$time = time();
	$d['time'] = $time;
	$d = fetchdata();
	include 'cron2B.php';
	if($skipped===false) {
		$time_elapsed_secs = microtime(true) - $time;
		$sleep = 10 - $time_elapsed_secs;
		if ($sleep > 0) {
			$sleep = round($sleep * 1000000);
			usleep($sleep);
		}
		if ($lastping < $time - 60) {
			$lastping = $time;
			$mqtt->publish('p', 'p');
		}
		if ($lastcheck < $time - 300) {
			$lastcheck = $time;
			stoploop();
			$d=fetchdata(0);
		}
	}
}

function stoploop() {
	global $history;
	$script = __FILE__;
	if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
		lg('🛑 functions.php gewijzigd → restarting cron2 loop...','cron2');
		if (!empty($history)) file_put_contents('/var/www/spotifyhistory.json', json_encode($history));
		exit;
	}
	if (filemtime(__DIR__ . '/cron2.php') > LOOP_START) {
		lg('🛑 cron2.php gewijzigd → restarting cron2 loop...','cron2');
		if (!empty($history)) file_put_contents('/var/www/spotifyhistory.json', json_encode($history));
		exit;
	}
	static $cycles=0;
	if($cycles>=50) {
		gc_collect_cycles();
		$cycles=0;
	} else $cycles++;
}
