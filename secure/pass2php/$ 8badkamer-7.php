<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['heating']['s']>=0) {
	store('badkamer_set', 20, basename(__FILE__).':'.__LINE__);
	storemode('badkamer_set', 2, basename(__FILE__).':'.__LINE__);
	if ($d['badkamervuur1']['s']=='Off') {
		sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
		sleep(2);
		if ($d['badkamervuur2']['s']=='Off') sw('badkamervuur2', 'On', basename(__FILE__).':'.__LINE__);
	}
}
douche();
