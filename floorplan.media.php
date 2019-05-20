<?php
/**
 * Pass2PHP
 * php version 7.3.5-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
require 'secure/functionsfloorplan.php';
require 'secure/authentication.php';
if ($home) {
    $d=fetchdata();
    $ctx=stream_context_create(array('http'=>array('timeout'=>2)));
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
		<title>Media</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<meta name="HandheldFriendly" content="true">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">';
	if ($udevice=='iPhone') {
	    echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=0.655,user-scalable=yes,minimal-ui">';
	} elseif ($udevice=='iPad') {
	    echo '
		<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.2,user-scalable=yes,minimal-ui">';
	}
	echo '
		<link rel="icon" type="image/png" href="images/media.png">
		<link rel="shortcut icon" href="images/media.png">
		<link rel="apple-touch-startup-image" href="images/media.png">
		<link rel="apple-touch-icon" href="images/media.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php?v=5">
		<style type="text/css">
			.btn{height:64px;}
			.input{width:78px;}
		</style>
		<script type="text/javascript" src="/scripts/jQuery.js"></script>
		<script type="text/javascript" src="/scripts/floorplan.js?v='.$floorplanjs.'"></script>
		<script type=\'text/javascript\'>
            $(document).ready(function() {
                ajax();
                setInterval(ajax, '.($local===true?'300':'1500').');
            });
        </script>';
    echo '
	</head>';
	floorplanactions();
    echo '
    <body class="floorplan">';
    if ($d['denon']['s']=='On') {
        $denonmain=json_decode(json_encode(simplexml_load_string(@file_get_contents('http://'.$denonip.'/goform/formMainZone_MainZoneXml.xml?_='.time, false, $ctx))), true);
        if (!empty($denonmain)) {
            $denoninput=$denonmain['InputFuncSelect']['value'];
        } else {
            $denoninput='Not ready yet';
        }
    } else {
        $denoninput='UIT';
    }
    echo '
        <div class="fix clock">
	        <a href=\'javascript:navigator_Go("floorplan.media.php");\' id="clock">
	            '.strftime("%k:%M:%S", TIME).'
	        </a>
	    </div>
    	<div class="fix z1" style="top:5px;left:5px;">
    	    <a href=\'javascript:navigator_Go("floorplan.php");\'>
    	        <img src="/images/close.png" width="72px" height="72px" alt="">
    	    </a>
    	</div>
	    <div class="fix" style="top:100px;left:0px;">
		    <form method="POST" action="floorplan.media.php">';
    $inputs=array('TUNER','UIT');
    if (!in_array($denoninput, $inputs)&&$denoninput!='') {
        $inputs[]=$denoninput;
    }
    foreach ($inputs as $input) {
        if ($denoninput==$input) {
            echo '
                <input type="submit" name="Scene" value="'.$input.'" class="btn input btna">
                <br>
                <br>';
        } else {
            if ($input=='UIT') {
                echo '
                <input type="submit" name="Scene" value="UIT" class="btn input" onclick="return confirm(\'Are you sure?\');">
                <br>
                <br>';
            } else {
                echo '
                <input type="submit" name="Scene" value="'.$input.'" class="btn input">
                <br>
                <br>';
            }
        }
    }
    echo '
            </form>
            <br>
            <a href=\'javascript:navigator_Go("denon.php");\'>
                <img src="/images/denon.png" class="i48" alt="">
            </a>
            <br>
            <br>
            <br>
            <a href=\'javascript:navigator_Go("https://films.egregius.be/films.php");\'>
                <img src="/images/kodi.png" class="i48" alt="">
                <br>Films
            </a>
            <br>
            <br>
            <a href=\'javascript:navigator_Go("https://films.egregius.be/series.php");\'>
                <img src="/images/kodi.png" class="i48" alt="">
                <br>
                Series
            </a>
            <br>
            <br>
            <a href=\'javascript:navigator_Go("kodi.php");\'>
                <img src="/images/kodi.png" class="i48" alt="">
                <br>
                Kodi<br>
                Control
            </a>
            <br>
            <br>';
    echo '
	    </div>';
    dimmer('zithoek');
    dimmer('eettafel');
    schakelaar('jbl', 'Light');
    schakelaar('kristal', 'Light');
    bose(101);
    if (past('kristal')<$eendag) {
        echo '
        <div class="fix z0 right" style="top:55px;left:154px;width:35px;">
            '.strftime("%k:%M", $d['kristal']['t']).'
        </div>';
    }
    schakelaar('bureel', 'Light');
    if (past('bureel')<$eendag) {
        echo '
        <div class="fix z0 right" style="top:55px;left:213px;width:35px;">
            '.strftime("%k:%M", $d['bureel']['t']).'
        </div>';
    }
    schakelaar('keuken', 'Light');
    schakelaar('wasbak', 'Light');
    schakelaar('kookplaat', 'Light');
    schakelaar('werkblad1', 'Light');
    rollery('Rbureel');
    rollery('RkeukenL');
    rollery('RkeukenR');
    rollery('Rliving');
    if ($d['denonpower']['s']=='ON') {
        echo '
	<div class="fix denon z1">
			<input type="image" src="/images/denon_On.png" id="denon">
	</div>';
    } else {
        echo '
	<div class="fix denon z1">
			<input type="image" src="/images/denon_Off.png" id="denon">
	</div>';
    }
    if ($d['tv']['s']=='On') {
        schakelaar('lgtv');
    } else {
        schakelaar('tv');
    }
    if (past('lgtv')<$eendag) {
        echo '
        <div class="fix z0 right" style="top:116px;left:175px;width:35px;">
            '.strftime("%k:%M", $d['lgtv']['t']).'
        </div>';
    }
    if ($d['nvidia']['m']=='On') {
        echo '
	<div class="fix nvidia z1">
			<input type="image" src="/images/nvidia_On.png" id="nvidia">
	</div>';
    } else {
        echo '
	<div class="fix nvidia z1">
			<input type="image" src="/images/nvidia_Off.png" id="nvidia">
	</div>';
    }
    if ($d['nas']['s']=='On') {
        echo '
        <div class="fix nas z1">
            <a href=\'javascript:navigator_Go("?nas=sleep");\'>
                <img src="images/nas_On.png" class="i48" alt="">
            </a>
            <br>';
    } else {
        echo '
        <div class="fix nas z1">
            <a href=\'javascript:navigator_Go("?nas=wake");\'>
                <img src="images/nas_Off.png" class="i48" alt="">
            </a>
            <br>';
    }
    if (past('nas')<$eendag) {
        echo strftime("%H:%M", $d['nas']['t']);
    }
    echo '
        </div>
        <div class="fix blackmedia">
            <form method="POST" action="floorplan.media.php">';
    if ($d['denon']['s']=='Off'||$d['tv']['s']=='Off'||$d['nvidia']['s']=='Off') {
        echo '
                <br>
                <br>
                <button type="submit" class="btn b1" name="PowerOn">Power On</button>';
    }
    if ($d['denon']['s']=='On') {
        if (!empty($denonmain)) {
            $cv=80+$denonmain['MasterVolume']['value'];
            if ($cv==80) {
                $cv=0;
            }
            if ($denonmain['ZonePower']['value']=='ON') {
                $levels=array($cv-10,$cv-5,$cv-3,$cv-2,$cv-1,$cv,$cv+1,$cv+2,$cv+3,$cv+5,$cv+10);
                foreach ($levels as $k) {
                    $setvalue=80-$k;
                    $showvalue=$k;
                    if ($showvalue==80) {
                        $showvalue=0;
                    }
                    if ($showvalue>=0) {
                        if ($k==$cv) {
                            echo '
                <button type="submit" name="vol" value="'.$setvalue.'" class="btn volume btna">'.$showvalue.'</button>';
                        } else {
                            echo '
                <button type="submit" name="vol" value="'.$setvalue.'" class="btn volume">'.$showvalue.'</button>';
                        }
                    }
                }
            }
        }
    }
    //echo '<pre><div align="left">';print_r($_REQUEST);echo '</div></pre>';
    if ($d['lgtv']['s']=='On') {
        $lgsource=trim(shell_exec('python3 secure/lgtv.py -c get-input '.$lgtvip));
        if ($lgsource=='com.webos.app.hdmi2') {
            $current=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","method":"Player.GetItem","params":{"properties":["title","album","artist","season","episode","duration","showtitle","tvshowid","thumbnail","file","imdbnumber"],"playerid":1},"id":"VideoGetItem"}', false, $ctx), true);
            //print_r($current);
            if (isset($current['result']['item']['file'])) {
                if (!empty($current['result']['item']['file'])) {
                       $item=$current['result']['item'];
                       //print_r($item);
                    if ($item['episode']>0) {
                        echo '<h1>'.$item['showtitle'].' S '.$item['season'].' E '.$item['episode'].'</h1>';
                        echo '<h1>'.$item['label'].'</h1>';
                    } else {
                        echo '<a href="http://www.imdb.com/title/'.$item['imdbnumber'].'" style="color:#f5b324"><h1>'.$item['label'].'</h1></a>';
                    }
                    $properties=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","method":"Player.GetProperties","id":1,"params":{"playerid":1,"properties":["playlistid","speed","position","totaltime","time","audiostreams","currentaudiostream","subtitleenabled","subtitles","currentsubtitle"]}}', false, $ctx), true);
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
                        echo '<table align="center">
			  <tr>
				<td>Passed</td>
				<td>'.$passedtime.'</h2></td><td>Runtime</td><td>'.$totaltime.'</td>
			  </tr>
			  <tr>
				<td>Remaining</td>
				<td>'.strftime("%k:%M:%S", (strtotime($totaltime)-strtotime($passedtime)-3600)).'</td>
				<td>End at</td>
				<td>'.strftime("%k:%M:%S", (TIME+strtotime($totaltime)-strtotime($passedtime))).'</td>
			  </tr>
			  </table>';
                        echo $prop['speed']==1
                        ?'<input type="submit" name="PauseKodi" value="Playing" class="btn b2">'
                        :'<input type="submit" name="PauseKodi" value="Paused" class="btn b2">';
                        echo '        <input type="submit" name="StopKodi" value="STOP" class="btn b2">
			  ';
                        if ($prop['speed']==1) {
                            echo '<br>
				<input type="submit" name="bigbackward" value="<<" class="btn b4">
				<input type="submit" name="smallbackward" value="<" class="btn b4">
				<input type="submit" name="smallforward" value=">" class="btn b4">
				<input type="submit" name="bigforward" value=">>" class="btn b4">
				';
                        }
                        $stream=0;
                        foreach ($prop['audiostreams'] as $audio) {
                            echo $audio['index']===$prop['currentaudiostream']['index']
                            ?'<button type="submit" name="audio" value="'.$audio['index'].'" class="btn btna b3">'.$audio['name'].'</button>'
                            :'<button type="submit" name="audio" value="'.$audio['index'].'" class="btn b3">'.$audio['name'].'</button>';
                            $stream=$stream + 1;
                        }
                        echo '<br>';
                        foreach ($prop['subtitles'] as $subtitle) {
                            echo $subtitle['index']===$prop['currentsubtitle']['index']
                            ?'<button type="submit" name="subtitle" value="'.$subtitle['index'].'" class="btn btna b3">'.lang($subtitle['language']).' '.$subtitle['name'].'</button>'
                            :'<button type="submit" name="subtitle" value="'.$subtitle['index'].'" class="btn b3">'.lang($subtitle['language']).' '.$subtitle['name'].'</button>';
                        }
                        echo '<br><button type="submit" name="subtitle" value="enable" class="btn b2">Enable</button><button type="submit" name="subtitle" value="disable" class="btn b2">Disable</button>';
                    }
                    echo '</div>';


                } else {
                    echo '<div class="box">Update Library:<br>
				  <input type="submit" name="UpdateKodi" value="Wij" class="btn b3">
				  <input type="submit" name="UpdateKodi" value="Tobi" class="btn b3">
				  <input type="submit" name="UpdateKodi" value="Alex" class="btn b3"><br><br>
				  <br>
				  <input type="submit" name="PowerOff" value="Power Off" class="btn b2" onclick="return confirm(\'Are you sure?\');">
				  <input type="submit" name="PowerOn" value="Power On" class="btn b2"><br>
				</div>';
                }
            }
        }
    }
    $pfsense=json_decode(@file_get_contents('http://192.168.2.254:44300/egregius.php'), true);
    echo '
                </div>
            </div>
            </form>
        </div>
        <div class="fix floorplanstats">
            '.$udevice.' | '.$ipaddress.' | Up:'.human_kb(round($pfsense['up']), 0).' | Down:'.human_kb(round($pfsense['down']), 0).'
        </div>';
}
function human_filesize($bytes,$dec=2)
{
    $size=array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor=floor((strlen($bytes)-1)/3);
    return sprintf("%.{$dec}f", $bytes/pow(1024, $factor)).@$size[$factor];
}
function human_kb($bytes,$dec=2)
{
    $size=array('kb','Mb','Gb');
    $factor=floor((strlen($bytes)-1)/3);
    return sprintf("%.{$dec}f", $bytes/pow(1000, $factor)).@$size[$factor];
}
function lang($lang)
{
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
//else{header("Location: index.php");die("Redirecting to: index.php");}
?>
</body></html>