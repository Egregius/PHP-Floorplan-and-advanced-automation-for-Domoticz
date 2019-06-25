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
if ($status=='Open'&&TIME>strtotime('5:00')&&TIME<strtotime('21:00')) {
    storemode('RkamerL', 0, basename(__FILE__).':'.__LINE__);
    storemode('RkamerR', 0, basename(__FILE__).':'.__LINE__);
}
if ($status=='Open') {
    $d['raamkamer']['s']='Open';
    $d['RkamerL']['m']=0;
    $d['RkamerR']['m']=0;
    include '_rolluiken.php';
}