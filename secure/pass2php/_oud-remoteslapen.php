<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
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
        sl('kamer', 3, basename(__FILE__).':'.__LINE__);
        storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
    } elseif ($d['Weg']['s']==0&&$d['kamer']['s']>=2) {
        sl('kamer', 2, basename(__FILE__).':'.__LINE__);
        include 'pass2php/minihall1s.php';
        storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
    } elseif ($d['Weg']['s']==1&&$d['kamer']['s']==0) {
        sl('kamer', 2, basename(__FILE__).':'.__LINE__);
        storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
    } elseif ($d['kamer']['m']==1) {
        sl('kamer', 0, basename(__FILE__).':'.__LINE__);
        storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
    } elseif ($d['Weg']['s']==1) {
        sl('kamer', 1, basename(__FILE__).':'.__LINE__);
        storemode('kamer', 1, basename(__FILE__).':'.__LINE__);
    }
} else {
    if ($d['sirene']['s']!='Group Off') {
        sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
    }
    if ($d['Weg']['s']!=0) {
        store('Weg', 0);
    } elseif ($d['Weg']['s']==0) {
        if ($d['hall']['s']=='Off') {
            sw('hall', 'On', basename(__FILE__).':'.__LINE__);
        } else {
            sl('lichtbadkamer', 18, basename(__FILE__).':'.__LINE__);
        }
    }
}