<?php
$elec=0.3634;
$start=microtime(true);
require 'secure/functions.php';
require '/var/www/authentication.php';
$db=dbconnect();
if (isset($_GET['setauto'])) {
	storemode('daikin', $_GET['setauto'], basename(__FILE__).':'.__LINE__);
} elseif (isset($_GET['setpower'])) {
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	storemode('daikin_kwh', $_GET['setpower'],'',1);
	storeicon('daikin_kwh', $_GET['setpower'],'',1);
	if ($_GET['setpower']!='Auto') {
		file_get_contents('http://192.168.2.111/aircon/set_demand_control?type=1&en_demand=1&mode=0&max_pow='.$_GET['setpower'].'&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0').'&nbsp;';
		file_get_contents('http://192.168.2.112/aircon/set_demand_control?type=1&en_demand=1&mode=0&max_pow='.$_GET['setpower'].'&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0').'&nbsp;';
		file_get_contents('http://192.168.2.113/aircon/set_demand_control?type=1&en_demand=1&mode=0&max_pow='.$_GET['setpower'].'&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0').'&nbsp;';
	}
}
$d=fetchdata(0, basename(__FILE__).':'.__LINE__);
echo '
<html>
	<head>
		<title>Floorplan</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="theme-color" content="#000">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale='.$scale.',user-scalable=yes,minimal-ui">
		<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<script type="text/javascript" src="/scripts/floorplanjs.js"></script>
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.css">
		<style>
			html{width:320px!important;}
			body{width:320px!important;}
			td{font-size:0.8em;text-align:center;}
			th{text-align:center;}
			.fix{width:320px;padding:0}
			.btn{width:90%;padding:20px 8px 8px 8px;margin:8px;}
			.btna{color:#000!important}
			tr.border_bottom td {border-bottom:1pt dotted #777;color:#FFF;font-size:0.9em}
			.border_right {border-right:1pt dotted #777;}
		</style>
	</head>
	<body>
		<div class="fix" style="bottom:10px;left:10px;width:100%">
			<a href=\'javascript:navigator_Go("floorplan.php");\'>
				<img src="/images/close.png" width="50px" height="50px"/>
			</a>
		</div>
		<div class="fix center" style="top:0px;left:0px;width:100%">
			<br>
			<br>
			<div style="font-size:2em">Current: '.$d['daikin_kwh']['icon'].'</div>';
echo '
			<br>
			<br>';
foreach (array(40,50,60,70,80,90,100, 'Auto') as $i) {
	if ($d['daikin_kwh']['m']==$i) echo '
			<a href="/floorplan.daikinpowerusage.php?setpower='.$i.'" class="btn btna">'.$i.'</a>';
	else echo '
			<a href="/floorplan.daikinpowerusage.php?setpower='.$i.'" class="btn">'.$i.'</a>';
}
?>
		</div>
		<br>
		<br>
		<br>



	</body>
</html>
