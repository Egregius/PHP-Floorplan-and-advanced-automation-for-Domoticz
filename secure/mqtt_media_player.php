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
$user='MEDIA';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$d=fetchdata(0,'mqtt:'.__LINE__);
$startloop=microtime(true);
define('LOOP_START', $startloop);
$d['lastfetch']=$startloop;
$d['time']=$startloop;
$d['rand']=rand(60,120);
$lastEvent=$startloop;
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt=new MqttClient('192.168.2.26',1883,basename(__FILE__),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);

$mqtt->subscribe('homeassistant/media_player/+/state',function (string $topic,string $status) use ($startloop,&$d, &$lastcheck) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		$d['time']=microtime(true);
		$d=fetchdata($d['lastfetch'],'mqtt:'.__LINE__);
		$d['lastfetch']=$d['time'] - 300;
		$status = ucfirst(strtolower(trim($status, '"')));
		if ($d[$device]['s']!=$status) {
//			lg('mqtt '.__LINE__.' |media |state |'.$device.'|'.$status.'|');
			include '/var/www/html/secure/pass2php/'.$device.'.php';
			store($device,$status,'',1);
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
	if ($lastcheck < $d['time'] - $d['rand']) {
        $lastcheck = $d['time'];
        stoploop();
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$sleepMicroseconds=10000;
$maxSleep=500000;
while (true) {
	$result=$mqtt->loop(true);
	if ($result === 0) {
		$sleepMicroseconds=min($sleepMicroseconds + 10000,$maxSleep);
		usleep($sleepMicroseconds);
	} else {
		$sleepMicroseconds=10000;
	}
}

$mqtt->disconnect();
lg('🛑 MQTT loop stopped '.__FILE__,1);

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