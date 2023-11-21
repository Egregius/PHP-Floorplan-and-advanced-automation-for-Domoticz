<?php
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
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="theme-color" content="#000">';
if ($ipaddress=='192.168.2.202'||$ipaddress=='192.168.4.3')  { //Aarde
	echo '
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.868,user-scalable=yes,minimal-ui">';
} elseif ($ipaddress=='192.168.2.203'||$ipaddress=='192.168.4.4'||$udevice=='iPad')  { //iPad
	echo '
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,user-scalable=yes,minimal-ui">';
} elseif ($ipaddress=='192.168.2.23'||$ipaddress=='192.168.4.5')  { //iPhone Kirby
	echo '
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.755,user-scalable=yes,minimal-ui">';
} elseif ($udevice=='iPhone') {
	echo '
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.755,user-scalable=yes,minimal-ui">';
} else {
	echo '
	<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui">';
}
echo '
	<meta name="msapplication-TileColor" content="#000000">
	<meta name="msapplication-TileImage" content="images/domoticzphp48.png">
	<link rel="icon" type="image/png" href="images/domoticzphp48.png">
	<link rel="shortcut icon" href="images/domoticzphp48.png">
	<link rel="apple-touch-startup-image" href="images/domoticzphp450.png">
	<link rel="apple-touch-icon" href="images/domoticzphp48.png">
	<link rel="stylesheet" type="text/css" href="/styles/floorplan.css?v=2">
	<style type="text/css">
		.btn{height:5vh;margin:3px;}
		.b1{max-width:98vw;}
		.b2{margin:1px;}
		.input{width:78px;}
		.blackmedia{top:50px;left:0px;height:581px;width:100%;background-color:#000;text-align:center;}
		#clock{top:0px;left:0px;width:100vw;text-align:center;}
		#volume{width:99vw;}
		#bass{width:99vw;}
		.volume{width:8.4%;height:60px;padding:0!important;margin:0 0.3%;}

	</style>
	<script type="text/javascript" src="/scripts/jQuery.js"></script>
	<script type="text/javascript" src="/scripts/floorplanjs.js?v=2"></script>
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
		<a href=\'javascript:navigator_Go("floorplan.bose.php?ip='.$bose.'");\' id="time"></a>
	</div>
	<div class="fix z1" style="bottom:14px;left:14px;">
		<a href=\'javascript:navigator_Go("floorplan.php");\'><img src="/images/close.png" width="72px" height="72px" alt="close"></a>
	</div>
	<div class="fix blackmedia bose" >
			<input type="hidden" name="ip" value="'.$bose.'">
			<div style="height:180px;" id="art"></div>
			<h4 id="artist"></h4>
			<span id="track"></span><br>
			<div id="volume"></div>
			<div id="bass"></div>
			<br>
			<br>
			<div id="power"></div>
	</div>
</body>
</html>';
