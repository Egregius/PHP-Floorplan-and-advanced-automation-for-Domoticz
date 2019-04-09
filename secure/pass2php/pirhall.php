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
if ($status=='On'&&$d['auto']['s']) {
    if ($d['Weg']['s']==0&&$d['hall']['s']=='Off'&&(TIME<strtotime('7:00')||$d['zon']['s']<$zoninkom)) {
        sw('hall', 'On');
    }
    if ($d['Weg']['s']==2&&$d['Weg']['m']>TIME-178) {
        sw('sirene', 'On');
        shell_exec('../ios.sh "Beweging Hall" > /dev/null 2>/dev/null &');
        telegram('Beweging hall om '.strftime("%k:%M:%S", TIME), false, 2);
    }

    storemode('Weg', TIME);
}
lgsql('pir',$device,$status);