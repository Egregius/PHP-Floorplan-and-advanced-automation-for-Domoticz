<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
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
    $d=fetchdata();
    $gpio=$_REQUEST['gpio'];
    if ($gpio==20) {
        store('gasvandaag', $d['gasvandaag']['s']+1, basename(__FILE__).':'.__LINE__);
        if ($d['lichtbadkamer']['s']>0&&past('gasvandaag')<80&&past('watervandaag')<80) {
            store('douche', $d['douche']['s']+1, basename(__FILE__).':'.__LINE__);
        } elseif ($d['brander']['s']=='Off'&&$d['living_temp']['s']>$d['living_set']['s']) {
                sw('brander', 'Off',basename(__FILE__).':'.__LINE__);
        }
    } elseif ($gpio==21) {
        store('watervandaag', $d['watervandaag']['s']+1, basename(__FILE__).':'.__LINE__);
        if ($d['lichtbadkamer']['s']>0&&past('gasvandaag')<80&&past('watervandaag')<80) {
            storemode('douche', $d['douche']['m']+1, basename(__FILE__).':'.__LINE__, 1);
        } elseif ($d['water']['s']=='On') {
            storemode('watertuin', $d['watertuin']['m']+1, basename(__FILE__).':'.__LINE__, 1);
        }
    } elseif ($gpio==19) {
        if ($_REQUEST['action']=='on') {
            store('poort', 'Closed', basename(__FILE__).':'.__LINE__);
        } else {
            store('poort', 'Open', basename(__FILE__).':'.__LINE__);
            fgarage();
            sw('voordeur', 'Off');
            sirene('Poort open');
            if ($d['dampkap']['s']=='On') {
                sw('dampkap', 'Off',basename(__FILE__).':'.__LINE__);
            }
        }
    } else {
        die('Unknown');
    }
    if (($gpio==20||$gpio==21)&&($d['lichtbadkamer']['s']>0&&past('gasvandaag')<80&&past('watervandaag')<80)) {
        $douchegas=$d['douche']['s']*10;
        $douchewater=$d['douche']['m']*1;
        $euro=($douchegas*0.0004)+($douchewater*0.005);
        lg('Douche = '.round($euro*100));
        if ($euro>0&&round($euro*100)%450==0) {
            douchewarn($euro, 69);
        } elseif ($euro>0&&round($euro*100)%400==0) {
            douchewarn($euro, 66);
        } elseif ($euro>0&&round($euro*100)%350==0) {
            douchewarn($euro, 63);
        } elseif ($euro>0&&round($euro*100)%300==0) {
            douchewarn($euro, 60);
        } elseif ($euro>0&&round($euro*100)%250==0) {
            douchewarn($euro, 57);
        } elseif ($euro>0&&round($euro*100)%200==0) {
            douchewarn($euro, 54);
        } elseif ($euro>0&&round($euro*100)%150==0) {
            douchewarn($euro, 51);
        } elseif ($euro>0&&round($euro*100)%100==0) {
            douchewarn($euro, 48);
        } elseif ($euro>0&&round($euro*100)%50==0) {
            douchewarn($euro, 45);
        }
    }
    echo 'ok';
}