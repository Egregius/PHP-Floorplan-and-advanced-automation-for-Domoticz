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
require '/var/www/authentication.php';
require 'scripts/chart.php';
if ($home===true) {
	if ($user=='Guy') {
		error_reporting(E_ALL);
		ini_set("display_errors", "on");
	}

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
			<meta name="HandheldFriendly" content="true"/>
			<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no, minimal-ui"/>
			<meta name="apple-mobile-web-app-capable" content="yes">
			<meta name="apple-mobile-web-app-status-bar-style" content="black">
			<title>Smappee</title>
			<link href="/styles/temp.css" rel="stylesheet" type="text/css"/>
			<style>
				td{text-align:center;width:24%;}
			</style>
			<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
		</head>
		<body style="width:100%">
			<form action="/floorplan.php">
				<input type="submit" class="btn b2" value="Plan"/>
			</form>
			<form action="/Smappee.php">
				<select name="periode" class="btn b2 btna" onchange="this.form.submit()"/>';
	if (!isset($_REQUEST['periode'])) $_REQUEST['periode']='maand';
	foreach (array('kwartaal', 'maand','dag','AlwaysOn') as $k) {
		if ($k==$_REQUEST['periode']) echo '<option value="'.$k.'" selected>'.$k.'</option>';
		else echo '<option value="'.$k.'">'.$k.'</option>';
	}
	echo '
				</select>
			</form>';
	$colors=array('#FF0000','#00FF00','#0000FF','#FFFF00');
	$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
	if ($db->connect_errno>0) die('Unable to connect to database [' . $db->connect_error . ']');
	$args=array(
			'hide_legend'=>true,
			'responsive'=>true,
			'background_color'=>'#000',
			'colors'=>$colors,
			'margins'=>array(0,0,0,50),
			'y_axis_text_style'=>array('fontSize'=>18,'color'=>'FFFFFF'),
			'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),
			'raw_options'=>'vAxis: {
				//viewWindowMode:\'explicit\',
				//viewWindow:{
				//	min:0
				//},
				textStyle: {color: "#FFFFFF", fontSize: 18}
			},
			series:{
				0:{pointSize:5},
				1:{pointSize:5},
				3:{pointSize:5},
			},
			hAxis: {
				showTextEvery: 300,
				textStyle: {color: "#DDD", fontSize: 0}
			},
			vAxis: {
				format:"#",
				textStyle: {color: "#DDD", fontSize: 14},
				//Gridlines: {multiple: 20},
				//minorGridlines: {multiple: 10}
			},
			//chartArea:{left:0,top:0,width:"100%",height:"100%"}
			'
	);
	if ($udevice=='iPad') {$args['width']=1000;$args['height']=880;}
	elseif ($udevice=='iPhone') {$args['width']=400;$args['height']=550;}
	elseif ($udevice=='Mac') {$args['width']=490;$args['height']=720;}
	else {$args['width']=460;$args['height']=200;}
	$time=time();
	if ($_REQUEST['periode']=='AlwaysOn') {
		$table='smappee_dag';
		$query="SELECT timestamp,alwaysOn from `$table` ORDER BY timestamp DESC limit 0,180";
		if (!$result=$db->query($query)) die('There was an error running the query ['.$query.'-'.$db->error.']');
		if ($result->num_rows==0) {
			echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';exit;
		}
		while ($row=$result->fetch_assoc()) {
			$temp['Date']=strftime("%F", $row['timestamp']);
			$temp['AlwaysOn']=$row['alwaysOn']/10000;
			$data[]=$temp;
		}
		$result->free();
		echo '<a href="Smappee.php?periode='.$_REQUEST['periode'].'"><h1>AlwaysOn</h1></a>';
		$y=2018;
		sort($data);
		$args['chart_div']='AlwaysOn';
		$chart=array_to_chart($data, $args);
		echo $chart['script'];
		echo $chart['div'];
		unset($chart);
		echo '<pre>';print_r($data);echo '</pre>';
	} else {
		if ($_REQUEST['periode']=='kwartaal') $months=array('01'=>'Jan-Feb-Maa','04'=>'April-Mei-Jun','07'=>'Jul-Aug-Sep','10'=>'Okt-Nov-Dec');
		else $months=array('01'=>'Januari','02'=>'Februari','03'=>'Maart','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Augustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'December');
		for($y=2018;$y<=strftime("%Y",$time);$y++){
			foreach($months as $m=>$ms){
				foreach (array('consumption','solar','alwaysOn','gridImport','gridExport','selfConsumption','selfSufficiency') as $t) {
					${$t}[$m]['Maand']=$ms;
					${$t}[$m][$y]=0;
				}
			}
		}
		$table='smappee_'.$_REQUEST['periode'];
		$query="SELECT timestamp,consumption,solar,alwaysOn,gridImport,gridExport,selfConsumption,selfSufficiency from `$table` ORDER BY timestamp ASC";
		if (!$result=$db->query($query)) die('There was an error running the query ['.$query.'-'.$db->error.']');
		if ($result->num_rows==0) {
			echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';exit;
		}
		while ($row=$result->fetch_assoc()) {
			$y=strftime("%Y", $row['timestamp']);
			$m=strftime("%m", $row['timestamp']);
			foreach (array('consumption','solar','alwaysOn','gridImport','gridExport','selfConsumption','selfSufficiency') as $t) {
				if (startsWith($t, 'self')) {
					${$t}[$m][$y]=$row[$t];
				} else {
					${$t}[$m][$y]=$row[$t]/1000;
				}
			}
		}
		$result->free();
		foreach (array('consumption','solar','alwaysOn','gridImport','gridExport','selfConsumption','selfSufficiency') as $t) {
			echo '<a href="Smappee.php?periode='.$_REQUEST['periode'].'"><h1>'.$t.'</h1></a>';
			$y=2018;
			foreach ($colors as $c) {
				echo ' <span style="color:'.$c.'"> '.$y.' </span> &nbsp; ';
				$y++;
			}
			$args['chart_div']=$t;
			$chart=array_to_chart(${$t}, $args);
			echo $chart['script'];
			echo $chart['div'];
			unset($chart);
			//echo '<pre>';print_r(${$t});echo '</pre>';
		}
	}
	$db->close();
} else {
	header("Location: index.php");
	die("Redirecting to: index.php");
}
