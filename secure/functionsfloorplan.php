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
function requestdimmer(){
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
}
function requestweg()
{
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
}