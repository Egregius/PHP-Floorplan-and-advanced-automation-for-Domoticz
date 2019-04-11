<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '/var/www/config.php';
//define('TIME', $_SERVER['REQUEST_TIME']);
$db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt=$db->query("select n,i,s,t,m from devices;");
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
    $d[$row['n']] = $row;
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
    sw(array('slapen'), 'Off', true, 'huisslapen', 250000);
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
    $status=json_decode(json_encode(simplexml_load_string(file_get_contents('http://'.$boseipbuiten.':8090/now_playing'))), true);
    if (!empty($status)) {
        if (isset($status['@attributes']['source'])) {
            if ($status['@attributes']['source']!='STANDBY') {
                bosekey("POWER", 0, 5);
            }
        }
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
    $items=array('modeRtobi','modeRalex','modeRkamerL','modeRkamerR');
    foreach ($items as $i) {
        storemode($i, 1);
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
        telegram('Douche__Gas: '.$douchegas.'L = '.($douchegas*0.00065).'€__Water: '.$douchewater.'L = '.($douchewater*0.0055).'€__Som = '.(($douchegas*0.00065)+($douchewater*0.0065)).'€');
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
    $volume=json_decode(json_encode(simplexml_load_string(file_get_contents('http://'.$boseipbadkamer.':8090/volume'))), true);
    shell_exec('./boseplayinfo.sh "martian-gun" > /dev/null 2>/dev/null &');
    $cv=$volume['actualvolume'];
    if ($cv<$vol) {
        usleep(1550000);
        bosevolume($vol, 4);
        usleep(3500000);
        bosevolume($cv, 4);
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
function waarschuwing($msg)
{
    global $d;
    if ($d['Xvol']['s']!=30) {
        sl('Xvol', 30);
    }
    sl('Xring', 30);
    sw('deurbel', 'On');
    telegram($msg, false, 1);
    sleep(4);
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
function blinds($name)
{
    global $d;
    echo '
	<div class="fix z '.$name.'">
		<form method="POST">
			<input type="hidden" name="Schakel" value="'.$d[$name]['i'].'"/>
			<input type="hidden" name="Naam" value="'.$name.'"/>
			<input type="hidden" name="Actie" value="Off"/>';
    echo $d[$name]['s']=='Closed'
    ?'<input type="image" src="/images/arrowgreenup.png" class="i48"/>'
    :'<input type="image" src="/images/arrowup.png" class="i48"/>';
    echo '
		</form><br/>
	<form method="POST">
		<input type="hidden" name="Schakel" value="'.$d[$name]['i'].'"/>
		<input type="hidden" name="Naam" value="'.$name.'"/>
		<input type="hidden" name="Actie" value="On"/>';
    echo $d[$name]['s']=='Open'
    ?'<input type="image" src="/images/arrowgreendown.png" class="i48"/>'
    :'<input type="image" src="/images/arrowdown.png" class="i48"/>';
    echo '
	</form>
</div>';
}
function dimmer($name)
{
    global $page,$d;
    echo '
	<form method="POST">
		<a href="'.$page.'?setdimmer='.$name.'">
		<div class="fix z '.$name.'">
			<input type="hidden" name="setdimmer" value="'.$name.'"/>';
    if ($d[$name]['s']==0|$d[$name]['s']=='') {
        echo '
			<input type="image" src="/images/Light_Off.png" class="i70"/>';
    } else {
        echo'
			<input type="image" src="/images/Light_On.png" class="i70"/>
			<div class="fix center dimmerlevel">
				'.$d[$name]['s'].'
			</div>';
    }
    echo '
		</div>
		</a>
	</form>';
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
function schakelaar($name,$kind)
{
    global $d;
    echo '
	<div class="fix '.preg_replace('/\s/', '', $name).' z1">
		<form method="POST">
			<input type="hidden" name="Naam" value="'.$name.'"/>';
    echo $d[$name]['s']=='Off'?'
			<input type="hidden" name="Actie" value="On"/>
			<input type="image" src="/images/'.$kind.'_Off.png" id="'.$name.'"/>'
    :'
			<input type="hidden" name="Actie" value="Off">
			<input type="image" src="/images/'.$kind.'_On.png" id="'.$name.'"/>';
    echo '
		</form>
	</div>';
}
function schakelaar2($name,$kind)
{
    global $eendag,$d;
    echo '<div class="fix z1 center '.$name.'" style="width:70px;">
	<form method="POST"><input type="hidden" name="Schakel" value="'.$d[$name]['i'].'">';
    echo $d[$name]['s']=='Off'?'<input type="hidden" name="Actie" value="On">
        <input type="hidden" name="Naam" value="'.$name.'">
        <input type="image" src="/images/'.$kind.'_Off.png" class="i40"/>'
                   :'<input type="hidden" name="Actie" value="Off">
        <input type="hidden" name="Naam" value="'.$name.'">
        <input type="image" src="/images/'.$kind.'_On.png" class="i40"/>';
    echo '<br/>'.$name;
    if ($d[$name]['t']>$eendag) {
        echo '<div class="fix center" style="top:52px;left:0px;width:70px;">'.strftime("%H:%M", $d[$name]['t']).'</div>';
    }
    echo '</form></div>';
}
function sl($name,$level,$check=false)
{
    global $user,$d,$domoticzurl;
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
    global $d;
    $items=array('SDbadkamer','SDkamer','SDalex','SDtobi','SDzolder','SDliving');
    foreach ($items as $i) {
        if ($d[$i]['s']!='Off') {
            file_get_contents($domoticzurl.'/json.htm?type=command&param=resetsecuritystatus&idx='.$d[$i]['i'].'&switchcmd=Normal');
        }
    }
    if ($d['sirene']['s']!='Group Off') {
        sw('sirene', 'Off');
    }
}
function sw($name,$action='Toggle',$check=false,$msg='',$usleep=0)
{
    global $user,$d,$domoticzurl;
    if (is_array($name)) {
        $check=true;
        foreach ($name as $i) {
            if ($i=='media') {
                sw(array(/*'lgtv','denon',*/'tvled','kristal'/*,'nvidia'*/), $action, $check, $msg, $usleep);
            } elseif ($i=='lichtenbeneden') {
                sw(array('garage','garageled','pirgarage','pirkeuken','pirliving','pirinkom','eettafel','zithoek','media','bureel','jbl','terras','tuin','keuken','werkblad1','wasbak','kookplaat','inkom','zolderg','voordeur'), $action, $check, $msg, $usleep);
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
            if ($check==false) {
                lg($msg.' check=false');
                file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
            } else {
                if ($d[$name]['s']!=$action) {
                    lg($msg.' check=true');
                    file_get_contents($domoticzurl.'/json.htm?type=command&param=switchlight&idx='.$d[$name]['i'].'&switchcmd='.$action);
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
    global $db, $d;
    $time=TIME;
    if ($force==true) {
        if ($idx>0) {
            $db->query("INSERT INTO devices (n,i,s,t) VALUES ('$name','$idx','$status','$time') ON DUPLICATE KEY UPDATE s='$status',i='$idx',t='$time';");
        } else {
            $db->query("INSERT INTO devices (n,s,t) VALUES ('$name','$status','$time') ON DUPLICATE KEY UPDATE s='$status',t='$time';");
        }
    } else {
        if ($idx>0) {
            $db->query("INSERT INTO devices (n,i,s) VALUES ('$name','$idx','$status') ON DUPLICATE KEY UPDATE s='$status',i='$idx';");
        } else {
            $db->query("INSERT INTO devices (n,s) VALUES ('$name','$status') ON DUPLICATE KEY UPDATE s='$status';");
        }
    }
}
function storemode($name,$mode,$time=false)
{
    global $db, $username;
    if ($time==true) {
        $time=TIME;
        $db->query("INSERT INTO devices (n,m,t) VALUES ('$name','$mode','$time') ON DUPLICATE KEY UPDATE m='$mode',t='$time';");
    } else {
        $db->query("INSERT INTO devices (n,m) VALUES ('$name','$mode') ON DUPLICATE KEY UPDATE m='$mode';");
    }
    lgsql($username, $name.'_mode', $mode);
}
function alert($name,$msg,$ttl,$silent=true,$ios=false)
{
    global $db;
    if ($ios) {
        shell_exec('./ios.sh "'.$msg.'" > /dev/null 2>/dev/null &');
    }
    $time=TIME;
    $stmt=$db->query("SELECT t FROM alerts WHERE n='$name';");
    $last=$stmt->fetch(PDO::FETCH_ASSOC);
    if (isset($last['t'])) {
        $last=$last['t'];
    }
    if ($last < $time-$ttl) {
        telegram($msg, $silent);
        lg('alert='.$last);
        $db->query("INSERT INTO alerts (n,t) VALUES ('$name','$time') ON DUPLICATE KEY UPDATE t='$time';");
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
function thermometer($name)
{
    global $d;
    $temp=$d[$name]['s'];
    $hoogte=$temp*3;
    if ($hoogte>88) {
        $hoogte=88;
    } elseif ($hoogte<20) {
        $hoogte=20;
    }
    $top=88-$hoogte;
    if ($top<0) {
        $top=0;
    }
    $top=$top+5;
    if ($temp>=22) {
        $tcolor='F00';
        $dcolor='55F';
    } elseif ($temp>=20) {
        $tcolor='D12';
        $dcolor='44F';
    } elseif ($temp>=18) {
        $tcolor='B24';
        $dcolor='33F';
    } elseif ($temp>=15) {
        $tcolor='93B';
        $dcolor='22F';
    } elseif ($temp>=10) {
        $tcolor='64D';
        $dcolor='11F';
    } else {
        $tcolor='55F';
        $dcolor='00F';
    }
    echo '
	<a href=\'javascript:navigator_Go("temp.php?sensor=998");\'>
		<div class="fix '.$name.'" >
			<div class="fix tmpbg" style="top:'.number_format($top, 0).'px;left:8px;height:'.number_format($hoogte, 0).'px;background:linear-gradient(to bottom, #'.$tcolor.', #'.$dcolor.');">
			</div>
			<img src="/images/temp.png" height="100px" width="auto"/>
			<div class="fix center" style="top:73px;left:5px;width:30px;">
				'.number_format($temp, 1, ',', '').'
			</div>
		</div>
	</a>';
}
function thermostaat($name,$top,$left)
{
    global $d;
    $stat=$d[$name.'_set']['s'];
    $dif=$d[$name.'_temp']['s']-$stat;
    $mode=$d[$name.'_set']['m'];
    if ($dif>0.2) {
        $circle='hot';
    } elseif ($dif<0) {
        $circle='cold';
    } else {
        $circle='grey';
    }
    if ($stat>20.5) {
        $centre='red';
    } elseif ($stat>19) {
        $centre='orange';
    } elseif ($stat>13) {
        $centre='grey';
    } else {
        $centre='blue';
    }
    echo '
        <a href=\'javascript:navigator_Go("floorplan.heating.php?SetSetpoint='.$name.'");\'>
        <div class="fix z1" style="top:'.$top.'px;left:'.$left.'px;">
			<img src="/images/thermo'.$circle.$centre.'.png" class="i48"/>
		<div class="fix center" style="top:32px;left:11px;width:26px;">';
    if ($mode>0) {
        echo '
            <font size="2" color="#222">';
    } else {
        echo '
            <font size="2" color="#CCC">';
    }
    echo number_format($stat, 1, ',', '').'</font></div>';
    if ($mode>0) {
        echo '
            <div class="fix" style="top:2px;left:2px;z-index:-100;background:#b08000;width:44px;height:44px;border-radius:45px;">
            </div>';
    }
    echo '
        </div>
    </a>';
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
function showTimestamp($name,$draai)
{
    global $eendag,$d;
    if (past($name)<82800) {
        echo '
        <div class="fix stamp z1 r'.$draai.' t'.$name.'">
            '.strftime("%k:%M", $d[$name]['t']).'
        </div>';
    }
}
function secured($name)
{
    echo '
            <div class="fix secured '.$name.'">
            </div>';
}
function motion($name)
{
    echo '
            <div class="fix motion '.$name.'">
            </div>';
}
function zwavecancelaction()
{
    global $domoticzurl;
    file_get_contents(
        $domoticzurl.'/ozwcp/admpost.html',
        false,
        stream_context_create(
            array(
                'http'=>array(
                    'header'=>'Content-Type: application/x-www-form-urlencoded\r\n',
                    'method'=>'POST',
                    'content'=>http_build_query(
                        array(
                            'fun'=>'cancel'
                        )
                    ),
                ),
            )
        )
    );
}
function zwaveCommand($node,$command)
{
    global $domoticzurl;
    $cm=array(
        'AssignReturnRoute'=>'assrr',
        'DeleteAllReturnRoutes'=>'delarr',
        'NodeNeighbourUpdate'=>'reqnnu',
        'RefreshNodeInformation'=>'refreshnode',
        'RequestNetworkUpdate'=>'reqnu',
        'HasNodeFailed'=>'hnf',
        'Cancel'=>'cancel'
    );
    $cm=$cm[$command];
    for ($k=1;$k<=5;$k++) {
        $result=file_get_contents(
            $domoticzurl.'/ozwcp/admpost.html',
            false,
            stream_context_create(
                array(
                    'http'=>array(
                        'header'=>'Content-Type: application/x-www-form-urlencoded\r\n',
                        'method'=>'POST',
                        'content'=>http_build_query(
                            array(
                                'fun'=>$cm,
                                'node'=>'node'.$node
                                )
                        ),
                    ),
                )
            )
        );
        if ($result=='OK') {
            break;
        }
        sleep(1);
    }return $result;
}
function controllerBusy($retries)
{
    global $domoticzurl;
    for ($k=1;$k<=$retries;$k++) {
        $result=file_get_contents($domoticzurl.'/ozwcp/poll.xml');
        $p=xml_parser_create();
        xml_parse_into_struct($p, $result, $vals, $index);
        xml_parser_free($p);
        foreach ($vals as $val) {
            if ($val['tag']=='ADMIN') {
                $result=$val['attributes']['ACTIVE'];
                break;
            }
        }
        if ($result=='false') {
            break;
        }
        if ($k==$retries) {
            zwaveCommand(1, 'Cancel');
            break;
        }
        sleep(1);
    }
}
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
    setcookie($cookie, $user, $expirytime, '/');
}
function telegram($msg,$silent=true,$to=1)
{
    shell_exec('./telegram.sh "'.$msg.'" "'.$silent.'" "'.$to.'" > /dev/null 2>/dev/null &');
    lg(
        'Telegram sent:
'.$msg
    );
}
function luifel($name,$stat)
{
    echo '
	<form method="POST">
		<a href=\'javascript:navigator_Go("floorplan.heating.php?luifel='.$name.'");\'>
		<div class="fix z '.$name.'">
			<input type="hidden" name="luifel" value="'.$name.'"/>';
    if ($stat==100) {
        echo '<input type="image" src="/images/arrowgreenup.png" class="i60"/>';
    } elseif ($stat==0) {
        echo '<input type="image" src="/images/arrowgreendown.png" class="i60"/>';
    } else {
        echo'
			<input type="image" src="/images/arrowdown.png" class="i60"/>
			<div class="fix center dimmerlevel" style="position:absolute;top:10px;left:-2px;width:70px;letter-spacing:4;" onclick="location.href=\'floorplan.heating.php?luifel='.$name.'\';"><font size="5" color="#CCC">
				'. (100 - $stat) .'</font>
			</div>';
    }
    echo '
		</div>
		</a>
	</form>';
}
function rollers($name,$stat)
{
    global $d;
    echo '
	<form method="POST">
		<a href=\'javascript:navigator_Go("floorplan.heating.php?rollers='.$name.'");\'>
		<div class="fix z '.$name.'">
			<input type="hidden" name="rollers" value="'.$name.'"/>';
    if ($stat==100) {
        echo '<input type="image" src="/images/arrowgreendown.png" class="i60"/>';
    } elseif ($stat==0) {
        echo '<input type="image" src="/images/arrowgreenup.png" class="i60"/>';
    } else {
        echo'
				<input type="image" src="/images/circlegrey.png" class="i60"/>
				<div class="fix center dimmerlevel" style="position:absolute;top:17px;left:-2px;width:70px;letter-spacing:4;" onclick="location.href=\'floorplan.heating.php?rollers='.$name.'\';">';
        if ($d[$name]['m']==2) {
            echo '<font size="5" color="#F00">';
        } elseif ($d[$name]['m']==1) {
            echo '<font size="5" color="#222">';
        } else {
            echo '<font size="5" color="#CCC">';
        }
        echo $stat .'</font>
				</div>';
    }
    if ($d[$name]['m']==2) {
        echo '
        <div class="fix" style="top:2px;left:2px;z-index:-100;background:#fc8000;width:56px;height:56px;border-radius:45px;">
        </div>';
    } elseif ($d[$name]['m']==1) {
        echo '
        <div class="fix" style="top:2px;left:2px;z-index:-100;background:#fff7d8;width:56px;height:56px;border-radius:45px;">
        </div>';
    }
    echo '
		</div>
		</a>
	</form>';
}
function rollery($name,$stat,$top,$left,$size,$rotation)
{
    $stat=100-$stat;
    if ($stat<100) {
        $perc=($stat/100)*0.7;
    } else {
        $perc=1;
    }
    if ($rotation=='P') {
        if ($stat==0) {
            $nsize=0;
            $top=$top;
        } elseif ($stat>0) {
            $nsize=($size*$perc)+5;
            if ($nsize>$size) {
                $nsize=$size;
            }
            $top=$top+($size-$nsize);
        } else {
            $nsize=$size;
        }
        echo '
        <div class="fix yellow" style="top:'.
                $top.'px;left:'.
                $left.'px;width:7px;height:'.
                $nsize.'px;">
        </div>';
    } elseif ($rotation=='PL') {
        if ($stat==100) {
            $nsize=0;
            $top=$top;
        } elseif ($stat>0) {
            $nsize=($size*$perc)+5;
            if ($nsize>$size) {
                $nsize=$size;
            }
            $top=$top+($size-$nsize);
        } else {
            $nsize=$size;
        }
        echo '
        <div class="fix yellow" style="top:'.
            $top.'px;left:'.
            $left.'px;width:7px;height:'.
            $nsize.'px;">
        </div>';
    } elseif ($rotation=='L') {
        if ($stat==0) {
            $nsize=0;
        } elseif ($stat>0) {
            $nsize=($size*$perc)+5;
            if ($nsize>$size) {
                $nsize=$size;
            }
        } else {
            $nsize=$size;
        }
        echo '
        <div class="fix yellow" style="top:'.
            $top.'px;left:'.
            $left.'px;width:'.
            $nsize.'px;height:7px;">
        </div>';
    }
}
function lg($msg)
{
    $fp=fopen('/var/log/domoticz.log', "a+");
    $time=microtime(true);
    $dFormat="Y-m-d H:i:s";
    $mSecs=$time-floor($time);
    $mSecs=substr(number_format($mSecs, 3), 1);
    fwrite($fp, sprintf("%s%s %s\n", date($dFormat), $mSecs, $msg));
    fclose($fp);
}
function lgsql($user='',$device='',$status='',$info='')
{
    global $db;
    $db->query(
        "INSERT INTO log (user,device,status,info)
        VALUES ('$user','$device','$status','$info');"
    );
}
function logwrite($msg,$msg2=null)
{
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
function bosekey($key,$sleep=100000,$ip=3)
{
    $xml="<key state=\"press\" sender=\"Gabbo\">$key</key>";
    echo bosepost("key", $xml, $ip, true);
    $xml="<key state=\"release\" sender=\"Gabbo\">$key</key>";
    echo bosepost("key", $xml, $ip);
}
function bosevolume($vol,$ip=3)
{
    $vol=1*$vol;
    $xml="<volume>$vol</volume>";
    bosepost("volume", $xml, $ip, true);
    if ($ip==3) {
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
function bosebass($bass,$ip=201)
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
    echo $ip;
    bosekey("PRESET_$pre", 0, $ip, true);
}
function bosezone($func,$master,$ipslave,$macslave,$ip)
{
    $xml="<zone master=\"$master\">
	<member ipaddress=\"$ipslave\">$macslave</member>
</zone>";
    echo 'zone<br>';
    echo htmlentities($xml);
    echo bosepost($func, $xml, $ip, true);
}
function bosepost($method,$xml,$ip=3,$log=false)
{
    $ch=curl_init("http://192.168.2.$ip:8090/$method");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    $response=curl_exec($ch);
    curl_close($ch);
    if ($log) {
        lg('Bose '.$ip.' '.$method.' '.$xml);
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
