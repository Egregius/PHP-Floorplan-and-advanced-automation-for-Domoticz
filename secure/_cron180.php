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
$user='cron180';
foreach (array('living', 'kamer', 'alex') as $k) {
	$data=daikinstatus($k);
	if ($data&&$data!=$d['daikin'.$k]['s']) store('daikin'.$k, $data, basename(__FILE__).':'.__LINE__);
}