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
/**
 * Function ajax
 *
 * Echo's the ajax javascript for the floorplans
 *
 * @return string
 */
function blinds($name)
{
    global $d;
    echo '
        <div class="fix z '.$name.'">
            <form method="POST" action="">
                <input type="hidden" name="Schakel" value="'.$d[$name]['i'].'">
                <input type="hidden" name="Naam" value="'.$name.'">
                <input type="hidden" name="Actie" value="Off">';
    echo $d[$name]['s']=='Closed'
    ?'
                <input type="image" src="/images/arrowgreenup.png" class="i48" alt="Open">'
    :'
                <input type="image" src="/images/arrowup.png" class="i48" alt="Mixed">';
    echo '
            </form><br>
            <form method="POST" action="">
                <input type="hidden" name="Schakel" value="'.$d[$name]['i'].'">
                <input type="hidden" name="Naam" value="'.$name.'">
                <input type="hidden" name="Actie" value="On">';
    echo $d[$name]['s']=='Open'
    ?'
                <input type="image" src="/images/arrowgreendown.png" class="i48" alt="Closed">'
    :'
                <input type="image" src="/images/arrowdown.png" class="i48" alt="Mixed">';
    echo '
            </form>
        </div>';
}
function dimmer($name,$class='i70')
{
    global $page,$d;
    $page=str_replace('ajax', '', $page);
    echo '
        <form method="POST" action="">
            <div class="fix z '.$name.'" onclick="location.href=\''.$page.'?setdimmer='.$name.'\';">
                <input type="hidden" name="setdimmer" value="'.$name.'">';
    if ($d[$name]['s']==0||$d[$name]['s']=='') {
        echo '
			    <input type="image" src="/images/light_Off.png" class="'.$class.'" id="'.$name.'">
			    <div class="fix center dimmerlevel '.$class.'" id="level'.$name.'">
                </div>';
    } else {
        echo'
                <input type="image" src="/images/light_On.png" class="'.$class.'" id="'.$name.'">
                <div class="fix center dimmerlevel '.$class.'" id="level'.$name.'">
                    <a href=\'javascript:navigator_Go('.$page.'?setdimmer='.$name.');\'><font color="#000">'.$d[$name]['s'].'</font></a>
                </div>';
    }
    echo '
	        </div>
        </form>';
}
function schakelaar($name)
{
    echo '
        <div class="fix '.preg_replace('/\s/', '', $name).' z1 i48" id="'.$name.'"></div>';
}
function schakelaar2($name)
{
    global $eendag,$d;
    echo '
        <div class="fix z1 center '.$name.'" style="width:70px;">
        	<form method="POST" action=""><input type="hidden" name="Schakel" value="'.$d[$name]['i'].'">';
    echo $d[$name]['s']=='Off'?'
                <input type="hidden" name="Actie" value="On">
                <input type="hidden" name="Naam" value="'.$name.'">
                <input type="image" src="/images/'.$d[$name]['icon'].'_Off.png" class="i40">'
                   :'
                <input type="hidden" name="Actie" value="Off">
                <input type="hidden" name="Naam" value="'.$name.'">
                <input type="image" src="/images/'.$d[$name]['icon'].'_On.png" class="i40">';
    echo '
                <br>'.$name;
    if ($d[$name]['t']>$eendag) {
        echo '
                <div class="fix center" style="top:52px;left:0px;width:70px;">
                    '.strftime("%H:%M", $d[$name]['t']).'
                </div>';
    }
    echo '
            </form>
        </div>';
}
function thermometer($name)
{
    echo '
        <div class="fix '.$name.'" onclick="location.href=\'temp.php?sensor=998\';" id="'.$name.'"></div>';
}
function contact($name)
{
    echo '
        <div class="fix '.$name.'" id="'.$name.'"></div>';
}
function thermostaat($name,$top,$left)
{
    echo '
        <div class="fix z1" style="top:'.$top.'px;left:'.$left.'px;" onclick="location.href=\'floorplan.heating.php?SetSetpoint='.$name.'\';" id="'.$name.'_set"></div>';
}
function showTimestamp($name,$draai)
{
    echo '
        <div class="fix stamp z1 r'.$draai.' t'.$name.'" id="t'.$name.'"></div>';
}
function secured($name)
{
    echo '
        <div class="fix '.$name.' z0" id="'.$name.'"></div>';
}
function luifel($name,$stat)
{
    global $d;
    echo '
        <form method="POST" action="">
            <div class="fix z '.$name.'" onclick="location.href=\'floorplan.heating.php?luifel='.$name.'\';">
                <input type="hidden" name="luifel" value="'.$name.'">';
    if ($stat==00) {
        echo '
                <input type="image" src="/images/arrowgreenup.png" class="i60">';
    } elseif ($stat==100) {
        echo '
                <input type="image" src="/images/arrowgreendown.png" class="i60">';
    } else {
        echo'
                <input type="image" src="/images/arrowdown.png" class="i60">
                <div class="fix center dimmerlevel" style="position:absolute;top:10px;left:-2px;width:70px;letter-spacing:4;" onclick="location.href=\'floorplan.heating.php?luifel='.$name.'\';"><font size="5" color="#CCC">
                    '.$stat.'</font>
                </div>';
    }
    if ($d[$name]['m']==1) {
        echo '
                <div class="fix" style="top:2px;left:2px;z-index:-100;background:#fff7d8;width:56px;height:56px;border-radius:45px;">
                </div>';
    }
    echo '
		    </div>
        </form>';
}
function rollers($name)
{
    echo '
        <div class="fix z '.$name.'" onclick="location.href=\'floorplan.heating.php?rollers='.$name.'\';" id="R'.$name.'"></div>';
}
function rollery($name)
{
    echo '
        <div class="fix yellow" id="'.$name.'"></div>';
}
function bose($ip)
{
    global $d;
    echo '
        <div class="fix bose'.$ip.'" id="bosediv'.$ip.'">
            <a href=\'javascript:navigator_Go("floorplan.bose.php?ip='.$ip.'");\'>
                <img src="images/bose_'.($d['bose'.$ip]['s']=='On'?'On':'Off').'.png" id="bose'.$ip.'" alt="">
            </a>
        </div>';
}
function sidebar()
{
    global $d,$udevice,$lat,$lon;
    if (!empty($d['icon']['s'])) {
        if ($udevice=='Mac') {
            echo '
        <div class="fix weather">
            <a href="https://darksky.net/details/'.$lat.','.$lon.'/'.strftime("%Y-%m-%d", TIME).'/si24/nl" target="popup" >
                <img src="https://openweathermap.org/img/w/'.$d['icon']['s'].'.png" alt="icon" id="icon">
            </a>
        </div>';
        } else {
            echo '
        <div class="fix weather">
            <a href=\'javascript:navigator_Go("https://darksky.net/details/'.$lat.','.$lon.'/'.strftime("%Y-%m-%d", TIME).'/si24/nl");\'>
                <img src="https://openweathermap.org/img/w/'.$d['icon']['s'].'.png" alt="icon" id="icon">
            </a>
        </div>';
        }
    }
    thermometer('buiten_temp');
    if ($d['bose105']['m']=='Online') {
        bose(105);
    } else {
        echo '
        <div class="fix bose105" id="bosediv105">
        </div>';
    }
    echo '
        <div class="fix mediabuttons">
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
        echo 'tv_Off';
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
        <div class="fix center zon">
            <small>&#x21e7;</small><span id="maxtemp">'.number_format($d['minmaxtemp']['m'], 1, ',', '').'</span>°C<br>
            <small>&#x21e9;</small><span id="mintemp">'.number_format($d['minmaxtemp']['s'], 1, ',', '').'</span>°C<br>
            <a href=\'javascript:navigator_Go("regen.php");\'>
                Buien: <span id="buien">'.$d['buiten_temp']['m'].'</span>
            </a>
            <br>
            Hum:<span id="hum">'.round($d['icon']['m'], 0).'</span>%
            <br><span id="wind">'.number_format($d['wind']['s'], 1, ',', '').'</span>km/u

            <br>
            <br>
            <img src="images/sunrise.png" alt="sunrise">
            <br>
            <small>&#x21e7;</small><span id="zonop">'.strftime("%k:%M", $d['civil_twilight']['s']).'</span>
            <br>
            <small>&#x21e9;</small><span id="zononder">'.strftime("%k:%M", $d['civil_twilight']['m']).'</span>
            <br>
            <br>
            <div id="uv"></div>
	    </div>';
}
function floorplanactions()
{
    global $d;
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
        } elseif ($_REQUEST['Naam']=='poortrf') {
            if ($_REQUEST['Actie']=='On') {
                store('Weg', 0);
            }
            sw($_REQUEST['Naam'], $_REQUEST['Actie']);
        } elseif ($_REQUEST['Naam']=='lgtv') {
            if ($_REQUEST['Actie']=='On') {
                shell_exec('python3 secure/lgtv.py -c on -a '.$lgtvmac.' '.$lgtvip);
            } else {
                shell_exec('python3 secure/lgtv.py -c off '.$lgtvip);
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
    }
    if (isset($_POST['Setpoint'])) {
        if (isset($_POST['resetauto'])) {
            storemode($_POST['Naam'].'_set', 0);
            lgsql($user, $_POST['Naam'].'_mode', 'reset auto');
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
    } elseif (isset($_POST['Roller'])
        &&isset($_POST['Naam'])
        &&isset($_POST['mode'])
    ) {
        if ($_POST['mode']=='Manueel') {
            storemode($_POST['Naam'], 1);
        } else {
            storemode($_POST['Naam'], 0);
        }
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
					<input type="image" name="Rollerleveloff" value ="100" src="images/arrowgreendown.png" class="i90">
				</div>
				<div class="fix z" style="top:130px;left:200px;">
					<input type="submit" name="mode" value ="';
        echo $d[$name]['m']==0?'Manueel':'Auto';
        echo '" class="btn i90">
				</div>
				<div class="fix z" style="top:100px;left:330px;">
					<input type="image" name="Rollerlevelon" value ="0" src="images/arrowgreenup.png" class="i90">
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
</html>';
        exit;
    }
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
}