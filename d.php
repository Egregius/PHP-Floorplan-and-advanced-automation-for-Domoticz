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
if (isset($_GET['f'])) {
	$type='f';
	if($_GET['f']>0) {
		$msg=__LINE__;
		$t=$_GET['f'];
		$sql="SELECT n,s,t,m,d,i,rt,p FROM devices WHERE `$type`=1 AND t >= $t";
	} else {
		$msg=__LINE__;

		$sql="SELECT n,s,t,m,d,i,rt,p FROM devices WHERE `$type`=1";
		$en=true;
		$extra=true;
		$d = ['t' => $time];
	}
} elseif (isset($_GET['h'])) {
	$type='h';
	if($_GET['h']>0) {
		$msg=__LINE__;
		$t=$_GET['h'];
		$sql="SELECT n,s,t,m,d,i,rt,p FROM devices WHERE `$type`=1 AND t >= $t";
	} else {
				$msg=__LINE__;
		
		 $sql="SELECT n,s,t,m,d,i,rt,p FROM devices WHERE `$type`=1";
	}
} elseif (isset($_GET['o'])) {
	$type='o';
	if($_GET['o']>0) {
				$msg=__LINE__;
		$t=$_GET['o'];
		$sql="SELECT n,s,t,m,d,i,rt,p FROM devices WHERE `$type`=1 AND t >= $t";
	} else {
				$msg=__LINE__;
		$sql="SELECT n,s,t,m,d,i,rt,p FROM devices WHERE `$type`=1";
	}
} else exit;

$last=apcu_fetch($id.$type);
apcu_store($id.$type, $time, 7200);
if ($last!=$time) $d = ['t' => $time];
if ($type === 'f') {
	if($en==true||$last!=$time){
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
	$lastenergy_vandaag=apcu_fetch($id.$type.'energy_vandaag');
	$lastupd=filemtime('/dev/shm/cache/energy_vandaag.txt');
	if($last===false||$extra===true||$lastenergy_vandaag<$lastupd) {
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
                apcu_store($id.$type.'energy_vandaag',$lastupd);
            }
        }
    }
	if($last===false||$extra===true||$last<$time-900) {
		$msg.=':'.__LINE__;
        $sunrise = apcu_fetch('cache_sunrise');
		if ($sunrise === false) {
			$msg.=':'.__LINE__;
			$sunrise = getCache('sunrise');
//			lg(print_r($sunrise,true));
			if ($sunrise) {
				apcu_store('cache_sunrise', $sunrise, 14400);
			}
			$sunrise=json_decode($sunrise,true);
		} 
		$msg.=':'.__LINE__;
		if(!is_array($sunrise)) $sunrise = json_decode($sunrise, true);
//		lg(print_r($sunrise,true));
		$msg.=':'.__LINE__;
		$d['Tstart'] = $sunrise['CivTwilightStart'];
		$d['Srise'] = $sunrise['Sunrise'];
		$d['Sset'] = $sunrise['Sunset'];
		$d['Tend'] = $sunrise['CivTwilightEnd'];
		$d['pl'] = boseplaylist($time);
		$d['b_hist'] = json_decode(getCache('b_hist'), true);
    }
}
$db = Database::getInstance();
$stmt = $db->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_NUM);

foreach ($rows as [$n, $s, $t_val, $m, $device, $i, $rt, $p]) {
    // Bouw device array efficient op
    $d[$n] = ['s' => $s];
    
    // Conditionals geoptimaliseerd
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
if($log===true) {
	unset($d['t'],$d['n'],$d['a'],$d['b'],$d['c'],$d['z']);
	$aantal=count($d);
	if($aantal>0) {
		$msg.=' '.str_pad($id??'',10).' '.$type.' ('.$aantal.') ';
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