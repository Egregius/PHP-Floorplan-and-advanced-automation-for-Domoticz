<?php
if ($status==0) {
    if ($d['zithoek']['m']!=0) {
        storemode('zithoek', 0, basename(__FILE__).':'.__LINE__);
    }
}