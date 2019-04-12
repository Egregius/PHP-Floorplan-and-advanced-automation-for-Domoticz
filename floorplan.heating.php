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
        $_SESSION['referer']='floorplan.heating.php';
    }
    echo '<html>
	<head>
		<title>Heating</title>
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
	    <link rel="icon" type="image/png" href="images/heating.png"/>
		<link rel="shortcut icon" href="images/heating.png"/>
		<link rel="apple-touch-icon" href="images/heating.png"/>
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php?v=5">
		<style>
			.btn{font-size:25;padding:15px;width:100px;height:35px;}
			.mode{font-size:25;padding:15px;width:155px;height:50px;}
		</style>
	</head>';
    //echo '<div class="fix z1" style="top:20px;left:100px;background-color:#000;text-align:left;font-size:15;padding:20px;z-index:1000;"><pre>';print_r($_REQUEST);echo '</pre></div>';
    if (isset($_POST['Naam'])&&isset($_POST['Actie'])&&!isset($_POST['Setpoint'])) {
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
        } else {
            store($_POST['Naam'].'_set', $_POST['Actie']);
            lgsql($user, $_POST['Naam'].'_set', $_POST['Actie']);
            storemode($_POST['Naam'].'_set', 2);
            lgsql($user, $_POST['Naam'].'_mode', 2);
            lg(' (Set Setpoint) | '.$user.' set '.$_POST['Naam'].' to '.$_POST['Actie'].'°');
        }
        usleep(100000);
        header("Location: floorplan.heating.php");
        die("Redirecting to: floorplan.heating.php");
    }
    if (isset($_POST['Roller'])&&isset($_POST['Naam'])&&!isset($_POST['mode'])) {
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
        header("Location: floorplan.heating.php");die("Redirecting to: floorplan.heating.php");
    } elseif (isset($_POST['Roller'])&&isset($_POST['Naam'])&&isset($_POST['mode'])) {
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
                        <input type="image" name="Rollerlevelon" value ="100" src="images/arrowgreendown.png" class="i90"/>
                    </div>
                    <div class="fix z" style="top:130px;left:200px;">
                        <input type="submit" name="mode" value ="';
        echo $d[$name]['m']==0?'Manueel':'Auto';
        echo '" class="btn i90"/>
                    </div>
                    <div class="fix z" style="top:100px;left:330px;">
                        <input type="image" name="Rollerleveloff" value ="0" src="images/arrowgreenup.png" class="i90"/>
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
                        <img src="/images/close.png" width="72px" height="72px"/>
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
                        } else {
                            storemode($i, 0);
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
                        }
                    } elseif (isset($_REQUEST['Rollerleveloff_x'])) {
                        lg(' (Set rollers verdieping) | '.$user.' '.$verdiep.' dicht ');
                        sl($i, 0, 'Roller');
                        if ($d[$i]['m']==0) {
                            storemode($i, 1);
                        }
                    } else {
                        lg(' (Set rollers verdieping) | '.$user.' '.$verdiep.' to '.$_REQUEST['Rollerlevel']);
                        sl($i, $_REQUEST['Rollerlevel'], 'Roller');
                        if ($d[$i]['m']==0) {
                            storemode($i, 1);
                        }
                    }
                }
            }
            //echo '</pre></div>';
            usleep(100000);
            header("Location: floorplan.heating.php");
            die("Redirecting to: floorplan.heating.php");
        } else {
            $name=$_REQUEST['verdieping'];
            echo '
        <div id="luifel" class="fix dimmer" >
			<form method="POST" action="floorplan.heating.php" oninput="level.value = Rollerlevel.valueAsNumber">
					<div class="fix z" style="top:15px;left:90px;">';
            echo '<h2>'.$name.'</h2>
						<input type="hidden" name="verdieping" value="'.$name.'">
						<input type="hidden" name="confirm" value="true">
					</div>
					<div class="fix z" style="top:100px;left:40px;">
						<input type="image" name="Rollerlevelon" value ="100" src="images/arrowgreendown.png" class="i90"/>
					</div>
					<div class="fix z" style="top:95px;left:160px;">
						<input type="submit" name="verdiepingmode" value ="Manueel" class="btn mode"/><br>
						<input type="submit" name="verdiepingmode" value ="Automatisch" class="btn mode"/>
					</div>
					<div class="fix z" style="top:100px;left:350px;">
						<input type="image" name="Rollerleveloff" value ="0" src="images/arrowgreenup.png" class="i90"/>
					</div>
					<div class="fix z" style="top:210px;left:10px;">';
            $levels=array(0,15,20,25,30,35,40,45,50,55,60,70,80,85,100);
            foreach ($levels as $level) {
                echo '<button name="Rollerlevel" value="'.$level.'" class="dimlevel">'.$level.'</button>';
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
				        <img src="/images/close.png" width="72px" height="72px"/>
				    </a>
				</div>
			</div>
		</body>
		<script type="text/javascript">function navigator_Go(url){window.location.assign(url);}</script>
	</html>';
            exit;
        }
    }
    if (isset($_REQUEST['luifel'])) {
        $name=$_REQUEST['luifel'];
        $stat=$d[$name]['s'];
        echo '
        <div id="luifel" class="fix dimmer" >
		<form method="POST" action="floorplan.heating.php" oninput="level.value = Rollerlevel.valueAsNumber">
				<div class="fix z" style="top:15px;left:90px;">';
        if ($stat==100) {
            echo '<h2>'.$name.' dicht</h2>';
        } else {
            echo '<h2>'.$name.' '.(100-$stat).'% Open</h2>';
        }
        echo '
					<input type="hidden" name="Naam" value="'.$name.'">
					<input type="hidden" name="Roller" value="true">
				</div>
				<div class="fix z" style="top:100px;left:70px;">
					<input type="image" name="Rollerleveloff" value ="0" src="images/arrowgreendown.png" class="i90"/>
				</div>
				<div class="fix z" style="top:130px;left:200px;">
					<input type="submit" name="mode" value ="';
        echo $d[$name]['m']==0?'Manueel':'Auto';
        echo '" class="btn i90"/>
				</div>
				<div class="fix z" style="top:100px;left:330px;">
					<input type="image" name="Rollerlevelon" value ="100" src="images/arrowgreenup.png" class="i90"/>
				</div>
				<div class="fix z" style="top:210px;left:10px;">';
        $levels=array(0,25,30,32,34,36,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,62,64,66,68,70,75,95,96,97,98,99,100);

        foreach ($levels as $level) {
            if ($stat!='Off'&&$stat==$level) {
                echo '
                <button name="Rollerlevel" value="'. $level.'" class="dimlevel dimlevela">'.(100-$level).'</button>';
            } else {
                echo '
                <button name="Rollerlevel" value="'.$level.'" class="dimlevel">'.(100-$level).'</button>';
            }
        }
        echo '
				</div>
			</form>
			<div class="fix z" style="top:5px;left:5px;">
			    <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
			        <img src="/images/close.png" width="72px" height="72px"/>
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
		<div id="message" class="fix confirm">
			<form method="post">
				<input type="hidden" name="heating" value="true"/>
				<input type="submit" name="confirm" value="Gas/Elec" class="btn" style="width:100%;height:24%;margin:1% 0px 1% 0px;font-size:5em;"/><br>
				<input type="submit" name="confirm" value="Elec" class="btn" style="width:100%;height:24%;margin:1% 0px 1% 0px;font-size:5em;"/><br>
				<input type="submit" name="confirm" value="Neutral" class="btn" style="width:100%;height:24%;margin:1% 0px 1% 0px;font-size:5em;"/><br>
				<input type="submit" name="confirm" value="Cooling" class="btn" style="width:100%;height:24%;margin:1% 0px 1% 0px;font-size:5em;"/>
			</form>
		</div>
	</body>
</html>';
            exit;
        } elseif (isset($_POST['confirm'])) {
            if ($_REQUEST['confirm']=='Cooling') {
                store('heating', 1);
            } elseif ($_REQUEST['confirm']=='Neutral') {
                store('heating', 0);
            } elseif ($_REQUEST['confirm']=='Elec') {
                store('heating', 2);
            } elseif ($_REQUEST['confirm']=='Gas/Elec') {
                store('heating', 3);
            }
            usleep(100000);
            header("Location: floorplan.heating.php");
            die("Redirecting to: floorplan.heating.php");
        }
    }
    if (isset($_REQUEST['SetSetpoint'])) {
        $name=$_REQUEST['SetSetpoint'];
        echo '
        <div id="luifel" class="fix dimmer" >
		    <form method="POST" action="floorplan.heating.php" oninput="level.value = Actie.valueAsNumber">
                <input type="hidden" name="Setpoint" value="true" >
                <h2>'.ucwords($name).'<br/><big><bold>'.number_format($d[$name.'_temp']['s'], 1, ",", "").'°C</bold></big></h2>
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
            $temps[]=$stat;
        }
        asort($temps);
        $temps=array_slice($temps, 0, 43);
        foreach ($temps as $temp) {
            if ($d[$name.'_set']['s']==$temp) {
                echo '
					<input type="submit" name="Actie" value="'.$temp.'"/ class="dimlevel dimlevela">';
            } else {
                echo '
					<input type="submit" class="dimlevel" name="Actie" value="'.$temp.'"/>';
            }
        }
        echo '
					</div>
				</form>
			<div class="fix z" style="top:5px;left:5px;">
			    <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
			        <img src="/images/close.png" width="72px" height="72px"/>
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
            <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>'.strftime("%k:%M:%S", TIME).'</a>
        </div>
        <div class="fix z1" style="top:5px;left:5px;">
            <a href=\'javascript:navigator_Go("floorplan.php");\'>
                <img src="/images/close.png" width="72px" height="72px"/>
            </a>
        </div>
        <div class="fix z1" style="top:290px;left:415px;">
            <a href=\'javascript:navigator_Go("floorplan.doorsensors.php");\'>
                <img src="/images/close.png" width="72px" height="72px"/>
            </a>
        </div>
        <div class="fix leftbuttons">
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <a href=\'javascript:navigator_Go("floorplan.media.redirect.php");\'>
                <img src="/images/denon_';
    echo $d['denonpower']['s']=='ON'?'On':'Off';
    echo '.png" class="i70">
            </a>
            <br/>
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
    echo '.png" class="i60">
            </a>
            <br/>
            <a href=\'javascript:navigator_Go("floorplan.media.redirect.php");\'>
                <img src="/images/nvidia_';
    echo $d['nvidia']['m']=='On'?'On':'Off';
    echo '.png" class="i48">
            </a>
            <br/>
        </div>
        <div class="fix" style="top:290px;left:90px;width:300px">
            <a href=\'javascript:navigator_Go("?verdieping=beneden");\' class="btn">
                Beneden
            </a>
            <a href=\'javascript:navigator_Go("?verdieping=boven");\' class="btn">
                Boven
            </a>
        </div>
        <div class="fix center zon">';
    echo '
            <a href=\'javascript:navigator_Go("regen.php");\'>
                Buien: '.$d['buiten_temp']['m'].'
            </a>
            <br>';
    echo '
            Hum:'.round($d['icon']['m'], 0).'%
            <br>';

    echo number_format($d['wind']['s'], 1, ',', '').'km/u';

    echo '
            <br>
            <br>
            <img src="images/sunrise.png"/>
            <br>
            '.strftime("%k:%M", $d['civil_twilight']['s']).'
            <br>
            '.strftime("%k:%M", $d['civil_twilight']['m']).'
            <br>
            <br>';
    echo '
            UV: ';
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
    if (!empty($d['icon']['s'])) {
        if ($udevice=='Mac') {
            echo '
        <div class="fix weather">
            <a href="https://darksky.net/details/'.$lat.','.$lon.'/'.strftime("%Y-%m-%d", TIME).'/si24/nl" target="popup" >
                <img src="https://openweathermap.org/img/w/'.$d['icon']['s'].'.png"/>
            </a>
        </div>';
        } else {
            echo '
        <div class="fix weather">
            <a href=\'javascript:navigator_Go("https://darksky.net/details/'.$lat.','.$lon.'/'.strftime("%Y-%m-%d", TIME).'/si24/nl");\'>
                <img src="https://openweathermap.org/img/w/'.$d['icon']['s'].'.png"/>
            </a>
        </div>';
        }
    }
    //echo '<div class="fix" style="top:242px;left:100px;"><pre>';print_r($_REQUEST);echo '</pre></div>';
    thermometer('buiten_temp');
    thermometer('living_temp');
    thermometer('badkamer_temp');
    thermometer('kamer_temp');
    thermometer('tobi_temp');
    thermometer('alex_temp');
    thermometer('zolder_temp');
    schakelaar('GroheRed', 'Plug');
    schakelaar('heater1', 'Fan');
    schakelaar('heater2', 'Fire');
    schakelaar('heater3', 'Fire');
    schakelaar('heater4', 'Fire');
    //thermometer('zolder_temp');
    //blinds('zoldertrap');
    $Rluifel=$d['luifel']['s'];
    $Rliving=$d['Rliving']['s'];
    $Rbureel=$d['Rbureel']['s'];
    $RkeukenL=$d['RkeukenL']['s'];
    $RkeukenR=$d['RkeukenR']['s'];
    $Rtobi=$d['Rtobi']['s'];
    $Ralex=$d['Ralex']['s'];
    $RkamerL=$d['RkamerL']['s'];
    $RkamerR=$d['RkamerR']['s'];
    luifel('luifel', $Rluifel);
    rollers('Rliving', $Rliving);
    rollers('Rbureel', $Rbureel);
    rollers('RkeukenL', $RkeukenL);
    rollers('RkeukenR', $RkeukenR);
    rollers('Rtobi', $Rtobi);
    rollers('Ralex', $Ralex);
    rollers('RkamerL', $RkamerL);
    rollers('RkamerR', $RkamerR);
    //rollery('luifel',$Rluifel,20,65,220,'PL');
    rollery('Rliving', $Rliving, 46, 80, 165, 'P');
    rollery('Rbureel', $Rbureel, 0, 208, 43, 'L');
    rollery('RkeukenL', $RkeukenL, 128, 475, 44, 'P');
    rollery('RkeukenR', $RkeukenR, 179, 475, 44, 'P');
    rollery('Rtobi', $Rtobi, 448, 80, 44, 'P');
    rollery('Ralex', $Ralex, 568, 80, 44, 'P');
    rollery('RkamerL', $RkamerL, 529, 481, 44, 'P');
    rollery('RkamerR', $RkamerR, 586, 481, 44, 'P');
    thermostaat('living', 140, 260);
    thermostaat('badkamer', 427, 375);
    thermostaat('tobi', 475, 143);
    thermostaat('alex', 567, 202);
    thermostaat('kamer', 551, 295);
    thermostaat('zolder', 670, 190);
    schakelaar('badkamervuur1', 'Fire');
    schakelaar('badkamervuur2', 'Fire');
    schakelaar('zoldervuur', 'Fire');

    setpoint('alexZ', 555, 76, 270);
    setpoint('tobiZ', 415, 76, 270);
    setpoint('kamerZ', 523, 455, 90);
    $Weg=$d['Weg']['s'];
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
    showTimestamp('pirliving', 0);
    showTimestamp('pirkeuken', 0);
    showTimestamp('pirgarage', 0);
    showTimestamp('pirinkom', 0);
    showTimestamp('pirhall', 0);
    showTimestamp('raamliving', 270);
    showTimestamp('raamtobi', 270);
    showTimestamp('raamalex', 270);
    showTimestamp('raamkamer', 90);
    showTimestamp('deurbadkamer', 90);
    showTimestamp('Rliving', 270);
    showTimestamp('Rbureel', 0);
    showTimestamp('RkeukenL', 90);
    showTimestamp('RkeukenR', 90);
    showTimestamp('Rtobi', 270);
    showTimestamp('Ralex', 270);
    showTimestamp('RkamerL', 90);
    showTimestamp('RkamerR', 90);
    showTimestamp('achterdeur', 270);
    showTimestamp('poort', 90);
    if ($d['poort']['s']=='Open') {
        echo '
        <div class="fix poort">
        </div>';
    }
    if ($d['achterdeur']['s']=='Open') {
        echo '
        <div class="fix achterdeur">
        </div>';
    }
    if ($d['raamliving']['s']=='Open') {
        echo '
        <div class="fix raamliving">
        </div>';
    }
    if ($d['raamtobi']['s']=='Open') {
        echo '
        <div class="fix raamtobi">
        </div>';
    }
    if ($d['raamalex']['s']=='Open') {
        echo '
        <div class="fix raamalex">
        </div>';
    }
    if ($d['raamkamer']['s']=='Open') {
        echo '
        <div class="fix raamkamer">
        </div>';
    }
    if ($d['deurbadkamer']['s']=='Open') {
        echo '
        <div class="fix deurbadkamer">
        </div>';
    }
    if ($d['raamhall']['s']=='Open') {
        echo '
        <div class="fix raamhall">
        </div>';
    }
    echo '
        <div class="fix floorplanstats">
            '.$udevice.' | '.$ipaddress.' | '.number_format(((microtime(true)-$start)*1000), 3).'
        </div>
        <div class="fix floorplan2icon">
            <a href=\'javascript:navigator_Go("floorplan.others.php");\'>
                <img src="/images/plus.png" class="i60"/>
            </a>
        </div>';
    $bigdif=$d['heating']['m'];
    echo '
        <div class="fix divsetpoints z">
            <table class="tablesetpoints">
                <tr>
                    <td align="right" height="60" width="100px">
                    </td>
                    <td width="65px">';
    if ($bigdif>0) {
        echo '
                        <font color="red">';
    } elseif ($bigdif<0) {
        echo '
                        <font color="blue">';
    } else {
        echo '
                        <font>';
    }
    echo '
                            '.number_format($bigdif, 1, ',', '').'
            			</font>
			        </td>
			        <td width="65px">
                        <form method="POST">
                            <input type="hidden" name="Schakel" value="true"/>';
    if ($d['brander']['s']=='Off') {
        echo '
                            <input type="hidden" name="Actie" value="On"/>
                            <input type="hidden" name="Naam" value="brander"/>
                            &nbsp;<input type="image" src="images/Fire_Off.png" height="48px" width="auto"/>';
    } else {
        echo'
                            <input type="hidden" name="Actie" value="Off"/>
                            <input type="hidden" name="Naam" value="brander"/>
                            &nbsp;<input type="image" src="images/Fire_On.png" height="48px" width="auto"/>';
    }
    echo '
	                    </form>
                    </td>
                    <td align="left" height="60" width="80px" style="line-height:18px">
                        Brander<br>
                        '.convertToHours(past('brander')).'
                    </td>
                </tr>
                <tr>';
    if ($d['heatingauto']['s']=='Off') {
        echo '
                    <td align="right" height="60" width="100px" style="line-height:18px">
                        Manueel
                    </td>
                    <td width="65px">
                        <form method="POST">
                            <input type="hidden" name="Schakel" value="true"/>
                            <input type="hidden" name="Actie" value="On"/>
                            <input type="hidden" name="Naam" value="heatingauto"/>
                            <input type="image" src="images/Fire_Off.png" height="48px" width="auto"/>&nbsp;
                        </form>
                    </td>';
    } else {
        echo '
                    <td align="right" height="60" width="100px" style="line-height:18px">
                        Automatisch
                    </td>
                    <td width="65px">
                        <form method="POST">
                            <input type="hidden" name="Schakel" value="true"/>
                            <input type="hidden" name="Actie" value="Off"/>
                            <input type="hidden" name="Naam" value="heatingauto"/>
                            <input type="image" src="images/Fire_On.png" height="48px" width="auto"/>&nbsp;
                        </form>
                    </td>';
    }
    echo '
                    <td width="65px">
                        <form method="POST">
                            <input type="hidden" name="heating" value="true"/>';
    if ($d['heating']['s']==0) {
        echo '
                            &nbsp;<input type="image" src="images/Fire_Off.png" height="48px" width="auto"/>
                    </td>
                    <td align="left" height="60" width="80px" style="line-height:18px">
                        Neutral
                    </td>';
    } elseif ($d['heating']['s']==1) {
        echo '
                        &nbsp;<input type="image" src="images/Cooling.png" height="48px" width="auto"/>
                    </td>
                    <td align="left" height="60" width="80px" style="line-height:18px">
                        Cooling
                    </td>';
    } elseif ($d['heating']['s']==2) {
        echo '
                        &nbsp;<input type="image" src="images/Elec.png" height="40px" width="auto"/>
                    </td>
                    <td align="left" height="60" width="80px" style="line-height:18px">
                        Elec
                    </td>';
    } elseif ($d['heating']['s']==3) {
        echo '
                        &nbsp;<input type="image" src="images/Fire_On.png" height="48px" width="auto"/>
                    </td>
                    <td align="left" height="60" width="80px" style="line-height:18px">
                        Gas/Elec
                    </td>';
    }
    echo '
                </tr>
            </form>
        </table>
        <script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
			setTimeout("window.location.href=window.location.href;",'.($local===true?'3950':'15000').')
		</script>';
}
function setpoint($name,$top,$left,$rotation)
{
    global $d;
    if ($rotation==270) {
        echo '
        <div class="fix stamp r270" style="top:'.$top.'px;left:'.$left.'px;text-align:right;">
            '.round($d[$name]['s'], 1).'
        </div>';
    } elseif ($rotation==90) {
        echo '
        <div class="fix stamp r90" style="top:'.$top.'px;left:'.$left.'px;text-align:left;">
            '.round($d[$name]['s'], 1).'
        </div>';
    }
}
?>

    </body>
</html>