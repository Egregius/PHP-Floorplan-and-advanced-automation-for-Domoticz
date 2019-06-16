<?php
/**
 * Pass2PHP cron10
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
//lg('               __CRON10__');
$user='cron10';
if ($d['pirgarage']['s']=='Off'
    &&past('pirgarage')>120
    &&past('poort')>90
    &&past('garageled')>120
    &&$d['garageled']['s']=='On'
    &&$d['auto']['s']=='On'
) {
    sw('garageled', 'Off');
} elseif ($d['pirgarage']['s']=='On'
    &&$d['garageled']['s']=='Off'
    &&$d['garage']['s']=='Off'
    &&$d['auto']['s']=='On'
    &&$d['zon']['s']<500
) {
    sw('garageled', 'On');
}

if ($d['pirgarage']['s']=='Off'
    &&past('pirgarage')>120
    &&past('poort')>90
    &&past('garage')>240
    &&$d['garage']['s']=='On'
    &&$d['auto']['s']=='On'
) {
    sw('garage', 'Off');
}

if ($d['pirinkom']['s']=='Off'
    &&past('pirinkom')>50
    &&$d['pirhall']['s']=='Off'
    &&past('pirhall')>50
    &&past('inkom')>80
    &&past('hall')>80
    &&$d['auto']['s']=='On'
) {
    if ($d['inkom']['s']=='On') {
        sw('inkom', 'Off');
    }
    if ($d['hall']['s']=='On') {
        sw('hall', 'Off');
    }
} else {
    if ($d['pirinkom']['s']=='On'
        &&$d['zon']['s']<10
        &&$d['auto']['s']=='On'
    ) {
        if ($d['inkom']['s']=='Off') {
            sw('inkom', 'On');
        }
    }
    if ($d['pirhall']['s']=='On'
        &&$d['zon']['s']==0
        &&$d['auto']['s']=='On'
    ) {
        if ($d['hall']['s']=='Off'
            &&$d['Weg']['s']==0
        ) {
            sw('hall', 'On');
        }
    }
}
if (past('pirkeuken')>50
    &&past('keuken')>70
    &&$d['pirkeuken']['s']=='Off'
    &&$d['wasbak']['s']=='Off'
    &&$d['keuken']['s']=='On'
    &&$d['kookplaat']['s']=='Off'
    &&$d['werkblad1']['s']=='Off'
    &&$d['auto']['s']=='On'
) {
    sw('keuken', 'Off');
}
if ($d['GroheRed']['s']=='On') {
    if ($d['wasbak']['s']=='Off'
        &&$d['kookplaat']['s']=='Off'
        &&past('GroheRed')>110
        &&$d['GroheRed']['m']==0
    ) {
        sw('GroheRed', 'Off');
    }
    if (past('GroheRed')>900) {
        sw('GroheRed', 'Off');
        storemode('GroheRed', 0);
    }
} else {
    if (past('GroheRed')>120
        &&(
        ($d['wasbak']['s']=='On'&&past('wasbak')>10)
        ||($d['kookplaat']['s']=='On'&&past('kookplaat')>10))
    ) {
            sw('GroheRed', 'On');
    }
}
if (ping($lgtvip)) {
    sleep(3);
    if (ping($lgtvip)) {
        if ($d['lgtv']['s']!='On'
            &&past('lgtv')>10
            &&$d['Weg']['s']==0
        ) {
            sw('lgtv', 'On', true, 'LG TV On _cron10');
        }
        if ($d['denon']['s']!='On'
            &&past('denon')>30
            &&$d['Weg']['s']==0
        ) {
            sw('denon', 'On', true);
            storemode('denon', 'TV');
        }
        /*if ($d['nvidia']['s']!='On'
            &&past('nvidia')>30
            &&$d['Weg']['s']==0
        ) {
            sw('nvidia', 'On');
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
                sw('lgtv', 'Off', true, 'LG TV Off _cron10');
            }
            if ($d['denon']['s']!='Off'
                &&$d['denon']['m']=='TV'
                &&past('lgtv')>120
                &&past('denon')>300
            ) {
                sw('denon', 'Off', true);
            }
            if ($d['nvidia']['s']!='Off'
                &&past('lgtv')>120
                &&past('nvidia')>120
            ) {
                sw('nvidia', 'Off', true);
            }
            if ($d['kristal']['s']!='Off'
                &&past('lgtv')>120
                &&past('kristal')>120
            ) {
                sw('kristal', 'Off', true);
            }
        }
    }
}
if ($d['nvidia']['s']=='On') {
    if (pingport($shieldip, 9080)==1) {
        if ($d['nvidia']['m']=='Off') {
            storemode('nvidia', 'On');
        }
    } else {
        if ($d['nvidia']['m']=='On') {
            storemode('nvidia', 'Off');
        }
    }
}
if (ping('192.168.2.105')) {
    if ($d['bose105']['m']!='Online') {
        storemode('bose105', 'Online', 10);
        lg('bose105 Online');
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
                	lg('Bose buiten aan leggen | '.$status['@attributes']['source']);
                    bosezone(105);
                    sw('bose105', 'On');
                }
            }
        }
    }
} else {
    if ($d['bose105']['m']!='Offline') {
        storemode('bose105', 'Offline', 10);
        lg('bose105 Offline');
        sw('bose105', 'Off');
    }
}