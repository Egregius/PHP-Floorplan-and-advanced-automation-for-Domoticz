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
    session_start();
    if (!isset($_SESSION['referer'])) {
        $_SESSION['referer']='floorplan.php';
    }
    if (isset($_REQUEST['ip'])) {
        $bose=$_REQUEST['ip'];
    } else {
        $bose=3;//Living
    }
    echo '<html><head>
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
	    <meta name="msapplication-TileColor" content="#000000">
		<meta name="msapplication-TileImage" content="images/domoticzphp48.png">
		<meta name="theme-color" content="#000000">
		<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-startup-image" href="images/domoticzphp450.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php">
		<style>
			.btn{height:60px;}
			.input{width:78px;}
			.blackmedia{top:50px;left:0px;height:581px;width:490px;background-color:#000;text-align:center;}
		</style>
	</head>';

    if (isset($_REQUEST['power'])) {
        bosekey("POWER", 0, $bose);
        sw('bose'.$bose);
    } elseif (isset($_REQUEST['prev'])) {
        bosekey("PREV_TRACK", 0, $bose);
    } elseif (isset($_REQUEST['next'])) {
        bosekey("NEXT_TRACK", 0, $bose);
    } elseif (isset($_REQUEST['preset'])) {
        bosepreset($_REQUEST['preset'], $bose);
    } elseif (isset($_REQUEST['volume'])) {
        bosevolume($_REQUEST['volume'], $bose);
    } elseif (isset($_REQUEST['bass'])) {
        bosebass($_REQUEST['bass'], $bose);
    } elseif (isset($_REQUEST['stopbadkamer'])) {
        bosezone();
    } elseif (isset($_REQUEST['stopbuiten'])) {
        bosezone();
    } elseif (isset($_REQUEST['addbadkamer'])) {
        bosezone();
    } elseif (isset($_REQUEST['addbuiten'])) {
        bosezone();
    }



    $ctx=stream_context_create(array('http'=>array('timeout' =>2)));
    echo '<body>
	<div class="fix clock"><a href=\'javascript:navigator_Go("floorplan.bose'.$bose.'.php");\'>'.strftime("%k:%M:%S", TIME).'</a></div>
	<div class="fix z1" style="top:5px;left:5px;"><a href=\'javascript:navigator_Go("'.$_SESSION['referer'].'");\'><img src="/images/close.png" width="72px" height="72px"/></a></div>

	';

    $nowplaying=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/now_playing"))), true);
    if (!empty($nowplaying)) {
        if (isset($nowplaying['@attributes']['source'])) {
            echo '<div class="fix blackmedia" >
					<form method="POST">';
            if ($nowplaying['@attributes']['source']=='STANDBY') {
                  echo '<h3>STANDBY</h3>';
                  echo '<button type="submit" name="power" value="power" class="btn b1">Power</button>';
            } else {

                  $volume=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/volume"))), true);
                  $bass=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/bass"))), true);
                  //echo '<div style="text-align:left;"><pre>';print_r($bass);echo '</pre></div>';
                  $bass=$bass['actualbass'];
                  $cv=$volume['actualvolume'];
                  $levels=array($cv-10,$cv-7,$cv-4,$cv-2,$cv-1,$cv,$cv+1,$cv+2,$cv+4,$cv+7);
                foreach ($levels as $k) {
                    if ($k>=0&&$k<=80) {
                        if ($k==$cv) {
                            echo '<button type="submit" name="volume" value="'.$k.'" class="btn volume btna">'.$k.'</button>';
                        } else {
                            echo '<button type="submit" name="volume" value="'.$k.'" class="btn volume">'.$k.'</button>';
                        }
                    }
                }
                echo '<br>';
                $levels=array(-9,-8,-7,-6,-5,-4,-3,-2,-1,0);
                foreach ($levels as $k) {
                    if ($k==$bass) {
                        echo '<button type="submit" name="bass" value="'.$k.'" class="btn volume btna">'.$k.'</button>';
                    } else {
                        echo '<button type="submit" name="bass" value="'.$k.'" class="btn volume">'.$k.'</button>';
                    }
                }

                if ($nowplaying['@attributes']['source']=='SPOTIFY') {
                    echo '<h4>Spotify</h4>';
                    if (isset($nowplaying['artist'])&&!is_array($nowplaying['artist'])) {
                        echo '<h4>'.$nowplaying['artist'].'<br>';
                    }
                    if (isset($nowplaying['track'])&&!is_array($nowplaying['track'])) {
                        echo $nowplaying['track'];
                    }
                    echo '</h4>';
                    if (isset($nowplaying['art'])&&!is_array($nowplaying['art'])) {
                        echo '
					<img src="'.str_replace('http://', 'https://', $nowplaying['art']).'" height="160px" width="auto"/><br><br>
					<button type="submit" name="prev" class="btn b2">Prev</button>
					<button type="submit" name="next" class="btn b2">Next</button>
					';
                    }
                } elseif ($nowplaying['@attributes']['source']=='TUNEIN') {
                    echo '<h4>Internet Radio</h4>';
                    echo '<h4>'.$nowplaying['stationName'].'</h4>';
                    echo $nowplaying['artist'];
                    echo '<br><img src="'.str_replace('http://', 'https://', $nowplaying['art']).'" height="160px" width="auto"/><br><br>';
                } else {
                    echo '<h3>'.$nowplaying['@attributes']['source'].'</h3>';
                }

                //echo '<div style="text-align:left;"><pre>';print_r($nowplaying);echo '</pre></div>';

                $presets=json_decode(json_encode(simplexml_load_string(file_get_contents("http://192.168.2.$bose:8090/presets"))), true);
                foreach ($presets as $i) {
                            $x=1;
                    foreach ($i as $j) {
                        //print_r($j);
                        echo '<button type="submit" name="preset" class="btn b2" value="'.$j['@attributes']['id'].'">'.$j['@attributes']['id'].'. '.str_replace(', selected by Egregius', '', $j['ContentItem']['itemName']).'</button>';
                        if ($x%2==0) {
                            echo '<br>';
                        }
                        $x++;
                    }
                }
                echo '<br><br><button type="submit" name="power" value="power" class="btn b1">Power</button>';
            }
            echo '
					</form>
				</div>';
        }
    }
    echo '<script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
			setTimeout("window.location.href=window.location.href;",10000);
		</script>';
}

function setShuffle()
{
    keyPress("SHUFFLE_ON");
}
function setNextTrack()
{
    keyPress("NEXT_TRACK");
}
function setStop()
{
    keyPress("STOP");
}
?>
</body></html>