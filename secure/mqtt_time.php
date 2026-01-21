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
define('LOOP_START', $time);
$rand = rand(10, 20);

$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$lastMessageReceived = false;

$mqtt->subscribe('d/#', function (string $topic, string $status) use ($rand, &$lastcheck, &$time, &$lastpub, &$lastMessageReceived) {
    $lastpub = $time;
    $lastMessageReceived = true;
    if ($lastcheck < $time - $rand) {
        $lastcheck = $time;
        stoploop();
    }
}, MqttClient::QOS_AT_LEAST_ONCE);

$lastSecond = time();

while (true) {
    $time = time();
    $mqtt->loopOnce($time);

    // Check of we een nieuwe seconde zijn ingegaan
    if ($time > $lastSecond) {
        // Publish alleen als er GEEN bericht ontvangen was in de vorige seconde
        if (!$lastMessageReceived) {
            $lastpub = $time;
			lg('-----------------------------------------------------> '.$time-$lasttimepub);

            $mqtt->publish('d/t', json_encode(1));
        }

        // Reset voor de nieuwe seconde
        $lastSecond = $time;
        $lastMessageReceived = false;
    }

    // Bereken hoeveel microseconden er nog zijn tot de volgende seconde
    $microtime = microtime(true);
    $microsUntilNextSecond = (1 - ($microtime - floor($microtime))) * 1000000;

    // Sleep maximaal 50ms, of tot vlak voor de volgende seconde
    $sleepMicros = min(50000, max(1000, $microsUntilNextSecond - 5000)); // -5ms buffer
    usleep((int)$sleepMicros);
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
