<?php
$start=microtime(true);
require 'secure/functions.php';
require '/var/www/authentication.php';
$db=dbconnect();
echo '
<html>
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
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.655,user-scalable=yes,minimal-ui"/>';
} elseif ($udevice=='iPad') {
	echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.2,user-scalable=yes,minimal-ui"/>';
}
if (isset($_GET['setauto'])) {
	storemode('daikin', $_GET['setauto'], basename(__FILE__).':'.__LINE__);
} elseif (isset($_GET['setpower'])) {
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	storemode('daikin_kWh', $_GET['setpower']);
	storeicon('daikin_kWh', $_GET['setpower']);
	if ($_GET['setpower']!='Auto') {
		echo '111='.file_get_contents('http://192.168.2.111/aircon/set_demand_control?type=1&en_demand=1&mode=0&max_pow='.$_GET['setpower'].'&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0').'&nbsp;';
		echo '112='.file_get_contents('http://192.168.2.112/aircon/set_demand_control?type=1&en_demand=1&mode=0&max_pow='.$_GET['setpower'].'&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0').'&nbsp;';
		echo '113='.file_get_contents('http://192.168.2.113/aircon/set_demand_control?type=1&en_demand=1&mode=0&max_pow='.$_GET['setpower'].'&scdl_per_day=0&moc=0&tuc=0&wec=0&thc=0&frc=0&sac=0&suc=0').'&nbsp;';
	}
}
$d=fetchdata();
echo '
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
			.btn{width:300px;}
			.btna{color:#000!important}
			.btnd{width:236px;}
			.b4{max-width:155px!important;}
			.b3{max-width:200px!important;}
			tr.border_bottom td {border-bottom:1pt dotted #777;color:#FFF;font-size:0.9em}
			.border_right {border-right:1pt dotted #777;}
		</style>
	</head>
	<body>
		<div class="fix" style="top:0px;left:0px;width:100%">
			<a href=\'javascript:navigator_Go("floorplan.php");\'>
				<img src="/images/close.png" width="50px" height="50px"/>
			</a>';
if ($d['daikin']['m']==0) echo '
			<a href="/floorplan.daikinpowerusage.php?setauto=0" class="btn b4 btna">Manueel</a>
			<a href="/floorplan.daikinpowerusage.php?setauto=1" class="btn b4">Auto</a> '.$d['daikin_kWh']['icon'];
else echo '
			<a href="/floorplan.daikinpowerusage.php?setauto=0" class="btn b4">Manueel</a>
			<a href="/floorplan.daikinpowerusage.php?setauto=1" class="btn b4 btna">Auto</a> '.$d['daikin_kWh']['icon'];
echo '
			<br>
			<br>';


foreach (array(40,50,60,70,80,90,100, 'Auto') as $i) {
	if ($d['daikin_kWh']['m']==$i) echo '
			<a href="/floorplan.daikinpowerusage.php?setpower='.$i.'" class="btn btna b9">'.$i.'</a>';
	else echo '
			<a href="/floorplan.daikinpowerusage.php?setpower='.$i.'" class="btn b9">'.$i.'</a>';
}

echo '
		</div>
		<br>
		<br>
		<br>
		<div class="fix" style="top:112px;left:0px">
		<table  id="table" cellpadding="2" cellspacing="0">
			<thead>
				<tr>
					<th rowspan="2" class="border_right">Date</th>
					<th colspan="3" class="border_right">Heat</th>
					<th colspan="3" class="border_right">Cool</th>
					<th rowspan="2" class="border_right">Sum</th>
				</tr>
				<tr class="border_bottom">
					<th>Living</th>
					<th>Kamer</th>
					<th class="border_right">Alex</th>
					<th>Living</th>
					<th>Kamer</th>
					<th class="border_right">Alex</th>
				</tr>
			</thead>
			<tbody>';
$sql="SELECT * FROM `daikin` ORDER BY `date` DESC LIMIT 0,30";
if (!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
$livingheat=0;
$kamerheat=0;
$alexheat=0;
$livingcool=0;
$kamercool=0;
$alexcool=0;
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	echo '
		<tr class="border_bottom">
			<td nowrap class="border_right">'.$row['date'].'</td>
			<td>'.($row['livingheat']>1?number_format($row['livingheat']*0.1, 1, ',', ''):'').'</td>
			<td>'.($row['kamerheat']>1?number_format($row['kamerheat']*0.1, 1, ',', ''):'').'</td>
			<td class="border_right">'.($row['alexheat']>1?number_format($row['alexheat']*0.1, 1, ',', ''):'').'</td>
			<td>'.($row['livingcool']>1?number_format($row['livingcool']*0.1, 1, ',', ''):'').'</td>
			<td>'.($row['kamercool']>1?number_format($row['kamercool']*0.1, 1, ',', ''):'').'</td>
			<td class="border_right">'.($row['alexcool']>1?number_format($row['alexcool']*0.1, 1, ',', ''):'').'</td>
			<td class="border_right">'.(($row['livingheat']+$row['kamerheat']+$row['alexheat']+$row['livingcool']+$row['kamercool']+$row['alexcool'])>0?number_format(($row['livingheat']+$row['kamerheat']+$row['alexheat']+$row['livingcool']+$row['kamercool']+$row['alexcool'])*0.1, 1, ',', ''):'').'</td>
		</tr>';
	$livingheat=$livingheat+$row['livingheat'];
	$kamerheat=$kamerheat+$row['kamerheat'];
	$alexheat=$alexheat+$row['alexheat'];
	$livingcool=$livingcool+$row['livingcool'];
	$kamercool=$kamercool+$row['kamercool'];
	$alexcool=$alexcool+$row['alexcool'];
}
echo '
		</tbody>
		<tfoot>
			<tr>
				<th class="border_right border_bottom">Sum</th>
				<th>'.($livingheat>1?number_format($livingheat*0.1, 1, ',', ''):'').'</th>
				<th>'.($kamerheat>1?number_format($kamerheat*0.1, 1, ',', ''):'').'</th>
				<th class="border_right">'.($alexheat>1?number_format($alexheat*0.1, 1, ',', ''):'').'</th>
				<th>'.($livingcool>1?number_format($livingcool*0.1, 1, ',', ''):'').'</th>
				<th>'.($kamercool>1?number_format($kamercool*0.1, 1, ',', ''):'').'</th>
				<th class="border_right">'.($alexcool>1?number_format($alexcool*0.1, 1, ',', ''):'').'</th>
			</tr>
			<tr>
				<th class="border_right border_bottom">Total</th>
				<th colspan="6" class="border_right border_bottom">'.number_format(($livingheat+$kamerheat+$alexheat+$livingcool+$kamercool+$alexcool)*0.1, 1, ',', '') .'</th>
			<tr>
		</tfoot>
	</table>
	<br>
	<br>
	<table  id="table" cellpadding="2" cellspacing="0">
			<thead>
				<tr>
					<th rowspan="2" class="border_right">Month</th>
					<th colspan="3" class="border_right">Heat</th>
					<th colspan="3" class="border_right">Cool</th>
					<th rowspan="2" class="border_right">Sum</th>
				</tr>
				<tr class="border_bottom">
					<th>Living</th>
					<th>Kamer</th>
					<th class="border_right">Alex</th>
					<th>Living</th>
					<th>Kamer</th>
					<th class="border_right">Alex</th>
				</tr>
			</thead>
			<tbody>';
$sql="SELECT LEFT(date,7) as date, SUM(livingheat) AS livingheat, SUM(kamerheat) AS kamerheat, SUM(alexheat) AS alexheat, SUM(livingcool) AS livingcool, SUM(kamercool) AS kamercool, SUM(alexcool) AS alexcool FROM `daikin` GROUP BY LEFT(date, 7) ORDER BY `date` DESC LIMIT 0,30";
if (!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
$livingheat=0;
$kamerheat=0;
$alexheat=0;
$livingcool=0;
$kamercool=0;
$alexcool=0;
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	echo '
		<tr class="border_bottom">
			<td nowrap class="border_right">'.$row['date'].'</td>
			<td>'.($row['livingheat']>1?number_format($row['livingheat']*0.1, 1, ',', ''):'').'</td>
			<td>'.($row['kamerheat']>1?number_format($row['kamerheat']*0.1, 1, ',', ''):'').'</td>
			<td class="border_right">'.($row['alexheat']>1?number_format($row['alexheat']*0.1, 1, ',', ''):'').'</td>
			<td>'.($row['livingcool']>1?number_format($row['livingcool']*0.1, 1, ',', ''):'').'</td>
			<td>'.($row['kamercool']>1?number_format($row['kamercool']*0.1, 1, ',', ''):'').'</td>
			<td class="border_right">'.($row['alexcool']>1?number_format($row['alexcool']*0.1, 1, ',', ''):'').'</td>
			<td class="border_right">'.(($row['livingheat']+$row['kamerheat']+$row['alexheat']+$row['livingcool']+$row['kamercool']+$row['alexcool'])>0?number_format(($row['livingheat']+$row['kamerheat']+$row['alexheat']+$row['livingcool']+$row['kamercool']+$row['alexcool'])*0.1, 1, ',', ''):'').'</td>
		</tr>';
	$livingheat=$livingheat+$row['livingheat'];
	$kamerheat=$kamerheat+$row['kamerheat'];
	$alexheat=$alexheat+$row['alexheat'];
	$livingcool=$livingcool+$row['livingcool'];
	$kamercool=$kamercool+$row['kamercool'];
	$alexcool=$alexcool+$row['alexcool'];
}
echo '
		</tbody>
		<tfoot>
			<tr>
				<th class="border_right">Sum</th>
				<th>'.($livingheat>0.1?number_format($livingheat*0.1, 1, ',', ''):'').'</th>
				<th>'.($kamerheat>0.1?number_format($kamerheat*0.1, 1, ',', ''):'').'</th>
				<th class="border_right">'.($alexheat>0.1?number_format($alexheat*0.1, 1, ',', ''):'').'</th>
				<th>'.($livingcool>0.1?number_format($livingcool*0.1, 1, ',', ''):'').'</th>
				<th>'.($kamercool>0.1?number_format($kamercool*0.1, 1, ',', ''):'').'</th>
				<th class="border_right">'.($alexcool>0.1?number_format($alexcool*0.1, 1, ',', ''):'').'</th>
			</tr>
			<tr>
				<th rowspan="2" class="border_right border_bottom">Total</th>
				<th colspan="3" class="border_right border_bottom">'.number_format(($livingheat+$kamerheat+$alexheat)*0.1, 1, ',', '') .'</th>
				<th colspan="3" class="border_right border_bottom">'.number_format(($livingcool+$kamercool+$alexcool)*0.1, 1, ',', '') .'</th>
			</tr>
			<tr>
				<th colspan="6" class="border_right border_bottom">'.number_format(($livingheat+$kamerheat+$alexheat+$livingcool+$kamercool+$alexcool)*0.1, 1, ',', '') .'</th>
			<tr>
		</tfoot>
	</table>
		<br>
	<br>
	<table  id="table" cellpadding="2" cellspacing="0">
			<thead>
				<tr>
					<th rowspan="2" class="border_right">Year</th>
					<th colspan="3" class="border_right">Heat</th>
					<th colspan="3" class="border_right">Cool</th>
					<th rowspan="2" class="border_right">Sum</th>
				</tr>
				<tr class="border_bottom">
					<th>Living</th>
					<th>Kamer</th>
					<th class="border_right">Alex</th>
					<th>Living</th>
					<th>Kamer</th>
					<th class="border_right">Alex</th>
				</tr>
			</thead>
			<tbody>';
$sql="SELECT LEFT(date,4) as date, SUM(livingheat) AS livingheat, SUM(kamerheat) AS kamerheat, SUM(alexheat) AS alexheat, SUM(livingcool) AS livingcool, SUM(kamercool) AS kamercool, SUM(alexcool) AS alexcool FROM `daikin` GROUP BY LEFT(date, 4) ORDER BY `date` DESC LIMIT 0,30";
if (!$result=$db->query($sql)){die('There was an error running the query ['.$sql.'-'.$db->error.']');}
$livingheat=0;
$kamerheat=0;
$alexheat=0;
$livingcool=0;
$kamercool=0;
$alexcool=0;
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	echo '
		<tr class="border_bottom">
			<td nowrap class="border_right">'.$row['date'].'</td>
			<td>'.($row['livingheat']>1?number_format($row['livingheat']*0.1, 1, ',', ''):'').'</td>
			<td>'.($row['kamerheat']>1?number_format($row['kamerheat']*0.1, 1, ',', ''):'').'</td>
			<td class="border_right">'.($row['alexheat']>1?number_format($row['alexheat']*0.1, 1, ',', ''):'').'</td>
			<td>'.($row['livingcool']>1?number_format($row['livingcool']*0.1, 1, ',', ''):'').'</td>
			<td>'.($row['kamercool']>1?number_format($row['kamercool']*0.1, 1, ',', ''):'').'</td>
			<td class="border_right">'.($row['alexcool']>1?number_format($row['alexcool']*0.1, 1, ',', ''):'').'</td>
			<td class="border_right">'.(($row['livingheat']+$row['kamerheat']+$row['alexheat']+$row['livingcool']+$row['kamercool']+$row['alexcool'])>0?number_format(($row['livingheat']+$row['kamerheat']+$row['alexheat']+$row['livingcool']+$row['kamercool']+$row['alexcool'])*0.1, 1, ',', ''):'').'</td>
		</tr>';
	$livingheat=$livingheat+$row['livingheat'];
	$kamerheat=$kamerheat+$row['kamerheat'];
	$alexheat=$alexheat+$row['alexheat'];
	$livingcool=$livingcool+$row['livingcool'];
	$kamercool=$kamercool+$row['kamercool'];
	$alexcool=$alexcool+$row['alexcool'];
}
echo '
		</tbody>
		<tfoot>
			<tr>
				<th class="border_right">Sum</th>
				<th>'.($livingheat>0.1?number_format($livingheat*0.1, 1, ',', ''):'').'</th>
				<th>'.($kamerheat>0.1?number_format($kamerheat*0.1, 1, ',', ''):'').'</th>
				<th class="border_right">'.($alexheat>0.1?number_format($alexheat*0.1, 1, ',', ''):'').'</th>
				<th>'.($livingcool>0.1?number_format($livingcool*0.1, 1, ',', ''):'').'</th>
				<th>'.($kamercool>0.1?number_format($kamercool*0.1, 1, ',', ''):'').'</th>
				<th class="border_right">'.($alexcool>0.1?number_format($alexcool*0.1, 1, ',', ''):'').'</th>
			</tr>
			<tr>
				<th rowspan="2" class="border_right border_bottom">Total</th>
				<th colspan="3" class="border_right border_bottom">'.number_format(($livingheat+$kamerheat+$alexheat)*0.1, 1, ',', '') .'</th>
				<th colspan="3" class="border_right border_bottom">'.number_format(($livingcool+$kamercool+$alexcool)*0.1, 1, ',', '') .'</th>
			</tr>
			<tr>
				<th colspan="6" class="border_right border_bottom">'.number_format(($livingheat+$kamerheat+$alexheat+$livingcool+$kamercool+$alexcool)*0.1, 1, ',', '') .'</th>
			<tr>
		</tfoot>
	</table>';

?>
	</body>
</html>
