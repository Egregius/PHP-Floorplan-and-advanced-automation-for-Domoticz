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
if ($status=='On'&&$d['auto']['s']=='On'&&past('belknop')>10) {
    //shell_exec('curl -s "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
    //shell_exec('curl -s "http://192.168.2.11/telegram.php?deurbel=true" > /dev/null 2>/dev/null &');
    //shell_exec('curl -s "http://192.168.2.13/telegram.php?snapshot=true" > /dev/null 2>/dev/null &');
    //telegram('Deurbel', true, 2);
    if ($d['zon']['s']==0) {
        sw('voordeur', 'On');
    }
    if ($d['Weg']['s']==0) {
    	if ($d['bose101']['s']=='On') {
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
		}
		if ($d['lgtv']['s']=='On') {
			shell_exec('python3 ../lgtv.py -c send-message -a "Deurbel" 192.168.2.27 > /dev/null 2>/dev/null &');
		}
	
        if ($d['Xvol']['s']!=40) {
            sl('Xvol', 40);
            usleep(10000);
        }
        sl('Xbel', 10);
        sw('deurbel', 'On', false);
        rgb('Xlight', 360, 100);
    }
    sleep(2);
    sl('Xvol', 5);
}
