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
    if (isset($_REQUEST['device'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
        $d=fetchdata();
        call_user_func($_REQUEST['command'],$_REQUEST['device'],$_REQUEST['action']);
    } elseif (isset($_REQUEST['bose'])&&isset($_REQUEST['command'])&&isset($_REQUEST['action'])) {
        lg($_REQUEST, true);
        if ($_REQUEST['command']=='volume') {
            bosevolume($_REQUEST['action'], $_REQUEST['bose']);
        } elseif ($_REQUEST['command']=='bass') {
            bosebass($_REQUEST['action'], $_REQUEST['bose']);
        } elseif ($_REQUEST['command']=='preset') {
            bosepreset($_REQUEST['action'], $_REQUEST['bose']);
        } elseif ($_REQUEST['command']=='skip') {
            if ($_REQUEST['action']=='prev') {
                bosekey("PREV_TRACK", 0, $bose);
            } elseif ($_REQUEST['action']=='next') {
                bosekey("NEXT_TRACK", 0, $bose);
            }
        } elseif ($_REQUEST['command']=='power') {
                if ($_REQUEST['action']=='On') {
                    bosekey("POWER", 0, $_REQUEST['bose']);
                    sw('bose'.$_REQUEST['bose'], 'On');
                } elseif ($_REQUEST['action']=='Off') {
                    bosekey("POWER", 0, $_REQUEST['bose']);
                    sw('bose'.$_REQUEST['bose'], 'Off');
                }
        }
    }
}