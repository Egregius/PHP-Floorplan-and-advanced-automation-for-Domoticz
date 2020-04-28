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
if ($status=='Off') {
	if ($d['denonpower']['s']!='OFF') {
		store('denonpower', 'OFF', basename(__FILE__).':'.__LINE__);
	}
}