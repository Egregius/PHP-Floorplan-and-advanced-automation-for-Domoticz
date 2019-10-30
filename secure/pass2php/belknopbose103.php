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
include '../functions.php';
$volume=json_decode(json_encode(simplexml_load_string(file_get_contents('http://192.168.2.103:8090/volume'))), true);
$cv=$volume['actualvolume'];
if ($cv<55) {
	usleep(400000);
	bosevolume(55, 103);
	usleep(3000000);
	bosevolume($cv, 103);
}