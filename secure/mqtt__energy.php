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
ini_set('error_reporting',E_ALL);
ini_set('display_errors',true);
// Using https://github.com/php-mqtt/client
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
require_once '/var/www/vendor/autoload.php';
$user='ENERGY';
lg('🟢 Starting '.$user.' loop ',-1);
$time=time();
$lastcheck=$time;
$lastDayUpdate = null;
date_default_timezone_set('Europe/Brussels');
$startloop=microtime(true);
define('LOOP_START', $startloop);
$d['rand']=rand(10,20);
$connectionSettings=(new ConnectionSettings)
	->setUsername('mqtt')
	->setPassword('mqtt');
$mqtt=new MqttClient('192.168.2.22',1883,basename(__FILE__),MqttClient::MQTT_3_1);
$mqtt->connect($connectionSettings,true);
$dbverbruik = new Database('192.168.2.20', 'home', 'H0m€', 'verbruik');
$dbzonphp = new Database('192.168.2.20', 'home', 'H0m€', 'egregius_zonphp');
$force=true;
$newData = json_decode(getCache('teller'),true);
$mqtt->subscribe('t/+', function (string $topic, string $status) use (&$d,&$time,&$lastcheck,&$newData,$dbverbruik,$dbzonphp,&$force,&$mqtt) {
	$time=time();
	if($topic=='t/import') $newData['import']=$status;
    elseif($topic=='t/export') $newData['export']=$status;
    elseif($topic=='t/gas') $newData['gas']=$status;
    elseif($topic=='t/water') $newData['water']=$status;
    processEnergyData($dbverbruik, $dbzonphp, $force, $newData,$mqtt);
	if ($lastcheck < $time - $d['rand']) {
        $lastcheck = $time;
        stoploop();
    }
}, MqttClient::QOS_AT_LEAST_ONCE);

while (true) {
	$mqtt->loop(true,false,null,50000);
}
$mqtt->disconnect();
lg("🛑 MQTT {$user} loop stopped ".__FILE__,1);

