<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
require 'scripts/chart.php';
$sensor=998;
if (isset($_REQUEST['sensor'])) $sensor=$_REQUEST['sensor'];

$dag=date("Y-m-d H:i:00", TIME-86400*2);
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<link rel="preconnect" href="https://www.gstatic.com/" crossorigin />
	<link rel="dns-prefetch" href="https://www.gstatic.com/" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="HandheldFriendly" content="true"/>
	<meta name="viewport" content="width=device-width,height=device-height, user-scalable=yes, minimal-ui"/>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<title>Temperaturen</title>
	<link href="/styles/temp.css" rel="stylesheet" type="text/css"/>
	<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
	</head>
	<body style="width:100%">';
$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
if ($db->connect_errno>0) {
	die('Unable to connect to database [' . $db->connect_error . ']');
}
switch ($sensor) {
case 147:$setpoint=12;$radiator=179;$sensornaam='living';
	break;
case 246:$setpoint=13;$radiator=13;$sensornaam='badkamer';
	break;
case 278:$setpoint=14;$radiator=181;$sensornaam='kamer';
	break;
case 356:$setpoint=15;$radiator=183;$sensornaam='waskamer';
	break;
case 293:$setpoint=0;$radiator=0;$sensornaam='zolder';
	break;
case 244:$setpoint=16;$radiator=203;$sensornaam='alex';
	break;
case 998:$setpoint=998;$radiator=998;$sensornaam='binnen';
	break;
case 999:$setpoint=999;$radiator=999;$sensornaam='alles';
	break;
default:$setpoint=0;$radiator=0;$sensornaam='buiten';
	break;
}
$sensor=$sensornaam;
$living='#FF1111';
$badkamer='#6666FF';
$kamer='#44FF44';
$waskamer='00EEFF';
$alex='#EEEE00';
$zolder='#EE33EE';
$buiten='#FFFFFF';
$legend='<div style="position:absolute;top:14px;left;0px;width:100%;z-index:100;"><center>
	&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=147");\'><font color="'.$living.'">Living</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=246");\'><font color="'.$badkamer.'">Badkamer</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=278");\'><font color="'.$kamer.'">Kamer</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=356");\'><font color="'.$waskamer.'">waskamer</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=244");\'><font color="'.$alex.'">Alex</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=293");\'><font color="'.$zolder.'">Zolder</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=329");\'><font color="'.$buiten.'">Buiten</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=998");\'><font color="'.$buiten.'">Binnen</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=999");\'><font color="'.$buiten.'">Alles</font></a>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'javascript:navigator_Go("tempbig.php");\'><font color="#FFFFFF">'.strftime("%k:%M:%S", TIME).'</font></a></center></div>';
