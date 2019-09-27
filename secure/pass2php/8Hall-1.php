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
    if ($d['poort']['s']!='Closed') {
        waarschuwing('Opgelet: Poort open!', 'poortopen');
    }
    if ($d['bureeltobi']['s']=='On') {
        waarschuwing('Opgelet: bureel Tobi aan!', 'bureeltobi');
    }
    if ($d['bose105']['m']=='Online') {
        waarschuwing('Opgelet: bose buiten!', 'bosebuiten');
    }
    if ($d['Weg']['s']!=1) {
        store('Weg', 1, basename(__FILE__).':'.__LINE__);
    }
    if ($d['kamer']['s']>5) {
        sl('kamer', 5, basename(__FILE__).':'.__LINE__);
    }
    huisslapen();
    $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
	if (!empty($nowplaying)) {
		if (isset($nowplaying['@attributes']['source'])) {
			if ($nowplaying['@attributes']['source']!='STANDBY') {
				bosekey("POWER", 0, 101);
				if ($d['bose101']['s']!='Off') {
					sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}