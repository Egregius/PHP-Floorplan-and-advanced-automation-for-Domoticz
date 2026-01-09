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
$boses=array(
	101=>'Living',
	102=>'102',
	103=>'Boven',
	104=>'Garage',
	105=>'10-Wit',
	106=>'Buiten20',
	107=>'Keuken',
);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<link rel="preconnect" href="ws://192.168.2.22:9001">
	<link rel="dns-prefetch" href="//192.168.2.22">
	<title>Floorplan Bose</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<meta name="HandheldFriendly" content="true">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="theme-color" content="#000">
	<meta name="viewport" content="width=device-width,height=device-height,initial-scale=<?= $scale ?>,user-scalable=yes,minimal-ui">
	<meta name="msapplication-TileColor" content="#000000">
	<meta name="msapplication-TileImage" content="images/domoticzphp48.png">
	<link rel="icon" type="image/png" href="images/domoticzphp48.png">
	<link rel="shortcut icon" href="images/domoticzphp48.png">
	<link rel="apple-touch-startup-image" href="images/domoticzphp450.png">
	<link rel="apple-touch-icon" href="images/domoticzphp48.png">
	<link rel="stylesheet" type="text/css" href="/styles/floorplan.css">
	<style type="text/css">
		.btn{height:5vh;margin:3px;font-size:1.4em;}
		.b1{max-width:98vw;}
		.b2{margin:1px;}
		.input{width:78px;}
		.blackmedia{top:50px;left:0px;height:581px;width:100%;background-color:#000;text-align:center;}
		#clock{top:0px;left:0px;width:100vw;text-align:center;}
		#volume{width:99vw;}
		#bass{width:99vw;}
		.volume{width:8.4%;height:50px;padding:0!important;margin:0 0.3%;}
		#playlist{font-size:33px;top:4px;left:4px;font-weight:500;}
		#bose{font-size:33px;right:4px;font-weight:500;}
		#artist{font-size:1.2em;}
		#track{font-size:1.2em;}
	</style>
	<script src="/scripts/mqtt.min.js"></script>
	<script type="text/javascript" src="/scripts/floorplanjs.js?v=2"></script>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function() {
			ajaxbose('<?= $bose ?>');  // of gewoon de waarde van $bose als string
			myAjaxMedia = setInterval(function() {
				ajaxbose('<?= $bose ?>');
			}, 1000);
		});
	</script>
</head>
<body>
	<div class="fix" id="clock">
		<a href="javascript:navigator_Go('floorplan.bose.php?ip=<?= $bose ?>');" id="time"></a> 
	</div>
	<div class="fix" id="playlist">
		<a href="javascript:navigator_Go('floorplan.bose.php?ip=<?= $bose ?>');"><?= boseplaylist();?></a> 
	</div>
	<div class="fix" id="bose">
		<a href="avascript:navigator_Go('floorplan.bose.php?ip=<?= $bose ?>');"><?= $boses[$bose] ?></a> 
	</div>
	<div class="fix blackmedia bose" >
			<input type="hidden" name="ip" value="'.$bose.'">
			<div id="art"></div><br>
			<br>
			<br>
			<br>
			<h4 id="artist"></h4>
			<span id="track"></span><br>
			<div id="volume"></div>
			<br>
			<br>
			<div id="power"></div>
	</div>
	<button class="close-btn" onclick="javascript:navigator_Go('floorplan.php');">✕</button>
</body>
</html>';
