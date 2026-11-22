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
$invalidcounter=$lastplay=$playlisttries=$wiimunknown=$historyruns=$lastCleanKey=$lastdate=$boseSetVolume=0;
$skipped=false;
$history = file_exists('/var/www/spotifyhistory.json') ? json_decode(file_get_contents('/var/www/spotifyhistory.json'), true) : [];
$prevcleantitle = !empty($history) ? array_key_last($history) : null;
$ctx=stream_context_create(array('http'=>array('timeout' =>0.5)));
$user='cron2B';
$devices = [
	101 => 36,
	102 => 35,
	103 => 35,
	104 => 35,
	105 => 35,
	106 => 40,
	107 => 40,
	108 => 35,
	109 => 35,
];
$powerSwitches = [
	101 => 'boseliving',
	103 => 'badkamerpower',
	107 => 'bosekeuken',
];
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
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require '/var/www/vendor/autoload.php';
$mqttSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt')
	->setKeepAliveInterval(60);
$mqtt=new MqttClient('192.168.30.22',1883,basename(__FILE__) . '_' . getmypid(),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($mqttSettings,true);
$db = Database::getInstance();

while (1) {
	$loopStart = microtime(true);
	$time = (int)$loopStart;
	$d['time'] = $time;
	$d = fetchdata();
	include 'cron2B.php';
	if ($skipped === false) {
		$elapsed = microtime(true) - $loopStart;
		$remaining = 10.0 - $elapsed;
		if ($remaining > 0) {
			usleep((int)round($remaining * 1000000));
		} else {
			usleep(500000);
		}
		if ($lastping < $time - 60 && 1 == 2) {
			$lastping = $time;
			publishmqtt('p', 'p');
		}
		if ($lastcheck < $time - 60) {
			$lastcheck = $time;
			stoploop();
			$d = fetchdata(0);
		}
	}
}
function stoploop() {
	global $history;
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