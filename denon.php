<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home) {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="HandheldFriendly" content="true" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="viewport" content="width=device-width,height=device-width, initial-scale=0.5, user-scalable=yes, minimal-ui" />
	<title>Denon</title>
	<link rel="icon" type="image/png" href="images/denon.png">
	<link rel="shortcut icon" href="images/denon.png" />
	<link rel="apple-touch-icon" href="images/denon.png"/>
	<link rel="icon" sizes="196x196" href="images/denon.png">
	<link rel="icon" sizes="192x192" href="images/denon.png">
	<meta name="mobile-web-app-capable" content="yes">
	<link rel="manifest" href="/manifests/denon.json">
	<link href="images/denon.png" media="(device-width: 320px)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 320px) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2)"	rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: portrait) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	<link href="images/denon.png" media="(device-width: 768px) and (orientation: landscape) and (-webkit-device-pixel-ratio: 2)" rel="apple-touch-startup-image">
	<script type="text/javascript">setTimeout(\'window.location.href=window.location.href;\', 9950);</script>
	<link href="/styles/denon.css" rel="stylesheet" type="text/css"/>
  </head>
<body>';
	if (isset($_POST['vol'])) {
		@file_get_contents('http://192.168.2.6/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.number_format($_POST['vol'], 0).'.0');usleep(120000);
	} elseif (isset($_POST['input'])) {
		storemode('denon', $_POST['input'], basename(__FILE__).':'.__LINE__);
		@file_get_contents('http://192.168.2.6/MainZone/index.put.asp?cmd0=PutZone_InputFunction/'.$_POST['input'].'&cmd1=aspMainZone_WebUpdateStatus%2F');
		usleep(120000);
	} elseif (isset($_POST['surround'])) {
		@file_get_contents('http://192.168.2.6/MainZone/index.put.asp?cmd0=PutSurroundMode/'.$_POST['surround'].'&cmd1=aspMainZone_WebUpdateStatus%2F');usleep(120000);
	} elseif (isset($_POST['poweron'])) {
		$d=fetchdata();
		if ($d['denon']['s']=='On') {
			denon('PWON');
		} else {
			sw('denon', 'On',basename(__FILE__).':'.__LINE__);
		}
	} elseif (isset($_POST['delay'])) {
		$x=str_pad($_POST['delay'], 3, 0, STR_PAD_LEFT);
		denon('PSDELAY '.$x);
	}
	$ctx=stream_context_create(array('http'=>array('timeout' =>2)));
	$denonmain=@json_decode(@json_encode(@simplexml_load_string(@file_get_contents('http://192.168.2.6/goform/formMainZone_MainZoneXml.xml?_='.TIME, false, $ctx))), true);
	if (!$denonmain) {
		echo '<div class="error">Kon geen verbinding maken met Denon.<br/>Geen real-time info beschikbaar</div>';
	}
	echo '<form action="/floorplan.php"><input type="submit" class="btn b5" value="Plan"/></form>
	<form action="/denon.php"><input type="submit" class="btn btna b5" value="Denon"/></form>
	<form action="'.$urlfilms.'/films.php"><input type="submit" class="btn b5" value="Films"/></form>
	<form action="'.$urlfilms.'/series.php"><input type="submit" class="btn b5" value="Series"/></form>
</div>
		<div class="content">
			<form method="POST">
					<div class="box">';
			$currentvolume=80+$denonmain['MasterVolume']['value'];
	if ($currentvolume==80) {
		$currentvolume=0;
	}
	if ($denonmain['ZonePower']['value']=='ON') {
			$levels=array(10,12,14,16,18,20,22,24,26,28,30,31,32,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,54,56,58);
		if (!in_array($currentvolume, $levels)) {
			$levels[]=$currentvolume;
		}
		asort($levels);
		$levels=array_slice($levels, 0, 36);
		foreach ($levels as $k) {
			$setvalue = 80-$k;
			$showvalue = $k;
			if ($showvalue == 80) {
				$showvalue = 0;
			}
			if ($k==$currentvolume) {
				echo '<button name="vol" value="'.$setvalue.'" type="submit" class="btn volume btna">'.$showvalue.'</button>';
			} else {
				echo '<button name="vol" value="'.$setvalue.'" type="submit" class="btn volume">'.$showvalue.'</button>';
			}
		}
		echo '</div>
<div class="box">';
		$inp=$denonmain['InputFuncSelect']['value'];
		$inputs=array('TUNER','TV','KODI','CAST');
		if (!in_array($inp, $inputs)) {
			$inputs[]=$inp;
		}

		foreach ($inputs as $input) {
			switch($input){
			case 'TUNER':$txt='TUNER';
				break;
			case 'TV':$txt='SAT/CBL';
				break;
			case 'KODI':$txt='DVD';
				break;
			case 'CAST':$txt='GAME';
				break;
			default:$txt=$input;
			}
			if ($inp==$input) {
				echo '<button name="input" value="'.$txt.'" class="btn b3 btna">'.$input.'</button>';
			} else {
				echo '<button name="input" value="'.$txt.'" class="btn b3">'.$input.'</button>';
			}
		}
		echo '</div>
<div class="box">';
		$sur=trim($denonmain['selectSurround']['value']);
		$surrounds=array('MOVIE','MUSIC','PURE DIRECT','DOLBY DIGITAL','DTS SURROUND');
		if (!in_array($sur, $surrounds)) {
			echo '<button name="action" value="MainZone/index.put.asp?cmd0=PutSurroundMode/'.$sur.'" class="btn b3 btna">'.$sur.'</button>';
		}
		foreach ($surrounds as $surround) {
			if ($sur==$surround) {
				echo '<button name="surround" value="'.$surround.'" class="btn b3 btna">'.$surround.'</button>';
			} else {
				echo '<button name="surround" value="'.$surround.'" class="btn b3">'.$surround.'</button>';
			}
		}
		echo '
</div>
<div class="box">';
		$delay=file_get_contents('http://192.168.2.6/SETUP/AUDIO/AUDIODELAY/d_audio.asp');
		$delay=strafter($delay, "style='text-align:right;' value='");
		$delay=strbefore($delay, "'>");
		for ($x=0;$x<=200;$x++) {
			if ($x==$delay) echo '
		<button name="delay" value="'.$x.'" class="btn btna delay">'.$x.'</button>';
			else echo '
		<button name="delay" value="'.$x.'" class="btn delay">'.$x.'</button>';
		}
		echo '
</form>
<div class="box">
	<form action="/denonsetup.php"><input type="submit" class="btn b1" value="Setup"/></form>
</div>
</div>
</div>';
	} else {
		echo '<button name="poweron" value="poweron" class="btn b1">Power On</button>';
	}
	echo '</div></div></div></body></html>';
}
