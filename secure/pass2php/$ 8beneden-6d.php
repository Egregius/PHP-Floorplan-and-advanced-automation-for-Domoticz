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
$item='Rbureel';
if ($d[$item]['s']<$level) {
	sl($item, $level, basename(__FILE__).':'.__LINE__);
}
$item='RkeukenL';
if ($d[$item]['s']<$level) {
	sl($item, $level, basename(__FILE__).':'.__LINE__);
}
$item='RkeukenR';
if ($d[$item]['s']<$level) {
	sl($item, $level, basename(__FILE__).':'.__LINE__);
}