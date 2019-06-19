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
function createheader($page='')
{
    global $udevice;
    echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
    if ($page=='') {
        echo '
<html>';
    } else {
        echo '
<html manifest="floorplan.appcache">';
    }
    echo '
    <head>
		<title>Floorplan</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">';
    if ($udevice=='iPhone') {
        echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.655,user-scalable=yes,minimal-ui">';
    } elseif ($udevice=='iPad') {
        echo '
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.2,user-scalable=yes,minimal-ui">';
    }
    echo '
	    <link rel="manifest" href="/manifest.json">
	    <link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-startup-image" href="images/domoticzphp144.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php">
		<script type="text/javascript" src="/scripts/jQuery.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js"></script>';
    if ($page!='') {
        echo '
    <script type=\'text/javascript\'>
        function navigator_Go(url){window.location.assign(url);}
        $(document).ready(function(){initview();});
    </script>';
    }
    echo '
	</head>';
}