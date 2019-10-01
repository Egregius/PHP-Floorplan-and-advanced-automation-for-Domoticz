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
if ($status==0) {
    if ($d['pirhall']['s']!='Off') {
        store('pirhall', 'Off', basename(__FILE__).':'.__LINE__);
    }
} else {
    if ($d['Weg']['s']==1) {
        if ($d['Weg']['s']>0) {
        	store('Weg', 0, basename(__FILE__).':'.__LINE__);
        	//$db->query("UPDATE devices set t='0' WHERE n='heating';");
        }
    }
}