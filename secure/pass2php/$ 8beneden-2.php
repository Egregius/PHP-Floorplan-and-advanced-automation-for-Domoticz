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
    if ($d['tv']['s']=='On') {
        if ($d['Rbureel']['s'] > 45 ||$d['RkeukenL']['s'] > 30 ||$d['RkeukenR']['s'] > 30 ) {
            $item='Rbureel';
            if ($d[$item]['s']>45) {
                sl($item, 45, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenL';
            if ($d[$item]['s']>30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenR';
            if ($d[$item]['s']>30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
        } else {
            $level=0;
            $item='Rbureel';
            if ($d[$item]['s']>$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenL';
            if ($d[$item]['s']>$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenR';
            if ($d[$item]['s']>$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
        }
    } else {
        if ($d['Rbureel']['s']>30||$d['RkeukenL']['s']>30||$d['RkeukenR']['s']>30) {
            $item='Rbureel';
            if ($d[$item]['s']>30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenL';
            if ($d[$item]['s']>30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenR';
            if ($d[$item]['s']>30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
        } else {
            $level=0;
            $item='Rbureel';
            if ($d[$item]['s']>$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenL';
            if ($d[$item]['s']>$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenR';
            if ($d[$item]['s']>$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
        }
    }
}