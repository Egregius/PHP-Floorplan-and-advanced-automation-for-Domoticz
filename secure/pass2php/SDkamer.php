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
if ($status=='On') {
    $msg='Rook gedecteerd in slaapkamer!';
    alert($device, 	$msg, 	300, false, 2, true);
    $items=array(/*'Ralex',*/'Rtobi','RkamerL','RkeukenL','RkamerR','Rliving','RkeukenR','Rbureel');
    foreach ($items as $i) {
        if ($d[$i]['s']>0) {
        	sl($i, 0, basename(__FILE__).':'.__LINE__);
        }
    }
    $items=array('hall','inkom','kamer','tobi',/*'alex',*/'eettafel','zithoek','lichtbadkamer');
    foreach ($items as $i) {
        if ($d[$i]['s']<100) {
        	sl($i, 100, basename(__FILE__).':'.__LINE__);
        }
    }
    $items=array('keuken','garage','jbl','bureel');
    foreach ($items as $i) {
        if ($d[$i]['s']!='On') {
        	sw($i, 'On', basename(__FILE__).':'.__LINE__);
        }
    }
    sleep(10);
    resetsecurity();
}