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
if (isset($_REQUEST['token'])&&isset($_REQUEST['action'])&&$_REQUEST['token']==$garmintoken) {
    echo 'OK';
    if ($_REQUEST['action']=='weg') store('Weg', 2, basename(__FILE__).':'.__LINE__);
    elseif ($_REQUEST['action']=='thuis') store('Weg', 0, basename(__FILE__).':'.__LINE__);
    elseif ($_REQUEST['action']=='slapen') store('Weg', 1, basename(__FILE__).':'.__LINE__);
    elseif ($_REQUEST['action']=='poortrf') {
    	store('Weg', 0, basename(__FILE__).':'.__LINE__);
    	sw('poortrf', 'On', basename(__FILE__).':'.__LINE__);
    }
}