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
    echo '<div class="fix clock">
            <a href=\'javascript:navigator_Go("floorplan.php");\' id="clock">'.strftime("%k:%M:%S", TIME).'</a>
        </div>';
    rollery('Rbureel', 0, 208, 43, 'L');
    rollery('RkeukenL', 128, 475, 44, 'P');
    rollery('RkeukenR', 179, 475, 44, 'P');
    rollery('Rtobi', 448, 80, 44, 'P');
    rollery('Ralex', 568, 80, 44, 'P');
    rollery('RkamerL', 529, 481, 44, 'P');
    rollery('RkamerR', 586, 481, 44, 'P');
    rollery('Rliving', 46, 80, 165, 'P');
    $items=array('gas','water');
    foreach ($items as $i) {
        if (past($i.'vandaag')<15) {
            ${$i.'color'}='#FF0000';
        } elseif (past($i.'vandaag')<30) {
            ${$i.'color'}='#FF4400';
        } elseif (past($i.'vandaag')<60) {
            ${$i.'color'}='#FF8800';
        } elseif (past($i.'vandaag')<90) {
            ${$i.'color'}='#FFAA00';
        } elseif (past($i.'vandaag')<120) {
            ${$i.'color'}='#FFCC00';
        } elseif (past($i.'vandaag')<600) {
            ${$i.'color'}='#FFFF00';
        } else {
            ${$i.'color'}='#CCCCCC';
        }
    }
    if ($d['elec']['s']>6000) {
        $verbruikcolor='#FF0000';
    } elseif ($d['elec']['s']>5000) {
        $verbruikcolor='#FF4400';
    } elseif ($d['elec']['s']>4000) {
        $verbruikcolor='#FF8800';
    } elseif ($d['elec']['s']>3000) {
        $verbruikcolor='#FFAA00';
    } elseif ($d['elec']['s']>2000) {
        $verbruikcolor='#FFCC00';
    } elseif ($d['elec']['s']>1000) {
        $verbruikcolor='#FFFF00';
    } else {
        $verbruikcolor='#CCCCCC';
    }

    if ($d['elec']['m']>20) {
        $vandaagcolor='#FF0000';
    } elseif ($d['elec']['m']>18) {
        $vandaagcolor='#FF4400';
    } elseif ($d['elec']['m']>16) {
        $vandaagcolor='#FF8800';
    } elseif ($d['elec']['m']>14) {
        $vandaagcolor='#FFAA00';
    } elseif ($d['elec']['m']>12) {
        $vandaagcolor='#FFCC00';
    } elseif ($d['elec']['m']>10) {
        $vandaagcolor='#FFFF00';
    } else {
        $vandaagcolor='#CCCCCC';
    }

    if ($d['gasvandaag']['s']>700) {
        $gasvandaagcolor='#FF0000';
    } elseif ($d['gasvandaag']['s']>600) {
        $gasvandaagcolor='#FF4400';
    } elseif ($d['gasvandaag']['s']>500) {
        $gasvandaagcolor='#FF8800';
    } elseif ($d['gasvandaag']['s']>400) {
        $gasvandaagcolor='#FFAA00';
    } elseif ($d['gasvandaag']['s']>300) {
        $gasvandaagcolor='#FFCC00';
    } elseif ($d['gasvandaag']['s']>200) {
        $gasvandaagcolor='#FFFF00';
    } else {
        $gasvandaagcolor='#CCCCCC';
    }

    if ($d['watervandaag']['s']>1000) {
        $watervandaagcolor='#FF0000';
    } elseif ($d['watervandaag']['s']>750) {
        $watervandaagcolor='#FF4400';
    } elseif ($d['watervandaag']['s']>500) {
        $watervandaagcolor='#FF8800';
    } elseif ($d['watervandaag']['s']>400) {
        $watervandaagcolor='#FFAA00';
    } elseif ($d['watervandaag']['s']>300) {
        $watervandaagcolor='#FFCC00';
    } elseif ($d['watervandaag']['s']>200) {
        $watervandaagcolor='#FFFF00';
    } else {
        $watervandaagcolor='#CCCCCC';
    }

    if ($d['zon']['s']>3500) {
        $zoncolor='#00FF00';
    } elseif ($d['zon']['s']>3000) {
        $zoncolor='#33FF00';
    } elseif ($d['zon']['s']>2700) {
        $zoncolor='#66FF00';
    } elseif ($d['zon']['s']>2400) {
        $zoncolor='#99FF00';
    } elseif ($d['zon']['s']>2100) {
        $zoncolor='#CCFF00';
    } elseif ($d['zon']['s']>1800) {
        $zoncolor='#EEFF00';
    } elseif ($d['zon']['s']>1500) {
        $zoncolor='#FFFF33';
    } elseif ($d['zon']['s']>1200) {
        $zoncolor='#FFFF66';
    } elseif ($d['zon']['s']>900) {
        $zoncolor='#FFFF99';
    } elseif ($d['zon']['s']>600) {
        $zoncolor='#FFFFCC';
    } elseif ($d['zon']['s']>300) {
        $zoncolor='#EEEECC';
    } else {
        $zoncolor='#CCCCCC';
    }
    if ($d['zonvandaag']['m']>=120) {
        $zonvandaagcolor='#00FF00';
    } elseif ($d['zonvandaag']['m']>=110) {
        $zonvandaagcolor='#33FF00';
    } elseif ($d['zonvandaag']['m']>=100) {
        $zonvandaagcolor='#66FF00';
    } elseif ($d['zonvandaag']['m']>=90) {
        $zonvandaagcolor='#99FF00';
    } elseif ($d['zonvandaag']['m']>=80) {
        $zonvandaagcolor='#CCFF00';
    } elseif ($d['zonvandaag']['m']>=70) {
        $zonvandaagcolor='#EEFF00';
    } elseif ($d['zonvandaag']['m']>=60) {
        $zonvandaagcolor='#FFFF33';
    } elseif ($d['zonvandaag']['m']>=50) {
        $zonvandaagcolor='#FFFF66';
    } elseif ($d['zonvandaag']['m']>=40) {
        $zonvandaagcolor='#FFFF99';
    } elseif ($d['zonvandaag']['m']>=30) {
        $zonvandaagcolor='#FFFFCC';
    } elseif ($d['zonvandaag']['m']>=20) {
        $zonvandaagcolor='#EEEECC';
    } else {
        $zonvandaagcolor='#CCCCCC';
    }
    echo '
        <div class="fix verbruik" onclick="location.href=\'https://verbruik.egregius.be/dag.php?Guy=on\';">
            <table>
                <tr>
                    <td>Elec:</td>
                    <td><font color="'.$verbruikcolor.'" id="elec">'.$d['elec']['s'].' W</font></td>
                    <td><font color="'.$vandaagcolor.'" id="elecvandaag">'.number_format($d['elec']['m'], 1, ',', '').' kWh</font></td>
                </tr>';
    if ($d['zon']['s']>0||$d['zonvandaag']['s']>0) {
        echo'
                <tr>
                    <td>Zon:</td>
                    <td><font color="'.$zoncolor.'" id="zon">'.$d['zon']['s'].' W</font></td>
                    <td><font color="'.$zonvandaagcolor.'" id="zonvandaag">'.number_format($d['zonvandaag']['s'], 1, ',', '.').' kWh</font></td>
                </tr>';
    }
    echo '
                <tr>
                    <td><font color="'.$gascolor.'" id="gas">Gas:</font></td>
                    <td colspan=2><font color="'.$gasvandaagcolor.'" id="gasvandaag">'.number_format($d['gasvandaag']['s']/100, 3, ',', '.').' m<sup>3</sup></font></td>
                </tr>
                <tr>
                    <td><font color="'.$watercolor.'" id="water">Water:</font></td>
                    <td colspan=2><font color="'.$watervandaagcolor.'" id="watervandaag">'.number_format($d['watervandaag']['s']/1000, 3, ',', '.').' m<sup>3</sup></font></td>
                </tr>';
    if ($d['douche']['s']>0||$d['douche']['m']>0) {
        echo '
                <tr>
                    <td>D-gas</td>
                    <td id="douchegas">'.$d['douche']['s']*10 .' L</td>
                    <td id="douchegaseuro>'.number_format(($d['douche']['s']*10*0.0004), 2, ',', '.').' €</td>
                <tr>
                <tr>
                    <td>D-water</td>
                    <td id="douchewater">'.$d['douche']['m'].' L</td>
                    <td id="douchewatereuro>'.number_format(($d['douche']['m']*0.005), 2, ',', '.').' €</td>
                <tr>';
    }
    echo '
		    </table>
	    </div>';
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
    schakelaar('bureeltobi');
    schakelaar('tvtobi');
    schakelaar('badkamervuur1');
    schakelaar('badkamervuur2');
    schakelaar('heater1');
    schakelaar('heater2');
    schakelaar('heater3');
    schakelaar('heater4');
    schakelaar('diepvries');
    if ($d['Weg']['s']==0||$d['poortrf']['s']=='On') {
        schakelaar('poortrf');
    }
    if ($d['Xlight']['s']!='Off') {
        schakelaar('Xlight');
    }
    schakelaar('jbl');
    blinds('zoldertrap');
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
    motion('living');
    motion('keuken');
    motion('inkom');
    motion('garage');
    motion('hall');
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
            <img src="images/Plug_On.png" width="28px" height="auto" alt="">
        </div>';
    } elseif ($d['Usage_grohered']['s']>10) {
        echo '
        <div class="fix z0 GroheRed">
            <img src="images/Plug_Red.png" width="28px" height="auto" alt="">
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
//    echo '<div class="fix z0" style="top:800px;left:100px">'.$d['IN1']['s'].' - '.$d['IN2']['s'].'</div>';
    if ($d['zoldervuur']['s']=='On') {
        echo '
        <div class="fix z0 zoldervuur2">
            <img src="images/Fire_On.png" width="28px" height="auto" alt="">
        </div>';
    }
    echo '
        <div class="fix floorplanstats">
            '.$udevice.' | '.
            $ipaddress.'
        </div>';
}