<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * This file sends the commands received by ajax to domoticz.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
require 'secure/authentication.php';
echo '<br><br><br><br><br><br>'.$_COOKIE[$cookie];
if ($home) {
    if (isset($_REQUEST['idx'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
        call_user_func($_REQUEST['command'],$_REQUEST['idx'],$_REQUEST['action']);
        lg('ajaxcontrol='.print_r($_REQUEST, true));
    }
}