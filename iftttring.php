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
if (isset($_REQUEST['token'])&&$_REQUEST['token']==$ifttttoken) {
	$d=fetchdata();
	if (isset($_REQUEST['RING'])) { // PYTHON
		$last=apcu_fetch($_REQUEST['RING']);
		$new=ceil($_REQUEST['time']);
		if ($last!=$new) {
			if ($new>($last+60)) {
				$msg.='newer'.PHP_EOL;
				apcu_store($_REQUEST['RING'], $new);
				print_r($_REQUEST);
				if ($_REQUEST['RING']=='motion') {
					$msg.='motion'.PHP_EOL;
					if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
						sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
					}
					shell_exec('secure/picams.sh Beweging > /dev/null 2>/dev/null &');
					if ($d['Weg']['s']==0/*&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90*/) {
						if ($d['lgtv']['s']=='On') {
							shell_exec('python3 secure/lgtv.py -c send-message -a "Beweging Ring" 192.168.2.27');
						}
						if (past('Xbel')>60) {
							$msg.='XBEL'.PHP_EOL;
							if ($d['Xvol']['s']!=5) {
							    sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
							}
							sl('Xbel', 30, basename(__FILE__).':'.__LINE__);
						}
					}
				} elseif ($_REQUEST['RING']=='ding') {
					require 'secure/pass2php/belknop.php';
				}
			}
		}
		if ($_REQUEST['battery']<60) {
			alert(
                            'BatterijRingDeurbel',
                            'Batterij Ring Deurbel '.$_REQUEST['battery'].' %',
                            86400
                        );
                }
	} elseif (isset($_REQUEST['ring'])&&$_REQUEST['ring']=='Beweging') { //IFTTT
		$last=apcu_fetch('motion');
		$split = preg_split('/[\ \n\,]+/', trim($_REQUEST['time']));
		$new=strtotime($split[1].' '.$split[0].' '.$split[2].' '.$split[4]);
		unset($_REQUEST['token']);
		if ($last!=$new) {
			if ($new>($last+60)) {
				apcu_store('motion', $new);
				if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
					sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
				}
		
				if ($d['Weg']['s']==0/*&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90*/) {
					$msg.='Notification'.PHP_EOL;
					shell_exec('secure/picams.sh Beweging > /dev/null 2>/dev/null &');
					if ($d['lgtv']['s']=='On') {
						shell_exec('python3 secure/lgtv.py -c send-message -a "Beweging Ring" 192.168.2.27');
					}
					if (past('Xbel')>60) {
						$msg.='XBEL'.PHP_EOL;
						if ($d['Xvol']['s']!=5) {
						    sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
						}
						sl('Xbel', 30, basename(__FILE__).':'.__LINE__);
					}
				}
			}
		}
	} elseif (isset($_REQUEST['ring'])&&$_REQUEST['ring']=='DEURBEL') { //IFTTT
		$last=apcu_fetch('ding');
		$split = preg_split('/[\ \n\,]+/', trim($_REQUEST['time']));
		$new=strtotime($split[1].' '.$split[0].' '.$split[2].' '.$split[4]);
		if ($last!=$new) {
			if ($new>($last+60)) {
				apcu_store('ding', $new);
				require 'secure/pass2php/belknop.php';
			}
		}
	}
}
