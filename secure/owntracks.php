<?php
require '/var/www/html/secure/functions.php';
if ($_SERVER['REMOTE_ADDR']=='192.168.2.20'||$_SERVER['REMOTE_ADDR']=='192.168.2.201') {
	if (isset($_GET['user'])) {
		if ($_GET['user']=='Guy'||$_GET['user']=='Kirby') {
			if (isset($_GET['event'])) {
				if ($_GET['event']=='enter') {
					$user=$_GET['user'];
					$db = Database::getInstance();
					$stmt=$db->query("SELECT n,s,d,t FROM devices WHERE n IN ('weg','voordeur','dag');");
					while ($row=$stmt->fetch(PDO::FETCH_NUM)) {
						$d[$row[0]]['s']=$row[1];
						$d[$row[0]]['d']=$row[2];
						$d[$row[0]]['t']=$row[3];
					}
//					telegram(print_r($d,true).PHP_EOL.'past weg = '.past('weg'));
//					if (past('weg')>120) {
//						telegram('Domoticz owntracks.php:'.__LINE__);
						if($d['weg']['s']==2) {
//							telegram('Domoticz owntracks.php='.__LINE__);
							hassnotify('🏠 Huis thuis', 'door '.$user, 'mobile_app_iphone_guy', false);
							setCache('remoteauto', time());
//							if ($d['voordeur']['s']=='Off') {
								sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
//								telegram('🏠 Huis thuis licht voordeur aan '.__LINE__);
//							}
							huisthuis('Huis thuis door '.$user);
							if ($d['dag']['s']>0) {
//								telegram('Domoticz owntracks.php:'.__LINE__);
								sleep(5);
								sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
							}
						} elseif($d['weg']['s']==0) {
//							telegram('Domoticz owntracks.php='.__LINE__);
			//				telegram('Huis thuis door '.$user);
							sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
							telegram('🏠 Huis thuis licht voordeur aan '.__LINE__);
							huisthuis('🏠 Huis thuis door '.$user);
							if ($d['dag']['s']>0) {
//								telegram('Domoticz owntracks.php:'.__LINE__);
								sleep(5);
								sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
							}
						}
//					}
				}
			} else telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET, true).PHP_EOL.'Event niet gevonden');
		} else telegram('domoticz/owntracks.php:'.__LINE__.PHP_EOL.'Onbekende gebruiker: '.$_GET['user']);
	} else telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET,true).PHP_EOL.'User niet gevonden');
} else telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET,true).PHP_EOL.$_SERVER['REMOTE_ADDR']);
//telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET,true).PHP_EOL.$_SERVER['REMOTE_ADDR']);