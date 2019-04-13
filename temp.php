<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
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
    if (!isset($_REQUEST['referrer'])) {
        $_REQUEST['referrer']='floorplan.php';
    }
    if ($user=='Guy') {
        error_reporting(E_ALL);
        ini_set("display_errors", "on");
    }
    $sensor=998;
    if (isset($_REQUEST['sensor'])) {
        $sensor=$_REQUEST['sensor'];
    }
    session_start();
    if (!isset($_SESSION['referer'])) {
        $_SESSION['referer']='floorplan.php';
    }
    if (isset($_REQUEST['f_startdate'])) {
        $_SESSION['f_startdate']=$_REQUEST['f_startdate'];
    }
    if (isset($_REQUEST['f_enddate'])) {
        $_SESSION['f_enddate']=$_REQUEST['f_enddate'];
    }
    if (!isset($_SESSION['f_startdate'])) {
        $_SESSION['f_startdate']=date("Y-m-d", TIME);
    }
    if (!isset($_SESSION['f_enddate'])) {
        $_SESSION['f_enddate']=date("Y-m-d", TIME);
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
    $r_startdate=date("Y-m-d", TIME);
    $r_enddate=date("Y-m-d", TIME);
    $week=date("Y-m-d", TIME-86400*6);
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
		<link href="/styles/temp.php" rel="stylesheet" type="text/css"/>
		<script type="text/javascript">function navigator_Go(url) {window.location.assign(url);}</script>
		</head>';
    if ($udevice=='iPad') {
        echo '<body style="width:800px">
		<form action="'.$_SESSION['referer'].'"><input type="submit" class="btn b5" value="Plan"/></form>
			<form action="/temp.php"><input type="submit" class="btn btna b5" value="Temperaturen"/></form>
			<form action="/regen.php"><input type="submit" class="btn b5" value="Regen"/></form>';
    } else {
        echo '<body style="width:100%">
			<form action="'.$_SESSION['referer'].'"><input type="submit" class="btn b3" value="Plan"/></form>
			<form action="/temp.php"><input type="submit" class="btn btna b3" value="Temperaturen"/></form>
			<form action="/regen.php"><input type="submit" class="btn b3" value="Regen"/></form>';
    }
    //  if ($udevice!='iPad') echo '<br>';
    echo '<form method="GET">
			<input type="hidden" name="sensor" value="'.$sensor.'"/>
			<input type="date" class="btn datum" name="f_startdate" value="'.$f_startdate.'" onchange="this.form.submit()"/>
			<input type="date" class="btn datum" name="f_enddate" value="'.$f_enddate.'" onchange="this.form.submit()"/>
			<input type="hidden" name="r_startdate" value="'.$r_startdate.'"/>
			<input type="hidden" name="r_enddate" value="'.$r_enddate.'"/>
			</form>';
    if ($udevice=="Mac") {
        echo '<a href="tempbig.php" target="popup" class="btn" onclick="window.open(this.href,\'Tempbig\',\'left=0,top=0,width=507,height=848,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no\').focus(); return false;">Big</a>
';
    }
    $db=new mysqli('localhost', 'domotica', 'domotica', 'domotica');
    if ($db->connect_errno>0) {
        die('Unable to connect to database ['.$db->connect_error.']');
    }
    switch($sensor){
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
    $legend='<div style="width:320px;padding:20px 0px 10px 0px;">
		&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=147");\'><font color="'.$living.'">Living</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=246");\'><font color="'.$badkamer.'">Badkamer</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=278");\'><font color="'.$kamer.'">Kamer</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=356");\'><font color="'.$tobi.'">Tobi</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=244");\'><font color="'.$alex.'">Alex</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=293");\'><font color="'.$zolder.'">Zolder</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=329");\'><font color="'.$buiten.'">Buiten</font></a><br/><br/>
		&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=998");\'><font color="'.$buiten.'">Binnen</font></a>
		&nbsp;<a href=\'javascript:navigator_Go("temp.php?sensor=999");\'><font color="'.$buiten.'">Alles</font></a></div>';
    echo $legend;
    if ($sensor=='alles') {
        $colors=array($buiten,$living,$badkamer,$kamer,$tobi,$alex,$zolder,$living,$badkamer,$kamer,$tobi,$alex);
        $line_styles=array('lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]');
        $query="SELECT stamp,buiten,living,badkamer,kamer,tobi,alex,zolder from `temp` where stamp >= '$f_startdate 00:00:00' AND stamp <= '$f_enddate 23:59:59'";
        if ($udevice=='iPad') {
            $args=array('width'=>1000,'height'=>880,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='iPhone') {
            $args=array('width'=>320,'height'=>440,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'999999'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='Mac') {
            $args=array('width'=>490,'height'=>700,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'999999'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } else {
            $args=array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        }
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';goto montha;
        }
        while ($row=$result->fetch_assoc()) {
            $graph[]=$row;
        }
        $result->free();
        $chart=array_to_chart($graph, $args);
        echo $chart['script'];
        echo $chart['div'];
        unset($chart);
        echo '<br/>'.$legend;
        montha:
        $query="SELECT stamp,buiten_avg as buiten,living_avg as living,badkamer_avg as badkamer,kamer_avg as kamer,tobi_avg as tobi,alex_avg as alex,zolder_avg as zolder from `temp_hour` where stamp > '$week'";
        if ($udevice=='iPad') {
            $argshour=array('width'=>1000,'height'=>880,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>$colors,'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
        } elseif ($udevice=='iPhone') {
            $argshour=array('width'=>320,'height'=>440,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>$colors,'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom','raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='Mac') {
            $argshour=array('width'=>490,'height'=>700,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>$colors,'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom','raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } else {
            $argshour=array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>$colors,'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom','raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        }
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for last week.<hr>';
            goto enda;
        } else {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Graph for last week';
        }
        while ($row=$result->fetch_assoc()) {
            $graphhour[]=$row;
        }
        $result->free();
        $charthour=array_to_chart($graphhour, $argshour);
        echo $charthour['script'];
        echo $charthour['div'];
        unset($charthour);
        enda:
    } elseif ($sensor=='binnen') {
        $colors=array($living,$badkamer,$kamer,$tobi,$alex,$living,$badkamer,$kamer,$tobi,$alex);
        $line_styles=array('lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[1,8]','lineDashStyle:[1,8]');
        $query="SELECT stamp,living,badkamer,kamer,tobi,alex from `temp` where stamp >= '$f_startdate 00:00:00' AND stamp <= '$f_enddate 23:59:59'";
        if ($udevice=='iPad') {
            $args=array('width'=>1000,'height'=>880,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='iPhone') {
            $args=array('width'=>320,'height'=>440,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'999999'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='Mac') {
            $args=array('width'=>490,'height'=>700,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'999999'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } else {
            $args=array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        }
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';goto monthb;
        }
        while ($row=$result->fetch_assoc()) {
            $graph[]=$row;
        }
        $result->free();
        $chart=array_to_chart($graph, $args);
        echo $chart['script'];
        echo $chart['div'];
        unset($chart);
        echo '<br/>'.$legend;
        monthb:
        $query="SELECT stamp,living_avg as living,badkamer_avg as badkamer,kamer_avg as kamer,tobi_avg as tobi,alex_avg as alex from `temp_hour` where stamp > '$week'";
        if ($udevice=='iPad') {
            $argshour=array('width'=>1000,'height'=>880,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>$colors,'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'));
        } elseif ($udevice=='iPhone') {
            $argshour=array('width'=>320,'height'=>440,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>$colors,'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom','raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='Mac') {
            $argshour=array('width'=>490,'height'=>700,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>$colors,'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom','raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } else {
            $argshour=array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>$colors,'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),'legend_position'=>'bottom','raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        }
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for last week<hr>';
            goto endb;
        } else {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Graph for last week';
        }
        while ($row=$result->fetch_assoc()) {
            $graphhour[]=$row;
        }
        $result->free();
        $charthour=array_to_chart($graphhour, $argshour);
        echo $charthour['script'];
        echo $charthour['div'];
        unset($charthour);
        endb:
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
        if ($udevice=='iPad') {
            $args=array('width'=>1000,'height'=>880,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='iPhone') {
            $args=array('width'=>320,'height'=>440,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='Mac') {
            $args=array('width'=>490,'height'=>700,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } else {
            $args=array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graph','colors'=>$colors,'margins'=>array(0,0,0,50),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'line_styles'=>$line_styles,'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        }
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query .' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for dates '.$f_startdate.' to '.$f_enddate.'<hr>';goto month;
        }
        while ($row=$result->fetch_assoc()) {
            $graph[]=$row;
        }
        $result->free();
        $chart=array_to_chart($graph, $args);
        echo $chart['script'];
        echo $chart['div'];
        unset($chart);
        echo $legend;
        month:
        $query="SELECT stamp, $min, $max, $avg from `temp_hour` where stamp > '$week'";
        if ($udevice=='iPad') {
            $argshour=array('width'=>1000,'height'=>880,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='iPhone') {
            $argshour=array('width'=>320,'height'=>440,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } elseif ($udevice=='Mac') {
            $argshour=array('width'=>490,'height'=>700,'hide_legend'=>true,'responsive'=>true,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        } else {
            $argshour=array('width'=>480,'height'=>200,'hide_legend'=>true,'responsive'=>false,'background_color'=>'#111','chart_div'=>'graphhour','colors'=>array('6666FF','FF5555','55FF55'),'margins'=>array(0,0,0,49),'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),'raw_options'=>'lineWidth:3,crosshair:{trigger:"both"}');
        }
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for last week<hr>';goto end;
        } else {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Graph for last week';
        }
        while ($row=$result->fetch_assoc()) {
            $graphhour[]=$row;
        }
        $result->free();
        $charthour=array_to_chart($graphhour, $argshour);
        echo $charthour['script'];
        echo $charthour['div'];
        unset($charthour);
        end:
    }
    if ($f_startdate==$r_startdate&&$f_enddate==$r_enddate) {
        $togo=61-date("s");
        if ($togo<15) {
            $togo=15;
        }
        $togo=$togo*1000+2000;
        echo "<br>refreshing in ".$togo/1000 ." seconds";
        echo '<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
    }
    $db->close();
} else {
    header("Location: index.php");
    die("Redirecting to: index.php");
}