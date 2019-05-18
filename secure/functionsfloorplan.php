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
function ajax()
{
    global $local;
    echo '
		<script type=\'text/javascript\'>
            $(document).ready(function() {
                ajax();
                setInterval(ajax, '.($local===true?'200':'800').');
            });
        </script>';
}
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
        <div class="fix '.preg_replace('/\s/', '', $name).' z1 i48" id="'.$name.'">
        </div>';
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
                <input type="image" src="/images/'.$d[$name]['dt'].'_Off.png" class="i40">'
                   :'
                <input type="hidden" name="Actie" value="Off">
                <input type="hidden" name="Naam" value="'.$name.'">
                <input type="image" src="/images/'.$d[$name]['dt'].'_On.png" class="i40">';
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
    global $d;
    $temp=$d[$name]['s'];
    $hoogte=$temp*3;
    if ($hoogte>88) {
        $hoogte=88;
    } elseif ($hoogte<20) {
        $hoogte=20;
    }
    $top=88-$hoogte;
    if ($top<0) {
        $top=0;
    }
    $top=$top+5;
    if ($temp>=22) {
        $tcolor='F00';
        $dcolor='55F';
    } elseif ($temp>=20) {
        $tcolor='D12';
        $dcolor='44F';
    } elseif ($temp>=18) {
        $tcolor='B24';
        $dcolor='33F';
    } elseif ($temp>=15) {
        $tcolor='93B';
        $dcolor='22F';
    } elseif ($temp>=10) {
        $tcolor='64D';
        $dcolor='11F';
    } else {
        $tcolor='55F';
        $dcolor='00F';
    }
    echo '
        <div class="fix '.$name.'" onclick="location.href=\'temp.php?sensor=998\';">
            <div class="fix tmpbg" style="top:'.number_format($top, 0).'px;left:8px;height:'.number_format($hoogte, 0).'px;background:linear-gradient(to bottom, #'.$tcolor.', #'.$dcolor.');">
            </div>
            <img src="/images/temp.png" height="100px" width="auto" alt="'.$name.'">
            <div class="fix center" style="top:73px;left:5px;width:30px;" id="'.$name.'">
                '.number_format($temp, 1, ',', '').'
            </div>
        </div>';
}
function contact($name)
{
    echo '
            <div class="fix '.$name.'" id="'.$name.'"></div>';
}
function thermostaat($name,$top,$left)
{
    global $d;
    $stat=$d[$name.'_set']['s'];
    $dif=$d[$name.'_temp']['s']-$stat;
    $mode=$d[$name.'_set']['m'];
    if ($dif>0.2) {
        $circle='hot';
    } elseif ($dif<0) {
        $circle='cold';
    } else {
        $circle='grey';
    }
    if ($stat>20.5) {
        $centre='red';
    } elseif ($stat>19) {
        $centre='orange';
    } elseif ($stat>13) {
        $centre='grey';
    } else {
        $centre='blue';
    }
    echo '
        <div class="fix z1" style="top:'.$top.'px;left:'.$left.'px;" onclick="location.href=\'floorplan.heating.php?SetSetpoint='.$name.'\';">
            <img src="/images/thermo'.$circle.$centre.'.png" class="i48" alt="">
            <div class="fix center" style="top:32px;left:11px;width:26px;">';
    if ($mode>0) {
        echo '
                <font size="2" color="#222">';
    } else {
        echo '
                <font size="2" color="#CCC">';
    }
    echo '<span id="'.$name.'_set">'.number_format($stat, 1, ',', '').'</span></font>
            </div>';
    if ($mode>0) {
        echo '
            <div class="fix" style="top:2px;left:2px;z-index:-100;background:#b08000;width:44px;height:44px;border-radius:45px;">
            </div>';
    }
    echo '
        </div>';
}
function showTimestamp($name,$draai)
{
    echo '
        <div class="fix stamp z1 r'.$draai.' t'.$name.'" id="t'.$name.'">
        </div>';
}
function secured($name)
{
    echo '
        <div class="fix secured '.$name.'">
        </div>';
}
function motion($name)
{
    echo '
        <div class="fix z'.$name.' z0" id="pir'.$name.'">
        </div>';
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
function rollers($name,$stat)
{
    global $d;
    echo '
        <form method="POST" action="">
            <div class="fix z '.$name.'" onclick="location.href=\'floorplan.heating.php?rollers='.$name.'\';">
                <input type="hidden" name="rollers" value="'.$name.'">';
    if ($stat==100) {
        echo '
                <input type="image" src="/images/arrowgreendown.png" class="i60">';
    } elseif ($stat==0) {
        echo '
                <input type="image" src="/images/arrowgreenup.png" class="i60">';
    } else {
        echo'
                <input type="image" src="/images/circlegrey.png" class="i60">
                <div class="fix center dimmerlevel" style="position:absolute;top:17px;left:-2px;width:70px;letter-spacing:4;" onclick="location.href=\'floorplan.heating.php?rollers='.$name.'\';">';
        if ($d[$name]['m']==2) {
            echo '
                    <font size="5" color="#F00">';
        } elseif ($d[$name]['m']==1) {
            echo '
                    <font size="5" color="#222">';
        } else {
            echo '
                    <font size="5" color="#CCC">';
        }
        echo '
                        '.$stat .'
                    </font>
                </div>';
    }
    if ($d[$name]['m']==2) {
        echo '
                <div class="fix" style="top:2px;left:2px;z-index:-100;background:#fc8000;width:56px;height:56px;border-radius:45px;">
                </div>';
    } elseif ($d[$name]['m']==1) {
        echo '
                <div class="fix" style="top:2px;left:2px;z-index:-100;background:#fff7d8;width:56px;height:56px;border-radius:45px;">
                </div>';
    }
    echo '
		    </div>
	    </form>';
}
function rollery($name,$top,$left,$size,$rotation)
{
    global $d;
    $stat=100-$d[$name]['s'];
    if ($stat<100) {
        $perc=($stat/100)*0.7;
    } else {
        $perc=1;
    }
    if ($rotation=='P') {
        if ($stat==0) {
            $nsize=0;
            $top=$top;
        } elseif ($stat>0) {
            $nsize=($size*$perc)+5;
            if ($nsize>$size) {
                $nsize=$size;
            }
            $top=$top+($size-$nsize);
        } else {
            $nsize=$size;
        }
        echo '
        <div class="fix yellow" style="top:'.
                $top.'px;left:'.
                $left.'px;width:7px;height:'.
                $nsize.'px;">
        </div>';
    } elseif ($rotation=='PL') {
        if ($stat==100) {
            $nsize=0;
            $top=$top;
        } elseif ($stat>0) {
            $nsize=($size*$perc)+5;
            if ($nsize>$size) {
                $nsize=$size;
            }
            $top=$top+($size-$nsize);
        } else {
            $nsize=$size;
        }
        echo '
        <div class="fix yellow" style="top:'.
            $top.'px;left:'.
            $left.'px;width:7px;height:'.
            $nsize.'px;">
        </div>';
    } elseif ($rotation=='L') {
        if ($stat==0) {
            $nsize=0;
        } elseif ($stat>0) {
            $nsize=($size*$perc)+5;
            if ($nsize>$size) {
                $nsize=$size;
            }
        } else {
            $nsize=$size;
        }
        echo '
        <div class="fix yellow" style="top:'.
            $top.'px;left:'.
            $left.'px;width:'.
            $nsize.'px;height:7px;">
        </div>';
    }
}
function bose($ip)
{
    global $d;
    echo '
        <div class="fix bose'.$ip.'" id="bosediv'.$ip.'">
            <a href=\'javascript:navigator_Go("floorplan.bose.php?ip='.$ip.'");\'>
                <img src="images/Bose_'.($d['bose'.$ip]['s']=='On'?'On':'Off').'.png" id="bose'.$ip.'" alt="">
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
            UV: ';
    if ($d['uv']['s']<2) {
        echo '
            <font color="#99EE00">
                <span id="uv">'.number_format($d['uv']['s'], 1, ',', '').'</span>
            </font>';
    } elseif ($d['uv']['s']>=2&&$d['uv']['s']<4) {
        echo '
            <font color="#99CC00">
                <span id="uv">'.number_format($d['uv']['s'], 1, ',', '').'</span>
            </font>';
    } elseif ($d['uv']['s']>=4&&$d['uv']['s']<6) {
        echo '
            <font color="#FFCC00">
                <span id="uv">'.number_format($d['uv']['s'], 1, ',', '').'</span>
            </font>';
    } elseif ($d['uv']['s']>=6&&$d['uv']['s']<8) {
        echo '
            <font color="#FF6600">
                <span id="uv">'.number_format($d['uv']['s'], 1, ',', '').'</span>
            </font>';
    } elseif ($d['uv']['s']>=8) {
        echo '
            <font color="#FF2200">
                <span id="uv">'.number_format($d['uv']['s'], 1, ',', '').'</span>
            </font>';
    }
    echo '
            <br>max:';
    if ($d['uv']['m']<2) {
        echo '
            <font color="#99EE00"><span id="uvmax">'.number_format($d['uv']['m'], 1, ',', '').'</span></font>';
    } elseif ($d['uv']['m']>=2&&$d['uv']['s']<4) {
        echo '
            <font color="#99CC00"><span id="uvmax">'.number_format($d['uv']['m'], 1, ',', '').'</span></font>';
    } elseif ($d['uv']['m']>=4&&$d['uv']['s']<6) {
        echo '
            <font color="#FFCC00"><span id="uvmax">'.number_format($d['uv']['m'], 1, ',', '').'</span></font>';
    } elseif ($d['uv']['m']>=6&&$d['uv']['s']<8) {
        echo '
            <font color="#FF6600"><span id="uvmax">'.number_format($d['uv']['m'], 1, ',', '').'</span></font>';
    } elseif ($d['uv']['m']>=8) {
        echo '
            <font color="#FF2200"><span id="uvmax">'.number_format($d['uv']['m'], 1, ',', '').'</span></font>';
    }

    echo '
	    </div>';
}