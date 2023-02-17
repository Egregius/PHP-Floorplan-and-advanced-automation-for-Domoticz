<?php
if ($status=='On'&&$d['auto']['s']=='On') {
    if ($d['zolderg']['s']=='Off') sw('zolderg', 'On', basename(__FILE__).':'.__LINE__);
}