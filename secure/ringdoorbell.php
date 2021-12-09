<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category	Home_Automation
 * @package	Pass2PHP
 * @author	Guy Verschuere <guy@egregius.be>
 * @license	GNU GPLv3
 * @link		https://egregius.be
 **/
require_once '/var/www/html/secure/functions.php';
if (isset($_REQUEST['source'])) {
	echo ' Start | ';
	if (apcu_fetch('ring-'.$_REQUEST['kind'])!=$_REQUEST['id']) {
		echo ' new id | ';
		apcu_store('ring-'.$_REQUEST['kind'], $_REQUEST['id']);
		$d=fetchdata();
		$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
		$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
		if ($d['voordeur']['s']=='Off'/*&&$d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)*/) sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
		if ($_REQUEST['kind']=='motion'&&$_REQUEST['dt']=='human') {
			echo ' Motion human | ';
			if ($d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90&&past('poortrf')>90) {
				echo ' Picams | ';
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?ringbeweging&source='.$_REQUEST['source'].'" > /dev/null 2>/dev/null &');
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.13/telegram.php?ringbeweging&source='.$_REQUEST['source'].'" > /dev/null 2>/dev/null &');
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.13/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
			}
			telegram('Ring beweging '.$_REQUEST['source'], true, 1);
		} elseif ($_REQUEST['kind']=='ding') {
			echo ' Ding | ';
			if ($d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)) {
				echo __LINE__.' Voordeur On | ';
				sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
			}
			telegram('Ring DEURBEL '.$_REQUEST['source'], false, 2);
			shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?deurbel" > /dev/null 2>/dev/null &');
			shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.13/telegram.php?deurbel" > /dev/null 2>/dev/null &');
			shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
			shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.13/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
			if ($d['Weg']['s']==0) {
				echo ' Deurbel | ';
				if ($d['Xvol']['s']!=40) {
					sl('Xvol', 40, basename(__FILE__).':'.__LINE__);
					usleep(10000);
				}
				sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
				if ($d['bose101']['s']=='On') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php?deurbel" > /dev/null 2>/dev/null &');
				if ($d['lgtv']['s']=='On') shell_exec('python3 /var/www/html/secure/lgtv.py -c send-message -a "Deurbel" 192.168.2.27 > /dev/null 2>/dev/null &');
				sleep(2);
				sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
			}
		}
		echo ' END';
	}
}
