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
if ($d['kamer']['s']>0) {
	$new=floor($d['kamer']['s']*0.65);
	sl('kamer', $new, basename(__FILE__).':'.__LINE__);
}
if ($d['kamer']['m']>0) storemode('kamer', 0, basename(__FILE__).':'.__LINE__);
