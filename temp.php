<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
require 'scripts/chart.php';
$dag=date("Y-m-d H:i:00", TIME-86400);
$week=date("Y-m-d", TIME-86400*6);
$maand=date("Y-m-d", TIME-86400*100);
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<link rel="preconnect" href="https://www.gstatic.com/" crossorigin />
	<link rel="dns-prefetch" href="https://www.gstatic.com/" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="HandheldFriendly" content="true"/>
	<meta name="viewport" content="width=300,height=500,initial-scale='.$scale.',user-scalable=yes,minimal-ui">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="theme-color" content="#000">
	<title>Temperaturen</title>
	<link rel="icon" href="images/temperatures.png"/>
	<link rel="shortcut icon" href="images/temperatures.png"/>
	<link rel="apple-touch-icon" href="images/temperatures.png"/>
	<link href="/styles/temp.css?v=6" rel="stylesheet" type="text/css"/>
	</head>';
if ($udevice=='iPad') echo '
	<body style="width:1010px">
		<form action="/temp.php"><input type="submit" class="btn btna b2" value="Temperaturen"/></form>
		<form action="/hum.php"><input type="submit" class="btn btn b2" value="Humidity"/></form>';
elseif ($udevice=='iPhoneGuy'||$udevice=='iPhoneKirby') echo '
	<body style="width:450px">
		<div style="position:fixed;bottom:0px;left:0px;z-index:10;width:100%;height:70px;background-color:#000;">
		<form action="/temp.php"><input type="submit" class="btn btna b2" value="Temperaturen"/></form>
		<form action="/hum.php"><input type="submit" class="btn btn b2" value="Humidity"/></form>
		</div>';

else 	echo '
	<body style="width:100%">
		<form action="/temp.php"><input type="submit" class="btn btna b2" value="Temperaturen"/></form>
		<form action="/hum.php"><input type="submit" class="btn btn b2" value="Humidity"/></form>';
$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
if ($db->connect_errno>0) die('Unable to connect to database ['.$db->connect_error.']');

$sensors=array(
	'living'=>array('Naam'=>'Living','Color'=>'#FF1111'),
	'badkamer'=>array('Naam'=>'Badkamr','Color'=>'#6666FF'),
	'kamer'=>array('Naam'=>'Kamer','Color'=>'#44FF44'),
	'alex'=>array('Naam'=>'Alex','Color'=>'#00EEFF'),
	'waskamer'=>array('Naam'=>'Waskamr','Color'=>'#EEEE00'),
	'zolder'=>array('Naam'=>'Zolder','Color'=>'#EE33EE'),
	'buiten'=>array('Naam'=>'Buiten','Color'=>'#FFFFFF'),
);
foreach ($sensors as $k=>$v) {
	if(isset($_GET[$k]))$_SESSION['sensors'][$k]=true;else $_SESSION['sensors'][$k]=false;
}
$aantalsensors=0;
foreach ($_SESSION['sensors'] as $k=>$v) {
	if ($v==1) $aantalsensors++;
}
$args['colors']=array();
$argshour['colors']=array();
if ($aantalsensors==1) $argshour['colors']=array('#00F', '#0F0', '#F00');
elseif ($aantalsensors==0) {
	$_SESSION['sensors']=array('living'=>1,'badkamer'=>1,'kamer'=>1,'alex'=>1,'waskamer'=>1);
	$aantalsensors=4;
}

echo '<div style="padding:20px 0px 20px 0px;"><form method="GET">';
foreach ($sensors as $k=>$v) {
	if(isset($_SESSION['sensors'][$k])&&$_SESSION['sensors'][$k]==1) echo '<input type="checkbox" name="'.$k.'" id="'.$k.'" onChange="this.form.submit()" class="'.$k.'" checked><label for="'.$k.'">'.$v['Naam'].'</label>';
	else echo '<input type="checkbox" name="'.$k.'" id="'.$k.'" onChange="this.form.submit()" class="'.$k.'"><label for="'.$k.'">'.$v['Naam'].'</label>';
}
echo '</form>';
$args=array(
		'width'=>1000,
		'height'=>880,
		'hide_legend'=>true,
		'responsive'=>true,
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
		'responsive'=>true,
		'background_color'=>'#000',
		'chart_div'=>'graphhour',
		'margins'=>array(0,0,0,0),
		'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),
		'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF')
	);
if ($udevice=='iPadGuy') {
	$args['width']=1000;$args['height']=1320;
	$argshour['width']=1000;$argshour['height']=1320;
} elseif ($udevice=='iPhoneGuy') {
	$args['width']=480;$args['height']=950;
	$argshour['width']=480;$argshour['height']=950;
} elseif ($udevice=='iPhoneKirby') {
	$args['width']=420;$args['height']=710;
	$argshour['width']=420;$argshour['height']=610;
} elseif ($udevice=='Mac') {
	$args['width']=490;$args['height']=780;
	$argshour['width']=490;$argshour['height']=780;
} else {
	$args['width']=480;$args['height']=902;
	$argshour['width']=480;$argshour['height']=900;
}
$args['colors']=array();
$argshour['colors']=array();
if ($aantalsensors==1) $argshour['colors']=array('#00F', '#0F0', '#F00');
elseif ($aantalsensors==0) $_SESSION['sensors']=array('living'=>1,'badkamer'=>1);

foreach ($_SESSION['sensors'] as $k=>$v) {
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
foreach ($_SESSION['sensors'] as $k=>$v) {
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
	foreach ($_SESSION['sensors'] as $k=>$v) {
		if ($v==1) {
			if ($t[$k]<$min) $min=$t[$k];
			if ($t[$k]>$max) $max=$t[$k];
		}
	}
}
$args['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 12},gridlines: {multiple: 2, color: "#444"},minorGridlines: {multiple: 1, color: "#222"},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
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
foreach ($_SESSION['sensors'] as $k=>$v) {
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
	foreach ($_SESSION['sensors'] as $k=>$v) {
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
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 12},gridlines: {multiple: 2, color: "#444"},minorGridlines: {multiple: 1, color: "#222"},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
$chart=array_to_chart($graph, $argshour);
echo $chart['script'];
echo $chart['div'];
unset($chart,$graph);
enda:
$query="SELECT DATE_FORMAT(stamp, '%a %e/%m %k:%i') as stamp";
foreach ($_SESSION['sensors'] as $k=>$v) {
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
	foreach ($_SESSION['sensors'] as $k=>$v) {
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
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 12},gridlines: {multiple: 2, color: "#444"},minorGridlines: {multiple: 1, color: "#222"},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek voor laatste 100 dagen';
$argshour['chart_div']='chart_div';
$chart=array_to_chart($graph, $argshour);
echo $chart['script'];
echo $chart['div'];
unset($chart,$graph);

$togo=61-date("s");
if ($togo<15) $togo=15;
$togo=$togo*1000+62000;
echo "<br>$udevice<br><br>refreshing in ".$togo/1000 ." seconds<br><br><br><br><br>";
echo '<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
$db->close();
