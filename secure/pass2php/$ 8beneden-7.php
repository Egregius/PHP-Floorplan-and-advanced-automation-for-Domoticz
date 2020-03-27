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
if ($d['eettafel']['s']==0) {
	sl('eettafel', 5, basename(__FILE__).':'.__LINE__);
} else {
	sl('eettafel', floor($d['eettafel']['s']*0.95));
}
if ($d['eettafel']['m']>0) storemode('eettafel', 0, basename(__FILE__).':'.__LINE__);