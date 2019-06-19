<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='On') {
    if ($d['achterdeur']['s']!='Closed') {
        waarschuwing('Opgelet: Achterdeur open!', 'achterdeur');
    }
    if ($d['raamliving']['s']!='Closed') {
        waarschuwing('Opgelet: Raam Living open!', 'raamliving');
    }
    if ($d['bureeltobi']['s']=='On') {
        waarschuwing('Opgelet: bureel Tobi aan!', 'bureeltobi');
    }
    if ($d['bose105']['m']=='Online') {
        waarschuwing('Opgelet: Bose buiten!', 'bosebuiten');
    }
    if ($d['poort']['s']=='Open') {
 		shell_exec('/var/www/html/secure/boseplayinfo.sh "allesok" > /dev/null 2>/dev/null &');
    	if ($d['bose104']['s']=='On') {
			shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose104.php" > /dev/null 2>/dev/null &');
		}
        if ($d['garage']['s']=='On') {
            sw('garage', 'Off');
        }
        if ($d['garageled']['s']=='On') {
            sw('garageled', 'Off');
        }
        
        store('Weg', 2);
        sw(array('weg'), 'Off');
    } else {
        sw('poortrf', 'On');
        if ($d['sirene']['s']!='Group Off') {
            double('sirene', 'Off');
        }
    }
    huisweg();
}