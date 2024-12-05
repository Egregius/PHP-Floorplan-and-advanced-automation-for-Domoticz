<?php
if ($status==0) {
    if ($d['daikin']['s']=='On'&&$d['daikinliving']['m']>0&&$d['Media']['s']=='Off') file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=1');
} else {
	if ($d['daikin']['s']=='On'&&$d['daikinliving']['m']>0) file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=0');
}
