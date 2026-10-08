<?php
if ($status==0) {
//    if ($d['daikin']->s=='On') @file_get_contents('http://192.168.40.161/aircon/set_special_mode?en_streamer=1');
	$dow = date("w");
	if ($dow == 6 || $dow == 0) $pop=25;
	else $pop=0;
	if($d['boseliving']->m!=$pop) storemode('boseliving',$pop,basename(__FILE__).':'.__LINE__);
} else {
//	if ($d['daikin']->s=='On') @file_get_contents('http://192.168.40.161/aircon/set_special_mode?en_streamer=0');
	if(($time>=strtotime('11:30')&&$time<strtotime('13:30'))||($time>=strtotime('17:30')&&$time<strtotime('20:30'))) {
		$pop=75;
		if($d['boseliving']->m!=$pop) storemode('boseliving',$pop,basename(__FILE__).':'.__LINE__);
	}
}
