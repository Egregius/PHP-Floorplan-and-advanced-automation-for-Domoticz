<?php
/**
 * Pass2PHP
 * php version 7.2.15
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='On') {
    $kamer=$d['kamer']['s'];
    if ($d['Weg']['s']==0&&$d['kamer']['s']!=3) {
        sl('kamer', 3);
        storemode('kamer', 0);
    } elseif ($d['Weg']['s']==0&&$d['kamer']['s']>=2) {
        sl('kamer', 2);
        include 'pass2php/minihall1s.php';
        storemode('kamer', 0);
    } elseif ($d['Weg']['s']==1&&$d['kamer']['s']==0) {
        sl('kamer', 2);
        storemode('kamer', 0);
    } elseif ($d['kamer']['m']==1) {
        sl('kamer', 0);
        storemode('kamer', 0);
    } elseif ($d['Weg']['s']==1) {
        sl('kamer', 1);
        storemode('kamer', 1);
    }
} else {
    if ($d['sirene']['s']!='Group Off') {
        sw('sirene', 'Off');
    }
    if ($d['Weg']['s']!=0) {
        store('Weg', 0);
    } elseif ($d['Weg']['s']==0) {
        if ($d['hall']['s']=='Off') {
            sw('hall', 'On');
        } else {
            sl('lichtbadkamer', 18);
        }
    }
}