<?php
require 'secure/functions.php';
require '/var/www/authentication.php';
if (isset($_POST['cmd'])) {
	$ctx=stream_context_create(array('http'=>array('timeout' =>2)));
	if ($_POST['cmd']=='mediauit') {
		ud('miniliving4l', 0, 'On');
	} elseif ($_POST['cmd']=='UpdateKodi') {
		$profile=$_POST['action'];echo 'Wanted profile='.$profile.'<br/>';
		profile:
		$loadedprofile=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.GetCurrentProfile","id":1}', false, $ctx), true);
		echo 'loadedprofile='.$loadedprofile['result']['label'].'<br/>';
		if ($loadedprofile['result']['label']!==$profile) {
			kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Stop","params":{"playerid":1}}');
			usleep(10000);
			$profilereply=@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.LoadProfile","params":{"profile":"'.$profile.'"},"id":1}', false, $ctx);
			echo 'profilereply='.$profilereply.'</pre><br/>';
			$count=$count + 1;
			if ($count>10) {
				die('Die Endless loop');
			}
			sleep(3);
			goto profile;
		} else {
			kodi('{"jsonrpc":"2.0","id":1,"method":"Videolibrary.Scan"}');
		}
	} elseif ($_POST['cmd']=='CleanKodi') {
		kodi('{"jsonrpc":"2.0","id":1,"method":"Videolibrary.Clean"}');
	} elseif ($_POST['cmd']=='PauseKodi') {
		kodi('{"jsonrpc":"2.0","id":1,"method":"Player.PlayPause","params":{"playerid":1}}');
	} elseif ($_POST['cmd']=='StopKodi') {
		kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Stop","params":{"playerid":1}}');
	} elseif ($_POST['cmd']=='bigbackward') {
		kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":{"seconds":-300}}}');
	} elseif ($_POST['cmd']=='smallbackward') {
		kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":{"seconds":-60}}}');
	} elseif ($_POST['cmd']=='smallforward') {
		kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":{"seconds":60}}}');
	} elseif ($_POST['cmd']=='bigforward') {
		kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":{"seconds":300}}}');
	} elseif ($_POST['cmd']=='audio') {
		kodi('{"jsonrpc":"2.0","id":1,"method":"Player.SetAudioStream","params":{"playerid":1,"stream":'.$_POST['action'].'}}', false, $ctx);
	} elseif ($_POST['cmd']=='subtitle') {
		if ($_POST['action']=='disable') {
			kodi('{"jsonrpc":"2.0","id":1,"method":"Player.SetSubtitle","params":{"playerid":1,"subtitle":"off"}}', false, $ctx);
		} elseif ($_POST['action']=='enable') {
			kodi('{"jsonrpc":"2.0","id":1,"method":"Player.SetSubtitle","params":{"playerid":1,"subtitle":"on"}}', false, $ctx);
		} else {
			kodi('{"jsonrpc":"2.0","id":1,"method":"Player.SetSubtitle","params":{"playerid":1,"subtitle":'.$_POST['action'].'}}', false, $ctx);
		}
	}
	exit;
} elseif (isset($_POST['kodicontrol'])) {
	header("Location: ../kodicontrol.php");
	die("Redirecting to: ../kodicontrol.php");
}

