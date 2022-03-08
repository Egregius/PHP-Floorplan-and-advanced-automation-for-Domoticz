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
require '/var/www/authentication.php';
require 'scripts/chart.php';
$f_enddate=date("Y-m-d", TIME);
$dag=date("Y-m-d H:i:00", TIME-86400);
$week=date("Y-m-d", TIME-86400*6);
$maand=date("Y-m-d", TIME-86400*60);
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="HandheldFriendly" content="true"/>
	<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no, minimal-ui"/>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<title>Temperaturen</title>
	<link rel="icon" href="images/temperatures.png"/>
	<link rel="shortcut icon" href="images/temperatures.png"/>
	<link rel="apple-touch-icon" href="images/temperatures.png"/>
	<link href="/styles/temp.css?v=5" rel="stylesheet" type="text/css"/>
	<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
	</head>';
if ($udevice=='iPad') echo '
	<body style="width:1010px">
		<form action="floorplan.php"><input type="submit" class="btn b3" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btna b3" value="Temperaturen"/></form>
		<form action="/regen.php"><input type="submit" class="btn b3" value="Regen"/></form>';
elseif ($udevice=='iPhone') echo '
	<body style="width:420px">
		<form action="floorplan.php"><input type="submit" class="btn b3" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btna b3" value="Temperaturen"/></form>
		<form action="/regen.php"><input type="submit" class="btn b3" value="Regen"/></form>';

else 	echo '
	<body style="width:100%">
		<form action="/floorplan.php"><input type="submit" class="btn b3" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btna b3" value="Temperaturen"/></form>
		<form action="/regen.php"><input type="submit" class="btn b3" value="Regen"/></form>';
$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
if ($db->connect_errno>0) die('Unable to connect to database ['.$db->connect_error.']');

$sensors=array(
	'living'=>array(
		'Naam'=>'Living',
		'Color'=>'#FF1111'
	),
	'badkamer'=>array(
		'Naam'=>'Badkamr',
		'Color'=>'#6666FF'
	),
	'kamer'=>array(
		'Naam'=>'Kamer',
		'Color'=>'#44FF44'
	),
	'alex'=>array(
		'Naam'=>'Alex',
		'Color'=>'#00EEFF'
	),
	'speelkamer'=>array(
		'Naam'=>'Splkamr',
		'Color'=>'#EEEE00'
	),
	'zolder'=>array(
		'Naam'=>'Zolder',
		'Color'=>'#EE33EE'
	),
	'buiten'=>array(
		'Naam'=>'Buiten',
		'Color'=>'#FFFFFF'
	),
);
foreach ($sensors as $k=>$v) {
	if(isset($_GET[$k]))$_SESSION['sensors'][$k]=true;else $_SESSION['sensors'][$k]=false;
}
echo '<div style="padding:16px 0px 20px 0px;"><form method="GET">';
foreach ($sensors as $k=>$v) {
	if($_SESSION['sensors'][$k]) echo '<input type="checkbox" name="'.$k.'" id="'.$k.'" onChange="this.form.submit()" class="'.$k.'" checked><label for="'.$k.'">'.$v['Naam'].'</label>';
	else echo '<input type="checkbox" name="'.$k.'" id="'.$k.'" onChange="this.form.submit()" class="'.$k.'"><label for="'.$k.'">'.$v['Naam'].'</label>';
}
echo '</form>';
$args=array(
		'width'=>1000,
		'height'=>880,
		'hide_legend'=>true,
		'responsive'=>false,
		'background_color'=>'#000',
		'chart_div'=>'graph',
		'margins'=>array(0,0,0,0),
		'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),
		'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF')
	);
$argshour=array(
		'width'=>1000,
		'height'=>880,
		'hide_legend'=>true,
		'responsive'=>false,
		'background_color'=>'#000',
		'chart_div'=>'graphhour',
		'margins'=>array(0,0,0,0),
		'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999')
	);
if ($udevice=='iPad') {
	$args['width']=1000;$args['height']=1230;
	$argshour['width']=1000;$argshour['height']=1230;
} elseif ($udevice=='iPhone') {
	$args['width']=462;$args['height']=610;
	$argshour['width']=462;$argshour['height']=710;
} elseif ($udevice=='iPhoneSE') {
	$args['width']=420;$args['height']=610;
	$argshour['width']=420;$argshour['height']=610;
} elseif ($udevice=='iMac') {
	$args['width']=490;$args['height']=780;
	$argshour['width']=490;$argshour['height']=780;
} else {
	$args['width']=480;$args['height']=610;
	$argshour['width']=480;$argshour['height']=610;
}
print_r($_SESSION['sensors']);
exit;

