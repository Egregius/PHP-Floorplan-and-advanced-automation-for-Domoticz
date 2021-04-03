<?php
/**
 * Pass2PHP
 * php version 7.3
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link	 https://egregius.be
 **/
if ($status=='On') {
	sw('poortrf', 'On');
	store('Weg', 0);
	sw('voordeur', 'On');
} else {
	store('Weg', 2);
	sw('voordeur', 'On');
	sleep(2);
	sw('voordeur', 'Off');
	huisslapen();
}
