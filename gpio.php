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
        if ($d['lichtbadkamer']['s']>0&&past('gasvandaag')<60&&past('watervandaag')<60) {
            $data=$d['douche']['s']+1;
            store('douche', $data, basename(__FILE__).':'.__LINE__);
        } elseif ($d['brander']['s']=='Off'&&$d['living_temp']['s']>$d['living_set']['s']) {
                sw('brander', 'Off',basename(__FILE__).':'.__LINE__);
        }
    } elseif ($gpio==21) {
        store('watervandaag', $d['watervandaag']['s']+1, basename(__FILE__).':'.__LINE__);
        if ($d['lichtbadkamer']['s']>0&&past('gasvandaag')<60&&past('watervandaag')<60) {
            $data=$d['douche']['m']+1;
            storemode('douche', $data, basename(__FILE__).':'.__LINE__, 1);
        } elseif ($d['water']['s']=='On') {
            $data=$d['watertuin']['m']+1;
            storemode('watertuin', $data, basename(__FILE__).':'.__LINE__, 1);
        }
    } elseif ($gpio==19) {
        if ($_REQUEST['action']=='on') {
            store('poort', 'Closed', basename(__FILE__).':'.__LINE__);
        } else {
            store('poort', 'Open', basename(__FILE__).':'.__LINE__);
            fgarage();
            if ($d['voordeur']['s']=='On'&&past('Weg')<120) {
            	sw('voordeur', 'Off');
            }
            sirene('Poort open');
            if ($d['dampkap']['s']=='On') {
                sw('dampkap', 'Off',basename(__FILE__).':'.__LINE__);
            }
        }
    } else {
        die('Unknown');
    }
    if (($gpio==20||$gpio==21)&&($d['lichtbadkamer']['s']>0&&past('gasvandaag')<90&&past('watervandaag')<-90)) {
        $douchegas=$d['douche']['s']*10;
        $douchewater=$d['douche']['m']*1;
        $euro=($douchegas*0.0004)+($douchewater*0.005);
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