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
$start=microtime(true);
require 'secure/functions.php';
require 'secure/functionsfloorplan.php';
require 'secure/authentication.php';
if ($home) {
    echo '<html>
    <head>
		<title>Floorplan</title>
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
			.dachterdeur{top:265px;left:80px;width:60px;height:45px;}
			.draamliving{top:46px;left:80px;width:60px;height:163px;}
			.draamtobi{top:448px;left:80px;width:60px;height:44px;}
			.draamalex{top:568px;left:80px;width:60px;height:44px;}
			.draamkamer{top:586px;left:425px;width:60px;height:43px;text-align:left;}
			.ddeurbadkamer{top:420px;left:341px;width:60px;height:46px;}
			.ddeurkamer{top:469px;left:290px;width:46px;height:33px;}
			.ddeurtobi{top:448px;left:167px;width:44px;height:44px;}
			.ddeuralex{top:535px;left:213px;width:43px;height:33px;}
			.ddeurgarage{top:221px;left:341px;width:43px;height:33px;}
			.ddeurinkom{top:56px;left:338px;width:43px;height:44px;}
			.ddeurwc{top:43px;left:418px;width:43px;height:33px;}
		</style>
	</head>';
    if (isset($_REQUEST['name'])&&isset($_REQUEST['action'])) {
        store($_REQUEST['name'], $_REQUEST['action'], null, false);
        if ($_REQUEST['action']=='Open') {
            if ($_REQUEST['name']=='raamkamer') {
                storemode('RkamerL', 0);
                storemode('RkamerR', 0);
            } elseif ($_REQUEST['name']=='raamtobi') {
                storemode('Rtobi', 0);
            } elseif ($_REQUEST['name']=='raamalex') {
                storemode('Ralex', 0);
            }
        }
    }
    $d=fetchdata();
    echo '
    <body class="floorplan">
        <div class="fix z1" style="top:5px;left:5px;">
            <a href=\'javascript:navigator_Go("floorplan.php");\'>
                <img src="/images/close.png" width="72px" height="72px"/>
            </a>
        </div>
        <div class="fix" id="clock" onclick="javascript:navigator_Go(\'floorplan.doorsensors.php\');">'.strftime("%k:%M:%S", TIME).'
        </div>';
    if ($d['poort']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=poort&action=Closed");\'>
            <div class="fix red dpoort">
                <br>
                <br>
                <br>
                Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=poort&action=Open");\'>
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
        <a href=\'javascript:navigator_Go("?name=achterdeur&action=Closed");\'>
            <div class="fix red dachterdeur">
                <br>
                &nbsp;&nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=achterdeur&action=Open");\'>
            <div class="fix green dachterdeur">
                <br>
                &nbsp;&nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    if ($d['raamliving']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamliving&action=Closed");\'>
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
        <a href=\'javascript:navigator_Go("?name=raamliving&action=Open");\'>
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
        <a href=\'javascript:navigator_Go("?name=raamtobi&action=Closed");\'>
            <div class="fix red draamtobi">
                <br>
                &nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamtobi&action=Open");\'>
            <div class="fix green draamtobi">
                <br>
                &nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    if ($d['raamalex']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamalex&action=Closed");\'>
            <div class="fix red draamalex">
                <br>
                &nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamalex&action=Open");\'>
            <div class="fix green draamalex">
                <br>
                &nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    if ($d['raamkamer']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamkamer&action=Closed");\'>
            <div class="fix red draamkamer">
                <br>
                &nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=raamkamer&action=Open");\'>
            <div class="fix green draamkamer">
                <br>
                &nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    if ($d['deurbadkamer']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurbadkamer&action=Closed");\'>
            <div class="fix red ddeurbadkamer">
                <br>
                &nbsp;&nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurbadkamer&action=Open");\'>
            <div class="fix green ddeurbadkamer">
                <br>
                &nbsp;&nbsp;&nbsp;Closed
            </div>
        </a>';
    }

    if ($d['deurkamer']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurkamer&action=Closed");\'>
            <div class="fix red ddeurkamer">
                <br>
                &nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurkamer&action=Open");\'>
            <div class="fix green ddeurkamer">
                <br>
                Closed
            </div>
        </a>';
    }

    if ($d['deurtobi']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurtobi&action=Closed");\'>
            <div class="fix red ddeurtobi">
                <br>
                &nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurtobi&action=Open");\'>
            <div class="fix green ddeurtobi">
                <br>
                &nbsp;Closed
            </div>
        </a>';
    }

    if ($d['deuralex']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=deuralex&action=Closed");\'>
            <div class="fix red ddeuralex">
                <br>
                &nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=deuralex&action=Open");\'>
            <div class="fix green ddeuralex">
                <br>
                Closed
            </div>
        </a>';
    }

    if ($d['deurgarage']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurgarage&action=Closed");\'>
            <div class="fix red ddeurgarage">
                <br>
                &nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurgarage&action=Open");\'>
            <div class="fix green ddeurgarage">
                <br>
                Closed
            </div>
        </a>';
    }

    if ($d['deurinkom']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurinkom&action=Closed");\'>
            <div class="fix red ddeurinkom">
                <br>
                &nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurinkom&action=Open");\'>
            <div class="fix green ddeurinkom">
                <br>
                Closed
            </div>
        </a>';
    }
/*
    if ($d['deurwc']['s']=='Open') {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurwc&action=Closed");\'>
            <div class="fix red ddeurwc">
                <br>
                &nbsp;&nbsp;Open
            </div>
        </a>';
    } else {
        echo '
        <a href=\'javascript:navigator_Go("?name=deurwc&action=Open");\'>
            <div class="fix green ddeurwc">
                <br>
                &nbsp;Closed
            </div>
        </a>';
    }
*/
   echo '
        <script type="text/javascript">
			function navigator_Go(url) {window.location.assign(url);}
		</script>';
}
?>

    </body>
</html>