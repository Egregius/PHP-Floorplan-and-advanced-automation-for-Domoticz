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
if ($status=="Open"&&$d['auto']['s']=='On') {
    if ($d['Weg']['s']==0&&$d['inkom']['s']=='Off'&&$d['zon']['s']<$zoninkom) {
        sw('inkom', 'On');
    }
    if ($d['Weg']['s']>0&&$d['Weg']['m']>TIME-178) {
        sw('sirene', 'On');
        shell_exec('../ios.sh "Deur inkom" > /dev/null 2>/dev/null &');
        telegram('Deur inkom open om '.strftime("%k:%M:%S", TIME), false, 2);
    }
    storemode('Weg', TIME);
}