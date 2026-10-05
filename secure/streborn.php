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

		} elseif($_GET['key']==3) {

		} elseif($_GET['key']==4) {

		} elseif($_GET['key']==5) {

		} elseif($_GET['key']==6) {
		} elseif($_GET['key']=='aux') {

		} elseif($_GET['key']=='power') {
			sl('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
		}
	} //else 
//	telegram(print_r($_GET,true));
	$data=curl('http://192.168.2.2/ajax.php?bose=101');
	echo $data;
	$data=json_decode($data,true);
	echo '<pre>';print_r($data);echo '</pre>';
	echo updatescore($data['cleantitle'],-1,$data['track_id']);
	
//	telegram(print_r($data,true));
}



function updatescore($cleantitle,$score_change,$track_id) {
	$ch = curl_init('https://secure.egregius.be/spotify/actions.php');
	$data['cleantitle']=$cleantitle;
	$data['score_change']=$score_change;
	$data['track_id']=$track_id;
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	$response = curl_exec($ch);
	echo $response;
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpCode === 200 && $response !== false) {
		return (isset($responseData['status']) && $responseData['status'] === 'ok');
	}
}