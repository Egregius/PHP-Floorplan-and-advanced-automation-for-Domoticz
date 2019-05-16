<?php
/**
 * Pass2PHP
 * php version 7.3.5-1
 *
 * This flooplan handles everything that has to do with heating and rollers.
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
    echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Heating</title>
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
	    <link rel="icon" type="image/png" href="images/heating.png">
		<link rel="shortcut icon" href="images/heating.png">
		<link rel="apple-touch-icon" href="images/heating.png">
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php?v=5">
		<style type="text/css">
			.btn{font-size:25;padding:15px;width:100px;height:35px;}
			.mode{font-size:25;padding:15px;width:155px;height:50px;}
		</style>
		<script type="text/javascript" src="/scripts/jQuery.js"></script>
		<style type="text/css">
			.water{top:200px;left:218px;}
		</style>
		<script type=\'text/javascript\'>
            $(document).ready(function() {
                ajax();
                setInterval(ajax, '.($local===true?'2950':'9950').');
            });
            function ajax() {
                $.ajax({
                    url: \'/ajaxfloorplan.heating.php\',
                    success: function(data) {
                        $(\'#ajax\').html(data);
                    },
                });
            }
            function navigator_Go(url) {window.location.assign(url);}
        </script>
	</head>';
    //echo '<div class="fix z1" style="top:20px;left:100px;background-color:#000;text-align:left;font-size:15;padding:20px;z-index:1000;"><pre>';print_r($_REQUEST);echo '</pre></div>';
    if (isset($_POST['Naam'])
        &&isset($_POST['Actie'])
        &&!isset($_POST['Setpoint'])
    ) {
        sw($_POST['Naam'], $_POST['Actie']);
        usleep(80000);
        if ($_POST['Naam']=='GroheRed') {
            if ($_POST['Actie']=='On') {
                storemode('GroheRed', 1);
            } else {
                storemode('GroheRed', 0);
            }
        }
        header("Location: floorplan.heating.php");
        die("Redirecting to: floorplan.heating.php");
    }
    if (isset($_POST['Setpoint'])) {
        if (isset($_POST['resetauto'])) {
            storemode($_POST['Naam'].'_set', 0);
            lgsql($user, $_POST['Naam'].'_mode', $_POST['Actie']);
            lg(' (Set Setpoint) | '.$user.' set '.$_POST['Naam'].' to Automatic');
            $d[$_POST['Naam'].'_set']['m']=0;
            include 'secure/_verwarming.php';
        } else {
            store($_POST['Naam'].'_set', $_POST['Actie']);
            lgsql($user, $_POST['Naam'].'_set', $_POST['Actie']);
            storemode($_POST['Naam'].'_set', 2);
            lgsql($user, $_POST['Naam'].'_mode', 2);
            lg(' (Set Setpoint) | '.$user.' set '.$_POST['Naam'].' to '.$_POST['Actie'].'°');
            $d[$_POST['Naam'].'_set']['s']=$_POST['Actie'];
            $d[$_POST['Naam'].'_set']['m']=2;
            include 'secure/_verwarming.php';
        }
        usleep(100000);
        header("Location: floorplan.heating.php");
        die("Redirecting to: floorplan.heating.php");
    }
    if (isset($_POST['Roller'])
        &&isset($_POST['Naam'])
        &&!isset($_POST['mode'])
    ) {
        if (isset($_POST['Rollerlevelon_x'])) {
            sl($_POST['Naam'], 100, 'Roller');
            if ($d[$_POST['Naam']]['m']==0) {
                storemode($_POST['Naam'], 1);
            }
        } elseif (isset($_POST['Rollerleveloff_x'])) {
            if ($_POST['Naam']=='Rlivingzzz') {
                sl($_POST['Naam'], 8, 'Roller');
            } else {
                sl($_POST['Naam'], 0, 'Roller');
            }
            if ($d[$_POST['Naam']]['m']==0) {
                storemode($_POST['Naam'], 1);
            }
        } else {
            if ($_POST['Naam']=='Rlivingzzz'&&$_POST['Rollerlevel']<8) {
                sl($_POST['Naam'], 8, 'Roller');
            }
            sl($_POST['Naam'], $_POST['Rollerlevel'], 'Roller');
            if ($d[$_POST['Naam']]['m']==0) {
                storemode($_POST['Naam'], 1);
            }
        }
        usleep($Usleep);
        header("Location: floorplan.heating.php");
        die("Redirecting to: floorplan.heating.php");
    } elseif (isset($_POST['Roller'])
        &&isset($_POST['Naam'])
        &&isset($_POST['mode'])
    ) {
        if ($_POST['mode']=='Manueel') {
            storemode($_POST['Naam'], 1);
        } else {
            storemode($_POST['Naam'], 0);
        }
        usleep(100000);
        header("Location: floorplan.heating.php");
        die("Redirecting to: floorplan.heating.php");
    }
    if (isset($_REQUEST['rollers'])) {
        $name=$_REQUEST['rollers'];
        $stat=$d[$name]['s'];
        echo '
    <body>
        <div id="luifel" class="fix dimmer" >
            <form method="POST" action="floorplan.heating.php" oninput="level.value = Rollerlevel.valueAsNumber">
                    <div class="fix z" style="top:15px;left:90px;">';
        if ($stat==0) {
            echo '
                        <h2>'.$name.' dicht</h2>';
        } else {
            echo '
                        <h2>'.$name.' '.$stat.'% Dicht</h2>';
        }
        echo '
                        <input type="hidden" name="Naam" value="'.$name.'">
                        <input type="hidden" name="Roller" value="true">
                    </div>
                    <div class="fix z" style="top:100px;left:70px;">
                        <input type="image" name="Rollerlevelon" value ="100" src="images/arrowgreendown.png" class="i90" alt="">
                    </div>
                    <div class="fix z" style="top:130px;left:200px;">
                        <input type="submit" name="mode" value ="';
        echo $d[$name]['m']==0?'Manueel':'Auto';
        echo '" class="btn i90"/>
                    </div>
                    <div class="fix z" style="top:100px;left:330px;">
                        <input type="image" name="Rollerleveloff" value ="0" src="images/arrowgreenup.png" class="i90" alt="">
                    </div>
                    <div class="fix z" style="top:210px;left:10px;">';
        $levels=array(0,15,20,25,30,35,40,45,50,55,60,70,80,85,100);
        foreach ($levels as $level) {
            if ($stat!='Off'&&$stat==$level) {
                echo '
                        <button name="Rollerlevel" value="'. $level.'" class="dimlevel dimlevela">'.$level.'</button>';
            } else {
                echo '
                        <button name="Rollerlevel" value="'.$level.'" class="dimlevel">'.$level.'</button>';
            }
        }
        echo '
                        <br>
                        <br>
                        <br>';
        $levels=array(21,22,23,24,26,27,28,29,31,32,33,34,36,37,38,39,41,42,43,44,46,47,48,49,51,52,53,54,56,57,58,59,61,62,63,64,65,66,67,68,69,71,72,73,74,75,76,77,79,80,81,82,83,84,85,86,87,88,89,91,92,93,94,95,96,97,98,99);
        foreach ($levels as $level) {
            if ($stat!='Off'&&$stat==$level) {
                echo '
                        <button name="Rollerlevel" value="'. $level.'" class="dimlevel dimlevela">
                            '.$level.'
                        </button>';
            } else {
                echo '
                        <button name="Rollerlevel" value="'.$level.'" class="dimlevel">
                            '.$level.'
                        </button>';
            }
        }
            echo '
                    </div>
                </form>
                <div class="fix z" style="top:5px;left:5px;">
                    <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
                        <img src="/images/close.png" width="72px" height="72px" alt="">
                    </a>
                </div>
            </div>
        </body>
        <script type="text/javascript">function navigator_Go(url){window.location.assign(url);}</script>
    </html>';
        exit;
    }
    if (isset($_REQUEST['verdieping'])) {
        if (isset($_REQUEST['confirm'])) {
            $verdiep=$_REQUEST['verdieping'];
            $beneden=array(/*'Rliving',*/'Rbureel','RkeukenL','RkeukenR');
            $boven=array('Rtobi','Ralex','RkamerL','RkamerR');
            $items=${$verdiep};
            //echo '<div class="fix z1" style="top:20px;left:100px;background-color:#000;text-align:left;font-size:15;padding:20px;z-index:1000;"><pre>';
            //print_r($_REQUEST);
            //print_r($items);
            if (isset($_REQUEST['verdiepingmode'])) {
                lg(' (Set rollers verdieping) | '.$user.' '.$verdiep.' to '.$_REQUEST['verdiepingmode']);
                foreach ($items as $i) {
                    if ($d[$i]['m']<2) {
                        if ($_REQUEST['verdiepingmode']=='Manueel') {
                            storemode($i, 1);
                            $d[$i]['m']=1;
                        } else {
                            storemode($i, 0);
                            $d[$i]['m']=0;
                        }
                    }
                }
            } else {
                foreach ($items as $i) {
                    if (isset($_REQUEST['Rollerlevelon_x'])) {
                        lg(' (Set rollers verdieping) | '.$user.' '.$verdiep.' dicht ');
                        sl($i, 100, 'Roller');
                        if ($d[$i]['m']==0) {
                            storemode($i, 1);
                            $d[$i]['m']=1;
                        }
                    } elseif (isset($_REQUEST['Rollerleveloff_x'])) {
                        lg(' (Set rollers verdieping) | '.$user.' '.$verdiep.' dicht ');
                        sl($i, 0, 'Roller');
                        if ($d[$i]['m']==0) {
                            storemode($i, 1);
                            $d[$i]['m']=1;
                        }
                    } else {
                        lg(' (Set rollers verdieping) | '.$user.' '.$verdiep.' to '.$_REQUEST['Rollerlevel']);
                        sl($i, $_REQUEST['Rollerlevel'], 'Roller');
                        if ($d[$i]['m']==0) {
                            storemode($i, 1);
                            $d[$i]['m']=1;
                        }
                    }
                }
            }
            include 'secure/_rolluiken.php';
            //echo '</pre></div>';
            usleep(100000);
            header("Location: floorplan.heating.php");
            die("Redirecting to: floorplan.heating.php");
        } else {
            $name=$_REQUEST['verdieping'];
            echo '
    <body>
        <div id="luifel" class="fix dimmer" >
			<form method="POST" action="floorplan.heating.php" oninput="level.value = Rollerlevel.valueAsNumber">
					<div class="fix z" style="top:15px;left:90px;">';
            echo '<h2>'.$name.'</h2>
						<input type="hidden" name="verdieping" value="'.$name.'">
						<input type="hidden" name="confirm" value="true">
					</div>
					<div class="fix z" style="top:100px;left:40px;">
						<input type="image" name="Rollerlevelon" value ="100" src="images/arrowgreendown.png" class="i90">
					</div>
					<div class="fix z" style="top:95px;left:160px;">
						<input type="submit" name="verdiepingmode" value ="Manueel" class="btn mode"><br>
						<input type="submit" name="verdiepingmode" value ="Automatisch" class="btn mode">
					</div>
					<div class="fix z" style="top:100px;left:350px;">
						<input type="image" name="Rollerleveloff" value ="0" src="images/arrowgreenup.png" class="i90">
					</div>
					<div class="fix z" style="top:210px;left:10px;">';
            $levels=array(0,15,20,25,30,35,40,45,50,55,60,70,80,85,100);
            foreach ($levels as $level) {
                echo '
                        <button name="Rollerlevel" value="'.$level.'" class="dimlevel">
                            '.$level.'
                        </button>';
            }
            echo '
                        <br>
                        <br>
                        <br>';
            $levels=array(21,22,23,24,26,27,28,29,31,32,33,34,36,37,38,39,41,42,43,44,46,47,48,49,51,52,53,54,56,57,58,59,61,62,63,64,65,66,67,68,69,71,72,73,74,75,76,77,79,80,81,82,83,84,85,86,87,88,89,91,92,93,94,95,96,97,98,99);
            foreach ($levels as $level) {
                echo '
                        <button name="Rollerlevel" value="'.$level.'" class="dimlevel">'.$level.'</button>';
            }
            echo '
					</div>
				</form>
				<div class="fix z" style="top:5px;left:5px;">
				    <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
				        <img src="/images/close.png" width="72px" height="72px" alt="Close">
				    </a>
				</div>
			</div>
		</body>
		<script type="text/javascript">
		    function navigator_Go(url){window.location.assign(url);}
		</script>
	</html>';
            exit;
        }
    }
    if (isset($_REQUEST['luifel'])) {
        $name=$_REQUEST['luifel'];
        $stat=$d[$name]['s'];
        echo '
    <body>
        <div id="luifel" class="fix dimmer" >
		    <form method="POST" action="floorplan.heating.php" oninput="level.value = Rollerlevel.valueAsNumber">
				<div class="fix z" style="top:15px;left:90px;">';
        if ($stat==0) {
            echo '<h2>'.$name.' dicht</h2>';
        } else {
            echo '<h2>'.$name.' '.$stat.'% Open</h2>';
        }
        echo '
					<input type="hidden" name="Naam" value="'.$name.'">
					<input type="hidden" name="Roller" value="true">
				</div>
				<div class="fix z" style="top:100px;left:70px;">
					<input type="image" name="Rollerleveloff" value ="0" src="images/arrowgreendown.png" class="i90">
				</div>
				<div class="fix z" style="top:130px;left:200px;">
					<input type="submit" name="mode" value ="';
        echo $d[$name]['m']==0?'Manueel':'Auto';
        echo '" class="btn i90">
				</div>
				<div class="fix z" style="top:100px;left:330px;">
					<input type="image" name="Rollerlevelon" value ="100" src="images/arrowgreenup.png" class="i90">
				</div>
				<div class="fix z" style="top:210px;left:10px;">';
        $levels=array(0,25,30,32,34,36,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,62,64,66,68,70,75,95,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100);
        foreach ($levels as $level) {
            if ($stat!='Off'&&$stat==$level) {
                echo '
                    <button name="Rollerlevel" value="'. $level.'" class="dimlevel dimlevela">
                        '.$level.'
                    </button>';
            } else {
                echo '
                    <button name="Rollerlevel" value="'.$level.'" class="dimlevel">
                        '.$level.'
                    </button>';
            }
        }
        echo '
				</div>
			</form>
			<div class="fix z" style="top:5px;left:5px;">
			    <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
			        <img src="/images/close.png" width="72px" height="72px" alt="Close">
			    </a>
			</div>
		</div>
	</body>
	<script type="text/javascript">function navigator_Go(url){window.location.assign(url);}</script>
</html>';
        exit;
    }
    if (isset($_REQUEST['heating'])) {
        if (!isset($_POST['confirm'])) {
            echo '
    <body>
        <div id="message" class="fix confirm">
			<form method="POST" action="">
				<input type="hidden" name="heating" value="true">
				<input type="submit" name="confirm" value="Gas/Elec" class="btn" style="width:100%;height:24%;margin:1% 0px 1% 0px;font-size:5em;"><br>
				<input type="submit" name="confirm" value="Elec" class="btn" style="width:100%;height:24%;margin:1% 0px 1% 0px;font-size:5em;"><br>
				<input type="submit" name="confirm" value="Neutral" class="btn" style="width:100%;height:24%;margin:1% 0px 1% 0px;font-size:5em;"><br>
				<input type="submit" name="confirm" value="Cooling" class="btn" style="width:100%;height:24%;margin:1% 0px 1% 0px;font-size:5em;">
			</form>
		</div>
	</body>
</html>';
            exit;
        } elseif (isset($_POST['confirm'])) {
            if ($_REQUEST['confirm']=='Cooling') {
                store('heating', 1);
                $d['heating']['s']=1;
            } elseif ($_REQUEST['confirm']=='Neutral') {
                store('heating', 0);
                $d['heating']['s']=0;
            } elseif ($_REQUEST['confirm']=='Elec') {
                store('heating', 2);
                $d['heating']['s']=2;
            } elseif ($_REQUEST['confirm']=='Gas/Elec') {
                store('heating', 3);
                $d['heating']['s']=3;
            }
            include 'secure/_verwarming.php';
            usleep(100000);
            header("Location: floorplan.heating.php");
            die("Redirecting to: floorplan.heating.php");
        }
    }
    if (isset($_REQUEST['SetSetpoint'])) {
        $name=$_REQUEST['SetSetpoint'];
        echo '
    <body>
        <div id="luifel" class="fix dimmer" >
		    <form method="POST" action="floorplan.heating.php" oninput="level.value = Actie.valueAsNumber">
                <input type="hidden" name="Setpoint" value="true" >
                <h2>'.ucwords($name).'<br><big><bold>'.number_format($d[$name.'_temp']['s'], 1, ",", "").'°C</bold></big></h2>
                <div class="fix z" style="top:210px;left:10px;">';
        if ($d[$name.'_set']['m']==0) {
            echo '
                    <input type="submit" name="resetauto" value="Auto" class="dimlevel dimlevela" style="width:182px;">';
        } else {
            echo '
                    <input type="submit" name="resetauto" value="Auto" class="dimlevel" style="width:182px;">';
        }
        echo '
                    <input type="hidden" name="Naam" value="'.$name.'">';
        if ($name=='living'||$name=='badkamer') {
            $temps=array(15,15.5,16,16.5,17,17.5,18,18.5,19,19.2,19.5,19.7,20,20.1,20.2,20.3,20.4,20.5,20.6,20.7,20.8,20.9,21,21.1,21.2,21.3,21.4,21.5,21.6,21.7,21.8,21.9,22);
        } elseif ($name=='zolder') {
            $temps=array(4,7,8,9,10,11,12,13,14,15,16,16.5,17,17.5,18,18.5,19,19.5,19.6,19.7,19.8,19.9,20,20.1,20.2,20.3,20.4,20.5);
        } else {
            $temps=array(10,10.5,11,11.5,12,12.5,13,13.5,14,14.2,14.5,14.7,15,15.1,15.2,15.3,15.4,15.5,15.6,15.7,15.8,15.9,16,16.1,16.2,16.3,16.4,16.5,16.6,16.7,16.8,16.9,17,17.1,17.2,17.3,17.4,17.5,17.6,17.7,17.8,17.9,18);
        }
        if (!in_array($d[$name.'_set']['s'], $temps)) {
            $temps[]=$d[$name.'_set']['s'];
        }
        asort($temps);
        $temps=array_slice($temps, 0, 33);
        foreach ($temps as $temp) {
            if ($d[$name.'_set']['s']==$temp) {
                echo '
					<input type="submit" name="Actie" value="'.$temp.'"/ class="dimlevel dimlevela">';
            } else {
                echo '
					<input type="submit" class="dimlevel" name="Actie" value="'.$temp.'">';
            }
        }
        echo '
					</div>
				</form>
			<div class="fix z" style="top:5px;left:5px;">
			    <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
			        <img src="/images/close.png" width="72px" height="72px" alt="Close">
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
}

?>

    </body>
</html>