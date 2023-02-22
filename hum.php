<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
require 'scripts/chart.php';
$dag=date("Y-m-d H:i:00", TIME-86400);
//$dag='2022-12-05 16:24:00';
$week=date("Y-m-d", TIME-86400*6);
//$week='2022-12-05 16:24:00';
$maand=date("Y-m-d", TIME-86400*100);
$maand='2022-12-05 16:24:00';
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<link rel="preconnect" href="https://www.gstatic.com/" crossorigin />
	<link rel="dns-prefetch" href="https://www.gstatic.com/" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="HandheldFriendly" content="true"/>
	<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no, minimal-ui"/>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<title>Humidity</title>
	<link rel="icon" href="images/temperatures.png"/>
	<link rel="shortcut icon" href="images/temperatures.png"/>
	<link rel="apple-touch-icon" href="images/temperatures.png"/>
	<link href="/styles/temp.css?v=6" rel="stylesheet" type="text/css"/>
	</head>';
if ($udevice=='iPad') echo '
	<body style="width:1010px">
		<form action="floorplan.php"><input type="submit" class="btn b4" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btn b4" value="Temperaturen"/></form>
		<form action="/hum.php"><input type="submit" class="btn btna b4" value="Humidity"/></form>
		<form action="/regen.php"><input type="submit" class="btn b4" value="Regen"/></form>';
elseif ($udevice=='iPhone') echo '
	<body style="width:420px">
		<form action="floorplan.php"><input type="submit" class="btn b4" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btn b4" value="Temperaturen"/></form>
		<form action="/hum.php"><input type="submit" class="btn btna b4" value="Humidity"/></form>
		<form action="/regen.php"><input type="submit" class="btn b4" value="Regen"/></form>';

else 	echo '
	<body style="width:100%">
		<form action="/floorplan.php"><input type="submit" class="btn b4" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btn b4" value="Temperaturen"/></form>
		<form action="/hum.php"><input type="submit" class="btna btn b4" value="Humidity"/></form>
		<form action="/regen.php"><input type="submit" class="btn b4" value="Regen"/></form>';
$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
if ($db->connect_errno>0) die('Unable to connect to database ['.$db->connect_error.']');
//unset($_SESSION['sensors_hum']);
$sensors=array(
	'living_hum'=>array('Naam'=>'Living','Color'=>'#FF1111'),
	'kamer_hum'=>array('Naam'=>'Kamer','Color'=>'#44FF44'),
	'alex_hum'=>array('Naam'=>'Alex','Color'=>'#00EEFF'),
	'waskamer_hum'=>array('Naam'=>'Wask','Color'=>'#EEEE00'),
	'badkamer_hum'=>array('Naam'=>'Badk','Color'=>'#6666FF'),
	'buiten_hum'=>array('Naam'=>'Buiten','Color'=>'#FFFFFF'),
);
foreach ($sensors as $k=>$v) {
	if(isset($_GET[$k]))$_SESSION['sensors_hum'][$k]=true;else $_SESSION['sensors_hum'][$k]=false;
}
$aantalsensors=0;
foreach ($_SESSION['sensors_hum'] as $k=>$v) {
	if ($v==1) $aantalsensors++;
}


echo '<div style="padding:16px 0px 20px 0px;">';
if ($aantalsensors==6) echo '
	<a href="/hum.php?living_hum=on" class="btn Living">Living</a>
	<a href="/hum.php?kamer_hum=on" class="btn Kamer">Kamer</a>
	<a href="/hum.php?alex_hum=on" class="btn Alex">Alex</a>
	<a href="/hum.php?waskamer_hum=on" class="btn Kamer">Wask</a>
	<a href="/hum.php?badkamer_hum=on" class="btn Kamer">Badk</a>
	<a href="/hum.php?buiten_hum=on" class="btn Buiten">Buiten</a>';
