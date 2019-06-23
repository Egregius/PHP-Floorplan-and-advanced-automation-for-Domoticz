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
		sl('Rbureel', 50);
	}
	if(TIME>=strtotime('10:00')&&$d['zon']['s']>1000&&$d['Rtobi']['s']<80&&$d['Rtobi']['m']==0&&past('Rtobi')>7200) {
		sl('Rtobi', 80);
	}
	if(TIME>=strtotime('10:00')&&$d['zon']['s']>1000&&$d['Ralex']['s']<80&&$d['Rtobi']['m']==0&&past('Ralex')>7200) {
		sl('Ralex', 80);
	}
}
if ($d['zwembadfilter']['s']=='On') {
	if (past('zwembadfilter')>10700
		&&TIME>strtotime("16:00")
		&&$d['zwembadwarmte']['s']=='Off'
		&&$d['buiten_temp']['s']<27
	) {
		sw('zwembadfilter','Off');
	}
}else{
	if (
			(past('zwembadfilter')>10700&&TIME>strtotime("12:59")&&TIME<strtotime("15:59"))
			||
			(past('zwembadfilter')>10700&&$d['buiten_temp']['s']>27)
	   ) {
	   	sw('zwembadfilter','On');
	}
}
if ($d['zwembadwarmte']['s']=='On') {
	if (past('zwembadwarmte')>86398) {
		sw('zwembadwarmte','Off');
	}
	if ($d['zwembadfilter']['s']=='Off') {
		sw('zwembadfilter','On');
	}
}
$items=array('buiten_temp', 'living_temp', 'badkamer_temp', 'kamer_temp', 'tobi_temp', 'alex_temp', 'zolder_temp');
foreach ($items as $i) {
    if (past($i)>900) {
        storeicon($i, '');
    }
}