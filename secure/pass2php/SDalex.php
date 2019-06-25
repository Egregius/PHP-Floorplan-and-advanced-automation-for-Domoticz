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
if ($status=='On') {
    $msg='Rook gedecteerd in kamer Alex!';
    alert($device, $msg, 300, false, 2, true);
    $items=array('Ralex','Rtobi','RkamerL','RkeukenL','RkamerR','Rliving','RkeukenR','Rbureel');
    foreach ($items as $i) {
        sl($i, 0);
    }
    $items=array('kamer','tobi','alex','eettafel','zithoek','lichtbadkamer');
    foreach ($items as $i) {
        sl($i, 100);
    }
    $items=array('hall','inkom','keuken','garage','jbl','bureel');
    foreach ($items as $i) {
        sw($i, 'On');
    }
    sleep(10);
    resetsecurity();
}