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
$user='cron300';
if (TIME<=strtotime('11:00')) {
	if ($d['nas']['s']!='On') {
		if (file_get_contents($urlnas)>0) {
			shell_exec('./wakenas.sh');
		}
	}
}
/*if ($d['zwembadfilter']['s']=='On') {
	if (past('zwembadfilter')>10700
		&&TIME>strtotime("16:00")
		&&$d['zwembadwarmte']['s']=='Off'
		&&$d['buiten_temp']['s']<27
	) {
		sw('zwembadfilter','Off', basename(__FILE__).':'.__LINE__);
	}
}else{
	if (
			(past('zwembadfilter')>10700&&TIME>strtotime("12:59")&&TIME<strtotime("15:59"))
			||
			(past('zwembadfilter')>10700&&$d['buiten_temp']['s']>27)
	   ) {
	   	sw('zwembadfilter','On', basename(__FILE__).':'.__LINE__);
	}
}
if ($d['zwembadwarmte']['s']=='On') {
	if (past('zwembadwarmte')>86398) {
		sw('zwembadwarmte','Off', basename(__FILE__).':'.__LINE__);
	}
	if ($d['zwembadfilter']['s']=='Off') {
		sw('zwembadfilter','On', basename(__FILE__).':'.__LINE__);
	}
}*/
$items=array('buiten_temp', 'living_temp', 'badkamer_temp', 'kamer_temp', 'tobi_temp', 'alex_temp', 'zolder_temp');
foreach ($items as $i) {
    if (past($i)>900) {
        storeicon($i, '', basename(__FILE__).':'.__LINE__);
    }
}
foreach ($items as $i) {
	if ($d[$i]['m']==1&&past($i)>21600) {
		storemode($i, 0, basename(__FILE__).':'.__LINE__);
	}
}
if ($d['auto']['s']!='On') {
	if (past('auto')>10795) {
		sw('auto', 'On', basename(__FILE__).':'.__LINE__);
	}
}
if (past('Weg')>14400
	&& $d['Weg']['s']==0
	&& past('pirliving')>14400
	&& past('pirkeuken')>14400
	&& past('pirinkom')>14400
	&& past('pirhall')>14400
	&& past('pirgarage')>14400
) {
	store('Weg', 1, basename(__FILE__).':'.__LINE__);
	telegram('Slapen ingeschakeld na 4 uur geen beweging', false, 2);
} elseif (past('Weg')>36000
	&& $d['Weg']['s']==1
	&& past('pirliving')>36000
	&& past('pirkeuken')>36000
	&& past('pirinkom')>36000
	&& past('pirhall')>36000
	&& past('pirgarage')>36000
) {
	store('Weg', 2, basename(__FILE__).':'.__LINE__);
	telegram('Weg ingeschakeld na 10 uur geen beweging', false, 2);
}
if ($d['picam2plug']['s']=='On'&&$d['Ralex']['s']<75&&$d['deuralex']['s']=='Open'&&TIME>strtotime("7:00")&&TIME<strtotime("18:00")) {
	echo 'ok';
    file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
    sleep(1);
    file_get_contents('http://192.168.2.12/fifo_command.php?cmd=halt');
    sleep(10);
    sw('picam2plug', 'Off', basename(__FILE__).':'.__LINE__);
}