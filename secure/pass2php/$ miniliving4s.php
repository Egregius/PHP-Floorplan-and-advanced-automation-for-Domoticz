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
if($d['sony']['s']=='On') {
	if (past('$ miniliving4s')<=1) fvolume(+4);
	else fvolume(+1);
} else fvolume('up');
store('Weg', 0, basename(__FILE__).':'.__LINE__);