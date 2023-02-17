<?php
if ($status==0) {
    if ($d['eettafel']['m']!=0) {
        storemode('eettafel', 0, basename(__FILE__).':'.__LINE__);
    }
    if ($d['daikinliving']['m']>0&&$d['lgtv']['s']=='Off') file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=1');
} else {
	if ($d['daikinliving']['m']>0) file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=0');
}
