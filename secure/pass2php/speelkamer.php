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
	if ($d['speelkamer']['m']!=0&&TIME>strtotime('6:00')&&TIME<strtotime('12:00')) {
		storemode('speelkamer', 0, basename(__FILE__).':'.__LINE__);
	}
	if (TIME>strtotime('6:00')&&TIME<strtotime('10:00')&&$d['deuralex']['s']=='Open') {
		if ($d['Rspeelkamer']['s']>0) {
			sl('Rspeelkamer', 0, basename(__FILE__).':'.__LINE__);
		}
	}
}
