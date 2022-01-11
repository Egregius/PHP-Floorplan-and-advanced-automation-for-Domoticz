<?php
include '/var/www/vendor/autoload.php';
$server   = '192.168.2.28';
$port     = 1883;
$clientId = 'Pass2PHP';

$mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
$mqtt->connect();
$mqtt->publish('php-mqtt/client/test', 'Hello World!', 0);
$mqtt->disconnect();
