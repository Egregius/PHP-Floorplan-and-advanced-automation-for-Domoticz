<?php
/**
 * Pass2PHP
 * php version 7.0.33
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/settings.php';
require "scripts/chart.php";
if ($home===true) {
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="HandheldFriendly" content="true" /><meta name="viewport" content="width=device-width,height=device-height, user-scalable=yes, minimal-ui" />
	<meta name="apple-mobile-web-app-capable" content="yes"><meta name="apple-mobile-web-app-status-bar-style" content="black">
	<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
	<title>Battery levels</title>
	<link href="/styles/general.php" rel="stylesheet" type="text/css"/>
	</head>
	<body>
	<div class="header">
		<a href="javascript:navigator_Go(\'floorplan.php\');" class="btn b2">Plan</a>
		<a href="javascript:navigator_Go(\'bat.php\');" class="btn btna b2">Battery levels</a>
	</div>
	<div class="clear"></div>';

    $db=new mysqli('localhost', 'domotica', 'domotica', 'domotica');
    if ($db->connect_errno>0) {
        die('Unable to connect to database ['.$db->connect_error.']');
    }
    if (isset($_REQUEST['device'])) {
        $sensor=$_REQUEST['device'];
        //echo '<br/><h1>'.$sensor.'</h1>';
        $query="SELECT date, value from `battery` where `id` like '$sensor' order by date desc limit 0,365";
        if ($udevice=='iPad') {
            $args=array('chart'=>'AreaChart','width'=>740,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>array('55FF55'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'raw_options'=>'lineWidth:5,crosshair:{trigger:"both"},pointSize:14');
        } elseif ($udevice=='Mac') {
            $args=array('chart'=>'AreaChart','width'=>490,'height'=>670,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>array('55FF55'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'raw_options'=>'lineWidth:5,crosshair:{trigger:"both"},pointSize:14');
        } else {
            $args=array('chart'=>'AreaChart','width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>array('55FF55'),'margins'=>array(0,0,0,45),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'raw_options'=>'lineWidth:5,crosshair:{trigger:"both"},pointSize:14');
        }
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        while ($row=$result->fetch_assoc()) {
            $graph[]=$row;
        }
        $result->free();
        sort($graph);
        $chart=array_to_chart($graph, $args);
        echo $chart['script'];
        echo $chart['div'];
        unset($chart);
    } else {
        $sql="SELECT d.id,name,date,value FROM battery t1 JOIN batterydevices d on t1.id = d.id WHERE t1.date = (SELECT t2.date FROM battery t2 WHERE t2.id = t1.id ORDER BY t2.date DESC LIMIT 1) order by name asc;";
        if (!$result=$db->query($sql)) {
            die('There was an error running the query ['.$sql.' - '.$db->error.']');
        }
        while ($row = $result->fetch_assoc()) {
            $items[]=$row;
        }
        echo '<table cellpadding="4px">';
        foreach ($items as $item) {
            echo '<tr><td><a href=\'javascript:navigator_Go("bat.php?device='.$item['id'].'");\'>'.$item['name'].'</a></td><td>'.$item['date'].'</td><td align="right">'.$item['value'].'</td></tr>';
        }
        echo '</table>';
    }
    echo '</center>';
} else {
    header("Location: index.php");
    die("Redirecting to: index.php");
}
