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
    if ($d['eettafel']['s']<14) {
        sl('eettafel', 14, basename(__FILE__).':'.__LINE__);
    } else {
        sl('eettafel', 100, basename(__FILE__).':'.__LINE__);
    }
}