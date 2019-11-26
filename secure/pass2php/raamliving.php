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
if ($status=='Open') {
    if ($d['Weg']['s']>0&&$d['auto']['s']) {
        store('beweging', TIME, basename(__FILE__).':'.__LINE__);
        if (past('beweging')<1800) {
            sw('sirene', 'On', basename(__FILE__).':'.__LINE__);
            telegram('Raam living open om '.strftime("%k:%M:%S", TIME), false, 3);
        }
    }
}
