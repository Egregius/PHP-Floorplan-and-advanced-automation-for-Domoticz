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
	} elseif ($udevice=='iPhoneSE') {
	    echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.74,user-scalable=yes,minimal-ui"/>';
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
		    td{font-size:1.1em;text-align:center;}
		    th{text-align:center;}
		    .fix{width:320px;padding:0}
		    .btn{width:300px;font-size:1.4em;height:50px;}
			table{border-collapse: collapse; }
			tr{border:1px solid grey;}
			th, td{border:1px solid grey;padding:5px;height:50px;min-width:55px;}
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
					<th rowspan="2">name</th>
					<th>Status</th>
					<th colspan="4">action</th>
				</tr>
				<tr>
					<th nowrap>uptime</th>
					<th>&nbsp;cpu&nbsp;</th>
					<th>memory</th>
					<th>netin</th>
					<th>netout</th>
				</tr>
			</thead>
			<tbody>';
		foreach ($data as $node) {
			uasort($node, "cmp");
			foreach ($node as $vm) {
				$name=substr($vm['name'], 2);
				echo '
				<tr>
					<td rowspan="2">'.$name.'</td>';
				if($name!='Domoticz'&&$name!='pfSense') {
					echo '
					
					<td>'.$vm['status'].'</td>
					<td colspan="4">
						<form method="POST">
							<input type="hidden" name="vmid" value="'.$vm['vmid'].'"/>';
					if($vm['status']=='stopped') {
						echo '
							<input type="submit" name="action" value="start" class="btn"/>';
					} else {
						echo '
							<input type="submit" name="action" value="stop" class="btn b2"/>
							<input type="submit" name="action" value="shutdown" class="btn b2"/>';
					}
					echo '
						</form>
					</td>';
				}
				echo '
				</tr>
				<tr>';
				if($vm['status']!='stopped') {
					echo '
					<td>'.floor($vm['uptime']/86400).'d '.gmdate("G:i:s", ($vm['uptime']%86400)).'</td>
					<td>'.number_format($vm['cpu']*100, 0).' %</td>
					<td>'.human_filesize($vm['mem']).'<br>'.human_filesize($vm['maxmem']).'</td>
					<td>'.human_filesize($vm['netin']).'</td>
					<td>'.human_filesize($vm['netout']).'</td>';
				}
				echo '
				</tr>';
			}
		}
	  echo '
	  	</tbody>
	  </table>
	  <br>
	  <br>
	  <script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\',20000);</script>';

	$data = $proxmox->get('/nodes/proxmox/status');
	$data=$data['data'];
	echo '
		<table>
			<thead>
				<tr>
					<th>Uptime</th>
					<th>cpu</th>
					<th>memory</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>'.floor($data['uptime']/86400).'d '.gmdate("G:i:s", ($data['uptime']%86400)).'</td>
					<td>'.number_format($data['cpu']*100, 0).' %</td>
				</tr>
			</tbody>
		</table>';
	echo '<pre>';print_r($data);echo '</pre>';
	
}
function cmp($a, $b) {
    return strcmp($a['name'], $b['name']);
}
?>
    </body>
</html>