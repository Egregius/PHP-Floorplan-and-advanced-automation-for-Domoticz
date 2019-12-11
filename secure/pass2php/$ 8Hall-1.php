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
        waarschuwing('Let op. Achterdeur open', 55);
        exit('');
    }
    if ($d['raamliving']['s']!='Closed') {
        waarschuwing('Let op. Raam Living open', 55);
        exit('');
    }
    if ($d['bureeltobi']['s']=='On') {
        waarschuwing('Let op. Bureel Tobi aan', 55);
        exit('');
    }
    if ($d['bose105']['m']=='Online') {
        waarschuwing('Let op. Bose buiten', 55);
        exit('');
    }
    if ($d['poort']['s']!='Closed') {
        waarschuwing('Let op. Poort open', 55);
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