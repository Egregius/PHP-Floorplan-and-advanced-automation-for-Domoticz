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
if ($status=='Off') {
	sw('poortrf', 'On');
	sw('voordeur', 'On');
	huisthuis();
	sleep(2);
	if ($d['zon']['s']>0) sw('voordeur', 'Off');
} else {
	if ($d['achterdeur']['s']!='Closed') {
		waarschuwing(' Let op . Achterdeur open');
		exit;
	}
	if ($d['deurvoordeur']['s']!='Closed') {
		waarschuwing(' Let op . Voordeur open');
		exit;
	}
	if ($d['raamliving']['s']!='Closed') {
		waarschuwing(' Let op . Raam Living open');
		exit;
	}
	if ($d['raamhall']['s']!='Closed') {
		waarschuwing(' Let op . Raam hall open');
		exit;
	}
	if ($d['raamkeuken']['s']!='Closed') {
		waarschuwing(' Let op . Raam keuken open');
		exit;
	}
	if ($d['bose105']['m']=='Online') {
		waarschuwing(' Let op . Bose buiten');
		exit;
	}
	store('Weg', 2);
	sw('voordeur', 'On');
	sleep(2);
	sw('voordeur', 'Off');
	huisslapen();
}
