#!/usr/bin/php
<?php
declare(strict_types=1);

$lock_file = fopen('/run/lock/'.basename(__FILE__).'.pid', 'c');
$got_lock = flock($lock_file, LOCK_EX | LOCK_NB, $wouldblock);
if ($lock_file === false || (!$got_lock && !$wouldblock)) {
    throw new Exception("Unexpected error opening or locking lock file.");
} else if (!$got_lock && $wouldblock) {
    exit("Another instance is already running; terminating.\n");
}

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

require_once '/var/www/vendor/autoload.php';

$user = 'ENERGY';
lg('🟢 Starting ' . $user . ' loop ', -1);

$time = time();
$lastcheck = $time;
date_default_timezone_set('Europe/Brussels');
$startloop = microtime(true);
define('LOOP_START', $startloop);

$d['rand'] = rand(10, 20);
$connectionSettings = (new ConnectionSettings)
    ->setUsername('mqtt')
    ->setPassword('mqtt');

$mqtt = new MqttClient('192.168.2.22', 1883, basename(__FILE__), MqttClient::MQTT_3_1);
$mqtt->connect($connectionSettings, true);

$dbverbruik = new Database('192.168.2.20', 'home', 'H0m€', 'verbruik');
$dbzonphp = new Database('192.168.2.20', 'home', 'H0m€', 'egregius_zonphp');

$force = true;

// CRUCIAAL: Initialiseer de array met default waarden uit cache om count mismatch te voorkomen
$storedTeller = json_decode(getCache('teller'), true);
$newData = [
    'import' => $storedTeller['import'] ?? 0,
    'export' => $storedTeller['export'] ?? 0,
    'gas'    => $storedTeller['gas'] ?? 0,
    'water'  => $storedTeller['water'] ?? 0
];

$mqtt->subscribe('t/+', function (string $topic, string $status) use (&$d, &$time, &$lastcheck, &$newData, $dbverbruik, $dbzonphp, &$force, &$mqtt) {
    $time = time();
    $changed = false;

    if ($topic == 't/import') { $newData['import'] = $status; $changed = true; }
    elseif ($topic == 't/export') { $newData['export'] = $status; $changed = true; }
    elseif ($topic == 't/gas') { $newData['gas'] = $status; $changed = true; }
    elseif ($topic == 't/water') { $newData['water'] = $status; $changed = true; }

    if ($changed) {
        // Sla de laatste standen direct op in cache
        setCache('teller', json_encode($newData));
        processEnergyData($dbverbruik, $dbzonphp, $force, $newData, $mqtt, $time);
    }

    if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
    }
}, MqttClient::QOS_AT_LEAST_ONCE);

while (true) {
    $mqtt->loop(true, false, null, 50000);
}

$mqtt->disconnect();

// --- FUNCTIES ---

