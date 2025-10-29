<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
if (isset($_POST['up'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Up"}');
elseif (isset($_POST['right'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Right"}');
elseif (isset($_POST['down'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Down"}');
elseif (isset($_POST['left'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Left"}');
elseif (isset($_POST['select'])) 	kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Select"}');
elseif (isset($_POST['back'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Back"}');
elseif (isset($_POST['home'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Home"}');
elseif (isset($_POST['context'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ContextMenu"}');
elseif (isset($_POST['OSD'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ShowOSD"}');
elseif (isset($_POST['INFO'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.Info"}');
elseif (isset($_POST['Later'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ExecuteAction","params":{"action":"subtitledelayminus"}}');
elseif (isset($_POST['Earlier'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ExecuteAction","params":{"action":"subtitledelayplus"}}');
elseif (isset($_POST['AudioLater'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ExecuteAction","params":{"action":"audiodelayminus"}}');
elseif (isset($_POST['AudioEarlier'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ExecuteAction","params":{"action":"audiodelayplus"}}');
elseif (isset($_POST['Subup'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ExecuteAction","params":{"action":"subtitleshiftup"}}');
elseif (isset($_POST['Subdown'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ExecuteAction","params":{"action":"subtitleshiftdown"}}');
elseif (isset($_POST['Imgup'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ExecuteAction","params":{"action":"verticalshiftup"}}');
elseif (isset($_POST['Imgdown'])) kodi('{"jsonrpc":"2.0","id":1,"method":"Input.ExecuteAction","params":{"action":"verticalshiftdown"}}');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			<meta name="HandheldFriendly" content="true"/>
<?php
	if ($ipaddress=='192.168.2.203'||$ipaddress=='192.168.4.3')  { //Aarde
		echo '
		<meta name="viewport" content="width=300,height=500,initial-scale=0.84,user-scalable=no,minimal-ui">';
	} elseif ($ipaddress=='192.168.2.204'||$ipaddress=='192.168.4.4'||$udevice=='iPad')  { //iPad
		echo '
		<meta name="viewport" content="width=device-width,initial-scale=1.15,user-scalable=no,minimal-ui">';
	} elseif ($ipaddress=='192.168.2.23'||$ipaddress=='192.168.4.5')  { //iPhone Kirby
		echo '
		<meta name="viewport" content="width=device-width,initial-scale=0.755,user-scalable=no,minimal-ui">';
	} elseif ($udevice=='iPhone') {
		echo '
		<meta name="viewport" content="width=device-width,initial-scale=0.755,user-scalable=no,minimal-ui">';
	} else {
		echo '
		<meta name="viewport" content="width=device-width,user-scalable=yes,minimal-ui">';
	}
?>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="theme-color" content="#000">
	<title>Kodi</title>
	<link rel="icon" type="image/png" href="images/kodi.png">
	<link rel="shortcut icon" href="images/kodi.png" />
	<link rel="apple-touch-icon" href="images/kodi.png"/>
	<link rel="icon" sizes="196x196" href="images/kodi.png">
	<link rel="icon" sizes="192x192" href="images/kodi.png">
	<meta name="mobile-web-app-capable" content="yes">
	<link href="/styles/kodi.css" rel="stylesheet" type="text/css"/>
	<style>
		.big{min-height:11vh;}
	</style>
</head>
<body>
<div class="navbar">
	<form action="/index.html"><input type="submit" class="btn b2" value="Plan"/></form>
	<form action="/kodi.php"><input type="submit" class="btn btna b2" value="Kodi"/></form>
</div>
<div><form method="POST">
	<br><br>
	<input type="submit" name="back" value="Back" class="btn big b3"/>
	<input type="submit" name="up" value="UP" class="btn big b3"/>
	<input type="submit" name="context" value="Context" class="btn big b3"/>
	<input type="submit" name="left" value="LEFT" class="btn big b3"/>
	<input type="submit" name="select" value="Select" class="btn big b3"/>
	<input type="submit" name="right" value="RIGHT" class="btn big b3"/>
	<input type="submit" name="home" value="Home" class="btn big b3"/>
	<input type="submit" name="down" value="DOWN" class="btn big b3"/>
	<input type="submit" name="context" value="Context" class="btn big b3"/>
	<br><br>
	<input type="submit" name="INFO" value="INFO" class="btn big b2"/>
	<input type="submit" name="OSD" value="OSD" class="btn big b2"/>
	<br><br>
	<input type="submit" name="Earlier" value="Earlier" class="btn big b3"/>
	<input type="submit" name="SUBSYNC" value="Subs" class="btn big b3"/>
	<input type="submit" name="Later" value="Later" class="btn big b3"/>
	<br>
	<input type="submit" name="AudioEarlier" value="Earlier" class="btn big b3"/>
	<input type="submit" name="AudioSYNC" value="Sound" class="btn big b3"/>
	<input type="submit" name="AudioLater" value="Later" class="btn big b3"/>
	<br>
	<input type="submit" name="Subup" value="Subup" class="btn big b2"/>
	<input type="submit" name="Subdown" value="Subdown" class="btn big b2"/>
	<br>
	<input type="submit" name="Imgup" value="Imgup" class="btn big b2"/>
	<input type="submit" name="Imgdown" value="Imgdown" class="btn big b2"/>
	<br>
	<br>
