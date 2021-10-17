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
if ($d['bose105']['s']=='On') {
	bosekey("POWER", 0, 105);
	sw('bose105', 'Off',basename(__FILE__).':'.__LINE__);
}
