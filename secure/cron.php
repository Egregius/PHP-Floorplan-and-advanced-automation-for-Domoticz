<?php
/**
 * Pass2PHP cron trigger script
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require 'functions.php';
$d=fetchdata();
if (isset($_REQUEST['cron10'])) {
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
		if ($d['buiten_temp']['s']>22&&$d['minmaxtemp']['m']>25)$x=-2;//Airco cooling
		elseif ($d['buiten_temp']['s']>19&&$d['minmaxtemp']['m']>21)$x=-1;//Passive cooling
		elseif ($d['buiten_temp']['s']<4||$d['minmaxtemp']['m']<4||$d['minmaxtemp']['s']<4) $x=2;//Gas heating
		elseif ($d['buiten_temp']['s']<16||$d['minmaxtemp']['m']<17||$d['minmaxtemp']['s']<15) $x=1;//Airco heating
		else $x=0;//Neutral
	}
	//lg('HEATING >>>	heatingauto = '.$d['heatingauto']['s'].'	buiten_temp='.$d['buiten_temp']['s'].'	minmax m='.$d['minmaxtemp']['m'].'	minmax s='.$d['minmaxtemp']['s'].'	jaarteller='.$d['jaarteller']['s'].'	$x='.$x);
	if ($d['heatingauto']['s']=='On'&&$d['heating']['s']!=$x) {
		store('heating', $x, basename(__FILE__).':'.__LINE__);
		$d['heating']['s']=$x;
		lg('HEATING >>>	heatingauto = '.$d['heatingauto']['s'].'	buiten_temp='.$d['buiten_temp']['s'].'	minmax m='.$d['minmaxtemp']['m'].'	minmax s='.$d['minmaxtemp']['s'].'	jaarteller='.$d['jaarteller']['s'].'	$x='.$x);
	}
	    if ($d['heating']['s']==-2) include ('_TC_aircocooling.php');
	elseif ($d['heating']['s']==-1) include ('_TC_passivecooling.php');
	elseif ($d['heating']['s']==0) include ('_TC_neutral.php');
	elseif ($d['heating']['s']==1) include ('_TC_aircoheating.php');
	elseif ($d['heating']['s']==2) include ('_TC_gasheating.php');
	include '_cron10.php';
}
if (isset($_REQUEST['cron60'])) include '_cron60.php';
if (isset($_REQUEST['cron120'])) include '_cron120.php';
if (isset($_REQUEST['cron180'])) include '_cron180.php';
if (isset($_REQUEST['cron240'])) include '_cron240.php';
if (isset($_REQUEST['cron300'])) {
	include '_cron300.php';
    include 'gcal/gcal.php';
    include 'gcal/verlof.php';
    include 'gcal/tobibeitem.php';
    include 'gcal/mirom.php';
}
if (isset($_REQUEST['cron3600'])) include '_cron3600.php';