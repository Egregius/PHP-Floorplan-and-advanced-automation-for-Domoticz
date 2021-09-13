<?php
/**
 * Pass2PHP cron trigger script
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/

require 'functions.php';
$d=fetchdata();
$s=(int)strftime("%S", TIME);
if ($d['heating']['s']==-2) include ('_TC_cooling_airco.php');
elseif ($d['heating']['s']==-1) include ('_TC_cooling_passive.php');
elseif ($d['heating']['s']==0) include ('_TC_neutral.php');
elseif ($d['heating']['s']>0) include '_TC_heating.php';
$user='cron';
include '_cron10.php';
if($s<10) {
	include '_cron60.php';
	$m=date('i');
	if ($m%2==0) include '_cron120.php';
	if ($m%3==0) include '_cron180.php';
	if ($m%4==0) include '_cron240.php';
	if ($m%5==0) {
		include '_cron300.php';
		include 'gcal/gcal.php';
		include 'gcal/mirom.php';
	}
	if ($m==0) include '_cron3600.php';
}
if (isset($_REQUEST['cron'])) include '_'.$_REQUEST['cron'].'.php';
