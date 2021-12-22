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
$sensor=998;
if (isset($_REQUEST['sensor'])) $sensor=$_REQUEST['sensor'];
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
	<link href="/styles/temp.css?v=2" rel="stylesheet" type="text/css"/>
	<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
	</head>';
if ($udevice=='iPad') echo '
	<body style="width:800px">
		<form action="floorplan.php"><input type="submit" class="btn b5" value="Plan"/></form>
		<form action="/temp.php"><input type="submit" class="btn btna b5" value="Temperaturen"/></form>
		<form action="/regen.php"><input type="submit" class="btn b5" value="Regen"/></form>';
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
switch($sensor){
	case 147:$sensornaam='living';
		break;
	case 246:$sensornaam='badkamer';
		break;
	case 278:$sensornaam='kamer';
		break;
	case 356:$sensornaam='speelkamer';
		break;
	case 293:$sensornaam='zolder';
		break;
	case 244:$sensornaam='alex';
		break;
	case 998:$sensornaam='binnen';
		break;
	case 999:$sensornaam='alles';
		break;
	default:$sensornaam='buiten';
		break;
}
$sensor=$sensornaam;
$living='#FF1111';
$badkamer='#6666FF';
$kamer='#44FF44';
$alex='#00EEFF';
$speelkamer='#EEEE00';
$zolder='#EE33EE';
$buiten='#FFFFFF';
$legend='<div style="width:420px;padding:20px 0px 10px 0px;">
	&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=147");\'><font color="'.$living.'">Living</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=246");\'><font color="'.$badkamer.'">Badk</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=278");\'><font color="'.$kamer.'">Kamer</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=244");\'><font color="'.$alex.'">Alex</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=293");\'><font color="'.$zolder.'">Zolder</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=329");\'><font color="'.$buiten.'">Buiten</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=998");\'><font color="'.$buiten.'">Binnen</font></a>
	&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=999");\'><font color="'.$buiten.'">Alles</font></a></div>';
