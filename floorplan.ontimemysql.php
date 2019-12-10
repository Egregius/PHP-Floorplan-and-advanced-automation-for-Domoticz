<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home) {
    error_reporting(E_ALL);ini_set("display_errors", "on");
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
		<style type="text/css">
		    .btn{width:100%;height:60px;}
		</style>
	</head>
	<body>
		<div class="fix" style="top:0px;left:0px;">
			<a href=\'javascript:navigator_Go("floorplan.php");\'>
				<img src="/images/close.png" width="72px" height="72px"/>
			</a>
		</div>
		<br>
		<br>
		<br>';
	$db=new PDO("mysql:host=localhost;dbname=$dbname;", $dbuser, $dbpass);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt=$db->query("SELECT DISTINCT device FROM ontime ORDER BY device ASC");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$devices[]=$row['device'];
	}
	if (!isset($_REQUEST['device']))$device='heater1';
	else $device=$_REQUEST['device'];
    foreach ($devices as $d) {
        if ($device==$d) {
            echo '
            <div class="fix" style="top:35px;left:75px;width:245px;">
                <button class="btn btnd" onclick="toggle_visibility(\'devices\');" >'.$d.'</button>
            </div>';
        }
    }
    echo '
        <form method="POST">
            <div id="devices" class="fix devices" style="top:0px;left:0px;display:none;background-color:#000;z-index:100;">';
    foreach ($devices as $d) {
        if ($device==$d) {
            echo '
				<button name="device" value="'.$d.'" class="btn" onclick="toggle_visibility(\'devices\');" style="padding:7px;margin-bottom:0px;">'.$d.'</button>';
        } else {
            echo '
			    <button name="device" value="'.$d.'" class="btn" onclick="toggle_visibility(\'devices\');" style="padding:7px;margin-bottom:0px;">'.$d.'</button>';
        }
    }
    echo '
            </div>
		</form>';
   
    echo '
		<div style="margin-top:40px">
		    <table>';
    $stmt=$db->query("SELECT stamp,status FROM ontime WHERE device='$device' ORDER BY stamp DESC");
	while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
		$datas[]=$row;
	}
	
	//print_r($datas);

    $status='';$tijdprev=TIME;$totalon=0;
    if (!empty($datas)) {
        foreach ($datas as $data) {
            $status=$data['status'];
            $tijd=$data['stamp'];
            $period=($tijdprev-$tijd);
            if ($status=='Off') {
                $style="color:#1199FF";
            } else {
                $totalon=$totalon+$period;
                $style="color:#FF4400";
            }
            $tijdprev=$tijd;
            echo '
			<tr>
				<td style="'.$style.'">'.strftime("%F %T", $data['stamp']).'</td>
				<td style="'.$style.'">&nbsp;'.$status.'&nbsp;</td>
				<td style="'.$style.'">&nbsp;'.convertToHours($period).'</td>
			</tr>';
        }
    }
    echo '
		</table>
		</div><br>
		<div class="fix" style="top:0px;left:204px;width:60px;font-size:2em"><a href="?device='.$device.'">'.convertToHours($totalon).'</a></div>
		<script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
		    function toggle_visibility(id){var e=document.getElementById(id);if(e.style.display==\'inherit\') e.style.display=\'none\';else e.style.display=\'inherit\';}
		</script>';
}
?>

    </body>
</html>