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
require 'secure/functions.php';
require 'secure/functionsfloorplan.php';
require 'secure/authentication.php';
if ($home) {
    $d=fetchdata();
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
		<script type="text/javascript" src="/scripts/floorplan.js?v='.$floorplanjs.'"></script>
		<script type=\'text/javascript\'>
            $(document).ready(function() {
                ajax();
                setInterval(ajax, '.($local===true?'500':'2000').');
            });
        </script>
	</head>';
    floorplanactions();
    echo '
	<body class="floorplan">
	    <div id="placeholder"></div>';
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
    //schakelaar('IN1');
    //schakelaar('IN2');
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

    rollery('Ralex');
    rollery('Rbureel');
    rollery('RkamerL');
    rollery('RkamerR');
    rollery('RkeukenL');
    rollery('RkeukenR');
    rollery('Rliving');
    rollery('Rtobi');

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
    echo '
	    <div class="fix leftbuttons" id="heating" onclick="javascript:navigator_Go(\'floorplan.heating.php\');"></div>';
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
        <div class="fix Weg" id="Weg"></div>
        <div class="fix clock">
            <a href=\'javascript:navigator_Go("floorplan.php");\' id="clock">'.strftime("%k:%M:%S", TIME).'</a>
        </div>';


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
    if ($d['zoldervuur']['s']=='On') {
        echo '
        <div class="fix z0 zoldervuur2">
            <img src="images/Fire_On.png" width="28px" height="auto" alt="">
        </div>';
    }
    echo '
        <div class="fix floorplanstats">'.$udevice.' | '.$ipaddress.'</div>';
    sidebar();
    echo '
        <div class="fix verbruik" onclick="location.href=\'https://verbruik.egregius.be/dag.php?Guy=on\';" id="verbruik">
            <table>
                <tr id="trelec"></tr>
                <tr id="trzon"></tr>
                <tr id="trgas"></tr>
                <tr id="trwater"></tr>
                <tr id="trdgas"></tr>
                <tr id="trdwater"></tr>
            </table>
        </div>';
}
?>

    </body>
</html>