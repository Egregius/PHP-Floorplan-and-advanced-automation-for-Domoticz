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
if ($d['auto']['s']=='On') {
	if ($status=="On") {
		fkeuken();
		sirene('Beweging keuken');
	} else {
		if ($d['keuken']['s']=='On'&&$d['lgtv']['s']=='On') {
			sw('keuken', 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
}