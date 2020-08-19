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
require '/var/www/proxmox/vendor/autoload.php';
use ProxmoxVE\Proxmox;

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
		    td{font-size:0.8em;text-align:center;}
		    th{text-align:center;}
		    .fix{width:320px;padding:0}
		    .btn{width:300px;}
		    .btnd{width:236px;}
			table{border-collapse: collapse; }
			tr{border:1px solid grey;}
			th, td{border:1px solid grey;padding:5px;}
			.right{text-align:right;}
        </style>
	</head>
	<body>
		<div class="fix" style="top:0px;left:0px;height:50px;width:50px;background-color:#CCC">
			<a href=\'javascript:navigator_Go("floorplan.proxmox.php");\'>
				<img src="/images/restart.png" width="50px" height="50px"/>
			</a>
		</div>';

$proxmox = new Proxmox($proxmoxcredentials);

if (isset($_POST['vmid'])&&isset($_POST['action'])) {
	$proxmox->create('/nodes/proxmox/qemu/'.$_POST['vmid'].'/status/'.$_POST['action']);
}

$data = $proxmox->get('/nodes/proxmox/qemu');

	  echo '
		<br>
		<br>
		<br>
		<br>
		<table>
			<thead>
				<tr>
					<th rowspan="2">id</th>
					<th rowspan="2">Name</th>
					<th>Status</th>
					<th colspan="4">Action</th>
				</tr>
				<tr>
					<th nowrap>Uptime</th>
					<th>CPU</th>
					<th>Memory</th>
					<th>netin</th>
					<th>netout</th>
				</tr>
			</thead>
			<tbody>';
		foreach ($data as $node) {
			uasort($node, "cmp");
			foreach ($node as $vm) {
				echo '
				<tr>
					<td rowspan="2">'.$vm['vmid'] .'</td>
					<td rowspan="2">'.substr($vm['name'], 2).'</td>
					<td>'.$vm['status'].'</td>
					<td colspan="4">
						<form method="POST">
							<input type="hidden" name="vmid" value="'.$vm['vmid'].'"/>';
				if($vm['status']=='stopped') {
					echo '
							<input type="submit" name="action" value="start" class="btn">';
				} else {
					echo '
							<input type="submit" name="action" value="stop">
							<input type="submit" name="action" value="shutdown">';
				}
				echo '
						</form>
					</td>
				</tr>
				<tr>
					<td class="right">'.floor($vm['uptime']/86400).'d</td>
					<td class="right">'.gmdate("G:i", ($vm['uptime']%86400)).'</td>
					<td class="right">'.number_format($vm['cpu'], 2).'</td>
					<td class="right">'.human_filesize($vm['mem']).'/'.human_filesize($vm['maxmem']).'</td>
					<td class="right">'.human_filesize($vm['netin']).'</td>
					<td class="right">'.human_filesize($vm['netout']).'</td>
					
				</tr>';
			}
		}
	  echo '
	  	</tbody>
	  </table>';



	
}
function cmp($a, $b) {
    return strcmp($a['name'], $b['name']);
}
?>
    </body>
</html>