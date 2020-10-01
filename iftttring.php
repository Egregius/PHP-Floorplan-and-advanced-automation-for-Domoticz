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
	if (isset($_REQUEST['action'])&&$_REQUEST['action']=='motion'&&$d['Weg']['s']==0) {
		echo 'Motion';
		if ($d['lgtv']['s']=='On') {
		    shell_exec('python3 secure/lgtv.py -c send-message -a "Beweging voordeur" 192.168.2.27');
		}
//		if (past('Xbel')>60) {
			if ($d['Xvol']['s']!=5) {
			    sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
			}
			sl('Xbel', 20, basename(__FILE__).':'.__LINE__);
//		}
		
	} else {
		echo 'Doorbell';
		shell_exec('curl -s "http://192.168.2.13/telegram.php?snapshot=true" > /dev/null 2>/dev/null &');
		shell_exec('curl -s "http://192.168.2.11/fifo_command.php?cmd=record%20on%2015%2055" > /dev/null 2>/dev/null &');
		shell_exec('curl -s "http://192.168.2.11/telegram.php?deurbel=true" > /dev/null 2>/dev/null &');
		telegram('Deurbel', true, 2);
		if ($d['zon']['s']==0) {
			sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
		}
		if ($d['Weg']['s']==0) {
			sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
			if ($d['Xvol']['s']!=40) {
			    sl('Xvol', 40, basename(__FILE__).':'.__LINE__);
			    usleep(10000);
			}
			sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
			/* if ($d['bose101']['s']=='On') {
				shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php?deurbel'.$url.'" > /dev/null 2>/dev/null &');
			}
			if ($d['bose102']['s']=='On') {
				shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose102.php?deurbel'.$url.'" > /dev/null 2>/dev/null &');
			}
			if ($d['bose103']['s']=='On') {
				shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose103.php?deurbel'.$url.'" > /dev/null 2>/dev/null &');
			}
			if ($d['bose104']['s']=='On') {
				shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose104.php?deurbel'.$url.'" > /dev/null 2>/dev/null &');
			}
			if ($d['bose105']['s']=='On') {
				shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose105.php?deurbel'.$url.'" > /dev/null 2>/dev/null &');
			}*/
			if ($d['lgtv']['s']=='On') {
				shell_exec('python3 ../lgtv.py -c send-message -a "Deurbel" 192.168.2.27 > /dev/null 2>/dev/null &');
			}
		}
		sleep(2);
		sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
	}
}