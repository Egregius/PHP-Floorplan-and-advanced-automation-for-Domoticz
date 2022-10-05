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
	if ($d['poort']['s']=='Open') {
		if ($d['achterdeur']['s']!='Closed') {
			waarschuwing(' Let op . Achterdeur open', 55);
			exit;
		}
		if ($d['deurvoordeur']['s']!='Closed') {
			waarschuwing(' Let op . Raam Living open', 55);
			exit;
		}
		if ($d['raamliving']['s']!='Closed') {
			waarschuwing(' Let op . Raam Living open', 55);
			exit;
		}
		if ($d['raamhall']['s']!='Closed') {
			waarschuwing(' Let op . Raam hall open', 55);
			exit;
		}
		if ($d['raamkeuken']['s']!='Closed') {
			waarschuwing(' Let op . Raam keuken open', 55);
			exit;
		}
		if ($d['bose104']['m']=='Online') {
			waarschuwing(' Let op . Bose garage aan');
			exit;
		}
		if ($d['bose105']['m']=='Online') {
			waarschuwing(' Let op . Bose badkamer', 55);
			exit;
		}
		if ($d['bose106']['m']=='Online') {
			waarschuwing(' Let op . Bose buiten20', 55);
			exit;
		}
		if ($d['bose107']['m']=='Online') {
			waarschuwing(' Let op . Bose buiten10', 55);
			exit;
		}
		sl('Xring', 90, basename(__FILE__).':'.__LINE__);
		sleep(4);
		sl('Xring', 0, basename(__FILE__).':'.__LINE__);
	
		huisslapen();
		store('Weg', 2, basename(__FILE__).':'.__LINE__);
	} else {
		sw('poortrf', 'On', basename(__FILE__).':'.__LINE__);
		if ($d['sirene']['s']!='Off') {
			sw('sirene', 'Off', basename(__FILE__).':'.__LINE__);
		}
	}
}
