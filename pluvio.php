<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
require 'scripts/chart.php';
session_start();
		error_reporting(E_ALL);
		ini_set("display_errors", "on");
	$f_startdate=date("Y-m-d", TIME-86400);
	//if($user=='Guy'){echo '<pre>';print_r($_REQUEST);print_r($_SESSION);echo '</pre>';}

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
			<meta name="HandheldFriendly" content="true"/>
			<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no, minimal-ui"/>
			<meta name="apple-mobile-web-app-capable" content="yes">
			<meta name="apple-mobile-web-app-status-bar-style" content="black">
			<title>Pluviometer</title>
			<link href="/styles/temp.php" rel="stylesheet" type="text/css"/>
			<style>
					td{text-align:center;width:24%;}
				</style>
			<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
		</head>
		<body style="width:100%">';
	$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
	if ($db->connect_errno>0) {
		die('Unable to connect to database [' . $db->connect_error . ']');
	}
	$eenmaand=TIME-86400*31;$eenmaandstr=strftime("%Y-%m-%d", $eenmaand);

	$args=array(
			'width'=>600,
			'height'=>600,
			'hide_legend'=>true,
			'responsive'=>false,
			'background_color'=>'#000',
			'chart_div'=>'graph',
			'margins'=>array(0,0,0,50),
			'y_axis_text_style'=>array('fontSize'=>18,'color'=>'FFFFFF'),
			'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),
			 'raw_options'=>'vAxis: {
				 viewWindowMode:\'explicit\',
				 viewWindow:{
					min:0
				  },
				 textStyle: {color: "#FFFFFF", fontSize: 18}
			},
			series:{
				0:{lineDashStyle:[2,2]},
				1:{lineDashStyle:[2,2]},
				3:{lineDashStyle:[0,0],pointSize:5},
			},
			hAxis: {
				showTextEvery: 300,
				textStyle: {color: "#DDD", fontSize: 0}
			},
			vAxis: {
				format:"#",
				textStyle: {color: "#AAA", fontSize: 14},
				Gridlines: {
					multiple: 20
				},
				minorGridlines: {
					multiple: 10
				}
			  },
			theme:"maximized",
			chartArea:{left:0,top:0,width:"100%",height:"100%"}'
	);

	$query="SELECT DATE_FORMAT(`date`, '%e/%c') as Datum, rain as Regen FROM `pluvio` WHERE `date` > '$eenmaandstr' ORDER BY DATE_FORMAT(`date`, '%Y%m%d') ASC;";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.'-'.$db->error.']');
	while ($row=$result->fetch_assoc()) $pluvio[]=$row;
	//echo '<pre>';print_r($pluvio);echo '</pre>';
	$query="SELECT month, rain FROM `pluvioklimaat`;";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.'-'.$db->error.']');
	while ($row=$result->fetch_assoc()) $klimaat[$row['month']]=$row['rain'];
	
	$query="SELECT YEAR(date) as year, MONTH(date) as month, SUM(rain) as rain FROM pluvio GROUP BY YEAR(date), MONTH(date) ORDER BY DATE_FORMAT(`date`, '%Y%m%d') ASC;";
	if (!$result=$db->query($query)) die('There was an error running the query ['.$query.'-'.$db->error.']');
	while ($row=$result->fetch_assoc()) {
		$pluviomaand[$row['month'].'-'.$row['year']]['Maand']=$row['month'].'-'.$row['year'];
		$pluviomaand[$row['month'].'-'.$row['year']]['Normaal']=$klimaat[$row['month']];
		$pluviomaand[$row['month'].'-'.$row['year']]['Regen']=$row['rain'];
	}
	$result->free();
	echo '<div style="float:left;margin:30px"><h3>Pluviometer per dag</h3>';
	$args['chart']='ColumnChart';
	$args['margins']=array(0,0,50,50);
	$args['colors']=array('#44C');
	$args['hide_legend']=true;
	$args['chart_div']='pluvioday';
	$args['raw_options']='seriesType:"bars",seriesDefaults: {

		rendererOptions: {
			barPadding: -50,
			barMargin: -50
		}
	},
			hAxis: {
				showTextEvery: 100,
				textStyle: {color: "#DDD", fontSize: 1}
			},
			vAxis: {
				format:"#",
				textStyle: {color: "#AAA", fontSize: 14},
				Gridlines: {
					multiple: 2
				},
				minorGridlines: {
					multiple: 0
				}
			  },
			theme:"maximized",
			chartArea:{left:0,top:0,width:"100%",height:"100%"},
			bar:{groupWidth:100}';
	$chart=array_to_chart($pluvio, $args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
	echo '</div><div style="float:left;margin:30px"><h3>Pluviometer per maand</h3>';
	$args['chart_div']='pluviomonth';
	$args['chart']='ComboChart';
	$args['raw_options']='
		lineWidth:3,
		seriesType:"steppedArea",
		series: {
			0: {type: "steppedArea", areaOpacity:0.2},
			1: {type: "bars", areaOpacity:0.7, groupWidth:60}
		},
			hAxis: {
				showTextEvery: 1,
				textStyle: {color: "#DDD", fontSize: 0}
			},
			vAxis: {
				format:"#",
				textStyle: {color: "#AAA", fontSize: 14},
				Gridlines: {
					multiple: 20
				},
				minorGridlines: {
					multiple: 10
				},
				viewWindowMode:\'explicit\',
				 viewWindow:{
					min:0
				  },
				 
			  },
			theme:"maximized",
			chartArea:{left:0,top:0,width:"100%",height:"100%"},
			bar:{groupWidth:60}';
	$chart=array_to_chart($pluviomaand, $args);
	// '<pre>';print_r($pluviomaand);echo '</pre>';
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
	$total=0;
	$current=0;
	foreach ($pluviomaand as $i) {
		$total=$total+$i['Normaal'];
		$current=$current+$i['Regen'];
	}
	echo '
	<h3><center>'.$current.' mm / '.$total.' mm = '.(number_format(($current/$total)*100, 0)).' %</center></h3></div>';
	echo '
			<table>
				<thead>
					<tr>
						<th>Maand</th>
						<th>Regen</th>
						<th>Normaal</th>
						<th>Procent</th>
					</tr>
				</thead>
				<tbody>';
		foreach ($pluviomaand as $i) {
			echo '
					<tr>
						<td>'.$i['Maand'].'</td>
						<td> '.$i['Regen'].' mm </td>
						<td> '.$i['Normaal'].' mm </td>
						<td> '.number_format(($i['Regen']/$i['Normaal'])*100, 2, ',', '.').' % </td>
					</tr>';
		}
		echo '
				</tbody>
			</table>
			<br>
			<br>
			<br>';
	end:
	$db->close();
