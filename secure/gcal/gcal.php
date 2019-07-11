<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
require_once 'google-api-php-client/vendor/autoload.php';
$username='gcal';
function getClient()
{
    global $calendarApp;
	$client=new Google_Client();
	$client->setAuthConfig('/var/www/service-account-credentials.json');
	$client->setApplicationName($calendarApp);
	$client->setScopes(['https://www.googleapis.com/auth/calendar.readonly']);
	return $client;
}
$client=getClient();
$service=new Google_Service_Calendar($client);
$timeMin=strftime("%Y-%m-%d",TIME).'T'.strftime("%H:%M:%S",TIME-7200).'+0000';
$msg='GCAL time: '.$timeMin.'<br>';
$optParams=array(
    'maxResults'=>10,
    'orderBy'=>'startTime',
    'singleEvents'=>TRUE,
    'timeMin'=>$timeMin
);
$results=$service->events->listEvents($calendarId, $optParams);
if (count($results->getItems())>0) {
	foreach ($results->getItems() as $event) {
		if (isset($event->start->dateTime)) {
		    $start=strtotime($event->start->dateTime);
		}
		if (isset($event->end->dateTime)) {
		    $end=strtotime($event->end->dateTime);
		}
		if (empty($start)) {
		    $start=strtotime($event->start->date);
		}
		if (empty($end)) {
		    $end=strtotime($event->end->date);
		}
		if (TIME>$start&&TIME<$end) {
			echo 'Executing<br>';
			$user='GCal';
			$item=explode(" ", $event->summary);
			$action=strtolower($item[0]);
			if ($action=="licht") {
			    $action="schakel";
			} elseif ($action=="dim") {
			    $action="dimmer";
			} elseif ($action=="opstaan") {
			    $action="wake";
			} elseif ($action=="slaap") {
			    $action="sleep";
			} elseif ($action=="set") {
			    $action="setpoint";
			}
			$place=strtolower($item[1]);
			if (isset($item[2])) {
				$detail=strtolower($item[2]);
				if ($detail=="on") {
				    $detail="On";
				} elseif ($detail=="off") {
				    $detail="Off";
				} elseif ($detail=="aan") {
				    $detail="On";
				} elseif ($detail=="uit") {
				    $detail="Off";
				}
			}
			if ($action=="wake") {
				if ($d[$place]['s']=='Off') {
				    $d[$place]['s']=0;
				}
				if ($place=='kamer') {
					if ($d['kamer']['s']<30
					    &&$d['kamer']['m']!=2
					    &&$d['Ralex']['m']==2
					    &&past('kamer')>300
					) {
						if ($d['zon']['s']>0||$d['auto']['m']) {
							if ($d['RkamerR']['s']>0) {
								sl('RkamerR', 0, basename(__FILE__).':'.__LINE__);
							}
							if ($d['RkamerL']['s']>0) {
								sl('RkamerL', 0, basename(__FILE__).':'.__LINE__);
							}
						} else {
							storemode($place,2);
							sl($place, ($d[$place]['s']+1), basename(__FILE__).':'.__LINE__);
						}
					}
				} else {
					if ($d[$place]['s']<30
					    &&$d[$place]['m']!=2
					    &&past($place)>300
					) {
						storemode($place,2);
						sl($place, ($d[$place]['s']+1), basename(__FILE__).':'.__LINE__);
					}
				}
			} elseif ($action=="sleep") {
				if ($d[$place]['s']>0&&$d[$place]['m']!=1) {
					storemode($place,1);
					sl($place,$d[$place]['s']-1, basename(__FILE__).':'.__LINE__);
				}
			} elseif ($action=="dimmer") {
				if ($d[$place]['s']=='Off') {
				    $d[$place]['s']=0;
				}
				if ($d[$place]['s']!=$detail&&past($place)>300) {
				    sl($place,$detail, basename(__FILE__).':'.__LINE__);
				}
			} elseif ($action=="schakel"){
				if ($d[$place]['s']!=$detail&&past($place)>300){
					sw($place,$detail, basename(__FILE__).':'.__LINE__);
				}
			}elseif ($action=="setpoint"){
				storemode($place.'_set',2);
				if ($d[$place]['s']!=$detail&&past($place)>300) {
				    ud($place.'_set',0,$detail, basename(__FILE__).':'.__LINE__);
				}
			}
		}
  	}
}