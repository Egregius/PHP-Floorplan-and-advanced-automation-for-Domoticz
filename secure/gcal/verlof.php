<?php
/**
 * Pass2PHP
 * php version 7.0.33
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
echo '<pre>';
require_once 'google-api-php-client/vendor/autoload.php';
$client=getClient();
$service=new Google_Service_Calendar($client);
$timeMin=strftime("%Y-%m-%d", TIME).'T'.strftime("%H:%M:%S", TIME-7200).'+0000';//Winteruur
$optParams=array('maxResults'=>10,'orderBy'=>'startTime','singleEvents'=>TRUE,'timeMin'=>$timeMin);
$results=$service->events->listEvents($calendarIdVerlof, $optParams);
$verlof=false;
if (count($results->getItems())>0) {
	foreach ($results->getItems() as $event) {
		if (isset($event->start->dateTime)) {
		    $start=strtotime($event->start->dateTime);
		} else {
		    $start=strtotime($event->start->date);
		}
		if (isset($event->end->dateTime)) {
		    $end=strtotime($event->end->dateTime);
		} else {
		    $end=strtotime($event->end->date);
		}
		if (TIME>$start&&TIME<$end) {
			if (trim(strtolower($event->summary))=='verlof') {
			    $verlof=true;
			}
		}
  	}
}
store('verlof', $verlof);
lg('Updating verlof '.$verlof);
