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
    echo '<html>
    <head>
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
		<style>
			.green{color:#000;}
			.red{color:#FFF;}
			.stamp{width:38px;text-align:center;font-size:120%;color:#888}
			.fix{cursor:pointer;z-index:100;}
			.dpoort{top:270px;left:404px;width:60px;height:114px;}
			.dachterdeur{top:265px;left:80px;width:65px;height:45px;}
			.draamliving{top:46px;left:80px;width:65px;height:163px;}
			.draamtobi{top:448px;left:80px;width:65px;height:44px;}
			.draamalex{top:568px;left:80px;width:65px;height:44px;}
			.draamkamer{top:584px;left:413px;width:75px;height:44px;text-align:left;}
			.ddeurbadkamer{top:420px;left:341px;width:65px;height:46px;}
		</style>
	</head>';
    if (isset($_REQUEST['name'])&&isset($_REQUEST['action'])) {
        $name=$_REQUEST['name'];
        ud($name, 0, $_REQUEST['action']);
        if ($_REQUEST['action']=='On') {
            $action='Open';
        } else {
            $action='Closed';
        }
        store($name, $action, null, true);
        if ($action=='Open') {
            if ($name=='raamkamer') {
                storemode('RkamerL', 0);
                storemode('RkamerR', 0);
            } elseif ($name=='raamtobi') {
                storemode('Rtobi', 0);
            } elseif ($name=='raamalex') {
                storemode('Ralex', 0);
            }
        }
        usleep(120000);
        header("Location: floorplan.doorsensors.php");die("Redirecting to: floorplan.doorsensors.php");
    }
    echo '
    <body class="floorplan">
        <div class="fix clock">
            <a href=\'javascript:navigator_Go("floorplan.doorsensors.php");\'>
                '.strftime("%k:%M:%S", TIME).'
            </a>
        </div>
        <div class="fix z1" style="top:5px;left:5px;">
            <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>
                <img src="/images/close.png" width="72px" height="72px"/>
            </a>
        </div>';
    showTimestamp('raamliving', 270);
    showTimestamp('raamtobi', 270);
    showTimestamp('raamalex', 270);
    showTimestamp('raamkamer', 90);
    showTimestamp('deurbadkamer', 90);
    showTimestamp('achterdeur', 270);
    showTimestamp('poort', 90);

    if ($d['poort']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=poort&action=Off");\'>
            <div class="fix red dpoort">
                <br>
                <br>
                <br>
                Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=poort&action=On");\'>
            <div class="fix green dpoort">
                <br>
                <br>
                <br>
                Closed
            </div>
        </a>';
    }

    if ($d['achterdeur']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=achterdeur&action=Off");\'>
            <div class="fix red dachterdeur">
                <br>
                &nbsp;&nbsp;&nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=achterdeur&action=On");\'>
            <div class="fix green dachterdeur">
                <br>
                &nbsp;&nbsp;&nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    if ($d['raamliving']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamliving&action=Off");\'>
            <div class="fix red draamliving">
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamliving&action=On");\'>
            <div class="fix green draamliving">
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                Closed
            </div>
        </a>';
    }

    if ($d['raamtobi']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamtobi&action=Off");\'>
            <div class="fix red draamtobi">
                <br>
                &nbsp;&nbsp;&nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamtobi&action=On");\'>
            <div class="fix green draamtobi">
                <br>
                &nbsp;&nbsp;&nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    if ($d['raamalex']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamalex&action=Off");\'>
            <div class="fix red draamalex">
                <br>
                &nbsp;&nbsp;&nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamalex&action=On");\'>
            <div class="fix green draamalex">
                <br>
                &nbsp;&nbsp;&nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    if ($d['raamkamer']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamkamer&action=Off");\'>
            <div class="fix red draamkamer">
                <br>
                &nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamkamer&action=On");\'>
            <div class="fix green draamkamer">
                <br>
                &nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    if ($d['deurbadkamer']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurbadkamer&action=Off");\'>
            <div class="fix red ddeurbadkamer">
                <br>
                &nbsp;&nbsp;&nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurbadkamer&action=On");\'>
            <div class="fix green ddeurbadkamer">
                <br>
                &nbsp;&nbsp;&nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    echo '
        <div class="fix floorplanstats">
            '.$udevice.' | '.$ipaddress. ' | '.number_format(((microtime(true)-$start)*1000), 3).'
        </div>
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