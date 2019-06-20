<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
session_start();
require '/var/www/config.php';
$db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$d=fetchdata();
/**
 * Function fetchdata
 *
 * Fetches all the data from the devices table
 *
 * @return array $d
 */
function fetchdata()
{
    global $db;
    $stmt=$db->query("select n,i,s,t,m,dt,icon from devices;");
    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
        $d[$row['n']] = $row;
    }
    return $d;
}
/**
 * Function huisslapen
 *
 * Switches off everything that should be off while sleeping
 *
 * @return null
 */
function huisslapen()
{
    global $d,$boseipbuiten;
    sw(array('slapen'), 'Off', true, 'huisslapen', 200000);
    $items=array('living_set','tobi_set','alex_set','kamer_set','eettafel','zithoek'/*,'dimactionkamer','dimactiontobi','dimactionalex'*/);
    foreach ($items as $i) {
        storemode($i, 0);
    }
    $items=array('Rliving','Rbureel','RkeukenL','RkeukenR','luifel');
    foreach ($items as $i) {
        storemode($i, 0);
    }
    $items=array('Ralex','RkamerL','RkamerR');
    foreach ($items as $i) {
        storemode($i, 2);
    }
    if ($d['gcal']['s']==true) {
        storemode('Rtobi', 2);
    }
    $status=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.104:8090/now_playing'))), true);
    if (!empty($status)) {
        if (isset($status['@attributes']['source'])) {
            if ($status['@attributes']['source']!='STANDBY') {
                bosekey("POWER", 0, 104);
            }
        }
    }
    if ($d['auto']['s']=='Off') {
        sw('auto', 'On');
    }
}
/**
 * Function huisweg
 *
 * Switches off everything that should be off while not at home
 *
 * @return null
 */
function huisweg()
{
    huisslapen();
    $items=array('Rtobi','Ralex','RkamerL','RkamerR');
    foreach ($items as $i) {
        storemode($i, 0);
    }
}
/**
 * Function douche
 *
 * Calculates the gas and water consumption of the shower, sents a telegram
 * and resets the gas and water counters
 *
 * @return null
 */
function douche()
{
    global $d;
    $douchegas=$d['douche']['s']*10;
    $douchewater=$d['douche']['m']*1;
    if ($douchegas>0&&$douchewater>0) {
        telegram('Douche__Gas: '.$douchegas.'L = '.($douchegas*0.004).'€__Water: '.$douchewater.'L = '.($douchewater*0.005).'€__Som = '.(($douchegas*0.004)+($douchewater*0.005)).'€');
    }
    store('douche', 0);
    storemode('douche', 0);
}
/**
 * Function douchewarn
 *
 * Calculates the gas and water consumption of the shower, sents a telegram
 * and resets the gas and water counters
 *
 * @param int $euro current amount of shower costs
 * @param int $vol  Volume of the notification sound
 *
 * @return null
 */
function douchewarn($euro,$vol)
{
    global $boseipbadkamer;
    lg('Douche € '.$euro.' geluid!');
    $volume=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.102:8090/volume'))), true);
    shell_exec('./boseplayinfo.sh "martian-gun" > /dev/null 2>/dev/null &');
    $cv=$volume['actualvolume'];
    if ($cv<$vol) {
        usleep(1550000);
        bosevolume($vol, 102);
        usleep(3500000);
        bosevolume($cv, 102);
    }
}
/**
 * Function waarschuwing
 *
 * Plays a sound on the Xiami doorbell and a regular doorbell
 * and sents a telegram message
 *
 * @param string $msg Message to sent to telegram
 *
 * @return null
 */
