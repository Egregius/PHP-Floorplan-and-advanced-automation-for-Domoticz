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
	store('badkamer_set', 20, basename(__FILE__).':'.__LINE__);
	if ($d['badkamervuur1']['s']=='Off') {
		sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
	}
}
douche();
