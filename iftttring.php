<?php
/**
 * Pass2PHP
 * php version 8.0
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
		echo __LINE__.'<br>';
		$last=apcu_fetch($_REQUEST['RING']);
		$new=ceil($_REQUEST['time']);
		echo $new.'<br>';
		if ($last!=$new&&$new>($last+60)) {
			echo __LINE__.'<br>';
			apcu_store($_REQUEST['RING'], $new);
			if ($_REQUEST['RING']=='motion') {
				echo __LINE__.'<br>';
				if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
					sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
				}
				if ($d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90&&past('poortrf')>90) {
					shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?ringbeweging&battery='.$_REQUEST['battery'].'" > /dev/null 2>/dev/null &');
					shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.13/telegram.php?ringbeweging&battery='.$_REQUEST['battery'].'" > /dev/null 2>/dev/null &');
					shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
					shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.13/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
				}
			} elseif ($_REQUEST['RING']=='ding') {
				require 'secure/pass2php/belknop.php';
				lg('PYTHON Deurbel');
			}
			//telegram($_REQUEST['RING'].PHP_EOL.'python');
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
		if ($last!=$new&&$new>($last+60)) {
			apcu_store('motion', $new);
			if ($d['zon']['s']==0&&(TIME<$d['Sun']['s']||TIME>$d['Sun']['m'])) {
				sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
			}
			if ($d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90&&past('poortrf')>90) {
				shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?ringbeweging" > /dev/null 2>/dev/null &');
				shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.13/telegram.php?ringbeweging" > /dev/null 2>/dev/null &');
				shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
				shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.13/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
			}
			//telegram($_REQUEST['ring'].PHP_EOL.'ifttt');
		}
		
	} elseif (isset($_REQUEST['ring'])&&$_REQUEST['ring']=='DEURBEL') { //IFTTT
		$last=apcu_fetch('ding');
		$split = preg_split('/[\ \n\,]+/', trim($_REQUEST['time']));
		$new=strtotime($split[1].' '.$split[0].' '.$split[2].' '.$split[4]);
		if ($last!=$new) {
			if ($new>($last+60)) {
				apcu_store('ding', $new);
				require 'secure/pass2php/belknop.php';
				lg('IFTTT Deurbel');
				telegram($_REQUEST['ring'].PHP_EOL.'ifttt');
			}
		}
		
	}
}
