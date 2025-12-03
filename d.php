<?php
require '/var/www/config.php';
$time = $_SERVER['REQUEST_TIME'];
if (isset($_REQUEST['all'])) {
	$t=0;
} else {
	$lastRequest = getCache($_SERVER['HTTP_X_FORWARDED_FOR']) ?? 1;
	
	if (($time - $lastRequest) > 2) {
		$t = 0;
	} else {
		$t = $lastRequest - 1;
	}
}
setCache($_SERVER['HTTP_X_FORWARDED_FOR'], $time);
$d = array();
$d['t'] = $time;
$db = dbconnect();
if ($t == 0) {
	$stmt = $db->query("SELECT n,s,t,m,dt,icon,ajax FROM devices WHERE ajax>=1;");
} else {
	$stmt = $db->query("SELECT n,s,t,m,dt,icon,ajax FROM devices WHERE ajax>=1 AND t >= $t;");
}

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$d[$row['n']]['s'] = $row['s'];
	if ($row['ajax'] == 2) $d[$row['n']]['t'] = $row['t'];
	if (!is_null($row['m'])) $d[$row['n']]['m'] = $row['m'];
	if (!is_null($row['dt'])) {
		$d[$row['n']]['dt'] = $row['dt'];
		if ($row['dt']=='daikin') {
			$d[$row['n']]['s'] = null;
		}
	} else $row['dt'] = null;
	if (!is_null($row['icon'])) {
		if ($row['dt']=='th'&&$row['n']!='badkamer_set') {
			$d[$row['n']]['icon']=json_decode($row['icon'],true);
			if (json_last_error() !== JSON_ERROR_NONE) echo "INVALID JSON FOR ICON: ".$row['n']." >> ".$row['icon'];
		}
		else $d[$row['n']]['icon']=$row['icon'];
	}
}
$en = json_decode(getCache('en'));
if ($en) {
	$d['n'] = $en->n;
	$d['a'] = $en->a;
	$d['b'] = $en->b;
	$d['c'] = $en->c;
	$d['z'] = $en->z;
}
if ($t == 0) {
	$sunrise = json_decode(getCache('sunrise'), true);
	if ($sunrise) {
		$d['CivTwilightStart'] = $sunrise['CivTwilightStart'];
		$d['Sunrise'] = $sunrise['Sunrise'];
		$d['Sunset'] = $sunrise['Sunset'];
		$d['CivTwilightEnd'] = $sunrise['CivTwilightEnd'];
		$map = [
			'PRESET_1' => 'EDM-1',
			'PRESET_2' => 'EDM-2',
			'PRESET_3' => 'EDM-3',
			'PRESET_4' => 'MIX-1',
			'PRESET_5' => 'MIX-2',
			'PRESET_6' => 'MIX-3',
		];
		$d['playlist'] = $map[boseplaylist()];
	}
	$d['thermo_hist'] = json_decode(getCache('thermo_hist'), true);
}
if (
	$t == 0
	|| ($t > 0 && getCache('energy_lastupdate') > $t - 1)
) {
	$vandaag = json_decode(getCache('energy_vandaag'));
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

echo json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);


function dbconnect() {
    global $dbname, $dbuser, $dbpass;
    static $db = null;
    try {
        if ($db !== null) {
            $db->query('SELECT 1');
            return $db;
        }
        $db = new PDO("mysql:host=127.0.0.1;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_PERSISTENT => true,
        ]);
        return $db;
    }
    catch (PDOException $e) {
        if ($db !== null && (
            $e->getCode() == 2006 || 
            $e->getCode() == 'HY000' ||
            strpos($e->getMessage(), 'server has gone away') !== false ||
            strpos($e->getMessage(), 'MySQL server has gone away') !== false
        )) {
            lg('⚠️ Verbinding verbroken, opnieuw verbinden (PID: '.getmypid().')');
            $db = null;
            return dbconnect();
        }
        lg('‼️ PDO fout: '.$e->getMessage());
        throw $e;
    }
}
function boseplaylist() {
	global $time;
	$dag=floor($time/86400);
	$dow=date("w");
	if($dow==0||$dow==6)$weekend=true; else $weekend=false;
	if ($weekend==true) {
		if ($dag % 3 == 0) $preset='MIX-3';
		elseif ($dag % 2 == 0) $preset='MIX-2';
		else $preset='MIX-1';
	} else {
		if ($dag % 3 == 0) $preset='EDM-3';
		elseif ($dag % 2 == 0) $preset='EDM-2';
		else $preset='EDM-1';
	}
	$map = [
		'EDM-1' => 'PRESET_1',
		'EDM-2' => 'PRESET_2',
		'EDM-3' => 'PRESET_3',
		'MIX-1' => 'PRESET_4',
		'MIX-2' => 'PRESET_5',
		'MIX-3' => 'PRESET_6',
	];
	return $map[$preset];
}
function setCache(string $key, $value): bool {
    return file_put_contents('/dev/shm/cache/' . $key .'.txt', $value, LOCK_EX) !== false;
}

function getCache(string $key, $default = false) {
    $data = @file_get_contents('/dev/shm/cache/' . $key .'.txt');
    return $data === false ? $default : $data;
}