function waarschuwing($msg,$sound)
{
    global $d;
    if ($d['bose101']['s']=='On') {
    	shell_exec('/var/www/html/secure/boseplayinfo.sh "'.$sound.'" > /dev/null 2>/dev/null &');
    }
    if ($d['bose102']['s']=='On') {
    	shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose102.php" > /dev/null 2>/dev/null &');
    }
    if ($d['bose103']['s']=='On') {
    	shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose103.php" > /dev/null 2>/dev/null &');
    }
    if ($d['bose104']['s']=='On') {
    	shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose104.php" > /dev/null 2>/dev/null &');
    }
    if ($d['bose105']['s']=='On') {
    	shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose105.php" > /dev/null 2>/dev/null &');
    }

    if ($d['Xvol']['s']!=25) {
        sl('Xvol', 25);
    }
    sl('Xring', 30);
    sw('deurbel', 'On');
    //telegram($msg, false, 2);
    usleep(1500000);
    sl('Xring', 0);
    die($msg);
}
/**
 * Function past
 *
 * Calculates how long it's ago that this device was updated
 *
 * @param string $name Name of the device to check
 *
 * @return int
 */
function past($name)
{
    global $d;
    if (!empty($d[$name]['t'])) {
        return TIME-$d[$name]['t'];
    } else {
        return 999999999;
    }
}

