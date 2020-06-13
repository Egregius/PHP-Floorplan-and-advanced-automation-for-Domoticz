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
    $sensor=998;
    if (isset($_REQUEST['sensor'])) {
        $sensor=$_REQUEST['sensor'];
    }
    $f_startdate=date("Y-m-d", TIME);
    $f_enddate=date("Y-m-d", TIME);
    $week=date("Y-m-d", TIME-86400*6);
    echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
		<form action="floorplan.php"><input type="submit" class="btn b5" value="Plan"/></form>
			<form action="/temp.php"><input type="submit" class="btn btna b5" value="Temperaturen"/></form>
			<form action="/regen.php"><input type="submit" class="btn b5" value="Regen"/></form>';
    } else {
        echo '<body style="width:100%">
			<form action="/floorplan.php"><input type="submit" class="btn b3" value="Plan"/></form>
			<form action="/temp.php"><input type="submit" class="btn btna b3" value="Temperaturen"/></form>
			<form action="/regen.php"><input type="submit" class="btn b3" value="Regen"/></form>';
    }
    $db=new mysqli('localhost', $dbuser, $dbpass, $dbname);
    if ($db->connect_errno>0) {
        die('Unable to connect to database ['.$db->connect_error.']');
    }
    switch($sensor){
    case 147:$sensornaam='living';
        break;
    case 246:$sensornaam='badkamer';
        break;
    case 278:$sensornaam='kamer';
        break;
    case 356:$sensornaam='tobi';
        break;
    case 293:$sensornaam='zolder';
        break;
    case 244:$sensornaam='alex';
        break;
    case 998:$sensornaam='binnen';
        break;
    case 999:$sensornaam='alles';
        break;
    default:$sensornaam='buiten';
        break;
    }
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
    $args=array(
        	'width'=>1000,
        	'height'=>880,
        	'hide_legend'=>true,
        	'responsive'=>false,
        	'background_color'=>'#111',
        	'chart_div'=>'graph',
        	'margins'=>array(0,0,0,0),
        	'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),
        	'text_style'=>array('fontSize'=>12,'color'=>'FFFFFF'),
        	'raw_options'=>'
        		lineWidth:3,
        		crosshair:{trigger:"both"},
				hAxis: {
					slantedText: true,
					slantedTextAngle: 90,
					showTextEvery: 60,
					textStyle: {color: "#DDD", fontSize: 0}
				},
				vAxis: {
					format:"# °C",
					textStyle: {color: "#AAA", fontSize: 14},
					Gridlines: {
						multiple: 1
					},
					minorGridlines: {
						multiple: 0.5
					}
				  },
				theme:"maximized",
				chartArea:{left:0,top:0,width:"100%",height:"100%"}'
        );
    $argshour=array(
        	'width'=>1000,
        	'height'=>880,
        	'hide_legend'=>true,
        	'responsive'=>false,
        	'background_color'=>'#111',
        	'chart_div'=>'graphhour',
        	'margins'=>array(0,0,0,0),
        	'y_axis_text_style'=>array('fontSize'=>18,'color'=>'999999'),
        	'raw_options'=>'
        		lineWidth:3,
        		crosshair:{trigger:"both"},
				hAxis: {
					slantedText: true,
					slantedTextAngle: 90,
					showTextEvery: 30,
					textStyle: {color: "#DDD", fontSize: 0}
				},
				vAxis: {
					format:"# °C",
					textStyle: {color: "#AAA", fontSize: 14},
					Gridlines: {
						multiple: 1
					},
					minorGridlines: {
						multiple: 0.5
					}
				  },
				theme:"maximized",
				chartArea:{left:0,top:0,width:"100%",height:"100%"}'
        );
    if ($udevice=='iPad') {
		$args['width']=1000;$args['height']=880;
		$argshour['width']=1000;$argshour['height']=880;
	} elseif ($udevice=='iPhone') {
		$args['width']=300;$args['height']=440;
		$argshour['width']=300;$argshour['height']=440;
	} elseif ($udevice=='Mac') {
		$args['width']=490;$args['height']=700;
		$argshour['width']=490;$argshour['height']=700;
	} else {
		$args['width']=480;$args['height']=200;
		$argshour['width']=480;$argshour['height']=200;
	}
    if ($sensor=='alles') {
        $args['colors']=array($buiten,$living,$badkamer,$kamer,$tobi,$alex,$zolder,$living,$badkamer,$kamer,$tobi,$alex);
        $argshour['colors']=array($buiten,$living,$badkamer,$kamer,$tobi,$alex,$zolder,$living,$badkamer,$kamer,$tobi,$alex);
        $args['line_styles']=array('lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [0, 0]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]','lineDashStyle: [1, 1]');
        $query="SELECT DATE_FORMAT(stamp, '%h:%i') as stamp,buiten,living,badkamer,kamer,tobi,alex,zolder from `temp` where stamp >= '$f_startdate 00:00:00' AND stamp <= '$f_enddate 23:59:59'";
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
        $query="SELECT DATE_FORMAT(stamp, '%W') as stamp,buiten_avg as buiten,living_avg as living,badkamer_avg as badkamer,kamer_avg as kamer,tobi_avg as tobi,alex_avg as alex,zolder_avg as zolder from `temp_hour` where stamp > '$week'";
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for last week.<hr>';
            goto enda;
        } else {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek laatste 5 dagen.';
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
        $args['colors']=array($living,$badkamer,$kamer,$tobi,$alex,$living,$badkamer,$kamer,$tobi,$alex);
        $argshour['colors']=array($living,$badkamer,$kamer,$tobi,$alex,$living,$badkamer,$kamer,$tobi,$alex);
        $args['line_styles']=array('lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[1,8]','lineDashStyle:[1,8]');
        $argshour['line_styles']=array('lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[3,5]','lineDashStyle:[1,8]','lineDashStyle:[1,8]');
        $query="SELECT DATE_FORMAT(stamp, '%h:%i') as stamp, living,badkamer,kamer,tobi,alex from `temp` where stamp >= '$f_startdate 00:00:00' AND stamp <= '$f_enddate 23:59:59'";
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
        $query="SELECT DATE_FORMAT(stamp, '%W') as stamp, living_avg as living,badkamer_avg as badkamer,kamer_avg as kamer,tobi_avg as tobi,alex_avg as alex from `temp_hour` where stamp > '$week'";
        if (!$result=$db->query($query)) {
            die('There was an error running the query ['.$query.' - '.$db->error.']');
        }
        if ($result->num_rows==0) {
            echo 'No data for last week<hr>';
            goto endb;
        } else {
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Grafiek voor laatste 5 dagen';
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
        $args['line_styles']=array('lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[1,8]');
        $argshour['line_styles']=array('lineDashStyle:[0,0]','lineDashStyle:[3,5]','lineDashStyle:[1,8]');
        if ($sensor=='badkamer') {
            $args['colors']=array(${$sensornaam},${$sensornaam},'#ffb400');
            $argshour['colors']=array('#00F','#F00','#0F0');
        } else {
            $args['colors']=array(${$sensornaam},${$sensornaam},'#FFFF00');
            $argshour['colors']=array('#00F','#F00','#0F0');
        }
        $query="SELECT DATE_FORMAT(stamp, '%h:%i') as stamp,$sensor from `temp` where stamp >= '$f_startdate 00:00:00' AND stamp <= '$f_enddate 23:59:59'";
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
        $query="SELECT DATE_FORMAT(stamp, '%W') as stamp, $min, $max, $avg from `temp_hour` where stamp > '$week'";
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
    $togo=61-date("s");
    if ($togo<15) {
        $togo=15;
    }
    $togo=$togo*1000+2000;
    echo "<br>refreshing in ".$togo/1000 ." seconds";
    echo '<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\','.$togo.');</script>';
    $db->close();
} else {
    header("Location: index.php");
    die("Redirecting to: index.php");
}