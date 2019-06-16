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
    if ($d['pirhall']['s']!='Off') {
        store('pirhall', 'Off');
    }
} elseif ($status=='On') {
    if ($d['Weg']['s']==1) {
        store('Weg', 0);
        $db->query("UPDATE devices set t='0' WHERE n='heating';");
    }
}