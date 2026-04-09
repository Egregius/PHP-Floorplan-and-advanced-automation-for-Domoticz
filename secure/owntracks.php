<?php
require '/var/www/html/secure/functions.php';
if (isset($_GET['user'])) {
	if ($_GET['user']=='Guy'||$_GET['user']=='Kirby') {
		if (isset($_GET['event'])) {
			if ($_GET['event']=='enter') {
				$user=$_GET['user'];
				$db = Database::getInstance();
				$d=fetchdata();
				if($d['weg']->s==2) {
					setCache('timestampweg',$time);
					hassnotify('🏠 Huis thuis', 'door '.$user, 'mobile_app_iphone_guy', false);
					setCache('remoteauto', time());
					sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
					huisthuis('Huis thuis door '.$user);
					if ($d['dag']->s>=-2) {
						sleep(5);
						sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
					}
				} elseif($d['weg']->s==0) {
					setCache('timestampweg',$time);
					sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
					huisthuis('🏠 Huis thuis door '.$user);
					if ($d['dag']->s>=-2) {
						sleep(5);
						sw('voordeur', 'Off', basename(__FILE__).':'.__LINE__);
					}
				}
			}
		} else telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET, true).PHP_EOL.'Event niet gevonden');
	} else telegram('domoticz/owntracks.php:'.__LINE__.PHP_EOL.'Onbekende gebruiker: '.$_GET['user']);
} else telegram('domoticz/owntracks.php:'.__LINE__.print_r($_GET,true).PHP_EOL.'User niet gevonden');
