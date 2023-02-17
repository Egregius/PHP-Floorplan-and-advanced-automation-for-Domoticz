<?php
if ($status=='Off') {
    if ($d['pirgarage']['s']!='Off') {
        store('pirgarage', 'Off', basename(__FILE__).':'.__LINE__);
    }
}