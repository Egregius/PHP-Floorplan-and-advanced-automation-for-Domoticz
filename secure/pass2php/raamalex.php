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
if (TIME>strtotime('6:00')) {
    storemode('Ralex', 0);
}
if ($status=='Open') {
    $d['raamalex']['s']='Open';
    $d['Ralex']['m']=0;
    include '_rolluiken.php';
}