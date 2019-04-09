<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
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
$optParams=array('maxResults'=>5,'orderBy'=>'startTime','singleEvents'=>TRUE,'timeMin'=>$timeMin);
$results=$service->events->listEvents($calendarIdTobi, $optParams);
$tobibeitem=false;
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
		echo('Tobi Calendar: '.$event->summary.' '.strftime('%Y-%m-%d %H:%M:%S', $start).' '.strftime('%Y-%m-%d %H:%M:%S', $end));
		echo '<br>';
		if (TIME>$start&&TIME<$end) {
			if (trim(strtolower($event->summary))=='beitem') {
			    $tobibeitem=true;
			}
			echo('Tobi Calendar: '.$event->summary.' '.strftime('%Y-%m-%d %H:%M:%S', $start).' '.strftime('%Y-%m-%d %H:%M:%S', $end));
		}
  	}
}
store('gcal',$tobibeitem);
