<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home===true) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="HandheldFriendly" content="true" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="viewport" content="width=device-width,height=device-width, initial-scale=0.5, user-scalable=yes, minimal-ui" />
	<title>Denon</title>
	<link rel="icon" type="image/png" href="images/denon.png">
	<link rel="shortcut icon" href="images/denon.png" />
	<link rel="apple-touch-icon" href="images/denon.png"/>
	<link rel="icon" sizes="196x196" href="images/denon.png">
	<link rel="icon" sizes="192x192" href="images/denon.png">
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="manifest" href="/manifests/denon.json">
	<link href="images/denon.png" media="(device-width: 320px)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\', 4950);</script>
	<link href="/styles/denon.css" rel="stylesheet" type="text/css"/>
  </head>
<body>';
if (isset($_POST['poweroff'])) {
	denon('PWSTANDBY');
} elseif (isset($_POST['action'])) {
	denon($_POST['action']);
	denon($_POST['action']);
}
?>
<div class="navbar">
	<form action="/floorplan.php"><input type="submit" class="btn b5" value="Plan"/></form>
	<form action="/denon.php"><input type="submit" class="btn btna b5" value="Denon"/></form>
	<form action="/kodi.php"><input type="submit" class="btn b5" value="Kodi"/></form>
	<form action="/films/films.php"><input type="submit" class="btn b5" value="Films"/></form>
	<form action="/films/series.php"><input type="submit" class="btn b5" value="Series"/></form>
</div>
<div class="content">
<form method="POST">
<div class="box" style="width:96%">
	<table width="100%">
	<tr><td width="30%"></td><td width="30%"></td><td width="30%"></td></tr>
	<tr><td align="right"><b>Dialoog</b><td ><button name="action" value="CVC UP" class="btn" style="width:100%">UP</button></td><td><button name="action" value="CVC DOWN" class="btn" style="width:100%">DOWN</button></td></tr>
	<tr><td></td><td></td><td></td></tr>
	<tr><td align="right"><b>Subwoofer</b><td ><button name="action" value="CVSW UP" class="btn" style="width:100%">UP</button></td><td><button name="action" value="CVSW DOWN" class="btn" style="width:100%">DOWN</button></td></tr>
	<tr><td></td><td></td><td></td></tr>
	<tr><td align="right"><b>Front L</b><td ><button name="action" value="CVFL UP" class="btn" style="width:100%">UP</button></td><td><button name="action" value="CVFL DOWN" class="btn" style="width:100%">DOWN</button></td></tr>
	<tr><td></td><td></td><td></td></tr>
	<tr><td align="right"><b>Front R</b><td ><button name="action" value="CVFR UP" class="btn" style="width:100%">UP</button></td><td><button name="action" value="CVFR DOWN" class="btn" style="width:100%">DOWN</button></td></tr>
	<tr><td></td><td></td><td></td></tr>
	<tr><td align="right"><b>Rear L</b><td ><button name="action" value="CVSL UP" class="btn" style="width:100%">UP</button></td><td><button name="action" value="CVSL DOWN" class="btn" style="width:100%">DOWN</button></td></tr>
	<tr><td></td><td></td><td></td></tr>
	<tr><td align="right"><b>Rear R</b><td ><button name="action" value="CVSR UP" class="btn" style="width:100%">UP</button></td><td><button name="action" value="CVSR DOWN" class="btn" style="width:100%">DOWN</button></td></tr>
	<tr><td></td><td></td><td></td></tr>
	</table>
</div>
<div class="box" style="width:96%">
	<table width="100%">
	<tr><td width="30%"></td><td width="30%"></td><td width="30%"></td></tr>
	<tr><td align="right"><b>Bass</b><td ><button name="action" value="PSBAS UP" class="btn" style="width:100%">UP</button></td><td><button name="action" value="PSBAS DOWN" class="btn" style="width:100%">DOWN</button></td></tr>
	<tr><td></td><td></td><td></td></tr>
	<tr><td align="right"><b>Trebble</b><td ><button name="action" value="PSTRE UP" class="btn" style="width:100%">UP</button></td><td><button name="action" value="PSTRE DOWN" class="btn" style="width:100%">DOWN</button></td></tr>
	<tr><td></td><td></td><td></td></tr>
	<tr><td align="right"><b>LFE</b><td ><button name="action" value="PSLEE UP" class="btn" style="width:100%">UP</button></td><td><button name="action" value="PSLEE DOWN" class="btn" style="width:100%">DOWN</button></td></tr>
	<tr><td></td><td></td><td></td></tr>
	</table>
</div>
<div class="box">
	<button name="poweroff" value="poweroff" class="btn b1">Power Off</button>
</div>
</form>
</body>
</html>
<?php
}
