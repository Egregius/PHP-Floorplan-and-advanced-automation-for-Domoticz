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
if (isset($_REQUEST['source'])&&isset($_REQUEST['token'])&&$_REQUEST['token']==$ringtoken) {
	echo ' Start | ';
	$d=fetchdata();
	$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
	$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
	if ($d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)) {
		echo ' licht voordeur aan | ';
		sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	}
	if ($_REQUEST['kind']=='on_demand') {
		echo ' On demand | ';
		telegram('Ring on demand '.$_REQUEST['source'], true, 1);
	} elseif ($_REQUEST['kind']=='motion'&&$_REQUEST['dt']=='human') {
		echo ' Motion human | ';
		if ($_SERVER['REQUEST_TIME']>apcu_fetch('ring_motion')+30) {
			apcu_store('ring_motion', $_SERVER['REQUEST_TIME']);
			if ($d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90&&past('poortrf')>90) {
				echo ' Picams | ';
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?ringbeweging&source='.$_REQUEST['source'].'" > /dev/null 2>/dev/null &');
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
			}
//			telegram('Ring beweging '.$_REQUEST['source'], true, 1);
		}
	} elseif ($_REQUEST['kind']=='ding') {
		echo ' Ding | ';
		if ($_SERVER['REQUEST_TIME']>apcu_fetch('ring_ding')+30) {
			apcu_store('ring_ding', $_SERVER['REQUEST_TIME']);
			telegram('Ring DEURBEL '.$_REQUEST['source'], false, 1);
			if ($d['deurvoordeur']['s']=='Closed') {
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?ringdeurbel&source='.$_REQUEST['source'].'" > /dev/null 2>/dev/null &');
				shell_exec('/usr/bin/wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
			}
			if ($d['Weg']['s']==0) {
				echo ' Deurbel | ';
				rgb('Xlight', 50, 100);
				if ($d['Xvol']['s']!=40) {
					sl('Xvol', 40, basename(__FILE__).':'.__LINE__);
					usleep(10000);
				}
				sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
				if ($d['bose101']['s']=='On') shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php" > /dev/null 2>/dev/null &');
				if ($d['lgtv']['s']=='On') shell_exec('python3 /var/www/html/secure/lgtv.py -c send-message -a "Deurbel" 192.168.2.6 > /dev/null 2>/dev/null &');
				sleep(2);
				sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
			}
		}
	}
	echo ' END';
}
//telegram(print_r($_REQUEST, true), true, 1);
