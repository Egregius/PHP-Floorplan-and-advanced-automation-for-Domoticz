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
require '/var/www/html/secure/functions.php';
$d=fetchdata();
$last=apcu_fetch('ring'.$_REQUEST['kind']);
$new=$_REQUEST['id'];
echo 'last='.$last.'<br>';
echo 'new='.$new.'<br>';
if ($last!=$new) {
	echo __LINE__.'<br>';
	apcu_store('ring'.$_REQUEST['kind'], $new);
	$zonop=($d['civil_twilight']['s']+$d['Sun']['s'])/2;
	$zononder=($d['civil_twilight']['m']+$d['Sun']['m'])/2;
	if ($d['zon']['s']==0&&(TIME<$zonop||TIME>$zononder)) {
		echo __LINE__.'<br>';
		sw('voordeur', 'On', basename(__FILE__).':'.__LINE__);
	}
	if ($_REQUEST['kind']=='motion') {
		echo __LINE__.'<br>';
		if ($d['poortrf']['s']=='Off'&&$d['deurvoordeur']['s']=='Closed'&&past('deurvoordeur')>90&&past('poortrf')>90) {
			echo __LINE__.'<br>';
			shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?ringbeweging&battery='.$_REQUEST['battery'].'&source='.$_REQUEST['source'].'" > /dev/null 2>/dev/null &');
			shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.13/telegram.php?ringbeweging&battery='.$_REQUEST['battery'].'&source='.$_REQUEST['source'].'" > /dev/null 2>/dev/null &');
			shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
			shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.13/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
		}
		telegram('Python Ring '.$_REQUEST['kind'].PHP_EOL.$_REQUEST['source']);
	} elseif ($_REQUEST['kind']=='ding') {
		echo __LINE__.'<br>';
		if ($d['Weg']['s']==0) {
			sw('deurbel', 'On', basename(__FILE__).':'.__LINE__);
		}
		shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.11/telegram.php?deurbel" > /dev/null 2>/dev/null &');
		shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.13/telegram.php?deurbel" > /dev/null 2>/dev/null &');
		shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.11/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
		shell_exec('wget -O /dev/null -o /dev/null "http://192.168.2.13/fifo_command.php?cmd=record%20on%205%2055" > /dev/null 2>/dev/null &');
		if ($d['Weg']['s']==0) {
			if ($d['Xvol']['s']!=40) {
				sl('Xvol', 40, basename(__FILE__).':'.__LINE__);
				usleep(10000);
			}
			sl('Xbel', 10, basename(__FILE__).':'.__LINE__);
			if ($d['bose101']['s']=='On') {
				shell_exec('curl -s "http://127.0.0.1/secure/pass2php/belknopbose101.php?deurbel" > /dev/null 2>/dev/null &');
			}
			if ($d['lgtv']['s']=='On') {
				shell_exec('python3 ../lgtv.py -c send-message -a "Deurbel" 192.168.2.27 > /dev/null 2>/dev/null &');
			}
			sleep(2);
			sl('Xvol', 5, basename(__FILE__).':'.__LINE__);
		}
		telegram('Python Ring '.$_REQUEST['kind'].PHP_EOL.$_REQUEST['source'], true, 2);
	}
	
}
apcu_store('ringdoorbellbattery', $_REQUEST['battery']);
if ($_REQUEST['battery']<60) {
	echo __LINE__.'<br>';
	if ($d['ringdoorbell']['s']=='Off') {
		sw('ringdoorbell', 'On', basename(__FILE__).':'.__LINE__);
		alert(
		    'BatterijRingDeurbel',
		    'Batterij Ring Deurbel '.$_REQUEST['battery'].' %',
		    28800
		);
	}
}
