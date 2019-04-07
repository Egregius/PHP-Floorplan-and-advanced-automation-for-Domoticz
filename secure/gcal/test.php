<?php
error_reporting(E_ALL);ini_set("display_errors","on");
require_once 'google-api-php-client/vendor/autoload.php';
function getClient(){
	$client=new Google_Client();
	$client->setAuthConfig('/var/www/service-account-credentials.json');
	$client->setApplicationName("homeegregius");
	$client->setScopes(['https://www.googleapis.com/auth/calendar.readonly']);
	return $client;
}
$DST=true;
$client=getClient();
$service=new Google_Service_Calendar($client);
$timeMin=strftime("%Y-%m-%d",TIME).'T'.strftime("%H:%M:%S",TIME-($DST==true?3600:0)).'+0000';//Winteruur
$optParams = array('maxResults' => 10,'orderBy' => 'startTime','singleEvents' => TRUE,'timeMin' => $timeMin);
$results = $service->events->listEvents($calendarId, $optParams);
echo 'timeMin = '.$timeMin.'<hr>';
echo '<pre>';
if(count($results->getItems())>0){
	foreach($results->getItems() as $event){
		if(isset($event->start->dateTime))$start=strtotime($event->start->dateTime);else $start=strtotime($event->start->date);
		if(isset($event->end->dateTime))$end=strtotime($event->end->dateTime);else $end=strtotime($event->end->date);
		if(TIME>$start&&TIME<$end){
			if(trim(strtolower($event->summary))=='beitem')$tobibeitem=true;
			echo $event->summary.' '.strftime('%Y-%m-%d %H:%M:%S',$start).' '.strftime('%Y-%m-%d %H:%M:%S',$end);
		}
  	}
}
echo '</pre>';
