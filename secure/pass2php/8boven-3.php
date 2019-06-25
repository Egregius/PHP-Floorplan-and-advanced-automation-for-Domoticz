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
    $item='Ralex';
    if (past('8boven-3')>=2) {
        sl($item, 0, basename(__FILE__).':'.__LINE__);
    } else {
        $half=45;
        $lijntjes=78;
        $itemstatus=$d[$item]['s'];
        if ($itemstatus>$half) {
            sl($item, $half, basename(__FILE__).':'.__LINE__);
        } elseif ($itemstatus>$lijntjes) {
            sl($item, $lijntjes, basename(__FILE__).':'.__LINE__);
        } else {
            sl($item, 0, basename(__FILE__).':'.__LINE__);
        }
    }
    if ($d[$item]['m']==0) {
        storemode($item, 1, basename(__FILE__).':'.__LINE__);
    }
    resetsecurity();
}