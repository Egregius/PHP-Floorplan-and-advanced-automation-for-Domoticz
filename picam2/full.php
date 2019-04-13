<?php
require '../secure/functions.php';
require '../secure/authentication.php';
require 'config.php';
echo '<html><head>
<title>'.TITLE_STRING.'</title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <meta name="HandheldFriendly" content="true"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui" />
    <link rel="icon" type="image/png" href="../images/Camera.png"/>
    <link rel="shortcut icon" href="../images/Camera.png"/>
    <link rel="apple-touch-startup-image" href="../images/Camera.png"/>
    <link rel="apple-touch-icon" href="../images/Camera.png"/>
    <meta name="msapplication-TileColor" content="#ffffff"/>
    <meta name="msapplication-TileImage" content="../images/Camera.png"/>
    <meta name="msapplication-config" content="browserconfig.xml"/>
    <meta name="mobile-web-app-capable" content="yes"/><link rel="manifest" href="manifest.json"/>
    <meta name="theme-color" content="#ffffff"/>
	<link rel="stylesheet" href="../style.css"/>
	<style type="text/css">
		body{margin:0 auto;}
		form, table {display:inline;margin:0px;padding:0px;}
	</style>
	<!--<script type="text/javascript">
		setTimeout(\'window.location.href=window.location.href;\',900000);
	</script>-->
</head>';
if($home===true) {
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
		echo '<img style="width:1200px;height:auto" id="mjpeg_dest" src="jpg.php">';
	}
	else echo '<img style="width:896px;height:auto" id="mjpeg_dest" src="jpg.php">';
} else {
	header("Location: index.php");
	die("Redirecting to: index.php");
}