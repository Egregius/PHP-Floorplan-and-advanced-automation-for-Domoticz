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
    if ($d['zon']['s']==0) {
        sl('Rliving', 100, basename(__FILE__).':'.__LINE__);
    } else {
        if ($d['lgtv']['s']=='On') {
            if ($d['Rliving']['s']<40) {
                sl('Rliving', 40, basename(__FILE__).':'.__LINE__);
            } else {
                if ($d['heating']['s']<0) {
                    $level=88;
                } else {
                    $level=100;
                }
                if ($d['Rliving']['s']<$level) {
                    sl('Rliving', $level, basename(__FILE__).':'.__LINE__);
                }
            }
        } else {
            if ($Rliving<25) {
                sl('Rliving', 25, basename(__FILE__).':'.__LINE__);
            } else {
                if ($d['heating']['s']<0) {
                    $level=88;
                } else {
                    $level=100;
                }
                if ($d['Rliving']['s']<$level) {
                    sl('Rliving', $level, basename(__FILE__).':'.__LINE__);
                }
            }
        }
    }
}