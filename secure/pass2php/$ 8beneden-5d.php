<?php
/**
 * Pass2PHP
 * php version 7.3.11-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['heating']['s']<0) {
	$level=85;
} else {
	$level=100;
}
if ($d['Rliving']['s']<$level) {
	sl('Rliving', $level, basename(__FILE__).':'.__LINE__);
}