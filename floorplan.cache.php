<?php
$start=microtime(true);
require 'secure/functions.php';
require '/var/www/authentication.php';
$db = Database::getInstance();
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
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale='.$scale.',user-scalable=yes,minimal-ui"/>
		<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<script type="text/javascript" language="javascript" src="https://mynetpay.be/js/jquery-3.5.1.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://mynetpay.be/js/jQuery.dataTables.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://mynetpay.be/js/jQuery.dataTables.columnFilter.js"></script>
		<script type="text/javascript" src="/scripts/floorplanjs.js"></script>

		<link rel="stylesheet" type="text/css" href="/styles/floorplan.css">
		<style>
			html{width:100%!important;}
			body{width:100%!important;overflow:auto;}
			td{font-size:1em;text-align:left;white-space:nowrap;}
			th{text-align:left;}
			.fix{width:320px;padding:0}
			.btn{width:300px;}
			.btnd{width:236px;}
			.b4{max-width:155px!important;}
			.b3{max-width:320px!important;}
			tr.border_bottom td {border-bottom:1pt dotted #777;color:#FFF;font-size:0.9em}
			#table {
				width: 100% !important;
				table-layout: fixed;
			}
			#table th:nth-child(1),
			#table td:nth-child(1) { width: 18%; }

			#table th:nth-child(2),
			#table td:nth-child(2) { width: 15%; }

			#table th:nth-child(3),
			#table td:nth-child(3) { width: 10%; }

			#table th:nth-child(4),
			#table td:nth-child(4) {
				width: 12%;
				white-space: nowrap;
			}
			.table-wrapper {
				width: 100%;
				overflow-x: auto;
			}

			.table-wrapper table {
				width: 100% !important;
			}
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
			<a href=\'javascript:navigator_Go("floorplan.php");\'>
				<img src="/images/close.png" width="50px" height="50px"/>
			</a>
		</div>
		<br>
		<br>
		<br>
		<div class="table-wrapper">
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
$d=fetchdata(0, basename(__FILE__).':'.__LINE__);
$sql="SELECT *  FROM `devices` ORDER BY t DESC";
if (!$result=$db->query($sql)) {
	die('There was an error running the query ['.$sql.' - '.$db->error.']');
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
	echo '
				<tr class="border_bottom">';
	if (isset($_REQUEST['nicestatus'])) {
		if (str_ends_with($row['n'], '_set')) {
			echo '
					<td>'.$row['n'].'</td>';
			if ($row['s']=='D') echo '
					<td>Drogen</td>';
			else echo '
					<td>'.number_format($row['s'], 1, ',', '').' °C</td>';
			if ($row['m']==0) {
				echo '
					<td>Auto</td>';
			} else {
				echo '
					<td>Manueel</td>';
			}
		} elseif (str_ends_with($row['n'], 'Z')) {
			echo '
					<td>'.$row['n'].'</td>
					<td>'.number_format($row['s'], 1, ',', '').' °C</td>
					<td></td>';
		} elseif (str_ends_with($row['n'], '_temp')) {
			if ($row['n']=='buiten_temp') {
				echo  '
					<td>'.$row['n'].'</td>
					<td>'.number_format($row['s'], 1, ',', '').' °C</td>
					<td>'.number_format($row['m'], 0, ',', '').' % Buien</td>';
			} else {
				echo '
					<td>'.$row['n'].'</td>
					<td>'.number_format($row['s'], 1, ',', '').' °C</td>
					<td>'.$row['m'].'</td>';
			}
		} elseif (str_starts_with($row['n'], 'R')) {
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
			} elseif ($row['m']==1) {
				echo '
					<td>Manueel</td>';
			} elseif ($row['m']==2) {
				echo '
					<td>Slapen</td>';
			}
		} elseif (str_starts_with($row['n'], '8')) {
			echo '
					<td>'.$row['n'].'</td>
					<td></td>
					<td></td>';
		} elseif (str_starts_with($row['n'], 'mini')) {
			echo '
					<td>'.$row['n'].'</td>
					<td></td>
					<td></td>';
		} elseif($row['n']=='luifel') {
			echo '
					<td>'.$row['n'].'</td>';
			if ($row['s']==0) {
				echo '
					<td>Gesloten</td>';
			} elseif ($row['s']==100) {
				echo '
					<td>Open</td>';
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
		} elseif (in_array($row['n'], array('eettafel','zithoek','kamer','waskamer','alex','lichtbadkamer'))) {
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
					<td>'.($row['m']>0?number_format($row['m'], 1, ',', ''):0).' % Ref</td>';
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
					<td>'.number_format($row['s'], 0, ',', '').' W</td>
					<td></td>';
		} elseif ($row['n']=='elec') {
			echo '
					<td>Elec</td>
					<td>'.number_format($row['s'], 0).' W</td>
					<td>'.number_format($row['m'], 1, ',', '').' kWh</td>';
		} elseif ($row['n']=='icon') {
			echo '
					<td>icon</td>
					<td><img src="/images/'.$d['icon']['s'].'.png" alt="icon"></td>
					<td>'.$row['m'].'% Humidity</td>';
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
					<td nowrap>Laatste beweging:<br>'.date("d-m G:i:s", $row['m']).'</td>';
		} elseif ($row['n']=='wind') {
			echo '
					<td>'.$row['n'].'</td>
					<td>'.number_format($row['s'], 1, ',', '').' km/u</td>';
			echo '
					<td></td>';
		} elseif ($row['n']=='auto') {
			echo '
					<td>'.$row['n'].'</td>';
			if ($row['s']=='Off') {
				echo '
					<td>Lichten manueel</td>';
			} elseif ($row['s']=='On') {
				echo '
					<td>Lichten automatisch</td>';
			}
			if ($row['m']==0) {
				echo '
					<td>Nacht</td>';
			} elseif ($row['m']==1) {
				echo '
					<td>Dag</td>';
			} else echo '
					<td></td>';
		} elseif ($row['n']=='minmaxtemp') {
			echo '
					<td>Temp &lt; 6u</td>
					<td>min '.number_format($row['s'], 1, ',', '') .' °C</td>
					<td>max '.number_format($row['m'], 1, ',', '').' °C</td>';
		} elseif ($row['n']=='heating') {
			echo '
					<td>Heating</td>
					<td>';
			if ($row['s']==0) {
				echo '0 Neutral';
			} elseif ($row['s']==-2) {
				echo '-2 Active cooling';
			} elseif ($row['s']==-1) {
				echo '-1 Passive cooling';
			} elseif ($row['s']==1) {
				echo '1 Airco heating';
			} elseif ($row['s']==2) {
				echo '2 Gas heating';
			}
			echo '</td>
					<td>Big diff '.number_format($row['m']??0, 1, ',', '').' °C</td>';
		} elseif ($row['n']=='jaarteller') {
			echo '
					<td>Teller jaar</td>
					<td>'.number_format($row['s'], 2, ',', '').' kWh/dag</td>
					<td></td>';
		} else {
			echo '
					<td>'.$row['n'].'</td>
					<td>'.substr($row['s'] ?? '', 0, 20).'</td>
					<td>'.substr($row['m'] ?? '', 0, 20).'</td>';
		}
	} else {
		echo '
					<td>'.$row['n'].'</td>
					<td>'.substr($row['s'] ?? '', 0, 20).'</td>
					<td>'.substr($row['m'] ?? '', 0, 20).'</td>';
	}
	if ($row['t']<TIME - (86400*7*4)) {
		echo '
					<td nowrap>'.date('d-m-Y', $row['t']).'</td>
				</tr>';
	} else {
		echo '
					<td nowrap>'.date("d-m G:i:s", $row['t']).'</td>
				</tr>';
	}
}
echo '
		</tbody>
	</table>
	<br>
	</div>
	<br>';
?>
	<script type="text/javascript" charset="utf-8">
		var asInitVals = new Array();
		$(document).ready(function() {
			$('#table').dataTable(
			{
				"bStateSave": true,
				"bPaginate": false,
				"ordering": false,
				"fnInitComplete":function(){
					$("#table_filter input").focus();}
			});
		});
	</script>
	</body>
</html>
