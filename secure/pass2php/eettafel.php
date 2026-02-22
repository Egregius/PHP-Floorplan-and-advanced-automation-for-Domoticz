<?php
if ($status==0) {
    if ($d['daikin']->s=='On') @file_get_contents('http://192.168.2.161/aircon/set_special_mode?en_streamer=1');
    if ($d['media']->s=='On'&&$d['boseliving']->s=='On') sw('boseliving','Off',basename(__FILE__).':'.__LINE__);
} else {
	if ($d['daikin']->s=='On') @file_get_contents('http://192.168.2.161/aircon/set_special_mode?en_streamer=0');
}
