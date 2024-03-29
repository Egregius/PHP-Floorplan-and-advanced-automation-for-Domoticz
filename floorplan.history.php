<?php
$start=microtime(true);
require 'secure/functions.php';
require '/var/www/authentication.php';
$d=fetchdata();
$perpage=37;
if (isset($_REQUEST['device'])) {
	$device=$_REQUEST['device'];
}
$modes=array(
	'auto_mode'=>'DST',
	'buiten_temp_mode'=>'buiten',
	'civil_twilight_mode'=>'civil_twilight_mode',
	'elec_mode'=>'elec vandaag',
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
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="theme-color" content="#000">';
if ($udevice=='iPhone') {
	echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1,user-scalable=yes,minimal-ui"/>';
} elseif ($udevice=='iPad') {
	echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.2,user-scalable=yes,minimal-ui"/>';
}
echo '
		<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.css">
		<style>
			html{width:320px!important;}
			body{width:320px!important;}
			td{font-size:0.8em;text-align:left;}
			.fix{width:320px;padding:0}
			.btn{width:300px;}
			.btnd{width:236px;}
			.b4{max-width:155px!important;}
			.b3{max-width:320px!important;}
			.container {display: block;position: relative;padding-left: 35px;margin-bottom: 12px;cursor: pointer;font-size: 22px;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
			.container input {position: absolute;opacity: 0;cursor: pointer;height: 0;width: 0;}
			.checkmark {position: absolute;top: 0;left: 0;height: 35px;width: 35px;background-color: #eee;}
			.container:hover input ~ .checkmark {background-color: #ccc;}
			.container input:checked ~ .checkmark {background-color: #2196F3;}
			.checkmark:after {content: "";position: absolute;display: none;}
			.container input:checked ~ .checkmark:after {display: block;}
			.container .checkmark:after {left: 14px;top: 5px;width: 5px;height: 20px;border: solid white;border-width: 0 3px 3px 0;-webkit-transform: rotate(45deg);-ms-transform: rotate(45deg);transform: rotate(45deg);}
		</style>
		<script type="text/javascript" src="/scripts/jQuery.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js?v='.$floorplanjs.'"></script>
	</head>
	<body>
		<div class="fix" style="top:0px;left:0px;height:50px;width:50px;background-color:#CCC">
			<a href=\'javascript:navigator_Go("floorplan.history.php'.(isset($device)?'?device='.$device:'').'");\'>
				<img src="/images/restart.png" width="50px" height="50px"/>
			</a>
		</div>';
if (isset($_REQUEST['realstatus'])) {
	echo '
		<div class="fix btn" style="top:0px;left:55px;height:50px;width:150px;" onclick="location.href=\'floorplan.history.php'.(isset($_REQUEST['page'])?'?page='.$_REQUEST['page']:'').'\';">
			Real status
		</div>';
} else {
	echo '
		<div class="fix btn" style="top:0px;left:55px;height:50px;width:150px;" onclick="location.href=\'floorplan.history.php?realstatus'.(isset($_REQUEST['page'])?'&page='.$_REQUEST['page']:'').'\';">
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
		<form method="GET" id="filter" action="floorplan.history.php">';
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
if (isset($_REQUEST['start'])) {
	$offset=$_REQUEST['start'];
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
	if (isset($_REQUEST['realstatus'])) {
		if (endsWith($name, '_temp')) {
			$status=number_format($status, 1, ',', '').' °C';
		} elseif (endsWith($name, 'Z')) {
			$status=number_format($status, 1, ',', '').' °C';
		} elseif ($name=='bigdif') {
			$status=number_format($status, 1, ',', '').' °C';
		} elseif ($name=='elec vandaag') {
			$status=number_format($status, 1, ',', '').' kWh';
		} elseif ($name=='humidity') {
			$status=$status.' %';
		} else {
			$status=substr($row['status'], 0, 15);
		}
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
if (isset($count)&&($count>=$perpage||isset($_POST['start']))) {
	echo '
	<form method="GET">';
	if (isset($device)) {
		echo '
		<input type="hidden" name="device" value="'.$device.'"/>';
	}
	if ($offset==0&&$count==$perpage) {
		echo '
		<br>
		<button type="submit" name="start" value="'.($offset+$perpage).'" class="btn b3" >Next</button>';
	} elseif ($offset>0&&$count<$perpage) {
		echo '
		<br>
		<button type="submit" name="start" value="'.($offset-$perpage).'" class="btn b3" >Prev</button>';
	} else {
		echo '
		<br>
		<button type="submit" name="start" value="'.($offset-$perpage).'" class="btn b4" >Prev</button>
		<button type="submit" name="start" value="'.($offset+$perpage).'" class="btn b4" >Next</button>';
	}
	echo '
	</form>
	</div>'.$udevice;
}
?>
	<script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
		</script>';
	</body>
</html>