echo $legend;
$args=array(
	'width'=>3340,
	'height'=>1800,
	'hide_legend'=>true,
	'responsive'=>false,
	'background_color'=>'#000',
	'chart_div'=>'graph',
	'margins'=>array(0,0,0,50),
	'y_axis_text_style'=>array('fontSize'=>18,'color'=>'FFFFFF'),
	'x_axis_text_style'=>array('fontSize'=>18,'color'=>'FFFFFF'),
	'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),
	'raw_options'=>'
		lineWidth:4,
		crosshair:{trigger:"both"},
		hAxis:{textPosition:"None"},
		vAxis:{format:"# °C",textStyle:{color:"#AAA",fontSize:14},gridlines: {multiple: 2, color: "#F00"},minorGridlines: {multiple: 1, color: "#FFF"}},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}'
);
if ($sensor=='alles') {
	$args['colors']=array($buiten,$living,$badkamer,$kamer,$waskamer,$alex,$zolder,$living,$badkamer,$kamer,$waskamer,$alex);
	$line_styles=array('lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]');
	$query="SELECT stamp,buiten,living,badkamer,kamer,waskamer,alex,zolder from `temp` where stamp >= '$dag'";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
	while ($row=$result->fetch_assoc()) $graph[]=$row;
	$result->free();
	$min=99;
	$max=0;
	foreach ($graph as $i) {
		if ($i['buiten']<$min) $min=$i['buiten'];
		elseif ($i['buiten']>$max) $max=$i['buiten'];
		if ($i['living']<$min) $min=$i['living'];
		elseif ($i['living']>$max) $max=$i['living'];
		if ($i['badkamer']<$min) $min=$i['badkamer'];
		elseif ($i['badkamer']>$max) $max=$i['badkamer'];
		if ($i['kamer']<$min) $min=$i['kamer'];
		elseif ($i['kamer']>$max) $max=$i['kamer'];
		if ($i['alex']<$min) $min=$i['alex'];
		elseif ($i['alex']>$max) $max=$i['alex'];
		if ($i['waskamer']<$min) $min=$i['waskamer'];
		elseif ($i['waskamer']>$max) $max=$i['waskamer'];
		if ($i['zolder']<$min) $min=$i['zolder'];
		elseif ($i['zolder']>$max) $max=$i['zolder'];
	}
	$min=floor($min);
	$max=ceil($max);
	$args['raw_options']='
		lineWidth:4,
		crosshair:{trigger:"both"},
		hAxis:{textPosition:"None"},
		vAxis:{format:"# °C",textStyle:{color:"#AAA",fontSize:14},gridlines: {multiple: 2, color: "#444"},minorGridlines: {multiple: 1, color: "#222"},viewWindow:{min:'.$min.',max:'.$max.'}},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
	$chart=array_to_chart($graph, $args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
} elseif ($sensor=='binnen') {
	$args['colors']=array($living,$badkamer,$kamer,$alex,$living,$badkamer,$kamer,$waskamer,$alex);
	$line_styles=array('lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[1,8]','lineDashStyle:[1,8]');
	$query="SELECT stamp,living,badkamer,kamer,alex from `temp` where stamp >= '$dag'";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
	while ($row=$result->fetch_assoc()) $graph[]=$row;
	$result->free();
	$min=99;
	$max=0;
	foreach ($graph as $i) {
		if ($i['living']<$min) $min=$i['living'];
		elseif ($i['living']>$max) $max=$i['living'];
		if ($i['badkamer']<$min) $min=$i['badkamer'];
		elseif ($i['badkamer']>$max) $max=$i['badkamer'];
		if ($i['kamer']<$min) $min=$i['kamer'];
		elseif ($i['kamer']>$max) $max=$i['kamer'];
		if ($i['alex']<$min) $min=$i['alex'];
		elseif ($i['alex']>$max) $max=$i['alex'];
	}
	$min=floor($min);
	$max=ceil($max);
	$args['raw_options']='
		lineWidth:4,
		crosshair:{trigger:"both"},
		hAxis:{textPosition:"None"},
		vAxis:{format:"# °C",textStyle:{color:"#AAA",fontSize:14},gridlines: {multiple: 2, color: "#444"},minorGridlines: {multiple: 1, color: "#222"},viewWindow:{min:'.$min.',max:'.$max.'}},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
	$chart=array_to_chart($graph, $args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
} else {
	$line_styles=array('lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[1,8]');
	if ($sensor=='badkamer') $args['colors']=array(${$sensornaam},${$sensornaam},'#ffb400');
	else $args['colors']=array(${$sensornaam},${$sensornaam},'#FFFF00');
	$query="SELECT DATE_FORMAT(stamp, '%H:%i') as stamp,$sensor AS temp from `temp` where stamp >= '$dag'";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query .' - '.$db->error.']');
	while ($row=$result->fetch_assoc())	$graph[]=$row;
	$result->free();
	$min=99;
	$max=0;
	foreach ($graph as $i) {
		if ($i['temp']<$min) $min=$i['temp'];
		elseif ($i['temp']>$max) $max=$i['temp'];
	}
	$min=floor($min);
	$max=ceil($max);
	$args['raw_options']='
		lineWidth:4,
		crosshair:{trigger:"both"},
		hAxis:{textPosition:"None"},
		vAxis:{format:"# °C",textStyle:{color:"#AAA",fontSize:14},gridlines: {multiple: 2, color: "#444"},minorGridlines: {multiple: 1, color: "#222"},viewWindow:{min:'.$min.',max:'.$max.'}},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
	$chart=array_to_chart($graph, $args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
}
$togo=61-date("s");
if ($togo<15) {
	$togo=15;
}
$togo=$togo*1000+2000;
echo '<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
$db->close();
