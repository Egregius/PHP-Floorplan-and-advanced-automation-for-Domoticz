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
    $msg='Rook gedecteerd in kamer Alex!';
    shell_exec("../ios.sh '$msg' > /dev/null 2>/dev/null &");
    telegram($msg, false, 2);
    $items=array('Ralex','Rtobi','RkamerL','RkeukenL','RkamerR','Rliving','RkeukenR','Rbureel');
    foreach ($items as $i) {
        sl($i, 0, true);
    }
    $items=array('kamer','tobi','alex','eettafel','zithoek','lichtbadkamer');
    foreach ($items as $i) {
        sl($i, 100, true);
    }
    $items=array('hall','inkom','keuken','garage','jbl','bureel');
    foreach ($items as $i) {
        sw($i, 'On', true);
    }
    sleep(10);
    resetsecurity();
}