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
if ($status=='On') {
    lgcommand('on');
    if ($d['daikinliving']['m']>0) file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=0');
} elseif ($status=='Off') {
    lgcommand('off');
    if ($d['daikinliving']['m']>0&&$d['eettafel']['s']==0) file_get_contents('http://192.168.2.111/aircon/set_special_mode?en_streamer=1');
    if ($d['tévé']['s']=='On') sw('tévé', 'Off');
}
