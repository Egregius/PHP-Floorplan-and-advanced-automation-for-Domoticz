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
if ($status!="Open") {
    if ($d['Weg']['s']>0&&$d['auto']['s']==1&&$d['Weg']['m']>TIME-178) {
            sw('sirene', 'On');
            telegram('Achterdeur open om '.strftime("%k:%M:%S", TIME), false, 3);
    }
} else {
    if ($d['dampkap']['s']=='On') {
        sw('dampkap', 'Off');
    }
    //telegram('Achterdeur toe om '.strftime("%k:%M:%S", TIME));
}