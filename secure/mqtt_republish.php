<?php
require '/var/www/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

$server   = "127.0.0.1";  // WebSocket MQTT broker
$port     = 1883;         // WebSocket poort
$clientId = "php_mqtt_ws_" . rand(); 

$subscribeTopic = "domoticz/out/#";   // Gebruik wildcard voor alle berichten
$publishTopic   = "test";

try {
    // Maak verbinding met de MQTT broker via WebSocket
    $mqtt = new MqttClient($server, $port, $clientId);
    
    // Zet de verbindingseinstellungen, zoals Keep-Alive en gebruik geen TLS (aangezien het een WebSocket verbinding is)
    $connectionSettings = (new ConnectionSettings())
        ->setKeepAliveInterval(60)
        ->setUseTls(false); // Zorg ervoor dat er geen TLS gebruikt wordt (WebSocket verbindingen gebruiken vaak geen TLS)

    // Maak de verbinding
    $mqtt->connect($connectionSettings, true);
    echo "✅ Verbonden met WebSocket broker op $server:$port\n";

    // Subscribe op het topic (gebruik # om alles te ontvangen onder domoticz/out)
    $mqtt->subscribe($subscribeTopic, function ($topic, $message) use ($mqtt, $publishTopic) {
        echo "📩 Ontvangen: [$topic] $message\n";
        $mqtt->publish($publishTopic.'/'.str_replace('domoticz/out/','',$topic), $message, 0, true);  // Publiceer het bericht met Retain
        echo "🚀 Herpublicatie: [$publishTopic]/[$topic] $message\n";
    }, 0);

    // Loop om berichten te verwerken
    while ($mqtt->loop(true)) {}

    // Verbreek de verbinding
    $mqtt->disconnect();
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage() . "\n";
}
