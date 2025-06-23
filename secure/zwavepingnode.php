<?php
require '/var/www/websocket/vendor/autoload.php';

use WebSocket\Client;

$wsUrl = "ws://192.168.2.26:3000";

try {
    $client = new Client($wsUrl);

    // Eerste bericht is de versie
    $version = $client->receive();
    echo "Version: $version\n";
	usleep(100000);
    // Ping node 81
    $pingRequest = json_encode([
        "messageId" => 1,
        "command" => "node.ping",
        "payload" => [
            "nodeId" => 81
        ]
    ]);
	$client->send(json_encode([
		"id" => 2,
		"type" => "request",
		"command" => "getNodes"
	]));
	
	$response = $client->receive();
	echo "Nodes response: $response\n";

    $client->send($pingRequest);

    $response = $client->receive();
    echo "Ping response: $response\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