$args['colors']=array($buiten,$living,$badkamer,$kamer,$speelkamer,$alex,$zolder,$living,$badkamer,$kamer,$speelkamer,$alex);
$argshour['colors']=array($buiten,$living,$badkamer,$kamer,$speelkamer,$alex,$zolder,$living,$badkamer,$kamer,$speelkamer,$alex);
$args['line_styles']=array('lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]');
$query="SELECT DATE_FORMAT(stamp, '%H:%i') as stamp,buiten,living,badkamer,kamer,speelkamer,alex,zolder from `temp` where stamp >= '$dag' AND stamp <= '$f_enddate 23:59:59'";
if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
if ($result->num_rows==0) {echo 'No data for dates '.$dag.' to '.$f_enddate.'<hr>';goto montha;}
$min=9999;
$max=-1000;
while ($row=$result->fetch_assoc()) $graph[]=$row;
$result->free();
foreach ($graph as $t) {
	foreach (array('buiten','living','badkamer','kamer','speelkamer','alex','zolder') as $i) {
		if ($t[$i]<$min) $min=$t[$i];
		if ($t[$i]>$max) $max=$t[$i];
	}
}
$args['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 14},Gridlines: {multiple: 1},minorGridlines: {multiple: 1},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
//	vAxis:{viewWindowMode:"explicit",viewWindow:{max:'.ceil($max).',min:'.floor($min).'},gridlines:{count:0}}
$chart=array_to_chart($graph, $args);
echo $chart['script'];
echo $chart['div'];
unset($chart,$graph);
montha:
$query="SELECT DATE_FORMAT(stamp, '%W %k:%i') as stamp, AVG(buiten) AS buiten, AVG(living) AS living,AVG(badkamer) AS badkamer,AVG(kamer) AS kamer,AVG(speelkamer) AS speelkamer,AVG(alex) AS alex,AVG(zolder) AS zolder from `temp` where stamp > '$week' GROUP BY UNIX_TIMESTAMP(stamp) DIV 3600";
if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
if ($result->num_rows==0) {echo 'No data for last week.<hr>';goto enda;} else echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek laatste week.';
while ($row=$result->fetch_assoc()) $graph[]=$row;
$result->free();
$min=9999;
$max=-1000;
foreach ($graph as $t) {
	foreach (array('buiten','living','badkamer','kamer','speelkamer','alex','zolder') as $i) {
		if ($t[$i]<$min) $min=$t[$i];
		if ($t[$i]>$max) $max=$t[$i];
	}
}
$args['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 14},Gridlines: {multiple: 1},minorGridlines: {multiple: 1},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
$chart=array_to_chart($graph, $argshour);
echo $chart['script'];
echo $chart['div'];
unset($chart,$graph);
enda:
$query="SELECT DATE_FORMAT(stamp, '%d-%m-%Y %k:%i') as stamp, AVG(buiten) AS buiten, AVG(living) AS living, AVG(badkamer) AS badkamer, AVG(kamer) AS kamer, AVG(speelkamer) AS speelkamer, AVG(alex) AS alex, AVG(zolder) AS zolder from `temp` where stamp > '$maand' GROUP BY UNIX_TIMESTAMP(stamp) DIV 86400";
if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
while ($row=$result->fetch_assoc()) $graph[]=$row;
$result->free();
$min=9999;
$max=-1000;
foreach ($graph as $t) {
	foreach (array('buiten','living','badkamer','kamer','speelkamer','alex','zolder') as $i) {
		if ($t[$i]<$min) $min=$t[$i];
		if ($t[$i]>$max) $max=$t[$i];
	}
}
$args['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 14},Gridlines: {multiple: 1},minorGridlines: {multiple: 1},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek voor laatste 60 dagen';
$argshour['chart_div']='chart_div';
$chart=array_to_chart($graph, $argshour);
echo $chart['script'];
echo $chart['div'];
unset($chart,$graph);

$togo=61-date("s");
if ($togo<15) $togo=15;
$togo=$togo*1000+62000;
echo "<br>$udevice<br><br>refreshing in ".$togo/1000 ." seconds";
echo '<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
$db->close();
