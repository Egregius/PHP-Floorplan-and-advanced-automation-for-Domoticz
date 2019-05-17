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
$start=microtime(true);
require 'secure/functions.php';
require 'secure/functionsfloorplan.php';
require 'secure/authentication.php';
if ($home) {
    echo '
        <div class="fix clock">
            <a href=\'javascript:navigator_Go("floorplan.heating.php");\' id="clock">'.strftime("%k:%M:%S", TIME).'</a>
        </div>';


    schakelaar('GroheRed');
    schakelaar('heater1');
    schakelaar('heater2');
    schakelaar('heater3');
    schakelaar('heater4');
    luifel('luifel', $d['luifel']['s']);
    rollers('Rliving', $d['Rliving']['s']);
    rollers('Rbureel', $d['Rbureel']['s']);
    rollers('RkeukenL', $d['RkeukenL']['s']);
    rollers('RkeukenR', $d['RkeukenR']['s']);
    rollers('Rtobi', $d['Rtobi']['s']);
    rollers('Ralex', $d['Ralex']['s']);
    rollers('RkamerL', $d['RkamerL']['s']);
    rollers('RkamerR', $d['RkamerR']['s']);
    rollery('Rliving', 46, 80, 165, 'P');
    rollery('Rbureel', 0, 208, 43, 'L');
    rollery('RkeukenL', 128, 475, 44, 'P');
    rollery('RkeukenR', 179, 475, 44, 'P');
    rollery('Rtobi', 448, 80, 44, 'P');
    rollery('Ralex', 568, 80, 44, 'P');
    rollery('RkamerL', 529, 481, 44, 'P');
    rollery('RkamerR', 586, 481, 44, 'P');
    thermostaat('living', 140, 260);
    thermostaat('badkamer', 427, 375);
    thermostaat('tobi', 475, 143);
    thermostaat('alex', 567, 202);
    thermostaat('kamer', 551, 295);
    thermostaat('zolder', 670, 190);
    schakelaar('badkamervuur1');
    schakelaar('badkamervuur2');
    schakelaar('zoldervuur');
    setpoint('alexZ', 555, 76, 270);
    setpoint('tobiZ', 415, 76, 270);
    setpoint('kamerZ', 523, 455, 90);
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
    contact('poort');
    contact('achterdeur');
    contact('raamliving');
    contact('raamtobi');
    contact('raamalex');
    contact('raamkamer');
    contact('deurbadkamer');
    contact('raamhall');
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
                        <form method="POST" action="">
                            <input type="hidden" name="Schakel" value="true">';
    if ($d['brander']['s']=='Off') {
        echo '
                            <input type="hidden" name="Actie" value="On">
                            <input type="hidden" name="Naam" value="brander">
                            &nbsp;<input type="image" src="images/fire_Off.png">';
    } else {
        echo'
                            <input type="hidden" name="Actie" value="Off">
                            <input type="hidden" name="Naam" value="brander">
                            &nbsp;<input type="image" src="images/fire_On.png">';
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
                        <form method="POST" action="">
                            <input type="hidden" name="Schakel" value="true">
                            <input type="hidden" name="Actie" value="On">
                            <input type="hidden" name="Naam" value="heatingauto">
                            <input type="image" src="images/fire_Off.png">&nbsp;
                        </form>
                    </td>';
    } else {
        echo '
                    <td align="right" height="60" width="100px" style="line-height:18px">
                        Automatisch
                    </td>
                    <td width="65px">
                        <form method="POST" action="">
                            <input type="hidden" name="Schakel" value="true">
                            <input type="hidden" name="Actie" value="Off">
                            <input type="hidden" name="Naam" value="heatingauto">
                            <input type="image" src="images/fire_On.png">&nbsp;
                        </form>
                    </td>';
    }
    echo '
                    <td width="65px">
                        <form method="POST" action="">
                            <input type="hidden" name="heating" value="true">';
    if ($d['heating']['s']==0) {
        echo '
                            &nbsp;<input type="image" src="images/fire_Off.png">
                        </form>
                    </td>
                    <td align="left" height="60" width="80px" style="line-height:18px">
                        Neutral
                    </td>';
    } elseif ($d['heating']['s']==1) {
        echo '
                        &nbsp;<input type="image" src="images/Cooling.png">
                        </form>
                    </td>
                    <td align="left" height="60" width="80px" style="line-height:18px">
                        Cooling
                    </td>';
    } elseif ($d['heating']['s']==2) {
        echo '
                        &nbsp;<input type="image" src="images/Elec.png">
                    </td>
                    <td align="left" height="60" width="80px" style="line-height:18px">
                        Elec
                    </td>';
    } elseif ($d['heating']['s']==3) {
        echo '
                        &nbsp;<input type="image" src="images/fire_On.png">
                        </form>
                    </td>
                    <td align="left" height="60" width="80px" style="line-height:18px">
                        Gas/Elec
                    </td>';
    }
    echo '
                </tr>
        </table>
        </div>
        <div class="fix floorplanstats">
            '.$udevice.' | '.$ipaddress.' | '.number_format(((microtime(true)-$start)*1000), 3, ',', '').'
        </div>';
}
function setpoint($name,$top,$left,$rotation)
{
    global $d;
    if ($rotation==270) {
        echo '
        <div class="fix stamp r270" style="top:'.$top.'px;left:'.$left.'px;text-align:right;" id="'.$name.'">
            '.round($d[$name]['s'], 1).'
        </div>';
    } elseif ($rotation==90) {
        echo '
        <div class="fix stamp r90" style="top:'.$top.'px;left:'.$left.'px;text-align:left;" id="'.$name.'">
            '.round($d[$name]['s'], 1).'
        </div>';
    }
}