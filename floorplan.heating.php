<?php
/**
 * Pass2PHP
 * php version 7.3.5-1
 *
 * This flooplan handles everything that has to do with heating and rollers.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'secure/functions.php';
require 'secure/authentication.php';
if ($home) {
    $d=fetchdata();
    createheader('floorplanheating');
    if (isset($_POST['Setpoint'])) {
        if (isset($_POST['resetauto'])) {
            storemode($_POST['Naam'].'_set', 0);
            lgsql($user, $_POST['Naam'].'_mode', 'reset auto');
            lg(' (Set Setpoint) | '.$user.' set '.$_POST['Naam'].' to Automatic');
            $d[$_POST['Naam'].'_set']['m']=0;
            include 'secure/_verwarming.php';
        } else {
            store($_POST['Naam'].'_set', $_POST['Actie']);
            lgsql($user, $_POST['Naam'].'_set', $_POST['Actie']);
            storemode($_POST['Naam'].'_set', 2);
            lgsql($user, $_POST['Naam'].'_mode', 2);
            lg(' (Set Setpoint) | '.$user.' set '.$_POST['Naam'].' to '.$_POST['Actie'].'°');
            $d[$_POST['Naam'].'_set']['s']=$_POST['Actie'];
            $d[$_POST['Naam'].'_set']['m']=2;
            include 'secure/_verwarming.php';
        }
    }
    if (isset($_POST['Roller'])
        &&isset($_POST['Naam'])
        &&!isset($_POST['mode'])
    ) {
        if (isset($_POST['Rollerlevelon_x'])) {
            sl($_POST['Naam'], 100, 'Roller');
            if ($d[$_POST['Naam']]['m']==0) {
                storemode($_POST['Naam'], 1);
            }
        } elseif (isset($_POST['Rollerleveloff_x'])) {
            if ($_POST['Naam']=='Rlivingzzz') {
                sl($_POST['Naam'], 8, 'Roller');
            } else {
                sl($_POST['Naam'], 0, 'Roller');
            }
            if ($d[$_POST['Naam']]['m']==0) {
                storemode($_POST['Naam'], 1);
            }
        } else {
            if ($_POST['Naam']=='Rlivingzzz'&&$_POST['Rollerlevel']<8) {
                sl($_POST['Naam'], 8, 'Roller');
            }
            sl($_POST['Naam'], $_POST['Rollerlevel'], 'Roller');
            if ($d[$_POST['Naam']]['m']==0) {
                storemode($_POST['Naam'], 1);
            }
        }
    } elseif (isset($_POST['Roller'])
        &&isset($_POST['Naam'])
        &&isset($_POST['mode'])
    ) {
        if ($_POST['mode']=='Manueel') {
            storemode($_POST['Naam'], 1);
        } else {
            storemode($_POST['Naam'], 0);
        }
    }
    if (isset($_REQUEST['rollers'])) {
        handlerollers();
    }
    if (isset($_REQUEST['verdieping'])) {
        handleverdieping();
    }
    if (isset($_REQUEST['luifel'])) {
        handleluifel();
    }
    if (isset($_REQUEST['heating'])) {
        handleheating();
    }
    if (isset($_REQUEST['SetSetpoint'])) {
        handlesetsetpoint();
    }
    echo '
    <body class="floorplan">
        <div id="placeholder"></div>';
    luifel('luifel', $d['luifel']['s']);
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
        </div>';
    sidebar();
}
?>

    </body>
</html>