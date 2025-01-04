<?php
if ($status==0) {
    store('zolder_set', 4, basename(__FILE__).':'.__LINE__);
    storemode('zolder_set', 0, basename(__FILE__).':'.__LINE__);
}