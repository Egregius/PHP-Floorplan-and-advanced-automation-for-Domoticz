<?php
include '/var/www/vendor/autoload.php';
$server   = '192.168.2.28';
$port     = 1883;
$clientId = 'Pass2PHP';

pcntl_async_signals(true);


$mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
pcntl_signal(SIGINT, function (int $signal, $info) use ($mqtt) {
    $mqtt->interrupt();
});
$mqtt->connect();
$mqtt->subscribe('php-mqtt/client/test', function ($topic, $message) {
    echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
}, 0);
$mqtt->loop(true);
$mqtt->disconnect();
