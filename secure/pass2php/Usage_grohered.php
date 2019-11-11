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
if ($d['GroheRed']['s']=='Off') {
	sw('Xlight', 'Off', basename(__FILE__).':'.__LINE__);
} elseif ($d['GroheRed']['s']=='On') {
	if (past('GroheRed')<10&&$status<10) {
		rgb('Xlight', 127, 100);
	} elseif (past('GroheRed')>10) {
		if ($status<10) {
			rgb('Xlight', 0, 100);
		} else {
			rgb('Xlight', 230, 100);
		}
	}
}