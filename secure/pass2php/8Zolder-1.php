<?php
/**
 * Pass2PHP
 * php version 7.3.4-2
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
if ($d['zolder']['s']<19) {
	sl('zolder', 20, basename(__FILE__).':'.__LINE__);
} elseif ($d['zolder']['s']<38) {
	sl('zolder', 40, basename(__FILE__).':'.__LINE__);
} elseif ($d['zolder']['s']<100) {
	sl('zolder', 100, basename(__FILE__).':'.__LINE__);
}