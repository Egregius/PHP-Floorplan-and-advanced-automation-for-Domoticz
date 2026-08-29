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
		if($_GET['key']==1) {
		} elseif($_GET['key']==2) {
			require('pass2php/8beneden_1.php');
		} elseif($_GET['key']==3) {
			require('pass2php/8beneden_3.php');
		} elseif($_GET['key']==4) {
		} elseif($_GET['key']==5) {
			require('pass2php/8beneden_5.php');
		} elseif($_GET['key']==6) {
		} elseif($_GET['key']=='aux') {
		} elseif($_GET['key']=='power') {
		}
	} else telegram(print_r($_GET,true));
}