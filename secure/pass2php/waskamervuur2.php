<?php
if ($status=='On') {
    if ($d['waskamervuur1']['s']!='On') {
        sw('waskamervuur1', 'On', basename(__FILE__).':'.__LINE__);
    }
}
