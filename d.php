<?php
header('Content-Type: application/json; charset=ISO-8859-1');
$sql="SELECT n,s,t,m,d,i,rt,p FROM devices WHERE f=1";
$d = ['t' => $_SERVER['REQUEST_TIME']];

$db = Database::getInstance();
$stmt = $db->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_NUM);
foreach ($rows as [$n, $s, $t_val, $m, $device, $i, $rt, $p]) {
    $d[$n] = ['s' => $s];
    $rt === 1 && $d[$n]['t'] = $t_val;
    $m !== null && $d[$n]['m'] = $m;
    if ($device !== null) {
        $d[$n]['d'] = $device;
        $device === 'daikin' && $d[$n]['s'] = null;
    }
    if ($i !== null) {
        if ($device === 'th' && $n !== 'badkamer_set') {
            $icon = json_decode($i, true);
        } else {
            $d[$n]['i'] = $i;
        }
    }
    $p !== null && $d[$n]['p'] = $p;
}

$data=json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
header('Content-Type: application/json');
header('Content-Length: '.strlen($data));
echo $data;

final class Database {
    private static ?PDO $instance = null;
    private function __construct() {}
    private function __clone(): void {}
    public static function getInstance(): PDO {
        return self::$instance ??= self::createConnection();
    }
    private static function createConnection(): PDO {
        try {
            return new PDO(
                dsn: "mysql:host=192.168.2.23;dbname=domotica;charset=latin1",
                username: 'dbuser',
                password: 'dbuser',
                options: [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_PERSISTENT => true,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES latin1",
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::MYSQL_ATTR_FOUND_ROWS => true
                ]
            );
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new RuntimeException('Database connection failed.', 0, $e);
        }
    }
    public static function reset(): void {
        self::$instance = null;
    }
    public static function isConnected(): bool {
        return self::$instance !== null;
    }
}
