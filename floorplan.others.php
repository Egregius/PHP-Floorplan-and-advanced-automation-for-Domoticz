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
    error_reporting(E_ALL);ini_set("display_errors", "on");
    echo '<html><head>
		<title>Floorplan</title>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
		<meta name="HandheldFriendly" content="true"/>
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=yes,minimal-ui"/>
		<link rel="icon" type="image/png" href="images/domoticzphp48.png"/>
		<link rel="shortcut icon" href="images/domoticzphp48.png"/>
		<link rel="apple-touch-icon" href="images/domoticzphp48.png"/>
		<link rel="stylesheet" type="text/css" href="/styles/floorplan.php">
	</head>
	<body>';
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
        usleep(80000);
        header("Location: floorplan.others.php");
        die("Redirecting to: floorplan.others.php");
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
    } elseif (isset($_POST['fetchdomoticz'])) {
        include __DIR__ . '/secure/_fetchdomoticz.php';
    }

    echo '<body class="floorplan">
	<div class="fix clock">
		<a href=\'javascript:navigator_Go("floorplan.others.php");\'>'.strftime("%k:%M:%S", TIME).'</a>
	</div>
	<div class="fix z1" style="top:5px;left:5px;"><a href=\'javascript:navigator_Go("floorplan.php");\'><img src="/images/close.png" width="72px" height="72px"/></a></div>';
    //echo '<div class="fix" style="top:242px;left:100px;"><pre>';print_r($_REQUEST);echo '</pre></div>';
    rollery('Rliving', $d['Rliving']['s'], 46, 80, 165, 'P');
    rollery('Rbureel', $d['Rbureel']['s'], 0, 208, 43, 'L');
    rollery('RkeukenL', $d['RkeukenL']['s'], 128, 475, 44, 'P');
    rollery('RkeukenR', $d['RkeukenR']['s'], 179, 475, 44, 'P');
    rollery('Rtobi', $d['Rtobi']['s'], 448, 80, 44, 'P');
    rollery('Ralex', $d['Ralex']['s'], 568, 80, 44, 'P');
    rollery('RkamerL', $d['RkamerL']['s'], 529, 481, 44, 'P');
    rollery('RkamerR', $d['RkamerR']['s'], 586, 481, 44, 'P');
    schakelaar2('auto', 'Alarm');

    schakelaar2('water', 'Light');
    schakelaar2('regenpomp', 'Light');
    schakelaar2('zwembadfilter', 'Light');
    schakelaar2('zwembadwarmte', 'Light');
    schakelaar2('dampkap', 'Light');

    echo '
<div class="fix z1 center" style="top:370px;left:410px;"><a href=\'javascript:navigator_Go("bat.php");\'><img src="/images/verbruik.png" width="40px" height="40px"/><br/>&nbsp;Bats</a></div>
<div class="fix z1 center" style="top:100px;left:170px;">
'.($d['gcal']['s']==true?'Tobi: Beitem':'Tobi: Rumbeke').'<br>
</div>
<div class="fix z1 center" style="top:600px;left:100px;"><a href=\'javascript:navigator_Go("logs.php");\'><img src="/images/log.png" width="40px" height="40px"/><br>Log</a></div>
<div class="fix z1 center" style="top:600px;left:200px;"><a href=\'javascript:navigator_Go("floorplan.history.php");\'><img src="/images/log.png" width="40px" height="40px"/><br>History</a></div>
<div class="fix z1 center" style="top:600px;left:300px;"><a href=\'javascript:navigator_Go("floorplan.cache.php");\'><img src="/images/log.png" width="40px" height="40px"/><br>Cache</a></div>
<div class="fix z1 center" style="top:600px;left:400px;"><a href=\'javascript:navigator_Go("floorplan.ontime.php");\'><img src="/images/log.png" width="40px" height="40px"/><br>On-Time</a></div>
';

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
    if ($d['poort']['s']=='Open') {
        echo '<div class="fix poort"></div>';
    }
    if ($d['achterdeur']['s']=='Open') {
        echo '<div class="fix achterdeur"></div>';
    }
    if ($d['raamliving']['s']=='Open') {
        echo '<div class="fix raamliving"></div>';
    }
    if ($d['raamtobi']['s']=='Open') {
        echo '<div class="fix raamtobi"></div>';
    }
    if ($d['raamalex']['s']=='Open') {
        echo '<div class="fix raamalex"></div>';
    }
    if ($d['raamkamer']['s']=='Open') {
        echo '<div class="fix raamkamer"></div>';
    }
    if ($d['deurbadkamer']['s']=='Open') {
        echo '<div class="fix deurbadkamer"></div>';
    }

    echo '<div class="fix blackmedia">
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
<div class="fix z1 bottom" style="left:0px">
	<form method="POST">
		<input type="hidden" name="username" value="'.$user.'"/>
		<input type="submit" name="logout" value="Logout" class="btn" style="padding:0px;margin:0px;width:90px;height:35px;"/>
	</form>
	<br/>
	<br/>
</div></div>';

    echo '<div class="fix floorplanstats">'.$udevice.' | '.$ipaddress;
    echo ' | '.number_format(((microtime(true)-$start)*1000), 3);
    echo '</div>
<script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
			setTimeout("window.location.href=window.location.href;",';
    echo $local===true?'4950':'15000';
    echo ');
		</script>';
}
?>
    </body>
</html>