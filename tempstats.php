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
    if (isset($_REQUEST['f_startdate'])) {
        $_SESSION['f_startdate']=$_REQUEST['f_startdate'];
    }
    if (isset($_REQUEST['f_enddate'])) {
        $_SESSION['f_enddate']=$_REQUEST['f_enddate'];
    }
    /*if(!isset($_SESSION['f_startdate']))*/$_SESSION['f_startdate']=date("Y-m", TIME);
    /*if(!isset($_SESSION['f_enddate']))*/$_SESSION['f_enddate']=date("Y-m", TIME);
    if (isset($_REQUEST['clear'])) {
        $_SESSION['f_startdate']=$_REQUEST['r_startdate'];
        $_SESSION['f_startdate']=$_REQUEST['r_startdate'];
    }
    if ($_SESSION['f_startdate']>$_SESSION['f_enddate']) {
        $_SESSION['f_enddate']=$_SESSION['f_startdate'];
    }
    $f_startdate=$_SESSION['f_startdate'];
    $f_enddate=$_SESSION['f_enddate'];
    $r_startdate=date("Y-m-d", TIME);
    $r_enddate=date("Y-m-d", TIME);
    $week=date("Y-m-d", TIME-86400*6);
    $items=array('buiten', 'living', 'kamer', 'tobi', 'alex');
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="viewport" content="width=device-width,height=device-height, user-scalable=yes, minimal-ui"/>
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<title>Temperaturen</title>
		<link href="/styles/temp.php" rel="stylesheet" type="text/css"/>
		<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
		<style>
			table{border:1px solid white;}
			td{text-align:right;border:1px solid #555;min-width:80px;}
		</style>
		</head>
		<body style="width:100%">
			<form method="GET">';
	foreach ($items as $i) {
		echo '
				<input type="checkbox" name="'.$i.'" '.(isset($_REQUEST[$i])?'checked':'').' onChange="submit()">'.ucfirst($i).'</input>';
		if (isset($_REQUEST[$i])) $kamers[]=$i;
	}
	if (!isset($kamers)) $kamers=array('buiten');
	echo '<br>';
	for ($x=0;$x<=23;$x++) {
		echo '
				<input type="checkbox" name="'.$x.'" '.(isset($_REQUEST[$x])?'checked':'').' onChange="submit()">'.$x.'</input>';
		if (isset($_REQUEST[$x])) $hours[$x]=$x;
	}
	if (!isset($hours)) {
		for ($x=0;$x<=23;$x++) {
			$hours[$x]=$x;
		}
	}
	//print_r($hours);
	echo '
			</form>';
    $db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
    if ($db->connect_errno>0) {
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    /*$query="SELECT MIN(stamp) AS start FROM temp_hour WHERE stamp like '2019%'";
	$result=$db->query($query);
	while ($row=$result->fetch_assoc()) $startdate=$row['start'];
	$query="SELECT COUNT(stamp) AS aantal FROM temp_hour WHERE stamp like '2019%'";
	$result=$db->query($query);
	while ($row=$result->fetch_assoc()) $aantal=$row['aantal'];*/
	
	
	$query="SELECT stamp";
	foreach ($kamers as $i) $query.=", ".$i."_avg as $i";
	$query.=" FROM temp_hour WHERE stamp like '2019%'";
	$result=$db->query($query);
	while ($row=$result->fetch_assoc()) $datas[]=$row;

	//print_r($datas);
	foreach ($kamers as $i) {
		for ($x=30;$x>=0;$x--) ${$i.$x}=0;
	}
	foreach ($datas as $a) {
		$hour=substr($a['stamp'], 11, 2) * 1;
		$day=substr($a['stamp'], 0, 10);
		if (in_array($hour, $hours)) {
			foreach ($kamers as $i) {
				for ($x=30;$x>=0;$x--) {
					if ($a[$i]>=$x) {
						${$i.$x}++;
						@$dag[$i.$x][$day]=true;
					}
				}
			}
			//echo $a['stamp'].' = '.$hour.'<br>';
			//print_r($a);
		}
	}
	//unset ($datas);echo '<pre>';print_r(GET_DEFINED_VARS());echo '</pre>';exit;
	echo '
	<table>
		<thead>
			<tr>
				<th></th>';
	foreach ($kamers as $i) echo '
				<th colspan="4">'.ucfirst($i).'</th>';
	echo '
			</tr>
			<tr>
				<th></th>';
	foreach ($kamers as $i) echo '
				<th colspan="2">Dagen</th><th colspan="2">Uren</th>';
	echo '
			</tr>
			<tr>
				<th>Temp</th>';
	foreach ($kamers as $i) echo '
				<th>Aantal</th><th>Percent</th>';
	echo '
			</tr>
		</thead>
		<tbody>';
	$aantal=0;
	foreach ($kamers as $i) {
		if (${$i.'0'}>$aantal) $aantal = ${$i.'0'};
	}
	for ($x=30;$x>=0;$x--) {
		echo '
			<tr>
				<td>'.$x.'</td>';
		foreach ($kamers as $i) {
			echo '
				<td>'.count($dag[$i.$x]).'</td>
				<td>'.number_format((${$i.$x}/$aantal)*100, 2, ',', '').' %</td>
				<td>'.${$i.$x}.'</td>
				<td>'.number_format((${$i.$x}/$aantal)*100, 2, ',', '').' %</td>';
		}
		echo '
			</tr>';
	}
	echo '
		</tbody>
	</table>';
    $db->close();
    
} else {
    header("Location: index.php");
    die("Redirecting to: index.php");
}
?>