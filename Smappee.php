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
require 'secure/authentication.php';
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
	foreach (array('kwartaal', 'maand','dag') as $k) {
		if ($k==$_REQUEST['periode']) echo '<option value="'.$k.'" selected>'.$k.'</option>';
		else echo '<option value="'.$k.'">'.$k.'</option>';
	}
	echo '
				</select>
			</form>';
	$colors=array('#FFFFFF','#FF0000','#00FF00','#0000FF','#FFFF00');
	$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
	if ($db->connect_errno>0) die('Unable to connect to database [' . $db->connect_error . ']');
	$args=array(
			'width'=>1100,
			'height'=>1500,
			'hide_legend'=>false,
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
				0:{lineDashStyle:[0,0]},
				1:{lineDashStyle:[0,0]},
				3:{lineDashStyle:[0,0]},
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
	elseif ($udevice=='iPhone') {$args['width']=360;$args['height']=240;}
	elseif ($udevice=='Mac') {$args['width']=460;$args['height']=300;}
	else {$args['width']=460;$args['height']=200;}
	$time=time();
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
		echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';goto end;
	}
	while ($row=$result->fetch_assoc()) {
		$y=strftime("%Y", $row['timestamp']);
		$m=strftime("%m", $row['timestamp']);
		foreach (array('consumption','solar','alwaysOn','gridImport','gridExport','selfConsumption','selfSufficiency') as $t) {
			if (startsWith($t, 'self')) ${$t}[$m][$y]=$row[$t];
			else ${$t}[$m][$y]=$row[$t]/1000;
		}
	}
	$result->free();
	foreach (array('consumption','solar','alwaysOn','gridImport','gridExport','selfConsumption','selfSufficiency') as $t) {
		echo '<h1>'.$t.'</h1>';
		$y=2017;
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
	exit;





	echo '<h3>Pluviometer per dag</h3>';
	$args['chart']='ColumnChart';
	$args['margins']=array(0,0,50,50);
	$args['colors']=array('#44C');
	$args['hide_legend']=true;
	$args['chart_div']='pluvioday';
	$args['raw_options']='seriesType:"bars",
		seriesDefaults: {
			rendererOptions: {
				barPadding: -50,
				barMargin: -50
			}
		},
		hAxis: {
			showTextEvery: 100,
			textStyle: {color: "#000", fontSize: 1}
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
		bar:{groupWidth:90}';
	$chart=array_to_chart($pluvio, $args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
	echo '<h3>Pluviometer per maand</h3>';
	$args['colors']=array('#44CC44','#4444CC');
	$args['chart_div']='pluviomonth';
	//$args['chart']='ComboChart';
	$args['raw_options']='
		lineWidth:3,
		series: {
			0: {type: "steppedArea", areaOpacity:0.3},
			1: {type: "bars", areaOpacity:0.1, groupWidth:40}
		},
		hAxis: {
			showTextEvery: 1,
			textStyle: {color: "#EEE", fontSize: 0}
		},
		vAxis: {
			format:"#",
			textStyle: {color: "#CCC", fontSize: 14},
			Gridlines: {
				multiple: 10
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
		bar:{groupWidth:40}';
	$chart=array_to_chart($pluviomaand, $args);
	echo $chart['script'];
	echo $chart['div'];
	unset($chart);
	$total=0;
	$current=0;
	foreach ($pluviomaand as $i) {
		$total=$total+$i['Normaal'];
		$current=$current+$i['Regen'];
	}
	echo '<h3><center>'.$current.' mm / '.$total.' mm = '.(number_format(($current/$total)*100, 0)).' %</center></h3>';
	//echo '<pre>';print_r($pluviomaand);echo '</pre>';
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
					<td> '.number_format($i['Regen'], 0).' mm </td>
					<td> '.$i['Normaal'].' mm </td>
					<td> '.number_format(($i['Regen']/$i['Normaal'])*100, 0, ',', '.').' % </td>
				</tr>';
	}
	echo '
			</tbody>
		</table>
		<br>
		<br>
		<br>';
	end:
	if ($f_startdate==$r_startdate&&$f_enddate==$r_enddate) {
		$togo=61-date("s");
		if ($togo<15) {
			$togo=15;
		}
		$togo=$togo*1000+2000;
		echo '<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
	}
	$db->close();
} else {
	header("Location: index.php");
	die("Redirecting to: index.php");
}
