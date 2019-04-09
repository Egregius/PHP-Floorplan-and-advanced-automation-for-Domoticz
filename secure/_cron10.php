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
if ($d['pirgarage']['s']=='Off'
    &&past('pirgarage')>120
    &&past('poort')>90
    &&past('garage')>120
    &&$d['garage']['s']=='On'
    &&$d['auto']['s']
) {
    sw('garage', 'Off');
} elseif ($d['pirgarage']['s']=='On'
    &&$d['garage']['s']=='Off'
    &&$d['auto']['s']
    &&$d['zon']['s']<$zongarage
) {
    sw('garage', 'On');
}

if ($d['pirinkom']['s']=='Off'
    &&past('pirinkom')>50
    &&$d['pirhall']['s']=='Off'
    &&past('pirhall')>50
    &&past('inkom')>80
    &&past('hall')>80
    &&$d['auto']['s']
) {
    if ($d['inkom']['s']=='On') {
        sw('inkom', 'Off');
    }
    if ($d['hall']['s']=='On') {
        sw('hall', 'Off');
    }
} else {
    if ($d['pirinkom']['s']=='On'
        &&$d['zon']['s']<$zoninkom
        &&$d['auto']['s']
    ) {
        if ($d['inkom']['s']=='Off') {
            sw('inkom', 'On');
        }
    }
    if ($d['pirhall']['s']=='On'
        &&$d['zon']['s']<$zoninkom
        &&$d['auto']['s']
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
    &&$d['auto']['s']
) {
    sw('keuken', 'Off');
}

if (ping($lgtvip)) {
    sleep(3);
    if (ping($lgtvip)) {
        if ($d['lgtv']['s']!='On'
            &&past('lgtv')>10
            &&$d['Weg']['s']==0
        ) {
            sw('lgtv', 'On', true, 'LG TV On _cron5');
        }
        if ($d['denon']['s']!='On'
            &&past('denon')>30
            &&$d['Weg']['s']==0
        ) {
            sw('denon', 'On', true);
            storemode('denon', 'TV');
        }
        if ($d['nvidia']['s']!='On'
            &&past('nvidia')>30
            &&$d['Weg']['s']==0
        ) {
            sw('nvidia', 'On');
        }
    }
} else {
    sleep(1);
    if (!ping($lgtvip)) {
        sleep(1);
        if (!ping($lgtvip)) {
            if ($d['lgtv']['s']!='Off'
                &&past('lgtv')>120
            ) {
                sw('lgtv', 'Off', true, 'LG TV Off _cron5');
            }
            /*if ($d['denon']['s']!='Off'
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
            }*/
            if ($d['kristal']['s']!='Off'
                &&past('lgtv')>120
                &&past('kristal')>120
            ) {
                sw('kristal', 'Off', true);
            }
        }
    }
}
