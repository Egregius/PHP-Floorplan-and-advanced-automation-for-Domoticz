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
$user='cron10  ';
if ($d['auto']['s']=='On') {
	if ($d['pirgarage']['s']=='Off'
		&&past('pirgarage')>120
		&&past('poort')>90
		&&past('deurgarage')>90
		&&past('garageled')>120
		&&$d['garageled']['s']=='On'
	) {
		sw('garageled', 'Off', basename(__FILE__).':'.__LINE__);
	} elseif ($d['pirgarage']['s']=='On'
		&&$d['garageled']['s']=='Off'
		&&$d['garage']['s']=='Off'
		&&$d['zon']['s']<300
	) {
		sw('garageled', 'On', basename(__FILE__).':'.__LINE__);
	}

	if ($d['pirgarage']['s']=='Off'
		&&past('pirgarage')>120
		&&past('poort')>90
		&&past('deurgarage')>90
		&&past('garage')>240
		&&$d['garage']['s']=='On'
	) {
		sw('garage', 'Off', basename(__FILE__).':'.__LINE__);
	}

	if ($d['pirinkom']['s']=='Off'
		&&past('pirinkom')>=30
		&&$d['pirhall']['s']=='Off'
		&&past('pirhall')>=30
		&&past('deurwc')>=30
		&&past('deurinkom')>=30
		&&past('deurkamer')>=30
		&&past('deurbadkamer')>=30
		&&past('deurtobi')>=30
		&&past('deuralex')>=30
		&&past('inkom')>=30
		&&past('hall')>=30
	) {
		$items=array(31,25,20,0);
		foreach ($items as $i) {
			if ($d['inkom']['s']>$i) {
				sl('inkom', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
		foreach ($items as $i) {
			if ($d['hall']['s']>$i) {
				sl('hall', $i, basename(__FILE__).':'.__LINE__);
				break;
			}
		}
	} else {
		if ($d['pirinkom']['s']=='On'
			&&$d['zon']['s']==0
		) {
			finkom();
		}
		if ($d['pirhall']['s']=='On'
			&&$d['zon']['s']==0
		) {
			fhall();
		}
	}
	if (past('pirkeuken')>50
		&&past('keuken')>70
		&&$d['pirkeuken']['s']=='Off'
		&&$d['wasbak']['s']=='Off'
		&&$d['keuken']['s']=='On'
		&&$d['kookplaat']['s']=='Off'
		&&$d['werkblad1']['s']=='Off'
	) {
		sw('keuken', 'Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['GroheRed']['s']=='On') {
		if ($d['wasbak']['s']=='Off'
			&&$d['kookplaat']['s']=='Off'
			&&past('GroheRed')>110
			&&$d['GroheRed']['m']==0
		) {
			sw('GroheRed', 'Off', basename(__FILE__).':'.__LINE__);
		}
		if (past('GroheRed')>1200) {
			storemode('GroheRed', 0, basename(__FILE__).':'.__LINE__);
		}
	} else {
		if (past('GroheRed')>120
			&&(
			($d['wasbak']['s']=='On'&&past('wasbak')>10)
			||($d['kookplaat']['s']=='On'&&past('kookplaat')>10))
		) {
				sw('GroheRed', 'On', basename(__FILE__).':'.__LINE__);
		}
	}
}
if (ping($lgtvip)) {
    sleep(3);
    if (ping($lgtvip)) {
        if ($d['lgtv']['s']!='On'
            &&past('lgtv')>10
            &&$d['Weg']['s']==0
        ) {
            sw('lgtv', 'On', basename(__FILE__).':'.__LINE__);
        }
        if ($d['denon']['s']!='On'
            &&past('denon')>30
            &&$d['Weg']['s']==0
        ) {
            sw('denon', 'On', basename(__FILE__).':'.__LINE__);
            storemode('denon', 'TV', basename(__FILE__).':'.__LINE__);
        }
        /*if ($d['nvidia']['s']!='On'
            &&past('nvidia')>30
            &&$d['Weg']['s']==0
        ) {
            sw('nvidia', 'On', basename(__FILE__).':'.__LINE__);
        }*/
    }
} else {
    sleep(1);
    if (!ping($lgtvip)) {
        sleep(1);
        if (!ping($lgtvip)) {
            if ($d['lgtv']['s']!='Off'
                &&past('lgtv')>120
            ) {
                sw('lgtv', 'Off', basename(__FILE__).':'.__LINE__);
            }
            if ($d['denon']['s']!='Off'
                &&$d['denon']['m']=='TV'
                &&past('lgtv')>120
                &&past('denon')>300
            ) {
                sw('denon', 'Off', basename(__FILE__).':'.__LINE__);
            }
            if ($d['nvidia']['s']!='Off'
                &&past('lgtv')>120
                &&past('nvidia')>120
            ) {
                sw('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
            }
            if ($d['kristal']['s']!='Off'
                &&past('lgtv')>120
                &&past('kristal')>120
            ) {
                sw('kristal', 'Off', basename(__FILE__).':'.__LINE__);
            }
        }
    }
}
if ($d['nvidia']['s']=='On') {
    if (pingport($shieldip, 9080)==1) {
        if ($d['nvidia']['m']=='Off') {
            storemode('nvidia', 'On', basename(__FILE__).':'.__LINE__);
        }
    } else {
        if ($d['nvidia']['m']=='On') {
            storemode('nvidia', 'Off', basename(__FILE__).':'.__LINE__);
        }
    }
}
if (ping('192.168.2.105')) {
    if ($d['bose105']['m']!='Online') {
        storemode('bose105', 'Online', basename(__FILE__).':'.__LINE__, 10);
    }
    if ($d['achterdeur']['s']=='Open') {
        $status=json_decode(
            json_encode(
                simplexml_load_string(
                    @file_get_contents(
                        "http://192.168.2.105:8090/now_playing"
                    )
                )
            ),
            true
        );
        if (!empty($status)) {
            if (isset($status['@attributes']['source'])) {
                if ($status['@attributes']['source']=='STANDBY') {
                    bosezone(105);
                    sw('bose105', 'On', basename(__FILE__).':'.__LINE__);
                }
            }
        }
    }
} else {
    if ($d['bose105']['m']!='Offline') {
        storemode('bose105', 'Offline', basename(__FILE__).':'.__LINE__, 10);
        sw('bose105', 'Off', basename(__FILE__).':'.__LINE__);
    }
}
if (past('wind')>86) {
	require('_weather.php');
}