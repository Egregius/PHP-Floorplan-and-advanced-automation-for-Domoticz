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
	    <link rel="manifest" href="/manifest.json">
	    <link rel="shortcut icon" href="images/domoticzphp48.png">
		<link rel="apple-touch-icon" href="images/domoticzphp48.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php?v=3">
		<script type="text/javascript" src="/scripts/jQuery.js"></script>
		<style type="text/css">
			.water{top:200px;left:218px;}
		</style>
		<script type=\'text/javascript\'>
            $(document).ready(function() {
                timestamp();
                setInterval(timestamp, '.($local===true?'1950':'9950').');
            });

            function timestamp() {
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
	    <div id="ajax"></div>
        ';



    echo '
        <div class="fix floorplanstats">
            '.$udevice.' | '.
            $ipaddress.' | '.
            number_format(((microtime(true)-$start)*1000), 3).'
        </div>';
    echo '
        <script type="text/javascript">
            function navigator_Go(url) {window.location.assign(url);}
        </script>';
}
//else {header("Location: index.php");die("Redirecting to: index.php");}
?>

    </body>
</html>