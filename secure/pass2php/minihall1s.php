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
    if ($d['achterdeur']['s']!='Closed') {
        waarschuwing('Opgelet: Achterdeur open!');
        die('exit');
    }
    if ($d['raamliving']['s']!='Closed') {
        waarschuwing('Opgelet: Raam Living open!');
        die('exit');
    }
    if ($d['poort']['s']!='Closed') {
        waarschuwing('Opgelet: Poort open!');
        die('exit');
    }
    if ($d['bureeltobi']['s']=='On') {
        waarschuwing('Opgelet: bureel Tobi aan!');
        die('exit');
    }
    if ($d['Weg']['s']!=1) {
        store('Weg', 1);
    }
    if ($d['kamer']['s']>5) {
        sl('kamer', 5);
    }
    huisslapen();
}