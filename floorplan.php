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
require 'secure/functionsfloorplan.php';
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
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php?v=4">
		<style type="text/css">
			.water{top:200px;left:218px;}
		</style>
		<script type="text/javascript" src="/scripts/jQuery.js"></script>
		<script type="text/javascript" src="/scripts/floorplan.js"></script>
		<script type=\'text/javascript\'>
            $(document).ready(function() {
                ajax();
                setInterval(ajax, '.($local===true?'300':'1500').');
            });
        </script>
	</head>';
    if (isset($_REQUEST['Weg'])) {
        if (isset($_REQUEST['Action'])) {
            store('Weg', $_REQUEST['Action']);

            if ($_REQUEST['Action']==0) {
                $db->query("UPDATE devices set t='1' WHERE n='heating';");
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
					<input type="image" name="dimleveloff" value ="0" src="images/light_Off.png" class="i90">
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
					<input type="image" name="dimlevelon" value ="100" src="images/light_On.png" class="i90">
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
        </div>';
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
    echo '<div class="fix clock">
            <a href=\'javascript:navigator_Go("floorplan.php");\' id="clock">'.strftime("%k:%M:%S", TIME).'</a>
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
                    <td><font color="'.$verbruikcolor.'" id="elec">'.$d['elec']['s'].' W</font></td>
                    <td><font color="'.$vandaagcolor.'" id="elecvandaag">'.number_format($d['elec']['m'], 1, ',', '').' kWh</font></td>
                </tr>';
    if ($d['zon']['s']>0||$d['zonvandaag']['s']>0) {
        echo'
                <tr>
                    <td>Zon:</td>
                    <td><font color="'.$zoncolor.'" id="zon">'.$d['zon']['s'].' W</font></td>
                    <td><font color="'.$zonvandaagcolor.'" id="zonvandaag">'.number_format($d['zonvandaag']['s'], 1, ',', '.').' kWh</font></td>
                </tr>';
    }
    echo '
                <tr>
                    <td><font color="'.$gascolor.'" id="gas">Gas:</font></td>
                    <td colspan=2><font color="'.$gasvandaagcolor.'" id="gasvandaag">'.number_format($d['gasvandaag']['s']/100, 3, ',', '.').' m<sup>3</sup></font></td>
                </tr>
                <tr>
                    <td><font color="'.$watercolor.'" id="verbrwater">Water:</font></td>
                    <td colspan=2><font color="'.$watervandaagcolor.'" id="watervandaag">'.number_format($d['watervandaag']['s']/1000, 3, ',', '.').' m<sup>3</sup></font></td>
                </tr>';
    if ($d['douche']['s']>0||$d['douche']['m']>0) {
        echo '
                <tr>
                    <td>D-gas</td>
                    <td id="douchegas">'.$d['douche']['s']*10 .' L</td>
                    <td id="douchegaseuro>'.number_format(($d['douche']['s']*10*0.0004), 2, ',', '.').' €</td>
                <tr>
                <tr>
                    <td>D-water</td>
                    <td id="douchewater">'.$d['douche']['m'].' L</td>
                    <td id="douchewatereuro>'.number_format(($d['douche']['m']*0.005), 2, ',', '.').' €</td>
                <tr>';
    }
    echo '
		    </table>
	    </div>';
    dimmer('tobi','i60');
    dimmer('zithoek');
    dimmer('eettafel');
    dimmer('kamer','i60');
    dimmer('alex','i60');
    dimmer('lichtbadkamer','i60');
    dimmer('terras','i48');
    schakelaar('kristal');
    schakelaar('bureel');
    schakelaar('inkom');
    schakelaar('keuken');
    schakelaar('wasbak');
    schakelaar('kookplaat');
    schakelaar('werkblad1');
    schakelaar('voordeur');
    schakelaar('hall');
    schakelaar('garage');
    schakelaar('garageled');
    schakelaar('zolderg');
    schakelaar('tuin');
    schakelaar('zolder');
    schakelaar('wc');
    schakelaar('IN1');
    schakelaar('IN2');
    schakelaar('bureeltobi');
    schakelaar('tvtobi');
    schakelaar('badkamervuur1');
    schakelaar('badkamervuur2');
    schakelaar('heater1');
    schakelaar('heater2');
    schakelaar('heater3');
    schakelaar('heater4');
    schakelaar('diepvries');
    schakelaar('poortrf');
//    if ($d['Xlight']['s']!='Off') {schakelaar('Xlight');}
    schakelaar('jbl');
    blinds('zoldertrap');
    secured('zliving');
    secured('zkeuken');
    secured('zinkom');
    secured('zgarage');
    secured('zhalla');
    secured('zhallb');

    rollery('Ralex', 568, 80, 44, 'P');
    rollery('Rbureel', 0, 208, 43, 'L');
    rollery('RkamerL', 529, 481, 44, 'P');
    rollery('RkamerR', 586, 481, 44, 'P');
    rollery('RkeukenL', 128, 475, 44, 'P');
    rollery('RkeukenR', 179, 475, 44, 'P');
    rollery('Rliving', 46, 80, 165, 'P');
    rollery('Rtobi', 448, 80, 44, 'P');

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
    bose(101);
    bose(102);
    bose(103);
    bose(104);
    echo $d['diepvries_temp']['s'] > -15 ? '
        <div class="fix z0 diepvries_temp red" id="diepvries_temp">
            '.$d['diepvries_temp']['s'].'°C
        </div>'
     : '
        <div class="fix z0 diepvries_temp" id="diepvries_temp">
            '.$d['diepvries_temp']['s'].'°C
        </div>';
    if ($d['Usage_grohered']['s']>1&&$d['Usage_grohered']['s']<10) {
        echo '
        <div class="fix z0 GroheRed">
            <img src="images/plug_On.png" width="28px" height="auto" alt="">
        </div>';
    } elseif ($d['Usage_grohered']['s']>10) {
        echo '
        <div class="fix z0 GroheRed">
            <img src="images/plug_Red.png" width="28px" height="auto" alt="">
        </div>';
    }
    $tobi=explode(';', $d['kWh_bureeltobi']['s']);
    if ($tobi[0]>0) {
        echo '
        <div class="fix bureeltobikwh z0">
            '.round($tobi[0], 0).'W
        </div>';
    } else {
        echo '
        <div class="fix bureeltobikwh z0">
        </div>';
    }
//    echo '<div class="fix z0" style="top:800px;left:100px">'.$d['IN1']['s'].' - '.$d['IN2']['s'].'</div>';
    if ($d['zoldervuur']['s']=='On') {
        echo '
        <div class="fix z0 zoldervuur2">
            <img src="images/Fire_On.png" width="28px" height="auto" alt="">
        </div>';
    }
    echo '
        <div class="fix floorplanstats">'.$udevice.' | '.$ipaddress.'</div>';
    sidebar();
}
//else {header("Location: index.php");die("Redirecting to: index.php");}
?>

    </body>
</html>