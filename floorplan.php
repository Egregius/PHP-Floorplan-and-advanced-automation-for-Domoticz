<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * This is the main floorplan.
 * It handles all the lighting and shows status of heating and rollers.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$fetch=true;
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home) {
    echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Floorplan</title>
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
	    <link rel="manifest" href="/manifest.json">
	    <link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php?v=3">
		<style type="text/css">
			.water{top:200px;left:218px;}
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
                    url: \'/ajaxfloorplan.php\',
                    success: function(data) {
                        $(\'#ajax\').html(data);
                    },
                });
            }
        </script>
	</head>';
    if (isset($_REQUEST['Weg'])) {
        if (isset($_REQUEST['Action'])) {
            store('Weg', $_REQUEST['Action']);

            if ($_REQUEST['Action']==0) {
                $db->query("UPDATE devices set t='0' WHERE n='heating';");
                if ($d['Weg']['s']!=1&&$d['poortrf']['s']=='Off') {
                    sw('poortrf', 'On');
                }
                lgsql($user, 'Weg', 'Thuis');
                resetsecurity();
            } elseif ($_REQUEST['Action']==1) {
                lgsql($user, 'Weg', 'Slapen');
                  huisslapen();
            } elseif ($_REQUEST['Action']==2) {
                lgsql($user, 'Weg', 'Weg');
                  huisweg();
            }
        } else {
            if ($d['raamliving']['s']=='Open'&&!isset($_REQUEST['continue'])) {
                echo '
	<body>
	    <div id="message" class="fix dimmer" >
			<br><br>
			<h2>Warning:</h2>
			<h2>Raam Living open!<h2>
			<br><br>
			<form action="floorplan.php" method="post">
				<input type="hidden" name="Weg" value="true">
				<input type="submit" name="continue" value="Toch doorgaan" class="btn" style="height:200px;width:100%;"><br>
				<input type="submit" name="cancel" value="Sluit" class="btn" style="height:200px;width:100%;">
			</form>
		</div>
	</body>
</html>';
                exit;
            }
            if ($d['achterdeur']['s']=='Open'&&!isset($_REQUEST['continue'])) {
                echo '
    <body>
        <div id="message" class="fix dimmer" >
            <br><br>
            <h2>Warning:</h2>
            <h2>Achterdeur open!<h2>
            <br><br>
            <form action="floorplan.php" method="post">
                <input type="hidden" name="Weg" value="true">
                <input type="submit" name="continue" value="Toch doorgaan" class="btn" style="height:200px;width:100%;"><br>
                <input type="submit" name="cancel" value="Sluit" class="btn" style="height:200px;width:100%;">
            </form>
        </div>
    </body>
</html>';
                exit;
            }
            if ($d['poort']['s']=='Open'&&!isset($_REQUEST['continue'])) {
                echo '
    <body>
        <div id="message" class="fix dimmer" >
            <br><br>
            <h2>Warning:</h2>
            <h2>Poort open!<h2>
            <br><br>
            <form action="floorplan.php" method="GET">
                <input type="hidden" name="Weg" value="true">
                <input type="submit" name="continue" value="Toch doorgaan" class="btn" style="height:200px;width:100%;"><br>
                <input type="submit" name="cancel" value="Sluit" class="btn" style="height:200px;width:100%;">
            </form>
        </div>
    </body>
</html>';
                exit;
            }
            echo '
    <body>
        <div id="message" class="fix confirm">
            <form action="floorplan.php" method="GET">
                <input type="hidden" name="Weg" value="true">
                <button name="Action" value="2" class="btn huge3">Weg</button>
                <button name="Action" value="1" class="btn huge3">Slapen</button>
                <button name="Action" value="0" class="btn huge3">Thuis</button>
            </form>
        </div>
    </body>
</html>';
            exit;
        }
    }
    if (isset($_REQUEST['Naam'])&&!isset($_REQUEST['dimmer'])) {
        if (in_array($_REQUEST['Naam'], array('bureeltobi','weg','slapen'))) {
            if (!isset($_REQUEST['confirm'])) {
                switch($_REQUEST['Naam']){
                case 'weg':$txtoff='Thuis';$txton='Weg';
                    break;
                case 'slapen':$txtoff='Wakker';$txton='Slapen';
                    break;
                case 'bureeltobi':$txtoff='Uit';$txton='Aan';
                    break;
                }
                    echo '<body><div id="message" class="fix confirm">
				<form method="post">
					<input type="hidden" name="Actie" value="On">
					<input type="hidden" name="Naam" value="'.$_REQUEST['Naam'].'">
					<input type="submit" name="confirm" value="'.$txton.'" class="btn huge2">
				</form>
				<form method="post">
					<input type="hidden" name="Actie" value="Off">
					<input type="hidden" name="Naam" value="'.$_REQUEST['Naam'].'">
					<input type="submit" name="confirm" value="'.$txtoff.'" class="btn huge2">
				</form>
			</div>
			</body>
		</html>';
                    exit;
            } elseif (isset($_REQUEST['confirm'])) {
                  sw($_REQUEST['Naam'], $_REQUEST['Actie']);
            }
        } elseif ($_REQUEST['Naam']=='zoldertrap') {

            if ($d['raamhall']['s']=='Closed') {
                sw($_REQUEST['Naam'], $_REQUEST['Actie']);
            } else {
                echo '<body><div id="message" class="fix confirm">
			<form method="post" action="floorplan.php">
					<input type="submit" name="confirm" value="RAAM OPEN!" class="btn huge2">
					<input type="submit" name="confirm" value="Annuleer" class="btn huge2">
				</form>
			</div>
			</body>
		</html>';
                exit;
            }
        } elseif (!in_array($_REQUEST['Naam'], array('radioluisteren','tvkijken','kodikijken'))) {
            sw($_REQUEST['Naam'], $_REQUEST['Actie']);
        }
    } elseif (isset($_REQUEST['dimmer'])) {
        if (isset($_REQUEST['luifelauto'])) {
            storemode('dimactionluifel', 1);
        } elseif (isset($_REQUEST['dimlevelon_x'])) {
            sl($_REQUEST['Naam'], 100);
            storemode($_REQUEST['Naam'], 0);
        } elseif (isset($_REQUEST['dimleveloff_x'])) {
            sl($_REQUEST['Naam'], 0);
            storemode($_REQUEST['Naam'], 0);
        } elseif (isset($_REQUEST['dimsleep_x'])) {
            lg('=> '.$user.' => activated dimmer sleep for '.$_REQUEST['Naam']);
            storemode($_REQUEST['Naam'], 1);
        } elseif (isset($_REQUEST['dimwake_x'])) {
            lg('=> '.$user.' => activated dimmer wake for '.$_REQUEST['Naam']);
            sl($_REQUEST['Naam'], $_REQUEST['dimwakelevel']+2);
            storemode($_REQUEST['Naam'], 2);
        } elseif (isset($_REQUEST['dimwake3u_x'])) {
            lg('=> '.$user.' => activated dimmer wake after 3 hours for '.$_REQUEST['Naam']);
            storemode($_REQUEST['Naam'], 3);
        } else {
            sl($_REQUEST['Naam'], $_REQUEST['dimlevel']);
            storemode($_REQUEST['Naam'], 0);
        }
    }
    if (isset($_REQUEST['setdimmer'])) {
        $name=$_REQUEST['setdimmer'];
        $stat=$d[$name]['s'];
        $dimaction=$d[$name]['m'];
        echo '<div id="D'.$name.'" class="fix dimmer" >
		<form method="POST" action="floorplan.php" oninput="level.value = dimlevel.valueAsNumber">
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
        if ($name=='alex') {
            echo '
					<div class="fix z" style="top:10px;left:265px;">
						<input type="image" name="dimwake3u" value="100" src="images/Wakeup.png" style="height:90px;width:90px">
						<div class="fix" style="top:39px;left:25px;font-size:3em;z-index:-10;" >3u</div>';
            if ($dimaction==3) {
                echo '
        <div class="fix" style="top:0px;left:0px;z-index:-100;background: #ffba00;width:90px;height:90px;border-radius:45px;"></div>';
                echo '
        <div class="fix" style="top:32px;left:95px;z-index:-100;font-size:2em;">'.strftime("%k:%M", $d['alex']['t']+10800).'</div>';
            }
            echo '
						<input type="hidden" name="dimwakelevel" value="'.$stat.'">
					</div>';
        }
        echo '
				<div class="fix z" style="top:100px;left:385px;">
					<input type="image" name="dimlevelon" value ="100" src="images/Light_On.png" class="i90">
				</div>
				<div class="fix z" style="top:210px;left:10px;">';

        if ($name=='lichtbadkamer') {
            $levels=array(18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48,50,52,54,56,58,60,62,64,66,68,70,72,74,76,78,80,82,84,86);
        } else {
            $levels=array(1,2,3,4,5,6,7,8,9,10,12,14,16,18,20,22,24,26,28,30,32,35,40,45,50,55,60,65,70,75,80,85,90,95,100);
        }
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
			    <a href=\'javascript:navigator_Go("floorplan.php");\'>
			        <img src="/images/close.png" width="72px" height="72px">
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
	    ';
     if ($d['Rliving']['s']==100
        &&$d['Rbureel']['s']==100
        &&$d['RkeukenL']['s']==100
        &&$d['RkeukenR']['s']==100
        &&$d['Rtobi']['s']==100
        &&$d['Ralex']['s']==100
        &&$d['RkamerL']['s']==100
        &&$d['RkamerR']['s']==100
    ) {
        $Rup='arrowgreendown';
    } elseif ($d['Rliving']['s']==0
        &&$d['Rbureel']['s']==0
        &&$d['RkeukenL']['s']==0
        &&$d['RkeukenR']['s']==0
        &&$d['Rtobi']['s']==0
        &&$d['Ralex']['s']==0
        &&$d['RkamerL']['s']==0
        &&$d['RkamerR']['s']==0
    ) {
        $Rup='arrowgreenup';
    } else {
        $Rup='arrowup';
    }
    echo '
	    <div class="fix leftbuttons">
		    <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
		        <img src="/images/'.$Rup.'.png" class="i60" alt="Open">
		    </a>
		    <br>';
    if ($d['heating']['s']==3) {
        echo '
            <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
                <img src="/images/Fire_'.($d['brander']['s']=='On'?'On':'Off').'.png" class="i48" alt="Brander">
            </a>
            <br>
            <br>
            <br>
            <br>';
    } elseif ($d['heating']['s']==2) {
        echo '
            <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
                <img src="/images/Elec.png" height="40px" width="auto" alt="Elec">
            </a>
            <br>
            <br>
            <br>
            <br>';
    } elseif ($d['heating']['s']==1) {
        echo '
            <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
                <img src="/images/Cooling.png" class="i48" alt="Cooling">
            </a>
            <br>
            <br>
            <br>
            <br>';
    } elseif ($d['heating']['s']==0) {
        echo '
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>';
    }

    echo '
            <a href=\'javascript:navigator_Go("floorplan.media.redirect.php");\'>
                <img src="/images/denon_';
    echo $d['denonpower']['s']=='ON'?'On':'Off';
    echo '.png" class="i70" alt="denon">
            </a>
            <br>
		    <a href=\'javascript:navigator_Go("floorplan.media.redirect.php");\'>
		        <img src="/images/';
    if ($d['tv']['s']=='On') {
        if ($d['lgtv']['s']=='On') {
            echo 'lgtv_On';
        } else {
            echo 'lgtv_Off';
        }
    } else {
        echo 'TV_Off';
    }
    echo '.png" class="i60" alt="lgtv">
            </a>
            <br>
		    <a href=\'javascript:navigator_Go("floorplan.media.redirect.php");\'>
		        <img src="/images/nvidia_';
    echo $d['nvidia']['m']=='On'?'On':'Off';
    echo '.png" class="i48" alt="nvidia">
		    </a>
		    <br>
        </div>
        <div class="fix center zon">';
    echo '
            <small>&#x21e7;</small> '.number_format($d['minmaxtemp']['m'], 1, ',', '').'°C<br>
            <small>&#x21e9;</small> '.number_format($d['minmaxtemp']['s'], 1, ',', '').'°C<br>
            <a href=\'javascript:navigator_Go("regen.php");\'>
                Buien: '.$d['buiten_temp']['m'].'
            </a>
            <br>';
    echo 'Hum:'.round($d['icon']['m'], 0).'%
            <br>';

    echo number_format($d['wind']['s'], 1, ',', '').'km/u';

    echo '
            <br>
            <br>
            <img src="images/sunrise.png" class="i20" alt="sunrise">
            <br>
            <small>&#x21e7;</small> '.strftime("%k:%M", $d['civil_twilight']['s']).'
            <br>
            <small>&#x21e9;</small> '.strftime("%k:%M", $d['civil_twilight']['m']).'
            <br>
            <br>';
    echo 'UV: ';
    if ($d['uv']['s']<2) {
        echo '
            <font color="#99EE00">
                '.number_format($d['uv']['s'], 1, ',', '').'
            </font>';
    } elseif ($d['uv']['s']>=2&&$d['uv']['s']<4) {
        echo '
            <font color="#99CC00">
                '.number_format($d['uv']['s'], 1, ',', '').'
            </font>';
    } elseif ($d['uv']['s']>=4&&$d['uv']['s']<6) {
        echo '
            <font color="#FFCC00">
                '.number_format($d['uv']['s'], 1, ',', '').'
            </font>';
    } elseif ($d['uv']['s']>=6&&$d['uv']['s']<8) {
        echo '
            <font color="#FF6600">
                '.number_format($d['uv']['s'], 1, ',', '').'
            </font>';
    } elseif ($d['uv']['s']>=8) {
        echo '
            <font color="#FF2200">
                '.number_format($d['uv']['s'], 1, ',', '').'
            </font>';
    }
    echo '
            <br>max:';
    if ($d['uv']['m']<2) {
        echo '
            <font color="#99EE00">'.number_format($d['uv']['m'], 1, ',', '').'</font>';
    } elseif ($d['uv']['m']>=2&&$d['uv']['s']<4) {
        echo '
            <font color="#99CC00">'.number_format($d['uv']['m'], 1, ',', '').'</font>';
    } elseif ($d['uv']['m']>=4&&$d['uv']['s']<6) {
        echo '
            <font color="#FFCC00">'.number_format($d['uv']['m'], 1, ',', '').'</font>';
    } elseif ($d['uv']['m']>=6&&$d['uv']['s']<8) {
        echo '
            <font color="#FF6600">'.number_format($d['uv']['m'], 1, ',', '').'</font>';
    } elseif ($d['uv']['m']>=8) {
        echo '
            <font color="#FF2200">'.number_format($d['uv']['m'], 1, ',', '').'</font>';
    }

    echo '
	    </div>';
	thermometer('buiten_temp');
    thermometer('living_temp');
    thermometer('badkamer_temp');
    thermometer('kamer_temp');
    thermometer('tobi_temp');
    thermometer('alex_temp');
    thermometer('zolder_temp');
    if (!empty($d['gcal']['m'])) {
        echo '
        <div class="fix z0 afval">
            '.$d['gcal']['m'].'
        </div>';
    }
    if (!empty($d['icon']['s'])) {
        if ($udevice=='Mac') {
            echo '
        <div class="fix weather">
            <a href="https://darksky.net/details/'.$lat.','.$lon.'/'.strftime("%Y-%m-%d", TIME).'/si24/nl" target="popup" >
                <img src="https://openweathermap.org/img/w/'.$d['icon']['s'].'.png" alt="icon">
            </a>
        </div>';
        } else {
            echo '
        <div class="fix weather">
            <a href=\'javascript:navigator_Go("https://darksky.net/details/'.$lat.','.$lon.'/'.strftime("%Y-%m-%d", TIME).'/si24/nl");\'>
                <img src="https://openweathermap.org/img/w/'.$d['icon']['s'].'.png" alt="icon">
            </a>
        </div>';
        }
    }
    echo '
        <div class="fix floorplan2icon">
            <a href=\'javascript:navigator_Go("floorplan.others.php");\'>
                <img src="/images/plus.png" class="i60" alt="plus">
            </a>
        </div>
        <div class="fix picam1">
            <a href=\'javascript:navigator_Go("picam1/index.php");\'>
                <img src="/images/Camera.png" class="i48" alt="cam">
            </a>
        </div>
        <div class="fix picam2">
            <a href=\'javascript:navigator_Go("picam2/index.php");\'>
                <img src="/images/Camera.png" class="i48" alt="cam">
            </a>
        </div>
        <div class="fix Weg">
            <form action="floorplan.php" method="GET">
                <input type="hidden" name="Weg" value="true">';
    if ($d['Weg']['s']==0) {
        echo '
                <input type="image" src="/images/Thuis.png" id="Weg">';
    } elseif ($d['Weg']['s']==1) {
        echo '
                <input type="image" src="/images/Slapen.png" id="Weg">';
    } elseif ($d['Weg']['s']==2) {
        echo '
                <input type="image" src="/images/Weg.png" id="Weg">';
    }
        echo '
            </form>
        </div>';
    echo '<div id="ajax"></div>';
}
//else {header("Location: index.php");die("Redirecting to: index.php");}
?>

    </body>
</html>