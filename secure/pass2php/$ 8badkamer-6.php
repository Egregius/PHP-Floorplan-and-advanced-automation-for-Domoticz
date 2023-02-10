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
	store('badkamer_set', 21, basename(__FILE__).':'.__LINE__);
	storemode('badkamer_set', 1, basename(__FILE__).':'.__LINE__);
}
