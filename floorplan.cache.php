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
require 'secure/settings.php';
if ($home) {
    echo '
<html>
	<head>
		<title>Floorplan</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.655,user-scalable=yes,minimal-ui"/>
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
			<a href=\'javascript:navigator_Go("floorplan.php");\'>
				<img src="/images/close.png" width="50px" height="50px"/>
			</a>
		</div>
		<br>
		<br>
		<br>
	    <div class="fix" style="top:82px;left:0px">
		<table>';
    $sql="SELECT *  FROM `devices` ORDER BY t DESC";
    if (!$result=$db->query($sql)) {
        die('There was an error running the query ['.$sql.' - '.$db->error.']');
    }
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        //print_r($row);
        echo '
        <tr>
            <td nowrap>'.$row['n'].'</td>
            <td nowrap>&nbsp;'.substr($row['s'], 0, 25).'&nbsp;</td>
            <td nowrap>&nbsp;'.$row['m'].'</td>
            <td nowrap>&nbsp;'.$row['t'].'</td>
        </tr>';
        @$count++;
    }
    echo '
    </table>
    <script type="text/javascript">
        function navigator_Go(url) {window.location.assign(url);}
    </script>';
}
?>
    </body>
</html>