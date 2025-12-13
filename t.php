<?php
/**
 * Benchmark AJAX endpoint
 * Meet exact waar de tijd heen gaat
 */

$timers = [];
$timers['start'] = microtime(true);

// 1. Database connectie
$db = Database::getInstance();
$timers['db_connect'] = microtime(true);

// 2. Query uitvoeren
$stmt = $db->prepare("SELECT * FROM devices_mem");
$stmt->execute();
$timers['query_execute'] = microtime(true);

// 3. Data fetchen
$data = $stmt->fetchAll();
$timers['fetch_data'] = microtime(true);

// 4. JSON encoding
$json = json_encode($data);
$timers['json_encode'] = microtime(true);

// 5. Output
header('Content-Type: application/json');
echo $json;
$timers['output'] = microtime(true);

// Timing report (alleen in development)
if (isset($_GET['debug'])) {
    $report = [
        'db_connect' => round(($timers['db_connect'] - $timers['start']) * 1000, 2) . 'ms',
        'query_execute' => round(($timers['query_execute'] - $timers['db_connect']) * 1000, 2) . 'ms',
        'fetch_data' => round(($timers['fetch_data'] - $timers['query_execute']) * 1000, 2) . 'ms',
        'json_encode' => round(($timers['json_encode'] - $timers['fetch_data']) * 1000, 2) . 'ms',
        'output' => round(($timers['output'] - $timers['json_encode']) * 1000, 2) . 'ms',
        'total' => round(($timers['output'] - $timers['start']) * 1000, 2) . 'ms'
    ];
    error_log("AJAX Timing: " . json_encode($report));
}

class Database {
    private static ?PDO $instance = null;
    private function __construct() {}
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
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