echo $legend;
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
	$args['width']=1000;$args['height']=880;
	$argshour['width']=1000;$argshour['height']=880;
} elseif ($udevice=='iPhone') {
	$args['width']=420;$args['height']=610;
	$argshour['width']=420;$argshour['height']=710;
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
if ($sensor=='alles') {
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
	//echo '<br/>'.$legend;
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
} elseif ($sensor=='binnen') {
	$args['colors']=array($living,$badkamer,$kamer,$alex);
	$argshour['colors']=array($living,$badkamer,$kamer,$alex);
	$args['line_styles']=array('lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[1,8]','lineDashStyle:[1,8]');
	$argshour['line_styles']=array('lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[1,8]','lineDashStyle:[1,8]');
	$query="SELECT DATE_FORMAT(stamp, '%H:%i') as stamp, living,badkamer,kamer,alex from `temp` where stamp >= '$dag' AND stamp <= '$f_enddate 23:59:59'";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
	if ($result->num_rows==0) {echo 'No data for dates '.$dag.' to '.$f_enddate.'<hr>';goto monthb;}
	while ($row=$result->fetch_assoc()) $graph[]=$row;
	$result->free();
	$min=9999;
	$max=-1000;
	foreach ($graph as $t) {
		foreach (array('living','badkamer','kamer','alex') as $i) {
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
	$chart=array_to_chart($graph, $args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart,$graph);
	monthb:
//	$query="SELECT DATE_FORMAT(stamp, '%W %k') as stamp, AVG(living),AVG(badkamer),AVG(kamer),AVG(speelkamer),AVG(alex) from `temp` where stamp > '$week'";
	$query="SELECT DATE_FORMAT(stamp, '%W %k:%i') as stamp, AVG(living) AS living, AVG(badkamer) as badkamer, AVG(kamer) as kamer, AVG(alex) as alex from `temp` where stamp > '$week' GROUP BY UNIX_TIMESTAMP(stamp) DIV 3600";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
	if ($result->num_rows==0) {echo 'No data for last week<hr>';goto endb;} else echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek voor laatste week';
	while ($row=$result->fetch_assoc()) $graph[]=$row;
	$result->free();
	$min=9999;
	$max=-1000;
	foreach ($graph as $t) {
		foreach (array('living','badkamer','kamer','alex') as $i) {
			if ($t[$i]<$min) $min=$t[$i];
			if ($t[$i]>$max) $max=$t[$i];
		}
	}
	$argshour['raw_options']='
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
	endb:
//	$query="SELECT DATE_FORMAT(stamp, '%Y-%m-%d') as stamp, AVG(living_avg) as living, AVG(badkamer_avg) as badkamer, AVG(kamer_avg) as kamer, AVG(speelkamer_avg) as speelkamer, AVG(alex_avg) as alex from `temp` where stamp > '$maand' 	GROUP BY DATE_FORMAT(stamp, '%Y%m%d')";
	$query="SELECT DATE_FORMAT(stamp, '%d-%m-%Y %k:%i') as stamp, AVG(living) as living, AVG(badkamer) as badkamer, AVG(kamer) as kamer, AVG(alex) as alex from `temp` where stamp > '$maand' GROUP BY UNIX_TIMESTAMP(stamp) DIV 86400";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
	while ($row=$result->fetch_assoc()) $graph[]=$row;
	$result->free();
	$min=9999;
	$max=-1000;
	foreach ($graph as $t) {
		foreach (array('living','badkamer','kamer','alex') as $i) {
			if ($t[$i]<$min) $min=$t[$i];
			if ($t[$i]>$max) $max=$t[$i];
		}
	}
	$argshour['raw_options']='
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
} else {
	$args['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 14},Gridlines: {multiple: 1},minorGridlines: {multiple: 1}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
	$argshour['raw_options']='
		lineWidth:3,
		crosshair:{trigger:"both"},
		vAxis: {format:"# °C",textStyle: {color: "#AAA", fontSize: 14},Gridlines: {multiple: 1},minorGridlines: {multiple: 1}},
		hAxis:{textPosition:"none"},
		theme:"maximized",
		chartArea:{left:0,top:0,width:"100%",height:"100%"}';
	$args['line_styles']=array('lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[1,8]');
	$argshour['line_styles']=array('lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[1,8]');
	if ($sensor=='badkamer') {
		$args['colors']=array(${$sensornaam},${$sensornaam},'#ffb400');
		$argshour['colors']=array('#00F','#F00','#0F0');
	} else {
		$args['colors']=array(${$sensornaam},${$sensornaam},'#FFFF00');
		$argshour['colors']=array('#00F','#F00','#0F0');
	}
	$query="SELECT DATE_FORMAT(stamp, '%H:%i') as stamp,$sensor from `temp` where stamp >= '$dag' AND stamp <= '$f_enddate 23:59:59'";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query .' - '.$db->error.']');
	if ($result->num_rows==0) {echo 'No data for dates '.$dag.' to '.$f_enddate.'<hr>';goto month;}
	while ($row=$result->fetch_assoc()) $graph[]=$row;
	$result->free();
	$chart=array_to_chart($graph, $args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart,$graph);
	month:
	$query="SELECT DATE_FORMAT(stamp, '%W %k:%i') as stamp, MIN($sensor), MAX($sensor), AVG($sensor) from `temp` where stamp > '$week' GROUP BY UNIX_TIMESTAMP(stamp) DIV 3600";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
	if ($result->num_rows==0) {echo 'No data for last week<hr>';goto end;} else echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Graph for last week';
	while ($row=$result->fetch_assoc()) $graph[]=$row;
	$result->free();

	$chart=array_to_chart($graph, $argshour);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart,$graph);
	$query="SELECT DATE_FORMAT(stamp, '%d-%m-%Y %k:%i') as stamp, MIN($sensor), MAX($sensor), AVG($sensor) from `temp` where stamp > '$maand' GROUP BY UNIX_TIMESTAMP(stamp) DIV 86400";

	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.' - '.$db->error.']');
	while ($row=$result->fetch_assoc()) $graph[]=$row;
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek voor laatste 60 dagen';
	$argshour['chart_div']='chart_div';
	$chart=array_to_chart($graph, $argshour);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart,$graph);
	end:
}
$togo=61-date("s");
if ($togo<15) $togo=15;
$togo=$togo*1000+62000;
echo "<br>$udevice<br><br>refreshing in ".$togo/1000 ." seconds";
echo '<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
$db->close();
