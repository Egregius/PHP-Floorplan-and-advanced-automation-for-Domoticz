<?php
if ($d['heating']['s']>=0) {
	store('badkamer_set', 22, basename(__FILE__).':'.__LINE__);
	storemode('badkamer_set', 2, basename(__FILE__).':'.__LINE__);
}
