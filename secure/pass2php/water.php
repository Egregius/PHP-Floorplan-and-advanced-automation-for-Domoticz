<?php
if ($status=='Off') {
    storemode('water', 0, basename(__FILE__).':'.__LINE__);
    telegram('Water tuin: '.$d['watertuin']['m'].' L');
    storemode('watertuin', 0);
}