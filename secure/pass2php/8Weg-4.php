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
if ($status=='On') {
    if ($d['achterdeur']['s']!='Closed') {
        waarschuwing(' . Opgelet: Achterdeur open', 'achterdeur');
        exit('');
    }
    if ($d['raamliving']['s']!='Closed') {
        waarschuwing(' . Opgelet: Raam Living open', 'raamliving');
        exit('');
    }
    if ($d['bureeltobi']['s']=='On') {
        waarschuwing(' . Opgelet: bureel Tobi aan', 'bureeltobi');
        exit('');
    }
    if ($d['bose105']['m']=='Online') {
        waarschuwing(' . Opgelet: Bose buiten', 'bosebuiten');
        exit('');
    }
    if ($d['poort']['s']=='Open') {
        if ($d['garage']['s']=='On') {
            sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($d['garageled']['s']=='On') {
            sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
        }
 		shell_exec('/var/www/html/secure/boseplayinfo.sh "allesok" > /dev/null 2>/dev/null &');
		usleep(1100000);
		bosevolume(55, 104);
		usleep(3000000);
		bosekey("POWER", 0, 104);
        store('Weg', 2, basename(__FILE__).':'.__LINE__);
        sleep(5);
        sw(array('weg'), 'Off', basename(__FILE__).':'.__LINE__);
    } else {
        sw('poortrf', 'On', basename(__FILE__).':'.__LINE__);
        if ($d['sirene']['s']!='Off') {
            double('sirene', 'Off', basename(__FILE__).':'.__LINE__);
        }
    }
    huisweg();
}