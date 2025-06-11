<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
require '../secure/functions.php';
$_SESSION['referer']='picam1/index.php';
require '/var/www/authentication.php';
if(isset($_REQUEST['Record'])){
	file_get_contents("http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055");
	file_get_contents("http://192.168.2.12/fifo_command.php?cmd=record%20on%205%2055");
	exit;
}elseif(isset($_REQUEST['Foto'])){
	shell_exec('curl -s "http://192.168.2.11/telegram.php?snapshot=true" &');
	shell_exec('curl -s "http://192.168.2.12/telegram.php?snapshot=true" &');
	exit;
}elseif(isset($_REQUEST['Motion'])){
	shell_exec('curl -s "http://192.168.2.11/fifo_command.php?cmd=motion_enable%20toggle" &');
	shell_exec('curl -s "http://192.168.2.12/fifo_command.php?cmd=motion_enable%20toggle" &');
	exit;
}
$refresh=1500;
//if ($_SERVER['REMOTE_ADDR']=='192.168.2.201') $refresh=500;
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJmMTQ1ZThmNjYyNTk0Mjk5OWM2ZTUyMWNhZWY3MTUxYSIsImlhdCI6MTc0ODQwMDM0OCwiZXhwIjoyMDYzNzYwMzQ4fQ.SDUxztRFwr9p7w29LQ-_fDa5l4KB1cOTrz_riHQCFlY";
$ha_url = "http://192.168.2.26:8123/api/services/button/press";

// Pas dit aan naar jouw button-entity_id
$data = [
    "entity_id" => "button.start_p2p_stream"
];

$options = [
    "http" => [
        "method"  => "POST",
        "header"  => "Authorization: Bearer $token\r\nContent-Type: application/json\r\n",
        "content" => json_encode($data),
        "timeout" => 5
    ]
];
$context = stream_context_create($options);
$response = @file_get_contents($ha_url, false, $context);
echo '<html>
<head><title>Oprit</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="HandheldFriendly" content="true"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="theme-color" content="#000">
<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,initial-scale=1,minimal-ui" />
<link rel="icon" type="image/png" href="/images/Camera.png"/>
<link rel="shortcut icon" href="/images/Camera.png"/>
<link rel="apple-touch-icon" href="/images/Camera.png"/>
<meta name="mobile-web-app-capable" content="yes"/>
<link href="/styles/picam1.css?v=2" rel="stylesheet" type="text/css"/>
</head>
<body>';

echo '<div class="navbar" role="navigation">
	<form method="POST" action="../floorplan.php">
	  <input type="submit" value="Plan" class="btn b7" />
	</form>
	<form method="POST">
	  <input type="submit" value="Record" name="Record" class="btn b8" onclick="event.preventDefault();record();"/>
	  <input type="submit" value="Foto" name="Foto" class="btn b8" onclick="event.preventDefault();foto();"/>
	  <input type="submit" value="Licht" name="Licht" class="btn b8" onclick="event.preventDefault();licht();"/>
	  <input type="submit" value="Motion" name="Motion" class="btn b8" onclick="event.preventDefault();motion();"/>
	  <input type="submit" value="Refresh" name="Refresh" class="btn b8"/>
	</form>
	<form method="POST" action="media-archive.php">
		<input type="hidden" name="type" value="videos"/>
		<input type="hidden" name="year" value="'.date("Y").'"/>
		<input type="hidden" name="m0" value="'.date("n",time()-86400).'"/>
		<input type="hidden" name="d0" value="'.date("j",time()-86400).'"/>
		<input type="hidden" name="m1" value="'.date("n").'"/>
		<input type="hidden" name="d1" value="'.date("j").'"/>
		<input type="submit" value="Archief" name="Archief" class="btn b8"/>
	</form>
	</div>
	<div class="camera1">
		<img class="camerai" id="mjpeg_camera1" src="jpg.eufy.php"/>
	</div>
	<div class="camera2">
		<img class="camerai" id="mjpeg_camera2" src="jpg.php"/>
	</div>
	<div class="camera3">
		<img class="camerai" id="mjpeg_camera3" src="jpg.oprit.php"/>
	</div>
	
	
	<script type="text/javascript" src="/scripts/m4q.min.js"></script>
	<script type="text/javascript">
		function navigator_Go(url) {window.location.assign(url);}
		mycam1=setInterval(getpic1, '.$refresh.');
		mycam2=setInterval(getpic2, '.$refresh.');
		mycam3=setInterval(getpic3, '.$refresh.');
		function getpic1(){
			try{document.getElementById(\'mjpeg_camera1\').src = "jpg.eufy.php?random="+new Date().getTime();}catch{}
		}
		function getpic2(){
			try{document.getElementById(\'mjpeg_camera2\').src = "jpg.php?random="+new Date().getTime();}catch{}
		}
		function getpic3(){
			try{document.getElementById(\'mjpeg_camera3\').src = "jpg.oprit.php?random="+new Date().getTime();}catch{}
		}
		function licht(){
			$.get("/ajax.php?device=voordeur&command=sw&action=Toggle")
			return false
		}
		function record(){
			$.get("?Record")
			return false
		}
		function foto(){
			$.get("?Foto")
			return false
		}
		function motion(){
			$.get("?Motion")
			return false
		}
	</script>
	</body></html>
';
