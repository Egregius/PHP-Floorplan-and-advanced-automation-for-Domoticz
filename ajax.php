<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
session_write_close();
if (!isset($_REQUEST['t'])&&!isset($_REQUEST['q'])&&!isset($_REQUEST['bose'])&&!isset($_REQUEST['media'])&&!isset($_REQUEST['daikin'])) {
	$msg='';
	foreach($_REQUEST as $k=>$v) {
		$msg.='	'.$k.'='.$v;
		if (isset($diff)) {
			$msg.='	'.$diff;
		}
	}
	lg('(AJAX)	'.$user.$msg);
}
if (isset($_REQUEST['t'])) {
	if ($_REQUEST['t']=='undefined'||$_REQUEST['t']==0) $t=0;
	else $t=$_SERVER['REQUEST_TIME']-1;
	$d=array();
	$d['t']=$_SERVER['REQUEST_TIME_FLOAT'];
	$db=dbconnect();
	$stmt=$db->query("SELECT n,s,t,m,dt,icon,ajax FROM devices WHERE ajax>=1 AND t >= $t;");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$d[$row['n']]['s']=$row['s'];
		if($row['ajax']==2)$d[$row['n']]['t']=$row['t'];
		if(!empty($row['m']))$d[$row['n']]['m']=$row['m'];
		if(!empty($row['dt']))$d[$row['n']]['dt']=$row['dt'];
		if(!empty($row['icon']))$d[$row['n']]['icon']=$row['icon'];
	}
	$en=mget('en');
	$d['net']=$en['net'];
	$d['avg']=$en['avg'];
	$d['zon']=-$en['zon'];
	if ($_REQUEST['t']=='undefined'||$_REQUEST['t']==0) {
		$sunrise=mget('sunrise');
		$d['CivTwilightStart']=$sunrise['CivTwilightStart'];
		$d['Sunrise']=$sunrise['Sunrise'];
		$d['Sunset']=$sunrise['Sunset'];
		$d['CivTwilightEnd']=$sunrise['CivTwilightEnd'];
		$d['zonavg']=$d['zonvandaag']['icon'];
	}
	echo json_encode($d);
	exit;
}
elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='runsync'&&$_REQUEST['command']=='runsync') exec('curl -s http://192.168.2.20/secure/runsync.php?sync='.$_REQUEST['action'].' &');
elseif (isset($_REQUEST['device'])&&($_REQUEST['device']=='MQTT'||$_REQUEST['device']=='CRON')) {
	$db=dbconnect();
	$time=time();
	$db->query("UPDATE devices SET m=3,t=$time WHERE n ='Weg';");
}
elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='resetsecurity') resetsecurity();
elseif (isset($_REQUEST['bose'])) {
	$bose=$_REQUEST['bose'];
	$d=array();
	$d['time']=$_SERVER['REQUEST_TIME'];
	$db=dbconnect();
	$stmt=$db->query("SELECT m FROM devices WHERE n like 'bose101';");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$d['bose101mode']=$row['m'];
	}
	$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/now_playing"))), true);
	if (isset($nowplaying['isFavorite'])) $nowplaying['isFavorite']=1; else $nowplaying['isFavorite']=0;
	$d['nowplaying']=$nowplaying;
	$d['volume']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/volume"))), true);
	$d['bass']=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/bass"))), true);
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
	if ($_REQUEST['command']=='setpoint') {
		if ($_REQUEST['device']=='badkamer') {
			$d=fetchdata();
			$s=date('s');
			$dow=date("w");
			if($dow==0||$dow==6) $t=strtotime('7:30');
			elseif($dow==2||$dow==5) $t=strtotime('6:45');
			else $t=strtotime('7:00');
			if (TIME<$t+900||TIME>strtotime('12:00')||$user=='Guy') {
				if ($d['waskamervuur1']['s']=='Off'&&$d['badkamer_temp']['s']<$_REQUEST['action']) sw('waskamervuur1', 'On', basename(__FILE__).':'.__LINE__);
				setpoint($_REQUEST['device'].'_set', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
				storemode($_REQUEST['device'].'_set', 1, basename(__FILE__).':'.__LINE__);
			}
		} else {
			setpoint($_REQUEST['device'].'_set', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			if ($_REQUEST['device']=='living') {
				$d=fetchdata();
				if ($d['heating']['s']==-2) {//airco cooling
					if ($d['daikin']['s']=='Off'&&$_REQUEST['action']!='D'&&$d['living_temp']['s']>$_REQUEST['action']) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']['s']==-1) {//passive cooling
				} elseif ($d['heating']['s']==0) {// Neutral
				} elseif ($d['heating']['s']==1) {//heating airco
					if ($d['daikin']['s']=='Off'&&$d['living_temp']['s']<$_REQUEST['action']) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']['s']==2) {//heating airco gas
					if ($d['daikin']['s']=='Off'&&$d['living_temp']['s']<$_REQUEST['action']) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']['s']==3) {//heating gas airco
					if ($d['brander']['s']=='Off'&&$d['living_temp']['s']<$_REQUEST['action']) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']['s']==4) {//heating gas
					if ($d['brander']['s']=='Off'&&$d['living_temp']['s']<$_REQUEST['action']) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
					elseif ($d['brander']['s']=='On'&&$d['living_temp']['s']>$_REQUEST['action']) sw('brander', 'Off', basename(__FILE__).':'.__LINE__);
				}
				if ($d['daikin']['s']=='Off'&&$_REQUEST['action']=='D') sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
			}
			storemode($_REQUEST['device'].'_set', 1, basename(__FILE__).':'.__LINE__);
		}
	} elseif ($_REQUEST['command']=='setpoint2') {
		$s=date('s');
		$dow=date("w");
		if($dow==0||$dow==6) $t=strtotime('7:30');
		elseif($dow==2||$dow==5) $t=strtotime('6:45');
		else $t=strtotime('7:00');
		if (TIME<$t+900||TIME>strtotime('12:00')||$user=='Guy') {
			if ($d['waskamervuur1']['s']=='Off'&&$d['badkamer_temp']['s']<$_REQUEST['action']) {
				sw('waskamervuur1', 'On', basename(__FILE__).':'.__LINE__);
				if ($d['waskamervuur2']['s']=='Off'&&$d['badkamer_temp']['s']<$_REQUEST['action']-0.3) {
					sleep(2);
					sw('waskamervuur2', 'On', basename(__FILE__).':'.__LINE__);
				}
			}
			setpoint($_REQUEST['device'].'_set', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			storemode($_REQUEST['device'].'_set', 2, basename(__FILE__).':'.__LINE__);
		}
	} elseif ($_REQUEST['command']=='heating') {
		store('heating', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
	} elseif ($_REQUEST['command']=='Weg') {
		store('Weg', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
		if ($_REQUEST['action']==0) {
			huisthuis();
		} elseif ($_REQUEST['action']==1) {
			lg('huisslapen...');
			huisslapen();
		} elseif ($_REQUEST['action']>=2) {
			lg('huisslapen...');
			huisslapen(true);
		}
	} elseif ($_REQUEST['command']=='dimmer') {
		sl($_REQUEST['device'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__, true);
	} elseif ($_REQUEST['command']=='roller') {
		if ($_REQUEST['device']=='Beneden') {
			$d=fetchdata();
			foreach(array('Rliving', 'Rbureel', 'RkeukenL', 'RkeukenR') as $i) {
				if ($d[$i]['s']!=$_REQUEST['action']) sl($i, $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='RkeukenL') {
			$d=fetchdata();
			foreach(array('RkeukenL', 'RkeukenR') as $i) {
				if ($d[$i]['s']!=$_REQUEST['action']) sl($i, $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='Boven') {
			$d=fetchdata();
			foreach(array('RkamerL', 'RkamerR', 'Rwaskamer', 'Ralex') as $i) {
				if ($d[$i]['s']!=$_REQUEST['action']) sl($i, $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='tv') {
			$d=fetchdata();
			if ($d['Rliving']['s']<30) sl('Rliving', 30, basename(__FILE__).':'.__LINE__);
			if ($d['Rbureel']['s']<70) sl('Rbureel', 69, basename(__FILE__).':'.__LINE__);
			if ($d['RkeukenL']['s']<55) sl('RkeukenL', 55, basename(__FILE__).':'.__LINE__);
			if ($d['RkeukenR']['s']<55) sl('RkeukenR', 55, basename(__FILE__).':'.__LINE__);
		} elseif ($_REQUEST['device']=='RkamerL') {
			sl('RkamerL', $_REQUEST['action'], basename(__FILE__).':'.__LINE__, true);
			sl('RkamerR', $_REQUEST['action'], basename(__FILE__).':'.__LINE__, true);
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
	} elseif ($_REQUEST['command']=='fetch') {
		include 'secure/_fetchdomoticz.php';
	} elseif ($_REQUEST['command']=='water') {
		storemode('water', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
		double('water', 'On');
	} elseif ($_REQUEST['command']=='powermode') {
		if ($_REQUEST['device']=='living_set') {$ip=111;$daikin='living';}
		elseif ($_REQUEST['device']=='kamer_set') {$ip=112;$daikin='kamer';}
		elseif ($_REQUEST['device']=='alex_set') {$ip=113;$daikin='alex';}
		$data=json_decode($d[$_REQUEST['device']]['icon'], true);
		$data['powermode']=$_REQUEST['action'];
		storeicon($_REQUEST['device'], json_encode($data));
		if ($_REQUEST['action']=='Normal') {
			$d=fetchdata();
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
			$d=fetchdata();
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
				mset('powermeter',time());
				sleep(1);
				if (mget('avg')<$_REQUEST['action']) sw('powermeter', 'On', basename(__FILE__).':'.__LINE__);
				storemode('powermeter', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			} else {
				sw('powermeter', 'Off', basename(__FILE__).':'.__LINE__);
				storemode('powermeter', 0, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='GroheRed') {
			if ($_REQUEST['action']>0) {
				if (mget('avg')<$_REQUEST['action']) sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__);
				storemode('GroheRed', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			} else {
				sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
				storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
			}
		} else {
			if (endswith($_REQUEST['device'], '_set')) call_user_func($_REQUEST['command'], $_REQUEST['device'],$_REQUEST['action'],basename(__FILE__).':'.__LINE__);
			else call_user_func($_REQUEST['command'],str_replace('_', ' ', $_REQUEST['device']),$_REQUEST['action'],basename(__FILE__).':'.__LINE__);
		}
	}
}
elseif (isset($_REQUEST['boseip'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
	if ($_REQUEST['command']=='volume') {
		bosevolume($_REQUEST['action'], $_REQUEST['boseip'], basename(__FILE__).':'.__LINE__);
	} elseif ($_REQUEST['command']=='bass') {
		bosebass($_REQUEST['action'], $_REQUEST['boseip']);
	} elseif ($_REQUEST['command']=='preset') {
		bosepreset($_REQUEST['action'], $_REQUEST['boseip']);
	} elseif ($_REQUEST['command']=='skip') {
		$db=dbconnect();
		$stmt=$db->query("SELECT s FROM devices WHERE n like 'bose101';");
		while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
			$bose=$row['s'];
		}
		if ($bose=='On') $_REQUEST['boseip']=101;
		if ($_REQUEST['action']=='prev') {
			bosekey("PREV_TRACK", 0, $_REQUEST['boseip'],basename(__FILE__).':'.__LINE__);
		} elseif ($_REQUEST['action']=='next') {
			bosekey("NEXT_TRACK", 0, $_REQUEST['boseip'],basename(__FILE__).':'.__LINE__);
		}
	} elseif ($_REQUEST['command']=='power') {
			if ($_REQUEST['action']=='On') {
				bosezone($_REQUEST['boseip'], true);
			} elseif ($_REQUEST['action']=='Off') {
				bosekey("POWER", 0, $_REQUEST['boseip'],basename(__FILE__).':'.__LINE__);
				sw('bose'.$_REQUEST['boseip'], 'Off',basename(__FILE__).':'.__LINE__);
				if ($_REQUEST['boseip']==101) {
					$d=fetchdata();
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
			}  elseif ($_REQUEST['action']=='hartje') {
				bosekey('THUMBS_UP');
			}  elseif ($_REQUEST['action']=='skippen') {
				$status='On';
				require('secure/pass2php/Bose verwijderen.php');
			}
	} elseif ($_REQUEST['command']=='mode') {
		storemode('bose'.$_REQUEST['boseip'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
	}
}
$db=null;
