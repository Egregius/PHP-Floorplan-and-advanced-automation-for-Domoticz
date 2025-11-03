#!/usr/bin/php
<?php
declare(strict_types=1);
ini_set('error_reporting',E_ALL);
ini_set('display_errors',true);
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require '/var/www/vendor/autoload.php';
require '/var/www/html/secure/functions.php';
$user='ENERGY';
lg('🟢 Starting '.$user.' loop ',-1);
$counter=0;

define('LOOP_START', microtime(true));
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt=new MqttClient('192.168.2.26',1883,basename(__FILE__),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);

$mqtt->subscribe('en/+', function (string $topic, string $value) use (&$counter){
	static $data = ['n' => 0, 'a' => 0, 'z' => 0, 'b' => 0, 'c' => 0];
	$data[$topic[-1]] = $value;
	setCache('en', json_encode($data));
	$counter++;
	if ($counter>1000) {
		stoploop();
		$counter=0;
	}
}, MqttClient::QOS_AT_LEAST_ONCE);

$sleepMicroseconds=20000;
$maxSleep=1000000;
while (true) {
	$mqtt->loop(true);
	usleep(100000);
}

$mqtt->disconnect();
lg('MQTT loop stopped '.__FILE__,1);



function stoploop() {
    global $mqtt;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting '.basename($script).' loop...');
        $mqtt->disconnect();
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        exec("$script > /dev/null 2>&1 &");
        exit;
    }
}