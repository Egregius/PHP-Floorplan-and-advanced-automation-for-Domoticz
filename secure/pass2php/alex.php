<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($status>0) {
	if ($d['alex']['m']!=0&&TIME>strtotime('6:00')&&TIME<strtotime('12:00')) {
		storemode('alex', 0, basename(__FILE__).':'.__LINE__);
	}
	if (TIME>strtotime('7:30')&&TIME<strtotime('10:00')) {
		if ($d['Ralex']['s']>0) {
			sl('Ralex', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
