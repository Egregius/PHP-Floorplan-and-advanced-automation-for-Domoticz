<?php
declare(strict_types=1);

require '/var/www/client-examples/vendor/autoload.php';
require '/var/www/html/secure/functions.php';

use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;
use Psr\Log\LogLevel;

$client = new MqttClient('127.0.0.1', 1883, 'pass4mqtt', MqttClient::MQTT_3_1, null, null);
$client->connect(null, true);
$client->subscribe('#', function (string $topic, string $message, bool $retained) use ($client) {
	lg($topic.'	'.$message);
}, MqttClient::QOS_AT_MOST_ONCE);
$client->loop(true);
$client->disconnect();