<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
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
$lat=50.8930154;
$lon=3.1123163;
$start=microtime(true);
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home) {
    session_start();
    $_SESSION['referer']='floorplan.php';
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
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
	    <link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php?v=3">
		<style type="text/css">
			.water{top:200px;left:218px;}
		</style>
	</head>';
    if (isset($_REQUEST['Weg'])) {
        if (isset($_REQUEST['Action'])) {
            store('Weg', $_REQUEST['Action']);

            if ($_REQUEST['Action']==0) {
                $db->query("UPDATE devices set t='0' WHERE n='heating';");
                if ($d['Weg']['s']!=1&&$d['poortrf']['s']=='Off') {
                    sw('poortrf', 'On');
                    header("Location: floorplan.php");
                    die("Redirecting to: floorplan.php");
                }
                lgsql($user, 'Weg', 'Thuis');
                resetsecurity();
                header("Location: floorplan.php");
                die("Redirecting to: floorplan.php");
            } elseif ($_REQUEST['Action']==1) {
                lgsql($user, 'Weg', 'Slapen');
                  huisslapen();
                  header("Location: floorplan.php");
                  die("Redirecting to: floorplan.php");
            } elseif ($_REQUEST['Action']==2) {
                lgsql($user, 'Weg', 'Weg');
                  huisweg();
                  header("Location: floorplan.php");
                  die("Redirecting to: floorplan.php");
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
            echo '
    <body>
        <div id="message" class="fix confirm">
            <form action="floorplan.php" method="post">
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
    if (isset($_POST['Naam'])&&!isset($_POST['dimmer'])) {
        if (in_array($_POST['Naam'], array('bureeltobi','weg','slapen'))) {
            if (!isset($_POST['confirm'])) {
                switch($_POST['Naam']){
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
					<input type="hidden" name="Naam" value="'.$_POST['Naam'].'">
					<input type="submit" name="confirm" value="'.$txton.'" class="btn huge2">
				</form>
				<form method="post">
					<input type="hidden" name="Actie" value="Off">
					<input type="hidden" name="Naam" value="'.$_POST['Naam'].'">
					<input type="submit" name="confirm" value="'.$txtoff.'" class="btn huge2">
				</form>
			</div>
			</body>
		</html>';
                    exit;
            } elseif (isset($_POST['confirm'])) {
                  sw($_POST['Naam'], $_POST['Actie']);
                  usleep(100000);
                  header("Location: floorplan.php");
                  die("Redirecting to: floorplan.php");
            }
        } elseif ($_POST['Naam']=='zoldertrap') {

            if ($d['raamhall']['s']=='Closed') {
                sw($_POST['Naam'], $_POST['Actie']);
                header("Location: floorplan.php");
                die("Redirecting to: floorplan.php");
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
        } elseif (!in_array($_POST['Naam'], array('radioluisteren','tvkijken','kodikijken'))) {
            sw($_POST['Naam'], $_POST['Actie']);
            usleep(100000);
            header("Location: floorplan.php");
            die("Redirecting to: floorplan.php");
        }
    } elseif (isset($_POST['dimmer'])) {
        if (isset($_POST['luifelauto'])) {
            storemode('dimactionluifel', 1);
        } elseif (isset($_POST['dimlevelon_x'])) {
            sl($_POST['Naam'], 100);
            storemode($_POST['Naam'], 0);
        } elseif (isset($_POST['dimleveloff_x'])) {
            sl($_POST['Naam'], 0);
            storemode($_POST['Naam'], 0);
        } elseif (isset($_POST['dimsleep_x'])) {
            lg('=> '.$user.' => activated dimmer sleep for '.$_POST['Naam']);
            storemode($_POST['Naam'], 1);
        } elseif (isset($_POST['dimwake_x'])) {
            lg('=> '.$user.' => activated dimmer wake for '.$_POST['Naam']);
            sl($_POST['Naam'], $_POST['dimwakelevel']+2);
            storemode($_POST['Naam'], 2);
        } elseif (isset($_POST['dimwake3u_x'])) {
            lg('=> '.$user.' => activated dimmer wake after 3 hours for '.$_POST['Naam']);
            storemode($_POST['Naam'], 3);
        } else {
            sl($_POST['Naam'], $_POST['dimlevel']);
            storemode($_POST['Naam'], 0);
        }
        usleep(100000);
        header("Location: floorplan.php");
        die("Redirecting to: floorplan.php");
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
        <div class="fix clock">
            <a href=\'javascript:navigator_Go("floorplan.php");\'>'.strftime("%k:%M:%S", TIME).'</a>
        </div>';

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
    rollery('Rliving', $d['Rliving']['s'], 46, 80, 165, 'P');
    rollery('Rbureel', $d['Rbureel']['s'], 0, 208, 43, 'L');
    rollery('RkeukenL', $d['RkeukenL']['s'], 128, 475, 44, 'P');
    rollery('RkeukenR', $d['RkeukenR']['s'], 179, 475, 44, 'P');
    rollery('Rtobi', $d['Rtobi']['s'], 448, 80, 44, 'P');
    rollery('Ralex', $d['Ralex']['s'], 568, 80, 44, 'P');
    rollery('RkamerL', $d['RkamerL']['s'], 529, 481, 44, 'P');
    rollery('RkamerR', $d['RkamerR']['s'], 586, 481, 44, 'P');
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
    $items=array('gas','water');
    foreach ($items as $i) {
        if (past($i.'vandaag')<15) {
            ${$i.'color'}='#FF0000';
        } elseif (past($i.'vandaag')<30) {
            ${$i.'color'}='#FF4400';
        } elseif (past($i.'vandaag')<60) {
            ${$i.'color'}='#FF8800';
        } elseif (past($i.'vandaag')<90) {
            ${$i.'color'}='#FFAA00';
        } elseif (past($i.'vandaag')<120) {
            ${$i.'color'}='#FFCC00';
        } elseif (past($i.'vandaag')<600) {
            ${$i.'color'}='#FFFF00';
        } else {
            ${$i.'color'}='#CCCCCC';
        }
    }
    if ($d['elec']['s']>6000) {
        $verbruikcolor='#FF0000';
    } elseif ($d['elec']['s']>5000) {
        $verbruikcolor='#FF4400';
    } elseif ($d['elec']['s']>4000) {
        $verbruikcolor='#FF8800';
    } elseif ($d['elec']['s']>3000) {
        $verbruikcolor='#FFAA00';
    } elseif ($d['elec']['s']>2000) {
        $verbruikcolor='#FFCC00';
    } elseif ($d['elec']['s']>1000) {
        $verbruikcolor='#FFFF00';
    } else {
        $verbruikcolor='#CCCCCC';
    }

    if ($d['elec']['m']>20) {
        $vandaagcolor='#FF0000';
    } elseif ($d['elec']['m']>18) {
        $vandaagcolor='#FF4400';
    } elseif ($d['elec']['m']>16) {
        $vandaagcolor='#FF8800';
    } elseif ($d['elec']['m']>14) {
        $vandaagcolor='#FFAA00';
    } elseif ($d['elec']['m']>12) {
        $vandaagcolor='#FFCC00';
    } elseif ($d['elec']['m']>10) {
        $vandaagcolor='#FFFF00';
    } else {
        $vandaagcolor='#CCCCCC';
    }

    if ($d['gasvandaag']['s']>700) {
        $gasvandaagcolor='#FF0000';
    } elseif ($d['gasvandaag']['s']>600) {
        $gasvandaagcolor='#FF4400';
    } elseif ($d['gasvandaag']['s']>500) {
        $gasvandaagcolor='#FF8800';
    } elseif ($d['gasvandaag']['s']>400) {
        $gasvandaagcolor='#FFAA00';
    } elseif ($d['gasvandaag']['s']>300) {
        $gasvandaagcolor='#FFCC00';
    } elseif ($d['gasvandaag']['s']>200) {
        $gasvandaagcolor='#FFFF00';
    } else {
        $gasvandaagcolor='#CCCCCC';
    }

    if ($d['watervandaag']['s']>1000) {
        $watervandaagcolor='#FF0000';
    } elseif ($d['watervandaag']['s']>750) {
        $watervandaagcolor='#FF4400';
    } elseif ($d['watervandaag']['s']>500) {
        $watervandaagcolor='#FF8800';
    } elseif ($d['watervandaag']['s']>400) {
        $watervandaagcolor='#FFAA00';
    } elseif ($d['watervandaag']['s']>300) {
        $watervandaagcolor='#FFCC00';
    } elseif ($d['watervandaag']['s']>200) {
        $watervandaagcolor='#FFFF00';
    } else {
        $watervandaagcolor='#CCCCCC';
    }

    if ($d['zon']['s']>3500) {
        $zoncolor='#00FF00';
    } elseif ($d['zon']['s']>3000) {
        $zoncolor='#33FF00';
    } elseif ($d['zon']['s']>2700) {
        $zoncolor='#66FF00';
    } elseif ($d['zon']['s']>2400) {
        $zoncolor='#99FF00';
    } elseif ($d['zon']['s']>2100) {
        $zoncolor='#CCFF00';
    } elseif ($d['zon']['s']>1800) {
        $zoncolor='#EEFF00';
    } elseif ($d['zon']['s']>1500) {
        $zoncolor='#FFFF33';
    } elseif ($d['zon']['s']>1200) {
        $zoncolor='#FFFF66';
    } elseif ($d['zon']['s']>900) {
        $zoncolor='#FFFF99';
    } elseif ($d['zon']['s']>600) {
        $zoncolor='#FFFFCC';
    } elseif ($d['zon']['s']>300) {
        $zoncolor='#EEEECC';
    } else {
        $zoncolor='#CCCCCC';
    }
    if ($d['zonvandaag']['m']>=120) {
        $zonvandaagcolor='#00FF00';
    } elseif ($d['zonvandaag']['m']>=110) {
        $zonvandaagcolor='#33FF00';
    } elseif ($d['zonvandaag']['m']>=100) {
        $zonvandaagcolor='#66FF00';
    } elseif ($d['zonvandaag']['m']>=90) {
        $zonvandaagcolor='#99FF00';
    } elseif ($d['zonvandaag']['m']>=80) {
        $zonvandaagcolor='#CCFF00';
    } elseif ($d['zonvandaag']['m']>=70) {
        $zonvandaagcolor='#EEFF00';
    } elseif ($d['zonvandaag']['m']>=60) {
        $zonvandaagcolor='#FFFF33';
    } elseif ($d['zonvandaag']['m']>=50) {
        $zonvandaagcolor='#FFFF66';
    } elseif ($d['zonvandaag']['m']>=40) {
        $zonvandaagcolor='#FFFF99';
    } elseif ($d['zonvandaag']['m']>=30) {
        $zonvandaagcolor='#FFFFCC';
    } elseif ($d['zonvandaag']['m']>=20) {
        $zonvandaagcolor='#EEEECC';
    } else {
        $zonvandaagcolor='#CCCCCC';
    }
    echo '
        <div class="fix verbruik" onclick="location.href=\'https://verbruik.egregius.be/dag.php?Guy=on\';">
            <table>
                <tr>
                    <td>Elec:</td>
                    <td><font color="'.$verbruikcolor.'">'.$d['elec']['s'].' W</font></td>
                    <td><font color="'.$vandaagcolor.'">'.$d['elec']['m'].' kWh</font></td>
                </tr>';
    if ($d['zon']['s']>0||$d['zonvandaag']['s']>0) {
        echo'
                <tr>
                    <td>Zon:</td>
                    <td><font color="'.$zoncolor.'">'.$d['zon']['s'].' W</font></td>
                    <td><font color="'.$zonvandaagcolor.'">'.number_format($d['zonvandaag']['s'], 1, ',', '.').' kWh</font></td>
                </tr>';
    }
    echo '
                <tr>
                    <td><font color="'.$gascolor.'">Gas:</font></td>
                    <td colspan=2><font color="'.$gasvandaagcolor.'">'.number_format($d['gasvandaag']['s']/100, 3, ',', '.').' m<sup>3</sup></font></td>
                </tr>
                <tr>
                    <td><font color="'.$watercolor.'">Water:</font></td>
                    <td colspan=2><font color="'.$watervandaagcolor.'">'.number_format($d['watervandaag']['s']/1000, 3, ',', '.').' m<sup>3</sup></font></td>
                </tr>';
    if ($d['douche']['s']>0||$d['douche']['m']>0) {
        echo '
                <tr>
                    <td>D-gas</td>
                    <td>'.$d['douche']['s']*10 .' L</td>
                    <td>'.number_format(($d['douche']['s']*10*0.00065), 2, ',', '.').' €</td>
                <tr>
                <tr>
                    <td>D-water</td>
                    <td>'.$d['douche']['m'].' L</td>
                    <td>'.number_format(($d['douche']['m']*0.0055), 2, ',', '.').' €</td>
                <tr>';
    }
    echo '
		    </table>
	    </div>';
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
            <form action="floorplan.php" method="POST">
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
    dimmer('tobi','i60');
    dimmer('zithoek');
    dimmer('eettafel');
    dimmer('kamer','i60');
    dimmer('alex','i60');
    dimmer('lichtbadkamer','i60');
    dimmer('terras','i48');
    //schakelaar('tvled','Light');
    schakelaar('kristal', 'Light');
    schakelaar('bureel', 'Light');
    schakelaar('inkom', 'Light');
    schakelaar('keuken', 'Light');
    schakelaar('wasbak', 'Light');
    schakelaar('kookplaat', 'Light');
    schakelaar('werkblad1', 'Light');
    schakelaar('voordeur', 'Light');
    schakelaar('hall', 'Light');
    schakelaar('garage', 'Light');
    schakelaar('garageled', 'Light');
    schakelaar('zolderg', 'Light');
    schakelaar('tuin', 'Light');
    schakelaar('zolder', 'Light');
    schakelaar('wc', 'Light');
    schakelaar('bureeltobi', 'Plug');
    schakelaar('tvtobi', 'Plug');
    schakelaar('badkamervuur1', 'Fire');
    schakelaar('badkamervuur2', 'Fire');
    schakelaar('heater1', 'Fan');
    schakelaar('heater2', 'Fire');
    schakelaar('heater3', 'Fire');
    schakelaar('heater4', 'Fire');
    schakelaar('diepvries', 'Light');
    if ($d['Weg']['s']==0||$d['poortrf']['s']=='On') {
        schakelaar('poortrf', 'Alarm');
    }
    if ($d['Xlight']['s']!='Off') {
        schakelaar('Xlight', 'Alarm');
    }
    schakelaar('jbl', 'Light');
    thermometer('buiten_temp');
    thermometer('living_temp');
    thermometer('badkamer_temp');
    thermometer('kamer_temp');
    thermometer('tobi_temp');
    thermometer('alex_temp');
    thermometer('zolder_temp');
    blinds('zoldertrap');
    if ($d['Weg']['s']>0) {
        secured('zliving');
        secured('zkeuken');
        secured('zinkom');
        secured('zgarage');
    }
    if ($d['Weg']['s']==2) {
        secured('zhalla');
        secured('zhallb');
    }
    if ($d['pirliving']['s']=='On') {
        motion('zliving');
    }
    if ($d['pirkeuken']['s']=='On') {
        motion('zkeuken');
    }
    if ($d['pirinkom']['s']=='On') {
        motion('zinkom');
    }
    if ($d['pirgarage']['s']=='On') {
        motion('zgarage');
    }
    if ($d['pirhall']['s']=='On') {
        motion('zhalla');
        motion('zhallb');
    }
    showTimestamp('belknop', 270);
    showTimestamp('pirgarage', 0);
    showTimestamp('pirliving', 0);
    showTimestamp('pirkeuken', 0);
    showTimestamp('pirinkom', 0);
    showTimestamp('pirhall', 0);
    showTimestamp('achterdeur', 270);
    showTimestamp('poort', 90);
    showTimestamp('raamliving', 270);
    showTimestamp('raamtobi', 270);
    showTimestamp('raamalex', 270);
    showTimestamp('raamkamer', 90);
    showTimestamp('deurbadkamer', 90);
    showTimestamp('deurinkom', 90);
    showTimestamp('deurgarage', 0);
    contact('poort');
    contact('achterdeur');
    contact('raamliving');
    contact('raamtobi');
    contact('raamalex');
    contact('raamkamer');
    contact('raamhall');
    contact('deurinkom');
    contact('deurgarage');
    contact('deurbadkamer');
    echo '
        <div class="fix bose">
            <a href=\'javascript:navigator_Go("floorplan.bose.php?ip=101");\'>
                <img src="images/Bose_'.($d['bose101']['s']=='On'?'On':'Off').'.png" id="bose" alt="">
            </a>
        </div>
        <div class="fix bosebadkamer">
            <a href=\'javascript:navigator_Go("floorplan.bose.php?ip=102");\'>
                <img src="images/Bose_'.($d['bose102']['s']=='On'?'On':'Off').'.png" id="bosebadkamer" alt="">
            </a>
        </div>
        <div class="fix bosekamer">
            <a href=\'javascript:navigator_Go("floorplan.bose.php?ip=103");\'>
                <img src="images/Bose_'.($d['bose103']['s']=='On'?'On':'Off').'.png" id="bosekamer" alt="">
            </a>
        </div>
        <div class="fix bosegarage">
            <a href=\'javascript:navigator_Go("floorplan.bose.php?ip=104");\'>
                <img src="images/Bose_'.($d['bose104']['s']=='On'?'On':'Off').'.png" id="bosegarage" alt="">
            </a>
        </div>
        <div class="fix bosebuiten">
            <a href=\'javascript:navigator_Go("floorplan.bose.php?ip=105");\'>
                <img src="images/Bose_'.($d['bose105']['s']=='On'?'On':'Off').'.png" id="bosebuiten" alt="">
            </a>
        </div>';
    echo $d['diepvries_temp']['s'] > -15 ? '
        <div class="fix z0 diepvries_temp red">
            '.$d['diepvries_temp']['s'].'°C
        </div>'
     : '
        <div class="fix z0 diepvries_temp">
            '.$d['diepvries_temp']['s'].'°C
        </div>';

    if ($d['Usage_grohered']['s']>1&&$d['Usage_grohered']['s']<10) {
        echo '
        <div class="fix z0 GroheRed">
            <img src="images/Plug_On.png" width="28px" height="auto" alt="">
        </div>';
    } elseif ($d['Usage_grohered']['s']>10) {
        echo '
        <div class="fix z0 GroheRed">
            <img src="images/Plug_Red.png" width="28px" height="auto" alt="">
        </div>';
    }
    $tobi=explode(';', $d['kWh_bureeltobi']['s']);
    if ($tobi[0]>0) {
        echo '
        <div class="fix bureeltobikwh z0">
            '.round($tobi[0], 0).'W
        </div>';
    }
    if ($d['zoldervuur']['s']=='On') {
        echo '
        <div class="fix z0 zoldervuur2">
            <img src="images/Fire_On.png" width="28px" height="auto" alt="">
        </div>';
    }
    echo '
        <div class="fix floorplanstats">
            '.$udevice.' | '.
            $ipaddress.' | '.
            number_format(((microtime(true)-$start)*1000), 3).'
        </div>';
    echo '
        <script type="text/javascript">
            function navigator_Go(url) {window.location.assign(url);}
            setTimeout("window.location.href=window.location.href;",'.
            ($local===true?'3950':'14950').');
        </script>';
}
//else {header("Location: index.php");die("Redirecting to: index.php");}
?>

    </body>
</html>