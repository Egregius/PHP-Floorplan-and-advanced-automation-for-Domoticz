<?php
/**
 * Pass2PHP
 * php version 7.3.9-1
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
            $d['douche']['s']=$d['douche']['s']+1;
        } elseif ($d['brander']['s']=='Off'&&$d['living_temp']['s']>$d['living_set']['s']) {
                sw('brander', 'Off',basename(__FILE__).':'.__LINE__);
        }
    } elseif ($gpio==21) {
        store('watervandaag', $d['watervandaag']['s']+1, basename(__FILE__).':'.__LINE__);
        if ($d['lichtbadkamer']['s']>0&&past('gasvandaag')<80&&past('watervandaag')<80) {
            storemode('douche', $d['douche']['m']+1, basename(__FILE__).':'.__LINE__, 1);
            $d['douche']['m']=$d['douche']['m']+1;
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
        $euro=($d['douche']['s']*10*0.004)+($d['douche']['m']*0.005);
		$eurocent=round($euro*100, 0);
        lg('Douche = '.$euro.', round='.roundUpToAny($euro*100, 5));
        if ($eurocent>0) {
			if ($eurocent%450==0) douchewarn($eurocent, 41);
			elseif ($eurocent%400==0) douchewarn($eurocent, 41);
			elseif ($eurocent%350==0) douchewarn($eurocent, 41);
			elseif ($eurocent%300==0) douchewarn($eurocent, 41);
			elseif ($eurocent%250==0) douchewarn($eurocent, 41);
			elseif ($eurocent%200==0) douchewarn($eurocent, 41);
			elseif ($eurocent%150==0) douchewarn($eurocent, 41);
			elseif ($eurocent%100==0) douchewarn($eurocent, 38);
			elseif ($eurocent%50==0) douchewarn($eurocent, 35);
		}
    }
    echo 'ok';
}