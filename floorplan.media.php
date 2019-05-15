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
$start=microtime(true);
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home) {
    session_start();
    $_SESSION['referer']='floorplan.media.php';
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
		<script type=\'text/javascript\'>
            $(document).ready(function() {
                ajax();
                setInterval(ajax, '.($local===true?'2950':'9950').');
            });
            function navigator_Go(url) {window.location.assign(url);}
            function ajax() {
                $.ajax({
                    url: \'/ajaxfloorplan.media.php\',
                    success: function(data) {
                        $(\'#ajax\').html(data);
                    },
                });
            }
        </script>
	</head>';
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
        usleep(100000);
        header("Location: floorplan.media.php");
        die("Redirecting to: floorplan.media.php");
    } elseif (isset($_POST['PowerOn'])) {
        $items=array('tv','denon','nvidia');
        foreach ($items as $item) {
            if ($d[$item]['s']!='On') {
                sw($item, 'On');
            }
        }
    } elseif (isset($_POST['dimmer'])) {
        if (isset($_POST['dimlevelon_x'])) {
            sl($_POST['Naam'], 100);
        } elseif (isset($_POST['dimleveloff_x'])) {
            sl($_POST['Naam'], 0);
        } else {
            sl($_POST['Naam'], $_POST['dimlevel']);
            store('dimaction'.$_POST['Naam'], 0);
        }
        usleep(100000);
        header("Location: floorplan.media.php");
        die("Redirecting to: floorplan.media.php");
    } elseif (isset($_REQUEST['nas'])) {
        if ($_REQUEST['nas']=='sleep') {
            shell_exec('secure/sleepnas.sh');
        } elseif ($_REQUEST['nas']=='wake') {
            shell_exec('secure/wakenas.sh');
        }
        header("Location: floorplan.media.php");
        die("Redirecting to: floorplan.media.php");
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
        usleep(100000);
        header("Location: floorplan.media.php");
        die("Redirecting to: floorplan.media.php");
    } elseif (isset($_POST['vol'])) {
        @file_get_contents('http://'.$denonip.'/MainZone/index.put.asp?cmd0=PutMasterVolumeSet/-'.number_format($_POST['vol'], 0).'.0');
        usleep(120000);
    } elseif (isset($_POST['input'])) {
        @file_get_contents('http://'.$denonip.'/MainZone/index.put.asp?cmd0=PutZone_InputFunction/'.$_POST['input'].'&cmd1=aspMainZone_WebUpdateStatus%2F');
        storemode('denon', $_POST['input']);
        usleep(120000);
    } elseif (isset($_POST['surround'])) {
        @file_get_contents('http://'.$denonip.'/MainZone/index.put.asp?cmd0=PutSurroundMode/'.$_POST['surround'].'&cmd1=aspMainZone_WebUpdateStatus%2F');
        usleep(120000);
    } elseif (isset($_POST['UpdateKodi'])) {
        $profile=$_POST['UpdateKodi'];
        //echo 'Wanted profile='.$profile.'<br>';
        profile:
        $loadedprofile=json_decode(@file_get_contents($kodiurl.'/jsonrpc?request={"jsonrpc":"2.0","id":"1","method":"Profiles.GetCurrentProfile","id":1}', false, $ctx), true);
        //echo 'loadedprofile='.$loadedprofile['result']['label'].'<br>';
        if ($loadedprofile['result']['label']!==$profile) {
            @kodi('{"jsonrpc":"2.0","id":1,"method":"Player.Stop","params":{"playerid":1}}');
            usleep(10000);
            $profilereply=@kodi('{"jsonrpc":"2.0","id":"1","method":"Profiles.LoadProfile","params":{"profile":"'.$profile.'"},"id":1}');
            //echo 'profilereply='.$profilereply.'</pre><br>';
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
    } elseif (isset($_REQUEST['setdimmer'])) {
        $name=$_REQUEST['setdimmer'];
        $stat=$d[$name]['s'];
        $dimaction=$d[$name]['m'];
        echo '<div id="D'.$name.'" class="fix dimmer" >
		<form method="POST" action="floorplan.media.php" oninput="level.value = dimlevel.valueAsNumber">
				<div class="fix z" style="top:15px;left:90px;">';
        if ($stat=='Off') {
            echo '<h2>'.ucwords($name).': Off</h2>';
        } else {
            echo '<h2>'.ucwords($name).': '.$stat.'%</h2>';
        }
        echo '
					<input type="hidden" name="Naam" value="'.$name.'">
					<input type="hidden" name="dimmer" value="true">
				</div>
				<div class="fix z" style="top:100px;left:30px;">
					<input type="image" name="dimleveloff" value ="0" src="images/Light_Off.png" class="i90">
				</div>
				<div class="fix z" style="top:100px;left:150px;">
					<input type="image" name="dimsleep" value ="100" src="images/Sleepy.png" class="i90">';
        if ($dimaction==1) {
            echo '<div class="fix" style="top:0px;left:0px;z-index:-100;background:#ffba00;width:90px;height:90px;border-radius:45px;"></div>';
        }
        echo '
				</div>
				<div class="fix z" style="top:100px;left:265px;">
					<input type="image" name="dimwake" value="100" src="images/Wakeup.png" style="height:90px;width:90px">';
        if ($dimaction==2) {
            echo '<div class="fix" style="top:0px;left:0px;z-index:-100;background: #ffba00;width:90px;height:90px;border-radius:45px;"></div>';
        }
        echo '
					<input type="hidden" name="dimwakelevel" value="'.$stat.'">
				</div>';
        echo '
				<div class="fix z" style="top:100px;left:385px;">
					<input type="image" name="dimlevelon" value ="100" src="images/Light_On.png" class="i90">
				</div>
				<div class="fix z" style="top:210px;left:10px;">';

        $levels=array(1,2,3,4,5,6,7,8,9,10,12,14,16,18,20,22,24,26,28,30,32,35,40,45,50,55,60,65,70,75,80,85,90,95,100);
        if ($stat!=0&&$stat!=100) {
            if (!in_array($stat, $levels)) {
                $levels[]=$stat;
            }
        }
        asort($levels);
        $levels=array_slice($levels, 0, 35);
        foreach ($levels as $level) {
            if ($stat!='Off'&&$stat==$level) {
                echo '<input type="submit" name="dimlevel" value="'.$level.'"/ class="dimlevel dimlevela">';
            } else {
                echo '<input type="submit" name="dimlevel" value="'.$level.'" class="dimlevel">';
            }
        }
        echo '
				</div>
			</form>
			<div class="fix z" style="top:5px;left:5px;">
			    <a href=\'javascript:navigator_Go("floorplan.media.php");\'>
			        <img src="/images/close.png" width="72px" height="72px" alt="">
			    </a>
			</div>
		</div>
	</body>
	<script type="text/javascript">function navigator_Go(url){window.location.assign(url);}</script>
</html>';
        exit;
    }

    echo '
    <body class="floorplan">
        <div id="ajax"></div>';
}

//else{header("Location: index.php");die("Redirecting to: index.php");}
?>
</body></html>