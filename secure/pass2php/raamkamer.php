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
if ($status=='Open'&&TIME>strtotime('5:00')&&TIME<strtotime('21:00')) {
    if ($d['RkamerL']['m']!=0) storemode('RkamerL', 0, basename(__FILE__).':'.__LINE__);
    if ($d['RkamerR']['m']!=0) storemode('RkamerR', 0, basename(__FILE__).':'.__LINE__);
}
if ($status=='Open') {
    $d['raamkamer']['s']='Open';
    $d['RkamerL']['m']=0;
    $d['RkamerR']['m']=0;
    include '_rolluiken.php';
}