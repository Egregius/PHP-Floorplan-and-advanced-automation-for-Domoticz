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
			$data=curl('http://192.168.2.2/ajax.php?bose=101');
			$data=json_decode($data,true);
			if(updatescore($data['cleantitle'],+10,$data['track_id'])) {
				if($d['spotify']->s!='Off') sw('spotify','Off',basename(__FILE__).':'.__LINE__,'cron2');
				if($d['boseliving']->m=='NoScore') {
					Wiim('setPlayerCmd:next');
					usleep(1300000);
					Wiim('setPlayerCmd:seek:45');
				}
			}
		} elseif($_GET['key']==2) {

		} elseif($_GET['key']==3) {

		} elseif($_GET['key']==4) {
			$data=curl('http://192.168.2.2/ajax.php?bose=101');
			$data=json_decode($data,true);
			if(updatescore($data['cleantitle'],-10,$data['track_id'])) {
				if($d['spotify']->s!='Off') sw('spotify','Off',basename(__FILE__).':'.__LINE__,'cron2');
				if($d['boseliving']->m=='NoScore') {
					Wiim('setPlayerCmd:next');
					usleep(1300000);
					Wiim('setPlayerCmd:seek:45');
				}
			}
		} elseif($_GET['key']==5) {

		} elseif($_GET['key']==6) {
			$data=curl('http://192.168.2.2/ajax.php?bose=101');
			$data=json_decode($data,true);
			if(($data['genre']=='EDM'&&$data['score']>=120)||($data['genre']=='POP'&&$data['score']>=195)) {
				if(updatescore($data['cleantitle'],null,$data['track_id'])) {
					if($d['spotify']->s!='Off') sw('spotify','Off',basename(__FILE__).':'.__LINE__,'cron2');
					Wiim('setPlayerCmd:next');
					if($d['boseliving']->m=='NoScore') {
						usleep(1300000);
						Wiim('setPlayerCmd:seek:45');
					}
				}
			}
		} elseif($_GET['key']=='aux') {

		} elseif($_GET['key']=='power') {
			sl('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
		}
	} //else 
//	telegram(print_r($_GET,true));
	
	
//	telegram(print_r($data,true));
}



