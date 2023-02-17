<?php
if ($status=='On') {
    lgcommand('on');
    if ($d['daikinliving']['m']>0) file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=0');
} elseif ($status=='Off') {
    lgcommand('off');
    if ($d['daikinliving']['m']>0&&$d['eettafel']['s']==0) file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=1');
    if ($d['tévé']['s']=='On') sw('tévé', 'Off');
}
