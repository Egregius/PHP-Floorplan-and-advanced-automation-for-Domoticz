<?php
/**
 * Pass2PHP verwarming
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if (!isset($d)) $d=fetchdata();
$user='heating';
/* Heating
0 = Neutral
1 = Cooling
2 = Elec
3 = Elec / Gas
4 = Gas
*/
/* Heating
-2 = Airco cooling
-1 = Passive cooling
0 = Neutral
1 = Airco heating
2 = Gas
*/
$x=0;//Neutral
if ($d['heatingauto']['s']=='On') {
    if ($d['buiten_temp']['s']>24||$d['minmaxtemp']['m']>25)$x=-2;//Airco cooling
    elseif ($d['buiten_temp']['s']>20||$d['minmaxtemp']['m']>21)$x=-1;//Passive cooling
    elseif ($d['buiten_temp']['s']<12||$d['minmaxtemp']['m']<12||$d['minmaxtemp']['s']<12) $x=2;//Gas heating
    elseif ($d['buiten_temp']['s']<18||$d['minmaxtemp']['m']<18||$d['minmaxtemp']['s']<15) $x=1;//Airco heating
    else $x=0;//Neutral
}
//lg('HEATING >>>	heatingauto = '.$d['heatingauto']['s'].'	buiten_temp='.$d['buiten_temp']['s'].'	minmax m='.$d['minmaxtemp']['m'].'	minmax s='.$d['minmaxtemp']['s'].'	jaarteller='.$d['jaarteller']['s'].'	$x='.$x);
if ($d['heatingauto']['s']=='On'&&$d['heating']['s']!=$x) {
	store('heating', $x, basename(__FILE__).':'.__LINE__);
	$d['heating']['s']=$x;
}

if ($x==-2) include ('_tempcontrol_aircocooling.php');
elseif ($x==-1) include ('_tempcontrol_passivecooling.php');
elseif ($x==0) include ('_tempcontrol_neutral.php');
elseif ($x==1) include ('_tempcontrol_aircoheating.php');
elseif ($x==2) include ('_tempcontrol_gasheating.php');