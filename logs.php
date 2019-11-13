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
require 'secure/functions.php';
require 'secure/authentication.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Logs</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/><meta name="HandheldFriendly" content="true"/><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black"><meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/><link rel="icon" type="image/png" href="images/domoticzphp48.png"/><link rel="shortcut icon" href="images/domoticzphp48.png"/><link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/><link rel="apple-touch-icon" href="images/domoticzphp48.png"/><meta name="msapplication-TileColor" content="#ffffff"><meta name="msapplication-TileImage" content="images/domoticzphp48.png"><meta name="msapplication-config" content="browserconfig.xml"><meta name="mobile-web-app-capable" content="yes"><link rel="manifest" href="manifest.json"><meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" type="text/css" href="/styles/floorplan.php">
    <style>
        .btn{min-height:75px;}
    </style>
</head>
    <body>
        <div class="fix z1" style="top:5px;left:5px;">
            <a href="javascript:navigator_Go('floorplan.php');">
                <img src="/images/close.png" width="72px" height="72px"/>
            </a>
        </div>
        <div class="fix box box1" style="top:120px;left:0px;width:100%">
            <a href="javascript:navigator_Go('log.php#Domoticz');" class="btn b2">Domoticz</a>
            <a href="javascript:navigator_Go('log.php#Ajax');" class="btn b2">Ajax</a>
            <a href="javascript:navigator_Go('log.php#www error');" class="btn b2">www error</a>
            <a href="javascript:navigator_Go('log.php#Fail2Ban');" class="btn b2">Fail2Ban</a>
        </div>
        <div class="clear">
        </div>
        <div class="fix box box1" style="bottom:0px;left:0px;width:100%">
        	<?php echo ''.$udevice.'<br>'.$_SERVER['HTTP_USER_AGENT']; ?>
        </div>
        <script type="text/javascript">
            function navigator_Go(url) {window.location.assign(url);}
        </script>
    </body>
</html>
