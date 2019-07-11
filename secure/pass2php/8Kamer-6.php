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
if ($status=='On') {
    $item='RkamerR';
    if ($d['raamkamer']['s']=='Open') {
        if ($d[$item]['s']<70) {
            sl($item, 70, basename(__FILE__).':'.__LINE__);
        } elseif ($d[$item]['s']<81) {
            sl($item, 81, basename(__FILE__).':'.__LINE__);
        } elseif ($d[$item]['s']<100) {
            sl($item, 100, basename(__FILE__).':'.__LINE__);
        }
    } else {
        if ($d[$item]['s']<100) {
            sl($item, 100, basename(__FILE__).':'.__LINE__);
        }
    }
    resetsecurity();
}