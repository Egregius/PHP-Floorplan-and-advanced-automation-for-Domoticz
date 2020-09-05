<?php
/**
 * Pass2PHP
 * php version 7.3
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
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home==true) {
	if (!isset($_REQUEST['t'])&&!isset($_REQUEST['bose'])&&!isset($_REQUEST['media'])&&!isset($_REQUEST['daikin'])) {
		$msg='';
		foreach($_REQUEST as $k=>$v) {
			$msg.='	'.$k.'='.$v;
			if (isset($diff)) {
				$msg.='	'.$diff;
			}
		}
		lg(' (AJAX)	'.$_SERVER['REMOTE_ADDR'].'	'.$udevice.'	'.$user.$msg);
    } 
    if (isset($_REQUEST['t'])) {
        $t=$_SERVER['REQUEST_TIME'];
        $d=array();
        $d['t']=$t;
        if($_REQUEST['t']==0)$t=0;
        else $t=$t-2;
        $db=dbconnect();
        $stmt=$db->query("SELECT n,i,s,t,m,dt,icon FROM devices WHERE t >= $t;");
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $d[$row['n']]['s']=$row['s'];
            $d[$row['n']]['t']=$row['t'];
            if(!empty($row['m']))$d[$row['n']]['m']=$row['m'];
            if(!empty($row['dt']))$d[$row['n']]['dt']=$row['dt'];
            if(!empty($row['icon']))$d[$row['n']]['ic']=$row['icon'];
        }
        echo json_encode($d);
        exit;
    } 
    
    elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='imac') {
    	if ($_REQUEST['command']=='wake') {
    		shell_exec('secure/wakeimac.sh');
    	}
    } 
    
    elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='denonset') {
    	if ($_REQUEST['command']=='volume') {
    		$vol=80-$_REQUEST['action'];
    		@file_get_contents('http://192.168.2.6/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.number_format($vol, 0).'.0');
    	}
    }
	
	elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='resetsecurity') {
		resetsecurity();
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
    	} if ($_REQUEST['command']=='sw') {
			if ($_REQUEST['action']=='On') {
				exec('python3 /var/www/html/secure/lgtv.py -c on -a '.$lgtvmac.' '.$lgtvip, $output, $return_var);
			} elseif ($_REQUEST['action']=='Off') {
				shell_exec('python3 secure/lgtv.py -c off '.$lgtvip);
			}
		}
    }
    
    elseif (isset($_REQUEST['bose'])) {
    	$bose=$_REQUEST['bose'];
		$d=array();
		$d['time']=$_SERVER['REQUEST_TIME'];
		$db=dbconnect();
        $stmt=$db->query("SELECT m FROM devices WHERE n like 'bose101';");
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            $d['bose101mode']=$row['m'];
        }
		$d['nowplaying']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/now_playing"))), true);
		$d['volume']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/volume"))), true);
		$d['bass']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/bass"))), true);
		echo json_encode($d);
		exit;
    } 
    
    elseif (isset($_REQUEST['media'])) {
    	$d=fetchdata();
    	$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
    	$data=array();
    	$data['pfsense']=json_decode(@file_get_contents('https://pfsense.home.egregius.be:44300/egregius.php', false, $ctx), true);
    	if ($d['denon']['s']=='On') {
			$denon=json_decode(json_encode(simplexml_load_string(@file_get_contents('http://'.$denonip.'/goform/formMainZone_MainZoneXml.xml?_='.time, false, $ctx))), true);
			$data['denon']['power']=$denon['Power']['value'];
			$data['denon']['vol']=$denon['MasterVolume']['value'];
		}
    	if ($d['lgtv']['s']=='On') {			
			$data['lgtv']=trim(shell_exec('python3 secure/lgtv.py -c get-input '.$lgtvip));
		}
    	echo json_encode($data);
    	exit;
    } 
    elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='saytime') {
    	$d=fetchdata();
		boseplayinfo(saytime().'Het wordt tussen '.floor($d['minmaxtemp']['s']).' en '.ceil($d['minmaxtemp']['m']).' graden'.owcondition(), basename(__FILE__).':'.__LINE__, 101, true);
    }
    elseif (isset($_REQUEST['device'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
        $d=fetchdata();
        //if ($user=='Tobi'&&$_REQUEST['device']!='poortrf') exit;
        if ($_REQUEST['command']=='setpoint') {
        	/*if ($_REQUEST['device']=='zolder'&&$user=='Tobi') {
        		if ($_REQUEST['action']>16) $_REQUEST['action']=16;
        	}*/
			store($_REQUEST['device'].'_set', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			storemode($_REQUEST['device'].'_set', 1, basename(__FILE__).':'.__LINE__);
//			$d[$_REQUEST['device'].'_set']['s']=$_REQUEST['action'];
//			$d[$_REQUEST['device'].'_set']['m']=1;
        } elseif ($_REQUEST['command']=='heating') {
        	store('heating', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
        } elseif ($_REQUEST['command']=='Weg') {
        	store('Weg', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			if ($_REQUEST['action']==0) {
				$db=dbconnect();
				$db->query("UPDATE devices set t='1' WHERE n='heating';");
				if ($d['Weg']['s']!=1&&$d['poortrf']['s']=='Off') {
					sw('poortrf', 'On',basename(__FILE__).':'.__LINE__);
				}
				resetsecurity();
			} elseif ($_REQUEST['action']==1) {
				huisslapen();
			} elseif ($_REQUEST['action']==2) {
				huisweg();
			}
        } elseif ($_REQUEST['command']=='dimmerwake') {
        	storemode($_REQUEST['device'], 2, basename(__FILE__).':'.__LINE__);
        } elseif ($_REQUEST['command']=='dimmersleep') {
        	if ($_SERVER['REQUEST_TIME']>=strtotime('6:00')&&$_SERVER['REQUEST_TIME']<strtotime('8:00')) {
        		if ($user=='Guy'&&$_REQUEST['device']=='eettafel') {
        			$d=fetchdata();
        			if ($d['eettafel']['m']==2) {
        				lg(basename(__FILE__).':'.__LINE__);
        				sl('eettafel', (1+$d['eettafel']['s']), basename(__FILE__).':'.__LINE__);
        			} else {
			        	storemode($_REQUEST['device'], 1, basename(__FILE__).':'.__LINE__);
        			}
        		} else {
		        	storemode($_REQUEST['device'], 1, basename(__FILE__).':'.__LINE__);
        		}
        	} else {
	        	storemode($_REQUEST['device'], 1, basename(__FILE__).':'.__LINE__);
	        }
		} elseif ($_REQUEST['command']=='dimmer') {
        	storemode($_REQUEST['device'], 0, basename(__FILE__).':'.__LINE__);
        	sl($_REQUEST['device'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
        	
		} elseif ($_REQUEST['command']=='roller') {
			if ($_REQUEST['device']=='Beneden') {
				foreach(array('Rliving', 'Rbureel', 'RkeukenL', 'RkeukenR') as $i) {
					if ($d[$i]['s']!=$_REQUEST['action']) sl($i, $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
				}
			} elseif ($_REQUEST['device']=='Boven') {
				foreach(array('RkamerL', 'RkamerR', 'Rtobi', 'Ralex') as $i) {
					if ($d[$i]['s']!=$_REQUEST['action']) sl($i, $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
				}
			} elseif ($_REQUEST['device']=='tv') {
				if ($d['Rliving']['s']<34) sl('Rliving', 34, basename(__FILE__).':'.__LINE__);
				if ($d['Rbureel']['s']<70) sl('Rbureel', 70, basename(__FILE__).':'.__LINE__);
				if ($d['RkeukenL']['s']<55) sl('RkeukenL', 55, basename(__FILE__).':'.__LINE__);
				if ($d['RkeukenR']['s']<55) sl('RkeukenR', 55, basename(__FILE__).':'.__LINE__);
			} else {
				sl($_REQUEST['device'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
				//storemode($_REQUEST['device'], 1, basename(__FILE__).':'.__LINE__);
				if($_REQUEST['device']=='luifel')storemode($_REQUEST['device'], 1, basename(__FILE__).':'.__LINE__);
			}
			
		} elseif ($_REQUEST['device']=='luifel'&&$_REQUEST['command']=='luifel') {
			storemode('luifel', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
		} elseif ($_REQUEST['command']=='mode') {
			storemode($_REQUEST['device'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
		} elseif ($_REQUEST['command']=='fetch') {
			include 'secure/_fetchdomoticz.php';
		} elseif ($_REQUEST['command']=='media') {
			if ($_REQUEST['action']=='On') {
				if ($d['tv']['s']!='On') {
					sw('tv', 'On',basename(__FILE__).':'.__LINE__);
				}
				if ($d['nvidia']['s']!='On') {
					sw('nvidia', 'On',basename(__FILE__).':'.__LINE__);
				}
				/*if ($d['denon']['s']!='On') {
					sw('denon', 'On',basename(__FILE__).':'.__LINE__);
				}*/
				/*sleep(4);
				lgcommand('on');
				for ($x=1;$x<=4;$x++) {
					lgcommand('on');
					sleep(2);
				}*/
				if ($d['bose101']['s']=='On') {
					sw('bose101', 'Off');
					bosekey("POWER");
					foreach (array('bose102', 'bose103', 'bose104', 'bose105') as $i) {
						if ($d[$i]['s']=='On') {
							sw($i, 'Off');
						}
					}
				}
			} elseif ($_REQUEST['action']=='Off') {
				if ($d['lgtv']['s']!='Off') {
					shell_exec('python3 secure/lgtv.py -c off '.$lgtvip);
					sleep(2);
				}
				if ($d['denon']['s']!='Off') {
					sw('denon', 'Off',basename(__FILE__).':'.__LINE__);
				}
				if ($d['nvidia']['s']!='Off') {
					sleep(10);
					sw('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		} elseif ($_REQUEST['command']=='water') {
			storemode('water', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
        	double('water', 'On');
		} elseif ($_REQUEST['command']=='sw'&&$_REQUEST['device']=='GroheRed') {
			if ($_REQUEST['action']=='On') {
				sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__);
				storemode('GroheRed', 1, basename(__FILE__).':'.__LINE__);
			} else {
				sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
				storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['command']=='powermode') {
			if ($_REQUEST['device']=='living_set') {$ip=111;$daikin='living';}
			elseif ($_REQUEST['device']=='kamer_set') {$ip=112;$daikin='kamer';}
			elseif ($_REQUEST['device']=='alex_set') {$ip=113;$daikin='alex';}
			$data=json_decode($d[$_REQUEST['device']]['icon'], true);
			$data['powermode']=$_REQUEST['action'];
			storeicon($_REQUEST['device'], json_encode($data));
			if ($_REQUEST['action']=='Normal') file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=0&spmode_kind=1');
			elseif ($_REQUEST['action']=='Eco') file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=1&spmode_kind=2');
			elseif ($_REQUEST['action']=='Power') file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=1&spmode_kind=1');
			sleep(1);
			$data=daikinstatus($daikin);
			if ($data&&$data!=$d['daikin'.$daikin]['s']) {
				store('daikin'.$daikin, $data, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['command']=='streamer') {
			if ($_REQUEST['device']=='living_set') {$ip=111;$daikin='living';}
			elseif ($_REQUEST['device']=='kamer_set') {$ip=112;$daikin='kamer';}
			elseif ($_REQUEST['device']=='alex_set') {$ip=113;$daikin='alex';}
			$data=json_decode($d[$_REQUEST['device']]['icon'], true);
			$data['streamer']=$_REQUEST['action'];
			storeicon($_REQUEST['device'], json_encode($data));
			if ($_REQUEST['action']=='On') file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?en_streamer=1');
			elseif ($_REQUEST['action']=='Off') file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?en_streamer=0');
			sleep(1);
			$data=daikinstatus($daikin);
			if ($data&&$data!=$d['daikin'.$daikin]['s']) {
				store('daikin'.$daikin, $data, basename(__FILE__).':'.__LINE__);
			}
		} else {
			if ($_REQUEST['device']=='nas') {
				shell_exec('secure/wakenas.sh');
			} else {
				call_user_func($_REQUEST['command'],$_REQUEST['device'],$_REQUEST['action'],basename(__FILE__).':'.__LINE__);
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
                    sw('bose'.$_REQUEST['boseip'], 'On',basename(__FILE__).':'.__LINE__);
                } elseif ($_REQUEST['action']=='Off') {
                    bosekey("POWER", 0, $_REQUEST['boseip']);
                    sw('bose'.$_REQUEST['boseip'], 'Off',basename(__FILE__).':'.__LINE__);
                    if ($_REQUEST['boseip']==101) {
                    	if ($d['bose102']['s']=='On') {
                    		sw('bose102', 'Off',basename(__FILE__).':'.__LINE__);
                    	}
                    	if ($d['bose103']['s']=='On') {
                    		sw('bose103', 'Off',basename(__FILE__).':'.__LINE__);
                    	}
                    	if ($d['bose104']['s']=='On') {
                    		sw('bose104', 'Off',basename(__FILE__).':'.__LINE__);
                    	}
                    	if ($d['bose105']['s']=='On') {
                    		sw('bose105', 'Off',basename(__FILE__).':'.__LINE__);
                    	}
                    }
                }
        } elseif ($_REQUEST['command']=='mode') {
    		storemode('bose'.$_REQUEST['boseip'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
        }
    }
    
    elseif (isset($_REQUEST['daikin'])) {
    	
    }
} else {
	echo json_encode('NOTAUTHENTICATED');
}
