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
		<link href="/styles/picam1.php?v=6" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" href="/picam1/js-css/simple-lightbox.min.css" />
		<style>
			img{width:350px;heigt:auto}
			.item{float:left;background-color:#333;margin:2px;width:350px;height:295px;font-size:1.4em;}
			.gallery{margin-top:80px;}
		</style>
	</head>
	<body>
		<div class="navbar" role="navigation">
		        <form method="POST" action="../floorplan.php">
				<input type="submit" value="Plan" class="btn b7" />
			</form>
		</div>
		<div class="gallery">';
	$thumbs=rglob('/var/www/html/picam1/stills/.thumbs/*.jpg');
	rsort($thumbs);
	//echo '<pre>';print_r($thumbs);echo '</pre>';
	foreach ($thumbs as $t) {
		$th=str_replace('/var/www/html', '', $t);
		$f=str_replace('.th.jpg', '.jpg', str_replace('.thumbs/', '', $th));
		$t=str_replace('_', ' ', str_replace('/picam1/stills/motion_', '', str_replace('.jpg', '', $f)));
		echo '
			<div class="item">
				<a href="'.$f.'">
					<img src="'.$th.'">
				</a>
				'.$t.'
			</div>';
	}
	echo '
		</div>';
}


function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags); 
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}
?>
