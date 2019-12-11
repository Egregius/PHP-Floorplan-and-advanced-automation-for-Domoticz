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
if ($d['eettafel']['s']<100) {
	if ($d['eettafel']['s']==0) $d['eettafel']['s']=1;
	sl('eettafel', ceil($d['eettafel']['s']*1.05));
}