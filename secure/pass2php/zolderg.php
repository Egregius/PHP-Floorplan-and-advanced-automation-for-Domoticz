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
if ($status=='Off'&&past('zolderg')>30) {
	for ($x=1;$x<=10;$x++){
		sleep(2);
		sw('zolderg', 'Off');
	}
}