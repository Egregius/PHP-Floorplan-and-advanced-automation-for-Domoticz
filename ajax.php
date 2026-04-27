<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
//session_write_close();
if (isset($_REQUEST['device'])&&$_REQUEST['device']=='runsync'&&$_REQUEST['command']=='runsync') {
	$action = $_REQUEST['action'] ?? '';
	$urls = [
		'garmingpx' => 'http://192.168.30.2:9000/hooks/garmingpx',
		'syncfotos' => 'http://192.168.30.2:9000/hooks/syncfotos',
		'synccamera' => 'http://192.168.30.2:9000/hooks/synccamera',
		'googlemaps' => 'http://192.168.20.21:9000/hooks/googlemaps',
		'garminbadges' => 'http://192.168.20.21:9000/hooks/garminbadges',
		'trakt' => 'http://192.168.20.21:9000/hooks/trakt',
		'weegschaal' => 'http://192.168.20.21:9000/hooks/weegschaal',
		'dedup' => 'http://192.168.20.21:9000/hooks/dedup',
		'resetreizen' => 'http://192.168.20.21:9000/hooks/resetreizen',
	];
	if (isset($urls[$action])) {
		exec('curl -4 ' . $urls[$action] . ' -H "Content-Type: application/json" &');
	}
	exit;
}
elseif (isset($_REQUEST['device'])&&$_REQUEST['device']=='resetsecurity') resetsecurity();
elseif (isset($_REQUEST['bose'])&&$_REQUEST['bose']>=101&&$_REQUEST['bose']<=107) {
	$bose=$_REQUEST['bose'];
	$d=[];
	$nowplaying=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.$bose:8090/now_playing"))), true);
	
	$d['source']=$nowplaying['@attributes']['source'];
	if (isset($nowplaying['ContentItem']['itemName'])) {
		$d['artist']=$nowplaying['artist'];
		$d['track']=$nowplaying['track'];
		$d['art']=$nowplaying['art'];
		$d['playlist']=$nowplaying['ContentItem']['itemName'];
		$trackid=str_replace('spotify:track:','',$nowplaying['trackID']);
		$d['trackid']=$trackid;
		
		$db = Database::getInstance();
		// Check of de track in TOP staat
		$stmt = $db->prepare("SELECT 1 FROM track_mapping WHERE track_id = ? AND playlist_id = '4O0G5e4lsBRG5CV485iolD' LIMIT 1");
		$stmt->execute([$trackid]);
		$d['top'] = (bool)$stmt->fetch();

		// NIEUW: Check of de track überhaupt in een van je bron-lijsten staat (EDM, Pop, Top)
//		$stmtLib = $db->prepare("SELECT 1 FROM track_mapping WHERE track_id = ? LIMIT 1");
//		$stmtLib->execute([$trackid]);
//		$d['in_library'] = (bool)$stmtLib->fetch();
		$volume=json_decode(json_encode(simplexml_load_string(@file_get_contents("http://192.168.2.$bose:8090/volume"))), true);
		$d['volume']=$volume['actualvolume'];
	} else {
		$d['artist']='';
		$d['track']='';
		$d['art']='';
		$d['playlist']='';
		$d['trackid']='';
	}
	echo json_encode($d);
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
				if ($d['badkamervuur1']->s=='Off'&&$d['badkamer_temp']->s<$_REQUEST['action']) sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
				setpoint($_REQUEST['device'].'_set', $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
				storemode($_REQUEST['device'].'_set', 1, basename(__FILE__).':'.__LINE__);
			}
		} else {
			if ($_REQUEST['device']=='living') {
				if ($d['heating']->s==-2) {//airco cooling
					if ($d['daikin']->s=='Off'&&$_REQUEST['action']!='D'&&$d['living_temp']->s>$_REQUEST['action']) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']->s==-1) {//passive cooling
				} elseif ($d['heating']->s==0) {// Neutral
				} elseif ($d['heating']->s==1) {//heating airco
					if ($d['daikin']->s=='Off'&&$d['living_temp']->s<$_REQUEST['action']) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']->s==2) {//heating gas airco
//					if ($d['daikin']->s=='Off'&&$d['living_temp']->s<$_REQUEST['action']) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				} elseif ($d['heating']->s==3) {//heating gas
					if ($d['brander']->s=='Off'&&$d['living_temp']->s<$_REQUEST['action']) sw('brander', 'On', basename(__FILE__).':'.__LINE__);
				}
				if ($d['daikin']->s=='Off'&&$_REQUEST['action']=='D') sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
				if ($d['living_start_temp']->m!=0) storemode('living_start_temp',0);
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
		} elseif ($_REQUEST['action']==2) {
			lg('huisslapen weg...');
			huisslapen(true);
		} elseif ($_REQUEST['action']==3) {
			lg('huisslapen vakantie...');
			huisslapen(3);
		}
	} elseif ($_REQUEST['command']=='dimmer') {
/*		if($_REQUEST['device']=='kamer1'&&$_REQUEST['action']>0) hass('light', 'turn_on', 'light.kamer1', ['brightness_pct' => $_REQUEST['action'],'color_temp_kelvin' => 2202]);
		else */sl($_REQUEST['device'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__, true);
	} elseif ($_REQUEST['command']=='roller') {
		if ($_REQUEST['device']=='rkeukenl') {
			foreach(array('rkeukenl', 'rkeukenr') as $i) {
				if ($d[$i]->s!=$_REQUEST['action']) sl($i, $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
			}
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
			store('8keuken_8', 'On', basename(__FILE__).':'.__LINE__);
		} elseif ($_REQUEST['device']=='Egregius5') {
			$key = "Wifiupdate";
			if (apcu_exists($key)) {
				http_response_code(429);
				exit;
			}
			apcu_store($key, true, 10);
			if($_REQUEST['action']=='On') {
				shell_exec('php /var/www/setSSID.php \'{"main5":1}\' > /dev/null 2>&1 &');
				store('Egregius5', 1, basename(__FILE__).':'.__LINE__);
			} else {
				shell_exec('php /var/www/setSSID.php \'{"main5":0}\' > /dev/null 2>&1 &');
				store('Egregius5', 0, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='Egregius') {
			$key = "Wifiupdate";
			if (apcu_exists($key)) {
				http_response_code(429);
				exit;
			}
			apcu_store($key, true, 10);
			if($_REQUEST['action']=='On') {
				shell_exec('php /var/www/setSSID.php \'{"main24":1}\' > /dev/null 2>&1 &');
				store('Egregius', 1, basename(__FILE__).':'.__LINE__);
			} else {
				shell_exec('php /var/www/setSSID.php \'{"main24":0}\' > /dev/null 2>&1 &');
				store('Egregius', 0, basename(__FILE__).':'.__LINE__);
			}
		} elseif ($_REQUEST['device']=='VanOns') {
			$key = "Wifiupdate";
			if (apcu_exists($key)) {
				http_response_code(429);
				exit;
			}
			apcu_store($key, true, 10);
			if($_REQUEST['action']=='On') {
				shell_exec('php /var/www/setSSID.php \'{"guest":1}\' > /dev/null 2>&1 &');
				store('VanOns', 1, basename(__FILE__).':'.__LINE__);
			} else {
				shell_exec('php /var/www/setSSID.php \'{"guest":0}\' > /dev/null 2>&1 &');
				store('VanOns', 0, basename(__FILE__).':'.__LINE__);
			}
		} else {
			if (str_ends_with($_REQUEST['device'], '_set')) {
				call_user_func($_REQUEST['command'], $_REQUEST['device'],$_REQUEST['action']);
				if($_REQUEST['device']=='living_set') storemode('living_start_temp', 0, basename(__FILE__) . ':' . __LINE__);
			} else call_user_func($_REQUEST['command'],$_REQUEST['device'],$_REQUEST['action']);
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
		while ($row=$stmt->fetch(PDO::FETCH_NUM)) {
			$bose=$row[0];
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
				if ($d['bose102']->s=='On') {
					sw('bose102', 'Off');
				}
				if ($d['bose103']->s=='On') {
					sw('bose103', 'Off');
				}
				if ($d['bose104']->s=='On') {
					sw('bose104', 'Off');
				}
				if ($d['bose105']->s=='On') {
					sw('bose105', 'Off');
				}
			}
		}
	} elseif ($_REQUEST['command']=='mode') {
		storeicon('bose'.$_REQUEST['boseip'], $_REQUEST['action'], basename(__FILE__).':'.__LINE__);
	}
}
if (!isset($_REQUEST->t)&&!isset($_REQUEST['q'])&&!isset($_REQUEST['bose'])&&!isset($_REQUEST['media'])&&!isset($_REQUEST['daikin'])) {
	$msg='';
	foreach($_REQUEST as $k=>$v) {
		$msg.='	'.$k.'	'.$v;
		if (isset($diff)) {
			$msg.='	'.$diff;
		}
	}
	lg('👉🏻 '.$user.$msg);
}
echo 'ok';
