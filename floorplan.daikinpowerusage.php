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
		<script type="text/javascript" src="/scripts/floorplanjs.js"></script>
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
		        <tr>
		        	<th rowspan="2">Date</th>
		        	<th colspan="3">Heat</th>
		        	<th colspan="3">Cool</th>
		        </tr>
		        <tr class="border_bottom">
		            <th>Living</th>
		            <th>Kamer</th>
		            <th>Alex</th>
		            <th>Living</th>
		            <th>Kamer</th>
		            <th>Alex</th>
		        </tr>
		    </thead>
		    <tbody>';
	$d=fetchdata();
    $sql="SELECT * FROM `daikin` ORDER BY `date` DESC LIMIT 0,10";
    if (!$result=$db->query($sql)) {
        die('There was an error running the query ['.$sql.' - '.$db->error.']');
    }
    $livingheat=0;
    $kamerheat=0;
    $alexheat=0;
    $livingcool=0;
    $kamercool=0;
    $alexcool=0;
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        echo '
        <tr class="border_bottom">
        	<td nowrap>'.$row['date'].'</td>
        	<td>'.$row['livingheat']*0.1 .'</td>
        	<td>'.$row['kamerheat']*0.1 .'</td>
        	<td>'.$row['alexheat']*0.1 .'</td>
        	<td>'.$row['livingcool']*0.1 .'</td>
        	<td>'.$row['kamercool']*0.1 .'</td>
        	<td>'.$row['alexcool']*0.1 .'</td>
        </tr>';
        $livingheat=$livingheat+$row['livingheat'];
        $kamerheat=$kamerheat+$row['kamerheat'];
        $livingheat=$livingheat+$row['livingheat'];
        $livingheat=$livingheat+$row['livingheat'];
        $livingheat=$livingheat+$row['livingheat'];
        $livingheat=$livingheat+$row['livingheat'];
    }
    echo '
        </tbody>
        <tfoot>
        	<tr>
        		<th>Sum</th>
        		<th>'.$livingheat*0.1 .'</th>
        		<th>'.$kamerheat*0.1 .'</th>
        		<th>'.$alexheat*0.1 .'</th>
        		<th>'.$livingcool*0.1 .'</th>
        		<th>'.$kamercool*0.1 .'</th>
        		<th>'.$alexcool*0.1 .'</th>
        	</tr>
        </tfoot>
    </table>
    <br>
    <br>';
}
?>
    </body>
</html>