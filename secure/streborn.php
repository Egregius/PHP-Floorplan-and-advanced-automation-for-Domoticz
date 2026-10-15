<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_GET['bose'],$_GET['key'])) {
	header('Access-Control-Allow-Origin: *');
	$user='streborn';
	$status='On';
	require 'functions.php';
	$d=fetchdata();
	if($_GET['bose']==101) {
		if($_GET['key']==1) score(60);
		elseif($_GET['key']==2)  score(100);
		elseif($_GET['key']==3)  score(140);
		elseif($_GET['key']==4)  score(200);
		elseif($_GET['key']==5)  score(320);
		elseif($_GET['key']==6)  score(null);
		elseif($_GET['key']=='aux') {

		} elseif($_GET['key']=='power') {
			sl('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
		}
	} //else 
//	telegram(print_r($_GET,true));
	
	
//	telegram(print_r($data,true));
}