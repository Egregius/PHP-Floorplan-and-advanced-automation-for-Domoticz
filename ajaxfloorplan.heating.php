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
require 'secure/authentication.php';
if ($home) {
    echo '
    <body class="floorplan">
        <div class="fix clock">
            <a href=\'javascript:navigator_Go("floorplan.heating.php");\'>'.strftime("%k:%M:%S", TIME).'</a>
        </div>
        <div class="fix z1" style="top:5px;left:5px;">
            <a href=\'javascript:navigator_Go("floorplan.php");\'>
                <img src="/images/close.png" width="72px" height="72px" alt="Close">
            </a>
        </div>
        <div class="fix z1" style="top:290px;left:415px;">
            <a href=\'javascript:navigator_Go("floorplan.doorsensors.php");\'>
                <img src="/images/close.png" width="72px" height="72px" alt="Close">
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
        echo 'TV_Off';
    }
    echo '.png" class="i60" alt="tv">
            </a>
            <br>
            <a href=\'javascript:navigator_Go("floorplan.media.redirect.php");\'>
                <img src="/images/nvidia_';
    echo $d['nvidia']['m']=='On'?'On':'Off';
    echo '.png" class="i48" alt="nvidia">
            </a>
            <br>
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
            <small>&#x21e7;</small> '.number_format($d['minmaxtemp']['m'], 1, ',', '').'°C<br>
            <small>&#x21e9;</small> '.number_format($d['minmaxtemp']['s'], 1, ',', '').'°C<br>
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
            <img src="images/sunrise.png" alt="sunrise">
            <br>
            <small>&#x21e7;</small> '.strftime("%k:%M", $d['civil_twilight']['s']).'
            <br>
            <small>&#x21e9;</small> '.strftime("%k:%M", $d['civil_twilight']['m']).'
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
                <img src="https://openweathermap.org/img/w/'.$d['icon']['s'].'.png" alt="icon">
            </a>
        </div>';
        } else {
            echo '
        <div class="fix weather">
            <a href=\'javascript:navigator_Go("https://darksky.net/details/'.$lat.','.$lon.'/'.strftime("%Y-%m-%d", TIME).'/si24/nl");\'>
                <img src="https://openweathermap.org/img/w/'.$d['icon']['s'].'.png" alt="icon">
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
    luifel('luifel', $d['luifel']['s']);
    rollers('Rliving', $d['Rliving']['s']);
    rollers('Rbureel', $d['Rbureel']['s']);
    rollers('RkeukenL', $d['RkeukenL']['s']);
    rollers('RkeukenR', $d['RkeukenR']['s']);
    rollers('Rtobi', $d['Rtobi']['s']);
    rollers('Ralex', $d['Ralex']['s']);
    rollers('RkamerL', $d['RkamerL']['s']);
    rollers('RkamerR', $d['RkamerR']['s']);
    rollery('Rliving', $d['Rliving']['s'], 46, 80, 165, 'P');
    rollery('Rbureel', $d['Rbureel']['s'], 0, 208, 43, 'L');
    rollery('RkeukenL', $d['RkeukenL']['s'], 128, 475, 44, 'P');
    rollery('RkeukenR', $d['RkeukenR']['s'], 179, 475, 44, 'P');
    rollery('Rtobi', $d['Rtobi']['s'], 448, 80, 44, 'P');
    rollery('Ralex', $d['Ralex']['s'], 568, 80, 44, 'P');
    rollery('RkamerL', $d['RkamerL']['s'], 529, 481, 44, 'P');
    rollery('RkamerR', $d['RkamerR']['s'], 586, 481, 44, 'P');
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

        <div class="fix floorplan2icon">
            <a href=\'javascript:navigator_Go("floorplan.others.php");\'>
                <img src="/images/plus.png" class="i60" alt="plus">
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
                        <form method="POST" action="">
                            <input type="hidden" name="Schakel" value="true">';
    if ($d['brander']['s']=='Off') {
        echo '
                            <input type="hidden" name="Actie" value="On">
                            <input type="hidden" name="Naam" value="brander">
                            &nbsp;<input type="image" src="images/Fire_Off.png">';
    } else {
        echo'
                            <input type="hidden" name="Actie" value="Off">
                            <input type="hidden" name="Naam" value="brander">
                            &nbsp;<input type="image" src="images/Fire_On.png">';
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
                            <input type="image" src="images/Fire_Off.png">&nbsp;
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
                            <input type="image" src="images/Fire_On.png">&nbsp;
                        </form>
                    </td>';
    }
    echo '
                    <td width="65px">
                        <form method="POST" action="">
                            <input type="hidden" name="heating" value="true">';
    if ($d['heating']['s']==0) {
        echo '
                            &nbsp;<input type="image" src="images/Fire_Off.png">
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
                        &nbsp;<input type="image" src="images/Fire_On.png">
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