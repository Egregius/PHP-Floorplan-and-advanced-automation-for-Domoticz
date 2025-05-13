<?php
if ($_SERVER['REMOTE_ADDR']=='192.168.2.20') {
	require '/var/www/html/secure/functions.php';
	if (isset($_GET['user'])) $user=$_GET['user'];
	else $user='traccar';
	
	$d=fetchdata(0,basename(__FILE__).':'.__LINE__);
//	if (past('Weg')>300) {
		if ($d['voordeur']['s']=='Off'&&$d['dag']<2) {
			sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
			if($d['Weg']['s']==0) {
				telegram('Huis thuis door '.$user);
				huisthuis('Huis thuis door '.$user);
			} elseif($d['Weg']['s']==2) {
				telegram('Huis thuis door '.$user);
				mset('remoteauto', time());
				huisthuis();
			}
		} elseif($d['Weg']['s']==2) {
			telegram('Huis thuis door '.$user);
			mset('remoteauto', time());
			huisthuis('Huis thuis door '.$user);
		} elseif($d['Weg']['s']==0) {
//			telegram('Huis thuis door '.$user);
			huisthuis('Huis thuis door '.$user);
		}
//	}
}