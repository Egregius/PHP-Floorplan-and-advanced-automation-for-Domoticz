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
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home) {
    $d=fetchdata();
    createheader('floorplanmedia');
    $ctx=stream_context_create(array('http'=>array('timeout'=>2)));
	handlerequest();
    if (isset($_POST['Naam'])&&!isset($_POST['dimmer'])) {
        if ($_POST['Naam']=='lgtv') {
            if ($_POST['Actie']=='On') {
                shell_exec('python3 secure/lgtv.py -c on -a '.$lgtvmac.' '.$lgtvip);
            } else {
                shell_exec('python3 secure/lgtv.py -c off '.$lgtvip);
            }
        } else {
            sw($_POST['Naam'], $_POST['Actie']);
        }
    } elseif (isset($_POST['PowerOn'])) {
        $items=array('tv','denon'/*,'nvidia'*/);
        foreach ($items as $item) {
            if ($d[$item]['s']!='On') {
                sw($item, 'On');
            }
        }
    } elseif (isset($_REQUEST['nas'])) {
        if ($_REQUEST['nas']=='sleep') {
            shell_exec('secure/sleepnas.sh');
        } elseif ($_REQUEST['nas']=='wake') {
            shell_exec('secure/wakenas.sh');
        }
    } elseif (isset($_POST['Scene'])) {
        if ($_POST['Scene']=='TUNER') {
            ud('miniliving1s', 0, 'On');
        } elseif ($_POST['Scene']=='TV') {
            ud('miniliving1l', 0, 'On');
        } elseif ($_POST['Scene']=='SHIELD') {
            ud('miniliving2l', 0, 'On');
        } elseif ($_POST['Scene']=='UIT') {
            if ($d['nvidia']['s']!='Off') {
                   @kodi('{"jsonrpc":"2.0","id":1,"method":"System.Shutdown"}');
            }
            ud('miniliving4l', 0, 'On');
        }
    } elseif (isset($_POST['input'])) {
        @file_get_contents('http://'.$denonip.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/'.$_POST['input'].'&cmd1=aspMainZone_WebUpdateStatus%2F');
        storemode('denon', $_POST['input']);
        usleep(120000);
    } elseif (isset($_POST['surround'])) {
        @file_get_contents('http://'.$denonip.'/MainZone/index.put.asp?cmd0=PutSurroundMode/'.$_POST['surround'].'&cmd1=aspMainZone_WebUpdateStatus%2F');
        usleep(120000);
    } elseif (isset($_POST['UpdateKodi'])) {
        $profile=$_POST['UpdateKodi'];
        profile:
        $loadedprofile=json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.GetCurrentProfile","id":1}', false, $ctx), true);
        if ($loadedprofile['result']['label']!==$profile) {
            @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Stop","params":{"playerid":1}}');
            usleep(10000);
            $profilereply=@kodi('{"jsonrpc":"2.0","id":"1","method":"Profiles.LoadProfile","params":{"profile":"'.$profile.'"},"id":1}');
            $count=$count + 1;
            if ($count>10) {
                die('Die Endless loop');
            }
            sleep(3);
            goto profile;
        } else {
            @kodi('{"jsonrpc":"2.0","id":1,"method":"Videolibrary.Scan"}');
        }
    } elseif (isset($_POST['CleanKodi'])) {
        @kodi('{"jsonrpc":"2.0","id":1,"method":"Videolibrary.Clean"}');
    } elseif (isset($_POST['PauseKodi'])) {
        @file_get_contents($domoticzurl.'/json.htm?type=command&param=udevice&idx='.idx('miniliving2s').'&nvalue=0&svalue=On');
    } elseif (isset($_POST['StopKodi'])) {
        @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Stop","params":{"playerid":1}}');
    } elseif (isset($_POST['bigbackward'])) {
        @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":"bigbackward"}}');
    } elseif (isset($_POST['smallbackward'])) {
        @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":"smallbackward"}}');
    } elseif (isset($_POST['smallforward'])) {
        @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":"smallforward"}}');
    } elseif (isset($_POST['bigforward'])) {
        @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Seek","params":{"playerid":1,"value":"bigforward"}}');
    } elseif (isset($_POST['audio'])) {
        @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.SetAudioStream","params":{"playerid":1,"stream":'.$_POST['audio'].'}}');
    } elseif (isset($_POST['subtitle'])) {
        if ($_POST['subtitle']=='disable') {
            @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.SetSubtitle","params":{"playerid":1,"subtitle":"off"}}');
        } elseif ($_POST['subtitle']=='enable') {
            @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.SetSubtitle","params":{"playerid":1,"subtitle":"on"}}');
        } else {
            @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.SetSubtitle","params":{"playerid":1,"subtitle":'.$_POST['subtitle'].'}}');
        }
    } elseif (isset($_POST['PowerOff'])) {
        @kodi('{"jsonrpc":"2.0","id":1,"method":"System.Shutdown"}');
    } 

    echo '
    <body class="floorplan">
        <div id="placeholder"></div>';
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
        </div>
    </div>';

    
    if ($d['lgtv']['s']=='On') {
        $lgsource=trim(shell_exec('python3 secure/lgtv.py -c get-input '.$lgtvip));
        if ($lgsource=='com.webos.app.hdmi2') {
            $current=@json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","method":"Player.GetItem","params":{"properties":["title","album","artist","season","episode","duration","showtitle","tvshowid","thumbnail","file","imdbnumber"],"playerid":1},"id":"VideoGetItem"}', false, $ctx), true);
            if (isset($current['result']['item']['file'])) {
                if (!empty($current['result']['item']['file'])) {
                       $item=$current['result']['item'];
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
    echo '
                </div>
            </div>
            </form>
        </div>';
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
?>
    </body>
</html>