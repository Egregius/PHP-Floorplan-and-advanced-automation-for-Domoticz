<?php
if ($d['heating']['s']>=0) {
	store('badkamer_set', 21, basename(__FILE__).':'.__LINE__);
	storemode('badkamer_set', 1, basename(__FILE__).':'.__LINE__);
}
