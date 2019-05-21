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
require 'secure/functions.php';
require 'secure/functionsfloorplan.php';
require 'secure/authentication.php';
if ($home) {
    $d=fetchdata();
    createheader('floorplanothers');
    handlerequest();
    if (isset($_POST['Setpoint'])) {
        if (isset($_POST['resetauto'])) {
            storemode($_POST['Naam'].'_set', 0);
        } else {
            storemode($_POST['Naam'].'_set', 1);
            store($_POST['Naam'].'_set', $_POST['Actie']);
        }
    }
    if (isset($_POST['Schakel'])) {
        sw($_POST['Naam'], $_POST['Actie']);
        if ($_POST['Naam']=='water') {
            if ($_POST['Actie']=='Off') {
                storemode('water', 7201);
            } else {
                storemode('water', 300);
            }
        }
    } elseif (isset($_POST['RestartDomoticz'])) {
        shell_exec('service domoticz.sh restart &');
    } elseif (isset($_POST['RestartServer'])) {
        exec('nohup /sbin/reboot &');
    } elseif (isset($_POST['Water5min'])) {
        storemode('water', 300);
        double('water', 'On');
    } elseif (isset($_POST['Water30min'])) {
        storemode('water', 1800);
        double('water', 'On');
    } elseif (isset($_POST['Water2uur'])) {
        storemode('water', 7200);
        double('water', 'On');
    } elseif (isset($_REQUEST['fetchdomoticz'])) {
        include __DIR__ . '/secure/_fetchdomoticz.php';
    }

    echo '<body class="floorplan">
    <div id="placeholder"></div>

	<div class="fix z1" style="top:5px;left:5px;"><a href=\'javascript:navigator_Go("floorplan.php");\'><img src="/images/close.png" width="72px" height="72px"/></a></div>';
    //echo '<div class="fix" style="top:242px;left:100px;"><pre>';print_r($_REQUEST);echo '</pre></div>';

    echo '
<div class="fix z1 center" style="top:370px;left:410px;"><a href=\'javascript:navigator_Go("bat.php");\'><img src="/images/verbruik.png" width="40px" height="40px"/><br/>&nbsp;Bats</a></div>
<div class="fix z1 center" style="top:20px;left:130px;">
'.($d['gcal']['s']==true?'Tobi: Beitem':'Tobi: Rumbeke').'<br>
</div>
<div class="fix z1 center" style="top:600px;left:100px;"><a href=\'javascript:navigator_Go("logs.php");\'><img src="/images/log.png" width="40px" height="40px"/><br>Log</a></div>
<div class="fix z1 center" style="top:600px;left:200px;"><a href=\'javascript:navigator_Go("floorplan.history.php");\'><img src="/images/log.png" width="40px" height="40px"/><br>History</a></div>
<div class="fix z1 center" style="top:600px;left:300px;"><a href=\'javascript:navigator_Go("floorplan.cache.php?nicestatus");\'><img src="/images/log.png" width="40px" height="40px"/><br>Cache</a></div>
<div class="fix z1 center" style="top:600px;left:400px;"><a href=\'javascript:navigator_Go("floorplan.ontime.php");\'><img src="/images/log.png" width="40px" height="40px"/><br>On-Time</a></div>
<div class="fix blackmedia">
<form method="POST">
<div class="fix" style="top:230px;left:0px;width:400px">';
    if ($d['water']['s']=='On') {
        if ($d['water']['m']==300) {
            echo '<input type="submit" name="Water5min" value="Water 5 min" class="btn b3 btna"/>';
        } else {
            echo '<input type="submit" name="Water5min" value="Water 5 min" class="btn b3"/>';
        }
        if ($d['water']['m']==1800) {
            echo '<input type="submit" name="Water30min" value="Water 30 min" class="btn b3 btna"/>';
        } else {
            echo '<input type="submit" name="Water30min" value="Water 30 min" class="btn b3"/>';
        }
        if ($d['water']['m']==7200) {
            echo '<input type="submit" name="Water2uur" value="Water 2 uur" class="btn b3 btna"/>';
        } else {
            echo '<input type="submit" name="Water2uur" value="Water 2 uur" class="btn b3"/>';
        }
    } else {
        echo '<input type="submit" name="Water5min" value="Water 5 min" class="btn b3"/>
        <input type="submit" name="Water30min" value="Water 30 min" class="btn b3"/>
        <input type="submit" name="Water2uur" value="Water 2 uur" class="btn b3"/>';
    }
    echo '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
	<input type="submit" name="fetchdomoticz" value="Fetch Domoticz" class="btn b2" onclick="return confirm(\'Are you sure?\');"/><br><br>


</div>
</div>
</form>
<div class="fix z1 bottom" style="right:0px">
	<form method="POST">
		<input type="hidden" name="username" value="'.$user.'"/>
		<input type="submit" name="logout" value="Logout" class="btn" style="padding:0px;margin:0px;width:90px;height:35px;"/>
	</form>
	<br/>
	<br/>
</div></div>';
}
?>
    </body>
</html>