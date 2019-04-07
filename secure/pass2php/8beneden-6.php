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
    $items=array('Rbureel','RkeukenL','RkeukenR','LGTV - Status');
    foreach ($items as $i) {
        ${$i}=$d[$i]['s'];
    }
    if (${'LGTV - Status'}=='On') {
        if ($Rbureel<45||$RkeukenL<29||$RkeukenR<30) {
            $item='Rbureel';
            if ($d[$item]['s']<45) {
                sl($item, 45);
            }
            $item='RkeukenL';
            if ($d[$item]['s']<29) {
                sl($item, 29);
            }
            $item='RkeukenR';
            if ($d[$item]['s']<30) {
                sl($item, 30);
            }
        } else {
            if ($d['heating']['s']==2) {
                $level=85;
            } else {
                $level=100;
            }
            $item='Rbureel';
            if ($d[$item]['s']<$level) {
                sl($item, $level);
            }
            $item='RkeukenL';
            if ($d[$item]['s']<$level) {
                sl($item, $level);
            }
            $item='RkeukenR';
            if ($d[$item]['s']<$level) {
                sl($item, $level);
            }
        }
    } else {
        if ($Rbureel<30||$RkeukenL<29||$RkeukenR<30) {
            $item='Rbureel';
            if ($d[$item]['s']<30) {
                sl($item, 30);
            }
            $item='RkeukenL';
            if ($d[$item]['s']<29) {
                sl($item, 29);
            }
            $item='RkeukenR';
            if ($d[$item]['s']<30) {
                sl($item, 30);
            }
        } else {
            if ($d['heating']['s']==2) {
                $level=85;
            } else {
                $level=100;
            }
            $item='Rbureel';
            if ($d[$item]['s']<$level) {
                sl($item, $level);
            }
            $item='RkeukenL';
            if ($d[$item]['s']<$level) {
                sl($item, $level);
            }
            $item='RkeukenR';
            if ($d[$item]['s']<$level) {
                sl($item, $level);
            }
        }
    }
}