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
if ($status=="On"&&$d['auto']['s']=='On') {
    if (TIME<strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']<$zonkeuken||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        sw('keuken', 'On');
    } elseif (TIME>=strtotime('20:00')&&$d['Weg']['s']==0&&$d['keuken']['s']=='Off'&&$d['wasbak']['s']=='Off'&&$d['werkblad1']['s']=='Off'&&$d['kookplaat']['s']=='Off'&&($d['zon']['s']<$zonkeuken||($d['RkeukenL']['s']>70&&$d['RkeukenR']['s']>70))) {
        if ($d['tv']['s']=='On'||$d['jbl']['s']=='On') {
            sw('keuken', 'On');
        }
    }
    if ($d['Weg']['s']>0&&$d['Weg']['m']>TIME-178) {
        sw('sirene', 'On');
        shell_exec('../ios.sh "Beweging Keuken" > /dev/null 2>/dev/null &');
        telegram('Beweging keuken om '.strftime("%k:%M:%S", TIME), false, 2);
    }
    storemode('Weg', TIME);
}