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
lg('🟢 Starting ' . $user . ' loop ');
$time = time();
$lastcheck = $time;
date_default_timezone_set('Europe/Brussels');
$startloop = microtime(true);
define('LOOP_START', $startloop);
$connectionSettings = (new ConnectionSettings)
    ->setUsername('mqtt')
    ->setPassword('mqtt');

$mqtt = new MqttClient('192.168.30.22', 1883, basename(__FILE__), MqttClient::MQTT_3_1);
$mqtt->connect($connectionSettings, true);

$dbverbruik = new Database('192.168.30.23', 'dbuser', 'dbuser', 'verbruik');
$dbzonphp = new Database('192.168.30.23', 'dbuser', 'dbuser', 'zon');

$force = true;

define('BUFFER_SIZE',     20);   // laatste 30 gesynchroniseerde metingen
define('MAX_AGE_SEC',     60);   // max leeftijd per meter-waarde
define('MIN_ALWAYSON',    50);   // negeer ruis onder 50W
define('MAX_STD_DEV',      8);   // max toegelaten standaarddeviatie (W)
define('MIN_BUFFER_FILL', 15);   // wacht tot buffer minstens half gevuld is


// CRUCIAAL: Initialiseer de array met default waarden uit cache om count mismatch te voorkomen
$storedTeller = json_decode(getCache('teller'), true);
$newData = [
    'import' => $storedTeller['import'],
    'export' => $storedTeller['export'],
    'gas'    => $storedTeller['gas'],
    'water'  => $storedTeller['water']
];
$alwayson    = (int)getCache('alwayson');
$peakpower   = (int)getCache('peakpower');
$mqtt->subscribe('t/+', function (string $topic, string $status) use (&$time, &$lastcheck, &$newData, $dbverbruik, $dbzonphp, &$force, &$mqtt, &$alwayson, &$peakpower) {
	try {
		lg($topic.'	'.$status);
		$time = time();
		$changed = false;

		if ($topic == 't/import') { $newData['import'] = $status; $changed = true; }
		elseif ($topic == 't/export') { $newData['export'] = $status; $changed = true; }
		elseif ($topic == 't/gas') { $newData['gas'] = $status; $changed = true; }
		elseif ($topic == 't/water') { $newData['water'] = $status; $changed = true; }

		if ($changed) {
			// Sla de laatste standen direct op in cache
			setCache('teller', json_encode($newData));
			processEnergyData($dbverbruik, $dbzonphp, $force, $newData, $mqtt, $time, $alwayson);
		}
	} catch (Throwable $e) {
		lg("‼️ Fout in MQTT_energy: " . __LINE__ . ' ' . $topic . ' ' . $e->getMessage());
	}
    if ($lastcheck < $time - 5) {
        $lastcheck = $time;
        stoploop();
    }
}, MqttClient::QOS_AT_LEAST_ONCE);

