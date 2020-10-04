<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
if (isset($_REQUEST['token'])&&$_REQUEST['token']==$ifttttoken) {
	$d=fetchdata();
	if (isset($_REQUEST['ring'])&&$_REQUEST['ring']=='motion') {
		echo 'Motion';
		if ($d['zon']['s']==0) {
			sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
		}
		shell_exec('curl -s "http://192.168.2.11/telegram.php?action=Beweging" > /dev/null 2>/dev/null &');
		shell_exec('curl -s "http://192.168.2.13/telegram.php?action=Beweging" > /dev/null 2>/dev/null &');
		shell_exec('curl -s "http://192.168.2.11/fifo_command.php?cmd=record%20on%2015%2055" > /dev/null 2>/dev/null &');
		shell_exec('curl -s "http://192.168.2.13/fifo_command.php?cmd=record%20on%2015%2055" > /dev/null 2>/dev/null &');
		if ($d['Weg']['s']==0&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed') {
			if ($d['lgtv']['s']=='On') {
			    shell_exec('python3 secure/lgtv.py -c send-message -a "Beweging Ring" 192.168.2.27');
			}
			if (past('Xbel')>60) {
				if ($d['Xvol']['s']!=5) {
				    sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
				}
				sl('Xbel', 30, basename(__FILE__).':'.__LINE__);
			}
		}
	} elseif (isset($_REQUEST['action'])&&$_REQUEST['action']=='doorbell') {
		echo 'Doorbell';
		if ($d['zon']['s']==0) {
			sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
		}
		shell_exec('curl -s "http://192.168.2.11/telegram.php?action=DEURBEL" > /dev/null 2>/dev/null &');
		shell_exec('curl -s "http://192.168.2.13/telegram.php?action=DEURBEL" > /dev/null 2>/dev/null &');
		shell_exec('curl -s "http://192.168.2.11/fifo_command.php?cmd=record%20on%2015%2055" > /dev/null 2>/dev/null &');
		shell_exec('curl -s "http://192.168.2.13/fifo_command.php?cmd=record%20on%2015%2055" > /dev/null 2>/dev/null &');
		telegram('Deurbel', true, 2);
		if ($d['Weg']['s']==0&&$d['deurvoordeur']['s']=='Closed') {
			sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['lgtv']['s']=='On') {
				shell_exec('python3 ../lgtv.py -c send-message -a "Deurbel" 192.168.2.27 > /dev/null 2>/dev/null &');
			}
			if ($d['Xvol']['s']!=40) {
			    sl('Xvol', 40, basename(__FILE__).':'.__LINE__);
			    usleep(10000);
			}
			sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
			sleep(2);
			sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
		}
		
	}
}