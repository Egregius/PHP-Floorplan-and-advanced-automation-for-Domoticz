<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * This file gives the status of the devices changed since $_REQUEST['t'] in json format.
 * It can also give statusses of different media devices in the house and controls the commands that need to be sent.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require '/var/www/config.php';
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home==true) {
    if (isset($_REQUEST['t'])) {
        $t=time();
        $d=array();
        $d['t']=$t;
        $diff=$t-$_REQUEST['t'];
		if ($diff>2&&$diff<50000)lg('----------- AJAX ----------- '.$diff);
        $t=$_REQUEST['t'];
        $db=new PDO("mysql:host=localhost;dbname=domotica;", 'domotica', 'domotica');
        $stmt=$db->query("SELECT n,i,s,t,m,dt,icon FROM devices WHERE t >= $t;");
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $d[$row['n']]['s']=$row['s'];
            $d[$row['n']]['t']=$row['t'];
            //if(!empty($row['i']))$d[$row['n']]['i']=$row['i'];
            if(!empty($row['m']))$d[$row['n']]['m']=$row['m'];
            if(!empty($row['dt']))$d[$row['n']]['dt']=$row['dt'];
            if(!empty($row['icon']))$d[$row['n']]['ic']=$row['icon'];
        }
        echo json_encode($d);
    } 
    
    elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='denonset') {
    	if ($_REQUEST['command']=='volume') {
    		$vol=80-$_REQUEST['action'];
    		@file_get_contents('http://192.168.2.6/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.number_format($vol, 0).'.0');
    	}
    }

    elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='lgtv') {
    	if ($_REQUEST['command']=='input') {
    		if($_REQUEST['action']=='Netflix') {
				shell_exec('python3 secure/lgtv.py -c app -a netflix '.$lgtvip);
			} elseif ($_REQUEST['action']=='YouTube') {
				shell_exec('python3 secure/lgtv.py -c app -a youtube.leanback.v4 '.$lgtvip);
			} elseif ($_REQUEST['action']=='HDMI 2') {
				shell_exec('python3 secure/lgtv.py -c set-input -a com.webos.app.hdmi2 '.$lgtvip);
			}
    	} elseif ($_REQUEST['command']=='play') {
			shell_exec('python3 secure/lgtv.py -c play '.$lgtvip);
    	} elseif ($_REQUEST['command']=='pause') {
			shell_exec('python3 secure/lgtv.py -c pause '.$lgtvip);
    	}
    }
    
    elseif (isset($_REQUEST['bose'])) {
    	$bose=$_REQUEST['bose'];
		$d=array();
		$d['time']=time();
		$d['nowplaying']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/now_playing"))), true);
		$d['volume']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/volume"))), true);
		$d['bass']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/bass"))), true);
		echo json_encode($d);
    } 
    
    elseif (isset($_REQUEST['media'])) {
    	$d=fetchdata();
    	$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
    	$data=array();
    	$data['pfsense']=json_decode(@file_get_contents('http://192.168.2.254:44300/egregius.php'), true);
    	if ($d['denon']['s']=='On') {
			$denon=json_decode(json_encode(simplexml_load_string(@file_get_contents('http://'.$denonip.'/goform/formMainZone_MainZoneXml.xml?_='.time, false, $ctx))), true);
			$data['denon']['power']=$denon['Power']['value'];
			$data['denon']['vol']=$denon['MasterVolume']['value'];
		}
    	if ($d['lgtv']['s']=='On') {			
			$data['lgtv']=trim(shell_exec('python3 secure/lgtv.py -c get-input '.$lgtvip));
		}
    	echo json_encode($data);
    } 
    
    elseif (isset($_REQUEST['device'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
        $d=fetchdata();
        if ($_REQUEST['command']=='setpoint') {
        	store($_REQUEST['device'].'_set', $_REQUEST['action']);
			storemode($_REQUEST['device'].'_set', 1);
			$d[$_REQUEST['device'].'_set']['s']=$_REQUEST['action'];
			$d[$_REQUEST['device'].'_set']['m']=1;
			include 'secure/_verwarming.php';
        } elseif ($_REQUEST['command']=='heating') {
        	store('heating', $_REQUEST['action']);
        } elseif ($_REQUEST['command']=='Weg') {
        	store('Weg', $_REQUEST['action']);
			if ($_REQUEST['action']==0) {
				$db->query("UPDATE devices set t='1' WHERE n='heating';");
				if ($d['Weg']['s']!=1&&$d['poortrf']['s']=='Off') {
					sw('poortrf', 'On');
				}
				resetsecurity();
			} elseif ($_REQUEST['action']==1) {
				huisslapen();
			} elseif ($_REQUEST['action']==2) {
				huisweg();
			}
        } elseif ($_REQUEST['command']=='dimmerwake') {
        	storemode($_REQUEST['device'], 2);
        } elseif ($_REQUEST['command']=='dimmersleep') {
        	storemode($_REQUEST['device'], 1);
		} elseif ($_REQUEST['command']=='dimmer') {
        	storemode($_REQUEST['device'], 0);
        	sl($_REQUEST['device'], $_REQUEST['action']);
		} elseif ($_REQUEST['command']=='roller') {
        	sl($_REQUEST['device'], $_REQUEST['action']);
        	storemode($_REQUEST['device'], 1);
		} elseif ($_REQUEST['command']=='mode') {
			storemode($_REQUEST['device'], $_REQUEST['action']);
		} elseif ($_REQUEST['command']=='fetch') {
			include 'secure/_fetchdomoticz.php';
		} elseif ($_REQUEST['command']=='media') {
			if ($_REQUEST['action']=='On') {
				if ($d['denon']['s']!='On') {
					sw('denon', 'On');
				}
				if ($d['tv']['s']!='On') {
					sw('tv', 'On');
				}
				sleep(4);
				lgcommand('on');
				for ($x=1;$x<=4;$x++) {
					lgcommand('on');
					sleep(2);
				}
			} elseif ($_REQUEST['action']=='Off') {
				if ($d['lgtv']['s']!='Off') {
					shell_exec('python3 secure/lgtv.py -c off '.$lgtvip);
					sleep(2);
				}
				if ($d['denon']['s']!='Off') {
					sw('denon', 'Off');
				}				
			}
		} elseif ($_REQUEST['command']=='water') {
			storemode('water', $_REQUEST['action']);
        	double('water', 'On');
		} else {
			if ($_REQUEST['device']=='nas') {
				shell_exec('secure/wakenas.sh');
			} else {
				call_user_func($_REQUEST['command'],$_REQUEST['device'],$_REQUEST['action']);
			}			
		}
    } 
    
    elseif (isset($_REQUEST['boseip'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
        if ($_REQUEST['command']=='volume') {
            bosevolume($_REQUEST['action'], $_REQUEST['boseip']);
        } elseif ($_REQUEST['command']=='bass') {
            bosebass($_REQUEST['action'], $_REQUEST['boseip']);
        } elseif ($_REQUEST['command']=='preset') {
            bosepreset($_REQUEST['action'], $_REQUEST['boseip']);
        } elseif ($_REQUEST['command']=='skip') {
            if ($_REQUEST['action']=='prev') {
                bosekey("PREV_TRACK", 0, $_REQUEST['boseip']);
            } elseif ($_REQUEST['action']=='next') {
                bosekey("NEXT_TRACK", 0, $_REQUEST['boseip']);
            }
        } elseif ($_REQUEST['command']=='power') {
                if ($_REQUEST['action']=='On') {
                    bosezone($_REQUEST['boseip']);
                    sw('bose'.$_REQUEST['boseip'], 'On');
                } elseif ($_REQUEST['action']=='Off') {
                    bosekey("POWER", 0, $_REQUEST['boseip']);
                    sw('bose'.$_REQUEST['boseip'], 'Off');
                }
        }
    }
} else echo json_encode('NOTAUTHENTICATED');
if (count($_REQUEST)>1) {
	lg('ajax '.$ipaddress.' '.$udevice.' '.$user.' '.print_r($_REQUEST, true));
} else {
	lg('ajax '.$ipaddress.' '.$udevice.' '.$user.' t='.$t);
}