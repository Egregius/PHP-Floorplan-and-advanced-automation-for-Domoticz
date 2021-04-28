<?php //Alex
require('../secure/functions.php');
$_SESSION['referer']='picam2/index.php';
require('/var/www/authentication.php');
$d=fetchdata();
require(dirname(__FILE__) . '/config.php');
echo '<html><head><title>'.TITLE_STRING.'</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="HandheldFriendly" content="true"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,initial-scale=0.5,minimal-ui" />
<link rel="icon" type="image/png" href="/images/Camera.png"/>
<link rel="shortcut icon" href="/images/Camera.png"/>
<link rel="apple-touch-icon" href="/images/Camera.png"/>
<meta name="mobile-web-app-capable" content="yes"/>
<link href="/styles/picam2.php" rel="stylesheet" type="text/css"/>
</head><body>';
if (isset($_POST['Record'])) {
	shell_exec('curl -s "http://192.168.2.12/fifo_command.php?cmd=record%20on%205%2055" >/dev/null 2>/dev/null &');
} elseif (isset($_POST['Foto'])) {
	shell_exec('curl -s "http://192.168.2.12/telegram.php?snapshot=true" >/dev/null 2>/dev/null &');
} elseif (isset($_POST['Off'])) {
	shell_exec('curl -s "http://127.0.0.1/secure/pass2php/Ralex.php?status=0" >/dev/null 2>/dev/null &');
	echo '<script type="text/javascript">
	window.location.replace("/floorplan.php");
	</script>';
} elseif (isset($_POST['On'])) {
	sw('picam2plug', 'On');
}
echo '<div class="navbar" role="navigation">
		<form method="POST" action="../floorplan.php">
	  <input type="submit" value="Plan" class="btn b5"/>
	</form>
	<form method="POST">
	  <input type="submit" value="Record" name="Record" class="btn b5"/>
	  <input type="submit" value="Foto" name="Foto" class="btn b5"/>
	  <input type="submit" value="Refresh" name="Refresh" class="btn b5"/>';
if ($d['picam2plug']['s']=='On') {
	echo '
	  <input type="submit" value="Off" name="Off" class="btn b7"/>';
} else {
	echo '
	  <input type="submit" value="On" name="On" class="btn b7"/>';
}
echo '
	</form>
	</div>
	<div class="fix camera">';
if ($d['picam2plug']['s']=='On') {
	echo '
		<img class="camerai" id="mjpeg_dest" src="jpg.php"/>';
} else {
	echo 'Camera uit';
}
echo '
	</div>
	<script type="text/javascript">
	for (var i = 1; i < 99999; i++){
		try{window.clearInterval(i);}catch{};
	}
	mypicam=setInterval(getpic, 500);
	function getpic(){
		document.getElementById(\'mjpeg_dest\').src = "jpg.php?random="+new Date().getTime();
	}
	</script></body></html>
';

