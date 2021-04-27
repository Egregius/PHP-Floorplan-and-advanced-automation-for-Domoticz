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
foreach(array('Rliving', 'Rbureel', 'RkeukenL', 'RkeukenR') as $i) {
	if ($d[$i]['s']!=0) sl($i, 0, basename(__FILE__).':'.__LINE__);
}
