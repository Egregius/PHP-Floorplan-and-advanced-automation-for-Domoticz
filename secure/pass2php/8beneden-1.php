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
    if ($d['zon']['s']==0) {
        sl('Rliving', 0);
    } else {
        if ($d['tv']['s']=='On') {
            if ($d['Rliving']['s']>40) {
                if ($d['Rliving']['s']>40) {
                    sl('Rliving', 40);
                }
            } else {
                if ($d['Rliving']['s']>0) {
                    sl('Rliving', 0);
                }
            }
        } else {
            if ($d['Rliving']['s']>25) {
                sl('Rliving', 25);
            } else {
                if ($d['Rliving']['s']>0) {
                    sl('Rliving', 0);
                }
            }
        }
    }
}