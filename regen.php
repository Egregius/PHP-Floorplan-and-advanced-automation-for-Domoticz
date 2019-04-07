<?php
/**
 * Pass2PHP
 * php version 7.2.15
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
            <title>Regenvoorspelling</title>
            <link href="/styles/temp.php" rel="stylesheet" type="text/css"/>
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
			</form>';
    }
    //  if($udevice!='iPad') echo '<br>';
    echo '
            <form method="GET">
                <input type="date" class="btn datum" name="f_startdate" value="'.$f_startdate.'" onchange="this.form.submit()"/>
                <input type="date" class="btn datum" name="f_enddate" value="'.$f_enddate.'" onchange="this.form.submit()"/>
                <input type="hidden" name="r_startdate" value="'.$r_startdate.'"/>
                <input type="hidden" name="r_enddate" value="'.$r_enddate.'"/>
            </form>';
    $db=new mysqli('localhost', 'domotica', 'domotica', 'domotica');
    if ($db->connect_errno>0) {
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    $eendag=TIME-86400*2;$eendagstr=strftime("%Y-%m-%d %H:%M:%S", $eendag);
    $eenweek=TIME-86400*7;$eenweekstr=strftime("%Y-%m-%d %H:%M:%S", $eenweek);
    $eenmaand=TIME-86400*31;$eenmaandstr=strftime("%Y-%m-%d %H:%M:%S", $eenmaand);
    $buienradar='#FF1111';
    $darksky='#FFFF44';
    $buien='#44FF44';
    $legend='
            <div style="width:379px;padding:15px 0px 10px 4px;">
                &nbsp;<a href=\'javascript:navigator_Go("regen.php");\'><font color="'.$buienradar.'">Buienradar</font></a>
                &nbsp;<a href=\'javascript:navigator_Go("regen.php");\'><font color="'.$darksky.'">DarkSky</font></a><br>
                &nbsp;<a href=\'javascript:navigator_Go("regen.php");\'><font color="'.$buien.'">Buien</font></a>
		    </div>
';
    echo $legend;
    $colors=array($buienradar,$darksky,$buien);
    $query="SELECT stamp,buienradar,darksky,buien from `regen` where stamp >= '$f_startdate 00:00:00' AND stamp <= '$f_enddate 23:59:59'";
    if ($udevice=='iPad') {
        $args=array(
                            'width'=>1000,
                            'height'=>880,
                            'hide_legend'=>true,
                            'responsive'=>false,
                            'background_color'=>'#111',
                            'chart_div'=>'graph',
                            'colors'=>$colors,
                            'margins'=>array(0,0,0,50),
                            'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),
                            'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),
                             'raw_options'=>'vAxis: {
                                 viewWindowMode:\'explicit\',
                                 textStyle: {color: "#FFFFFF", fontSize: 18}
							},
							series:{
								0:{lineDashStyle:[2,2]},
								1:{lineDashStyle:[2,2]},
								2:{lineDashStyle:[2,2]},
								3:{lineDashStyle:[0,0],pointSize:5},
							}
							');
    } elseif ($udevice=='iPhone') {
        $args=array(
                            'width'=>320,
                            'height'=>420,
                            'hide_legend'=>true,
                            'responsive'=>false,
                            'background_color'=>'#111',
                            'chart_div'=>'graph',
                            'colors'=>$colors,
                            'margins'=>array(0,0,0,50),
                            'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),
                            'text_style'=>array('fontSize'=>12,'color'=>'999999'),
                            'raw_options'=>'vAxis: {
							  viewWindowMode:\'explicit\',
							  textStyle: {color: "#FFFFFF", fontSize: 18}
							},
							series:{
								0:{lineDashStyle:[2,2]},
								1:{lineDashStyle:[2,2]},
								2:{lineDashStyle:[2,2]},
								3:{lineDashStyle:[0,0],pointSize:5},
							}
							');
    } elseif ($udevice=='Mac') {
        $args=array('width'=>500,'height'=>660,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'999999'),
        'raw_options'=>'vAxis: {
							  viewWindowMode:\'explicit\',
							  textStyle: {color: "#FFFFFF", fontSize: 18}
							},
							series:{
								0:{lineDashStyle:[2,2]},
								1:{lineDashStyle:[2,2]},
								2:{lineDashStyle:[2,2]},
								3:{lineDashStyle:[0,0],pointSize:5},
							}
							');
    } elseif ($udevice=='S4') {
        $args=array('width'=>480,'height'=>500,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),
        'raw_options'=>'vAxis: {
							  viewWindowMode:\'explicit\',
							  textStyle: {color: "#FFFFFF", fontSize: 18}
							},
							series:{
								0:{lineDashStyle:[2,2]},
								1:{lineDashStyle:[2,2]},
								2:{lineDashStyle:[2,2]},
								3:{lineDashStyle:[0,0],pointSize:5},
							}
							');
    } elseif ($udevice=='Stablet') {
        $args=array('width'=>480,'height'=>500,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),
        'raw_options'=>'vAxis: {
							  viewWindowMode:\'explicit\',
							  textStyle: {color: "#FFFFFF", fontSize: 18}
							},
							series:{
								0:{lineDashStyle:[2,2]},
								1:{lineDashStyle:[2,2]},
								2:{lineDashStyle:[2,2]},
								3:{lineDashStyle:[0,0],pointSize:5},
							}
							');
    } else {
        $args=array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'));
    }
    if (!$result=$db->query($query)) {
        die('There was an error running the query ['.$query.' - '.$db->error.']');
    }
    if ($result->num_rows==0) {
        echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';goto end;
    }
    while ($row=$result->fetch_assoc()) {
        $graph[]=$row;
    }
    $result->free();
    $chart=array_to_chart($graph, $args);
    echo $chart['script'];
    echo $chart['div'];
    unset($chart);
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