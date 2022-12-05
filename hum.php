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
	</head>';
if ($udevice=='iPad') echo '
	<body style="width:1010px">
		<form action="floorplan.php"><input type="submit" class="btn b4" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btn b4" value="Temperaturen"/></form>
		<form action="/hum.php"><input type="submit" class="btn btna b4" value="Hum"/></form>
		<form action="/regen.php"><input type="submit" class="btn b4" value="Regen"/></form>';
elseif ($udevice=='iPhone') echo '
	<body style="width:420px">
		<form action="floorplan.php"><input type="submit" class="btn b4" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btn b4" value="Temperaturen"/></form>
		<form action="/hum.php"><input type="submit" class="btn btna b4" value="Hum"/></form>
		<form action="/regen.php"><input type="submit" class="btn b4" value="Regen"/></form>';

else 	echo '
	<body style="width:100%">
		<form action="/floorplan.php"><input type="submit" class="btn b4" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btn b4" value="Temperaturen"/></form>
		<form action="/hum.php"><input type="submit" class="btna btn b4" value="Hum"/></form>
		<form action="/regen.php"><input type="submit" class="btn b4" value="Regen"/></form>';
$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
if ($db->connect_errno>0) die('Unable to connect to database ['.$db->connect_error.']');
//unset($_SESSION['sensors_hum']);
$sensors=array(
	'living_hum'=>array('Naam'=>'Living','Color'=>'#FF1111'),
	'kamer_hum'=>array('Naam'=>'Kamer','Color'=>'#44FF44'),
	'alex_hum'=>array('Naam'=>'Alex','Color'=>'#00EEFF'),
	'buiten_hum'=>array('Naam'=>'Buiten','Color'=>'#FFFFFF'),
);
foreach ($sensors as $k=>$v) {
	if(isset($_GET[$k]))$_SESSION['sensors_hum'][$k]=true;else $_SESSION['sensors_hum'][$k]=false;
}
$aantalsensors=0;
foreach ($_SESSION['sensors_hum'] as $k=>$v) {
	if ($v==1) $aantalsensors++;
}
$args['colors']=array();
$argshour['colors']=array();
if ($aantalsensors==1) $argshour['colors']=array('#00F', '#0F0', '#F00');
elseif ($aantalsensors==0) {
	$_SESSION['sensors_hum']=array('living_hum'=>1,'kamer_hum'=>1,'alex_hum'=>1,'buiten_hum'=>1);
	$aantalsensors=4;
}

echo '<div style="padding:16px 0px 20px 0px;"><form method="GET">';
foreach ($sensors as $k=>$v) {
	if(isset($_SESSION['sensors_hum'][$k])&&$_SESSION['sensors_hum'][$k]==1) echo '<input type="checkbox" name="'.$k.'" id="'.$k.'" onChange="this.form.submit()" class="'.$k.'" checked><label for="'.$k.'">'.$v['Naam'].'</label>';
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
		'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),
		'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF')
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
$args['colors']=array();
$argshour['colors']=array();
if ($aantalsensors==1) $argshour['colors']=array('#00F', '#0F0', '#F00');
elseif ($aantalsensors==0) $_SESSION['sensors_hum']=array('living_hum'=>1,'kamer_hum'=>1);
//echo '<pre>';print_r($sensors);echo '</pre>';

