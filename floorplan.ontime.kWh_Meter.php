<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
require 'secure/functions.php';
require '/var/www/authentication.php';
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
	<link rel="stylesheet" type="text/css" href="/styles/floorplan.css">
	<style type="text/css">
		.btn{width:100%;height:60px;}
		td{text-align:left}
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

echo '
	<div style="margin-top:40px">
		<table>';
$stmt=$db->query("SELECT stamp,value FROM kWh_Meter ORDER BY stamp DESC");
while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
	$datas[]=$row;
}

//print_r($datas);

$status='';$tijdprev=TIME;$totalon=0;
if (!empty($datas)) {
	foreach ($datas as $data) {
		$status=$data['value'];
		$tijd=strtotime($data['stamp']);
		$period=($tijdprev-$tijd);
		if ($status==0) {
			$style="color:#1199FF";
		} else {
			$totalon=$totalon+$period;
			$style="color:#FF4400";
		}
		$tijdprev=$tijd;
		echo '
		<tr>
			<td style="'.$style.'">'.$data['stamp'].'</td>
			<td style="'.$style.'">&nbsp;'.$status.'&nbsp;</td>
			<td style="'.$style.'">&nbsp;'.convertToHours($period).'</td>
		</tr>';
	}
}
echo '
	</table>
	</div><br>
	<a href="floorplan.ontime.kWh_Meter.php">
	<div class="fix" style="top:0px;left:75px;width:500px;font-size:2em">
		<table>
			<tr>
				<td>On</td>
				<td>'.convertToHours($totalon).' = '.round(($totalon/(TIME-$tijdprev))*100,2).'%</td>
			</tr>
			<tr>
				<td>Totaal</td>
				<td>'.convertToHours(TIME-$tijdprev).'</td>
			</tr>
		</table>
	</div>
	</a>
	<script type="text/javascript">
		function navigator_Go(url) {window.location.assign(url);}
		function toggle_visibility(id){var e=document.getElementById(id);if(e.style.display==\'inherit\') e.style.display=\'none\';else e.style.display=\'inherit\';}
	</script>';
?>
	</body>
</html>
