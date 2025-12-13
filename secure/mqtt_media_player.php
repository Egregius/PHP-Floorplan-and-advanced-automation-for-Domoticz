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
$dow = null;
$d=fetchdata(0,'mqtt_media_player:'.__LINE__);
$startloop=time();
define('LOOP_START', $startloop);
$d['time']=$startloop;
$d['rand']=rand(10,20);
updateWekker($t, $weekend, $dow, $d);
$lastEvent=$startloop;
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt=new MqttClient('192.168.2.22',1883,basename(__FILE__),MqttClient::MQTT_3_1,null,null);
$mqtt->connect($connectionSettings,true);

$mqtt->subscribe('homeassistant/media_player/+/state',function (string $topic,string $status) use ($startloop,&$d, &$lastcheck, &$time) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		$time=time();
		$d['time']=$time;
		$d=fetchdata();
		$d['lastfetch']=$time;
		$status = ucfirst(strtolower($status));
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
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('homeassistant/media_player/+/source',function (string $topic,string $status) use ($startloop,&$d, &$lastcheck) {
	try {	
		$path=explode('/',$topic);
		$device=$path[2];
		if ($device=='nvidia') {
			$d['time']=microtime(true);
			$d=fetchdata($d['lastfetch'],'mqtt_media_player:'.__LINE__);
			$d['lastfetch']=$d['time'] - 300;
			$status = ucfirst(strtolower(trim($status, '"')));
			if ($d[$device]['m']!=$status) {
				storemode($device,$status,'',1);
			}
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT: ".__LINE__.' '.$topic.' '.$e->getMessage());
	}
},MqttClient::QOS_AT_LEAST_ONCE);

while (true) {
	$result=$mqtt->loop(true);
	usleep(50000);
}
$mqtt->disconnect();
lg('🛑 MQTT loop stopped '.__FILE__,1);

function stoploop() {
    global $mqtt;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting '.basename($script).' loop...');
        $mqtt->disconnect();
        exec("nice -n 10 /usr/bin/php $script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        exec("nice -n 10 /usr/bin/php $script > /dev/null 2>&1 &");
        exit;
    }
}