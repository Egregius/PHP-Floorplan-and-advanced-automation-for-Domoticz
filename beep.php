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
require 'secure/functions.php';
if ($d['Weg']['s']>0) {
    die('Slapen of niet thuis');
}
if (past('Xlight')<60) {
    die('To soon');
}
if (past('belknop')<60) {
    die('To soon');
}
if ($d['poortrf']['s']=='On') {
    die('Poort on');
}
if ($d['auto']['s']=='Off') {
    die('Meldingen uitgeschakeld');
}
if ($d['lgtv']['s']=='On') {
    shell_exec('python3 secure/lgtv.py -c send-message -a "Beweging oprit" 192.168.2.27');
}
if ($d['Xvol']['s']!=5) {
    sl('Xvol', 5);
}
sl('Xbel', 20);
rgb('Xlight', 57, 50);
