<?php
/**
 * Pass2PHP
 * php version 8.0
 *
 * @category Home_Automation
 * @package  Pass2PHP
 * @author   Guy Verschuere <guy@egregius.be>
 * @license  GNU GPLv3
 * @link     https://egregius.be
 **/
$user='cron180';
/*if ($d['daikin']['s']=='On'&&past('daikin')>118) {
	foreach (array('living', 'kamer', 'alex') as $k) {
		$data=daikinstatus($k);
		if ($data&&$data!=$d['daikin'.$k]['s']) store('daikin'.$k, $data, basename(__FILE__).':'.__LINE__);
		$data=json_decode($data);
		if (isset($data->power)) {
			if ($data->power==0&&$d['daikin'.$k]['m']!=0) storemode('daikin'.$k, 0);
			elseif($data->power==1&&$data->mode!=$d['daikin'.$k]['m']) storemode('daikin'.$k, $data->mode);
		}
	}
}*/
if ($d['daikinliving']['m']==0&&$d['daikinkamer']['m']==0&&$d['daikinalex']['m']==0) {
	$usage=explode(';', $d['daikin_kWh']['s']);
	if ($d['daikin']['s']=='On'&&$usage[0]<12&&past('daikin')>1800&&past('daikinliving')>1800&&past('daikinkamer')>1800&&past('daikinalex')>1800) sw('daikin', 'Off', basename(__FILE__).':'.__LINE__);
} else {
	if ($d['daikin']['s']=='Off'&&past('daikin')>900) sw('daikin', 'On', basename(__FILE__).':'.__LINE__);
}
