<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status=='On') {
    sl('alex', 8, basename(__FILE__).':'.__LINE__);
    storemode('alex', 1, basename(__FILE__).':'.__LINE__);
    if ($d['Ralex']['s']<70) {
    	sl('Ralex', 100);
    }
    resetsecurity();
}