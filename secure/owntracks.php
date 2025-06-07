<?php
if ($_SERVER['REMOTE_ADDR']=='192.168.2.20') {
	require '/var/www/html/secure/functions.php';
	if (isset($_GET['user'])) {
		if ($_GET['user']=='Guy'||$_GET['user']=='Kirby') {
			if (isset($_GET['event'])) {
				if ($_GET['event']=='enter') {
					$user=$_GET['user'];
					$db=dbconnect();
					$stmt=$db->query("SELECT n,s,t FROM devices WHERE n IN ('weg','voordeur','dag');");
					while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
						$d[$row['n']]['s']=$row['s'];
						$d[$row['n']]['t']=$row['t'];
					}
					if (past('weg')>300) {
						if ($d['voordeur']['s']=='Off'&&$d['dag']['s']<2) {
							sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
						}
						if($d['weg']['s']==2) {
							telegram('Huis thuis door '.$user);
							mset('remoteauto', time());
							huisthuis('Huis thuis door '.$user);
						} elseif($d['weg']['s']==0) {
			//				telegram('Huis thuis door '.$user);
							huisthuis('Huis thuis door '.$user);
						}
					}
				}
			} else telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET, true).PHP_EOL.'Event niet gevonden');
		} else telegram('domoticz/owntracks.php:'.__LINE__.PHP_EOL.'Onbekende gebruiker: '.$_GET['user']);
	} else telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET,true).PHP_EOL.'User niet gevonden');
} else telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET,true).PHP_EOL.$_SERVER['REMOTE_ADDR']);
//telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET,true).PHP_EOL.$_SERVER['REMOTE_ADDR']);