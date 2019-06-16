<?php
/**
 * Pass2PHP
 * php version 7.3.3-1
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['Rliving']['s']<26) {
	sl('Rliving', 26);
}
if ($d['Rbureel']['s']<40) {
	sl('Rbureel', 40);
}
store('Weg', 0, null, true);