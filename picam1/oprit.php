<?php
include('../secure/settings.php');
require_once(dirname(__FILE__) . '/config.php');
echo '
<html><head>
<title>'.TITLE_STRING.'</title>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta name="HandheldFriendly" content="true"/><meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,initial-scale=0.5,minimal-ui" />
<link rel="icon" type="image/png" href="../images/Camera.png"/>
<link rel="shortcut icon" href="../images/Camera.png"/>
<link rel="apple-touch-startup-image" href="images/Camera.png"/>
<link rel="apple-touch-icon" href="../images/Camera.png"/>
<meta name="msapplication-TileColor" content="#ffffff"/>
<meta name="msapplication-TileImage" content="../images/Camera.png"/>
<meta name="msapplication-config" content="browserconfig.xml"/>
<meta name="mobile-web-app-capable" content="yes"/><link rel="manifest" href="manifest.json"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="theme-color" content="#000">
<link rel="stylesheet" href="../style.css"/>
<style type="text/css">body{margin:0 auto;}form,table{display:inline;margin:0px;padding:0px;}</style>
<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
</head>';
if(isset($_POST['Licht'])) file_get_contents("http://127.0.0.1:8084/json.htm?type=command&param=switchlight&idx=124&switchcmd=Toggle&passcode=");
if(isset($_POST['Rvoordeur'])) file_get_contents("http://192.168.0.11/fifo_command.php?cmd=record%20on%205%2055");
if(isset($_POST['Roprit'])) file_get_contents("http://192.168.0.12/fifo_command.php?cmd=record%20on%205%2055");
echo '
<div class="navbar" role="navigation">
<form method="POST" action="../floorplan.php"><input type="submit" value="Floorplan" class="btn" style="min-width:5em"/></form>
<form method="POST"><input type="submit" value="Licht" name="Licht" class="btn" style="min-width:5em"/></form>
<form method="POST" action="media-archive.php?type=videos&year='.date("Y").'&label= &m0='.date("n",time()-86400).'&d0='.date("j",time()-86400).'&m1='.date("n").'&d1='.date("j").'"><input type="submit" value="A Voordeur" class="btn" style="min-width:5em"/></form>
<form method="POST" action="../picam3/media-archive.php?type=videos&year='.date("Y").'&label= &m0='.date("n",time()-86400).'&d0='.date("j",time()-86400).'&m1='.date("n").'&d1='.date("j").'"><input type="submit" value="A Oprit" class="btn" style="min-width:5em"/></form>
<form method="POST"><input type="submit" value="Rvoordeur" name="Rvoordeur" class="btn" style="min-width:5em"/></form>
<form method="POST"><input type="submit" value="Roprit" name="Roprit" class="btn" style="min-width:5em"/></form>
</div>
<div class="clear"></div>';
if($udevice=='iPad') echo '<img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest" src="jpg.php"><img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest2" src="../picam2/jpg.php">';
else if($udevice=='iPhone') echo '<img style="width:896px;max-width:100%;height:auto" id="mjpeg_dest" src="jpg.php"><img style="width:896px;max-width:100%;height:auto" id="mjpeg_dest2" src="../picam2/jpg.php">';
else if($udevice=='Mac') echo '<img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest" src="jpg.php"><img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest2" src="../picam2/jpg.php">';
else if($udevice=='S4') echo '<img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest" src="jpg.php"><img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest2" src="../picam2/jpg.php">';
else if($udevice=='Stablet') echo '<img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest" src="jpg.php"><img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest2" src="../picam2/jpg.php">';
else echo '<img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest" src="jpg.php"><img style="width:896px;max-width:50%;height:auto" id="mjpeg_dest2" src="../picam2/jpg.php">';
