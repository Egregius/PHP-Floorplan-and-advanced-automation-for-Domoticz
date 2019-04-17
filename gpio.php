<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * This file is called by a secondary Domoticz running on a Rasperry Pi
 * It handles some GPIO's that has sensors on it for gas and water meter counting
 * Also the garage door is connected to it.
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if (isset($_REQUEST['gpio'])) {
    include 'secure/functions.php';
    $gpio=$_REQUEST['gpio'];
    if ($gpio==20) {
        store('gasvandaag', $d['gasvandaag']['s']+1);
        if ($d['lichtbadkamer']['s']>0&&$d['gasvandaag']['t']>TIME-60&&$d['watervandaag']['t']>TIME-60) {
            $data=$d['douche']['s']+1;
            store('douche', $data);
        } elseif ($d['brander']['s']=='Off'&&$d['living_temp']['s']>$d['living_set']['s']) {
                sw('brander', 'Off');
        }
    } elseif ($gpio==21) {
        store('watervandaag', $d['watervandaag']['s']+1);
        if ($d['lichtbadkamer']['s']>0&&$d['gasvandaag']['t']>TIME-60&&$d['watervandaag']['t']>TIME-60) {
            $data=$d['douche']['m']+1;
            storemode('douche', $data);
        }
    } elseif ($gpio==19) {
        if ($_REQUEST['action']=='on') {
            if ($d['poort']['s']!='Closed') {
                store('poort', 'Closed');
            }
        } else {
            if ($d['poort']['s']!='Open') {
                store('poort', 'Open');
            }
            if ($d['Weg']['s']==0&&$d['zon']['s']<100&&$d['auto']['s']=='On'&&$d['garage']['s']=='Off'&&$d['garageled']['s']=='Off') {
                sw('garageled', 'On');
            } elseif ($d['Weg']>0&&$d['auto']['s']&&past('Weg')>178&&$d['poortrf']['s']=='Off') {
                storemode('Weg', TIME);
                sw('sirene', 'On');
                shell_exec('secure/ios.sh "Poort open" > /dev/null 2>/dev/null &');
                telegram('Poort open om '.strftime("%k:%M:%S", TIME), false, 2);
            }
            if ($d['dampkap']['s']=='On') {
                sw('dampkap', 'Off');
            }
        }
    } else {
        die('Unknown');
    }
    if (($gpio==20||$gpio==21)&&($d['lichtbadkamer']['s']>0&&$d['gasvandaag']['t']>TIME-90&&$d['watervandaag']['t']>TIME-90)) {
        $douchegas=$d['douche']['s']*10;
        $douchewater=$d['douche']['m']*1;
        $euro=($douchegas*0.00065)+($douchewater*0.0055);
        lg('Douche = '.round($euro*100));
        if ($euro>0&&round($euro*100)%450==0) {
            douchewarn($euro, 85);
        } elseif ($euro>0&&round($euro*100)%400==0) {
            douchewarn($euro, 80);
        } elseif ($euro>0&&round($euro*100)%350==0) {
            douchewarn($euro, 75);
        } elseif ($euro>0&&round($euro*100)%300==0) {
            douchewarn($euro, 70);
        } elseif ($euro>0&&round($euro*100)%250==0) {
            douchewarn($euro, 65);
        } elseif ($euro>0&&round($euro*100)%200==0) {
            douchewarn($euro, 60);
        } elseif ($euro>0&&round($euro*100)%150==0) {
            douchewarn($euro, 55);
        } elseif ($euro>0&&round($euro*100)%100==0) {
            douchewarn($euro, 50);
        } elseif ($euro>0&&round($euro*100)%50==0) {
            douchewarn($euro, 45);
        }
    }
    echo 'ok';
}