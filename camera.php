<?php
require '/var/www/config.php';
if (isset($_GET['token'])&&$_GET['token']==$cameratoken) {
	$user='camera';
	$mysqli=new mysqli('192.168.30.23', $dbuser, $dbpass, $dbname);
	$result = $mysqli->query("select n,s,d,t,m from devices WHERE n in ('weg', 'auto', 'poort', 'deurvoordeur', 'voordeur');") or trigger_error($mysqli->error." [$sql]");
	while ($row = $result->fetch_array()) {
		$d[$row['n']]['s'] = $row['s'];
		$d[$row['n']]['d'] = $row['d'];
		$d[$row['n']]['t'] = $row['t'];
		$d[$row['n']]['m'] = $row['m'];
	}
	$data=array();
	$data['w']=$d['weg']['s'];
	$data['p']=$d['poort']['s'];
	if ($d['auto']=='Off') $data['p']='Open';
	$data['d']=$d['deurvoordeur']['s'];
	$times[]=TIME-$d['deurvoordeur']['t'];
	$times[]=TIME-$d['poort']['t'];
	$times[]=TIME-$d['weg']['t'];
	$data['t']=min($times);
	if (getCache('dag')<0) {
		$data['z']=0;
		sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	} else $data['z']=1;
	echo serialize($data);
} elseif (isset($_GET['token'])&&$_GET['token']==$cameratoken.'b') {
	$user='camera';
	$mysqli=new mysqli('localhost', $dbuser, $dbpass, $dbname);
	$result = $mysqli->query("select n,s from devices WHERE n ='weg';") or trigger_error($mysqli->error." [$sql]");
	while ($row = $result->fetch_array()) {
		$d[$row['n']]['s'] = $row['s'];
	}
	$data=array();
	$data['w']=$d['weg']['s'];
	echo serialize($data);
} else echo '403 Access denied';

function sw($name,$action='Toggle',$msg='',$force=false) {
	global $d,$user,$db;
	if (!isset($d)) $d=fetchdata(0, basename(__FILE__).':'.__LINE__);
	if (is_array($name)) {
		foreach ($name as $i) {
			if ($d[$i]['s']!=$action) {
				sw($i, $action, $msg);
				usleep(300000);
			}
		}
	} else {
		$msg='(SWITCH)'.str_pad($user, 13, ' ', STR_PAD_LEFT).' => '.str_pad($name, 13, ' ', STR_PAD_RIGHT).' => '.$action.' ('.$msg.')';
		if ($d[$name]['s']!=$action||$force==true) {
			if ($d[$name]['d']=='hsw'||$d[$name]['d']=='sw') {
				lg('[hsw] '.$msg,4);
				if ($action=='On') hass('switch','turn_on','switch.'.$name);
				elseif ($action=='Off') hass('switch','turn_off','switch.'.$name);
				store($name, $action, $msg);
			} else {
				store($name, $action, $msg);
			}
		}
	}
}
function hass($domain,$service,$entity,$opts=null) {
	$ch=curl_init();
	curl_setopt($ch,CURLOPT_URL,'http://192.168.2.26:8123/api/services/'.$domain.'/'.$service);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_HTTPHEADER,array('Content-Type: application/json','Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmMTQ1ZThmNjYyNTk0Mjk5OWM2ZTUyMWNhZWY3MTUxYSIsImlhdCI6MTc0ODQwMDM0OCwiZXhwIjoyMDYzNzYwMzQ4fQ.SDUxztRFwr9p7w29LQ-_fDa5l4KB1cOTrz_riHQCFlY'));
	if ($opts==null) $data='{"entity_id":"'.$entity.'"}';
	else $data='{"entity_id":"'.$entity.'",'.$opts.'}';
	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch,CURLOPT_TIMEOUT,5);
	$response=curl_exec($ch);
	curl_close($ch);
}

function lg($msg) {
	global $log;
	if ($log==true) {
		$fp=fopen('/temp/domoticz.log', "a+");
		$time=microtime(true);
		$dFormat="Y-m-d H:i:s";
		$mSecs=$time-floor($time);
		$mSecs=substr(number_format($mSecs, 3), 1);
		fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
		fclose($fp);
	}
}

function setCache(string $key, $value): bool {
    return file_put_contents('/dev/shm/cache/' . $key .'.txt', $value, LOCK_EX) !== false;
}

function getCache(string $key, $default = false) {
    $data = @file_get_contents('/dev/shm/cache/' . $key .'.txt');
    return $data === false ? $default : $data;
}
function store($name='',$status='',$msg='',$log='store') {
	global $d,$user;
	for ($attempt = 0; $attempt <= 4; $attempt++) {
		try {
			$d['time']??=time();
			$d[$name]->s=$status;
			$d[$name]->t=$d['time'];
			$db=Database::getInstance();
			$stmt=$db->prepare("UPDATE devices SET s = :s, t = :t WHERE n = :n");
			$stmt->execute([':s'=>$status,':t'=>$d['time'],':n'=>$name]);
			$affected=$stmt->rowCount();
			break;
		} catch (PDOException $e) {
			if (in_array($e->getCode(),[2006,'HY000']) && $attempt < 4) {
				lg('♻ DB gone away → reconnect & retry', $log);
				Database::reset();
				if($attempt>0) sleep($attempt);
				continue;
			}
			throw $e;
		}
	}
	if($affected>0/*&&!in_array($name,['dag'])*/){
		if($d[$name]->f===1) publishmqtt('d/'.$name,toJsonClean($d[$name]),$msg);
		lg('💾 STORE     '.str_pad($user??'',9).' '.str_pad($name??'',13).' '.$status.($msg?' ('.$msg.')':''),$log);
	}
	return $affected ?? 0;
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
                dsn: "mysql:host=192.168.30.23;dbname=domotica;charset=latin1",
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
function fetchdata(): array {
    global $d;
    for ($attempt = 0; $attempt <= 4; $attempt++) {
        try {
            $db = Database::getInstance();
            static $stmt = null;
            $stmt ??= $db->prepare("SELECT n,s,t,m,d,i,p,rt,f FROM devices");
            $stmt->execute();
            foreach ($stmt->fetchAll(PDO::FETCH_NUM) as [$n, $s, $t, $m, $deviceD, $i, $p, $rt, $f]) {
                $dev = new Device();
                $dev->n  = $n;
                $dev->s  = $s;
                $dev->t  = $t;
                $dev->m  = $m;
                $dev->d  = $deviceD;
                $dev->i  = $i;
                $dev->p  = $p;
                $dev->rt = $rt;
                $dev->f  = $f;

                $d[$n] = $dev;
            }
            break;
        } catch (PDOException $e) {
            $isRecoverable = in_array($e->getCode(), [2006, 'HY000'], true) && $attempt < 4;
            if ($isRecoverable) {
                lg(' ♻  DB gone away → reconnect & retry fetchdata');
                Database::reset();
                $stmt = null;
                $attempt > 0 && sleep($attempt);
                continue;
            }
            lg('FETCHDATA ERROR! ' . $e->getCode());
            throw $e;
        }
    }
    if ($en = json_decode(getCache('en'))) {
		foreach (['n','a','b','c','z'] as $key) {
			$d[$key] = $en->$key ?? 0;
		}
	}
    return $d;
}