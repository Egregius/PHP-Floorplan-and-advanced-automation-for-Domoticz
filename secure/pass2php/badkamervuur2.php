<?php
if ($status=='On') {
    if ($d['badkamervuur1']->s!='On') {
        sw('badkamervuur1', 'On', basename(__FILE__).':'.__LINE__);
    }
}
