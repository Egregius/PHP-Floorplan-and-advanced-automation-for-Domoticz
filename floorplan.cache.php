<?php
$start=microtime(true);
require 'secure/functions.php';
require '/var/www/authentication.php';
//$db = Database::getInstance();
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
	<body>
		<div class="fix" style="bottom:5px;left:5px;">
			<a href=\'javascript:navigator_Go("floorplan.php");\'>
				<img src="/images/close.png" width="50px" height="50px"/>
			</a>
		</div>
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
uasort($d, function ($a, $b) {
    if (!isset($a->t) && !isset($b->t)) return 0;
    if (!isset($a->t)) return 1;
    if (!isset($b->t)) return -1;

    return $b->t <=> $a->t;
});

foreach($d as $n=>$row) {
	echo '
				<tr class="border_bottom">';
		if (str_ends_with($n, '_set')) {
			echo '
					<td>'.$n.'</td>';
			if ($row->s=='D') echo '
					<td>Drogen</td>';
			else echo '
					<td>'.number_format($row->s, 1, ',', '').' °C</td>';
			if ($row->m==0) {
				echo '
					<td>Auto</td>';
			} else {
				echo '
					<td>Manueel</td>';
			}
		} elseif (str_ends_with($n, '_temp')) {
			if ($n=='waskamer_temp'||$n=='zolder_temp') {
				echo  '
					<td>'.$n.'</td>
					<td>'.number_format($row->s, 1, ',', '').' °C</td>
					<td></td>';
			} else {
				echo '
					<td>'.$n.'</td>
					<td>'.number_format($row->s, 1, ',', '').' °C</td>
					<td>'.$row->m.' %</td>';
			}
		} elseif (str_starts_with($n, 'r')) {
			echo '
					<td>'.$n.'</td>';
			if ($row->s==0) {
				echo '
					<td>Open</td>';
			} elseif ($row->s==100) {
				echo '
					<td>Gesloten</td>';
			} else {
				echo '
					<td>'.$row->s.' % Toe</td>';
			}
			echo '
					<td></td>';
		} elseif (str_starts_with($n, '8')) {
			echo '
					<td>'.$n.'</td>
					<td></td>
					<td></td>';
		} elseif($n=='luifel') {
			echo '
					<td>'.$n.'</td>';
			if ($row->s==0) {
				echo '
					<td>Gesloten</td>';
			} elseif ($row->s==100) {
				echo '
					<td>Open</td>';
			} else {
				echo '
					<td>'.$row->s.' % Open</td>';
			}
			if ($row->m==0) {
				echo '
					<td>Auto</td>';
			} else {
				echo '
					<td>Manueel</td>';
			}
		} elseif (in_array($n, array('eettafel','zithoek','kamer','waskamer','alex','lichtbadkamer'))) {
			echo '
					<td>'.$n.'</td>';
			if ($row->s==0) {
				echo '
					<td>Off</td>';
			} else {
				echo '
					<td>'.$row->s.'</td>';
			}
			if ($row->m==0) {
				echo '
					<td></td>';
			} elseif ($row->m==1) {
				echo '
					<td>Wake-up</td>';
			} elseif ($row->m==2) {
				echo '
					<td>Sleep</td>';
			}
		} elseif ($n=='Weg') {
			echo '
					<td>Weg</td>';
			if ($row->s==0) {
				echo '
					<td>Thuis</td>';
			} elseif ($row->s==1) {
				echo '
					<td>Slapen</td>';
			} elseif ($row->s==2) {
				echo '
					<td>Weg</td>';
			}
			echo '
					<td nowrap>Laatste beweging:<br>'.date("d-m G:i:s", $row->m).'</td>';
		} elseif ($n=='auto') {
			echo '
					<td>'.$n.'</td>';
			if ($row->s=='Off') {
				echo '
					<td>Lichten manueel</td>';
			} elseif ($row->s=='On') {
				echo '
					<td>Lichten automatisch</td>';
			}
			if ($row->m==0) {
				echo '
					<td>Nacht</td>';
			} elseif ($row->m==1) {
				echo '
					<td>Dag</td>';
			} else echo '
					<td></td>';
		} elseif ($n=='heating') {
			echo '
					<td>Heating</td>
					<td>';
			if ($row->s==0) {
				echo '0 Neutral';
			} elseif ($row->s==-2) {
				echo '-2 Active cooling';
			} elseif ($row->s==-1) {
				echo '-1 Passive cooling';
			} elseif ($row->s==1) {
				echo '1 Airco heating';
			} elseif ($row->s==2) {
				echo '2 Gas/Airco heating';
			} elseif ($row->s==3) {
				echo '2 Gas heating';
			}
			echo '</td>
					<td></td>';
		} else {
			echo '
					<td>'.$n.'</td>
					<td>'.substr($row->s ?? '', 0, 20).'</td>
					<td>'.substr($row->m ?? '', 0, 20).'</td>';
		}
	if (isset($row->t)) {
		if ($row->t<TIME - (86400*7*4)) {
			echo '
					<td nowrap>'.date('d-m-Y', $row->t).'</td>
				</tr>';
		} elseif ($row->t<TIME - 82800) {
			echo '
					<td nowrap>'.date('d-m-Y G:i', $row->t).'</td>
				</tr>';
		} else {
			echo '
					<td nowrap>'.date("G:i:s", $row->t).'</td>
				</tr>';
		}
	} else {
		echo '
					<td></td>
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
