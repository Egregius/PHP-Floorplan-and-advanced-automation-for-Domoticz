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
$user='LIGHT';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$t = null;
$weekend = null;
$dow = null;
$d=fetchdata(0,'mqtt_light:'.__LINE__);
$startloop=time();
define('LOOP_START', $startloop);
$d['time']=$startloop;
$d['rand']=rand(100,200);
updateWekker($t, $weekend, $dow, $d);
$lastEvent=$startloop;
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt=new MqttClient('192.168.2.22',1883,basename(__FILE__),MqttClient::MQTT_3_1);
$mqtt->connect($connectionSettings,true);
$alreadyProcessed=[];
$validDevices = [];
foreach (glob('/var/www/html/secure/pass2php/*.php') as $file) {
	$basename = basename($file, '.php');
	$validDevices[$basename] = true;
}

$mqtt->subscribe('homeassistant/light/+/brightness',function (string $topic,string $status) use ($startloop,$validDevices,&$d,/*&$alreadyProcessed, */&$lastcheck, &$time, $user) {
	try {
		$path=explode('/',$topic);
		$device=$path[2];
		if (isset($validDevices[$device])) {
			$time=time();
			$d['time']=$time;
			if (($time - $startloop) <= 2 && in_array($device,['inputliving1'])) return;
//			if (isProcessed($topic,$status,$alreadyProcessed)) return;
//			if (($d[$device]['s'] ?? null) === $status) return;
			if (isset($status)) {
				$d=fetchdata();
				if ($status === 'null') $status=0;
				elseif ($status > 0 ) $status=round((float)$status / 2.55);
				else $status=0;
//				if($status>40&&$status<100)$status+=1;
//				lg('💡 mqtt '.__LINE__.' |bright |state |'.$device.'|'.$status);
				if ($d[$device]['s']!=$status) {
					store($device,$status);
					include '/var/www/html/secure/pass2php/'.$device.'.php';
				}
			}
		}
	} catch (Throwable $e) {
		lg("Fout in MQTT {$user}: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage());
	}
	if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
        updateWekker($t, $weekend, $dow, $d);
    }
},MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('d/+/+',function (string $topic,string $status) use (&$d) {
	$path=explode('/',$topic);
	$d[$path[1]][$path[2]]=$status;
},MqttClient::QOS_AT_LEAST_ONCE);

while (true) {
	$result=$mqtt->loop(true);
	usleep(50000);
}
$mqtt->disconnect();
lg("🛑 MQTT {$user} loop stopped ".__FILE__,1);

function isProcessed(string $topic,string $status,array &$alreadyProcessed): bool {
	if (isset($alreadyProcessed[$topic]) && $alreadyProcessed[$topic] === $status) return true;
	$alreadyProcessed[$topic]=$status;
	return false;
}

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