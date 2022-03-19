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
if ($status=='Open') {
	sl('zolder', 100, basename(__FILE__).':'.__LINE__);
} elseif ($status=='Closed') {
	sl('zolder', 0, basename(__FILE__).':'.__LINE__);
}