else foreach ($sensors as $k=>$v) {
//	print_r($v);
//	$v=ucfirst(str_replace('_hum', '', $k));
	if(isset($_SESSION['sensors_hum'][$k])&&$_SESSION['sensors_hum'][$k]==1) echo '<a href="/hum.php?'.$k.'=on" class="btn '.$k.'">'.$v['Naam'].'</a>';
	else echo '<a href="/hum.php?'.$k.'=on" class="btn '.$k.'">'.$v['Naam'].'</a>';
}
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
	$args['width']=462;$args['height']=710;
	$argshour['width']=462;$argshour['height']=710;
} elseif ($udevice=='iPhoneSE') {
	$args['width']=420;$args['height']=710;
	$argshour['width']=420;$argshour['height']=610;
} elseif ($udevice=='Mac') {
	$args['width']=490;$args['height']=788;
	$argshour['width']=490;$argshour['height']=780;
} else {
	$args['width']=480;$args['height']=610;
	$argshour['width']=480;$argshour['height']=610;
}
$args['colors']=array('#FF6600','#FFFF33','#FFF','#FFFF33','#FF6600');
$argshour['colors']=array('#FF6600','#FFFF33','#FFF','#FFFF33','#FF6600');
if ($aantalsensors==1) $argshour['colors']=array('#FF6600','#FFFF33','#FFF','#FFFF33','#FF6600','#00F', '#0F0', '#F00');
elseif ($aantalsensors==0) {
	$_SESSION['sensors_hum']=array('living_hum'=>1,'waskamer_hum'=>1,'badkamer_hum'=>1,'kamer_hum'=>1,'alex_hum'=>1,'buiten_hum'=>1);
	$aantalsensors=6;
}
//echo '<pre>';print_r($sensors);echo '</pre>';
//echo '<pre>';print_r($_SESSION['sensors_hum']);echo '</pre>';

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
$query.=" from `temp` where stamp >= '$dag' AND living_hum is not null";
//echo $query.'<br>';
if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
if ($result->num_rows==0) {echo 'No data for dates '.$dag.' to '.$f_enddate.'<hr>';goto montha;}
$min=9999;
$max=-1000;
$x=0;
while ($row=$result->fetch_assoc()) {
	$graph[$x]['stamp']=$row['stamp'];
	$graph[$x][40]=40;
	$graph[$x][45]=45;
	$graph[$x][50]=50;
	$graph[$x][55]=55;
	$graph[$x][60]=60;
	foreach ($_SESSION['sensors_hum'] as $k=>$v) {
		if ($v==1) {
			if ($row[$k]>0) $graph[$x][$k]=$row[$k];
			else $graph[$x][$k]=50;
		}
	}
	$x++;
}
$result->free();
foreach ($graph as $t) {
	foreach ($_SESSION['sensors_hum'] as $k=>$v) {
		if ($v==1) {
			if ($aantalsensors==1) {
				if ($t[$k]<$min) $min=$t[$k];
				if ($t[$k]>$max) $max=$t[$k];
			} elseif ($aantalsensors==6) {
				if ($k!='badkamer_hum'&&$k!='buiten_hum'&&$k!='waskamer_hum') {
					if ($t[$k]<$min) $min=$t[$k];
					if ($t[$k]>$max) $max=$t[$k];
				}
			}
		}
	}
}
$min=roundDownToAny($min);
$max=roundUpToAny($max);
if ($min>60) $min=60;
if ($max>100) $max=100;

