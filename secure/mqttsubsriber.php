<?php
include '/var/www/vendor/autoload.php';
$server   = '192.168.2.28';
$port     = 1883;
$clientId = 'Pass2PHP';

$mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
$mqtt->connect();
$mqtt->subscribe('domoticz', function ($topic, $message) {
    echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
}, 0);
$mqtt->loop(true);
$mqtt->disconnect();
