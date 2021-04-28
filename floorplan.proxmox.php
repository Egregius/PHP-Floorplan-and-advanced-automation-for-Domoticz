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
$start=microtime(true);
require 'secure/functions.php';
require '/var/www/authentication.php';
require '/var/www/proxmox/vendor/autoload.php';
use ProxmoxVE\Proxmox;
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
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.css">
		<style>
			html{width:100%!important;}
			body{width:100%!important;}
			td{font-size:1.1em;text-align:center;}
			th{text-align:center;}
			.fix{width:100%;padding:0}
			.btn{width:300px;font-size:1.4em;height:50px;}
			table{border-collapse:collapse;width:100%;}
			tr{border:1px solid grey;}
			th, td{border:1px solid grey;padding:5px;height:50px;min-width:55px;}
			.borderlefttick{border-left: 3px solid white;}
			.borderrighttick{border-right: 3px solid white;}
			.borderbottomtick{border-bottom: 3px solid white;}
			.bordertoptick{border-top: 3px solid white;}
		</style>
	</head>
	<body>
		<div class="fix" style="top:0px;left:0px;height:50px;width:50px;background-color:#CCC">
			<a href=\'javascript:navigator_Go("floorplan.php");\'>
				<img src="/images/close.png" width="50px" height="50px"/>
			</a>
		</div>
		<div class="fix" style="top:0px;left:100px;height:50px;width:50px;background-color:#CCC">
			<a href=\'javascript:navigator_Go("floorplan.proxmox.php");\'>
				<img src="/images/restart.png" width="50px" height="50px"/>
			</a>
		</div>';

$proxmox = new Proxmox($proxmoxcredentials);

if (isset($_POST['vmid'])&&isset($_POST['action'])) {
	$proxmox->create('/nodes/proxmox/qemu/'.$_POST['vmid'].'/status/'.$_POST['action']);
}

$data = $proxmox->get('/nodes/proxmox/qemu');
$resources = $proxmox->get('/cluster/resources');
$vms=array();
if (isset($resources['data'])) {
	foreach ($resources['data'] as $i) {
		//echo '<pre>';print_r($i);echo '</pre>';
		if (isset($i['vmid'])) {
			$vms[$i['vmid']]['vmid']=$i['vmid'];
			$vms[$i['vmid']]['name']=$i['name'];
			$vms[$i['vmid']]['uptime']=$i['uptime'];
			$vms[$i['vmid']]['status']=$i['status'];
			$vms[$i['vmid']]['mem']=$i['mem'];
			$vms[$i['vmid']]['maxmem']=$i['maxmem'];
			$vms[$i['vmid']]['diskread']=$i['diskread'];
			$vms[$i['vmid']]['diskwrite']=$i['diskwrite'];
			$vms[$i['vmid']]['cpu']=$i['cpu'];
			$vms[$i['vmid']]['netin']=$i['netin'];
			$vms[$i['vmid']]['netout']=$i['netout'];
		}
	}
	uasort($vms, "cmp");
	//echo '<hr><pre>';print_r($vms);echo '</pre>';

	echo '
			<br>
			<br>
			<br>
			<table>
				<thead>
					<tr>
						<th rowspan="2" class="borderlefttick bordertoptick">name</th>
						<th colspan="2" class="bordertoptick">uptime</th>
						<th class="bordertoptick">&nbsp;cpu&nbsp;</th>
						<th class="borderrighttick bordertoptick">memory</th>
					</tr>
					<tr>
						<th nowrap>disk read</th>
						<th nowrap>disk write</th>
						<th>net in</th>
						<th class="borderrighttick">net out</th>
					</tr>
				</thead>
				<tbody>';
	foreach ($vms as $vm) {
		$name=substr($vm['name'], 2);
		echo '
					<tr>
						<td rowspan="3"  class="bordertoptick borderlefttick borderbottomtick">'.$name.'</td>';
		if($name!='Domoticz'&&$name!='pfSense') {
			echo '
						<td colspan="4"  class="borderbottomtick borderrighttick">
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
						<td colspan="2" class="bordertoptick">'.floor($vm['uptime']/86400).'d '.gmdate("G:i:s", ($vm['uptime']%86400)).'</td>
						<td class="bordertoptick">'.number_format($vm['cpu']*100, 0).' %</td>
						<td class="bordertoptick borderrighttick">'.human_filesize($vm['mem']).'<br>'.human_filesize($vm['maxmem']).'</td>';
		}
		echo '
					</tr>
					<tr>';
		if($vm['status']!='stopped') {
			echo '
						<td class="borderbottomtick">'.human_filesize($vm['diskread']).'</td>
						<td class="borderbottomtick">'.human_filesize($vm['diskwrite']).'</td>
						<td class="borderbottomtick">'.human_filesize($vm['netin']).'</td>
						<td class="borderbottomtick borderrighttick">'.human_filesize($vm['netout']).'</td>';
		}
		echo '
					</tr>';
	}
	echo '
			</tbody>
		</table>
		<br>
		<br>
		<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\',2000);</script>';
	$data = $proxmox->get('/nodes/proxmox/status');
	$data=$data['data'];
	echo '
			<table>
				<thead>
					<tr>
						<th>Uptime</th>
						<th>cpu</th>
						<th>memory</th>
						<th>load avg</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>'.floor($data['uptime']/86400).'d '.gmdate("G:i:s", ($data['uptime']%86400)).'</td>
						<td>'.number_format($data['cpu']*100, 0).' %</td>
						<td>'.human_filesize($data['memory']['used']).'<br>'.human_filesize($data['memory']['total']).'</td>
						<td>';
	foreach($data['loadavg'] as $i) {
		echo $i.' - '.number_format(($i/4)*100, 2) .'%<br>';
	}
	echo '
						</td>
					</tr>
				</tbody>
			</table>';
} else echo '<br><br><br><br><br>NO CONNECTION WITH PROXMOX';
//	echo '<pre>';print_r($data);echo '</pre>';

function cmp($a, $b) {
	return strcmp($a['name'], $b['name']);
}
?>
	</body>
</html>
