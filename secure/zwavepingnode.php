<?php
$wsUrl = "ws://192.198.2.26:3000"; // Pas aan naar jouw HA IP/FQDN + poort van Z-Wave JS UI (standaard 3000)
$nodeId = 23; // Pas aan naar het NodeID dat je wil pingen

$pingRequest = [
    'command' => 'ping',
    'nodeId'  => $nodeId,
];

$ws = new WebSocketClient($wsUrl);
$ws->send(json_encode($pingRequest));
$response = $ws->receive();
echo "Response:\n$response\n";
$ws->close();


class WebSocketClient {
    private $socket;
    private $host;
    private $port;
    private $path;

    public function __construct($url) {
        $parts = parse_url($url);
        $this->host = $parts['host'];
        $this->port = $parts['port'] ?? 80;
        $this->path = $parts['path'] ?? '/';

        $this->connect();
    }

    private function connect() {
        $address = "tcp://{$this->host}:{$this->port}";
        $this->socket = stream_socket_client($address, $errno, $errstr, 5);
        if (!$this->socket) {
            throw new Exception("Could not connect to $address: $errstr ($errno)");
        }

        $key = base64_encode(random_bytes(16));
        $headers = "GET {$this->path} HTTP/1.1\r\n"
            . "Host: {$this->host}:{$this->port}\r\n"
            . "Upgrade: websocket\r\n"
            . "Connection: Upgrade\r\n"
            . "Sec-WebSocket-Key: $key\r\n"
            . "Sec-WebSocket-Version: 13\r\n\r\n";
        fwrite($this->socket, $headers);

        // Read response headers (handshake)
        while (true) {
            $line = fgets($this->socket);
            if (trim($line) === '') break; // Headers done
        }
    }

    public function send($payload) {
        $header = chr(0x81); // FIN + text
        $length = strlen($payload);

        if ($length <= 125) {
            $header .= chr($length);
        } elseif ($length <= 65535) {
            $header .= chr(126) . pack('n', $length);
        } else {
            $header .= chr(127) . pack('J', $length);
        }

        fwrite($this->socket, $header . $payload);
    }

    public function receive() {
        $data = fread($this->socket, 8192);
        if (!$data) return null;

        $payloadLen = ord($data[1]) & 127;
        if ($payloadLen === 126) {
            $payloadOffset = 4;
        } elseif ($payloadLen === 127) {
            $payloadOffset = 10;
        } else {
            $payloadOffset = 2;
        }

        return substr($data, $payloadOffset);
    }

    public function close() {
        fclose($this->socket);
    }
}
