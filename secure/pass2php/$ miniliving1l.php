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
if (past('$ miniliving1l')>2) {
	sw('jbl', 'Toggle', basename(__FILE__).':'.__LINE__);
	store('Weg', 0, basename(__FILE__).':'.__LINE__);
}