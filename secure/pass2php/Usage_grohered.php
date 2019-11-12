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
	if (past('GroheRed')<=2&&$status<10) {
		rgb('Xlight', 127, 15);
	} elseif (past('GroheRed')>=2) {
		if ($status<10) {
			rgb('Xlight', 0, 15);
		} else {
			rgb('Xlight', 230, 15);
		}
	}
}