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
if (isset($_REQUEST['device'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
    require '/var/www/config.php';
    require 'secure/authentication.php';
    require 'secure/functions.php';
    if ($home==true) {
        lg('ajaxcontrol='.print_r($_REQUEST, true));
    }
}