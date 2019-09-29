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
    $items=array('Rbureel','RkeukenL','RkeukenR','LGTV - Status');
    foreach ($items as $i) {
        ${$i}=$d[$i]['s'];
    }
    if (${'LGTV - Status'}=='On') {
        if ($Rbureel<45||$RkeukenL<30||$RkeukenR<30) {
            $item='Rbureel';
            if ($d[$item]['s']<45) {
                sl($item, 45, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenL';
            if ($d[$item]['s']<30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenR';
            if ($d[$item]['s']<30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
        } else {
            if ($d['heating']['s']==2) {
                $level=85;
            } else {
                $level=100;
            }
            $item='Rbureel';
            if ($d[$item]['s']<$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenL';
            if ($d[$item]['s']<$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenR';
            if ($d[$item]['s']<$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
        }
    } else {
        if ($Rbureel<30||$RkeukenL<30||$RkeukenR<30) {
            $item='Rbureel';
            if ($d[$item]['s']<30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenL';
            if ($d[$item]['s']<30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenR';
            if ($d[$item]['s']<30) {
                sl($item, 30, basename(__FILE__).':'.__LINE__);
            }
        } else {
            if ($d['heating']['s']==2) {
                $level=85;
            } else {
                $level=100;
            }
            $item='Rbureel';
            if ($d[$item]['s']<$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenL';
            if ($d[$item]['s']<$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
            $item='RkeukenR';
            if ($d[$item]['s']<$level) {
                sl($item, $level, basename(__FILE__).':'.__LINE__);
            }
        }
    }
}