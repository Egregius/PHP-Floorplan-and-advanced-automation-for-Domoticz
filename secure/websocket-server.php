<?php
/**
 * WebSocket server voor real-time updates
 * Gebruik Ratchet library: composer require cboden/ratchet
 * 
 * Voordelen:
 * - Server pusht updates naar client (geen polling)
 * - Lagere latency (1-2ms)
 * - Minder server load
 * 
 * Start met: php websocket-server.php
 */
include '/var/www/vendor/autoload.php';
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\Loop;

class DomoticaServer implements MessageComponentInterface {
    protected $clients;
    protected $db;
    protected $lastCheck;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->db = Database::getInstance();
        $this->lastCheck = time();
        
        lg('✓ DomoticaServer initialized, lastCheck: ' . $this->lastCheck);
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        lg("✓ New connection: {$conn->resourceId} (total: {$this->clients->count()})");
        
        // Stuur initial state
        $this->sendInitialState($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        lg("← Message from {$from->resourceId}: {$msg}");
        
        // Handle client messages (bijv. commando's)
        $data = json_decode($msg, true);
        
        if (isset($data['action']) && $data['action'] === 'set_device') {
            $this->setDevice($data['name'], $data['value']);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        lg("✗ Connection closed: {$conn->resourceId} (remaining: {$this->clients->count()})");
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        lg("✗ Error: {$e->getMessage()}");
        $conn->close();
    }
    
    public function checkForChanges() {
        if ($this->clients->count() === 0) {
            return; // Geen clients, skip check
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT n, s, t
                FROM devices 
                WHERE t >= ?
            ");
            
            $stmt->execute([$this->lastCheck]);
            $changes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($changes)) {
                $data = json_encode([
                    't' => time(),
                    'changes' => $changes
                ]);
                lg("⚡ Found " . count($changes) . " changes: " . $data);
                $this->broadcast($data);
                
                $this->lastCheck = time();
            }
        } catch (Exception $e) {
            lg("✗ Error checking changes: {$e->getMessage()}");
        }
    }
    
    protected function sendInitialState(ConnectionInterface $conn) {
        try {
            $stmt = $this->db->query("SELECT n, s, t FROM devices");
            $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = json_encode([
                'type' => 'initial',
                'devices' => $devices
            ]);
            lg("→ Sending initial state: " . $data);
            $conn->send($data);
        } catch (Exception $e) {
            lg("✗ Error sending initial state: {$e->getMessage()}");
        }
    }
    
    protected function broadcast($message) {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
        lg("→ Broadcasted to {$this->clients->count()} clients");
    }
    
    protected function setDevice($name, $value) {
        try {
            // Update device in database
            $stmt = $this->db->prepare("
                UPDATE devices 
                SET s = ?, t = UNIX_TIMESTAMP() 
                WHERE n = ?
            ");
            $stmt->execute([$value, $name]);
            
            lg("✓ Updated device '{$name}' to '{$value}'");
            
            // Broadcast change
            $this->broadcast(json_encode([
                't' => time(),
                'changes' => [
                    ['n' => $name, 's' => $value, 't' => time()]
                ]
            ]));
        } catch (Exception $e) {
            lg("✗ Error updating device: {$e->getMessage()}");
        }
    }
}

class Database {
    private static ?PDO $instance = null;
    private function __construct() {}
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                $caller = $backtrace[1];
                $msg = str_replace('.php','',basename($caller['file'])) . ':' . $caller['line'];
                lg(' ⌗ '.$msg.' New DB connection');
                self::$instance = new PDO("mysql:host=192.168.2.23;dbname=domotica",'dbuser','dbuser',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_PERSISTENT => true
                    ]
                );
            } catch (PDOException $e) {
                die('Database connection failed.');
            }
        }
        return self::$instance;
    }
}

function lg($msg) {
    echo "$msg\n";
    $fp = fopen('/temp/domoticz.log', "a+");
    $time = microtime(true);
    $dFormat = "d-m H:i:s";
    $mSecs = $time - floor($time);
    $mSecs = substr(number_format($mSecs, 3), 1);
    fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
    fclose($fp);
}

// Onderdruk deprecated warnings
error_reporting(E_ALL & ~E_DEPRECATED);

lg("Starting WebSocket server...");

$domotica = new DomoticaServer();

// Start server op poort 8080
$server = IoServer::factory(
    new HttpServer(
        new WsServer($domotica)
    ),
    8080
);

// BELANGRIJKE FIX: Voeg periodic timer toe VOOR run()
Loop::addPeriodicTimer(1.0, function() use ($domotica) {
    $domotica->checkForChanges();
});

lg("✓ WebSocket server running on port 8080");
lg("✓ Checking for changes every 1 second");
lg("✓ Press Ctrl+C to stop");

// Start de event loop (dit blokkeert)
Loop::run();