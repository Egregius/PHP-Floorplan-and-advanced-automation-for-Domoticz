<?php
require '/var/www/config.php';
$time = $_SERVER['REQUEST_TIME'];
$extra=false;
$verbruik=false;
$d = array();
$d['t'] = $time;
$msg2='';
if (isset($_GET['o'])) {
	$type='o';
	if (isset($_GET['all'])) {
		$t=0;
		$extra=true;
		$msg=$_SERVER['HTTP_X_FORWARDED_FOR'].'	o	all';
		$sql="SELECT n,s,t,m,dt,icon,rt FROM devices WHERE `o`=1";
	} else {
		$t = apcu_fetch($_SERVER['HTTP_X_FORWARDED_FOR'].$type) ?? 1;
		if ($t === false) {
			$t = 0;
			$extra=true;
			$msg=$_SERVER['HTTP_X_FORWARDED_FOR'].'	o	expired';
		} else $msg=$_SERVER['HTTP_X_FORWARDED_FOR'].'	o	'.$time - $t.' sec';
		$sql="SELECT n,s,t,m,dt,icon,rt FROM devices WHERE `o`=1 AND `t`>=$t";
	}
	$ctx = stream_context_create(['http'=>['timeout'=>1],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false,'allow_self_signed'=> true]]);
	$d['pf']=json_decode(@file_get_contents('https://192.168.2.254:44300/egregius.php', false, $ctx), true);
} elseif (isset($_GET['h'])) {
	$type='h';
	if (isset($_GET['all'])) {
		$t=0;
		$extra=true;
		$msg=$_SERVER['HTTP_X_FORWARDED_FOR'].'	h	all';
		$sql="SELECT n,s,t,m,dt,icon,rt FROM devices WHERE `h`=1";
	} else {
		$t = apcu_fetch($_SERVER['HTTP_X_FORWARDED_FOR'].$type) ?? 1;
		if ($t === false) {
			$t = 0;
			$extra=true;
			$msg=$_SERVER['HTTP_X_FORWARDED_FOR'].'	h	expired';
		} else $msg=$_SERVER['HTTP_X_FORWARDED_FOR'].'	h	'.$time - $t.' sec';
		$sql="SELECT n,s,t,m,dt,icon,rt FROM devices WHERE `h`=1 AND `t`>=$t";
	}
} else {
	$type='f';
	if (isset($_GET['all'])) {
		$t=0;
		$extra=true;
		$verbruik=true;
		$msg=$_SERVER['HTTP_X_FORWARDED_FOR'].'	f	all';
		$sql="SELECT n,s,t,m,dt,icon,rt FROM devices WHERE `f`=1";
	} else {
		$t = apcu_fetch($_SERVER['HTTP_X_FORWARDED_FOR'].$type) ?? 1;
		if ($t === false) {
			$t = 0;
			$extra=true;
			$msg=$_SERVER['HTTP_X_FORWARDED_FOR'].'	f	expired';
		} else $msg=$_SERVER['HTTP_X_FORWARDED_FOR'].'	f	'.$time - $t.' sec';
		$sql="SELECT n,s,t,m,dt,icon,rt FROM devices WHERE `f`=1 AND `t`>=$t";
	}
	$en = json_decode(getCache('en'));
	if ($en) {
		$d['n'] = $en->n;
		$d['a'] = $en->a;
		$d['b'] = $en->b;
		$d['c'] = $en->c;
		$d['z'] = $en->z;
	}
	$verbruiklast=apcu_fetch($_SERVER['HTTP_X_FORWARDED_FOR'].'v');
	if ($verbruiklast===false||$verbruik===true) {
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
			$msg2.=' + verbruik';
			apcu_store($_SERVER['HTTP_X_FORWARDED_FOR'].'v', $time, 600);
		}
	}
}
apcu_store($_SERVER['HTTP_X_FORWARDED_FOR'].$type, $time, 14400);
$db = dbconnect();
//try {
    $stmt = $db->query($sql);
//} catch (PDOException $e) {
//    error_log("SQL ERROR: " . $e->getMessage());
//    error_log("SQL QUERY: " . $sql);
//    throw $e;
//}
$extralast=apcu_fetch($_SERVER['HTTP_X_FORWARDED_FOR'].$type.'e');
if ($extralast===false||$extra===true) {
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
		apcu_store($_SERVER['HTTP_X_FORWARDED_FOR'].$type.'e', $time, 14400);
	}
	$d['thermo_hist'] = json_decode(apcu_fetch('thermo_hist'), true);
	$msg2.=' + extra';
}

while ($row = $stmt->fetch()) {
	$d[$row['n']]['s'] = $row['s'];
	if ($row['rt'] == 1) $d[$row['n']]['t'] = $row['t'];
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

$data=json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
echo $data;
if ($type=='f') lg($msg.'	'.count($d)-6 .' updates	'.strlen($data).' bytes'.$msg2);
else lg($msg.'	'.count($d)-1 .' updates	'.strlen($data).' bytes'.$msg2);
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
function getCache(string $key, $default = false) {
    $data = @file_get_contents('/dev/shm/cache/' . $key .'.txt');
    return $data === false ? $default : $data;
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