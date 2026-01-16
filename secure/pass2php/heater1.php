<?php
if ($status=='Off') {
    if ($d['heater2']->s!='Off') {
        sw('heater2', 'Off', basename(__FILE__).':'.__LINE__);
    }
}