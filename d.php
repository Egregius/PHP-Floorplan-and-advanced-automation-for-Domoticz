<?php
header('Content-Type: application/json; charset=ISO-8859-1');
$time = $_SERVER['REQUEST_TIME'];
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$map = [
    '192.168.2.201' => 'Mac',
    '192.168.4.2'   => 'Mac',
    '192.168.2.203' => 'iPhoneGuy',
    '192.168.4.3'   => 'iPhoneGuy',
    '192.168.2.200' => 'iPadGuy',
    '192.168.4.4'   => 'iPadGuy',
    '192.168.2.55'   => 'iPhoneKirby',
    '192.168.4.5'   => 'iPhoneKirby',
];
$id = $map[$ip] ?? $ip;
$extra = false;
$verbruik = false;
$d = ['t' => $time];
if (isset($_GET['o'])) {
    $type = 'o';
    $filter = 'o';
} elseif (isset($_GET['h'])) {
    $type = 'h';
    $filter = 'h';
} else {
    $type = 'f';
    $filter = 'f';
    $verbruik = true;
}
if (isset($_GET['all'])) {
    $t = 0;
    $extra = true;
} else {
    $t = apcu_fetch($id.$type);
    if ($t === false) {
        $t = 0;
        $extra = true;
    } elseif ($t < $time - 5) {
        $t -= 600;
        $extra = true;
    }
}
if ($type === 'f') {
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
	if ($extra === true) {
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
    }

}
apcu_store($id.$type, $time, 86400);
$extralast = apcu_fetch($id.$type.'e');
if ($extralast === false || $extra === true) {
    $sunrise = apcu_fetch('cache_sunrise');
    if ($sunrise === false) {
        $sunrise = getCache('sunrise');
        if ($sunrise) {
            apcu_store('cache_sunrise', $sunrise, 14400);
        }
    }
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
    $d['thermo_hist'] = json_decode(getCache('thermo_hist'), true);
    apcu_store($id.$type.'e', $time, 14400);
}
$db = Database::getInstance();
$stmt = $db->prepare("SELECT n,s,t,m,dt,icon,rt,p FROM devices WHERE `$filter`=1 AND t >= :t");
$stmt->execute([':t' => $t]);
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $n = $row[0];
    $d[$n]['s'] = $row[1];
    if ($row[6] == 1) {
        $d[$n]['t'] = $row[2];
    }
    if (!is_null($row[3])) {
        $d[$n]['m'] = $row[3];
    }
    if (!is_null($row[4])) {
        $d[$n]['dt'] = $row[4];
        if ($row[4] === 'daikin') {
            $d[$n]['s'] = null;
        }
    }
    if (!is_null($row[5])) {
        if ($row[4] === 'th' && $n !== 'badkamer_set') {
            $icon = json_decode($row[5], true);
        } else {
            $d[$n]['icon'] = $row[5];
        }
    }
    if (!is_null($row[7])) {
        $d[$n]['p'] = $row[7];
    }
}


echo json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
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
function getCache(string $key, $default = false) {
    $data = @file_get_contents('/dev/shm/cache/' . $key . '.txt');
    return $data === false ? $default : $data;
}
function lg($msg) {
	$fp = fopen('/temp/floorplan-access.log', "a+");
	$time = microtime(true);
	$dFormat = "d-m H:i:s";
	$mSecs = $time - floor($time);
	$mSecs = substr(number_format($mSecs, 3), 1);
	fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
	fclose($fp);
}
class Database {
    private static ?PDO $instance = null;
    private function __construct() {}
    public static function getInstance(): PDO {
    self::$instance = new PDO("mysql:host=192.168.2.23;dbname=domotica;charset=latin1",'dbuser','dbuser',
        [
//                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => true
        ]
        );
        return self::$instance;
    }
}