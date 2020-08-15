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
	if (isset($_POST['addregen'])) {
		$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
		if ($db->connect_errno>0) {
			die('Unable to connect to database [' . $db->connect_error . ']');
		}
		/*for ($x=60;$x>=1;$x--) {
			$date=date("Y-m-d", (TIME-($x*86400)));
			$query="INSERT IGNORE INTO `pluvio` (`date`, `rain`) VALUES ('$date', '0');";
			if(!$result=$db->query($query)){die('There was an error running the query ['.$query.'-'.$db->error.']');}
		}*/
		$date=date("Y-m-d", TIME);
		$value=$_POST['addregen'];
		$query="INSERT INTO `pluvio` (`date`, `rain`) VALUES ('$date', '$value') ON DUPLICATE KEY Update rain=rain+$value;";
		if(!$result=$db->query($query)){die('There was an error running the query ['.$query.'-'.$db->error.']');}
		
	}
	if (isset($_REQUEST['add'])) {
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
				<meta name="HandheldFriendly" content="true"/>
				<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no, minimal-ui"/>
				<meta name="apple-mobile-web-app-capable" content="yes">
				<meta name="apple-mobile-web-app-status-bar-style" content="black">
				<title>Regenvoorspelling</title>
				<link href="/styles/temp.php" rel="stylesheet" type="text/css"/>
				<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
				<style>
					.btn{margin-left:0!important;}
				</style>
			</head>';
		if ($udevice=='iPad') {
			echo '
			<body style="width:800px">
				<form action="/floorplan.php">
					<input type="submit" class="btn b5" value="Plan"/>
				</form>
				<form action="/temp.php">
					<input type="submit" class="btn b5" value="Temperaturen"/>
				</form>
				<form action="/regen.php">
					<input type="submit" class="btn b5" value="Regen"/>
					<input type="submit" class="btn btna b1" name="add" value="Regen invullen"/>
				</form>';
		} else {
			echo '
			<body style="width:100%">
				<form action="/floorplan.php">
					<input type="submit" class="btn b3" value="Plan"/>
				</form>
				<form action="/temp.php">
					<input type="submit" class="btn b3" value="Temperaturen"/>
				</form>
				<form action="/regen.php">
					<input type="submit" class="btn b3" value="Regen"/>
					<input type="submit" class="btn btna b1" name="add" value="Regen invullen"/>
				</form>';
		}
		echo '<form action="/regen.php" method="POST">';
		for ($x=1;$x<=30;$x++) {
			echo '
					<input type="submit" class="btn b5" name="addregen" value="'.$x.'" style="height:70px"/>';
		}
		echo '
				</form>';
	} else {
		if ($user=='Guy') {
			error_reporting(E_ALL);
			ini_set("display_errors", "on");
		}
		$sensor=998;
		if (isset($_REQUEST['sensor'])) {
			$sensor=$_REQUEST['sensor'];
		}
		if (isset($_REQUEST['f_startdate'])) {
			$_SESSION['f_startdate']=$_REQUEST['f_startdate'];
		}
		if (isset($_REQUEST['f_enddate'])) {
			$_SESSION['f_enddate']=$_REQUEST['f_enddate'];
		}
		if (isset($_REQUEST['f_setpoints'])) {
			$_SESSION['f_setpoints']==0?$_SESSION['f_setpoints']=1:$_SESSION['f_setpoints']=0;
		}
		if (isset($_REQUEST['f_heater'])) {
			$_SESSION['f_heater']==0?$_SESSION['f_heater']=1:$_SESSION['f_heater']=0;
		}
		if (!isset($_SESSION['f_startdate'])) {
			$_SESSION['f_startdate']=date("Y-m-d", TIME-86400);
		}
		if (!isset($_SESSION['f_enddate'])) {
			$_SESSION['f_enddate']=date("Y-m-d", TIME);
		}
		if (!isset($_SESSION['f_setpoints'])) {
			$_SESSION['f_setpoints']=0;
		}
		if (!isset($_SESSION['f_heater'])) {
			$_SESSION['f_heater']=0;
		}
		if (isset($_REQUEST['clear'])) {
			$_SESSION['f_startdate']=$_REQUEST['r_startdate'];
			$_SESSION['f_startdate']=$_REQUEST['r_startdate'];
		}
		if ($_SESSION['f_startdate']>$_SESSION['f_enddate']) {
			$_SESSION['f_enddate']=$_SESSION['f_startdate'];
		}
		$f_startdate=$_SESSION['f_startdate'];
		$f_enddate=$_SESSION['f_enddate'];
		$f_setpoints=$_SESSION['f_setpoints'];
		$f_heater=$_SESSION['f_heater'];
		$r_startdate=date("Y-m-d", TIME);
		$r_enddate=date("Y-m-d", TIME);
		$maand=date("Y-m", strtotime($f_startdate));
		$jaar=date("Y", strtotime($f_startdate));
		//if($user=='Guy'){echo '<pre>';print_r($_REQUEST);print_r($_SESSION);echo '</pre>';}

		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
				<meta name="HandheldFriendly" content="true"/>
				<meta name="viewport" content="width=device-width,height=device-height, user-scalable=no, minimal-ui"/>
				<meta name="apple-mobile-web-app-capable" content="yes">
				<meta name="apple-mobile-web-app-status-bar-style" content="black">
				<title>Regenvoorspelling + Pluviometer</title>
				<link href="/styles/temp.php" rel="stylesheet" type="text/css"/>
				<style>
					td{text-align:center;width:24%;}
				</style>
				<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
			</head>';
		if ($udevice=='iPad') {
			echo '
			<body style="width:800px">
				<form action="/floorplan.php">
					<input type="submit" class="btn b5" value="Plan"/>
				</form>
				<form action="/temp.php">
					<input type="submit" class="btn b5" value="Temperaturen"/>
				</form>
				<form action="/regen.php">
					<input type="submit" class="btn btna b5" value="Regen"/>
					<input type="submit" class="btn b1" name="add" value="Regen invullen"/>
				</form>';
		} else {
			echo '
			<body style="width:100%">
				<form action="/floorplan.php">
					<input type="submit" class="btn b3" value="Plan"/>
				</form>
				<form action="/temp.php">
					<input type="submit" class="btn b3" value="Temperaturen"/>
				</form>
				<form action="/regen.php">
					<input type="submit" class="btn btna b3" value="Regen"/>
					<input type="submit" class="btn b1" name="add" value="Regen invullen"/>
				</form>';
		}
		$db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
		if ($db->connect_errno>0) {
			die('Unable to connect to database [' . $db->connect_error . ']');
		}
		$eendag=TIME-86400*2;$eendagstr=strftime("%Y-%m-%d %H:%M:%S", $eendag);
		$eenweek=TIME-86400*7;$eenweekstr=strftime("%Y-%m-%d %H:%M:%S", $eenweek);
		$eenmaand=TIME-86400*31;$eenmaandstr=strftime("%Y-%m-%d", $eenmaand);
		$buienradar='#FF1111';
		$darksky='#FFFF44';
		$buien='#44FF44';
		echo '<h3>Voospellingen</h3>';
		$legend='
				<div style="width:379px;padding:15px 0px 10px 4px;">
					&nbsp;<a href=\'javascript:navigator_Go("regen.php");\'><font color="'.$buienradar.'">Buienradar</font></a>
					&nbsp;<a href=\'javascript:navigator_Go("regen.php");\'><font color="'.$darksky.'">DarkSky</font></a>
					&nbsp;<a href=\'javascript:navigator_Go("regen.php");\'><font color="'.$buien.'">Buien</font></a>
				</div>
	';
		echo $legend;
		$colors=array($buienradar,$darksky,$buien);
		$query="SELECT DATE_FORMAT(stamp, '%W %H:%i') as stamp,buienradar,darksky,buien from `regen` where stamp >= '$f_startdate 00:00:00' ORDER BY DATE_FORMAT(stamp, '%Y%m%d%H%i') ASC";
		$args=array(
				'width'=>1000,
				'height'=>880,
				'hide_legend'=>true,
				'responsive'=>false,
				'background_color'=>'#000',
				'chart_div'=>'graph',
				'colors'=>$colors,
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
				chartArea:{left:0,top:0,width:"100%",height:"100%"}'
        );
		if ($udevice=='iPad') {$args['width']=1000;$args['height']=880;}
		elseif ($udevice=='iPhone') {$args['width']=360;$args['height']=240;}
		elseif ($udevice=='Mac') {$args['width']=460;$args['height']=300;}
		else {$args['width']=460;$args['height']=200;}
		if (!$result=$db->query($query)) die('There was an error running the query ['.$query.'-'.$db->error.']');
		if ($result->num_rows==0) {
			echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';goto end;
		}
		while ($row=$result->fetch_assoc()) $graph[]=$row;
		$result->free();
		$chart=array_to_chart($graph, $args);
		echo $chart['script'];
		echo $chart['div'];
		unset($chart);
		$query="SELECT DATE_FORMAT(`date`, '%e/%c') as date, rain FROM `pluvio` WHERE `date` > '$eenmaandstr' ORDER BY DATE_FORMAT(`date`, '%Y%m%d') ASC;";
		if (!$result=$db->query($query)) die('There was an error running the query ['.$query.'-'.$db->error.']');
		while ($row=$result->fetch_assoc()) $pluvio[]=$row;
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
		echo '<h3>Pluviometer per dag</h3>';
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
					}
				  },
				chartArea:{left:0,top:0,width:"100%",height:"100%"},
				bar:{groupWidth:100}';
        $chart=array_to_chart($pluvio, $args);
		echo $chart['script'];
		echo $chart['div'];
		unset($chart);
		echo '<h3>Pluviometer per maand</h3>';
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
				bar:{groupWidth:100}';
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
		if ($f_startdate==$r_startdate&&$f_enddate==$r_enddate) {
			$togo=61-date("s");
			if ($togo<15) {
				$togo=15;
			}
			$togo=$togo*1000+2000;
			echo '<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
		}
		$db->close();
	}
} else {
    header("Location: index.php");
    die("Redirecting to: index.php");
}