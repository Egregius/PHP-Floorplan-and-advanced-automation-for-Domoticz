<?php
/**
 * WebSocket server voor real-time updates
 * Output format matches legacy polling API exactly
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
        $this->sendInitialState($conn);
    }
    public function onMessage(ConnectionInterface $from, $msg) {
        lg("← Message from {$from->resourceId}: {$msg}");
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
            return;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT n, s, t, m, dt, icon, rt, p 
                FROM devices_mem 
                WHERE f = 1 AND t >= ?
            ");
            
            $stmt->execute([$this->lastCheck]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (!empty($rows)) {
                $d = ['t' => time()];
                
                foreach ($rows as $row) {
                    $n = $row['n'];
                    $d[$n]['s'] = $row['s'];
                    
                    if ($row['rt'] == 1) {
                        $d[$n]['t'] = $row['t'];
                    }
                    
                    if (!is_null($row['m'])) {
                        $d[$n]['m'] = $row['m'];
                    }
                    
                    if (!is_null($row['dt'])) {
                        $d[$n]['dt'] = $row['dt'];
                        if ($row['dt'] === 'daikin') {
                            $d[$n]['s'] = null;
                        }
                    }
                    
                    if (!is_null($row['icon'])) {
                        if ($row['dt'] === 'th' && $n !== 'badkamer_set') {
                            $icon = json_decode($row['icon'], true);
                        } else {
                            $d[$n]['icon'] = $row['icon'];
                        }
                    }
                    
                    if (!is_null($row['p'])) {
                        $d[$n]['p'] = $row['p'];
                    }
                }
                
                $json = json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                $this->broadcast($json);
                
                $this->lastCheck = time();
            }
        } catch (Exception $e) {
            lg("✗ Error checking changes: {$e->getMessage()}");
        }
    }
    
    protected function sendInitialState(ConnectionInterface $conn) {
        try {
            $time = time();
            $d = ['t' => $time];
            
            // Energy data (cached)
            $en = getCache('en');
            if ($en) {
                $en = json_decode($en);
                if ($en) {
                    $d['n'] = $en->n;
                    $d['a'] = $en->a;
                    $d['b'] = $en->b;
                    $d['c'] = $en->c;
                    $d['z'] = $en->z;
                }
            }
            
            // Vandaag energy stats
            $vandaag = getCache('energy_vandaag');
            if ($vandaag) {
                $vandaag = json_decode($vandaag);
                if ($vandaag) {
                    $d['gas'] = $vandaag->gas;
                    $d['gasavg'] = $vandaag->gasavg;
                    $d['elec'] = $vandaag->elec;
                    $d['elecavg'] = $vandaag->elecavg;
                    $d['verbruik'] = $vandaag->verbruik;
                    $d['zon'] = $vandaag->zon;
                    $d['zonavg'] = $vandaag->zonavg;
                    $d['zonref'] = $vandaag->zonref;
                    $d['alwayson'] = $vandaag->alwayson;
                }
            }
            
            // Sunrise/sunset data
            $sunrise = getCache('sunrise');
            if ($sunrise) {
                $sunrise = json_decode($sunrise, true);
                if ($sunrise) {
                    $d['CivTwilightStart'] = $sunrise['CivTwilightStart'];
                    $d['Sunrise'] = $sunrise['Sunrise'];
                    $d['Sunset'] = $sunrise['Sunset'];
                    $d['CivTwilightEnd'] = $sunrise['CivTwilightEnd'];
                    $d['playlist'] = boseplaylist($time);
                }
            }
            
            // Thermo history
            $thermo_hist = getCache('thermo_hist');
            if ($thermo_hist !== false) {
                $d['thermo_hist'] = json_decode($thermo_hist, true);
            }
            
            // All devices
            $stmt = $this->db->query("
                SELECT n, s, t, m, dt, icon, rt, p 
                FROM devices_mem 
                WHERE f = 1
            ");
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $n = $row['n'];
                $d[$n]['s'] = $row['s'];
                
                if ($row['rt'] == 1) {
                    $d[$n]['t'] = $row['t'];
                }
                
                if (!is_null($row['m'])) {
                    $d[$n]['m'] = $row['m'];
                }
                
                if (!is_null($row['dt'])) {
                    $d[$n]['dt'] = $row['dt'];
                    if ($row['dt'] === 'daikin') {
                        $d[$n]['s'] = null;
                    }
                }
                
                if (!is_null($row['icon'])) {
                    if ($row['dt'] === 'th' && $n !== 'badkamer_set') {
                        $icon = json_decode($row['icon'], true);
                    } else {
                        $d[$n]['icon'] = $row['icon'];
                    }
                }
                
                if (!is_null($row['p'])) {
                    $d[$n]['p'] = $row['p'];
                }
            }
            
            $json = json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            lg("→ Sending initial state (" . strlen($json) . " bytes)");
            $conn->send($json);
            
        } catch (Exception $e) {
            lg("✗ Error sending initial state: {$e->getMessage()}");
        }
    }
    
    protected function broadcast($message) {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
        lg("→ Broadcasted to {$this->clients->count()} clients:$message");
    }
    
    protected function setDevice($name, $value) {
        try {
            $stmt = $this->db->prepare("
                UPDATE devices_mem 
                SET s = ?, t = UNIX_TIMESTAMP() 
                WHERE n = ?
            ");
            $stmt->execute([$value, $name]);
            
            lg("✓ Updated device '{$name}' to '{$value}'");
            
            // Trigger immediate update broadcast
            $this->checkForChanges();
            
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
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}

function getCache(string $key, $default = false) {
    $data = @file_get_contents('/dev/shm/cache/' . $key . '.txt');
    return $data === false ? $default : $data;
}

function boseplaylist($time) {
    $dag = floor($time / 86400);
    $dow = date("w", $time);
    $weekend = ($dow == 0 || $dow == 6);
    if ($weekend) {
        if ($dag % 3 == 0) return 'MIX-3';
        if ($dag % 2 == 0) return 'MIX-2';
        return 'MIX-1';
    } else {
        if ($dag % 3 == 0) return 'EDM-3';
        if ($dag % 2 == 0) return 'EDM-2';
        return 'EDM-1';
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

$server = IoServer::factory(
    new HttpServer(
        new WsServer($domotica)
    ),
    8080
);

// Check voor changes elke seconde
Loop::addPeriodicTimer(1.0, function() use ($domotica) {
    $domotica->checkForChanges();
});

lg("✓ WebSocket server running on port 8080");
Loop::run();