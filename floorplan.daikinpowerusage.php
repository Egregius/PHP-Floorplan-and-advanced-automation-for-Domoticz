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
$start=microtime(true);
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home) {
	$db=dbconnect();
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
		<script type="text/javascript" src="/scripts/floorplanjs.js"></script>
		<script type="text/javascript" charset="utf-8">
			var asInitVals = new Array();
			$(document).ready(function() {
				$(\'#table\').dataTable(
				{
					"bStateSave": true,
					"bPaginate": false,
                    "ordering": false,
                    "fnInitComplete":function(){
						$("#table_filter input").focus();}
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
		    td{text-align:right;}
        </style>
	</head>
	<body>';

	echo '
		<div class="fix" style="top:0px;right:0px;">
			<a href=\'javascript:navigator_Go("floorplan.php");\'>
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
		            <th>Date</th>
		            <th>S</th>
		            <th>M</th>
		            <th>Time</th>
		        </tr>
		    </thead>
		    <tbody>';
	$d=fetchdata();
    $sql="SELECT * FROM `daikin` ORDER BY `date` DESC LIMIT 0,10";
    if (!$result=$db->query($sql)) {
        die('There was an error running the query ['.$sql.' - '.$db->error.']');
    }
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '
        <tr class="border_bottom">
        	<td>'.$row['date'].'</td>
        	<td>'.$row['livingheat']*0.1 .'</td>
        	<td>'.$row['kamerheat']*0.1 .'</td>
        	<td>'.$row['alexheat']*0.1 .'</td>
        	<td>'.$row['livingcool']*0.1 .'</td>
        	<td>'.$row['kamercool']*0.1 .'</td>
        	<td>'.$row['alexcool']*0.1 .'</td>
        </tr>';
    }
    echo '
        </tbody>
    </table>
    <br>
    <br>';
}
?>
    </body>
</html>