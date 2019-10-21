<?php
/**
 * Pass2PHP
 * php version 7.3.9-1
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
    sw(array('slapen'), 'Off', basename(__FILE__).':'.__LINE__);
    $items=array('living_set','tobi_set','alex_set','kamer_set','badkamer_set','eettafel','zithoek'/*,'dimactionkamer','dimactiontobi','dimactionalex'*/);
    foreach ($items as $i) {
        if ($d[$i]['m']!=0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
    }
    $items=array('Rliving','Rbureel','RkeukenL','RkeukenR','luifel');
    foreach ($items as $i) {
        if ($d[$i]['m']!=0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
    }
    $items=array('Ralex','RkamerL','RkamerR');
    foreach ($items as $i) {
        if ($d[$i]['m']!=2) storemode($i, 2, basename(__FILE__).':'.__LINE__);
    }
    if ($d['gcal']['s']==true) {
        if ($d['Rtobi']['m']!=2) storemode('Rtobi', 2, basename(__FILE__).':'.__LINE__);
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
        sw('auto', 'On', basename(__FILE__).':'.__LINE__);
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
        if ($d[$i]['m']!=0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
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
        $euro=($d['douche']['s']*10*0.004)+($d['douche']['m']*0.005);
		$eurocent=round($euro*100, 0);
		douchewarn($eurocent, 0);
		$msg='Douche__Gas: '.$douchegas.'L = '.($douchegas*0.004).'€__Water: '.$douchewater.'L = '.($douchewater*0.005).'€__Som = '.(($douchegas*0.004)+($douchewater*0.005)).'€';
		echo $msg;
		telegram($msg);
		store('douche', 0, basename(__FILE__).':'.__LINE__);
		storemode('douche', 0, basename(__FILE__).':'.__LINE__);
		sleep(8);
    }
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
function douchewarn($eurocent,$vol=0)
{
    lg('Douchewarn '.$eurocent);
    global $boseipbadkamer, $d;
    if ($d['douche']['icon']<TIME-30) {
    	storeicon('douche', TIME);
		if ($vol>0) $volume=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.102:8090/volume'))), true);
		if ($eurocent<100) boseplayinfo('Douche. '.$eurocent.' cent', 40);
		else {
			$euro=floor($eurocent/100);
			$cent=$eurocent%($euro*100);
			if ($cent==0) boseplayinfo('Douche. '.$euro.' euro', 40);
			else boseplayinfo('Douche. '.$euro.' euro '.$cent.' cent', 40);
		}
		if ($vol>0) {
			$cv=$volume['actualvolume'];
			if ($cv<$vol) {
				usleep(1550000);
				bosevolume($vol, 102);
				usleep(3500000);
				bosevolume($cv, 102);
			}
		}
		if ($vol>0) telegram('Douche € '.number_format(($eurocent/100), 2, ',', '.').' geluid op vol '.$vol);
		else telegram('Douche € '.number_format(($eurocent/100), 2, ',', '.'));
	}
}
function roundUpToAny($n,$x=5) {
    return round(($n+$x/2)/$x)*$x;
}
function boseplayinfo($sound, $vol=50, $log='') {
	global $d;
	if(empty($d)) $d=fetchdata();
	//if ($d['bose101']['s']=='On') {
		$raw=rawurlencode($sound);
		if(file_exists('/var/www/html/sounds/'.$sound.'.mp3')) {
			$postdata="<play_info><app_key>UJvfKvnMPgzK6oc7tTE1QpAVcOqp4BAY</app_key><url>http://192.168.2.2/sounds/$raw.mp3</url><service>$sound</service><reason>$sound</reason><message>$sound</message><volume>$vol</volume></play_info>";
			$opts=array('http'=>array('method'=>'POST', 'header' =>'Content-Type: text/xml', 'content'=>$postdata));
			for($x=1;$x<=100;$x++) {
				$context=stream_context_create($opts);
				$result=file_get_contents('http://192.168.2.105:8090/speaker', false, $context);
				if ($result=='<?xml version="1.0" encoding="UTF-8" ?><Error value="409" name="HTTP_STATUS_CONFLICT" severity="Unknown">request not supported while speaker resource is in use</Error>') {
					usleep(30000);
				} else {
					lg($result);
					break;
				}
			}
		} else {
			$postdata = http_build_query(array('msg'=>'<break time="500ms"/>'.$sound, 'lang'=>'Lotte', 'source'=>'ttsmp3'));
			$opts = array('http'=>array('method'=>'POST', 'header' =>'Content-Type: application/x-www-form-urlencoded', 'content'=>$postdata));
			$context  = stream_context_create($opts);
			$result = json_decode(file_get_contents('https://ttsmp3.com/makemp3.php', false, $context), true);
			if($result['Error']==0&&isset($result['URL'])) {
				$mp3=file_get_contents($result['URL']);
				if(strlen($mp3)>1000) {
					file_put_contents('/var/www/html/sounds/'.$sound.'.mp3', $mp3);
				}
			}
			$postdata="<play_info><app_key>UJvfKvnMPgzK6oc7tTE1QpAVcOqp4BAY</app_key><url>http://192.168.2.2/sounds/$raw.mp3</url><service>$sound</service><reason>$sound</reason><message>$sound</message><volume>$vol</volume></play_info>";
			$opts=array('http'=>array('method'=>'POST', 'header' =>'Content-Type: text/xml', 'content'=>$postdata));
			for($x=1;$x<=100;$x++) {
				$context=stream_context_create($opts);
				$result=file_get_contents('http://192.168.2.105:8090/speaker', false, $context);
				if ($result=='<?xml version="1.0" encoding="UTF-8" ?><Error value="409" name="HTTP_STATUS_CONFLICT" severity="Unknown">request not supported while speaker resource is in use</Error>') {
					usleep(30000);
				} else {
					lg($result);
					break;
				}
			}
		}
	//}
}
function saytime() {
	$hour=strftime('%k', TIME);
	if ($hour>12) $hour=$hour-12;
	$minute=(1*strftime('%M', TIME));
	echo 'SayTime = '.$hour.':'.$minute.'	';
	if ($hour==0) $hourtxt='middernacht';
	else $hourtxt=$hour;
	if ($minute==0) $msg='Het is '.$hourtxt.' uur';
	elseif ($minute>0&&$minute<15) $msg='Het is '.$minute.' over '.$hourtxt;
	elseif ($minute==15) $msg='Het is kwart over '.$hourtxt;
	elseif ($minute==20) $msg='Het is 20 over '.$hourtxt;
	elseif ($minute==30) {
		if ($hour==12) $msg='Het is half 1';
		else $msg='Het is half '.($hour+1);
	} elseif ($minute>30&&$minute<40) {
		if ($hour==12) $msg='Het is '.($minute-30).' over half 1';
		else $msg='Het is '.($minute-30).' over half '.($hour+1);
	} elseif ($minute==40) $msg='Het is 20 voor '.($hour+1);
	elseif ($minute>40&&$minute<45) {
		if ($hour==12) $msg='Het is '.($minute-30).' over half 1';
		else $msg='Het is '.($minute-30).' over half '.($hour+1);
	} elseif ($minute==45) $msg='Het is kwart voor '.($hour+1);
	elseif ($minute>=50) $msg='Het is '.(60-$minute).' voor '.($hour+1);
	else $msg='Het is '.$hour.' uur '.$minute;
	echo $msg.'<br>';
	lg('SayTime = '.$hour.':'.$minute.'	'.$msg);
	boseplayinfo($msg, 30, basename(__FILE__).':'.__LINE__);
	return $msg;
}
function owcondition() {
	global $d;
	if (empty($d)) $d=fetchdata();
	$c=array(
		200=>'onweer met lichte regen',
		201=>'onweer met regen',
		202=>'onweer met zware regen',
		210=>'licht onweer',
		211=>'onweersbui',
		212=>'zware onweersbui',
		221=>'haveloze onweersbui',
		230=>'onweer met lichte motregen',
		231=>'onweer met motregen',
		232=>'onweer met zware motregen',
		300=>'lichte motregen',
		301=>'motregen',
		302=>'zware motregen',
		310=>'lichte motregen tot regen',
		311=>'motregen tot regen',
		312=>'zware intensiteit motregen tot regen',
		313=>'regen en motregen',
		314=>'zware regenbui en motregen',
		321=>'motregen',
		500=>'lichte regen',
		501=>'lichte regen',
		502=>'zware regenval',
		503=>'zeer zware regen',
		504=>'extreme regen',
		511=>'ijskoude regen',
		520=>'lichte regen',
		521=>' regen',
		522=>'zware regenbui',
		531=>'haveloze regen',
		600=>'lichte sneeuw',
		601=>'sneeuw',
		602=>'zware sneeuw',
		611=>'lichte ijzel',
		612=>'ijzel',
		613=>'sterke ijzel',
		615=>'lichte regen en sneeuw',
		616=>'regen en sneeuw',
		620=>'lichte sneeuw',
		621=>'sneeuw',
		622=>'zware sneeuw',
		701=>'nevel',
		711=>'rook',
		721=>'nevel',
		731=>'zand / stof wervelt',
		741=>'mist',
		751=>'zand',
		761=>'stof',
		762=>'vulkanische as',
		771=>'rukwinden',
		781=>'tornado',
		800=>'heldere lucht',
		801=>'weinig wolken',
		802=>'verspreide wolken',
		803=>'gebroken wolken',
		804=>'zwaar bewolkt',
	);
	if (isset($c[$d['icon']['m']])) return ' en '.$c[$d['icon']['m']];
	else return '';
}
/**
 * Function waarschuwing
 *
 * Plays a sound on the Xiami doorbell and a regular doorbell
 * Says the message on the Bose Soundtouch speakers
 * and sents a telegram message
 *
 * @param string $msg Message to sent to telegram
 *
 * @return null
 */
function waarschuwing($msg)
{
    global $d;
    if ($d['bose101']['s']=='On') {
    	boseplayinfo($msg);
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
        sl('Xvol', 25, basename(__FILE__).':'.__LINE__);
    }
    sl('Xring', 30, basename(__FILE__).':'.__LINE__);
    sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
    telegram($msg, false, 2);
    usleep(1500000);
    sl('Xring', 0, basename(__FILE__).':'.__LINE__);
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
function sl($name,$level,$msg='')
{
    global $user,$d,$domoticzurl;
    if(!isset($d))$d=fetchdata();
    lg(' (SETLEVEL)	'.$user.'=>'.$name.'=>'.$level.' ('.$msg.')');
    if ($d[$name]['i']>0) {
		if ($d[$name]['s']!=$level) {
			file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd=Set%20Level&level='.$level);
		}
    } else {
        store($name, $level, $msg);
    }
}
function rgb($name,$hue,$level,$check=false)
{
    global $user,$d,$domoticzurl;
    lg(' (RGB)		'.$user.' =>	'.$name.'	'.$level);
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
    if ($d['sirene']['s']!='Off') {
        sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
        usleep(100000);
        store('sirene', 'Off', basename(__FILE__).':'.__LINE__);
    }
    $items=array('SDbadkamer','SDkamer','SDalex','SDtobi','SDzolder','SDliving');
    foreach ($items as $i) {
        if ($d[$i]['s']!='Off') {
            file_get_contents($domoticzurl.'/json.htm?type=command&param=resetsecuritystatus&idx='.$d[$i]['i'].'&switchcmd=Normal');
            store($i, 'Off', basename(__FILE__).':'.__LINE__);
        }
    }
}
function sw($name,$action='Toggle',$msg='')
{
    global $user,$d,$domoticzurl;
    if (!isset($d)) $d=fetchdata();
    if (is_array($name)) {
        $usleep=200000;
        foreach ($name as $i) {
            if ($i=='media') {
                sw(array(/*'lgtv','denon',*/'tvled','kristal'/*,'nvidia'*/), $action, $msg);
            } elseif ($i=='lichtenbeneden') {
                sw(array('garage','garageled','pirgarage','pirkeuken','pirliving','pirinkom','eettafel','zithoek','media','bureel','jbl','terras','tuin','keuken','werkblad1','wasbak','kookplaat','inkom','zolderg','voordeur','wc'), $action, $msg);
            } elseif ($i=='lichtenboven') {
                sw(array('pirhall','lichtbadkamer','kamer','tobi','alex','hall','zolder'), $action, $msg);
            } elseif ($i=='slapen') {
                sw(array('hall','pirhall','lichtenbeneden','dampkap','GroheRed'), $action, $msg);
            } elseif ($i=='weg') {
                sw(array('garage','slapen','lichtenboven'), $action, $msg);
            } else {
                if ($d[$i]['s']!=$action) {
                    sw($i, $action);
                }
            }
            usleep($usleep);
        }
    } else {
        $msg=' (SWITCH)		'.$user.'=>'.$name.'=>'.$action.' ('.$msg.')';
        if ($d[$name]['i']>0) {
            lg($msg);
			if ($d[$name]['s']!=$action) {
				echo file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
			}
        } else {
            store($name, $action, $msg);
        }
        if ($name=='denon') {
            if ($action=='Off') {
                storemode('denon', 'UIT', basename(__FILE__).':'.__LINE__);
            }
        }
    }
}
function lgcommand($action,$msg='')
{
    global $lgtvip, $lgtvmac;
    if ($action=='on') {
        exec('python3 /var/www/html/secure/lgtv.py -c on -a '.$lgtvmac.' '.$lgtvip, $output, $return_var);
    } else {
        shell_exec('python3 lgtv.py -c '.$action.' '.$lgtvip.' > /dev/null 2>&1 &');
    }
}
function store($name,$status,$msg='',$idx=null,$force=true)
{
    global $db, $d, $user;
    if (!isset($d)) $d=fetchdata();
    $time=time();
	if ($idx>0) {
		$db->query("INSERT INTO devices (n,i,s,t) VALUES ('$name','$idx','$status','$time') ON DUPLICATE KEY UPDATE s='$status',i='$idx',t='$time';");
	} else {
		$db->query("INSERT INTO devices (n,s,t) VALUES ('$name','$status','$time') ON DUPLICATE KEY UPDATE s='$status',t='$time';");
	}
	lg(' (STORE)	'.$user.'	=> '.$name.'	=> '.$status.'	('.$msg.')');
}
function storemode($name,$mode,$msg='',$time=0)
{
    global $db, $user;
    $time=time()+$time;
	$db->query("INSERT INTO devices (n,m,t) VALUES ('$name','$mode','$time') ON DUPLICATE KEY UPDATE m='$mode',t='$time';");
	lg(' (STOREMODE)	'.$user.'	=> '.$name.'	=> '.$mode.'	('.$msg.')');
}
function storeicon($name,$icon,$msg='')
{
    global $db, $d, $user;
    $time=TIME;
    if ($d[$name]['icon']!=$icon) {
		$db->query("INSERT INTO devices (n,t,icon) VALUES ('$name','$time','$icon') ON DUPLICATE KEY UPDATE t='$time',icon='$icon';");
		lg(' (STOREICON)	'.$user.'	=> '.$name.'	=> '.$icon.'	('.$msg.')');
	}
}
function alert($name,$msg,$ttl,$silent=true,$to=1,$ios=false)
{
    global $db;
    $time=TIME;
    $stmt=$db->query("SELECT t FROM alerts WHERE n='$name';");
    $last=$stmt->fetch(PDO::FETCH_ASSOC);
    if (isset($last['t'])) {
        $last=$last['t'];
    }
    if ($last < $time-$ttl) {
        if ($ios) {
			shell_exec('./ios.sh "'.$msg.'" >/dev/null 2>/dev/null &');
		}
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
function ud($name,$nvalue,$svalue,$check=false,$smg='')
{
    global $user,$d,$domoticzurl;
    if ($d[$name]['i']>0) {
        if ($check==true) {
            if ($d[$name]['s']!=$svalue) {
                return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
            }
        } else {
            return file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.$d[$name]['i'].'&nvalue='.$nvalue.'&svalue='.$svalue);
        }
    } else {
        store($name, $svalue, basename(__FILE__).':'.__LINE__);
    }
    lg(' (udevice) | '.$user.'=>'.$name.'=>'.$nvalue.','.$svalue.(isset($msg)?' ('.$msg:')'));
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
                store('ping'.$ip, 0, basename(__FILE__).':'.__LINE__);
            }
            return 1;
        } else {
            $check=$d['ping'.$ip]['s']+1;
            if ($check>0) {
                store('ping'.$ip, $check, basename(__FILE__).':'.__LINE__);
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
                store('ping'.$ip, 0, basename(__FILE__).':'.__LINE__);
            }
            return 1;
        } else {
            $check=$d['ping'.$ip]['s']+1;
            if ($check>0) {
                store('ping'.$ip, $check, basename(__FILE__).':'.__LINE__);
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
function double($name, $action, $msg='')
{
    sw($name, $action, $msg);
    usleep(2000000);
    sw($name, $action, $msg);
}

function rookmelder($msg){
	global $d;
    alert($device, 	$msg, 	300, false, 2, true);
    $items=array(/*'Ralex',*/'Rtobi','RkamerL','RkeukenL','RkamerR','Rliving','RkeukenR','Rbureel');
    foreach ($items as $i) {
        if ($d[$i]['s']>0) {
        	sl($i, 0, basename(__FILE__).':'.__LINE__);
        }
    }
	if ($d['zon']['s']<500) {
		$items=array('hall','inkom','kamer','tobi',/*'alex',*/'eettafel','zithoek','lichtbadkamer', 'terras');
		foreach ($items as $i) {
			if ($d[$i]['s']<100) {
				sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
		$items=array('keuken','garage','jbl','bureel', 'tuin');
		foreach ($items as $i) {
			if ($d[$i]['s']!='On') {
				sw($i, 'On', basename(__FILE__).':'.__LINE__);
			}
		}
	}
    sleep(10);
    resetsecurity();
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
    lg('Telegram sent: '.$msg);
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
    $dontplayfirst=array(
    	'Cygnux X'=>'Superstring - Rank 1 Remix',
    	'Tiësto, Dzeko, Preme, Post Malone'=>'Jackie Chan',
    	'Pharrell Williams'=>'Happy - From "Despicable Me 2"',
    	'Christina Perri'=>'A Thousand Years'
    );
    if ($key=='PRESET_1') {
    	//Trance, Techno and retro
    	for ($x=1;$x<=10;$x++) {
			$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.".$ip.":8090/now_playing"))), true);
			if (!empty($nowplaying)) {
				if (isset($nowplaying['@attributes']['source'])) {
					if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
						if (array_key_exists(trim($nowplaying['artist']), $dontplayfirst)&&trim($nowplaying['track'])==$dontplayfirst[trim($nowplaying['artist'])]) {
							bosekey("NEXT_TRACK", $sleep, $ip);
							break;
						}
					}
				}
			}
			sleep(1);
		}
    } elseif ($key=='PRESET_2') {
    	//Tiesto
    	for ($x=1;$x<=10;$x++) {
			$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.".$ip.":8090/now_playing"))), true);
			if (!empty($nowplaying)) {
				if (isset($nowplaying['@attributes']['source'])) {
					if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
						if (array_key_exists(trim($nowplaying['artist']), $dontplayfirst)&&trim($nowplaying['track'])==$dontplayfirst[trim($nowplaying['artist'])]) {
							bosekey("NEXT_TRACK", $sleep, $ip);
							break;
						}
					}
				}
			}
			sleep(1);
		}
    } elseif ($key=='PRESET_3') {
    	//MNM
    } elseif ($key=='PRESET_4') {
    	//Happy music
    	for ($x=1;$x<=10;$x++) {
			$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.".$ip.":8090/now_playing"))), true);
			if (!empty($nowplaying)) {
				if (isset($nowplaying['@attributes']['source'])) {
					if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
						if (array_key_exists(trim($nowplaying['artist']), $dontplayfirst)&&trim($nowplaying['track'])==$dontplayfirst[trim($nowplaying['artist'])]) {
							bosekey("NEXT_TRACK", $sleep, $ip);
							break;
						}
					}
				}
			}
			sleep(1);
		}
    } elseif ($key=='PRESET_5') {
    	//Ballads
    	for ($x=1;$x<=10;$x++) {
			$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.".$ip.":8090/now_playing"))), true);
			if (!empty($nowplaying)) {
				if (isset($nowplaying['@attributes']['source'])) {
					if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
						if (array_key_exists(trim($nowplaying['artist']), $dontplayfirst)&&trim($nowplaying['track'])==$dontplayfirst[trim($nowplaying['artist'])]) {
							bosekey("NEXT_TRACK", $sleep, $ip);
							break;
						}
					}
				}
			}
			sleep(1);
		}
    } elseif ($key=='PRESET_6') {
    	//Mix
    	for ($x=1;$x<=10;$x++) {
			$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.".$ip.":8090/now_playing"))), true);
			if (!empty($nowplaying)) {
				if (isset($nowplaying['@attributes']['source'])) {
					if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
						if (array_key_exists(trim($nowplaying['artist']), $dontplayfirst)&&trim($nowplaying['track'])==$dontplayfirst[trim($nowplaying['artist'])]) {
							bosekey("NEXT_TRACK", $sleep, $ip);
							break;
						}
					}
				}
			}
			sleep(1);
		}
    }
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
function bosezone($ip,$vol='')
{
    $d=fetchdata();
    if (TIME<strtotime('9:00')) $preset='PRESET_4';
    elseif (TIME>strtotime('20:00')) $preset='PRESET_5';
    else  $preset='PRESET_6';
    if ($d['Weg']['s']<=1) {
        if ($d['Weg']['s']==0&&$d['denonpower']['s']=='OFF'&&$d['bose101']['s']=='Off'&&TIME<strtotime('21:00')) {
            sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
            bosekey($preset, 0, 101);
            bosevolume(25, 101);
        } /*elseif ($d['bose101']['s']=='On'&&$d['denonpower']['s']=='OFF') {
            $volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.101:8090/volume"))), true);
            if (isset($volume['actualvolume'])) {
                $cv=$volume['actualvolume'];
                if ($cv<10) {
                    bosevolume(10, 101);
                }
            }
        }*/
        if ($ip>101) {
        	if ($d['bose'.$ip]['s']!='On') {
	            sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);
	        }
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
                sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
                bosekey($preset, 0, 101);
                if ($d['denonpower']['s']=='ON'||$d['denon']['s']=='On') {
                    bosevolume(0, 101);
                } else {
                    bosevolume(25, 101);
                }
                bosepost('setZone', $xml, 101);
                if ($vol=='') {
					if (TIME>strtotime('6:00')&&TIME<strtotime('20:00')) {
						bosevolume(30, $ip);
					} else {
						bosevolume(22, $ip);
					}
				} else {
						bosevolume($vol, $ip);
				}
            } elseif ($d['bose'.$ip]['s']=='Off') {
                bosepost('setZone', $xml, 101);
                store('bose'.$ip, 'On');
                if ($vol=='') {
					if (TIME>strtotime('6:00')&&TIME<strtotime('21:00')) {
						bosevolume(30, $ip);
					} else {
						bosevolume(20, $ip);
					}
				} else {
					bosevolume($vol, $ip);
				}
            }
        }
        usleep(200000);
        if ($d['bose101']['m']<TIME-3600) {
        	storemode('bose101', TIME);
	        bosekey('SHUFFLE_ON', 0, 101);
	    }
    }
}
function bosepost($method,$xml,$ip=103,$log=false)
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
    if ($d['Weg']['s']==0&&$d['denonpower']['s']=='OFF'&&$d['bureel']['s']=='Off'&&$d['eettafel']['s']==0) {
        if ($d['zon']['s']==0) {
            if ($d['keuken']['s']=='Off') {
                sw('keuken', 'On', basename(__FILE__).':'.__LINE__);
            }
            if ($d['bureel']['s']=='Off') {
                sw('bureel', 'On', basename(__FILE__).':'.__LINE__);
            }
            if ($d['jbl']['s']=='Off') {
                sw('jbl', 'On', basename(__FILE__).':'.__LINE__);
            }
        }
        if (TIME>=strtotime('5:30')&&TIME<strtotime('21:30')) {
        	bosezone(101);
        }
    }
}
function fgarage()
{
    global $d;
    if ($d['Weg']['s']==0&&($d['zon']['s']<300||TIME<strtotime('7:00')||TIME>strtotime('22:00'))&&$d['garage']['s']=='Off'&&$d['garageled']['s']=='Off') {
        sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
    }
    if (TIME>=strtotime('5:30')&&TIME<strtotime('21:30')) {
    	bosezone(104);
    }
}
function fbadkamer()
{
    global $d;
    if (past('8badkamer-8')>10) {
		if ($d['lichtbadkamer']['s']<16&&$d['zon']['s']==0) {
            if (TIME>strtotime('5:30')&&TIME<strtotime('21:30')) {
            	sl('lichtbadkamer', 16, basename(__FILE__).':'.__LINE__);
            } else {
            	sl('lichtbadkamer', 10, basename(__FILE__).':'.__LINE__);
            }
        }
        if (TIME>strtotime('5:30')&&TIME<strtotime('10:30')) {
        	bosezone(102);
        	sleep(2);
        	saytime();
        	sleep(2);
        	boseplayinfo('Het wordt vandaag tussen '.floor($d['minmaxtemp']['s']).' en '.ceil($d['minmaxtemp']['m']).' graden'.owcondition(), 30);
        }
    }
}
function fkeuken()
{
    global $d;
    if (TIME<strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']==0||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        sw('keuken', 'On', basename(__FILE__).':'.__LINE__);
    } elseif (TIME>=strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']==0||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        if ($d['tv']['s']=='On'||$d['jbl']['s']=='On') {
            sw('keuken', 'On', basename(__FILE__).':'.__LINE__);
        }
    }
}
function finkom()
{
    global $d;
    if ($d['Weg']['s']==0&&$d['inkom']['s']<31&&TIME>strtotime('6:00')&&TIME<=strtotime('21:00')&&$d['zon']['s']<50) {
        sl('inkom', 31, basename(__FILE__).':'.__LINE__);
    } elseif ($d['Weg']['s']==0&&$d['inkom']['s']<24&&$d['zon']['s']==0) {
        sl('inkom', 24, basename(__FILE__).':'.__LINE__);
    }
}
function fhall()
{
    global $d,$device;
    if ($d['hall']['s']<31) {
		if ($d['Weg']['s']==0&&TIME>strtotime('6:00')&&TIME<=strtotime('21:00')&&$d['zon']['s']==0) {
			if ($d['hall']['s']<31) {
				sl('hall', 31, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($d['Weg']['s']==0&&$d['zon']['s']==0) {
			if ($d['hall']['s']<24) {
				sl('hall', 24, basename(__FILE__).':'.__LINE__);
			}
		} elseif (isset($device)&&$device!='pirhall'&&$d['Weg']['s']==1&&(TIME>strtotime('6:00')&&TIME<strtotime('8:00'))) {
			if ($d['hall']['s']<31) {
				sl('hall', 31, basename(__FILE__).':'.__LINE__);
			}
		}
	}
	if ($d['gcal']['s']==false&&TIME>=strtotime('6:00')&&TIME<strtotime('10:15')&&$d['Rtobi']['s']>0) {
		sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
	}
}
function sirene($msg)
{
    global $d,$device;
    $boven=array('pirhall');
    if (in_array($device, $boven)) {
        if ($d['Weg']['s']==2&&$d['Weg']['m']>TIME-178&&$d['poortrf']['s']=='Off') {
            sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
            shell_exec('../ios.sh "'.$msg.'" > /dev/null 2>/dev/null &');
            telegram($msg.' om '.strftime("%k:%M:%S", TIME), false, 2);
        }
    } else {
        if ($d['Weg']['s']>=1&&$d['Weg']['m']>TIME-178&&$d['poortrf']['s']=='Off') {
            sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
            shell_exec('../ios.sh "'.$msg.'" > /dev/null 2>/dev/null &');
            telegram($msg.' om '.strftime("%k:%M:%S", TIME), false, 2);
        }
    }
    if ($d['Weg']['s']>0) {
    	storemode('Weg', TIME, basename(__FILE__).':'.__LINE__);
    }
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
//manifest="floorplan.appcache"
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
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php?v=3">
		<script type="text/javascript" src="/scripts/jQuery.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js?v=4"></script>';
    if ($page!='') {
        echo '
		<script type=\'text/javascript\'>
			$(document).ready(function(){initview();});
		</script>';
    }
    echo '
	</head>';
}


class MP3File
{
    protected $filename;
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
 
    public static function formatTime($duration) //as hh:mm:ss
    {
        //return sprintf("%d:%02d", $duration/60, $duration%60);
        $hours = floor($duration / 3600);
        $minutes = floor( ($duration - ($hours * 3600)) / 60);
        $seconds = $duration - ($hours * 3600) - ($minutes * 60);
        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
 
    //Read first mp3 frame only...  use for CBR constant bit rate MP3s
    public function getDurationEstimate()
    {
        return $this->getDuration($use_cbr_estimate=true);
    }
 
    //Read entire file, frame by frame... ie: Variable Bit Rate (VBR)
    public function getDuration($use_cbr_estimate=false)
    {
        $fd = fopen($this->filename, "rb");
 
        $duration=0;
        $block = fread($fd, 100);
        $offset = $this->skipID3v2Tag($block);
        fseek($fd, $offset, SEEK_SET);
        while (!feof($fd))
        {
            $block = fread($fd, 10);
            if (strlen($block)<10) { break; }
            //looking for 1111 1111 111 (frame synchronization bits)
            else if ($block[0]=="\xff" && (ord($block[1])&0xe0) )
            {
                $info = self::parseFrameHeader(substr($block, 0, 4));
                if (empty($info['Framesize'])) { return $duration; } //some corrupt mp3 files
                fseek($fd, $info['Framesize']-10, SEEK_CUR);
                $duration += ( $info['Samples'] / $info['Sampling Rate'] );
            }
            else if (substr($block, 0, 3)=='TAG')
            {
                fseek($fd, 128-10, SEEK_CUR);//skip over id3v1 tag size
            }
            else
            {
                fseek($fd, -9, SEEK_CUR);
            }
            if ($use_cbr_estimate && !empty($info))
            { 
                return $this->estimateDuration($info['Bitrate'],$offset); 
            }
        }
        return round($duration);
    }
 
    private function estimateDuration($bitrate,$offset)
    {
        $kbps = ($bitrate*1000)/8;
        $datasize = filesize($this->filename) - $offset;
        return round($datasize / $kbps);
    }
 
    private function skipID3v2Tag(&$block)
    {
        if (substr($block, 0,3)=="ID3")
        {
            $id3v2_major_version = ord($block[3]);
            $id3v2_minor_version = ord($block[4]);
            $id3v2_flags = ord($block[5]);
            $flag_unsynchronisation  = $id3v2_flags & 0x80 ? 1 : 0;
            $flag_extended_header    = $id3v2_flags & 0x40 ? 1 : 0;
            $flag_experimental_ind   = $id3v2_flags & 0x20 ? 1 : 0;
            $flag_footer_present     = $id3v2_flags & 0x10 ? 1 : 0;
            $z0 = ord($block[6]);
            $z1 = ord($block[7]);
            $z2 = ord($block[8]);
            $z3 = ord($block[9]);
            if ( (($z0&0x80)==0) && (($z1&0x80)==0) && (($z2&0x80)==0) && (($z3&0x80)==0) )
            {
                $header_size = 10;
                $tag_size = (($z0&0x7f) * 2097152) + (($z1&0x7f) * 16384) + (($z2&0x7f) * 128) + ($z3&0x7f);
                $footer_size = $flag_footer_present ? 10 : 0;
                return $header_size + $tag_size + $footer_size;//bytes to skip
            }
        }
        return 0;
    }
 
    public static function parseFrameHeader($fourbytes)
    {
        static $versions = array(
            0x0=>'2.5',0x1=>'x',0x2=>'2',0x3=>'1', // x=>'reserved'
        );
        static $layers = array(
            0x0=>'x',0x1=>'3',0x2=>'2',0x3=>'1', // x=>'reserved'
        );
        static $bitrates = array(
            'V1L1'=>array(0,32,64,96,128,160,192,224,256,288,320,352,384,416,448),
            'V1L2'=>array(0,32,48,56, 64, 80, 96,112,128,160,192,224,256,320,384),
            'V1L3'=>array(0,32,40,48, 56, 64, 80, 96,112,128,160,192,224,256,320),
            'V2L1'=>array(0,32,48,56, 64, 80, 96,112,128,144,160,176,192,224,256),
            'V2L2'=>array(0, 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160),
            'V2L3'=>array(0, 8,16,24, 32, 40, 48, 56, 64, 80, 96,112,128,144,160),
        );
        static $sample_rates = array(
            '1'   => array(44100,48000,32000),
            '2'   => array(22050,24000,16000),
            '2.5' => array(11025,12000, 8000),
        );
        static $samples = array(
            1 => array( 1 => 384, 2 =>1152, 3 =>1152, ), //MPEGv1,     Layers 1,2,3
            2 => array( 1 => 384, 2 =>1152, 3 => 576, ), //MPEGv2/2.5, Layers 1,2,3
        );
        //$b0=ord($fourbytes[0]);//will always be 0xff
        $b1=ord($fourbytes[1]);
        $b2=ord($fourbytes[2]);
        $b3=ord($fourbytes[3]);
 
        $version_bits = ($b1 & 0x18) >> 3;
        $version = $versions[$version_bits];
        $simple_version =  ($version=='2.5' ? 2 : $version);
 
        $layer_bits = ($b1 & 0x06) >> 1;
        $layer = $layers[$layer_bits];
 
        $protection_bit = ($b1 & 0x01);
        $bitrate_key = sprintf('V%dL%d', $simple_version , $layer);
        $bitrate_idx = ($b2 & 0xf0) >> 4;
        $bitrate = isset($bitrates[$bitrate_key][$bitrate_idx]) ? $bitrates[$bitrate_key][$bitrate_idx] : 0;
 
        $sample_rate_idx = ($b2 & 0x0c) >> 2;//0xc => b1100
        $sample_rate = isset($sample_rates[$version][$sample_rate_idx]) ? $sample_rates[$version][$sample_rate_idx] : 0;
        $padding_bit = ($b2 & 0x02) >> 1;
        $private_bit = ($b2 & 0x01);
        $channel_mode_bits = ($b3 & 0xc0) >> 6;
        $mode_extension_bits = ($b3 & 0x30) >> 4;
        $copyright_bit = ($b3 & 0x08) >> 3;
        $original_bit = ($b3 & 0x04) >> 2;
        $emphasis = ($b3 & 0x03);
 
        $info = array();
        $info['Version'] = $version;//MPEGVersion
        $info['Layer'] = $layer;
        //$info['Protection Bit'] = $protection_bit; //0=> protected by 2 byte CRC, 1=>not protected
        $info['Bitrate'] = $bitrate;
        $info['Sampling Rate'] = $sample_rate;
        //$info['Padding Bit'] = $padding_bit;
        //$info['Private Bit'] = $private_bit;
        //$info['Channel Mode'] = $channel_mode_bits;
        //$info['Mode Extension'] = $mode_extension_bits;
        //$info['Copyright'] = $copyright_bit;
        //$info['Original'] = $original_bit;
        //$info['Emphasis'] = $emphasis;
        $info['Framesize'] = self::framesize($layer, $bitrate, $sample_rate, $padding_bit);
        $info['Samples'] = $samples[$simple_version][$layer];
        return $info;
    }
 
    private static function framesize($layer, $bitrate,$sample_rate,$padding_bit)
    {
        if ($layer==1)
            return intval(((12 * $bitrate*1000 /$sample_rate) + $padding_bit) * 4);
        else //layer 2, 3
            return intval(((144 * $bitrate*1000)/$sample_rate) + $padding_bit);
    }
}