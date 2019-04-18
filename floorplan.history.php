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
    $perpage=37;
    if (isset($_REQUEST['device'])) {
        $device=$_REQUEST['device'];
    }
    $modes=array(
        'auto_mode'=>'DST',
        'buiten_temp_mode'=>'buiten',
        'civil_twilight_mode'=>'civil_twilight_mode',
        'denon_mode'=>'denon input',
        'elec_mode'=>'elec vandaag',
        'gcal_mode'=>'gcal_mode',
        'heating_mode'=>'bigdif',
        'icon_mode'=>'humidity',
        'max_mode'=>'max regen',
        'Weg_mode'=>'Beweging',
        'wind_mode'=>'wind hist',
        'zonvandaag_mode'=>'zonvandaag percent',
    );
    echo '<html>
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
        </style>
	</head>
	<body>
		<div class="fix" style="top:0px;left:0px;height:50px;width:50px;background-color:#CCC">
			<a href=\'javascript:navigator_Go("floorplan.history.php'.(isset($device)?'?device='.$device:'').'");\'>
				<img src="/images/restart.png" width="50px" height="50px"/>
			</a>
		</div>
		<div class="fix" style="top:0px;right:0px;">
			<a href=\'javascript:navigator_Go("floorplan.others.php");\'>
				<img src="/images/close.png" width="50px" height="50px"/>
			</a>
		</div>
		<br>
		<br>
		<br>
        <div class="fix" style="top:52px;left:0px;">';

    if (isset($device)) {
        echo '
        <button class="btn btnd" onclick="toggle_visibility(\'devices\');" >'.$device.'</button>';
    } else {
        echo '
        <button class="btn btnd" onclick="toggle_visibility(\'devices\');" >All</button>';
    }
    echo '
        </div>
        <div id="devices" class="fix devices" style="top:0px;left:0px;display:none;background-color:#000;z-index:100;">
        <form method="GET">';
    $sql="SELECT DISTINCT device FROM log ORDER BY device ASC;";
    if (!$result=$db->query($sql)) {
        die('There was an error running the query ['.$sql.' - '.$db->error.']');
    }
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '
				<button name="device" value="'.$row['device'].'" class="btn" onclick="toggle_visibility(\'devices\');" style="padding:7px;margin-bottom:0px;">'.$row['device'].'</button><br>';
    }
    echo '
        </div>
		</form>
	    </div>
	    <div class="fix" style="top:82px;left:0px">
		<table>';
    if (isset($_REQUEST['page'])) {
        $offset=$_REQUEST['page'];
    } else {
        $offset=0;
    }
    if (isset($device)) {
        $sql="SELECT *  FROM `log` WHERE `device` = '$device' ORDER BY timestamp DESC LIMIT $offset,$perpage;";
    } else {
        $sql="SELECT *  FROM `log` ORDER BY timestamp DESC LIMIT $offset,$perpage;";
    }
    if (!$result=$db->query($sql)) {
        die('There was an error running the query ['.$sql.' - '.$db->error.']');
    }
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        //print_r($row);
        $name=strtr($row['device'], $modes);
        $status=$row['status'];
        if (endsWith($name, '_temp')) {
            $status=number_format($status, 1, ',', '').' °C';
        } elseif ($name='humidity') {
            $status=$status.' %';
        } else {
            $status=substr($row['status'], 0, 15);
        }
        echo '
        <tr>
            <td nowrap>'.substr($row['timestamp'], 8, 2).'-'.substr($row['timestamp'], 5, 2).'-'.substr($row['timestamp'], 0, 4).' '.substr($row['timestamp'], 10, 9).'</td>';
        if (!isset($device)) {
            echo '
            <td nowrap>'.$name.'</td>';
        }
        echo '
            <td nowrap>&nbsp;'.$status.'&nbsp;</td>
            <td nowrap>&nbsp;'.$row['user'].'</td>
            <td nowrap>&nbsp;'.$row['info'].'</td>
        </tr>';
        @$count++;
    }
    echo '
    </table>';
    if (isset($count)&&($count>=$perpage||isset($_POST['page']))) {
        echo '
        <form method="POST">';
        if (isset($device)) {
            echo '
            <input type="hidden" name="device" value="'.$device.'"/>';
        }
        if ($offset==0&&$count==$perpage) {
            echo '
            <br>
            <button type="submit" name="page" value="'.($offset+$perpage).'" class="btn b3" >Next</button>';
        } elseif ($offset>0&&$count<$perpage) {
            echo '
            <br>
            <button type="submit" name="page" value="'.($offset-$perpage).'" class="btn b3" >Prev</button>';
        } else {
            echo '
            <br>
            <button type="submit" name="page" value="'.($offset-$perpage).'" class="btn b4" >Prev</button>
            <button type="submit" name="page" value="'.($offset+$perpage).'" class="btn b4" >Next</button>';
        }
        echo '
        </form>
        </div>';
    }
    echo '
    <script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
		</script>';
}
?>
<script>
    function toggle_visibility(id){var e=document.getElementById(id);if(e.style.display=='inherit') e.style.display='none';else e.style.display='inherit';}
</script>
    </body>
</html>