<?php
require 'functions.php';
$d=fetchdata();
$s=(int)strftime("%S", TIME);
if ($d['heating']['s']==-2) include '_TC_cooling_airco.php';
elseif ($d['heating']['s']==-1) include '_TC_cooling_passive.php';
elseif ($d['heating']['s']==0) include '_TC_neutral.php';
elseif ($d['heating']['s']>0) include '_TC_heating.php';
$user='cron';
include '_cron10.php';
if($s<9) {
	include '_cron60.php';
	$m=date('i');
	if ($m%2==0) include '_cron120.php';
	if ($m%3==0) include '_cron180.php';
	if ($m%4==0) include '_cron240.php';
	if ($m%5==0) include '_cron300.php';
	if ($m==0) include '_cron3600.php';
}
if (isset($_REQUEST['cron'])) include '_'.$_REQUEST['cron'].'.php';
$db=null;
