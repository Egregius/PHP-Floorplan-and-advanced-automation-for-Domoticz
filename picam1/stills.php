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
		<title>Voordeur - Oprit</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
		<meta name="HandheldFriendly" content="true"/>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,initial-scale=0.5,minimal-ui" />
		<link rel="icon" type="image/png" href="/images/Camera.png"/>
		<link rel="shortcut icon" href="/images/Camera.png"/>
		<link rel="apple-touch-icon" href="/images/Camera.png"/>
		<meta name="mobile-web-app-capable" content="yes"/>
		<!-- <link href="/styles/picam1.php?v=6" rel="stylesheet" type="text/css"/> -->
		<style>
			.item{float:left;background-color:#FF0;margin:20px;width:150px;height:150px;}
		</style>
	</head>
	<body>
		<div class="navbar" role="navigation">
		        <form method="POST" action="../floorplan.php">
				<input type="submit" value="Plan" class="btn b7" />
			</form>';
	$thumbs=rglob('/var/www/html/picam1/stills/.thumbs/*.jpg');
	echo '<pre>';print_r($thumbs);echo '</pre>';
	foreach ($thumbs as $t) {
		$th=str_replace('/var/www/html', '', $t);
		$f=str_replace('.th.jpg', '.jpg', str_replace('.thumbs/', '', $th));
		echo '
			<div class="item">
				<a href="'.$f.'">
					<img src="'.$th.'">
				</a>
			</div>';
	}
}


function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags); 
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}
?>