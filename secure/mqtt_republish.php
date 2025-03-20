<?php
require '/var/www/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

$server   = "127.0.0.1"; // MQTT WebSocket broker (localhost)
$port     = 8081; // WebSocket poort (zoals geconfigureerd in Mosquitto)
$clientId = "php_mqtt_ws_" . rand(); // Unieke client ID

// Topics
$subscribeTopic = "#"; // Het topic waarop we luisteren
$publishTopic   = "test"; // Het topic waarop we publiceren

try {
    // Maak verbinding via WebSockets
    $mqtt = new MqttClient($server, $port, $clientId, MqttClient::MQTT_3_1); 

    // Instellingen (geen authenticatie nodig)
    $connectionSettings = (new ConnectionSettings())->setKeepAliveInterval(60);

    // Verbinden met de broker
    $mqtt->connect($connectionSettings, true);
    echo "✅ Verbonden met MQTT WebSocket broker op $server:$port\n";

    // Callback voor binnenkomende berichten
    $mqtt->subscribe($subscribeTopic, function ($topic, $message) use ($mqtt, $publishTopic) {
        echo "📩 Ontvangen: [$topic] $message\n";

        // Herpubliceren met retain flag
        $mqtt->publish($publishTopic, $message, 0, true);
        echo "🚀 Herpublicatie: [$publishTopic] $message\n";
    }, 0);

    // Blijf luisteren naar berichten
    while ($mqtt->loop(true)) {}

    // Verbinding sluiten (wordt normaal niet bereikt)
    $mqtt->disconnect();
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage() . "\n";
}
