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
if ($status=='Off') {
    if ($d['pirgarage']['s']!='Off') {
        store('pirgarage', 'Off');
    }
    if ($d['Weg']['s']==0&&($d['zon']['s']<$zongarage||TIME<strtotime('9:00')||TIME>strtotime('21:00'))&&$d['garageled']['s']=='Off') {
        sw('garageled', 'On');
    }
} elseif ($status=='On') {
    if ($d['garageled']['s']=='On') {
        sw('garageled','Off');
    }
}