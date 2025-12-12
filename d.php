<?php
require '/var/www/config.php';
$time = $_SERVER['REQUEST_TIME'];
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
$map = [
    '192.168.2.201' => 'Mac',
    '192.168.4.2'   => 'Mac',
    '192.168.2.203' => 'iPhoneGuy',
    '192.168.4.3'   => 'iPhoneGuy',
    '192.168.2.200' => 'iPadGuy',
    '192.168.4.4'   => 'iPadGuy',
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
        $t -= 3600;
    }
}

$sql = "SELECT n,s,t,m,dt,icon,rt,p FROM devices WHERE `$filter`=1";
if ($t > 0) {
    $sql .= " AND `t`>=$t";
}

// Haal energy data op (alleen voor type 'f')
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
    } else {
        lg("Can't fetch en");
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

$db = dbconnect();
$stmt = $db->query($sql);
$rows = $stmt->fetchAll();

$extralast = apcu_fetch($id.$type.'e');
if ($extralast === false || $extra === true) {
    $sunrise = apcu_fetch('cache_sunrise');
    if ($sunrise === false) {
        $sunrise = getCache('sunrise');
        if ($sunrise) {
            apcu_store('cache_sunrise', $sunrise, 300);
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
    
    $thermo_hist = apcu_fetch('thermo_hist');
    if ($thermo_hist !== false) {
        $d['thermo_hist'] = json_decode($thermo_hist, true);
    }
    
    apcu_store($id.$type.'e', $time, 3600);
}

// Verwerk device data
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
            if (json_last_error() === JSON_ERROR_NONE) {
                $d[$n]['icon'] = $icon;
            } else {
                lg("INVALID JSON FOR ICON: $n >> " . $row['icon']);
            }
        } else {
            $d[$n]['icon'] = $row['icon'];
        }
    }
    
    if (!is_null($row['p'])) {
        $d[$n]['p'] = $row['p'];
    }
}

echo json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

function dbconnect() {
    global $dbname, $dbuser, $dbpass;
    static $db = null;
    
    if ($db !== null) {
        return $db;
    }
    
    try {
        $db = new PDO("mysql:host=192.168.2.23;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_PERSISTENT => true,
        ]);
        return $db;
    } catch (PDOException $e) {
        lg('‼️ PDO fout: ' . $e->getMessage());
        $db = null;
        throw $e;
    }
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
function getCache(string $key, $default = false) {
    $data = @file_get_contents('/dev/shm/cache/' . $key . '.txt');
    return $data === false ? $default : $data;
}
function lg($msg) {
    $time = microtime(true);
    $mSecs = substr(number_format($time - floor($time), 3), 1);
    $line = sprintf("%s%s %s\n", date("d-m H:i:s", $time), $mSecs, $msg);
    file_put_contents('/temp/domoticz.log', $line, FILE_APPEND | LOCK_EX);
}