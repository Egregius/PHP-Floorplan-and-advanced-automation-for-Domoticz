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
    	$volume=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.104:8090/volume'))), true);
		$cv=$volume['actualvolume'];
		if ($cv<55) {
			usleep(1100000);
			bosevolume(55, 104);
			usleep(3000000);
		}
		
        if ($d['garage']['s']=='On') {
            sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
        }
        if ($d['garageled']['s']=='On') {
            sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
        }
        store('Weg', 2, basename(__FILE__).':'.__LINE__);
        sleep(8);
        sw(array('weg'), 'Off', basename(__FILE__).':'.__LINE__);
    } else {
        sw('poortrf', 'On', basename(__FILE__).':'.__LINE__);
        if ($d['sirene']['s']!='Group Off') {
            double('sirene', 'Off', basename(__FILE__).':'.__LINE__);
        }
    }
    huisweg();
}