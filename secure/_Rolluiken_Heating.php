<?php
/**
 * Pass2PHP Control roller while on vacation
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$boven=array('Rtobi','Ralex','RkamerL','RkamerR');
$beneden=array('Rbureel','RkeukenL','RkeukenR');
$benedenall=array('Rliving','Rbureel','RkeukenL','RkeukenR');
if ($d['minmaxtemp']['s']>20||$d['minmaxtemp']['m']>22) $warm=true; else $warm=false;
if ($d['minmaxtemp']['s']<5&&$d['minmaxtemp']['m']<5) $koud=true; else $koud=false;
if (TIME>=$d['civil_twilight']['s']&&TIME<=$d['civil_twilight']['m']) $dag=true; else $dag=false;

$zon=$d['zon']['s'];

if ($d['Weg']['s']<3) {
	if ($d['auto']['s']=='On') {
		if (TIME>=strtotime('5:30')&&TIME<strtotime('10:00')) {
			$dow=date("w");
			if($dow==0||$dow==6) {
				if ($d['RkamerL']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerR']['s']>0&&TIME>=strtotime('7:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Rtobi']['s']>0&&TIME>=strtotime('9:00')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Ralex']['s']>0&&TIME>=strtotime('9:00')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			} else {
				if ($d['RkamerL']['s']>0&&TIME>=strtotime('6:00')) sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
				if ($d['RkamerR']['s']>0&&TIME>=strtotime('6:00')) sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Rtobi']['s']>0&&TIME>=strtotime('8:00')) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
				if ($d['Ralex']['s']>0&&TIME>=strtotime('9:00')) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			}
			if ($dag==true||$d['pirhall']['s']=='On') {
				if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Rtobi']['s']>0&&($d['deurtobi']['s']=='Open'||$d['tobi']['s']>0)) sl('Rtobi', 0, basename(__FILE__).':'.__LINE__);
				if (($d['Weg']['s']!=1||$d['pirhall']['s']=='On')&&$d['Ralex']['s']>0&&($d['deuralex']['s']=='Open'||$d['alex']['s']>0)) sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
			}
			if ($dag==true&&$zon==0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
				foreach ($beneden as $i) {
					if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
				if ($d['Rliving']['s']>0&&$d['tv']['s']=='Off') sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
			} elseif ($dag==true&&$zon>0&&$d['Weg']['s']!=1&&$d['tv']['s']=='Off') {
				foreach ($beneden as $i) {
					if ($d[$i]['s']>0) sl($i, 0, basename(__FILE__).':'.__LINE__);
				}
				if ($d['Rliving']['s']>0) sl('Rliving', 0, basename(__FILE__).':'.__LINE__);
			}
		}

		elseif (TIME>=strtotime('15:00')&&TIME<strtotime('17:00')) {
			if ($d['buiten_temp']['s']<8) {
				if ($d['raamalex']['s']=='Open'&&$d['alex_temp']['s']<12&&past('raamalex')>14400&&$d['Ralex']['s']<80) {
					sl('Ralex', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamalex']['s']=='Open'&&$d['alex_temp']['s']<16&&past('raamalex')>14400&&$d['Ralex']['s']<60) {
					sl('Ralex', 60, basename(__FILE__).':'.__LINE__);
				}
				if ($d['raamtobi']['s']=='Open'&&$d['tobi_temp']['s']<12&&past('raamtobi')>14400&&$d['Rtobi']['s']<80) {
					sl('Rtobi', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamtobi']['s']=='Open'&&$d['tobi_temp']['s']<16&&past('raamtobi')>14400&&$d['Rtobi']['s']<60) {
					sl('Rtobi', 60, basename(__FILE__).':'.__LINE__);
				}
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<12&&past('raamkamer')>14400&&$d['RkamerL']['s']<80) {
					sl('RkamerL', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['RkamerL']['s']<60) {
					sl('RkamerL', 100, basename(__FILE__).':'.__LINE__);
				}
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<12&&past('raamkamer')>14400&&$d['RkamerR']['s']<80) {
					sl('RkamerR', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['RkamerR']['s']<60) {
					sl('RkamerR', 60, basename(__FILE__).':'.__LINE__);
				}
			}
		}

		elseif (TIME>=strtotime('17:00')&&TIME<strtotime('22:00')) {
			if ($zon==0) {
				if ($d['alex_temp']['s']<16&&$d['Ralex']['s']<80) {
					sl('Ralex', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamalex']['s']=='Open'&&$d['alex_temp']['s']<16&&past('raamalex')>14400&&$d['Ralexi']['s']<60) {
					sl('Ralex', 60, basename(__FILE__).':'.__LINE__);
				}
				if ($d['tobi_temp']['s']<14&&$d['Rtobi']['s']<80) {
					sl('Rtobi', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamtobi']['s']=='Open'&&$d['tobi_temp']['s']<16&&past('raamtobi')>14400&&$d['Rtobi']['s']<60) {
					sl('Rtobi', 60, basename(__FILE__).':'.__LINE__);
				}
				if ($d['kamer_temp']['s']<14&&$d['RkamerL']['s']<80) {
					sl('RkamerL', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['RkamerL']['s']<60) {
					sl('RkamerL', 100, basename(__FILE__).':'.__LINE__);
				}
				if ($d['kamer_temp']['s']<14&&$d['RkamerR']['s']<80) {
					sl('RkamerR', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['RkamerR']['s']<60) {
					sl('RkamerR', 60, basename(__FILE__).':'.__LINE__);
				}
				if ($d['Weg']['s']>0) {
					foreach ($benedenall as $i) {
						if ($d[$i]['s']<60) sl($i, 100, basename(__FILE__).':'.__LINE__);
					}
				} else {
					if ($dag==false) {
						foreach ($beneden as $i) {
							if ($d[$i]['s']<60) sl($i, 100, basename(__FILE__).':'.__LINE__);
						}
					}
				}
			} elseif ($d['buiten_temp']['s']<14) {
				if ($d['raamalex']['s']=='Open'&&$d['alex_temp']['s']<14&&past('raamalex')>14400&&$d['Ralex']['s']<80) {
					sl('Ralex', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamalex']['s']=='Open'&&$d['alex_temp']['s']<16&&past('raamalex')>14400&&$d['Ralexi']['s']<60) {
					sl('Ralex', 60, basename(__FILE__).':'.__LINE__);
				}
				if ($d['raamtobi']['s']=='Open'&&$d['tobi_temp']['s']<14&&past('raamtobi')>14400&&$d['Rtobi']['s']<80) {
					sl('Rtobi', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamtobi']['s']=='Open'&&$d['tobi_temp']['s']<16&&past('raamtobi')>14400&&$d['Rtobi']['s']<60) {
					sl('Rtobi', 60, basename(__FILE__).':'.__LINE__);
				}
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<14&&past('raamkamer')>14400&&$d['RkamerL']['s']<80) {
					sl('RkamerL', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['RkamerL']['s']<60) {
					sl('RkamerL', 100, basename(__FILE__).':'.__LINE__);
				}
				if ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<14&&past('raamkamer')>14400&&$d['RkamerR']['s']<80) {
					sl('RkamerR', 100, basename(__FILE__).':'.__LINE__);
				}elseif ($d['raamkamer']['s']=='Open'&&$d['kamer_temp']['s']<16&&past('raamkamer')>14400&&$d['RkamerR']['s']<60) {
					sl('RkamerR', 60, basename(__FILE__).':'.__LINE__);
				}
			}
		}

		elseif (TIME>=strtotime('22:00')||TIME<strtotime('6:00')) {
			if ($d['Weg']['s']>0) {
				foreach ($benedenall as $i) {
					if ($d[$i]['s']<60&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
				foreach ($boven as $i) {
					if ($d[$i]['s']<60&&past($i)>7200) sl($i, 100, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
} elseif ($d['auto']['s']=='On'&&$d['Weg']['s']==3) {
	include('_Rolluiken_Vakantie.php');
}
