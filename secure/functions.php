<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
//session_start();
require '/var/www/config.php';
$dow=date("w");
if($dow==0||$dow==6)$weekend=true; else $weekend=false;

function dbconnect() {
	global $dbname,$dbuser,$dbpass;
	return new PDO("mysql:host=localhost;dbname=$dbname;",$dbuser,$dbpass);
}
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
    $db=dbconnect();
    $stmt=$db->query("select n,i,s,t,m,dt,icon from devices;");
    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) $d[$row['n']] = $row;
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
    sl(array('hall','inkom','eettafel','zithoek','terras'), 0, basename(__FILE__).':'.__LINE__);
    sw(array('garageled','garage','pirgarage','pirkeuken','pirliving','pirinkom','pirhall','media','bureel','jbl','tuin','keuken','werkblad1','wasbak','kookplaat','zolderg','voordeur','wc','dampkap','GroheRed'), 'Off', basename(__FILE__).':'.__LINE__);
	
    foreach (array('living_set','tobi_set','alex_set','kamer_set','badkamer_set','eettafel','zithoek','luifel') as $i) {
        if ($d[$i]['m']!=0) storemode($i, 0, basename(__FILE__).':'.__LINE__);
    }
    $status=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
    if (!empty($status)) {
        if (isset($status['@attributes']['source'])) {
            if ($status['@attributes']['source']!='STANDBY') {
                bosekey("POWER", 0, 101);
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
    //global $d;
    //if(empty($d)) $d=fetchdata();
    huisslapen();
    
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
    if ($douchegas>0||$douchewater>0) {
        $euro=($d['douche']['s']*10*0.004)+($d['douche']['m']*0.005);
		$eurocent=round($euro*100, 0);
		douchewarn($eurocent, 0);
		$msg='Douche__Gas: '.$douchegas.'L = '.($douchegas*0.004).'€__Water: '.$douchewater.'L = '.($douchewater*0.005).'€__Som = '.(($douchegas*0.004)+($douchewater*0.005)).'€';
		ifttt('douche', $douchegas, $douchewater, (($douchegas*0.004)+($douchewater*0.005)));
		//telegram($msg);
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
		//if ($vol>0) telegram('Douche € '.number_format(($eurocent/100), 2, ',', '.').' geluid op vol '.$vol);
		//else telegram('Douche € '.number_format(($eurocent/100), 2, ',', '.'));
	}
}
function roundUpToAny($n,$x=5) {
    return round(($n+$x/2)/$x)*$x;
}
function boseplayinfo($sound, $vol=50, $log='', $ip=101) {
	global $d, $googleTTSAPIKey;
	if(empty($d)) $d=fetchdata();
	lg('boseplayinfo: '.$sound);
	$raw=rawurlencode($sound);
	if(file_exists('/var/www/html/sounds/'.$sound.'.mp3')) {
		$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
		$vol=$volume['actualvolume'];
		$xml="<play_info><app_key>UJvfKvnMPgzK6oc7tTE1QpAVcOqp4BAY</app_key><url>http://192.168.2.2/sounds/$raw.mp3</url><service>$sound</service><reason>$sound</reason><message>$sound</message><volume>$vol</volume></play_info>";
		bosepost('speaker', $xml);
		bosevolume($volume['actualvolume'], 101);
	} else {
		require 'gcal/google-api-php-client/vendor/autoload.php';
		$client=new GuzzleHttp\Client();
		$requestData=['input'=>['text'=>$sound],'voice'=>['languageCode'=>'nl-NL','name'=>'nl-NL-Wavenet-B'],'audioConfig'=>['audioEncoding'=>'MP3','pitch'=>0.00,'speakingRate'=>1.00,'effectsProfileId' => 'large-home-entertainment-class-device']];
		try {
			$response=$client->request('POST', 'https://texttospeech.googleapis.com/v1beta1/text:synthesize?key='.$googleTTSAPIKey, ['json'=>$requestData]);
			$fileData=json_decode($response->getBody()->getContents(), true);
			$audio=base64_decode($fileData['audioContent']);
			if(strlen($audio)>10) {
				file_put_contents('/var/www/html/sounds/'.$sound.'.mp3', $audio);
				$volume=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.101:8090/volume'))), true);
				$vol=$volume['actualvolume'];
				$xml="<play_info><app_key>UJvfKvnMPgzK6oc7tTE1QpAVcOqp4BAY</app_key><url>http://192.168.2.2/sounds/$raw.mp3</url><service>$sound</service><reason>$sound</reason><message>$sound</message><volume>$vol</volume></play_info>";
				bosepost('speaker', $xml);
				bosevolume($volume['actualvolume'], 101);
			}
		} catch (Exception $e) {
			exit('Something went wrong: ' . $e->getMessage());
		}
		
	}
}
function saytime($ip=101) {
	$hour=strftime('%k', TIME);
	$minute=(1*strftime('%M', TIME));
	if ($hour==0) {
		if ($minute==0) $msg='Het is middernacht';
		elseif ($minute>0&&$minute<15) $msg='Het is '.$minute.' over middernacht';
		elseif ($minute==15) $msg='Het is kwart over middernacht';
		elseif ($minute==20) $msg='Het is 20 over middernacht';
		elseif ($minute==30) $msg='Het is half 1';
		elseif ($minute>30&&$minute<40) $msg='Het is '.($minute-30).' over half 1';
		elseif ($minute==40) $msg='Het is 20 voor 1'.($hour+1);
		elseif ($minute>40&&$minute<45) $msg='Het is '.($minute-30).' over half 1';
		elseif ($minute==45) $msg='Het is kwart voor 1';
		elseif ($minute>=50) $msg='Het is '.(60-$minute).' voor 1';
		else $msg='Het is '.$hour.' uur '.$minute;
	} elseif ($hour<12) {
		if ($minute==0) $msg='Het is '.$hour.' uur';
		elseif ($minute>0&&$minute<15) $msg='Het is '.$minute.' over '.$hour;
		elseif ($minute==15) $msg='Het is kwart over '.$hour;
		elseif ($minute==20) $msg='Het is 20 over '.$hour;
		elseif ($minute==30) $msg='Het is half '.($hour+1);
		elseif ($minute>30&&$minute<40) $msg='Het is '.($minute-30).' over half '.($hour+1);
		elseif ($minute==40) $msg='Het is 20 voor '.($hour+1);
		elseif ($minute>40&&$minute<45) $msg='Het is '.($minute-30).' over half '.($hour+1);
		elseif ($minute==45) $msg='Het is kwart voor '.($hour+1);
		elseif ($minute>=50) $msg='Het is '.(60-$minute).' voor '.($hour+1);
		else $msg='Het is '.$hour.' uur '.$minute;
	} elseif ($hour==12) {
		if ($minute==0) $msg='Het is 12 uur';
		elseif ($minute>0&&$minute<15) $msg='Het is '.$minute.' over '.$hour;
		elseif ($minute==15) $msg='Het is kwart over '.$hour;
		elseif ($minute==20) $msg='Het is 20 over '.$hour;
		elseif ($minute==30) $msg='Het is half 1';
		elseif ($minute>30&&$minute<40) $msg='Het is '.($minute-30).' over half 1';
		elseif ($minute==40) $msg='Het is 20 voor 1';
		elseif ($minute>40&&$minute<45) $msg='Het is '.($minute-30).' over half 1';
		elseif ($minute==45) $msg='Het is kwart voor 1';
		elseif ($minute>=50) $msg='Het is '.(60-$minute).' voor 1';
		else $msg='Het is '.$hour.' uur '.$minute;
	} elseif ($hour>=13) {
		$hour=$hour-12;
		if ($minute==0) $msg='Het is '.$hour.' uur';
		elseif ($minute>0&&$minute<15) $msg='Het is '.$minute.' over '.$hour;
		elseif ($minute==15) $msg='Het is kwart over '.$hour;
		elseif ($minute==20) $msg='Het is 20 over '.$hour;
		elseif ($minute==30) $msg='Het is half '.($hour+1);
		elseif ($minute>30&&$minute<40) $msg='Het is '.($minute-30).' over half '.($hour+1);
		elseif ($minute==40) $msg='Het is 20 voor '.($hour+1);
		elseif ($minute>40&&$minute<45) $msg='Het is '.($minute-30).' over half '.($hour+1);
		elseif ($minute==45) $msg='Het is kwart voor '.($hour+1);
		elseif ($minute>=50) $msg='Het is '.(60-$minute).' voor '.($hour+1);
		else $msg='Het is '.$hour.' uur '.$minute;
	} 
	return $msg.'. ';
}
function sayweather($ip=101) {
	global $d;
	return ('Het wordt tussen '.floor($d['minmaxtemp']['s']).' en '.ceil($d['minmaxtemp']['m']).' graden '.owcondition().'.');
}
function owcondition() {
	global $d;
	if (empty($d)) $d=fetchdata();
	$c=array(
		200=>' met onweer en lichte regen',
		201=>' met onweer en regen',
		202=>' met onweer en zware regen',
		210=>' met licht onweer',
		211=>' met een onweersbui',
		212=>' met een zware onweersbui',
		221=>' met een haveloze onweersbui',
		230=>' met onweer en lichte motregen',
		231=>' met onweer en motregen',
		232=>' met onweer en zware motregen',
		300=>' met lichte motregen',
		301=>' met motregen',
		302=>' met zware motregen',
		310=>' met lichte motregen tot regen',
		311=>' met motregen tot regen',
		312=>' met zware motregen tot regen',
		313=>' met regen en motregen',
		314=>' met zware regenbui en motregen',
		321=>' met motregen',
		500=>' met lichte regen',
		501=>' met lichte regen',
		502=>' met zware regen',
		503=>' met zeer zware regen',
		504=>' met extreme regen',
		511=>' met ijskoude regen',
		520=>' met lichte regen',
		521=>' met regen',
		522=>' met een zware regenbui',
		531=>' met haveloze regen',
		600=>' met lichte sneeuw',
		601=>' met sneeuw',
		602=>' met zware sneeuw',
		611=>' met lichte ijzel',
		612=>' met ijzel',
		613=>' met sterke ijzel',
		615=>' met lichte regen en sneeuw',
		616=>' met regen en sneeuw',
		620=>' met lichte sneeuw',
		621=>' met sneeuw',
		622=>' met zware sneeuw',
		701=>' met nevel',
		711=>' met rook',
		721=>' met nevel',
		731=>' met zand / stof wervelt',
		741=>' met mist',
		751=>' met zand',
		761=>' met stof',
		762=>' met vulkanische as',
		771=>' met rukwinden',
		781=>' met tornado',
		800=>' met heldere lucht',
		801=>' met weinig wolken',
		802=>' met verspreide wolken',
		803=>' met veel wolken',
		804=>' met zwaar bewolkt',
	);
	if (isset($c[$d['icon']['m']])) return $c[$d['icon']['m']];
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
    	boseplayinfo($msg, 40);
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
     if (is_array($name)) {
        foreach ($name as $i) {
			if ($d[$i]['s']!=$level) {
				sl($i, $level, $msg);
				usleep(100000);
			}
        }
    } else {
		lg(' (SETLEVEL)	'.$user.'=>'.$name.'=>'.$level.' ('.$msg.')');
		if ($d[$name]['i']>0) {
			if ($d[$name]['s']!=$level) {
				file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd=Set%20Level&level='.$level);
			}
			
		} else {
			store($name, $level, $msg);
		}
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
    foreach (array('SDbadkamer','SDkamer','SDalex','SDtobi','SDzolder','SDliving') as $i) {
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
        foreach ($name as $i) {
			if ($d[$i]['s']!=$action) {
				sw($i, $action, $msg);
				usleep(100000);
			}
        }
    } else {
        $msg=' (SWITCH)		'.$user.'=>'.$name.'=>'.$action.' ('.$msg.')';
        if ($d[$name]['i']>0) {
            lg($msg);
			if ($d[$name]['s']!=$action||$name=='deurbel') {
				echo file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
			}
        } else {
            store($name, $action, $msg);
        }
        if ($name=='denon') {
            if ($action=='Off') {
                storemode('denon', 'UIT', basename(__FILE__).':'.__LINE__);
            }
        } else {
   			if (in_array($name, array('brander','badkamervuur1','badkamervuur2','heater1','heater2','regenpomp','zoldervuur'))) {
   				$stamp=TIME;
   				$db=dbconnect();
   				$db->query("INSERT INTO ontime (device,stamp,status) VALUES ('$name','$stamp','$action');");
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
    global $d, $user;
    if (!isset($d)) $d=fetchdata();
    $time=time();
	$db=dbconnect();
	if ($idx>0) {
		$db->query("INSERT INTO devices (n,i,s,t) VALUES ('$name','$idx','$status','$time') ON DUPLICATE KEY UPDATE s='$status',i='$idx',t='$time';");
	} else {
		$db->query("INSERT INTO devices (n,s,t) VALUES ('$name','$status','$time') ON DUPLICATE KEY UPDATE s='$status',t='$time';");
	}
	lg(' (STORE)	'.$user.'	=> '.$name.'	=> '.$status.'	('.$msg.')');
}
function storemode($name,$mode,$msg='',$time=0)
{
    global $user;
    $time=time()+$time;
	$db=dbconnect();
	$db->query("INSERT INTO devices (n,m,t) VALUES ('$name','$mode','$time') ON DUPLICATE KEY UPDATE m='$mode',t='$time';");
	lg(' (STOREMODE)	'.$user.'	=> '.$name.'	=> '.$mode.'	('.$msg.')');
}
function storeicon($name,$icon,$msg='')
{
    global $d, $user;
    $time=TIME;
    if ($d[$name]['icon']!=$icon) {
		$db=dbconnect();
		$db->query("INSERT INTO devices (n,t,icon) VALUES ('$name','$time','$icon') ON DUPLICATE KEY UPDATE t='$time',icon='$icon';");
		lg(' (STOREICON)	'.$user.'	=> '.$name.'	=> '.$icon.'	('.$msg.')');
	}
}
function alert($name,$msg,$ttl,$silent=true,$to=1,$ios=false)
{
    $db=dbconnect();
    $last=0;
    $stmt=$db->query("SELECT t FROM alerts WHERE n='$name';");

    while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		if (isset($row['t'])) {
			$last=$row['t'];
		}
    }

	if ($last < TIME-$ttl) {
        if ($ios) {
			shell_exec('./ios.sh "'.$msg.'" >/dev/null 2>/dev/null &');
		}
		$time=TIME;
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
function zwaveCommand($node,$command)
{
	global $domoticzurl;
	$cm=array('Refresh'=>'racp','AssignReturnRoute'=>'assrr','DeleteAllReturnRoutes'=>'delarr','NodeNeighbourUpdate'=>'reqnnu','RefreshNodeInformation'=>'refreshnode','RequestNetworkUpdate'=>'reqnu','HasNodeFailed'=>'hnf','Cancel'=>'cancel');
	$cm=$cm[$command];
	for($k=1;$k<=5;$k++){
		$result=file_get_contents($domoticzurl.'/ozwcp/admpost.html',false,stream_context_create(array('http'=>array('header'=>'Content-Type: application/x-www-form-urlencoded\r\n','method'=>'POST','content'=>http_build_query(array('fun'=>$cm,'node'=>'node'.$node)),),)));
		if ($result=='OK') {
			break;
		}
		sleep(1);
	}
	return $result;
}
function controllerBusy($retries){global $domoticzurl;for($k=1;$k<=$retries;$k++){$result=file_get_contents($domoticzurl.'/ozwcp/poll.xml');$p=xml_parser_create();xml_parse_into_struct($p,$result,$vals,$index);xml_parser_free($p);foreach($vals as $val){if($val['tag']=='ADMIN'){$result=$val['attributes']['ACTIVE'];break;}}if($result=='false'){break;}if($k==$retries){zwaveCommand(1,'Cancel');break;}sleep(1);}}
function convertToHours($time)
{
    if ($time<600) return substr(strftime('%k:%M:%S', $time-3600), 1);
    elseif ($time>=600&&$time<3600) return strftime('%k:%M:%S', $time-3600);
    else return strftime('%k:%M:%S', $time-3600);
}
function checkport($ip,$port='None')
{
    if ($port=='None') {
        if (ping($ip)) {
            $prevcheck=$d['ping'.$ip]['s'];
            if ($prevcheck>=5) telegram($ip.' online', true);
            if ($prevcheck>0) store('ping'.$ip, 0, basename(__FILE__).':'.__LINE__);
            return 1;
        } else {
            $check=$d['ping'.$ip]['s']+1;
            if ($check>0) store('ping'.$ip, $check, basename(__FILE__).':'.__LINE__);
            if ($check==5) telegram($ip.' Offline', true);
            if ($check%120==0) telegram($ip.' nog steeds Offline', true);
            return 0;
        }
    } else {
        if (pingport($ip, $port)==1) {
            $prevcheck=$d['ping'.$ip]['s'];
            if ($prevcheck>=5) telegram($ip.':'.$port.' online', true);
            if ($prevcheck>0) store('ping'.$ip, 0, basename(__FILE__).':'.__LINE__);
            return 1;
        } else {
            $check=$d['ping'.$ip]['s']+1;
            if ($check>0) store('ping'.$ip, $check, basename(__FILE__).':'.__LINE__);
            if ($check==5) telegram($ip.':'.$port.' Offline', true);
            if ($check%120==0) telegram($ip.':'.$port.' nog steeds Offline', true);
            return 0;
        }
    }
}
function ping($ip)
{
    $result=exec("/bin/ping -c1 -w1 $ip", $outcome, $status);
    if ($status==0) return true;
    else return false;
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
    foreach (array(/*'Ralex',*/'Rtobi','RkamerL','RkeukenL','RkamerR','Rliving','RkeukenR','Rbureel') as $i) {
        if ($d[$i]['s']>0) {
        	sl($i, 0, basename(__FILE__).':'.__LINE__);
        }
    }
	if ($d['zon']['s']<500) {
		foreach (array('hall','inkom','kamer','tobi',/*'alex',*/'eettafel','zithoek','lichtbadkamer', 'terras') as $i) {
			if ($d[$i]['s']<100) {
				sl($i, 100, basename(__FILE__).':'.__LINE__);
			}
		}
		foreach (array('keuken','garage','jbl','bureel', 'tuin') as $i) {
			if ($d[$i]['s']!='On') {
				sw($i, 'On', basename(__FILE__).':'.__LINE__);
			}
		}
	}
	bosezone(101);
	bosezone(102);
	bosezone(103);
	bosezone(104);
	boseplayinfo($msg, 45);
	sleep(5);
	boseplayinfo($msg, 45);
    resetsecurity();
}
function ifttt($event, $value1='', $value2='', $value3='', $msg='')
{
	global $iftttkey;
	$msg1='';
	if (!empty($value1))$msg1.='	value1='.$value1;
	if (!empty($value2))$msg1.='	value2='.$value2;
	if (!empty($value3))$msg1.='	value3='.$value3;
	for ($x=1;$x<=10;$x++) {
		$reply=file_get_contents('https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$iftttkey.'?value1='.$value1.'&value2='.$value2.'&value3='.$value3);
		if ($reply=="Congratulations! You've fired the $event event") {
			lg('ifttt '.$event.' executed '.$msg1.' ('.$msg.')');
			return true;
			break;
		}
		lg('ifttt '.$event.' '.$x.' failed '.$msg1.' ('.$msg.')');
		sleep($x);
	}
	return false;
}
function koekje($user,$expirytime)
{
    global $cookie,$domainname;
    setcookie($cookie, $user, $expirytime, '/', $domainname, true, true);
}
function telegram($msg,$silent=true,$to=1)
{
	if ($silent==true) $silent='true';
	else $silent='false';
    shell_exec('/var/www/html/secure/telegram.sh "'.$msg.'" "'.$silent.'" "'.$to.'" > /dev/null 2>/dev/null &');
    lg('Telegram sent: '.$msg);
}
function lg($msg)
{
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
function logwrite($msg,$msg2=null)
{
    global $log;
    if ($log==true) {
		$time=microtime(true);
		$dFormat="Y-m-d H:i:s";
		$mSecs=$time-floor($time);
		$mSecs=substr(number_format($mSecs, 3), 1);
		$fp=fopen('/temp/domoticz.log', "a+");
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
    $fp=fopen('/temp/home2ban.log', "a+");
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
function bosekey($key,$sleep=75000,$ip=101)
{
    $xml="<key state=\"press\" sender=\"Gabbo\">$key</key>";
    bosepost("key", $xml, $ip, true);
    $xml="<key state=\"release\" sender=\"Gabbo\">$key</key>";
    bosepost("key", $xml, $ip);
    if (startsWith($key,'PRESET')) {
    	$dontplayfirst=array(
			'Cygnux X'=>'Superstring - Rank 1 Remix',
			'Tiësto, Dzeko, Preme, Post Malone'=>'Jackie Chan',
			'Pharrell Williams'=>'Happy - From "Despicable Me 2"',
			'Christina Perri'=>'A Thousand Years',
			'Sam Smith'=>'Stay With Me'
		);
    	for ($x=1;$x<=10;$x++) {
			$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.".$ip.":8090/now_playing"))), true);
			if (!empty($nowplaying)) {
				bosekey('SHUFFLE_ON', 0, $ip);
				if (isset($nowplaying['@attributes']['source'])) {
					if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])&&isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
						if (array_key_exists(trim($nowplaying['artist']), $dontplayfirst)&&trim($nowplaying['track'])==$dontplayfirst[trim($nowplaying['artist'])]) {
							bosekey("NEXT_TRACK", $sleep, $ip);
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
        if ($vol>50) bosebass(0, $ip);
        elseif ($vol>40) bosebass(-1, $ip);
        elseif ($vol>30) bosebass(-2, $ip);
        elseif ($vol>20) bosebass(-3, $ip);
        elseif ($vol>10) bosebass(-4, $ip);
        else bosebass(-5, $ip);
    }
}
function bosebass($bass,$ip=101)
{
    $bass=1*$bass;
    $xml="<bass>$bass</bass>";
    bosepost("bass", $xml, $ip);
}
function bosepreset($pre,$ip=101)
{
    $pre=1*$pre;
    if ($pre<1||$pre>6) return;
    bosekey("PRESET_$pre", 0, $ip, true);
}
function bosezone($ip,$vol='')
{
    $d=fetchdata();
	/*if (TIME<strtotime('9:00')) $preset='PRESET_4';
	else*/if (TIME>strtotime('20:00')) $preset='PRESET_6';
	else  $preset='PRESET_2';
	//if ($d['bose105']['s']=='Off'/*&&$ip!=105*/) {
		if ($d['Weg']['s']<=1) {
			if ($d['Weg']['s']==0&&$d['lgtv']['s']=='Off'&&$d['bose101']['s']=='Off'&&TIME<strtotime('21:00')) {
				sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
				bosekey($preset, 0, 101);
				if ($d['lgtv']['s']=='On') bosevolume(0, 101);
				else bosevolume(21, 101);
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
				if ($d['bose'.$ip]['s']!='On') sw('bose'.$ip, 'On', basename(__FILE__).':'.__LINE__);

				    if ($ip==102) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.102">304511BC3CA5</member></zone>';
				elseif ($ip==103) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.103">C4F312F65070</member></zone>';
				elseif ($ip==104) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.104">C4F312DCE637</member></zone>';
				elseif ($ip==105) $xml='<zone master="587A6260C5B2" senderIPAddress="192.168.2.101"><member ipaddress="192.168.2.105">587A628BB5C0</member></zone>';

				if ($d['bose101']['s']=='Off'&&$d['bose'.$ip]['s']=='Off') {
					sw('bose101', 'On', basename(__FILE__).':'.__LINE__);
					bosekey($preset, 0, 101);
					if ($d['lgtv']['s']=='On') bosevolume(0, 101);
					else bosevolume(21, 101);

					bosepost('setZone', $xml, 101);
					if ($vol=='') {
						if (TIME>strtotime('6:00')&&TIME<strtotime('20:00')) bosevolume(30, $ip);
						else bosevolume(22, $ip);
					} else bosevolume($vol, $ip);
				} elseif ($d['bose'.$ip]['s']=='Off') {
					bosepost('setZone', $xml, 101);
					store('bose'.$ip, 'On');
					if ($vol=='') {
						if (TIME>strtotime('6:00')&&TIME<strtotime('21:00')) bosevolume(30, $ip);
						else bosevolume(20, $ip);
					} else bosevolume($vol, $ip);
				}
			}
		}
	/*} elseif ($ip==105&&$d['bose105']['s']=='Off') {//Bose buiten
		sw('bose105', 'On', basename(__FILE__).':'.__LINE__);
		bosekey($preset, 0, 105);
	}*/
}
function bosepost($method,$xml,$ip=101,$log=false)
{
    global $user;
    for($x=1;$x<=100;$x++) {
		$ch=curl_init("http://192.168.2.$ip:8090/$method");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		$response=curl_exec($ch);
		curl_close($ch);
		if ($log) {
			//lg($user.' >> Bose '.$ip.'	method='.$method.'	response='.$response);
		}
		//lg($user.' >> Bose '.$ip.'	method='.$method.'	response='.$response);
		if ($response=='<?xml version="1.0" encoding="UTF-8" ?><status>/'.$method.'</status>') break;
		usleep(100000);
	}
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
    if ($pos===false) return '';
    else return(substr($string, $pos+strlen($substring)));
}
function strbefore($string, $substring)
{
    $pos=strpos($string, $substring);
    if ($pos===false) return '';
    else return(substr($string, 0, $pos));
}
function fliving()
{
    global $d;
    if ($d['Weg']['s']==0&&$d['lgtv']['s']=='Off'&&$d['bureel']['s']=='Off'&&$d['eettafel']['s']==0) {
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
        if (TIME>=strtotime('5:30')&&TIME<strtotime('17:30')) {
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
    if (past('$ 8badkamer-8')>10) {
		if ($d['lichtbadkamer']['s']<16&&$d['zon']['s']==0) {
            if (TIME>strtotime('5:30')&&TIME<strtotime('21:30')) {
            	sl('lichtbadkamer', 16, basename(__FILE__).':'.__LINE__);
            } else {
            	sl('lichtbadkamer', 8, basename(__FILE__).':'.__LINE__);
            }
        }
        if (TIME>strtotime('5:30')&&TIME<strtotime('8:00')) {
        	if ($d['bose102']['s']=='Off'&&past('bose102')>30) {
				bosezone(102);
				if ($d['bose102']['m']==0) {
					sleep(2);
					boseplayinfo(saytime().sayweather());
					storemode('bose102', 1);
				}
			} 
        }
    }
}
function fkeuken()
{
    global $d;
    if (TIME<strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']==0||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        sw('keuken', 'On', basename(__FILE__).':'.__LINE__);
    } elseif (TIME>=strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']==0||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        if ($d['jbl']['s']=='Off') {
            sw('keuken', 'On', basename(__FILE__).':'.__LINE__);
        }
    }
}
function finkom()
{
    global $d;
    if ($d['Weg']['s']==0&&$d['inkom']['s']<31&&TIME>strtotime('6:00')&&TIME<=strtotime('21:00')&&$d['zon']['s']<50) {
        sl('inkom', 31, basename(__FILE__).':'.__LINE__);
    } elseif ($d['Weg']['s']==0&&$d['inkom']['s']<26&&$d['zon']['s']==0) {
        sl('inkom', 26, basename(__FILE__).':'.__LINE__);
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
			if ($d['hall']['s']<27) {
				sl('hall', 27, basename(__FILE__).':'.__LINE__);
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
    if (in_array($device, array('pirhall'))) {
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
    global $udevice,$ipaddress;
    echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<title>Floorplan</title>';
    if ($udevice=='iPhone') {
        echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">';
		if ($ipaddress=='192.168.2.37') /* Iphone11 */ echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.755,user-scalable=yes,minimal-ui">';
		else echo '
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui">';
    } elseif ($ipaddress=='192.168.2.199')  { //Nero
        echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.2,user-scalable=yes,minimal-ui">';
    } elseif ($ipaddress=='192.168.2.24')  { //Nokiatobi
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
/**
 * Function daikinstatus
 *
 * Returns the status of a Daikin airco
 *
 * @param string $device devicename of the Daikin airco
 *
 * @return array();
 */
function daikinstatus($device)
{
    if ($device=='living') $ip=111;
    elseif ($device=='kamer') $ip=112;
    elseif ($device=='alex') $ip=113;
    $ctx=stream_context_create(array('http'=>array('timeout' =>2)));
	$data = @file_get_contents("http://192.168.2.$ip/aircon/get_control_info", false, $ctx);
	if($data === FALSE){
		return FALSE;
	}else{
		$array=explode(",",$data);
		$control_info= array();
		foreach($array as $value){
			$pair= explode("=",$value);
			$control_info[$pair[0]]=$pair[1];
		}
		return json_encode($control_info);
	}
}
/**
 * Function daikinset
 *
 * Sets a Daikin airco in cooling mode to a temperature.
 *
 * @param string $device devicename of the Daikin airco
 * @param int $power 0 = Off, 1 = On
 * @param int $mode 0,1,7 = Auto, 2 = Dry, 3 = Cool, 4 = Heat, 6 = Fan only
 * @param float $temp Temperature of the setpoint
 * @param string $fan A = Auto, B = Silence, 3 = Level 1, 4 = Level 2, 5 = Level 3, 6 = Level 4, 7 = level 5
 * @param int $swing 0 = all wings stopped, 1 = vertical wings motion, 2 = horizontal wings motion, 3 = vertical and horizontal wings motion
 * @param int $hum 
 *
 * @return array();
 */
function daikinset($device, $power, $mode, $stemp,$msg='', $fan='A', $swing=0, $hum=0)
{
	if ($device=='living') $ip=111;
    elseif ($device=='kamer') $ip=112;
    elseif ($device=='alex') $ip=113;
    file_get_contents("http://192.168.2.$ip/aircon/set_control_info?pow=$power&mode=$mode&stemp=$stemp&f_rate=$fan&shum=$hum&f_dir=$swing");
    lg("Daikin $device pow=$power&mode=$mode&stemp=$stemp&f_rate=$fan&shum=$hum&f_dir=$swing ($msg)");
    sleep(1);
	store('daikin'.$device, daikinstatus($device));
	if ($power==0) {
		storemode('daikin'.$device, 0, basename(__FILE__).':'.__LINE__);
	} else {
		storemode('daikin'.$device, $mode, basename(__FILE__).':'.__LINE__);
	}

}