//error_reporting(E_ALL);ini_set("display_errors", "on");
$count=0;
$ctx=stream_context_create(array('http'=>array('timeout'=>4)));
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="HandheldFriendly" content="true"/>';
	if ($ipaddress=='192.168.2.203'||$ipaddress=='192.168.4.3')  { //Aarde
		echo '
		<meta name="viewport" content="width=300,height=500,initial-scale=0.84,user-scalable=yes,minimal-ui">';
	} elseif ($ipaddress=='192.168.2.204'||$ipaddress=='192.168.4.4'||$udevice=='iPad')  { //iPad
		echo '
		<meta name="viewport" content="width=device-width,initial-scale=1.15,user-scalable=yes,minimal-ui">';
	} elseif ($ipaddress=='192.168.2.23'||$ipaddress=='192.168.4.5')  { //iPhone Kirby
		echo '
		<meta name="viewport" content="width=device-width,initial-scale=0.755,user-scalable=yes,minimal-ui">';
	} elseif ($udevice=='iPhone') {
		echo '
		<meta name="viewport" content="width=device-width,initial-scale=0.755,user-scalable=yes,minimal-ui">';
	} else {
		echo '
		<meta name="viewport" content="width=device-width,user-scalable=yes,minimal-ui">';
	}
	echo '
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="theme-color" content="#000">
	<title>Kodi</title>
	<link rel="icon" type="image/png" href="images/kodi.png">
	<link rel="shortcut icon" href="images/kodi.png" />
	<link rel="apple-touch-icon" href="images/kodi.png"/>
	<link rel="icon" sizes="196x196" href="images/kodi.png">
	<link rel="icon" sizes="192x192" href="images/kodi.png">
	<meta name="mobile-web-app-capable" content="yes">
	<script type="text/javascript">
		setTimeout(\'window.location.href=window.location.href;\', 14950);
		function navigator_Go(url) {window.location.assign(url);}
		function exec(cmd, action=""){
			$.post("kodi.php",
			{
				cmd : cmd,
				action : action
			})
		}
	</script>
	<link href="/styles/kodi.css?v=6" rel="stylesheet" type="text/css"/>
	<script language="javascript" type="text/javascript" src="/scripts/jquery.2.0.0.min.js"></script>
	</head>
	<body>
		<div class="navbar">
			<form action="/floorplan.php">
				<input type="submit" class="btn b2" value="Plan"/>
			</form>
			<form action="/kodi.php">
				<input type="submit" class="btn btna b2" value="Kodi"/>
			</form>
		</div>';
$current=json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","method":"Player.GetItem","params":{"properties":["title","album","artist","season","episode","duration","showtitle","tvshowid","thumbnail","file","imdbnumber","streamdetails"],"playerid":1},"id":"VideoGetItem"}', false, $ctx), true);
if (isset($current['result']['item']['file'])) {
	if (!empty($current['result']['item']['file'])) {
		echo '
			<div class="title">';
		$item=$current['result']['item'];
		echo '<pre>';print_r($current);echo '</pre>';
		//print_r($item);
		if ($item['episode']>0) {
			echo '
				<a href="http://www.imdb.com/title/'.$item['imdbnumber'].'" style="color:#f5b324"><h1>'.$item['showtitle'].' S '.$item['season'].' E '.$item['episode'].'</h1></a>';
			echo '
				<h1>'.$item['label'].'</h1>';
		} else {
			echo '
				<a href="http://www.imdb.com/title/'.$item['imdbnumber'].'" style="color:#f5b324"><h1>'.$item['label'].'</h1></a>';
		}
		$properties=json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","method":"Player.GetProperties","id":1,"params":{"playerid":1,"properties":["playlistid","speed","position","totaltime","time","audiostreams","currentaudiostream","subtitleenabled","subtitles","currentsubtitle"]}}', false, $ctx), true);
		echo '<pre>';print_r($properties);echo '</pre>';
		if (!empty($properties['result'])) {
			$prop=$properties['result'];
			$point=$prop['time'];
			$total=$prop['totaltime'];
			$passedtime=$point['hours'].':';
			$point['minutes']<10?$passedtime.='0'.$point['minutes'].':':$passedtime.=$point['minutes'].':';
			$point['seconds']<10?$passedtime.='0'.$point['seconds']:$passedtime.=$point['seconds'];
			$totaltime=$total['hours'].':';
			$total['minutes']<10?$totaltime.='0'.$total['minutes'].':':$totaltime.=$total['minutes'].':';
			$total['seconds']<10?$totaltime.='0'.$total['seconds']:$totaltime.=$total['seconds'];
			if ($udevice=='iPad') {
				echo '
					<table align="center">
						<tr>
							<td>Passed</td>
							<td><h2>'.$passedtime.'</h2></td>
							<td>Runtime</td><td><h2>'.$totaltime.'</h2></td>
							<td>Remaining</td>
							<td><h2>'.date('G:i:s', (strtotime($totaltime)-strtotime($passedtime)-3600)).'</h2></td>
							<td>End at</td>
							<td><h2>'.date('G:i:s', (TIME+strtotime($totaltime)-strtotime($passedtime))).'</h2></td>
						</tr>
					</table>
				</div>';
			} else {
				echo '
					<table align="center">
						<tr>
							<td>Passed</td>
							<td><h2>'.$passedtime.'</h2></td>
							<td>Runtime</td><td><h2>'.$totaltime.'</h2></td>
						</tr>
						<tr>
							<td>Remaining</td>
							<td><h2>'.date('G:i:s', (strtotime($totaltime)-strtotime($passedtime)-3600)).'</h2></td>
							<td>End at</td>
							<td><h2>'.date('G:i:s', (TIME+strtotime($totaltime)-strtotime($passedtime))).'</h2></td>
						</tr>
					</table>
				</div>';
			}
			echo '
				<br>
				<div class="controls">';
			echo $prop['speed']==1
			 ?'
					<input type="submit" name="PauseKodi" value="Playing" class="btn b2" onclick="exec(\'PauseKodi\',\'Playing\');"/>'
			 :'
					<input type="submit" name="PauseKodi" value="Paused" class="btn b2" onclick="exec(\'PauseKodi\',\'Paused\');"/>';
			echo '
					<input type="submit" name="StopKodi" value="STOP" class="btn b2" onclick="exec(\'StopKodi\',\'STOP\');"/>';
			if ($prop['speed']==1) {
				echo '
					<br>
					<input type="submit" name="bigbackward" value="<<" class="btn b4" onclick="exec(\'bigbackward\',\'<<\');"/>
					<input type="submit" name="smallbackward" value="<" class="btn b4" onclick="exec(\'smallbackward\',\'<\');"/>
					<input type="submit" name="smallforward" value=">" class="btn b4" onclick="exec(\'smallforward\',\'>\');"/>
					<input type="submit" name="bigforward" value=">>" class="btn b4" onclick="exec(\'bigforward\',\'>>\');"/>';
			}
			echo '
				</div>';
			echo '
				<br><div class="audios">';
			$stream=0;
			foreach ($prop['audiostreams'] as $audio) {
				echo $audio['index']===$prop['currentaudiostream']['index']
				?'
					<button type="submit" name="audio" value="'.$audio['index'].'" class="btn btna b1" onclick="exec(\'audio\',\''.$audio['index'].'\');">'.$audio['name'].'</button>'
				:'
					<button type="submit" name="audio" value="'.$audio['index'].'" class="btn b1" onclick="exec(\'audio\',\''.$audio['index'].'\');">'.$audio['name'].'</button>';
				$stream=$stream + 1;
			}
			echo '
				</div>
				<br>
				<div class="subs">';
			foreach ($prop['subtitles'] as $subtitle) {
				echo $subtitle['index']===$prop['currentsubtitle']['index']
				?'
					<button type="submit" name="subtitle" value="'.$subtitle['index'].'" class="btn btna b1" onclick="exec(\'subtitle\',\''.$subtitle['index'].'\');">'.langu($subtitle['language']).' '.$subtitle['name'].'</button>'
				:'
					<button type="submit" name="subtitle" value="'.$subtitle['index'].'" class="btn b1" onclick="exec(\'subtitle\',\''.$subtitle['index'].'\');">'.langu($subtitle['language']).' '.$subtitle['name'].'</button>';
			}
			echo '
					<br>
					<button type="submit" name="subtitle" value="enable" class="btn b2" onclick="exec(\'subtitle\',\'enable\');">Enable</button>
					<button type="submit" name="subtitle" value="disable" class="btn b2" onclick="exec(\'subtitle\',\'disable\');">Disable</button>';
		} else {
			echo '
				</div>
			</div>
			<div class="box audios red">
				No Audio
			</div>
			<div class="box subs"></div>';
		}
		echo '
		 </div>';
	} else echo '<br><br><br><br><br>';
} else {
	echo 'aaa
		<br><br></div><br><br>bbb';
}

echo '
				<br>
				<div>
					<form action="kodicontrol.php">
					<input type="submit" name="kodicontrol" value="kodicontrol" class="btn big b1"/><br>
					</form>
				</div>
		</div>
	</body>
</html>';
function langu($lang) {
	switch($lang){
	case 'dut': $taal='&nbsp;NL&nbsp;';
		break;
	case 'eng': $taal='&nbsp;EN&nbsp;';
		break;
	case 'fre': $taal='&nbsp;FR&nbsp;';
		break;
	case '': $taal='N/A';
		break;
	default: $taal=$lang;
	}
	return $taal;
}
