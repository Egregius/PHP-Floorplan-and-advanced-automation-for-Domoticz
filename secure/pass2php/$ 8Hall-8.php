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
    sl('tobi', 8, basename(__FILE__).':'.__LINE__);
    storemode('tobi', 1, basename(__FILE__).':'.__LINE__);
    if ($d['Rtobi']['s']<70) {
    	sl('Rtobi', 100);
    }
    resetsecurity();
}
