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
    if ($d['tv']['s']=='On') {
        if ($d['Rbureel']['s'] > 45 ||$d['RkeukenL']['s'] > 29 ||$d['RkeukenR']['s'] > 30 ) {
            $item='Rbureel';
            if ($d[$item]['s']>45) {
                sl($item, 45);
            }
            $item='RkeukenL';
            if ($d[$item]['s']>29) {
                sl($item, 29);
            }
            $item='RkeukenR';
            if ($d[$item]['s']>30) {
                sl($item, 30);
            }
        } else {
            $level=0;
            $item='Rbureel';
            if ($d[$item]['s']>$level) {
                sl($item, $level);
            }
            $item='RkeukenL';
            if ($d[$item]['s']>$level) {
                sl($item, $level);
            }
            $item='RkeukenR';
            if ($d[$item]['s']>$level) {
                sl($item, $level);
            }
        }
    } else {
        if ($d['Rbureel']['s']>30||$d['RkeukenL']['s']>29||$d['RkeukenR']['s']>30) {
            $item='Rbureel';
            if ($d[$item]['s']>30) {
                sl($item, 30);
            }
            $item='RkeukenL';
            if ($d[$item]['s']>29) {
                sl($item, 29);
            }
            $item='RkeukenR';
            if ($d[$item]['s']>30) {
                sl($item, 30);
            }
        } else {
            $level=0;
            $item='Rbureel';
            if ($d[$item]['s']>$level) {
                sl($item, $level);
            }
            $item='RkeukenL';
            if ($d[$item]['s']>$level) {
                sl($item, $level);
            }
            $item='RkeukenR';
            if ($d[$item]['s']>$level) {
                sl($item, $level);
            }
        }
    }
}