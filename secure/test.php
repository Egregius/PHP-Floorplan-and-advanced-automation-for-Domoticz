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
$start=microtime(true);
require 'functions.php';
//error_reporting(E_ALL);
//ini_set("display_errors", "on");
echo '<pre>';
/*-------------------------------------------------*/
echo file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.GetCurrentProfile","id":1}', false);
/*---------------------------*/
echo '</pre>';
$total=microtime(true)-$start;
echo '<hr>Time:'.number_format(((microtime(true)-$start)*1000), 6);
unset($_COOKIE, $_GET, $_POST, $_FILES, $_SERVER, $start, $total, $users, $homes, $telegrambot, $smappeeclient_id, $smappeeclient_secret, $smappeeusername, $smappeepassword, $smappeeserviceLocationId, $LogFile, $cookie, $telegramchatid, $telegramchatid2, $smappeeip, $authenticated, $zongarage, $zonkeuken, $zoninkom, $zonmedia, $Usleep, $eendag, $Weg, $home, $log, $offline, $page, $udevice, $local, $user, $ipaddress, $timediff);
echo '<hr><hr><hr><pre>';print_r(GET_DEFINED_VARS());echo '</pre>';



function Human_kb($bytes,$dec=2)
{
    $size=array('kb','Mb','Gb');
    $factor=floor((strlen($bytes)-1)/3);
    return sprintf("%.{$dec}f", $bytes/pow(1000, $factor)).@$size[$factor];
}

function Get_data($url)
{
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.17 Safari/537.36');
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
