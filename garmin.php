<?php
/**
 * Pass2PHP
 * php version 7.3.9-1
 *
 * This file is called by a widget on a Garmin Fenix 6X
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
if (isset($_REQUEST['token'])&&$_REQUEST['token']==$garmintoken) {
    echo 'OK';
    
}