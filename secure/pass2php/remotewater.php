<?php
/**
 * Pass2PHP
 * php version 7.0.33
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='On') {
    sw('water', 'On');
    if ($d['water']['m']==0) {
        storemode('water', 300);
    } elseif ($d['water']['m']==300) {
        storemode('water', 1800);
    } elseif ($d['water']['m']==1800) {
        storemode('water', 7200);
    }
} else {
    sw('water', 'Off');
}
