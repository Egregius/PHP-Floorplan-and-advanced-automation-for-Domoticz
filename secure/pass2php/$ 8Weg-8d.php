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
if (past('$ 8Weg-8')<10) {
	if ($status=='On') {
		huisthuis();
		resetsecurity();
	}
}
