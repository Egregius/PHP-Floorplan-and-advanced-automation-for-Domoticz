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
/*if ($status=='On'&&$d['auto']['s']=='On'&&past('belknop')>10) {
    //shell_exec('../ios.sh "Deurbel" > /dev/null 2>/dev/null &');
    shell_exec('../boseplayinfo.sh "Deurbel" > /dev/null 2>/dev/null &');
    if ($d['lgtv']['s']=='On') {
        shell_exec('python3 ../lgtv.py -c send-message -a "Deurbel" 192.168.2.27');
    }
    //shell_exec('curl -s "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
    //shell_exec('curl -s "http://192.168.2.11/telegram.php?deurbel=true" > /dev/null 2>/dev/null &');
    //shell_exec('curl -s "http://192.168.2.13/telegram.php?snapshot=true" > /dev/null 2>/dev/null &');
    if ($d['zon']['s']==0) {
        sw('voordeur', 'On');
    }
    if ($d['Weg']['s']==0) {
        if ($d['Xvol']['s']!=40) {
            sl('Xvol', 40);
            usleep(10000);
        }
        sl('Xbel', 10);
        sw('deurbel', 'On');
        rgb('Xlight', 360, 100);
    }
    telegram('Deurbel', true, 2);
    sleep(2);
    sl('Xvol', 5);
}*/
echo shell_exec('../boseplayinfo.sh "Deurbel" > /dev/null 2>/dev/null &');