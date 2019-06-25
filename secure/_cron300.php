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
if ($d['heating']['s']==1) {//Cooling
	if(TIME>=strtotime('15:00')&&$d['zon']['s']>1000&&$d['Rbureel']['s']<50&&past('Rbureel')>7200) {
		sl('Rbureel', 50, basename(__FILE__).':'.__LINE__);
	}
	if(TIME>=strtotime('10:00')&&$d['zon']['s']>1000&&$d['Rtobi']['s']<80&&$d['Rtobi']['m']==0&&past('Rtobi')>7200) {
		sl('Rtobi', 80, basename(__FILE__).':'.__LINE__);
	}
	if(TIME>=strtotime('10:00')&&$d['zon']['s']>1000&&$d['Ralex']['s']<80&&$d['Rtobi']['m']==0&&past('Ralex')>7200) {
		sl('Ralex', 80, basename(__FILE__).':'.__LINE__);
	}
}
if ($d['zwembadfilter']['s']=='On') {
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
}
$items=array('buiten_temp', 'living_temp', 'badkamer_temp', 'kamer_temp', 'tobi_temp', 'alex_temp', 'zolder_temp');
foreach ($items as $i) {
    if (past($i)>900) {
        storeicon($i, '');
    }
}
foreach ($items as $i) {
	if ($d[$i]['m']==1&&past($i)>21600) {
		storemode($i, 0);
	}
}
if ($d['auto']['s']!='On') {
	if (past('auto')>10795) {
		sw('auto', 'On', basename(__FILE__).':'.__LINE__);
	}
}
if (past('Weg')>14400
	&& $d['Weg']['s']==0
	&& $d['Weg']['m']<TIME-14400
) {
	store('Weg', 1);
	telegram('Slapen ingeschakeld na 4 uur geen beweging', false, 2);
} elseif (past('Weg')>36000
	&& $d['Weg']['s']==1
	&& $d['Weg']['m']<TIME-36000
) {
	store('Weg', 2);
	telegram('Weg ingeschakeld na 10 uur geen beweging', false, 2);
}