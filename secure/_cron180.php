<?php
$d=fetchdata();
dag();
$user='cron180';
if ($d['daikin']['s']=='On'&&past('daikin')>118) {
	foreach (array('living', 'kamer', 'alex') as $k) {
		$data=daikinstatus($k);
		$data=str_replace('\\', '', $data);
		if ($data&&$data!=$d['daikin'.$k]['s']) store('daikin'.$k, $data, basename(__FILE__).':'.__LINE__);
		$data=json_decode($data);
		if (isset($data->power)) {
			if ($data->power==0&&$d['daikin'.$k]['m']!=0) storemode('daikin'.$k, 0);
			elseif($data->power==1&&$data->mode!=$d['daikin'.$k]['m']) storemode('daikin'.$k, $data->mode);
		}
	}
}
if ($d['daikinliving']['m']==0&&$d['daikinkamer']['m']==0&&$d['daikinalex']['m']==0) {
	if ($d['daikin']['s']=='On'&&$d['daikin']['m']==1&&$d['daikin_kWh']['s']<20&&past('daikin')>1800) sw('daikin', 'Off', basename(__FILE__).':'.__LINE__);
}