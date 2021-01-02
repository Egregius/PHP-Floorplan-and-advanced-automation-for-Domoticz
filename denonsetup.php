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
	if (isset($_POST['action'])) {
		denon($_POST['action']);
		exit;
	}
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="HandheldFriendly" content="true" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="viewport" content="width=device-width,height=device-width, initial-scale=1, user-scalable=no, minimal-ui" />
	<title>Denon</title>
	<link rel="icon" type="image/png" href="images/denon.png">
	<link rel="shortcut icon" href="images/denon.png" />
	<link rel="apple-touch-icon" href="images/denon.png"/>
	<link rel="icon" sizes="196x196" href="images/denon.png">
	<link rel="icon" sizes="192x192" href="images/denon.png">
	<meta name="mobile-web-app-capable" content="yes">
	<link href="images/denon.png" media="(device-width: 320px)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	<script type="text/javascript">
		function navigator_Go(url) {window.location.assign(url);}
		function exec(cmd, action=""){
			$.post("denonsetup.php",
			{
				cmd : cmd,
				action : action
			})
		}
	</script>
	<script language="javascript" type="text/javascript" src="/scripts/jquery.2.0.0.min.js"></script>
	<link href="/styles/denon.css?v='.TIME.'" rel="stylesheet" type="text/css"/>
</head>
<body>';

?>
<div class="navbar">
	<form action="/floorplan.php"><input type="submit" class="btn b5" value="Plan"/></form>
	<form action="/denon.php"><input type="submit" class="btn btna b5" value="Denon"/></form>
	<form action="/kodi.php"><input type="submit" class="btn b5" value="Kodi"/></form>
	<form action="/films/films.php"><input type="submit" class="btn b5" value="Films"/></form>
	<form action="/films/series.php"><input type="submit" class="btn b5" value="Series"/></form>
</div>
<div class="content">
<div class="box" style="width:96%">
	<table width="100%">
	<tr>
		<td width="30%"></td>
		<td width="30%"></td>
		<td width="30%"></td>
	</tr>
	<tr>
		<td align="right"><b>Dialoog</b><td >
		<button class="btn" style="width:100%" onclick="exec('action','CVC UP');">UP</button></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','CVC DOWN');">DOWN</button></td>
	</tr>
	<tr><td></td><td></td><td></td></tr>
	<tr>
		<td align="right"><b>Subwoofer</b><td >
		<button class="btn" style="width:100%" onclick="exec('action','CVSW UP');">UP</button></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','CVSW DOWN');">DOWN</button></td>
	</tr>
	<tr><td></td><td></td><td></td></tr>
	<tr>
		<td align="right"><b>Front L</b><td >
		<button class="btn" style="width:100%" onclick="exec('action','CVFL UP');">UP</button></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','CVFL DOWN');">DOWN</button></td>
	</tr>
	<tr><td></td><td></td><td></td></tr>
	<tr>
		<td align="right"><b>Front R</b><td >
		<button class="btn" style="width:100%" onclick="exec('action','CVFR UP');">UP</button></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','CVFR DOWN');">DOWN</button></td>
	</tr>
	<tr><td></td><td></td><td></td></tr>
	<tr>
		<td align="right"><b>Rear L</b>
		<td ><button class="btn" style="width:100%" onclick="exec('action','CVSL UP');">UP</button></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','CVSL DOWN');">DOWN</button></td>
	</tr>
	<tr><td></td><td></td><td></td></tr>
	<tr>
		<td align="right"><b>Rear R</b><td >
		<button class="btn" style="width:100%" onclick="exec('action','CVSR UP');">UP</button></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','CVSR DOWN');">DOWN</button></td>
	</tr>
	<tr><td></td><td></td><td></td></tr>
	</table>
</div>
<div class="box" style="width:96%">
	<table width="100%">
	<tr><td width="30%"></td><td width="30%"></td><td width="30%"></td></tr>
	<tr>
		<td align="right"><b>Bass</b></td>
		<td>	<button class="btn" style="width:100%" onclick="exec('action','PSBAS UP');">UP</button></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','PSBAS DOWN');">DOWN</button></td>
	</tr>
	<tr><td></td><td></td><td></td></tr>
	<tr>
		<td align="right"><b>Trebble</b></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','PSTRE UP');">UP</button></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','PSTRE DOWN');">DOWN</button></td>
	</tr>
	<tr><td></td><td></td><td></td></tr>
	<tr>
		<td align="right"><b>LFE</b></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','PSLFE UP');">UP</button></td>
		<td><button class="btn" style="width:100%" onclick="exec('action','PSLFE DOWN');">DOWN</button></td>
	</tr>
	<tr><td></td><td></td><td></td></tr>
	</table>
</div>
</body>
</html>
<?php
}
