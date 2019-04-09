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
require 'scripts/chart.php';
if ($home===true) {
    if ($user=='Guy') {
        error_reporting(E_ALL);
        ini_set("display_errors", "on");
    }
    $sensor=998;
    if (isset($_REQUEST['sensor'])) {
        $sensor=$_REQUEST['sensor'];
    }
    session_start();
    if (isset($_REQUEST['f_startdate'])) {
        $_SESSION['f_startdate']=$_REQUEST['f_startdate'];
    }
    if (isset($_REQUEST['f_enddate'])) {
        $_SESSION['f_enddate']=$_REQUEST['f_enddate'];
    }
    /*if(!isset($_SESSION['f_startdate']))*/$_SESSION['f_startdate']=date("Y-m-d", TIME);
    /*if(!isset($_SESSION['f_enddate']))*/$_SESSION['f_enddate']=date("Y-m-d", TIME);
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
    $db=new mysqli('localhost', 'domotica', 'domotica', 'domotica');
    if ($db->connect_errno>0) {
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    switch ($sensor) {
    case 147:$setpoint=12;$radiator=179;$sensornaam='living';
        break;
    case 246:$setpoint=13;$radiator=13;$sensornaam='badkamer';
        break;
    case 278:$setpoint=14;$radiator=181;$sensornaam='kamer';
        break;
    case 356:$setpoint=15;$radiator=183;$sensornaam='tobi';
        break;
    case 293:$setpoint=0;$radiator=0;$sensornaam='zolder';
        break;
    case 244:$setpoint=16;$radiator=203;$sensornaam='alex';
        break;
    case 998:$setpoint=998;$radiator=998;$sensornaam='binnen';
        break;
    case 999:$setpoint=999;$radiator=999;$sensornaam='alles';
        break;
    default:$setpoint=0;$radiator=0;$sensornaam='buiten';
        break;
    }
    $eendag=TIME-86400;
    $eendagstr=strftime("%Y-%m-%d %H:%M:%S", $eendag);
    $eenweek=TIME-86400*7;
    $eenweekstr=strftime("%Y-%m-%d %H:%M:%S", $eenweek);
    $eenmaand=TIME-86400*31;
    $eenmaandstr=strftime("%Y-%m-%d %H:%M:%S", $eenmaand);
    $sensor=$sensornaam;
    $living='#FF1111';
    $badkamer='#6666FF';
    $kamer='#44FF44';
    $tobi='00EEFF';
    $alex='#EEEE00';
    $zolder='#EE33EE';
    $buiten='#FFFFFF';
    $legend='<div style="position:absolute;top:14px;left;0px;width:100%;z-index:100;"><center>
		&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=147");\'><font color="'.$living.'">Living</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=246");\'><font color="'.$badkamer.'">Badkamer</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=278");\'><font color="'.$kamer.'">Kamer</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=356");\'><font color="'.$tobi.'">Tobi</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=244");\'><font color="'.$alex.'">Alex</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=293");\'><font color="'.$zolder.'">Zolder</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=329");\'><font color="'.$buiten.'">Buiten</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=998");\'><font color="'.$buiten.'">Binnen</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("tempbig.php?sensor=999");\'><font color="'.$buiten.'">Alles</font></a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\'javascript:navigator_Go("tempbig.php");\'><font color="#FFFFFF">'.strftime("%k:%M:%S", TIME).'</font></a></center></div>';
    echo $legend;
    if ($sensor=='alles') {
        $colors=array($buiten,$living,$badkamer,$kamer,$tobi,$alex,$zolder,$living,$badkamer,$kamer,$tobi,$alex);
        $line_styles=array('lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]');
        $query="SELECT stamp,buiten,living,badkamer,kamer,tobi,alex,zolder from `temp` where stamp >= '$f_startdate 00:00:00' AND stamp <= '$f_enddate 23:59:59'";
        $args=array('width'=>1880,'height'=>1000,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'raw_options'=>'lineWidth:4,crosshair:{trigger:"both"}');
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';
        }
        while ($row=$result->fetch_assoc()) {
            $graph[]=$row;
        }
        $result->free();
        $chart=array_to_chart($graph, $args);
        echo $chart['script'];
        echo $chart['div'];
        unset($chart);
    } elseif ($sensor=='binnen') {
        $colors=array($living,$badkamer,$kamer,$tobi,$alex,$living,$badkamer,$kamer,$tobi,$alex);
        $line_styles=array('lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[1,8]','lineDashStyle:[1,8]');
        $query="SELECT stamp,living,badkamer,kamer,tobi,alex from `temp` where stamp >= '$f_startdate 00:00:00' AND stamp <= '$f_enddate 23:59:59'";
        $args=array('width'=>1880,'height'=>1000,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'raw_options'=>'lineWidth:4,crosshair:{trigger:"both"}');
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';
        }
        while ($row=$result->fetch_assoc()) {
            $graph[]=$row;
        }
        $result->free();
        $chart=array_to_chart($graph, $args);
        echo $chart['script'];
        echo $chart['div'];
        unset($chart);
    } else {
        $min=$sensor.'_min';
        $max=$sensor.'_max';
        $avg=$sensor.'_avg';
        $line_styles=array('lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[1,8]');
        if ($sensor=='badkamer') {
            $colors=array(${$sensornaam},${$sensornaam},'#ffb400');
        } else {
            $colors=array(${$sensornaam},${$sensornaam},'#FFFF00');
        }
        $query="SELECT stamp,$sensor from `temp` where stamp >= '$f_startdate 00:00:00' AND stamp <= '$f_enddate 23:59:59'";
        $args=array('width'=>1880,'height'=>1000,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#000','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:4,crosshair:{trigger:"both"}');
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query .' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';
        }
        while ($row=$result->fetch_assoc()) {
            $graph[]=$row;
        }
        $result->free();
        $chart=array_to_chart($graph, $args);
        echo $chart['script'];
        echo $chart['div'];
        unset($chart);
    }
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
?>