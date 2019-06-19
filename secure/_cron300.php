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
			(past('zwembadfilter')>10700&&TIME>strtotime("13:00")&&TIME<strtotime("16:00"))
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