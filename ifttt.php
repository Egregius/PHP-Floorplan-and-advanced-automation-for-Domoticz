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
if ($_SERVER['HTTP_X_FORWARDED_FOR']==$vpsip&&isset($_REQUEST['token'])&&$_REQUEST['token']==$ifttttoken) {
	if (isset($_REQUEST['easymode'])) {
		store('easymode', $_REQUEST['easymode']);
	}
	echo 'OK';
}
telegram('ifttt GET'.PHP_EOL.print_r($_GET, true));