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
	if (isset($_REQUEST['easymode'])) {
		store('easymode', $_REQUEST['easymode']);
	} elseif (isset($_REQUEST['humidity'])) {
		store('easy_humidity', str_replace('%', '', $_REQUEST['humidity']));
	}
	unset($_GET['token']);
	telegram('ifttt'.PHP_EOL.print_r($_GET, true));
	echo 'OK';
}
