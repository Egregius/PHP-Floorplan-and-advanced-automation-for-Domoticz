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
if ($d['zolder']['s']<100) {
	if ($d['zolder']['s']==0) $d['zolder']['s']=1;
	sl('zolder', ceil($d['zolder']['s']*1.05));
}