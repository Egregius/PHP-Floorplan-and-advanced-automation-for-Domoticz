<?php
if ($status=='Off') {
    if ($d['badkamervuur2']->s!='Off') {
        sw('badkamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
    }
}