function idx($name)
{
    global $d;
    if ($d[$name]['i']>0) {
        return $d[$name]['i'];
    } else {
        return 0;
    }
}
function sl($name,$level,$check=false)
{
    global $user,$d,$domoticzurl;
    if(!isset($d))$d=fetchdata();
    lg(' (SETLEVEL) | '.$user.' =>	'.$name.'	'.$level);
    if ($d[$name]['i']>0) {
        if ($check==false) {
            file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd=Set%20Level&level='.$level);
        } else {
            if ($d[$name]['s']!=$level) {
                file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd=Set%20Level&level='.$level);
            }
        }
    } else {
        store($name, $level);
    }
}
function rgb($name,$hue,$level,$check=false)
{
    global $user,$d,$domoticzurl;
    lg(' (RGB) | '.$user.' =>	'.$name.'	'.$level);
    if ($d[$name]['i']>0) {
        if ($check==false) {
            file_get_contents($domoticzurl.'/json.htm?type=command&param=setcolbrightnessvalue&idx='.$d[$name]['i'].'&hue='.$hue.'&brightness='.$level.'&iswhite=false');
        } else {
            if ($d[$name]['s']!=$$level) {
                file_get_contents($domoticzurl.'/json.htm?type=command&param=setcolbrightnessvalue&idx='.$d[$name]['i'].'&hue='.$hue.'&brightness='.$level.'&iswhite=false');
            }
        }
    } else {
        store($name, $level);
    }
}
function resetsecurity()
{
    global $d,$domoticzurl;
    if (!isset($d)) $d=fetchdata();
    lg(' ********* RESETSECURITY ************');
    if ($d['sirene']['s']!='Off') {
        sw('sirene', 'Off');
        usleep(100000);
        store('sirene', 'Off');
    }
    $items=array('SDbadkamer','SDkamer','SDalex','SDtobi','SDzolder','SDliving');
    foreach ($items as $i) {
        if ($d[$i]['s']!='Off') {
            file_get_contents($domoticzurl.'/json.htm?type=command&param=resetsecuritystatus&idx='.$d[$i]['i'].'&switchcmd=Normal');
            store($i, 'Off');
        }
    }
}
function sw($name,$action='Toggle',$check=true,$msg='',$usleep=0)
{
    global $user,$d,$domoticzurl;
    if (!isset($d)) $d=fetchdata();
    if (is_array($name)) {
        $check=true;
        foreach ($name as $i) {
            if ($i=='media') {
                sw(array(/*'lgtv','denon',*/'tvled','kristal'/*,'nvidia'*/), $action, $check, $msg, $usleep);
            } elseif ($i=='lichtenbeneden') {
                sw(array('garage','garageled','pirgarage','pirkeuken','pirliving','pirinkom','eettafel','zithoek','media','bureel','jbl','terras','tuin','keuken','werkblad1','wasbak','kookplaat','inkom','zolderg','voordeur','wc'), $action, $check, $msg, $usleep);
            } elseif ($i=='lichtenboven') {
                sw(array('pirhall','lichtbadkamer','kamer','tobi','alex','hall','zolder'), $action, $check, $msg, $usleep);
            } elseif ($i=='slapen') {
                sw(array('hall','pirhall','lichtenbeneden','dampkap','GroheRed'), $action, $check, $msg, $usleep);
            } elseif ($i=='weg') {
                sw(array('garage','slapen','lichtenboven'), $action, $check, $msg, $usleep);
            } else {
                if ($d[$i]['s']!=$action) {
                    sw($i, $action, $check, $msg, $usleep);
                }
            }
        }
    } else {
        if (empty($msg)) {
            $msg=' (SWITCH) | '.$user.' => '.$name.' => '.$action;
        }
        if ($d[$name]['i']>0) {
            lg($msg);
            if ($check==false) {
                echo file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
            } else {
                if ($d[$name]['s']!=$action) {
                    echo file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
                }
            }
        } else {
            store($name, $action);
        }
        if ($name=='denon') {
            if ($action=='Off') {
                storemode('denon', 'UIT');
            }
        }
    }
    if ($usleep>0) {
        usleep($usleep);
    }
}
function lgcommand($action)
{
    global $lgtvip, $lgtvmac;
    if ($action=='on') {
        shell_exec('python3 lgtv.py -c on -a '.$lgtvmac.' '.$lgtvip.' > /dev/null 2>&1 &');
    } else {
        echo shell_exec('python3 lgtv.py -c '.$action.' '.$lgtvip.' > /dev/null 2>&1 &');
        echo 'python3 lgtv.py -c '.$action.' '.$lgtvip;
    }
}
function store($name,$status,$idx=null,$force=true)
{
    global $db, $d, $username;
    if (!isset($d)) $d=fetchdata();
    $time=TIME;
	if ($idx>0) {
		$db->query("INSERT INTO devices (n,i,s,t) VALUES ('$name','$idx','$status','$time') ON DUPLICATE KEY UPDATE s='$status',i='$idx',t='$time';");
	} else {
		$db->query("INSERT INTO devices (n,s,t) VALUES ('$name','$status','$time') ON DUPLICATE KEY UPDATE s='$status',t='$time';");
	}
	lg('store '.$name.' '.$status);
}
function storemode($name,$mode,$time=0)
{
    global $db, $d, $username;
    $time=TIME+$time;
	$db->query("INSERT INTO devices (n,m,t) VALUES ('$name','$mode','$time') ON DUPLICATE KEY UPDATE m='$mode',t='$time';");
	lg('storemode'.$name.' '.$mode);  
}
function storeicon($name,$icon)
{
    global $db, $d, $username;
    if ($d[$name]['icon']!=$icon) {
		$db->query("INSERT INTO devices (n,icon) VALUES ('$name','$icon') ON DUPLICATE KEY UPDATE icon='$icon';");
		lg('storeicon '.$name.' '.$icon);
	}
}
function alert($name,$msg,$ttl,$silent=true,$to=1,$ios=false)
{
    global $db;
    if ($ios) {
        shell_exec('./ios.sh "'.$msg.'" >/dev/null 2>/dev/null &');
    }
    $time=TIME;
    $stmt=$db->query("SELECT t FROM alerts WHERE n='$name';");
    $last=$stmt->fetch(PDO::FETCH_ASSOC);
    if (isset($last['t'])) {
        $last=$last['t'];
    }
    if ($last < $time-$ttl) {
        $db->query("INSERT INTO alerts (n,t) VALUES ('$name','$time') ON DUPLICATE KEY UPDATE t='$time';");
        telegram($msg, $silent, $to);
        lg('alert='.$last);
    }
}
function kodi($json)
{
    global $kodiurl;
    $ch=curl_init($kodiurl.'/jsonrpc');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $result=curl_exec($ch);
    return $result;
}
function ud($name,$nvalue,$svalue,$check=false)
{
    global $user,$d,$domoticzurl;
    if ($d[$name]['i']>0) {
        if ($check==true) {
            if ($d['name']['s']!=$svalue) {
                return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
            }
        } else {
            return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
        }
    } else {
        store($name, $svalue);
    }
    lg(' (udevice) | '.$user.' => '.$name.' => '.$nvalue.','.$svalue);
}
function zwavecancelaction(){global $domoticzurl;file_get_contents($domoticzurl.'/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>'cancel')),),)));}
function zwaveCommand($node,$command){global $domoticzurl;$cm=array('AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');$cm=$cm[$command];for($k=1;$k<=5;$k++){$result=file_get_contents($domoticzurl.'/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));if ($result=='OK') {break;}sleep(1);}return $result;}
function controllerBusy($retries){global $domoticzurl;for($k=1;$k<=$retries;$k++){$result=file_get_contents($domoticzurl.'/ozwcp/poll.xml');$p=xml_parser_create();xml_parse_into_struct($p,$result,$vals,$index);xml_parser_free($p);foreach($vals as $val){if($val['tag']=='ADMIN'){$result=$val['attributes']['ACTIVE'];break;}}if($result=='false'){break;}if($k==$retries){zwaveCommand(1,'Cancel');break;}sleep(1);}}
function convertToHours($time)
{
    if ($time<600) {
        return substr(strftime('%k:%M:%S', $time-3600), 1);
    } elseif ($time>=600&&$time<3600) {
        return strftime('%k:%M:%S', $time-3600);
    } else {
        return strftime('%k:%M:%S', $time-3600);
    }
}
function checkport($ip,$port='None')
{
    if ($port=='None') {
        if (ping($ip)) {
            $prevcheck=$d['ping'.$ip]['s'];
            if ($prevcheck>=5) {
                telegram($ip.' online', true);
            }
            if ($prevcheck>0) {
                store('ping'.$ip, 0);
            }
            return 1;
        } else {
            $check=$d['ping'.$ip]['s']+1;
            if ($check>0) {
                store('ping'.$ip, $check);
            }
            if ($check==5) {
                telegram($ip.' Offline', true);
            }
            if ($check%120==0) {
                telegram($ip.' nog steeds Offline', true);
            }
            return 0;
        }
    } else {
        if (pingport($ip, $port)==1) {
            $prevcheck=$d['ping'.$ip]['s'];
            if ($prevcheck>=5) {
                telegram($ip.':'.$port.' online', true);
            }
            if ($prevcheck>0) {
                store('ping'.$ip, 0);
            }
            return 1;
        } else {
            $check=$d['ping'.$ip]['s']+1;
            if ($check>0) {
                store('ping'.$ip, $check);
            }
            if ($check==5) {
                telegram($ip.':'.$port.' Offline', true);
            }
            if ($check%120==0) {
                telegram($ip.':'.$port.' nog steeds Offline', true);
            }
            return 0;
        }
    }
}
function ping($ip)
{
    $result=exec("/bin/ping -c1 -w1 $ip", $outcome, $status);
    if ($status==0) {
        $status=true;
    } else {
        $status=false;
    }
    return $status;
}
function pingport($ip,$port)
{
    $file=@fsockopen($ip, $port, $errno, $errstr, 5);
    $status=0;
    if (!$file) {
        $status=-1;
    } else {
        fclose($file);
        $status=1;
    }
    return $status;
}
function double($name,$action,$check=false,$comment='',$wait=2000000)
{
    sw($name, $action, $check, $comment);usleep($wait);sw($name, $action, $check, $comment.' | repeat');
}

function koekje($user,$expirytime)
{
    global $cookie,$domainname;
    setcookie($cookie, $user, $expirytime, '/', $domainname, true, true);
}
function telegram($msg,$silent=true,$to=1)
{
	if ($silent==true) {
		$silent='true';
	} else {
		$silent='false';
	}
    shell_exec('/var/www/html/secure/telegram.sh "'.$msg.'" "'.$silent.'" "'.$to.'" > /dev/null 2>/dev/null &');
    lg('Telegram sent: '.$msg.PHP_EOL.'<br>./telegram.sh "'.$msg.'" "'.$silent.'" "'.$to.'" > /dev/null 2>/dev/null &');
}
function lg($msg)
{
    global $log;
    if ($log==true) {
		$fp=fopen('/var/log/domoticz.log', "a+");
		$time=microtime(true);
		$dFormat="Y-m-d H:i:s";
		$mSecs=$time-floor($time);
		$mSecs=substr(number_format($mSecs, 3), 1);
		fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
		fclose($fp);
	}
}
function lgajax($msg)
{
    global $log;
    if ($log==true) {
		$fp=fopen('/temp/ajax.log', "a+");
		$time=microtime(true);
		$dFormat="Y-m-d H:i:s";
		$mSecs=$time-floor($time);
		$mSecs=substr(number_format($mSecs, 3), 1);
		fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
		fclose($fp);
	}
}
function logwrite($msg,$msg2=null)
{
    global $log;
    if ($log==true) {
		$time=microtime(true);
		$dFormat="Y-m-d H:i:s";
		$mSecs=$time-floor($time);
		$mSecs=substr(number_format($mSecs, 3), 1);
		$fp=fopen('/var/log/domoticz.log', "a+");
		fwrite(
			$fp,
			sprintf(
				"%s%s %s %s\n",
				date($dFormat),
				$mSecs,
				' > '.$msg,
				$msg2
			)
		);
		fclose($fp);
	}
}
function fail2ban($ip)
{
    $time=microtime(true);
    $dFormat="Y-m-d H:i:s";
    $mSecs=$time-floor($time);
    $mSecs=substr(number_format($mSecs, 3), 1);
    $fp=fopen('/var/log/home2ban.log', "a+");
    fwrite($fp, sprintf("%s %s\n", date($dFormat), $ip));
    fclose($fp);
}
function startsWith($haystack,$needle)
{
    return $needle===""||strrpos($haystack, $needle, -strlen($haystack))!==false;
}
function endswith($string,$test)
{
    $strlen=strlen($string);$testlen=strlen($test);
    if ($testlen>$strlen) {
        return false;
    }
    return substr_compare($string, $test, $strlen-$testlen, $testlen)===0;
}
function bosekey($key,$sleep=100000,$ip=101)
{
    $xml="<key state=\"press\" sender=\"Gabbo\">$key</key>";
    bosepost("key", $xml, $ip, true);
    $xml="<key state=\"release\" sender=\"Gabbo\">$key</key>";
    bosepost("key", $xml, $ip);
}
function bosevolume($vol,$ip=101)
{
    $vol=1*$vol;
    $xml="<volume>$vol</volume>";
    bosepost("volume", $xml, $ip, true);
    if ($ip==101) {
        if ($vol>50) {
            bosebass(0, $ip);
        } elseif ($vol>40) {
            bosebass(-1, $ip);
        } elseif ($vol>30) {
            bosebass(-2, $ip);
        } elseif ($vol>20) {
            bosebass(-3, $ip);
        } elseif ($vol>10) {
            bosebass(-4, $ip);
        } else {
            bosebass(-5, $ip);
        }
    }
}
function bosebass($bass,$ip=101)
{
    $bass=1*$bass;
    $xml="<bass>$bass</bass>";
    bosepost("bass", $xml, $ip);
}
function bosepreset($pre,$ip=3)
{
    $pre=1*$pre;
    if ($pre<1||$pre>6) {
        return;
    }
    bosekey("PRESET_$pre", 0, $ip, true);
}
function bosezone($ip)
{
    $d=fetchdata();
    if ($d['Weg']['s']<=1) {
        if ($d['Weg']['s']==0&&$d['denonpower']['s']=='OFF'&&$d['bose101']['s']=='Off'&&TIME<strtotime('21:00')-($d['auto']['m']==true?3600:0)) {
            bosekey("POWER", 0, 101);
            sw('bose101', 'On');
            bosevolume(25, 101);
            for ($x=1;$x<=10;$x++) {
                $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.101:8090/now_playing"))), true);
                if (!empty($nowplaying)) {
                    if (isset($nowplaying['@attributes']['source'])) {
                        if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
                            if (trim($nowplaying['artist'])=='Paul Kalkbrenner'&&trim($nowplaying['track'])=='Page Two') {
                                bosekey("NEXT_TRACK", 0, 101);
                                break;
                            }
                        }
                    }
                }
                sleep(1);
            }
        } elseif ($d['bose101']['s']=='On'&&$d['denonpower']['s']=='OFF') {
            $volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.101:8090/volume"))), true);
            if (isset($volume['actualvolume'])) {
                $cv=$volume['actualvolume'];
                if ($cv<10) {
                    bosevolume(10, 101);
                }
            }
        }
        if ($ip>101) {
            sw('bose'.$ip, 'On');
            if ($ip==102) {
                $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.102">304511BC3CA5</member></zone>';
            } elseif ($ip==103) {
                $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.103">C4F312F65070</member></zone>';
            } elseif ($ip==104) {
                $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.104">C4F312DCE637</member></zone>';
            } elseif ($ip==105) {
                $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.105">587A628BB5C0</member></zone>';
            }
            if ($d['bose101']['s']=='Off'&&$d['bose'.$ip]['s']=='Off') {
                bosekey("POWER", 0, 101);
                sw('bose101', 'On');
                sw('bose'.$ip, 'On');
                if ($d['denonpower']['s']=='ON') {
                    bosevolume(0, 101);
                } else {
                    bosevolume(25, 101);
                }
                bosepost('setZone', $xml, 101);
                if (TIME>strtotime('6:00')-($d['auto']['m']==true?3600:0)&&TIME<strtotime('22:00')-($d['auto']['m']==true?3600:0)) {
                    bosevolume(35, $ip);
                } else {
                    bosevolume(22, $ip);
                }
                for ($x=1;$x<=10;$x++) {
                    $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.101:8090/now_playing"))), true);
                    if (!empty($nowplaying)) {
                        if (isset($nowplaying['@attributes']['source'])) {
                            if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
                                if (trim($nowplaying['artist'])=='Paul Kalkbrenner'&&trim($nowplaying['track'])=='Page Two') {
                                    bosekey("NEXT_TRACK", 0, 101);
                                    break;
                                }
                            }
                        }
                    }
                    sleep(1);
                }
            } elseif ($d['bose'.$ip]['s']=='Off') {
                sw('bose'.$ip, 'On');
                bosepost('setZone', $xml, 101);
                if (TIME>strtotime('6:00')-($d['auto']['m']==true?3600:0)&&TIME<strtotime('22:00')-($d['auto']['m']==true?3600:0)) {
                    bosevolume(35, $ip);
                } else {
                    bosevolume(18, $ip);
                }
            }
        }
    }
}
function bosepost($method,$xml,$ip=3,$log=false)
{
    global $user;
    $ch=curl_init("http://192.168.2.$ip:8090/$method");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    $response=curl_exec($ch);
    curl_close($ch);
    if ($log) {
        lg($user.' >> Bose '.$ip.' '.$method.' '.$xml);
    }
    usleep(10000);
    return $response;
}
function denon($cmd)
{
    for ($x=1;$x<=10;$x++) {
        if (denontcp($cmd, $x)) {
            break;
        }
    }
}
function denontcp($cmd, $x)
{
    $sleep=102000*$x;
    $socket=fsockopen("192.168.2.6", "23", $errno, $errstr, 2);
    if ($socket) {
        fputs($socket, "$cmd\r\n");
        fclose($socket);
        usleep($sleep);
        return true;
    } else {
        usleep($sleep);
        echo 'sleeping '.$sleep.'<br>';
        return false;
    }
}
function strafter($string, $substring)
{
    $pos=strpos($string, $substring);
    if ($pos===false) {
        return '';
    } else {
        return(substr($string, $pos+strlen($substring)));
    }
}
function strbefore($string, $substring)
{
    $pos=strpos($string, $substring);
    if ($pos===false) {
        return '';
    } else {
        return(substr($string, 0, $pos));
    }
}
function fliving()
{
    global $d;
    lg('***** fliving *****');
    if ($d['Weg']['s']==0&&$d['denonpower']['s']=='OFF'&&$d['bureel']['s']=='Off'&&$d['eettafel']['s']==0) {
        if ($d['zon']['s']==0) {
            if ($d['keuken']['s']=='Off') {
                sw('keuken', 'On');
            }
            if ($d['bureel']['s']=='Off') {
                sw('bureel', 'On');
            }
            if ($d['jbl']['s']=='Off') {
                sw('jbl', 'On');
            }
        }
        $d['pirliving']['t']=TIME;
        bosezone(101);
        include '_rolluiken.php';
    }
}
function fgarage()
{
    global $d;
    if ($d['Weg']['s']==0&&($d['zon']['s']<500||TIME<strtotime('9:00')-($d['auto']['m']==true?3600:0)||TIME>strtotime('21:00')-($d['auto']['m']==true?3600:0))&&$d['garage']['s']=='Off'&&$d['garageled']['s']=='Off') {
        sw('garageled', 'On');
    }
    bosezone(104);
}
function fbadkamer()
{
    global $d;
    if (past('8badkamer-8')>10) {
        if (TIME>strtotime('5:00')&&TIME<strtotime('12:00')&&$d['lichtbadkamer']['s']<25&&$d['zon']['s']<20) {
            sl('lichtbadkamer', 25);
        } elseif ($d['lichtbadkamer']['s']<18&&$d['zon']['s']<20) {
            sl('lichtbadkamer', 18);
        }
        bosezone(102);
    }
    storemode('Weg', TIME);
}
function fkeuken()
{
    global $d;
    if (TIME<strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']<45||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        sw('keuken', 'On');
    } elseif (TIME>=strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']<45||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        if ($d['tv']['s']=='On'||$d['jbl']['s']=='On') {
            sw('keuken', 'On');
        }
    }
}
function finkom()
{
    global $d;
    if ($d['Weg']['s']==0&&$d['inkom']['s']=='Off'&&$d['zon']['s']<10) {
        sw('inkom', 'On');
    }
}
function fhall()
{
    global $d,$device;
    if ($d['hall']['s']=='Off') {
		if ($d['Weg']['s']==0&&((TIME>strtotime('6:00')&&TIME<strtotime('8:00'))||$d['zon']['s']==0)) {
			sw('hall', 'On');
		} elseif (isset($device)&&$device!='pirhall'&&$d['Weg']['s']==1&&(TIME>strtotime('6:00')&&TIME<strtotime('8:00'))) {
			sw('hall', 'On');
		}
	}
}
function sirene($msg)
{
    global $d,$device;
    $boven=array('pirhall');
    if (in_array($device, $boven)) {
        if ($d['Weg']['s']==2&&$d['Weg']['m']>TIME-178&&$d['poortrf']['s']=='Off') {
            sw('sirene', 'On');
            shell_exec('../ios.sh "'.$msg.'" > /dev/null 2>/dev/null &');
            telegram($msg.' om '.strftime("%k:%M:%S", TIME), false, 2);
        }
    } else {
        if ($d['Weg']['s']>=1&&$d['Weg']['m']>TIME-178&&$d['poortrf']['s']=='Off') {
            sw('sirene', 'On');
            shell_exec('../ios.sh "'.$msg.'" > /dev/null 2>/dev/null &');
            telegram($msg.' om '.strftime("%k:%M:%S", TIME), false, 2);
        }
    }
    storemode('Weg', TIME);
}
function createheader($page='')
{
    global $udevice;
    echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
    if ($page=='') {
        echo '
<html>';
    } else {
        echo '
<html manifest="floorplan.appcache">';
    }
    echo '
    <head>
		<title>Floorplan</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">';
    if ($udevice=='iPhone') {
        echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.655,user-scalable=yes,minimal-ui">';
    } elseif ($udevice=='iPad') {
        echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.2,user-scalable=yes,minimal-ui">';
    }
    echo '
	    <link rel="manifest" href="/manifest.json">
	    <link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-startup-image" href="images/domoticzphp144.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php">
		<script type="text/javascript" src="/scripts/jQuery.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js"></script>';
    if ($page!='') {
        echo '
    <script type=\'text/javascript\'>
        function navigator_Go(url){window.location.assign(url);}
        $(document).ready(function(){initview();});
    </script>';
    }
    echo '
	</head>';
}