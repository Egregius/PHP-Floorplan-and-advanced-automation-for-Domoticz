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
if ($status=='Open'&&TIME>strtotime('6:00')&&TIME<strtotime('21:00')) {
    storemode('Rtobi', 0, basename(__FILE__).':'.__LINE__);
}
if ($status=='Open') {
    $d['raamtobi']['s']='Open';
    $d['Rtobi']['m']=0;
    include '_rolluiken.php';
}