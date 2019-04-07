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
require 'secure/settings.php';
require "scripts/chart.php";
$domoticz=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=devices&used=true&plan=9'), true);
if ($domoticz['result']) {
    foreach ($domoticz['result'] as $dom) {
        $idx=$dom['idx'];
        $name=$dom['Name'];
        $dev=json_decode(file_get_contents('http://127.0.0.1:8080/json.htm?type=graph&sensor=counter&idx='.$idx.'&range=year'), true);
        //echo '<pre>';print_r($dev);echo '</pre>';
        $array[$name]=$dev;
    }
}
//echo '<hr><pre>';print_r($array);echo '</pre>';
foreach ($array as $key=>$result) {
    echo $key.'<br>';print_r($result['result']);echo '<hr>';
}