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
    if ($d['keuken']['s']=='On') {
        sw('keuken', 'Off', true, 'keuken uit omdat kookplaat aan is');
    }
} else {
    /*if ($d['pirkeuken']['s']=='Off') {
        ud('pirkeuken', 0, 'On');
    }*/
    if ($d['zon']['s']<$zonkeuken&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['auto']['s']) {
        sw('keuken', 'On', false, 'keuken aan omdat kookplaat uit is');
    }
}