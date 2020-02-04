<?php
/**
 * Pass2PHP
 * php version 7.3.9-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['badkamervuur1']['s']=='On') {
	if ($d['badkamervuur2']['s']=='On') sw('badkamervuur2', 'Off');
	sw('badkamervuur1', 'Off');
}
if ($d['badkamer_set']['s']!=10) {
	store('badkamer_set', $x, basename(__FILE__).':'.__LINE__);
}
sw('lichtbadkamer', 'Off', basename(__FILE__).':'.__LINE__);
if ($d['auto']['s']=='On') {
	fhall();
	if (TIME<strtotime('20:00')) {
		finkom();
		fliving();
	}
}
if ($d['badkamer_set']['m']!=0) {
	storemode('badkamer_set', 0, basename(__FILE__).':'.__LINE__);
}
douche();
if ($d['bose102']['m']>0) storemode('bose102', 0);
if (TIME>strtotime('20:00')&&$d['Weg']['s']==1&&$d['kamer']['s']>0) {
	if ($d['kamer']['m']!=1) {
		storemode('kamer', 1, basename(__FILE__).':'.__LINE__);
	}
}
if (past('$ 8badkamer-8')>3) {
	$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.102:8090/now_playing'))), true);
	if (!empty($nowplaying)) {
		if (isset($nowplaying['@attributes']['source'])) {
			if ($nowplaying['@attributes']['source']!='STANDBY') {
				$volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.102:8090/volume"))), true);
				$cv=$volume['actualvolume'];
				echo 'cv='.$cv.'<br>';
				$usleep=(6000000/$cv)/2;
				while (1) {
					echo $cv.'<br>';
					bosevolume($cv, 102);
					usleep($usleep);
					$cv=$cv-3;
					if ($cv<=3) {
						break;
					}
				}
				bosekey("POWER", 0, 102);
				if ($d['bose102']['s']!='Off') {
					sw('bose102', 'Off', basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
	if ($d['Weg']['s']==1&&TIME>strtotime('20:00')) {
		$nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.101:8090/now_playing'))), true);
		if (!empty($nowplaying)) {
			if (isset($nowplaying['@attributes']['source'])) {
				if ($nowplaying['@attributes']['source']!='STANDBY') {
					if ($d['bose101']['s']!='Off') {
						sw('bose101', 'Off', basename(__FILE__).':'.__LINE__);
					}
					bosekey("POWER", 0, 101);
				}
			}
		}
	}
}
if ($d['Weg']['s']==1&&TIME>strtotime('6:00')&&TIME<strtotime('12:00')) {
	if ($d['Weg']['s']>0) {
		store('Weg', 0, basename(__FILE__).':'.__LINE__);
	}
}
resetsecurity();
