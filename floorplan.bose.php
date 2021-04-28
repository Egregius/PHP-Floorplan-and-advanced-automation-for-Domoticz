<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * A small control interface for the Bose SoundTouch system
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
require 'secure/functions.php';
require '/var/www/authentication.php';
if (!isset($_SESSION['referer'])) {
	$_SESSION['referer']='floorplan.php';
}
if (isset($_REQUEST['ip'])) {
	$bose=str_replace('bose', '', $_REQUEST['ip']);
} else {
	$bose=101;//Living
}
echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>Floorplan</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="HandheldFriendly" content="true">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">';
if ($udevice=='iPhone') {
	echo '
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.655,user-scalable=yes,minimal-ui">';
} elseif ($udevice=='iPhoneSE') {
	echo '
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.8,user-scalable=yes,minimal-ui">';
} elseif ($udevice=='iPad') {
	echo '
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.2,user-scalable=yes,minimal-ui">';
}
echo '
	<meta name="msapplication-TileColor" content="#000000">
	<meta name="msapplication-TileImage" content="images/domoticzphp48.png">
	<meta name="theme-color" content="#000000">
	<link rel="icon" type="image/png" href="images/domoticzphp48.png">
	<link rel="shortcut icon" href="images/domoticzphp48.png">
	<link rel="apple-touch-startup-image" href="images/domoticzphp450.png">
	<link rel="apple-touch-icon" href="images/domoticzphp48.png">
	<link rel="stylesheet" type="text/css" href="/styles/floorplan.css">
	<style type="text/css">
		.btn{height:48px;margin:3px;}
		.b2{margin:1px;}
		.input{width:78px;}
		.blackmedia{top:50px;left:0px;height:581px;width:490px;background-color:#000;text-align:center;}
	</style>
	<script type="text/javascript" src="/scripts/jQuery.js"></script>
	<script type="text/javascript" src="/scripts/floorplanjs.js"></script>
	<script type="text/javascript">
		function navigator_Go(url){window.location.assign(url)}
		$(document).ready(function() {
			ajaxbose('.$bose.')
			myAjaxMedia=setInterval(function(){ajaxbose('.$bose.')},500)
		});
	</script>
</head>
<body>
	<div class="fix" id="clock">
		<a href=\'javascript:navigator_Go("floorplan.bose.php?ip='.$bose.'");\' id="time">
		</a>
	</div>
	<div class="fix z1" style="top:5px;left:5px;">
		<a href=\'javascript:navigator_Go("floorplan.php");\'>
			<img src="/images/close.png" width="72px" height="72px" alt="close">
		</a>
	</div>
	<div class="fix blackmedia" >
			<input type="hidden" name="ip" value="'.$bose.'">
			<div style="height:180px;" id="art"></div>
			<h4 id="artist"></h4>
			<span id="track"></span><br>
			<div id="volume"></div>
			<div id="bass"></div>
			<br>
			<br>
			<div id="power"></div>'.$udevice.'
	</div>
</body>
</html>';