$args['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"#",textStyle: {color: "#AAA", fontSize: 12},gridlines: {multiple: 10, color: "#444"},minorGridlines: {multiple: 10, color: "#333"},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"},
		series:{0:{lineDashStyle:[5,15]},1:{lineDashStyle:[5,15]},2:{lineDashStyle:[5,15]},3:{lineDashStyle:[5,15]},4:{lineDashStyle:[5,15]},},';
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

$query.=" from `temp` where stamp > '$week' AND living_hum is not null GROUP BY UNIX_TIMESTAMP(stamp) DIV 3600";
if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
if ($result->num_rows==0) {echo 'No data for last week.<hr>';goto enda;} else echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek laatste week.';
$x=0;
while ($row=$result->fetch_assoc()) {
	$graph[$x]['stamp']=$row['stamp'];
	$graph[$x][40]=40;
	$graph[$x][45]=45;
	$graph[$x][50]=50;
	$graph[$x][55]=55;
	$graph[$x][60]=60;
	foreach ($_SESSION['sensors_hum'] as $k=>$v) {
		if ($v==1) {
			if ($aantalsensors==1) {
				if ($row['MIN']>0) $graph[$x]['MIN']=$row['MIN'];
				else $graph[$x]['MIN']=50;
				if ($row['AVG']>0) $graph[$x]['AVG']=$row['AVG'];
				else $graph[$x]['AVG']=50;
				if ($row['MAX']>0) $graph[$x]['MAX']=$row['MAX'];
				else $graph[$x]['MAX']=50;
			} else {
				if ($row[$k]>0) $graph[$x][$k]=$row[$k];
				else $graph[$x][$k]=50;
			}
		}
	}
	$x++;
}
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
				if ($k!='badkamer_hum'&&$k!='buiten_hum'&&$k!='waskamer_hum') {
					if ($t[$k]<$min) $min=$t[$k];
					if ($t[$k]>$max) $max=$t[$k];
				}
			}
		}
	}
}
$min=roundDownToAny($min);
$max=roundUpToAny($max);
if ($min>60) $min=60;
if ($max>100) $max=100;

$argshour['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"#",textStyle: {color: "#AAA", fontSize: 12},gridlines: {multiple: 10, color: "#444"},minorGridlines: {multiple: 10, color: "#333"},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"},
		series:{0:{lineDashStyle:[5,15]},1:{lineDashStyle:[5,15]},2:{lineDashStyle:[5,15]},3:{lineDashStyle:[5,15]},4:{lineDashStyle:[5,15]},},';

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

$query.=" from `temp` where stamp > '$maand' AND living_hum is not null GROUP BY UNIX_TIMESTAMP(stamp) DIV 86400";
if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
$x=0;
while ($row=$result->fetch_assoc()) {
	$graph[$x]['stamp']=$row['stamp'];
	$graph[$x][40]=40;
	$graph[$x][45]=45;
	$graph[$x][50]=50;
	$graph[$x][55]=55;
	$graph[$x][60]=60;
	foreach ($_SESSION['sensors_hum'] as $k=>$v) {
		if ($v==1) {
			if ($aantalsensors==1) {
				if ($row['MIN']>0) $graph[$x]['MIN']=$row['MIN'];
				else $graph[$x]['MIN']=50;
				if ($row['AVG']>0) $graph[$x]['AVG']=$row['AVG'];
				else $graph[$x]['AVG']=50;
				if ($row['MAX']>0) $graph[$x]['MAX']=$row['MAX'];
				else $graph[$x]['MAX']=50;
			} else {
				if ($row[$k]>0) $graph[$x][$k]=$row[$k];
				else $graph[$x][$k]=50;
			}
		}
	}
	$x++;
}
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
				if ($k!='badkamer_hum'&&$k!='buiten_hum'&&$k!='waskamer_hum') {
					if ($t[$k]<$min) $min=$t[$k];
					if ($t[$k]>$max) $max=$t[$k];
				}
			}
		}
	}
}
$min=roundDownToAny($min);
$max=roundUpToAny($max);
if ($min>60) $min=60;
if ($max>100) $max=100;

$argshour['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"#",textStyle: {color: "#AAA", fontSize: 12},gridlines: {multiple: 10, color: "#444"},minorGridlines: {multiple: 10, color: "#333"},viewWindow:{max:'.ceil($max).',min:'.floor($min).'}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"},
		series:{0:{lineDashStyle:[5,15]},1:{lineDashStyle:[5,15]},2:{lineDashStyle:[5,15]},3:{lineDashStyle:[5,15]},4:{lineDashStyle:[5,15]},},';
echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek voor laatste 100 dagen';
$argshour['chart_div']='chart_div';
$chart=array_to_chart($graph, $argshour);
echo $chart['script'];
echo $chart['div'];
unset($chart,$graph);

$togo=61-date("s");
if ($togo<15) $togo=15;
$togo=$togo*1000+62000;
//echo "<br><br><br>refreshing in ".$togo/1000 ." seconds";
echo '<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
$db->close();
