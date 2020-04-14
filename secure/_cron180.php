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
	if ($data&&$data!=$d['daikin'.$k]['s']) {
		store('daikin'.$k, $data, basename(__FILE__).':'.__LINE__);
		$data=json_decode($data);
		if ($data->pow==0&&$d['daikin'.$k]['m']!=0) storemode('daikin'.$k, 0);
		elseif($data->pow==1) {
			if ($data->mode!=$d['daikin'.$k]['m']) storemode('daikin'.$k, $data->mode);
		}
	}
}