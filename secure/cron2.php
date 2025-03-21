#!/usr/bin/php
<?php
require '/var/www/html/secure/functions.php';
require '/var/www/vendor/autoload.php';
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

$mqtt=new MqttClient('127.0.0.1',1883,'cron2'.rand());
$connectionSettings=(new ConnectionSettings())
	->setKeepAliveInterval(60)
	->setUseTls(false);
$mqtt->connect($connectionSettings, true);


lg('Starting cron10B loop...');
$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
$lastfetch=time();
while (1){
	$start=microtime(true);
	$time=time();
	$d=fetchdata($lastfetch,basename(__FILE__).':'.__LINE__);
	$lastfetch=$time;
	include '_cron10B.php';
	$time_elapsed_secs=microtime(true)-$start;
	$sleep=10-$time_elapsed_secs;
	if ($sleep<0) $sleep=0;
	$sleep=round($sleep*1000000);
	usleep($sleep);
}

