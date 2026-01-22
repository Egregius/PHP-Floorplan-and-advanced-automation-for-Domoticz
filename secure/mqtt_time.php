#!/usr/bin/php
<?php
declare(strict_types=1);
$lock_file = fopen('/run/lock/'.basename(__FILE__).'.pid', 'c');
$got_lock = flock($lock_file, LOCK_EX | LOCK_NB, $wouldblock);
if ($lock_file === false || (!$got_lock && !$wouldblock)) {
    throw new Exception("Unexpected error opening or locking lock file.");
} else if (!$got_lock && $wouldblock) {
    exit("Another instance is already running; terminating.\n");
}
ini_set('error_reporting',E_ALL);
ini_set('display_errors',true);
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require_once '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
$user='TIME';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$lasttimepub=$time;
$lastSecond=$time;
define('LOOP_START', $time);
$rand = rand(100, 200);
$lastMessageReceived=true;

$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt = new MqttClient('192.168.2.22', 1883, basename(__FILE__) . '_' . getmypid() . VERSIE, MqttClient::MQTT_3_1);
$mqtt->connect($connectionSettings, true);

$mqtt->subscribe('d/#', function (string $topic, string $status) use ($rand, &$lastcheck, &$time, &$lastpub, &$lastMessageReceived) {
    $lastpub = $time;
    $lastMessageReceived = true;
    if ($lastcheck < $time - $rand) {
        $lastcheck = $time;
        stoploop();
    }
}, MqttClient::QOS_AT_LEAST_ONCE);
while (true) {
    $microtime = microtime(true);
    $microsUntilNextSecond = (1 - ($microtime - floor($microtime))) * 1000000;
    $sleepMicros = max(1000, $microsUntilNextSecond);
    usleep((int)$sleepMicros);
    $time = time();
    $mqtt->loopOnce($time);
    if (!$lastMessageReceived) {
        $lastpub = $time;
//        lg('🏓 ' . ($time - $lasttimepub));
        $lasttimepub = $time;
        $mqtt->publish('d/t', json_encode(1));
    }
    $lastSecond = $time;
    $lastMessageReceived = false;
}
$mqtt->disconnect();
lg("🛑 MQTT {$user} loop stopped ".__FILE__,1);

function stoploop() {
    global $mqtt,$lock_file;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting '.basename($script).' loop...');
        $mqtt->disconnect();
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 5 /usr/bin/php $script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 5 /usr/bin/php $script > /dev/null 2>&1 &");
        exit;
    }
}
