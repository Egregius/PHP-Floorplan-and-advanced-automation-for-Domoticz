<?php
if ($status==0) {
    if ($d['daikin']->s=='On') @file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=1');
} else {
	if ($d['daikin']->s=='On') @file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=0');
}
