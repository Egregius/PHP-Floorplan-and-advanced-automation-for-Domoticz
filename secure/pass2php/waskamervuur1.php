<?php
if ($status=='Off') {
    if ($d['waskamervuur2']['s']!='Off') {
        sw('waskamervuur2', 'Off', basename(__FILE__).':'.__LINE__);
    }
}
