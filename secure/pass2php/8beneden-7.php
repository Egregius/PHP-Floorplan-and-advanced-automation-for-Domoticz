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
    $eettafel=$d['eettafel']['s'];
    if ($eettafel>14) {
        sl('eettafel', 14);
    } elseif ($eettafel==0) {
        sl('eettafel', 5);
    } else {
        sl('eettafel', 0);
    }
}