function processEnergyData($dbverbruik, $dbzonphp, &$force, $newData, &$mqtt, $time) {
    $vandaag = date("Y-m-d", $time);
    $gisterenDatum = date("Y-m-d", strtotime("-1 day", $time));

    // 1. Kwartierpiek ophalen
    $kwartierpiek = 2500;
    $q = "SELECT MAX(wH) AS wH FROM `kwartierpiek` WHERE date LIKE :date";
    $stmt = $dbverbruik->query($q, [':date' => date('Y-m', $time) . '-%']);
    if ($row = $stmt->fetch()) {
        $kwartierpiek = $row['wH'] ?? 2500;
    }

    // 2. Cache 'en' ophalen (retry loop)
    $en = null;
    for ($x = 1; $x <= 5; $x++) {
        $en = json_decode(getCache('en'));
        if ($en) break;
        usleep(100000);
    }

    // DEBUG LOGGING voor data-integriteit
    if (count($newData) != 4 || !$en) {
        lg("⚠️ Onderbroken: newData count=" . count($newData) . " (G:" . ($newData['gas'] ?? 'N/A') . ") en=" . ($en ? 'OK' : 'FAIL'));
        return;
    }

    $gasStand    = $newData['gas'];
    $elecStand   = $newData['import'];
    $injectie    = $newData['export'];
    $waterStand  = $newData['water'];
    $alwayson    = (int)getCache('alwayson');
    $newavg      = $en->a;
    $prevavg     = (float)getCache('energy_prevavg');

    // 3. Alwayson logica
    if ($en->z == 0 || empty($alwayson)) {
        $power = ($en->b < 0) ? ($en->n - $en->b) : $en->n;
        if ($power >= 30 && ($power < $alwayson || empty($alwayson))) {
            setCache('alwayson', $power);
            $alwayson = $power;
            $force = true;
            lg('💡 New alwayson ' . $power . ' W');
            $q = "INSERT INTO `alwayson` (`date`, `w`) VALUES (:date, :w) ON DUPLICATE KEY UPDATE `w` = VALUES(`w`)";
            $dbverbruik->query($q, [':date' => $vandaag, ':w' => $alwayson]);
        }
    }

    // 4. Zon data ophalen
    $zonvandaag = 0; $zontotaal = 0;
    $q = "SELECT Geg_Maand FROM `tgeg_maand` WHERE `Datum_Maand` = :datum";
    $stmt = $dbzonphp->query($q, [':datum' => $vandaag . ' 0:00:00']);
    if ($row = $stmt->fetch()) $zonvandaag = $row['Geg_Maand'];

    $q = "SELECT SUM(Geg_Maand) AS Geg_Maand FROM `tgeg_maand`";
    $stmt = $dbzonphp->query($q);
    if ($row = $stmt->fetch()) $zontotaal = $row['Geg_Maand'];

    // 5. Tabel 'Guy' updaten (Totaalstanden)
    $q = "INSERT INTO `Guy` (`date`, `gas`, `elec`, `injectie`, `zon`, `water`)
          VALUES (:date, :gas, :elec, :injectie, :zon, :water)
          ON DUPLICATE KEY UPDATE gas=VALUES(gas), elec=VALUES(elec), injectie=VALUES(injectie), zon=VALUES(zon), water=VALUES(water)";

    try {
        $dbverbruik->query($q, [
            ':date' => $vandaag, ':gas' => $gasStand, ':elec' => $elecStand,
            ':injectie' => $injectie, ':zon' => $zontotaal, ':water' => $waterStand
        ]);
    } catch (Exception $e) {
        lg("❌ Error Guy update: " . $e->getMessage());
    }

    // 6. Bereken Dagverbruik (Guydag)
    $q = "SELECT gas, elec, injectie, water FROM `Guy` WHERE `date` = :gisteren";
    $stmt = $dbverbruik->query($q, [':gisteren' => $gisterenDatum]);
    $gisteren = $stmt->fetch();

    $dagGas = 0; $dagElec = 0; $dagWater = 0; $dagVerbruik = 0;

    if ($gisteren) {
        $dagGas      = round((float)$gasStand - (float)$gisteren['gas'], 3);
        $dagElec     = round((float)$elecStand - (float)$gisteren['elec'], 3);
        $dagWater    = round((float)$waterStand - (float)$gisteren['water'], 3);
        $dagInjectie = round((float)$injectie - (float)$gisteren['injectie'], 3);
        $dagVerbruik = round((float)$zonvandaag - $dagInjectie + $dagElec, 3);

        $q = "INSERT INTO `Guydag` (`date`, `gas`, `elec`, `verbruik`, `zon`, `water`)
              VALUES (:date, :gas, :elec, :verbruik, :zon, :water)
              ON DUPLICATE KEY UPDATE gas=VALUES(gas), elec=VALUES(elec), verbruik=VALUES(verbruik), zon=VALUES(zon), water=VALUES(water)";

        try {
            $dbverbruik->query($q, [
                ':date' => $vandaag, ':gas' => $dagGas, ':elec' => $dagElec,
                ':verbruik' => $dagVerbruik, ':zon' => $zonvandaag, ':water' => $dagWater
            ]);
        } catch (Exception $e) {
            lg("❌ Error Guydag update: " . $e->getMessage());
        }
    } else {
        // Belangrijk: als gisteren niet gevonden wordt, log dit!
        lg("⚠️ Geen gisteren data gevonden voor $gisterenDatum in tabel Guy.");
    }

    // 7. Statistieken & Gemiddelden
    $since = date("Y-m-d", $time - (86400 * 30));
    $avg = ['gas' => 0, 'elec' => 0];
    $q = "SELECT AVG(gas) AS gas, AVG(elec) AS elec FROM `Guydag` WHERE date >= :since AND date < CURRENT_DATE()";
    $stmt = $dbverbruik->query($q, [':since' => $since]);
    if ($row = $stmt->fetch()) $avg = $row;

    $maand = date('m', $time);
    $zonref = 0; $zonavg = 0;
    $q = "SELECT Dag_Refer FROM `tgeg_refer` WHERE Datum_Refer = :datum";
    $stmt = $dbzonphp->query($q, [':datum' => '2009-' . $maand . '-01 00:00:00']);
    if ($row = $stmt->fetch()) $zonref = round($row['Dag_Refer'], 1);

    $q = "SELECT AVG(Geg_Dag) AS AVG FROM `tgeg_dag` WHERE Datum_Dag LIKE :maand
          AND Geg_Dag > (SELECT MAX(Geg_Dag)/2 FROM tgeg_dag WHERE Datum_Dag LIKE :maand2)";
    $stmt = $dbzonphp->query($q, [':maand' => '%-' . $maand . '-%', ':maand2' => '%-' . $maand . '-%']);
    if ($row = $stmt->fetch()) $zonavg = round($row['AVG'], 0);

    // 8. MQTT & Cache
    $dataArray = [
        'gas' => round($dagGas, 2),
        'gasavg' => round((float)$avg['gas'], 2),
        'elec' => round($dagElec, 2),
        'elecavg' => round((float)$avg['elec'], 2),
        'verbruik' => $dagVerbruik,
        'zon' => round($zonvandaag, 2),
        'zonref' => round($zonref, 2),
        'zonavg' => round($zonavg),
        'alwayson' => $alwayson
    ];

    setCache('energy_vandaag', json_encode($dataArray));

    static $mqttcache = [];
    static $lastDate = null;

    if ($lastDate !== $vandaag) {
        $mqttcache = [];
        $lastDate = $vandaag;
        $force = true;
        lg("📅 Dagwissel gedetecteerd ($vandaag), cache gereset.");
    }

    $den = [
        'gas' => $dataArray['gas'],
        'elec' => $dataArray['elec'],
        'zon' => $dataArray['zon'],
        'alwayson' => $dataArray['alwayson'],
    ];

    foreach ($den as $k => $v) {
        if ($force || !isset($mqttcache[$k]) || $mqttcache[$k] !== $v) {
            publishmqtt('d/e/' . $k, $v);
            $mqttcache[$k] = $v;
        }
    }

    $force = false;
    setCache('energy_prevavg', $newavg);
}
function lg($msg) {
	$fp = fopen('/temp/domoticz.log', "a+");
	$time = microtime(true);
	$dFormat = "d-m H:i:s";
	$mSecs = $time - floor($time);
	$mSecs = substr(number_format($mSecs, 3), 1);
	fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
	fclose($fp);
}
function setCache(string $key, $value): bool {
    return file_put_contents('/dev/shm/cache/' . $key .'.txt', $value, LOCK_EX) !== false;
}
function getCache(string $key, $default = false) {
    $data = @file_get_contents('/dev/shm/cache/' . $key .'.txt');
    return $data === false ? $default : $data;
}
function publishmqtt($topic,$msg) {
	global $mqtt;
	$mqtt->publish($topic,(string)$msg,1,true);
	lg("🟢 {$topic} {$msg}");
	return;
}
class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $pdo = null;
    private $maxRetries = 3;
    private $retryDelay = 1;
    public function __construct($host, $user, $pass, $dbname) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbname = $dbname;
    }
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true, // Persistent connection
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
            return true;
        } catch (PDOException $e) {
            lg("Database connection failed: " . $e->getMessage());
            return false;
        }
    }
    private function ensureConnection() {
        if ($this->pdo === null) {
            return $this->connect();
        }
        try {
            $this->pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            lg("Connection lost, reconnecting: " . $e->getMessage());
            $this->pdo = null;
            return $this->connect();
        }
    }
    public function query($sql, $params = []) {
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            if (!$this->ensureConnection()) {
                if ($attempt < $this->maxRetries) {
                    sleep($this->retryDelay);
                    continue;
                }
                throw new Exception("Failed to connect to database after {$this->maxRetries} attempts");
            }
            try {
                if (empty($params)) {
                    return $this->pdo->query($sql);
                } else {
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute($params);
                    return $stmt;
                }
            } catch (PDOException $e) {
                if (in_array($e->getCode(), ['HY000', '2006', '2013'])) {
                    lg("Query failed (attempt {$attempt}): " . $e->getMessage());
                    $this->pdo = null;
                    if ($attempt < $this->maxRetries) {
                        sleep($this->retryDelay);
                        continue;
                    }
                }
                throw $e;
            }
        }
        throw new Exception("Query failed after {$this->maxRetries} attempts");
    }
    public function exec($sql) {
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            if (!$this->ensureConnection()) {
                if ($attempt < $this->maxRetries) {
                    sleep($this->retryDelay);
                    continue;
                }
                throw new Exception("Failed to connect to database after {$this->maxRetries} attempts");
            }
            try {
                return $this->pdo->exec($sql);
            } catch (PDOException $e) {
                if (in_array($e->getCode(), ['HY000', '2006', '2013'])) {
                    lg("Exec failed (attempt {$attempt}): " . $e->getMessage());
                    $this->pdo = null;
                    if ($attempt < $this->maxRetries) {
                        sleep($this->retryDelay);
                        continue;
                    }
                }
                throw $e;
            }
        }
        throw new Exception("Exec failed after {$this->maxRetries} attempts");
    }
}
function stoploop() {
    global $mqtt,$lock_file;
    $script = __FILE__;
    if (filemtime(__DIR__ . '/functions.php') > LOOP_START) {
        lg('🛑 functions.php gewijzigd → restarting '.basename($script).' loop...');
        $mqtt->disconnect();
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 5 /usr/bin/php8.2 $script > /dev/null 2>&1 &");
        exit;
    }
    if (filemtime($script) > LOOP_START) {
        lg('🛑 '.basename($script) . ' gewijzigd → restarting ...');
        $mqtt->disconnect();
        ftruncate($lock_file, 0);
		flock($lock_file, LOCK_UN);
		exec("nice -n 5 /usr/bin/php8.2 $script > /dev/null 2>&1 &");
        exit;
    }
}
