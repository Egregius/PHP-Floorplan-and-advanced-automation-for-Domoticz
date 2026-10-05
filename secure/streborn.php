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
			updatescore($data['cleantitle'],+1,$data['track_id']);
		} elseif($_GET['key']==2) {

		} elseif($_GET['key']==3) {

		} elseif($_GET['key']==4) {
			$data=curl('http://192.168.2.2/ajax.php?bose=101');
			$data=json_decode($data,true);
			updatescore($data['cleantitle'],-1,$data['track_id']);

		} elseif($_GET['key']==5) {

		} elseif($_GET['key']==6) {
			$data=curl('http://192.168.2.2/ajax.php?bose=101');
			$data=json_decode($data,true);
			$return=updatescore($data['cleantitle'],null,$data['track_id']);
			$return=json_decode($return,true);
			Wiim('setPlayerCmd:next');
		} elseif($_GET['key']=='aux') {

		} elseif($_GET['key']=='power') {
			sl('boseliving', 'Off', basename(__FILE__).':'.__LINE__);
		}
	} //else 
//	telegram(print_r($_GET,true));
	
	
//	telegram(print_r($data,true));
}



function updatescore($cleantitle, $score_change, $track_id) {
	$ch = curl_init('https://secure.egregius.be/spotify/actions.php');
	$data = [
		'cleantitle' => $cleantitle,
		'score_change' => $score_change,
		'track_id' => $track_id
	];
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
	$response = curl_exec($ch);
	echo $response;
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpCode === 200 && $response !== false) {
		$responseData = json_decode($response, true);
		return (isset($responseData['status']) && $responseData['status'] === 'ok');
	}
	return false;
}