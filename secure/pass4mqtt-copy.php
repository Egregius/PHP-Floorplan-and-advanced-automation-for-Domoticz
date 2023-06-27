#!/usr/bin/php
<?php
declare(strict_types=1);
$user='MQTT';
// Using https://github.com/bluerhinos/phpMQTT
require '/var/www/vendor/bluerhinos/phpmqtt/phpMQTT.php';
require '/var/www/html/secure/functions.php';
lg('Starting MQTT loop...');

$server = '127.0.0.1';     // change if necessary
$port = 1883;                     // change if necessary
$username = '';                   // set your username
$password = '';                   // set your password
$client_id = 'phpMQTT-subscriber'; // make sure this is unique for connecting to sever - you could use uniqid()

$mqtt = new Bluerhinos\phpMQTT($server, $port, $client_id);
if(!$mqtt->connect(true, NULL, $username, $password)) {
	exit(1);
}
$mqtt->debug = false;
$topics['domoticz/out/#'] = array('qos' => 0, 'function' => 'domoticz');
$topics['homeassistant/#'] = array('qos' => 0, 'function' => 'homeassistant');
$mqtt->subscribe($topics, 0);

while($mqtt->proc()) {
}
$mqtt->close();

function domoticz($topic, $msg){
		echo 'DMZ Received: ' . date('r') . "\n";
		echo "Topic: {$topic}\n\n";
		echo "\t$msg\n\n";
}

function homeassistant($topic, $msg){
		echo 'HA Received: ' . date('r') . "\n";
		echo "Topic: {$topic}\n\n";
		echo "\t$msg\n\n";
}