foreach ($_SESSION['sensors_hum'] as $k=>$v) {
//	echo $k.'<br>';
	if ($v==1) {
		if ($aantalsensors==1) {
			array_push($args['colors'], $sensors[$k]['Color']);
		} else {
			
			array_push($args['colors'], $sensors[$k]['Color']);
			array_push($argshour['colors'], $sensors[$k]['Color']);
		}
	}
}
//$args['line_styles']=array('lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]');
$query="SELECT DATE_FORMAT(stamp, '%H:%i') as stamp";
foreach ($_SESSION['sensors_hum'] as $k=>$v) {
	if ($v==1) $query.=', '.$k;
}
$query.=" from `temp` where stamp >= '$dag'";
if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
if ($result->num_rows==0) {echo 'No data for dates '.$dag.' to '.$f_enddate.'<hr>';goto montha;}
$min=9999;
$max=-1000;
while ($row=$result->fetch_assoc()) $graph[]=$row;
$result->free();
foreach ($graph as $t) {
	foreach ($_SESSION['sensors_hum'] as $k=>$v) {
		if ($v==1) {
			if ($t[$k]<$min) $min=$t[$k];
			if ($t[$k]>$max) $max=$t[$k];
		}
	}
}
$args['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 12},gridlines: {multiple: 1, color: "#999"},minorGridlines: {multiple: 0.5, color: "#333"},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
//	vAxis:{viewWindowMode:"explicit",viewWindow:{max:'.ceil($max).',min:'.floor($min).'},gridlines:{count:0}}
$chart=array_to_chart($graph, $args);
echo $chart['script'];
echo $chart['div'];
unset($chart,$graph);
montha:
$query="SELECT DATE_FORMAT(stamp, '%W %k:%i') as stamp";
foreach ($_SESSION['sensors_hum'] as $k=>$v) {
	if ($v==1) {
		if ($aantalsensors==1) $query.=", MIN($k) AS MIN, AVG($k) AS AVG, MAX($k) AS MAX";
		else $query.=", AVG($k) AS $k";
	}
}

$query.=" from `temp` where stamp > '$week' GROUP BY UNIX_TIMESTAMP(stamp) DIV 3600";
if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
if ($result->num_rows==0) {echo 'No data for last week.<hr>';goto enda;} else echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek laatste week.';
while ($row=$result->fetch_assoc()) $graph[]=$row;
$result->free();
$min=9999;
$max=-1000;
foreach ($graph as $t) {
	foreach ($_SESSION['sensors_hum'] as $k=>$v) {
		if ($v==1) {
			if ($aantalsensors==1) {
				if ($t['MIN']<$min) $min=$t['MIN'];
				if ($t['MAX']>$max) $max=$t['MAX'];
			} else {
				if ($t[$k]<$min) $min=$t[$k];
				if ($t[$k]>$max) $max=$t[$k];
			}
		}
	}
}
$argshour['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 12},gridlines: {multiple: 1, color: "#999"},minorGridlines: {multiple: 0.5, color: "#333"},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
$chart=array_to_chart($graph, $argshour);
echo $chart['script'];
echo $chart['div'];
unset($chart,$graph);
enda:
$query="SELECT DATE_FORMAT(stamp, '%a %e/%m %k:%i') as stamp";
foreach ($_SESSION['sensors_hum'] as $k=>$v) {
	if ($v==1) {
		if ($aantalsensors==1) $query.=", MIN($k) AS MIN, AVG($k) AS AVG, MAX($k) AS MAX";
		else $query.=", AVG($k) AS $k";
	}
}

$query.=" from `temp` where stamp > '$maand' GROUP BY UNIX_TIMESTAMP(stamp) DIV 86400";
if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
while ($row=$result->fetch_assoc()) $graph[]=$row;
$result->free();
$min=9999;
$max=-1000;
foreach ($graph as $t) {
	foreach ($_SESSION['sensors_hum'] as $k=>$v) {
		if ($v==1) {
			if ($aantalsensors==1) {
				if ($t['MIN']<$min) $min=$t['MIN'];
				if ($t['MAX']>$max) $max=$t['MAX'];
			} else {
				if ($t[$k]<$min) $min=$t[$k];
				if ($t[$k]>$max) $max=$t[$k];
			}
		}
	}
}
$argshour['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 12},gridlines: {multiple: 1, color: "#999"},minorGridlines: {multiple: 0.5, color: "#333"},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
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
echo '<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
$db->close();
