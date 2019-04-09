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
$timeMin=strftime("%Y-%m-%d",TIME).'T'.strftime("%H:%M:%S",TIME-7200).'+0000';
$msg='GCAL time: '.$timeMin.PHP_EOL;
$afval='';
$optParams=array('maxResults'=>5,'orderBy'=>'startTime','singleEvents'=>TRUE,'timeMin'=>$timeMin);
$results=$service->events->listEvents($calendarIdMirom, $optParams);
if (count($results->getItems())>0) {
	foreach ($results->getItems() as $event) {
//		print_r($event);
    	if (isset($event->start->dateTime))$start=strtotime($event->start->dateTime);
    	else $start=strtotime($event->start->date);
    	if (isset($event->end->dateTime))$end=strtotime($event->end->dateTime);
    	else $end=strtotime($event->end->date);
    	$msg.=strftime("%Y-%m-%d",$start).' '.$event->summary;
		if (TIME>$start-43200&&TIME<$end-50400) {
			$msg.=' | ACTIVE';
			if (empty($afval)) {
				if ($event->summary=='Restafval')$afval='Restafval';
				elseif ($event->summary=='PMD')$afval='PMD';
				elseif ($event->summary=='Papier & Karton')$afval='Papier';
				elseif ($event->summary=='Ophaling tuinafvalcontainer')$afval='Groenbak';
				else $afval=$event->summary;
			} else {
				if ($event->summary=='Restafval')$afval=str_replace(' buiten zetten','',$afval).' en restafval';
				elseif ($event->summary=='PMD')$afval=str_replace(' buiten zetten','',$afval).' en PMD';
				elseif ($event->summary=='Papier & Karton')$afval=str_replace(' buiten zetten','',$afval).' en papier';
				elseif ($event->summary=='Ophaling tuinafvalcontainer')$afval=str_replace(' buiten zetten','',$afval).' en groenbak';
				else $afval.=' '.$event->summary;
			}
		}
		$msg.=PHP_EOL;
  	}
  	storemode('gcal',$afval);
  	echo $msg.'<hr>'.$afval;
}