<?php
if ($status=='On') {
    if ($d['heater1']['s']!='On') {
        sw('heater1', 'On', basename(__FILE__).':'.__LINE__);
    }
}