<?php
header('Content-Type: application/json; charset=ISO-8859-1');
$log=true;
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
$en=false;
$d=[];
if (isset($_GET['o'])) $type = 'o';
elseif (isset($_GET['h'])) $type = 'h';
else $type = 'f';
$sql="SELECT n,s,t,m,d,i,rt,p FROM devices WHERE `$type`=1";
if (isset($_GET['all'])) {
    $t = 0;
    $delta=86399;
    $en=true;
    $extra = true;
} else {
    $t = apcu_fetch($id.$type);
    if ($t === false) {
    	$delta=86398;
        $t = 0;
        $en=true;
        $extra = true;
    } elseif ($t < $time - 1) {
    	lg(__LINE__);
		$delta=($time-$t)*5;
		$t-=$delta;
        $en=true;
        $extra = true;
    } else $delta=$time-$t;
    $sql.=" AND t >= $t";
}
apcu_store($id.$type, $time, 14400);

if ($t!=$time) {
	$d = ['t' => $time];
	$en=true;
	if($t%60==0){
		$extra=true;
		$t-=59;
		$delta+=59;
	}
}
if ($type === 'f') {
	if($en==true){
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
            $d['Tstart'] = $sunrise['CivTwilightStart'];
            $d['Srise'] = $sunrise['Sunrise'];
            $d['Sset'] = $sunrise['Sunset'];
            $d['Tend'] = $sunrise['CivTwilightEnd'];
            $d['pl'] = boseplaylist($time);
        }
    }
    $d['b_hist'] = json_decode(getCache('b_hist'), true);
    apcu_store($id.$type.'e', $time, 14400);
}
$db = Database::getInstance();
$stmt = $db->query($sql);
//$stmt->execute([':t' => $t]);
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
        $d[$n]['d'] = $row[4];
        if ($row[4] === 'daikin') {
            $d[$n]['s'] = null;
        }
    }
    if (!is_null($row[5])) {
        if ($row[4] === 'th' && $n !== 'badkamer_set') {
            $icon = json_decode($row[5], true);
        } else {
            $d[$n]['i'] = $row[5];
        }
    }
    if (!is_null($row[7])) {
        $d[$n]['p'] = $row[7];
    }
}

$data=json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
header('Content-Type: application/json');
header('Content-Length: '.strlen($data));
echo $data;
if($log===true) {
	unset($d['t'],$d['n'],$d['a'],$d['b'],$d['c'],$d['z']);
	$aantal=count($d);
	if($aantal>0) {
		$msg=$id.'	'.$type.' '.gmdate("H:i:s",$delta).' ('.$aantal.') ';
		$msg.=implode(',',array_keys($d));
		if($extra) $msg.=' + extra';
		lg($msg);
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
    $logFile = '/temp/floorplan-access.log';
    $fp = fopen($logFile, 'a');
    if ($fp === false) {
        error_log("Failed to open log file");
        return;
    }
    if (flock($fp, LOCK_EX)) {
        $now = microtime(true);
        $micro = sprintf("%03d", ($now - floor($now)) * 1000);
        $timestamp = date('d-m H:i:s', (int)$now) . '.' . $micro;
        fwrite($fp, "$timestamp $msg\n");
        flock($fp, LOCK_UN);
    }
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