<?php
/**
 * Pass2PHP cron trigger script
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'functions.php';
//lg('cron');
$d=fetchdata();
if (isset($_REQUEST['tempcontrol'])) {
	$user='heating';
	/* Temperature control
	-2 = Airco cooling
	-1 = Passive cooling
	0 = Neutral
	1 = Airco heating
	2 = Gas heating
	*/
	$x=0;//Neutral
	if ($d['heatingauto']['s']=='On') {
		if ($d['buiten_temp']['s']>24||$d['minmaxtemp']['m']>25)$x=-2;//Airco cooling
		elseif ($d['buiten_temp']['s']>20||$d['minmaxtemp']['m']>21)$x=-1;//Passive cooling
		elseif ($d['buiten_temp']['s']<20||$d['minmaxtemp']['m']<20||$d['minmaxtemp']['s']<20) $x=2;//Gas heating
		elseif ($d['buiten_temp']['s']<18||$d['minmaxtemp']['m']<18||$d['minmaxtemp']['s']<15) $x=1;//Airco heating
		else $x=0;//Neutral
	}
	//lg('HEATING >>>	heatingauto = '.$d['heatingauto']['s'].'	buiten_temp='.$d['buiten_temp']['s'].'	minmax m='.$d['minmaxtemp']['m'].'	minmax s='.$d['minmaxtemp']['s'].'	jaarteller='.$d['jaarteller']['s'].'	$x='.$x);
	if ($d['heatingauto']['s']=='On'&&$d['heating']['s']!=$x) {
		store('heating', $x, basename(__FILE__).':'.__LINE__);
		$d['heating']['s']=$x;
	}

	if ($x==-2) include ('_TC_aircocooling.php');
	elseif ($x==-1) include ('_TC_passivecooling.php');
	elseif ($x==0) include ('_TC_neutral.php');
	elseif ($x==1) include ('_TC_aircoheating.php');
	elseif ($x==2) include ('_TC_gasheating.php');
}
if (isset($_REQUEST['cron10'])) include '_cron10.php';
if (isset($_REQUEST['cron60'])) include '_cron60.php';
if (isset($_REQUEST['cron120'])) include '_cron120.php';
if (isset($_REQUEST['cron180'])) {
    include '_cron180.php';
    include 'gcal/gcal.php';
    include 'gcal/verlof.php';
    include 'gcal/tobibeitem.php';
    include 'gcal/mirom.php';
}
if (isset($_REQUEST['cron240'])) include '_cron240.php';
if (isset($_REQUEST['cron300'])) include '_cron300.php';
if (isset($_REQUEST['cron3600'])) include '_cron3600.php';