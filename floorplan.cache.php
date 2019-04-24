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
$start=microtime(true);
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home) {
    echo '
<html>
	<head>
		<title>Floorplan</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">';
	if ($udevice=='iPhone') {
	    echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.655,user-scalable=yes,minimal-ui"/>';
	} elseif ($udevice=='iPad') {
	    echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.2,user-scalable=yes,minimal-ui"/>';
	}
	echo '
	    <link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<script type="text/javascript" language="javascript" src="https://mynetpay.be/js/jQuery.js"></script>
		<script type="text/javascript" language="javascript" src="https://mynetpay.be/js/jQuery.dataTables.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://mynetpay.be/js/jQuery.dataTables.columnFilter.js"></script>
		<script type="text/javascript" charset="utf-8">
			var asInitVals = new Array();
			$(document).ready(function() {
				$(\'#table\').dataTable(
				{
					"bStateSave": true,
					"bPaginate": false,
                    "ordering": false
				});
			});
		</script>
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php">
		<style>
		    html{width:320px!important;}
		    body{width:320px!important;}
		    td{font-size:0.8em;text-align:left;}
		    .fix{width:320px;padding:0}
		    .btn{width:300px;}
		    .btnd{width:236px;}
		    .b4{max-width:155px!important;}
		    .b3{max-width:320px!important;}
		    tr.border_bottom td {border-bottom:1pt dotted #777;color:#FFF;font-size:0.9em}
        </style>
	</head>
	<body>';
	if (isset($_REQUEST['nicestatus'])) {
	    echo '
		<div class="fix" style="top:0px;left:0px;height:50px;width:50px;background-color:#CCC">
			<a href=\'javascript:navigator_Go("floorplan.cache.php?nicestatus");\'>
				<img src="/images/restart.png" width="50px" height="50px"/>
			</a>
		</div>
		<div class="fix btn" style="top:0px;left:55px;height:50px;width:150px;" onclick="location.href=\'floorplan.cache.php'.(isset($_REQUEST['page'])?'?page='.$_REQUEST['page']:'').'\';">
			Real status
		</div>';
	} else {
	    echo '
		<div class="fix" style="top:0px;left:0px;height:50px;width:50px;background-color:#CCC">
			<a href=\'javascript:navigator_Go("floorplan.cache.php");\'>
				<img src="/images/restart.png" width="50px" height="50px"/>
			</a>
		</div>
		<div class="fix btn" style="top:0px;left:55px;height:50px;width:150px;" onclick="location.href=\'floorplan.cache.php?nicestatus'.(isset($_REQUEST['page'])?'&page='.$_REQUEST['page']:'').'\';">
			Nice status
		</div>';
	}
	echo '
		<div class="fix" style="top:0px;right:0px;">
			<a href=\'javascript:navigator_Go("floorplan.others.php");\'>
				<img src="/images/close.png" width="50px" height="50px"/>
			</a>
		</div>
		<br>
		<br>
		<br>
	    <div class="fix" style="top:82px;left:0px">
		<table  id="table" cellpadding="2" cellspacing="0">
		    <thead>
		        <tr class="border_bottom">
		            <th>Name</th>
		            <th>S</th>
		            <th>M</th>
		            <th>Time</th>
		        </tr>
		    </thead>
		    <tbody>';
    $sql="SELECT *  FROM `devices` ORDER BY t DESC";
    if (!$result=$db->query($sql)) {
        die('There was an error running the query ['.$sql.' - '.$db->error.']');
    }
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        //print_r($row);
        echo '
        <tr class="border_bottom">';
        if (isset($_REQUEST['nicestatus'])) {
            if (endswith($row['n'], '_set')) {
                echo '
                <td>'.$row['n'].'</td>
                <td class="right">'.number_format($row['s'], 1, ',', '').' °C</td>';
                if ($row['m']==0) {
                    echo '
                <td>Auto</td>';
                } else {
                    echo '
                <td>Manueel</td>';
                }
            } elseif (endswith($row['n'], 'Z')) {
                echo '
                <td>'.$row['n'].'</td>
                <td class="right">'.number_format($row['s'], 1, ',', '').' °C</td>
                <td></td>';
            } elseif (endswith($row['n'], '_temp')) {
                if ($row['n']=='diepvries_temp') {
                    echo  '
                <td>'.$row['n'].'</td>
                <td class="right">'.number_format($row['s'], 1, ',', '').' °C</td>
                <td>Set '.number_format($row['m'], 1, ',', '').' °C</td>';
                } elseif ($row['n']=='buiten_temp') {
                    echo  '
                <td>'.$row['n'].'</td>
                <td class="right">'.number_format($row['s'], 1, ',', '').' °C</td>
                <td>'.number_format($row['m'], 0, ',', '').' % Buien</td>';
                } else {
                    echo '
                <td>'.$row['n'].'</td>
                <td class="right">'.number_format($row['s'], 1, ',', '').' °C</td>
                <td>'.$row['m'].'</td>';
                }

            } elseif (startswith($row['n'], 'R')) {
                echo '
                <td>'.$row['n'].'</td>';
                if ($row['s']==0) {
                    echo '
                <td>Open</td>';
                } elseif ($row['s']==100) {
                    echo '
                <td>Gesloten</td>';
                } else {
                    echo '
                <td>'.$row['s'].' % Toe</td>';
                }
                if ($row['m']==0) {
                    echo '
                <td>Auto</td>';
                } else {
                    echo '
                <td>Manueel</td>';
                }
            } elseif (startswith($row['n'], '8')) {
                echo '
                <td>'.$row['n'].'</td>
                <td></td>
                <td></td>';
            } elseif (startswith($row['n'], 'mini')) {
                echo '
                <td>'.$row['n'].'</td>
                <td></td>
                <td></td>';
            } elseif($row['n']=='luifel') {
                echo '
                <td>'.$row['n'].'</td>';
                if ($row['s']==0) {
                    echo '
                <td>Open</td>';
                } elseif ($row['s']==100) {
                    echo '
                <td>Gesloten</td>';
                } else {
                    echo '
                <td>'.$row['s'].' % Open</td>';
                }
                if ($row['m']==0) {
                    echo '
                <td>Auto</td>';
                } else {
                    echo '
                <td>Manueel</td>';
                }
            } elseif (in_array($row['n'], array('eettafel','zithoek','kamer','tobi','alex','lichtbadkamer'))) {
                echo '
                <td>'.$row['n'].'</td>';
                if ($row['s']==0) {
                    echo '
                <td>Off</td>';
                } else {
                    echo '
                <td>'.$row['s'].'</td>';
                }
                if ($row['m']==0) {
                    echo '
                <td></td>';
                } elseif ($row['m']==1) {
                    echo '
                <td>Wake-up</td>';
                } elseif ($row['m']==2) {
                    echo '
                <td>Sleep</td>';
                }
            } elseif ($row['n']=='zonvandaag') {
                echo '
                <td>'.$row['n'].'</td>
                <td>'.number_format($row['s'], 1, ',', '').' kWh</td>
                <td>'.number_format($row['m'], 1, ',', '').' % Ref</td>';
            } elseif ($row['n']=='max') {
                echo '
                <td>Max voorspeld</td>
                <td></td>
                <td>'.number_format($row['m']*100, 0).' % Regen</td>';
            } elseif ($row['n']=='uv') {
                echo '
                <td>UV</td>
                <td>Nu '.number_format($row['s'], 1, ',', '').'</td>
                <td>Max '.number_format($row['m'], 1, ',', '').'</td>';
            } elseif ($row['n']=='zon') {
                echo '
                <td>Zon</td>
                <td class="right">'.number_format($row['s'], 0, ',', '').' W</td>
                <td></td>';
            } elseif ($row['n']=='elec') {
                echo '
                <td>Elec</td>
                <td class="right">'.number_format($row['s'], 0).' W</td>
                <td class="right">'.number_format($row['m'], 1, ',', '').' kWh</td>';
            } elseif ($row['n']=='civil_twilight') {
                echo '
                <td>civil_twilight</td>
                <td>'.strftime("%k:%M:%S", $row['s']).'</td>
                <td>'.strftime("%k:%M:%S", $row['m']).'</td>';
            } elseif ($row['n']=='icon') {
                echo '
                <td>icon</td>
                <td><img src="https://openweathermap.org/img/w/'.$d['icon']['s'].'.png" alt="icon"></td>
                <td>'.$row['m'].'% Humidity</td>';
            } elseif ($row['n']=='gcal') {
                echo '
                <td>Gcal</td>';
                if ($row['s']==1) {
                    echo '
                <td>Tobi Beitem</td>';
                } else {
                    echo '
                <td>Tobi Rumbeke</td>';
                }
                echo '
                <td>'.$row['m'].'</td>';
            } elseif ($row['n']=='Weg') {
                echo '
                <td>Weg</td>';
                if ($row['s']==0) {
                    echo '
                <td>Thuis</td>';
                } elseif ($row['s']==1) {
                    echo '
                <td>Slapen</td>';
                } elseif ($row['s']==2) {
                    echo '
                <td>Weg</td>';
                }
                echo '
                <td nowrap>Laatste beweging:<br>
                '.strftime("%d-%m %k:%M:%S", $row['m']).'</td>';
            } elseif ($row['n']=='wind') {
                echo '
                <td>'.$row['n'].'</td>
                <td>'.number_format($row['s'], 1, ',', '').' km/u</td>';
                $hist=json_decode($row['m']);
                echo '
                <td>';
                foreach ($hist as $i) {
                    echo number_format($i, 1, ',', '').' km/u<br>';
                }
                echo '</td>';
            } elseif ($row['n']=='auto') {
                echo '
                <td>'.$row['n'].'</td>';
                if ($row['s']==0) {
                    echo '
                <td>Lichten manueel</td>';
                } elseif ($row['s']==1) {
                    echo '
                <td>Lichten automatisch</td>';
                }
                if ($row['m']==0) {
                    echo '
                <td>Wintertijd</td>';
                } elseif ($row['m']==1) {
                    echo '
                <td>Zomertijd</td>';
                }
            } elseif ($row['n']=='douche') {
                echo '
                <td>'.$row['n'].'</td>
                <td>'.$row['s']*10 .' L gas</td>
                <td>'.$row['m'].' L water</td>';
            } elseif ($row['n']=='minmaxtemp') {
                echo '
                <td>Temp < 6u</td>
                <td>min '.number_format($row['s'], 1, ',', '') .' °C</td>
                <td>max '.number_format($row['m'], 1, ',', '').' °C</td>';
            } else {
                echo '
                <td>'.$row['n'].'</td>
                <td>'.substr($row['s'], 0, 20).'</td>
                <td>'.substr($row['m'], 0, 20).'</td>';
            }
        } else {
            echo '
                <td>'.$row['n'].'</td>
                <td>'.substr($row['s'], 0, 20).'</td>
                <td>'.substr($row['m'], 0, 20).'</td>';
        }
        if ($row['t']<TIME - (86400*7*4)) {
            echo '
            <td nowrap>'.strftime("%d-%m-%Y", $row['t']).'</td>
        </tr>';
        } else {
            echo '
            <td nowrap>'.strftime("%d-%m %H:%M:%S", $row['t']).'</td>
        </tr>';
        }
    }
    echo '
        </tbody>
    </table>
    <br>
    <br>
    <script type="text/javascript">
        function navigator_Go(url) {window.location.assign(url);}
    </script>';
}
?>
    </body>
</html>