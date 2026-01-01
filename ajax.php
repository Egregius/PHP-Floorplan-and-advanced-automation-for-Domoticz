<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
session_write_close();
if (isset($_REQUEST['device'])&&$_REQUEST['device']=='runsync'&&$_REQUEST['command']=='runsync') exec('curl -s http://192.168.2.20/secure/runsync.php?sync='.$_REQUEST['action'].' &');
elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='eufy'&&$_REQUEST['command']=='eufy') {
	lg('Starting Eufy stream');
	shell_exec('/var/www/html/secure/eufystartstream.php > /dev/null 2>/dev/null &');
	$d=array();
	$db = Database::getInstance();
	$stmt=$db->query("SELECT n,s FROM devices WHERE n IN ('voordeur','dag');");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$d[$row['n']]['s']=$row['s'];
	}
	if ($d['dag']['s']<-5) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
}
elseif (isset($_REQUEST['device'])&&($_REQUEST['device']=='MQTT'||$_REQUEST['device']=='CRON')) {
	$db = Database::getInstance();
	$time=time();
	$db->query("UPDATE devices SET m=3,t=$time WHERE n ='weg';");
}
elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='resetsecurity') resetsecurity();
elseif (isset($_REQUEST['bose'])&&$_REQUEST['bose']>=101&&$_REQUEST['bose']<=107) {
	$bose=$_REQUEST['bose'];
	$d=array();
	$d['time']=$_SERVER['REQUEST_TIME'];
	$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/now_playing"))), true);
	$volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/volume"))), true);
	$d['source']=$nowplaying['@attributes']['source'];
	if (isset($nowplaying['ContentItem']['itemName'])) {
		$d['artist']=$nowplaying['artist'];
		$d['track']=$nowplaying['track'];
		$d['art']=$nowplaying['art'];
		$d['playlist']=$nowplaying['ContentItem']['itemName'];
	} else {
		$d['artist']='';
		$d['track']='';
		$d['art']='';
		$d['playlist']='';
	}
	$d['volume']=$volume['actualvolume'];
	$d['playlisttoday']=boseplaylisttoday();
	echo json_encode($d);
	exit;
}
elseif (isset($_REQUEST['media'])) {
	$ctx=stream_context_create(array('http'=>array('timeout'=>2)));
	$data=array();
	$data['pfsense']=json_decode(@file_get_contents('https://pfsense.egregius.be:44300/egregius.php', false, $ctx), true);
	echo json_encode($data);
	exit;
}
elseif (isset($_REQUEST['device'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
	$d=fetchdata();
	if ($_REQUEST['command']=='setpoint') {
		if ($_REQUEST['device']=='badkamer') {
			
			$s=date('s');
			$dow=date("w");
			if($dow==0||$dow==6) $t=strtotime('7:30');
			elseif($dow==2||$dow==5) $t=strtotime('6:45');
			else $t=strtotime('7:00');
			if (TIME<$t+900||TIME>strtotime('12:00')||$user=='Guy') {
				if ($d['badkamervuur1']['s']=='Off'&&$d['badkamer_temp']['s']<$_REQUEST['action']) sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
				setpoint($_REQUEST['device'].'_set', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
				storemode($_REQUEST['device'].'_set', 1, basename(__FILE__).':'.__LINE__);
			}
		} else {
			setpoint($_REQUEST['device'].'_set', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			if ($_REQUEST['device']=='living') {
				if ($d['heating']['s']==-2) {//airco cooling
					if ($d['daikin']['s']=='Off'&&$_REQUEST['action']!='D'&&$d['living_temp']['s']>$_REQUEST['action']) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']['s']==-1) {//passive cooling
				} elseif ($d['heating']['s']==0) {// Neutral
				} elseif ($d['heating']['s']==1) {//heating airco
					if ($d['daikin']['s']=='Off'&&$d['living_temp']['s']<$_REQUEST['action']) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']['s']==2) {//heating gas airco
//					if ($d['daikin']['s']=='Off'&&$d['living_temp']['s']<$_REQUEST['action']) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']['s']==3) {//heating gas
					if ($d['brander']['s']=='Off'&&$d['living_temp']['s']<$_REQUEST['action']) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
				}
				if ($d['daikin']['s']=='Off'&&$_REQUEST['action']=='D') sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				if ($d['living_start_temp']['m']!=0) storemode('living_start_temp',0);
			}
			setpoint($_REQUEST['device'].'_set', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			storemode($_REQUEST['device'].'_set', 1, basename(__FILE__).':'.__LINE__);
		}
	} elseif ($_REQUEST['command']=='heating') {
		store('heating', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
	} elseif ($_REQUEST['command']=='verlof') {
		store('verlof', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
	} elseif ($_REQUEST['command']=='weg') {
		store('weg', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
		if ($_REQUEST['action']==0) {
			huisthuis();
		} elseif ($_REQUEST['action']==1) {
			lg('huisslapen...');
			huisslapen();
		} elseif ($_REQUEST['action']>=2) {
			lg('huisslapen...');
			huisslapen(3);
		}
	} elseif ($_REQUEST['command']=='dimmer') {
		sl($_REQUEST['device'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__, true);
	} elseif ($_REQUEST['command']=='roller') {
		if ($_REQUEST['device']=='Beneden') {
			foreach(array('rliving', 'rbureel', 'rkeukenl', 'rkeukenr') as $i) {
				if ($d[$i]['s']!=$_REQUEST['action']) sl($i, $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='rkeukenl') {
			foreach(array('rkeukenl', 'rkeukenr') as $i) {
				if ($d[$i]['s']!=$_REQUEST['action']) sl($i, $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='Boven') {
			foreach(array('rkamerl', 'rkamerr', 'rwaskamer', 'ralex') as $i) {
				if ($d[$i]['s']!=$_REQUEST['action']) sl($i, $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='tv') {
			if ($d['rliving']['s']<30) sl('rliving', 30, basename(__FILE__).':'.__LINE__);
			if ($d['rbureel']['s']<70) sl('rbureel', 69, basename(__FILE__).':'.__LINE__);
			if ($d['rkeukenl']['s']<55) sl('rkeukenl', 55, basename(__FILE__).':'.__LINE__);
			if ($d['rkeukenr']['s']<55) sl('rkeukenr', 55, basename(__FILE__).':'.__LINE__);
		} elseif ($_REQUEST['device']=='rkamerl') {
			sl('rkamerl', $_REQUEST['action'], basename(__FILE__).':'.__LINE__, true);
			sl('rkamerr', $_REQUEST['action'], basename(__FILE__).':'.__LINE__, true);
		} else {
			sl($_REQUEST['device'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__, true);
			if($_REQUEST['device']=='luifel') {
				store($_REQUEST['device'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
				storemode($_REQUEST['device'], 1, basename(__FILE__).':'.__LINE__);
			}
		}
	} elseif ($_REQUEST['device']=='luifel'&&$_REQUEST['command']=='luifel') {
		storemode('luifel', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
	} elseif ($_REQUEST['command']=='mode') {
		storemode($_REQUEST['device'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
	} elseif ($_REQUEST['command']=='water') {
		storemode('water', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
		sw('water', 'On');
	} elseif ($_REQUEST['command']=='powermode') {
		if ($_REQUEST['device']=='living_set') {$ip=111;$daikin='living';}
		elseif ($_REQUEST['device']=='kamer_set') {$ip=112;$daikin='kamer';}
		elseif ($_REQUEST['device']=='alex_set') {$ip=113;$daikin='alex';}
		$data=json_decode($d[$_REQUEST['device']]['icon'], true);
		$data['powermode']=$_REQUEST['action'];
		storeicon($_REQUEST['device'], json_encode($data));
		if ($_REQUEST['action']=='Normal') {
			file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=0&spmode_kind=1');
			if ($d['buiten_temp']['s']>2&&$d['buiten_temp']['s']<30) {
				$low=40;
			} elseif ($d['buiten_temp']['s']< -5||$d['buiten_temp']['s']>35) {
				$low=60;
			} else {
				$low=50;
			}
			sleep(1);
			file_get_contents('http://192.168.2.'.$ip.'/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$low.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
		} elseif ($_REQUEST['action']=='Eco') {
			file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=1&spmode_kind=2');
			if ($d['buiten_temp']['s']>2&&$d['buiten_temp']['s']<30) {
				$low=40;
			} elseif ($d['buiten_temp']['s']< -5||$d['buiten_temp']['s']>35) {
				$low=60;
			} else {
				$low=50;
			}
			sleep(1);
			file_get_contents('http://192.168.2.'.$ip.'/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow='.$low.'&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
		} elseif ($_REQUEST['action']=='Power') {
			file_get_contents('http://192.168.2.'.$ip.'/aircon/set_special_mode?set_spmode=1&spmode_kind=1');
			sleep(1);
			file_get_contents('http://192.168.2.'.$ip.'/aircon/set_demand_control?type=1&en_demand=1&mode=2&max_pow=100&scdl_per_day=4&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0');
		}
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
			if ($_REQUEST['action']=='On') {
				lg('Wake NAS');
				shell_exec('secure/wakenas.sh');
			} else {
				lg('Sleep NAS');
				shell_exec('/var/www/sleepnas.sh');
			}
		} elseif ($_REQUEST['device']=='powermeter') {
			if ($_REQUEST['action']>0) {
				setCache('powermeter',time());
				sleep(1);
				if (mget('avg')<$_REQUEST['action']) sw('powermeter', 'On', basename(__FILE__).':'.__LINE__);
				storemode('powermeter', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			} else {
				sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
				storemode('powermeter', 0, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='sirene') {
			sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
			store('sirene','Off');
		} elseif ($_REQUEST['device']=='grohered') {
			sw('grohered', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
		} else {
			if (str_ends_with($_REQUEST['device'], '_set')) call_user_func($_REQUEST['command'], $_REQUEST['device'],$_REQUEST['action']);
			else call_user_func($_REQUEST['command'],$_REQUEST['device'],$_REQUEST['action']);
		}
	}
}
elseif (isset($_REQUEST['boseip'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
	if ($_REQUEST['command']=='volume') {
		bosevolume($_REQUEST['action'], $_REQUEST['boseip'], basename(__FILE__).':'.__LINE__);
	} elseif ($_REQUEST['command']=='bass') {
		bosebass($_REQUEST['action'], $_REQUEST['boseip']);
	} elseif ($_REQUEST['command']=='preset') {
		bosepreset('PRESET_'.$_REQUEST['action'], $_REQUEST['boseip']);
	} elseif ($_REQUEST['command']=='skip') {
		$db = Database::getInstance();
		$stmt=$db->query("SELECT s FROM devices WHERE n like 'bose101';");
		while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$bose=$row['s'];
		}
		if ($bose=='On') $_REQUEST['boseip']=101;
		if ($_REQUEST['action']=='prev') {
			bosekey("PREV_TRACK", 0, $_REQUEST['boseip']);
		} elseif ($_REQUEST['action']=='next') {
			bosekey("NEXT_TRACK", 0, $_REQUEST['boseip']);
		}
	} elseif ($_REQUEST['command']=='power') {
		$d=fetchdata(0);
		if ($_REQUEST['action']=='On') {
			bosezone($_REQUEST['boseip']);
		} elseif ($_REQUEST['action']=='Off') {
			bosekey("POWER", 0, $_REQUEST['boseip']);
			sw('bose'.$_REQUEST['boseip'], 'Off');
			if ($_REQUEST['boseip']==101) {
				if ($d['bose102']['s']=='On') {
					sw('bose102', 'Off');
				}
				if ($d['bose103']['s']=='On') {
					sw('bose103', 'Off');
				}
				if ($d['bose104']['s']=='On') {
					sw('bose104', 'Off');
				}
				if ($d['bose105']['s']=='On') {
					sw('bose105', 'Off');
				}
			}
		}
	} elseif ($_REQUEST['command']=='mode') {
		storeicon('bose'.$_REQUEST['boseip'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
	}
}
if (!isset($_REQUEST['t'])&&!isset($_REQUEST['q'])&&!isset($_REQUEST['bose'])&&!isset($_REQUEST['media'])&&!isset($_REQUEST['daikin'])) {
	$msg='';
	foreach($_REQUEST as $k=>$v) {
		$msg.='	'.$k.'	'.$v;
		if (isset($diff)) {
			$msg.='	'.$diff;
		}
	}
	lg('👉🏻 '.$user.$msg);
}
function boseplaylisttoday() {
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
/*	$map = [
		'EDM-1' => 'PRESET_1',
		'EDM-2' => 'PRESET_2',
		'EDM-3' => 'PRESET_3',
		'MIX-1' => 'PRESET_4',
		'MIX-2' => 'PRESET_5',
		'MIX-3' => 'PRESET_6',
	];
	return $map[$preset];*/
	return $preset;
}