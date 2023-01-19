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
if ($d['heating']['s']>=0) {
	if ($d['luchtdroger']['s']=='Off') sw('luchtdroger', 'On', basename(__FILE__).':'.__LINE__);
	$i=explode(';', $d['luchtdroger_kWh']['s']);
	$luchtdroger=$i[0];
	if ($d['badkamervuur1']['s']=='Off'&&$luchtdroger<100) {
		sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	}
	store('badkamer_set', 19, basename(__FILE__).':'.__LINE__);
	storemode('badkamer_set', 1, basename(__FILE__).':'.__LINE__);
}
douche();