function processEnergyData($dbverbruik, $dbzonphp, &$force, $newData, &$mqtt) {
	$kwartierpiek = 2500;
	$q = "SELECT MAX(wH) AS wH FROM `kwartierpiek` WHERE date LIKE :date";
	$stmt = $dbverbruik->query($q, [':date' => date('Y-m') . '-%']);
	if ($row = $stmt->fetch()) {
		$kwartierpiek = $row['wH'] ?? 2500;
	}
	for ($x=1; $x<=5; $x++) {
		$en = json_decode(getCache('en'));
		if ($en) break;
	}
	if(count($newData)!=4) {
		lg('Not enough data in $newData:'.print_r($newData,true));
		return;
	}
	$zon = $en->z;
	$gas = $newData['gas'];
	$elec = $newData['import'];
	$injectie = $newData['export'];
	$water = $newData['water'];
	$alwayson = (int)getCache('alwayson');
	$newavg = $en->a;
	$prevavg = getCache('energy_prevavg');
	if ($zon == 0 || empty($alwayson)) {
		if ($en->b < 0) {
			$power = $en->n - $en->b;
		} else {
			$power = $en->n;
		}
		if ($power >= 30 && ($power < $alwayson || empty($alwayson))) {
			setCache('alwayson', $power);
			$alwayson = $power;
			$force = true;
			$time = time();
			lg('New alwayson ' . $power . ' W');
			$vandaag = date("Y-m-d", $time);
			try {
				$q = "INSERT INTO `alwayson` (`date`, `w`) VALUES (:date, :w) ON DUPLICATE KEY UPDATE `w` = :w2";
				$dbverbruik->query($q, [':date' => $vandaag, ':w' => $alwayson, ':w2' => $alwayson]);
			} catch (Exception $e) {
				lg("Error updating alwayson: " . $e->getMessage());
			}
		}
	}
	if ($prevavg > 2500) {
		if ($newavg > $kwartierpiek - 200) {
			//alert('Kwartierpiek', 'Kwartierpiek momenteel al ' . $newavg . ' Wh!' . PHP_EOL . 'Piek deze maand = ' . $kwartierpiek . ' Wh', 120, false);
		}
		if ($newavg < $prevavg) {
			try {
				$q = "INSERT INTO `kwartierpiek` (`date`, `wh`) VALUES (:date, :wh)";
				$dbverbruik->query($q, [':date' => date('Y-m-d H:i:s'), ':wh' => $prevavg]);
				if ($prevavg > $kwartierpiek - 200) {
					//alert('KwartierpiekB', 'Kwartierpiek = ' . $prevavg . ' Wh' . PHP_EOL . 'Piek deze maand = ' . $kwartierpiek . ' Wh', 30, false);
					$kwartierpiek = $prevavg;
				}
			} catch (Exception $e) {
				lg("Error updating kwartierpiek: " . $e->getMessage());
			}
		}
	}
	$prevwater = getCache('water_meter');
	if ($prevwater != $water && getCache('weg') > 2) {
		setCache('water_meter', $water);
		alert('water_meter', 'Water verbruik gedetecteerd!', 300, true);
		lg("Waterteller: prev=$prevwater, nu=$water");
	}
	$time = time();
	$vandaag = date("Y-m-d", $time);
	$zonvandaag = 0;
	$zontotaal = 0;
	$q = "SELECT Geg_Maand FROM `tgeg_maand` WHERE `Datum_Maand` = :datum";
	$stmt = $dbzonphp->query($q, [':datum' => $vandaag . '  0:00:00']);
	if ($row = $stmt->fetch()) {
		$zonvandaag = $row['Geg_Maand'];
	}
	$q = "SELECT SUM(Geg_Maand) AS Geg_Maand FROM `tgeg_maand`";
	$stmt = $dbzonphp->query($q);
	if ($row = $stmt->fetch()) {
		$zontotaal = $row['Geg_Maand'];
	}

	$q = "INSERT INTO `Guy` (`date`, `gas`, `elec`, `injectie`, `zon`, `water`)
		  VALUES (:date, :gas, :elec, :injectie, :zon, :water)
		  ON DUPLICATE KEY UPDATE gas = :gas2, elec = :elec2, injectie = :injectie2, zon = :zon2, water = :water2";
	$dbverbruik->query($q, [
		':date' => $vandaag,
		':gas' => $gas,
		':elec' => $elec,
		':injectie' => $injectie,
		':zon' => $zontotaal,
		':water' => $water,
		':gas2' => $gas,
		':elec2' => $elec,
		':injectie2' => $injectie,
		':zon2' => $zontotaal,
		':water2' => $water
	]);

	$gisteren = null;
	$q = "SELECT `date`, `gas`, `elec`, `injectie`, `water` FROM `Guy` ORDER BY `date` DESC LIMIT 1,1";
	$stmt = $dbverbruik->query($q);
	$gisteren = $stmt->fetch();

	if ($gisteren) {
		$gas = round($gas - $gisteren['gas'], 3);
		$elec = round($elec - $gisteren['elec'], 3);
		$water = round($water - $gisteren['water'], 3);
		$injectie = round($injectie - $gisteren['injectie'], 3);
		$verbruik = round($zonvandaag - $injectie + $elec, 3);
		$q = "INSERT INTO `Guydag` (`date`, `gas`, `elec`, `verbruik`, `zon`, `water`)
			  VALUES (:date, :gas, :elec, :verbruik, :zon, :water)
			  ON DUPLICATE KEY UPDATE gas = :gas2, elec = :elec2, verbruik = :verbruik2, zon = :zon2, water = :water2";
		$dbverbruik->query($q, [
			':date' => $vandaag,
			':gas' => $gas,
			':elec' => $elec,
			':verbruik' => $verbruik,
			':zon' => $zonvandaag,
			':water' => $water,
			':gas2' => $gas,
			':elec2' => $elec,
			':verbruik2' => $verbruik,
			':zon2' => $zonvandaag,
			':water2' => $water
		]);

	}
	$since = date("Y-m-d", $time - (86400 * 30));
	$avg = ['gas' => 0, 'elec' => 0];
	$q = "
		SELECT
			AVG(gas)  AS gas,
			AVG(elec) AS elec
		FROM `Guydag`
		WHERE date >= :since
		  AND date < CURRENT_DATE()
	";
	$stmt = $dbverbruik->query($q, [':since' => $since]);
	if ($row = $stmt->fetch()) {
		$avg = $row;
	}

	$maand = date('m');
	$zonref = 0;
	$zonavg = 0;

	$q = "SELECT Dag_Refer FROM `tgeg_refer` WHERE Datum_Refer = :datum";
	$stmt = $dbzonphp->query($q, [':datum' => '2009-' . $maand . '-01 00:00:00']);
	if ($row = $stmt->fetch()) {
		$zonref = round($row['Dag_Refer'], 1);
	}

	$q = "SELECT AVG(Geg_Dag) AS AVG FROM `tgeg_dag`
		  WHERE Datum_Dag LIKE :maand
		  AND Geg_Dag > (SELECT MAX(Geg_Dag)/2 FROM tgeg_dag WHERE Datum_Dag LIKE :maand2)";
	$stmt = $dbzonphp->query($q, [':maand' => '%-' . $maand . '-%', ':maand2' => '%-' . $maand . '-%']);
	if ($row = $stmt->fetch()) {
		$zonavg = round($row['AVG'], 0);
	}

	$data = json_encode([
		'gas' => round($gas,2),
		'gasavg' => round((float)$avg['gas'], 2),
		'elec' => round($elec,2),
		'elecavg' => round((float)$avg['elec'], 2),
		'verbruik' => $verbruik,
		'zon' => round($zonvandaag,2),
		'zonref' => round($zonref,2),
		'zonavg' => round($zonavg),
		'alwayson' => $alwayson
	]);
	setCache('energy_vandaag', $data);
	setCache('energy_lastupdate', $time);
	$data = json_decode($data, true);
	$dailyen=json_encode([
		'gasavg' => $data['gasavg'],
		'elecavg' => $data['elecavg'],
		'zonref' => $data['zonref'],
		'zonavg' => $data['zonavg'],
	]);
	if(!isset($dailyencache)||$dailyencache!==$dailyen) {
		publishmqtt('d/e/dailyen',$dailyen);
		$dailyencache=$dailyen;
	}
	$den=[
		'gas' => $data['gas'],
		'elec' => $data['elec'],
		'zon' => $data['zon'],
		'alwayson' => $data['alwayson'],
	];

	static $mqttcache = [];
	foreach($den as $k => $v) {
		if(!isset($mqttcache[$k]) || $mqttcache[$k] !== $v) {
			publishmqtt('d/e/'.$k,$v);
			$mqttcache[$k] = $v;
		}
	}
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
//	lg("🟢 {$topic} {$msg}");
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
