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
$domoticz=@json_decode(@file_get_contents('http://127.0.0.1:8080/json.htm?type=devices&rid=617'), true);
if (isset($domoticz['result'][0]['Level'])) {
    store('luifel', $domoticz['result'][0]['Level']);
}