$mqtt->subscribe('d/e/+', function (string $topic, string $status)
    use (&$time, &$lastcheck, &$newData, $dbverbruik, $dbzonphp,
        &$force, &$mqtt, &$alwayson, &$peakpower)
{
    if($topic==='alwayson') return;
    $topic = substr($topic, -1);
    static $n = 0;
    static $z = 0;
    static $b = 0;
    static $a = 0;
    static $timestamps  = ['n' => 0, 'z' => 0, 'b' => 0];
    static $powerBuffer = [];

    ${$topic} = $status;
    $timestamps[$topic] = time();

    // --- Peakpower zonnepanelen (altijd uitvoeren, onafhankelijk van buffer) ---
    if ($topic === 'z') {
        if ($z > $peakpower || empty($peakpower)) {
            $peakpower = $z;
            setCache('peakpower', $peakpower);
            $msg = 'Solar peak power = ' . $peakpower . 'W';
            shell_exec('/var/www/html/secure/telegram.sh "' . $msg . '" "false" "1" > /dev/null 2>/dev/null &');
        }
    }

    // --- Kwartierpiek (altijd uitvoeren, onafhankelijk van buffer) ---
    if ($topic === 'a') {
        $newavg  = $a;
        $prevavg = (float)getCache('energy_prevavg');

        if ($a > 2500) {
            $kwartierpiek = 2500;
            $q    = "SELECT MAX(wH) AS wH FROM `kwartierpiek` WHERE date LIKE :date";
            $stmt = $dbverbruik->query($q, [':date' => date('Y-m', $time) . '-%']);
            if ($row = $stmt->fetch()) {
                $kwartierpiek = $row['wH'] ?? 2500;
            }
            if ($prevavg > 2500) {
                if ($newavg > $kwartierpiek - 200) {
                    alert('Kwartierpiek',
                        'Kwartierpiek momenteel al ' . $newavg . ' Wh!' . PHP_EOL .
                        'Piek deze maand = ' . $kwartierpiek . ' Wh', $time);
                }
                if ($newavg < $prevavg) {
                    try {
                        $q = "INSERT INTO `kwartierpiek` (`date`, `wh`) VALUES (:date, :wh)";
                        $dbverbruik->query($q, [':date' => date('Y-m-d H:i:s'), ':wh' => $prevavg]);
                        if ($prevavg > $kwartierpiek - 200) {
                            alert('KwartierpiekB',
                                'Kwartierpiek = ' . $prevavg . ' Wh' . PHP_EOL .
                                'Vorige piek deze maand = ' . $kwartierpiek . ' Wh', $time);
                            $kwartierpiek = $prevavg;
                        }
                    } catch (Exception $e) {
                        lg("Error updating kwartierpiek: " . $e->getMessage());
                    }
                }
            }
        }
        setCache('energy_prevavg', $newavg);
    }

    // --- Alwayson: wacht tot alle meters recent zijn ---
    $now = time();
    $allFresh = ($now - $timestamps['n']) < MAX_AGE_SEC
             && ($now - $timestamps['z']) < MAX_AGE_SEC
             && (($now - $timestamps['b']) < MAX_AGE_SEC || $b == 0 || $b == 800);

    if (!$allFresh) return;

    $p = $n + $z - $b;
    echo "n=$n\tz=$z\tb=$b\tp=$p" . PHP_EOL;

    $powerBuffer[] = $p;
    if (count($powerBuffer) > BUFFER_SIZE) {
        array_shift($powerBuffer);
    }

    if (count($powerBuffer) < MIN_BUFFER_FILL) return;

    $avg    = array_sum($powerBuffer) / count($powerBuffer);
    $stddev = sqrt(array_sum(array_map(
        fn($v) => ($v - $avg) ** 2, $powerBuffer
    )) / count($powerBuffer));

    $sorted = $powerBuffer;
    sort($sorted);
    $count  = count($sorted);
    $median = $count % 2 === 0
        ? ($sorted[$count/2 - 1] + $sorted[$count/2]) / 2
        : $sorted[intval($count/2)];
    $measurement = round($median);

    echo "stddev=" . round($stddev) . "\tmeasurement=$measurement\tstable=" . ($stddev < MAX_STD_DEV ? 'ja' : 'nee') . PHP_EOL;

    if ($stddev < MAX_STD_DEV && $measurement >= MIN_ALWAYSON) {
        if ($measurement < $alwayson || empty($alwayson)) {
            $alwayson = $measurement;
            setCache('alwayson', $alwayson);
            lg('💡 New alwayson ' . $alwayson . ' W (stddev=' . round($stddev) . ')');
            publishmqtt('d/e/alwayson', $alwayson);
            $q = "INSERT INTO `alwayson` (`date`, `w`) VALUES (:date, :w)
                  ON DUPLICATE KEY UPDATE `w` = VALUES(`w`)";
            $dbverbruik->query($q, [':date' => date('Y-m-d'), ':w' => $alwayson]);
        }
    }

}, MqttClient::QOS_AT_LEAST_ONCE);


while (true) {
    $mqtt->loop(true, false, null, 50000);
}

$mqtt->disconnect();

