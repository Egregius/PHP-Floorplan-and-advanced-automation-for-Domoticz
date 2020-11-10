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
require 'secure/functions.php';
echo __FILE__.'-'.__LINE__.'<br>';
if (isset($_REQUEST['token'])&&$_REQUEST['token']==$ifttttoken) {
	$d=fetchdata();
	if (isset($_REQUEST['RING'])) {
		$last=apcu_fetch($_REQUEST['kind']);
		$new=strtotime($_REQUEST['time']);
		if ($last!=$new) {
			if ($new>$last) {
				apcu_store($_REQUEST['kind'], $new);
				print_r($_REQUEST);
				if ($_REQUEST['kind']=='motion') {
					telegram('Python RING motion '.strftime("%T", $_SERVER['REQUEST_TIME']).' '.$new);
					if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
						sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
					}
		
					if ($d['Weg']['s']==0&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90) {
						shell_exec('secure/picams.sh Beweging > /dev/null 2>/dev/null &');
						if ($d['lgtv']['s']=='On') {
						    shell_exec('python3 secure/lgtv.py -c send-message -a "Beweging Ring" 192.168.2.27');
						}
						if (past('Xbel')>60) {
							if ($d['Xvol']['s']!=5) {
							    sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
							}
							sl('Xbel', 30, basename(__FILE__).':'.__LINE__);
						}
					}
				} elseif ($_REQUEST['kind']=='ding') {
					telegram('Python RING ding '.strftime("%T", $_SERVER['REQUEST_TIME']).' '.$new, true, 2);
					if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
						sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
					}
					shell_exec('secure/picams.sh DEURBEL > /dev/null 2>/dev/null &');
					if ($d['Weg']['s']==0&&$d['deurvoordeur']['s']=='Closed') {
						sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
						if ($d['lgtv']['s']=='On') {
							shell_exec('python3 ../lgtv.py -c send-message -a "DEURBEL" 192.168.2.27 > /dev/null 2>/dev/null &');
						}
						if ($d['Xvol']['s']!=40) {
						    sl('Xvol', 40, basename(__FILE__).':'.__LINE__);
						    usleep(10000);
						}
						sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
						sleep(2);
						sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
					}
				}
			}
		}
		if ($_REQUEST['battery']<60) {
			alert(
                            'BatterijRingDeurbel',
                            'Batterij Ring Deurbel '.$_REQUEST['battery'].' %',
                            172800
                        );
                }
	} elseif (isset($_REQUEST['ring'])&&$_REQUEST['ring']=='Beweging') {
		echo __FILE__.'-'.__LINE__.'<br>'; 
		$last=apcu_fetch($_REQUEST['ring']);
		echo __FILE__.'-'.__LINE__.' last '.$last.'<br>';
		echo __FILE__.'-'.__LINE__.' time '.$_REQUEST['time'].'<br>';
		$split = preg_split('/[\ \n\,]+/', $_REQUEST['time']);
		echo '<pre>';print_r($split);echo '</pre>';
		$new=strtotime($_REQUEST['time']);
		echo __FILE__.'-'.__LINE__.' new '.$new.'<br>';
		if ($last!=$new) {
			if ($new>$last) {
				apcu_store($_REQUEST['ring'], $new);
				echo 'Motion';
				telegram('IFTTT RING '.strftime("%d/%m/%y %T", $_SERVER['REQUEST_TIME']));
				if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
					sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
				}
		
				if ($d['Weg']['s']==0&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90) {
					shell_exec('secure/picams.sh Beweging > /dev/null 2>/dev/null &');
					if ($d['lgtv']['s']=='On') {
					    shell_exec('python3 secure/lgtv.py -c send-message -a "Beweging Ring" 192.168.2.27');
					}
					if (past('Xbel')>60) {
						if ($d['Xvol']['s']!=5) {
						    sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
						}
						sl('Xbel', 30, basename(__FILE__).':'.__LINE__);
					}
				}
			}
		}
	} elseif (isset($_REQUEST['ring'])&&$_REQUEST['ring']=='DEURBEL') {
		echo 'DEURBEL';
		$last=apcu_fetch($_REQUEST['ring']);
		$new=strtotime($_REQUEST['time']);
		if ($last!=$new) {
			if ($new>$last) {
				apcu_store($_REQUEST['ring'], $new);
				if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
					sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
				}
				shell_exec('secure/picams.sh DEURBEL > /dev/null 2>/dev/null &');
		
				if ($d['Weg']['s']==0&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed') {
					telegram('Deurbel', true, 2);
					sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
					if ($d['lgtv']['s']=='On') {
						shell_exec('python3 ../lgtv.py -c send-message -a "DEURBEL" 192.168.2.27 > /dev/null 2>/dev/null &');
					}
					if ($d['Xvol']['s']!=40) {
					    sl('Xvol', 40, basename(__FILE__).':'.__LINE__);
					    usleep(10000);
					}
					sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
					sleep(2);
					sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
				}
			}
		}
	}
}