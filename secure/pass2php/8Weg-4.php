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
    }
    if ($d['raamliving']['s']!='Closed') {
        waarschuwing('Opgelet: Raam Living open!');
    }
    if ($d['bose105']['m']=='Online') {
        waarschuwing('Opgelet: Bose buiten!');
    }
    if ($d['poort']['s']=='Open') {
        if ($d['garage']['s']=='On') {
            sw('garage', 'Off');
        }
        store('Weg', 2);
        sw('deurbel', 'On');
        lgsql('Remote','Weg','Weg');
        sw(array('weg'), 'Off');
    } else {
        sw('poortrf', 'On');
        if ($d['sirene']['s']!='Group Off') {
            double('sirene', 'Off');
        }
    }
    huisweg();
}