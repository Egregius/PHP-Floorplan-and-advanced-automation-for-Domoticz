<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * This is a receiver for ifttt webhooks.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
if ($_SERVER['HTTP_X_FORWARDED_FOR']==$vpsip&&isset($_GET['token'])&&$_GET['token']==$ifttttoken) {
	if (isset($_GET['easymode'])) {
		store('easymode', $_GET['easymode']);
	} elseif (isset($_GET['humidity'])) {
		store('easy_humidity', str_replace('%', '', $_GET['humidity']));
	} elseif (isset($_GET['livingtemp'])) {
		store('easy_temp', str_replace('\xb0C', '', $_GET['livingtemp']));
	}
	unset($_GET['token']);
	//telegram('ifttt'.PHP_EOL.print_r($_GET, true));
	echo 'OK';
}
