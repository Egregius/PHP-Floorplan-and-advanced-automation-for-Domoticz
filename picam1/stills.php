<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
// error_reporting(-1);
require '../secure/functions.php';
require '../secure/authentication.php';
require 'config.php';
if($home===true) {
	echo '<html>
	<head>
		<title>Oprit</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="HandheldFriendly" content="true"/>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,initial-scale=0.5,minimal-ui" />
		<link rel="icon" type="image/png" href="/images/Camera.png"/>
		<link rel="shortcut icon" href="/images/Camera.png"/>
		<link rel="apple-touch-icon" href="/images/Camera.png"/>
		<meta name="mobile-web-app-capable" content="yes"/>
		<link href="/styles/picam1.php?v=6" rel="stylesheet" type="text/css"/>
	</head>
	<body>
		<div class="navbar" role="navigation">
		        <form method="POST" action="../floorplan.php">
				<input type="submit" value="Plan" class="btn b7" />
			</form>';
	$thumbs=glob('/var/www/html/picam1/archive/*');
	
	echo '<pre>';print_r($thumbs);echo '</pre>';

}
?>