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
        waarschuwing(' . Let op. Achterdeur open');
        exit('');
    }
    if ($d['raamliving']['s']!='Closed') {
        waarschuwing(' . Let op. Raam Living open');
        exit('');
    }
    if ($d['bureeltobi']['s']=='On') {
        waarschuwing(' . Let op. bureel Tobi aan');
        exit('');
    }
    if ($d['bose105']['m']=='Online') {
        waarschuwing(' . Let op. Bose buiten');
        exit('');
    }
    if ($d['poort']['s']=='Open') {
        if ($d['garage']['s']=='On') {
            sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($d['garageled']['s']=='On') {
            sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
        }
		boseplayinfo(' . Alles ok. Vertrek maar.');
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