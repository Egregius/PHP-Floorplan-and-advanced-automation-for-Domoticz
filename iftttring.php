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
$msg=__FILE__.PHP_EOL;
if (isset($_REQUEST['token'])&&$_REQUEST['token']==$ifttttoken) {
	$d=fetchdata();
	if (isset($_REQUEST['RING'])) { // PYTHON
		$last=apcu_fetch($_REQUEST['RING']);
		$new=ceil($_REQUEST['time']);
		unset($_REQUEST['token']);
		$msg.=('PYTHON '.$_REQUEST['RING'].PHP_EOL.print_r($_REQUEST, true).PHP_EOL.'last='.$last.PHP_EOL.'new='.$new.PHP_EOL);
		if ($last!=$new) {
			if ($new>($last+60)) {
				$msg.=__LINE__.' newer'.PHP_EOL;
				apcu_store($_REQUEST['RING'], $new);
				print_r($_REQUEST);
				if ($_REQUEST['RING']=='motion') {
					$msg.=__LINE__.' motion'.PHP_EOL;
					if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
						$msg.=__LINE__.' voordeur aan'.PHP_EOL;
						sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
					}
		
					if ($d['Weg']['s']==0/*&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90*/) {
						$msg.=__LINE__.' Notificatie'.PHP_EOL;
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
				} elseif ($_REQUEST['RING']=='ding') {
					$msg.=__LINE__.PHP_EOL;
					if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
						$msg.='-'.__LINE__;
						sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
					}
					shell_exec('secure/picams.sh DEURBEL > /dev/null 2>/dev/null &');
					if ($d['Weg']['s']==0/*&&$d['deurvoordeur']['s']=='Closed'*/) {
						$msg.=__LINE__.PHP_EOL;
						sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
						if ($d['lgtv']['s']=='On') {
							$msg.=__LINE__.PHP_EOL;
							shell_exec('python3 ../lgtv.py -c send-message -a "DEURBEL" 192.168.2.27 > /dev/null 2>/dev/null &');
						}
						if ($d['Xvol']['s']!=40) {
							$msg.=__LINE__.PHP_EOL;
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
			$msg.=__LINE__.' Battery '.$_REQUEST['battery'].' %'.PHP_EOL;
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
		$msg.=('IFTTT Beweging'.PHP_EOL.print_r($_REQUEST, true).PHP_EOL.'last='.$last.PHP_EOL.'new='.$new.PHP_EOL);
		if ($last!=$new) {
			$msg.=__LINE__.' last!=new'.PHP_EOL;
			if ($new>($last+60)) {
				$msg.=__LINE__.' new>last'.PHP_EOL;
				apcu_store('motion', $new);
				if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
					$msg.=__LINE__.' Licht voordeur'.PHP_EOL;
					sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
				}
		
				if ($d['Weg']['s']==0/*&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90*/) {
					$msg.=__LINE__.' Notification'.PHP_EOL;
					shell_exec('secure/picams.sh Beweging > /dev/null 2>/dev/null &');
					if ($d['lgtv']['s']=='On') {
						$msg.=__LINE__.' LGTV'.PHP_EOL;
						shell_exec('python3 secure/lgtv.py -c send-message -a "Beweging Ring" 192.168.2.27');
					}
					if (past('Xbel')>60) {
						$msg.=__LINE__.' XBEL'.PHP_EOL;
						if ($d['Xvol']['s']!=5) {
						    sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
						}
						sl('Xbel', 30, basename(__FILE__).':'.__LINE__);
					}
				}
			}
		}
	} elseif (isset($_REQUEST['ring'])&&$_REQUEST['ring']=='DEURBEL') { //IFTTT
		unset($_REQUEST['token']);
		$last=apcu_fetch('ding');
		$split = preg_split('/[\ \n\,]+/', trim($_REQUEST['time']));
		$new=strtotime($split[1].' '.$split[0].' '.$split[2].' '.$split[4]);
		$msg.=('IFTTT DEURBEL'.PHP_EOL.print_r($_REQUEST, true).PHP_EOL.print_r($split, true).PHP_EOL.'time='.$split[1].' '.$split[0].' '.$split[2].' '.$split[4].PHP_EOL.'last='.$last.PHP_EOL.'new='.$new.PHP_EOL);
		if ($last!=$new) {
			$msg.=__LINE__.' last!=new'.PHP_EOL;
			if ($new>($last+60)) {
				$msg.=__LINE__.' new>last'.PHP_EOL;
				apcu_store('ding', $new);
				if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
					$msg.=__LINE__.' Licht voordeur'.PHP_EOL;
					sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
				}
				shell_exec('secure/picams.sh DEURBEL > /dev/null 2>/dev/null &');
		
				if ($d['Weg']['s']==0/*&&$d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'*/) {
					$msg.=__LINE__.' Notification'.PHP_EOL;
					telegram('DEURBEL', true, 2);
					sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
					if ($d['lgtv']['s']=='On') {
						$msg.=__LINE__.' LGTV'.PHP_EOL;
						shell_exec('python3 ../lgtv.py -c send-message -a "DEURBEL" 192.168.2.27 > /dev/null 2>/dev/null &');
					}
					if ($d['Xvol']['s']!=40) {
						$msg.=__LINE__.' XVOL'.PHP_EOL;
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
echo $msg;
telegram($msg);