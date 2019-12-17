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
		</head>
		<body style="width:100%">';
    $db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
    if ($db->connect_errno>0) {
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    $query="SELECT MIN(stamp) AS start FROM temp_hour";
	$result=$db->query($query);
	while ($row=$result->fetch_assoc()) $startdate=$row['start'];
	$query="SELECT COUNT(stamp) AS aantal FROM temp_hour";
	$result=$db->query($query);
	while ($row=$result->fetch_assoc()) $aantal=$row['aantal'];
	foreach (array('living', 'kamer', 'tobi', 'alex') as $a) {
		$b=$a.'_avg';
		for ($x=30;$x>=20;$x--) {
			$query="SELECT COUNT(stamp) AS aantal FROM temp_hour WHERE $b > $x";
			$result=$db->query($query);
			while ($row=$result->fetch_assoc()) $data[$x][$a]=$row['aantal'];
		}
	}
	print_r($data);
	echo '
	<table>
		<thead>
		</thead>
		<tbody>';
	foreach ($data as $a=>$b) {
		echo '
			<tr>
				<td>'.$a.'</td>
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