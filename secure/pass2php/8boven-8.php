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
    $item='Rtobi';
    if (past('8boven-8')>=2) {
        sl($item, 100);
    } else {
        $half=45;
        $lijntjes=78;
        $itemstatus=$d[$item]['s'];
        if ($itemstatus<$half) {
            sl($item, $half);
        } elseif ($itemstatus<$lijntjes) {
            sl($item, $lijntjes);
        } else {
            sl($item, 100);
        }
    }
    if ($d[$item]['m']==0) {
        storemode($item, 1);
    }
}