// --- FUNCTIES ---
function processEnergyData($dbverbruik, $dbzonphp, &$force, $newData, &$mqtt, $time, &$alwayson) {
    static $mqttcache = [];
    static $lastDate = null;
    static $gisteren = null;
	static $zonref = 0;
	static $zonavg = 0;
	static $avg = ['gas' => 0, 'elec' => 0];
	$vandaag = date("Y-m-d", $time);
    $gisterenDatum = date("Y-m-d", strtotime("-1 day", $time));

    // 1. Kwartierpiek ophalen
    $kwartierpiek = 2500;
    $q = "SELECT MAX(wH) AS wH FROM `kwartierpiek` WHERE date LIKE :date";
    $stmt = $dbverbruik->query($q, [':date' => date('Y-m', $time) . '-%']);
    if ($row = $stmt->fetch()) {
        $kwartierpiek = $row['wH'] ?? 2500;
    }

    $gasStand    = $newData['gas'];
    $elecStand   = $newData['import'];
    $injectie    = $newData['export'];
    $waterStand  = $newData['water'];
    
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
	$opts=[
            ':date' => $vandaag, ':gas' => $gasStand, ':elec' => $elecStand,
            ':injectie' => $injectie, ':zon' => $zontotaal, ':water' => $waterStand
        ];
    lg($q.'
'.json_encode($opts));
    try {
        $dbverbruik->query($q, $opts);
    } catch (Exception $e) {
        lg("❌ Error Guy update: " . $e->getMessage());
    }
    // 6. Bereken Dagverbruik (Guydag)
    if ($lastDate !== $vandaag) {
		$q = "SELECT gas, elec, injectie, water FROM `Guy` ORDER BY date DESC LIMIT 1,1";
		$stmt = $dbverbruik->query($q);
		$gisteren = $stmt->fetch();
	}
    $dagGas = 0; $dagElec = 0; $dagWater = 0; $dagVerbruik = 0;

    if ($gisteren) {
        $dagGas      = round((float)$gasStand - (float)$gisteren['gas'], 3);
        $dagElec     = round((float)$elecStand - (float)$gisteren['elec'], 3);
        if($dagGas>=0&&$dagElec>=0) {
			$dagWater    = round((float)$waterStand - (float)$gisteren['water'], 3);
			$dagInjectie = round((float)$injectie - (float)$gisteren['injectie'], 3);
			$dagVerbruik = round((float)$zonvandaag - $dagInjectie + $dagElec, 3);
	
			$q = "INSERT INTO `Guydag` (`date`, `gas`, `elec`, `verbruik`, `zon`, `water`)
				  VALUES (:date, :gas, :elec, :verbruik, :zon, :water)
				  ON DUPLICATE KEY UPDATE gas=VALUES(gas), elec=VALUES(elec), verbruik=VALUES(verbruik), zon=VALUES(zon), water=VALUES(water)";
			$opts=[
					':date' => $vandaag, ':gas' => $dagGas, ':elec' => $dagElec,
					':verbruik' => $dagVerbruik, ':zon' => $zonvandaag, ':water' => $dagWater
				];
			lg($q.'
	'.json_encode($opts));
			try {
				$dbverbruik->query($q, $opts);
			} catch (Exception $e) {
				lg("❌ Error Guydag update: " . $e->getMessage());
			}
		}
    } else {
        // Belangrijk: als gisteren niet gevonden wordt, log dit!
        lg("⚠️ Geen gisteren data gevonden voor $gisterenDatum in tabel Guy.");
    }
	if ($lastDate !== $vandaag) {
		// 7. Statistieken & Gemiddelden
		$today = date('Y-m-d');
		$dy = (int)date('z', strtotime($today)) + 1;
		$range = 10;
		$start = $dy - $range;
		$end = $dy + $range;
		
		if ($start <= 0 || $end > 366) {
			$sVal = $start <= 0 ? 366 + $start : $start;
			$eVal = $end > 366 ? $end - 366 : $end;
			$whereGas = "(DAYOFYEAR(date) >= :start OR DAYOFYEAR(date) <= :end)";
			$whereZon = "(DAYOFYEAR(Datum_Dag) >= :start OR DAYOFYEAR(Datum_Dag) <= :end)";
			$whereSub = "(DAYOFYEAR(Datum_Dag) >= :s2 OR DAYOFYEAR(Datum_Dag) <= :e2)";
			$params = [':start' => $sVal, ':end' => $eVal];
			$paramsZon = [':start' => $sVal, ':end' => $eVal, ':s2' => $sVal, ':e2' => $eVal];
		} else {
			$whereGas = "DAYOFYEAR(date) BETWEEN :start AND :end";
			$whereZon = "DAYOFYEAR(Datum_Dag) BETWEEN :start AND :end";
			$whereSub = "DAYOFYEAR(Datum_Dag) BETWEEN :s2 AND :e2";
			$params = [':start' => $start, ':end' => $end];
			$paramsZon = [':start' => $start, ':end' => $end, ':s2' => $start, ':e2' => $end];
		}
		
		$qGas = "SELECT AVG(gas) AS gas, AVG(elec) AS elec FROM `Guydag` WHERE $whereGas";
		$stmtGas = $dbverbruik->query($qGas, $params);
		if ($rowGas = $stmtGas->fetch()) $avg = $rowGas;
		
		$qZon = "SELECT AVG(Geg_Dag) AS AVG FROM `tgeg_dag` WHERE $whereZon AND Geg_Dag > (SELECT MAX(Geg_Dag)/2 FROM tgeg_dag WHERE $whereSub)";
		$stmtZon = $dbzonphp->query($qZon, $paramsZon);
		if ($rowZon = $stmtZon->fetch()) $zonavg = round($rowZon['AVG'], 0);
	
		$maand = date('m', $time);
		
		$q = "SELECT Dag_Refer FROM `tgeg_refer` WHERE Datum_Refer = :datum";
		$stmt = $dbzonphp->query($q, [':datum' => '2009-' . $maand . '-01 00:00:00']);
		if ($row = $stmt->fetch()) $zonref = round($row['Dag_Refer'], 1);
	
	}
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

    
    if ($lastDate !== $vandaag) {
        $mqttcache = [];
        $lastDate = $vandaag;
        $force = true;
        $alwayson=500;
        lg("📅 Dagwissel gedetecteerd ($vandaag), cache gereset.");
    }

    $den = [
        'gas' => $dataArray['gas'],
        'elec' => $dataArray['elec'],
        'zon' => $dataArray['zon'],
//        'alwayson' => $dataArray['alwayson'],
    ];
    $dailyen=json_encode([
		'gasavg' => $dataArray['gasavg'],
		'elecavg' => $dataArray['elecavg'],
		'zonavg' => $dataArray['zonavg'],
		'zonref' => $dataArray['zonref'],
	],JSON_NUMERIC_CHECK);
	if ($force || !isset($mqttcache['dailyen']) || $mqttcache['dailyen']!=$dailyen) {
		publishmqtt('d/e/dailyen', $dailyen);
		$mqttcache['dailyen'] = $dailyen;
	}
    foreach ($den as $k => $v) {
        if ($force || !isset($mqttcache[$k]) || $mqttcache[$k] !== $v) {
            publishmqtt('d/e/' . $k, $v);
            $mqttcache[$k] = $v;
        }
    }
    $force = false;
}
function lg($msg) {
	echo $msg."\n";
	$fp = fopen('/var/log/mqtt/energy.log', "a+");
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
function alert($name,$msg,$time) {
	$last=0;
	$db = new Database('192.168.30.23', 'dbuser', 'dbuser', 'domotica');
	$stmt=$db->query("SELECT t FROM alerts WHERE n='$name';");
	while ($row=$stmt->fetch(PDO::FETCH_NUM)) {
		if (isset($row[0])) $last=$row[0];
	}
	if ($last < $time-300) {
		shell_exec('/var/www/html/secure/telegram.sh "'.$msg.'" "false" "1" > /dev/null 2>/dev/null &');
		$db->query("INSERT INTO alerts (n,t) VALUES ('$name','$time') ON DUPLICATE KEY UPDATE t='$time';");
	}
}
function clamp($v,$min,$max){return max($min,min($max,$v));}

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
